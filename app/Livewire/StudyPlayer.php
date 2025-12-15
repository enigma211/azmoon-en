<?php

namespace App\Livewire;

use App\Models\Exam;
use App\Models\Question;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class StudyPlayer extends Component
{
    public Exam $exam;
    public $questions = [];
    
    #[Url(as: 'page', history: true)]
    public int $page = 1;

    public bool $isAnswerRevealed = false;
    public $selectedOption = null; // Just for visual feedback

    /**
     * Check if current user can interact (see answers)
     */
    public function canUserInteract(): bool
    {
        return Auth::check();
    }

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
        // Load all questions with choices, ordered by order_column
        $this->questions = $exam->questions()
            ->where('is_deleted', false)
            ->with('choices')
            ->orderBy('order_column')
            ->orderBy('id')
            ->get();

        if ($this->questions->isEmpty()) {
            return redirect()->route('exams', ['batch' => $this->exam->exam_batch_id]);
        }

        // If a specific question ID is passed via query string, jump to it
        $questionId = request()->query('q');
        if ($questionId) {
            $index = $this->questions->search(fn($q) => $q->id == $questionId);
            if ($index !== false) {
                $this->page = $index + 1;
            }
        }

        // Ensure page is valid
        $this->page = max(1, min($this->page, count($this->questions)));
    }

    public function getCurrentQuestionProperty()
    {
        return $this->questions[$this->page - 1] ?? null;
    }

    public function nextQuestion()
    {
        if ($this->page < count($this->questions)) {
            $this->page++;
            $this->resetState();
        }
    }

    public function prevQuestion()
    {
        if ($this->page > 1) {
            $this->page--;
            $this->resetState();
        }
    }

    public function jumpToQuestion($index)
    {
        // Index comes as 0-based usually, or we can change signature
        // Assuming $index is 0-based from old calls? 
        // Actually jumpToQuestion wasn't used in the view I saw, but let's keep it safe.
        // If index is 0-based:
        if (isset($this->questions[$index])) {
            $this->page = $index + 1;
            $this->resetState();
        }
    }

    public function toggleAnswer()
    {
        $this->isAnswerRevealed = !$this->isAnswerRevealed;
    }

    public function selectOption($choiceId)
    {
        // If answer is already revealed, don't allow changing selection (optional UX choice)
        // if ($this->isAnswerRevealed) return; 
        
        $this->selectedOption = $choiceId;
    }

    private function resetState()
    {
        $this->isAnswerRevealed = false;
        $this->selectedOption = null;
    }

    public function render()
    {
        return view('livewire.study-player')
            ->layout('layouts.app', [
                'seoTitle' => 'Study Mode: ' . $this->exam->title,
                'seoDescription' => 'Educational study mode for ' . $this->exam->title,
                'seoRobots' => 'noindex, nofollow',
            ]);
    }
}
