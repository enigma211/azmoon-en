<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Domain\Exam\Services\ScoringService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Session as SessionFacade;

class ExamResult extends Component
{
    public Exam $exam;

    #[Url]
    public ?int $attempt = null;

    public array $stats = [];
    public array $userAnswers = [];
    public array $review = [];

    protected ?ExamAttempt $attemptModel = null;

    public function mount(Exam $exam): void
    {
        $this->exam = $exam->load(['questions.choices']);

        // Try to resolve attempt from query or latest submitted attempt for user
        $attempt = null;
        if (!is_null($this->attempt)) {
            $query = ExamAttempt::query()->where('id', $this->attempt)->where('exam_id', $this->exam->id);
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }
            $attempt = $query->first();
            // If attempt id is provided but not found or not owned, show friendly message
            if (!$attempt) {
                SessionFacade::flash('error', 'نتیجه‌ای برای این شناسه تلاش یافت نشد یا به شما تعلق ندارد.');
                $this->stats = [];
                $this->userAnswers = [];
                $this->attemptModel = null;
                return;
            }
        } elseif (Auth::check()) {
            $attempt = ExamAttempt::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->whereNotNull('submitted_at')
                ->latest('submitted_at')
                ->first();
        }

        $this->attemptModel = $attempt;

        if ($attempt) {
            // Build answers array: [question_id => [choice_id => bool]]
            $answers = [];
            $rows = AttemptAnswer::where('exam_attempt_id', $attempt->id)->get();
            foreach ($rows as $row) {
                $answers[$row->question_id][$row->choice_id] = (bool) $row->selected;
            }
            $this->userAnswers = is_array($answers) ? $answers : [];

            // Compute stats using ScoringService
            $scores = app(ScoringService::class)->compute($this->exam, $this->userAnswers);
            $percentage = (float)($scores['percentage'] ?? 0.0);
            $this->stats = [
                'percentage' => $percentage,
                'earned' => (float)($scores['earned'] ?? 0.0),
                'total' => (float)($scores['total'] ?? 100.0),
                'correct' => (int)($scores['correct'] ?? 0),
                'wrong' => (int)($scores['wrong'] ?? 0),
                'unanswered' => (int)($scores['unanswered'] ?? 0),
                'passed' => $percentage >= (float)($this->exam->pass_threshold ?? 0),
            ];

            // Log viewing result with attempt
            ActivityLogger::log('result_viewed', [
                'percentage' => $percentage,
            ], $this->exam->id, $attempt->id);
        } else {
            // Fallback to session when no attempt is available (e.g., guest)
            $this->stats = (array) (session('exam_result_stats', []) ?? []);
            $this->userAnswers = (array) (session('exam_user_answers', []) ?? []);

            if (!empty($this->stats)) {
                ActivityLogger::log('result_viewed_session', [
                    'percentage' => (float)($this->stats['percentage'] ?? 0.0),
                ], $this->exam->id, null);
            }
        }

        // Final coercion to ensure view never gets non-arrays
        $this->stats = is_array($this->stats) ? $this->stats : [];
        $this->userAnswers = is_array($this->userAnswers) ? $this->userAnswers : [];

        // Build lightweight review for Blade to avoid inline PHP parsing issues
        $this->buildReview();
    }

    protected function buildReview(): void
    {
        $this->review = [];
        if (!$this->exam || !$this->exam->questions) {
            return;
        }

        foreach ($this->exam->questions as $q) {
            $ans = $this->userAnswers[$q->id] ?? [];
            $userSelected = collect($ans)->filter()->keys()->map(fn($v) => (int)$v);
            $isAnswered = $userSelected->count() > 0;
            if (!$isAnswered) {
                continue; // show only answered ones
            }

            $correctChoices = $q->choices->where('is_correct', true);
            $correctIds = $correctChoices->pluck('id')->map(fn($v) => (int)$v);

            $isCorrect = $userSelected->count() > 0
                && $userSelected->diff($correctIds)->isEmpty()
                && $correctIds->diff($userSelected)->isEmpty();

            $userPickedId = $userSelected->first();
            $orderMap = $q->choices->values()->pluck('id')->flip();
            $userNo = $orderMap->has($userPickedId) ? ((int)$orderMap->get($userPickedId) + 1) : null;
            $correctChoice = $correctChoices->first();
            $correctNo = ($correctChoice && $orderMap->has($correctChoice->id)) ? ((int)$orderMap->get($correctChoice->id) + 1) : null;
            $userChoiceModel = $userPickedId ? $q->choices->firstWhere('id', $userPickedId) : null;

            $this->review[] = [
                'question_number' => $q->order_column ?? ($this->exam->questions->search($q) + 1),
                'is_deleted' => (bool)$q->is_deleted,
                'text_html' => (string)$q->text,
                'is_correct' => (bool)$isCorrect,
                'user_no' => $userNo,
                'user_choice_text' => $userChoiceModel?->text,
                'correct_no' => $correctNo,
                'correct_choice_text' => $correctChoice?->text,
            ];
        }
    }

    public function render()
    {
        return view('livewire.exam-result', [
            'stats' => $this->stats,
            'userAnswers' => $this->userAnswers,
            'review' => $this->review,
            'passThreshold' => (float)($this->exam->pass_threshold ?? 50),
        ])->layout('layouts.app', [
            'seoTitle' => $this->exam->seo_title ?: 'نتیجه آزمون ' . $this->exam->title . ' - آزمون کده',
            'seoDescription' => $this->exam->seo_description ?? '',
        ]);
    }
}
