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

    public array $checkedQuestions = [];
    
    // Store the randomized order of question IDs for this session
    public array $questionOrder = [];

    public bool $requireAllAnswered = false;
    
    public string $reportText = '';
    public bool $showReportModal = false;

    public function mount(Exam $exam): void
    {
        // Questions are linked directly to Exam now
        $this->exam = $exam->load(['questions.choices']);

        // Initialize question order (randomized)
        // We filter deleted questions first
        $questions = $this->exam->questions->where('is_deleted', false)->values();
        
        // Shuffle questions to ensure random order for every new attempt/session
        $this->questionOrder = $questions->shuffle()->pluck('id')->toArray();

        // If a specific question is requested, start from that question index
        if ($this->questionId) {
            $position = array_search($this->questionId, $this->questionOrder);
            if ($position !== false) {
                $this->page = (int) $position + 1;
            }
        } else {
            // Ensure page is within bounds if set via URL
            $this->page = max(1, min($this->page, count($this->questionOrder)));
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
        return count($this->questionOrder);
    }

    public function unansweredCount(): int
    {
        $count = 0;
        // Iterate over the ordered questions
        foreach ($this->questionOrder as $qid) {
            $q = $this->findQuestion($qid);
            if (!$q) continue;
            
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

    public function checkAnswer(): void
    {
        $currentQuestionId = $this->questionOrder[$this->page - 1] ?? null;
        if (!$currentQuestionId) return;
        
        // Mark as checked
        $this->checkedQuestions[$currentQuestionId] = true;
    }

    public function isQuestionCorrect($questionId): bool
    {
        $q = $this->findQuestion($questionId);
        if (!$q) return false;

        $ans = $this->answers[$questionId] ?? null;
        
        // Logic adapted from ScoringService
        switch ($q->type) {
            case 'single_choice':
            case 'true_false':
                $selectedIds = collect($ans ?? [])->filter()->keys();
                if ($selectedIds->count() === 0) return false;
                $correctIds = $q->choices->where('is_correct', true)->pluck('id');
                return $selectedIds->count() === 1 && $selectedIds->diff($correctIds)->isEmpty() && $correctIds->diff($selectedIds)->isEmpty();
            
            case 'multi_choice':
                $selectedIds = collect($ans ?? [])->filter()->keys();
                if ($selectedIds->count() === 0) return false;
                $correctIds = $q->choices->where('is_correct', true)->pluck('id');
                return $selectedIds->diff($correctIds)->isEmpty() && $correctIds->diff($selectedIds)->isEmpty();
                
            case 'short_answer':
                $text = is_array($ans) ? ($ans['text'] ?? '') : (string) $ans;
                $text = trim(mb_strtolower($text));
                if ($text === '') return false;
                $keys = collect([$q->explanation])->filter()->map(fn($s) => trim(mb_strtolower((string)$s)))->filter();
                return $keys->contains($text);
                
            default:
                return false;
        }
    }

    public function getStatsProperty(): array
    {
        $correct = 0;
        $wrong = 0;
        
        foreach ($this->checkedQuestions as $qId => $isChecked) {
            if (!$isChecked) continue;
            if ($this->isQuestionCorrect($qId)) {
                $correct++;
            } else {
                $wrong++;
            }
        }
        
        $skipped = 0;
        // Use questionOrder for iteration to match user flow
        for ($i = 0; $i < $this->page - 1; $i++) {
            $qid = $this->questionOrder[$i] ?? null;
            if ($qid && !isset($this->checkedQuestions[$qid])) {
                $skipped++;
            }
        }
        
        return compact('correct', 'wrong', 'skipped');
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
            session()->flash('error', 'Please login to submit a report.');
            return;
        }

        $this->validate([
            'reportText' => 'required|string|min:10|max:1000',
        ], [
            'reportText.required' => 'Please enter the report text.',
            'reportText.min' => 'Report must be at least 10 characters.',
            'reportText.max' => 'Report must not exceed 1000 characters.',
        ]);

        $currentQuestionId = $this->questionOrder[$this->page - 1] ?? null;

        if (!$currentQuestionId) {
            session()->flash('error', 'Question not found.');
            return;
        }

        \App\Models\QuestionReport::create([
            'user_id' => auth()->id(),
            'question_id' => $currentQuestionId,
            'exam_id' => $this->exam->id,
            'report' => $this->reportText,
            'status' => 'pending',
        ]);

        $this->reportText = '';
        $this->showReportModal = false;
        session()->flash('success', 'Your report has been submitted successfully.');
    }

    public function render()
    {
        // Fetch current question based on randomized order
        $currentQuestionId = $this->questionOrder[$this->page - 1] ?? null;
        $question = $currentQuestionId ? $this->findQuestion($currentQuestionId) : null;

        return view('livewire.exam-player', [
            'question' => $question,
            'index' => $this->page - 1,
            'total' => count($this->questionOrder),
        ])->layout('layouts.app', [
            'seoTitle' => $this->exam->seo_title ?: ($this->exam->title . ' - AllExam24'),
            'seoDescription' => $this->exam->seo_description ?? '',
            'seoCanonical' => route('exam.play', ['exam' => $this->exam->id, 'page' => $this->page]),
        ]);
    }
}
