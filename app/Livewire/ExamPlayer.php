<?php

namespace App\Livewire;

use App\Models\Exam;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
use App\Domain\Exam\Services\ScoringService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use App\Support\ActivityLogger;

class ExamPlayer extends Component
{
    public Exam $exam;

    #[Url(as: 'page', history: true)]
    public int $page = 1;

    #[Url(as: 'question_id')]
    public ?int $questionId = null;

    public array $answers = [];

    public ?int $durationSeconds = null;
    public int $remainingSeconds = 0;

    public ?int $attemptId = null;
    protected array $dirtyQueue = [];

    public bool $requireAllAnswered = false;
    
    public string $reportText = '';
    public bool $showReportModal = false;

    public function mount(Exam $exam): void
    {
        // Questions are linked directly to Exam now
        $this->exam = $exam->load(['questions.choices']);

        // If a specific question is requested, start from that question index
        if ($this->questionId) {
            $questions = $this->exam->questions->where('is_deleted', false)->values();
            $position = $questions->search(fn ($q) => $q->id === $this->questionId);
            if ($position !== false) {
                $this->page = (int) $position + 1;
            }
        } else {
            // Ensure page is within bounds if set via URL
            $this->page = max(1, min($this->page, $this->questionsCount()));
        }

        // Always start with a clean state on each entry (as per requirement)
        $this->answers = [];
        Session::forget($this->sessionKey());

        // Initialize or create DB attempt only for logged-in users
        if (Auth::check() && $this->canUserInteract()) {
            // Cancel any existing in-progress attempts for this exam
            // Each entry to the exam page starts a fresh attempt
            ExamAttempt::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->update(['status' => 'cancelled']);

            // Always create a fresh attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $this->exam->id,
                'user_id' => Auth::id(),
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
            $this->attemptId = $attempt->id;

            // Log start
            ActivityLogger::log('exam_started', [
                'page' => $this->page,
                'duration_seconds' => $this->durationSeconds,
                'remaining_seconds' => $this->remainingSeconds,
            ], $this->exam->id, $this->attemptId);
        }

        // Initialize countdown timer based on duration_minutes
        $durationMin = (int) ($this->exam->duration_minutes ?? 0);
        $this->durationSeconds = $durationMin > 0 ? $durationMin * 60 : null;
        $this->remainingSeconds = $this->durationSeconds ?? 0;
    }

    public function next(): void
    {
        $this->page = min($this->page + 1, $this->questionsCount());
    }

    public function prev(): void
    {
        $this->page = max($this->page - 1, 1);
    }

    public function questionsCount(): int
    {
        return $this->exam->questions->where('is_deleted', false)->count();
    }

    public function unansweredCount(): int
    {
        $count = 0;
        foreach ($this->exam->questions as $q) {
            // Skip deleted questions
            if ($q->is_deleted) {
                continue;
            }
            
            $ans = $this->answers[$q->id] ?? null;
            $answered = false;
            if (in_array($q->type, ['single_choice','multi_choice','true_false'])) {
                $answered = is_array($ans) && collect($ans)->filter()->count() > 0;
            } else {
                $text = is_array($ans) ? ($ans['text'] ?? '') : (string) $ans;
                $answered = trim((string)$text) !== '';
            }
            if (! $answered) { $count++; }
        }
        return $count;
    }

    public function goTo(int $toPage): void
    {
        $total = $this->questionsCount();
        if ($toPage >= 1 && $toPage <= $total) {
            $this->page = $toPage;
        }
    }

    public function saveAnswer(int $questionId, int $choiceId, bool $checked): void
    {
        // Only allow logged-in users to save answers
        if (!$this->canUserInteract()) {
            return;
        }

        // Rate limit: max 10 calls per minute per user/exam
        $who = 'user:'.Auth::id();
        $rateKey = sprintf('saveAnswer:%d:%s', $this->exam->id, $who);
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return; // silently drop to protect server
        }
        RateLimiter::hit($rateKey, 60);
        // Enforce single vs multi choice based on question type
        $question = $this->findQuestion($questionId);
        $current = $this->answers[$questionId] ?? [];

        if ($question && ($question->type === 'single_choice' || $question->type === 'true_false')) {
            // Reset others if single-choice
            $current = [];
            if ($checked) {
                $current[$choiceId] = true;
            }
        } else {
            // Multi-choice
            $current[$choiceId] = $checked;
        }

        $this->answers[$questionId] = $current;

        // Persist in session (debounced from UI side)
        Session::put($this->sessionKey(), $this->answers);

        // Queue for debounced DB flush
        $key = $questionId . ':' . $choiceId;
        $this->dirtyQueue[$key] = [
            'question_id' => $questionId,
            'choice_id' => $choiceId,
            'checked' => $checked,
            'type' => $question?->type,
        ];

        $this->flushDirty();
    }

    public function flushDirty(): void
    {
        if (!$this->attemptId || empty($this->dirtyQueue)) {
            return;
        }

        // Group dirty by question id
        $byQuestion = [];
        foreach ($this->dirtyQueue as $item) {
            $qid = (int)$item['question_id'];
            $byQuestion[$qid] = true;
        }

        foreach (array_keys($byQuestion) as $qid) {
            $question = $this->findQuestion($qid);
            if (!$question) { continue; }

            // For single choice/true_false: delete all existing rows for this question and reinsert current selections
            if (in_array($question->type, ['single_choice','true_false'], true)) {
                AttemptAnswer::where('exam_attempt_id', $this->attemptId)
                    ->where('question_id', $qid)
                    ->delete();

                $current = $this->answers[$qid] ?? [];
                foreach ($current as $cid => $isOn) {
                    if ($isOn) {
                        AttemptAnswer::updateOrCreate(
                            [
                                'exam_attempt_id' => $this->attemptId,
                                'question_id' => $qid,
                                'choice_id' => (int)$cid,
                            ],
                            [
                                'selected' => true,
                            ]
                        );
                    }
                }
            } else {
                // Multi-choice: upsert only dirty choices for this question based on current answers state
                $current = $this->answers[$qid] ?? [];
                foreach ($current as $cid => $isOn) {
                    AttemptAnswer::updateOrCreate(
                        [
                            'exam_attempt_id' => $this->attemptId,
                            'question_id' => $qid,
                            'choice_id' => (int)$cid,
                        ],
                        [
                            'selected' => (bool)$isOn,
                        ]
                    );
                }
            }
        }

        // Clear queue after flush
        $this->dirtyQueue = [];
    }

    public function tick(): void
    {
        if (is_null($this->durationSeconds)) {
            return;
        }
        // Count down from duration to zero
        if ($this->remainingSeconds > 0) {
            $this->remainingSeconds -= 1;
        } else {
            $this->remainingSeconds = 0; // clamp at zero
        }
    }

    protected function sessionKey(): string
    {
        return 'exam_answers_' . $this->exam->id;
    }

    protected function findQuestion(int $id): ?Question
    {
        foreach ($this->exam->questions as $q) {
            if ($q->id === $id) return $q;
        }
        return null;
    }

    /**
     * Check if current user can interact with exam (answer questions, submit, etc.)
     */
    public function canUserInteract(): bool
    {
        return Auth::check();
    }

    public function submit(ScoringService $scoring = null)
    {
        // Only allow logged-in users to submit
        if (!$this->canUserInteract()) {
            return redirect()->route('login')->with('warning', 'Please login to submit the exam.');
        }

        // Fail-safe: If attemptId is lost, try to recover the active attempt
        if (!$this->attemptId && Auth::check()) {
            $this->attemptId = ExamAttempt::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->latest('id')
                ->first()?->id;
        }

        // Ensure pending changes are saved to DB before scoring/redirect
        $this->flushDirty();
        if ($this->requireAllAnswered && $this->unansweredCount() > 0) {
            // Still allow submission based on request, just proceed
        }

        // Compute score using the updated ScoringService with exam-level rules
        $service = $scoring ?: app(ScoringService::class);
        $scores = $service->compute($this->exam, $this->answers);
        
        $percentage = (float)($scores['percentage'] ?? 0.0);
        $correct = (int)($scores['correct'] ?? 0);
        $wrong = (int)($scores['wrong'] ?? 0);
        $unanswered = (int)($scores['unanswered'] ?? 0);
        $earned = (float)($scores['earned'] ?? 0.0);
        $total = (float)($scores['total'] ?? 100.0);

        if ($this->attemptId) {
            $passThreshold = property_exists($this->exam, 'pass_threshold') ? ((float) ($this->exam->pass_threshold ?? 0)) : 0.0;
            
            ExamAttempt::where('id', $this->attemptId)->update([
                'submitted_at' => now(),
                'score' => $percentage,
                'passed' => $percentage >= $passThreshold,
                'status' => 'submitted',
            ]);

            ActivityLogger::log('exam_finished', [
                'percentage' => $percentage,
                'correct' => $correct,
                'wrong' => $wrong,
                'unanswered' => $unanswered,
            ], $this->exam->id, $this->attemptId);
        }

        // Persist results for result page (not flash to be safe with SPA navigation)
        session()->put('exam_result_stats', [
            'percentage' => $percentage,
            'earned' => $earned,
            'total' => $total,
            'correct' => $correct,
            'wrong' => $wrong,
            'unanswered' => $unanswered,
            'passed' => $percentage >= (float)($this->exam->pass_threshold ?? 0),
        ]);

        // Save user answers for review
        session()->put('exam_user_answers', $this->answers);
        
        // Force session save before redirect
        session()->save();

        // Server-side redirect ensures a full navigation without relying on Alpine/Livewire SPA
        $params = ['exam' => $this->exam->id];
        if ($this->attemptId) {
            $params['attempt'] = $this->attemptId;
        }
        return redirect()->route('exam.result', $params);
    }

    public function submitReport(): void
    {
        // Only allow logged-in users to submit reports
        if (!$this->canUserInteract()) {
            session()->flash('error', 'برای گزارش خطا لطفاً ابتدا وارد حساب کاربری خود شوید.');
            return;
        }

        $this->validate([
            'reportText' => 'required|string|min:10|max:1000',
        ], [
            'reportText.required' => 'لطفاً متن گزارش را وارد کنید.',
            'reportText.min' => 'گزارش باید حداقل 10 کاراکتر باشد.',
            'reportText.max' => 'گزارش نباید بیشتر از 1000 کاراکتر باشد.',
        ]);

        $questions = $this->exam->questions->where('is_deleted', false)->values();
        $currentQuestion = $questions[$this->page - 1] ?? null;

        if (!$currentQuestion) {
            session()->flash('error', 'سوال یافت نشد.');
            return;
        }

        \App\Models\QuestionReport::create([
            'user_id' => auth()->id(),
            'question_id' => $currentQuestion->id,
            'exam_id' => $this->exam->id,
            'report' => $this->reportText,
            'status' => 'pending',
        ]);

        $this->reportText = '';
        $this->showReportModal = false;
        session()->flash('success', 'گزارش شما با موفقیت ثبت شد.');
    }

    public function render()
    {
        // Questions are directly on Exam, filter out deleted questions
        $questions = $this->exam->questions->where('is_deleted', false)->values();
        $question = $questions[$this->page - 1] ?? null;

        return view('livewire.exam-player', [
            'question' => $question,
            'index' => $this->page - 1,
            'total' => $questions->count(),
        ])->layout('layouts.app', [
            'seoTitle' => $this->exam->seo_title ?: ($this->exam->title . ' - آزمون کده'),
            'seoDescription' => $this->exam->seo_description ?? '',
            'seoCanonical' => route('exam.play', ['exam' => $this->exam->id, 'page' => $this->page]),
        ]);
    }
}
