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

        // Priority 1: Due cards
        $card = Flashcard::where('deck_id', $this->deck->id)
            ->whereHas('progress', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('next_review_at', '<=', now());
            })
            ->with(['progress' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->first();

        // Priority 2: New cards
        if (!$card) {
            $card = Flashcard::where('deck_id', $this->deck->id)
                ->whereDoesntHave('progress', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->first();
        }

        if (!$card) {
            $this->sessionCompleted = true;
            $this->currentCard = null;
        } else {
            $this->currentCard = $card;
            $this->sessionCompleted = false;
        }
    }

    public function flip()
    {
        $this->isFlipped = true;
    }

    public function processResult($known)
    {
        if (!$this->currentCard) return;

        $userId = Auth::id();
        $progress = LeitnerProgress::firstOrNew([
            'user_id' => $userId,
            'flashcard_id' => $this->currentCard->id,
        ]);

        if ($known) {
            // Move to next box
            // Treat new cards (not exists) as effectively being in Box 1 currently.
            // If Known, they graduate to Box 2.
            $currentBox = $progress->exists ? $progress->box_number : 1; 
            $nextBox = min($currentBox + 1, 5); // Max box 5
            
            $progress->box_number = $nextBox;
            
            // Calculate next review date
            $days = $this->intervals[$nextBox] ?? 1;
            $progress->next_review_at = Carbon::now()->addDays($days);
            
        } else {
            // Reset to box 1
            $progress->box_number = 1;
            $progress->next_review_at = Carbon::now(); // Review again immediately or next session
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
