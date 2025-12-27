<?php

namespace App\Livewire\Flashcard;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\LeitnerProgress;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class StudyDeck extends Component
{
    public FlashcardDeck $deck;
    public $currentCard = null;
    public $isFlipped = false;
    public $cardsDueCount = 0;
    public $cardsNewCount = 0;
    public $sessionCompleted = false;

    // Track when the session started to distinguish "old due" vs "just wrong due"
    public $sessionStartTime;

    // Leitner Box Intervals (in days)
    protected $intervals = [
        1 => 1,
        2 => 3,
        3 => 7,
        4 => 15,
        5 => 30,
    ];

    public function mount(FlashcardDeck $deck)
    {
        $this->deck = $deck;
        // Fix the session start time to now
        $this->sessionStartTime = now()->toDateTimeString();

        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $this->loadStats();
        $this->loadNextCard();
    }

    public function loadStats()
    {
        $userId = Auth::id();

        // Count due cards (progress exists and next_review_at <= now)
        $this->cardsDueCount = Flashcard::where('deck_id', $this->deck->id)
            ->whereHas('progress', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('next_review_at', '<=', now());
            })->count();

        // Count new cards (no progress record)
        $this->cardsNewCount = Flashcard::where('deck_id', $this->deck->id)
            ->whereDoesntHave('progress', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count();
    }

    public function loadNextCard()
    {
        $userId = Auth::id();
        $this->isFlipped = false;

        // Priority 1: Due cards (Older than session start)
        // Ensure "wrong" cards from this session are pushed to the end
        $card = Flashcard::select('flashcards.*')
            ->join('leitner_progress', 'flashcards.id', '=', 'leitner_progress.flashcard_id')
            ->where('flashcards.deck_id', $this->deck->id)
            ->where('leitner_progress.user_id', $userId)
            ->where('leitner_progress.next_review_at', '<=', now())
            ->where('leitner_progress.next_review_at', '<', $this->sessionStartTime) // Only cards due BEFORE we started
            ->orderBy('leitner_progress.next_review_at', 'asc') // Oldest first
            ->first();

        // Priority 2: New cards (If no "old due" cards left)
        if (!$card) {
            $card = Flashcard::where('deck_id', $this->deck->id)
                ->whereDoesntHave('progress', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->first();
        }

        // Priority 3: Wrong cards from THIS session (Re-review)
        if (!$card) {
            $card = Flashcard::select('flashcards.*')
                ->join('leitner_progress', 'flashcards.id', '=', 'leitner_progress.flashcard_id')
                ->where('flashcards.deck_id', $this->deck->id)
                ->where('leitner_progress.user_id', $userId)
                ->where('leitner_progress.next_review_at', '<=', now())
                ->where('leitner_progress.next_review_at', '>=', $this->sessionStartTime) // Cards we just got wrong
                ->orderBy('leitner_progress.next_review_at', 'asc')
                ->first();
        }

        if (!$card) {
            $this->sessionCompleted = true;
            $this->currentCard = null;
        } else {
            $this->currentCard = $card; // This is a raw model from select, ensure relations if needed, but here mostly text
            $this->sessionCompleted = false;
        }
    }

    public function flip()
    {
        $this->isFlipped = true;
    }

    public function processResult($known)
    {
        if (!$this->currentCard)
            return;

        $userId = Auth::id();
        $progress = LeitnerProgress::firstOrNew([
            'user_id' => $userId,
            'flashcard_id' => $this->currentCard->id,
        ]);

        if ($known) {
            // Move to next box
            $currentBox = $progress->exists ? $progress->box_number : 1;
            $nextBox = min($currentBox + 1, 5); // Max box 5

            $progress->box_number = $nextBox;

            // Calculate next review date
            $days = $this->intervals[$nextBox] ?? 1;
            $progress->next_review_at = Carbon::now()->addDays($days);

        } else {
            // Wrong answer: Reset to box 1
            $progress->box_number = 1;
            // It becomes due immediately (now), so it will be caught by "Priority 3" logic
            $progress->next_review_at = Carbon::now();
        }

        $progress->save();
        $this->loadStats();
        $this->loadNextCard();
    }

    #[Layout('layouts.app', ['seoTitle' => 'Study Deck - AllExam24'])]
    public function render()
    {
        return view('livewire.flashcard.study-deck');
    }
}
