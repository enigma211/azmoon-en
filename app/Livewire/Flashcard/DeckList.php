<?php

namespace App\Livewire\Flashcard;

use App\Models\FlashcardDeck;
use Livewire\Component;
use Livewire\Attributes\Layout;

class DeckList extends Component
{
    #[Layout('layouts.app', ['seoTitle' => 'Flashcards - AllExam24'])]
    public function render()
    {
        $decks = FlashcardDeck::where('is_active', true)
            ->withCount('flashcards')
            ->get();

        return view('livewire.flashcard.deck-list', [
            'decks' => $decks,
        ]);
    }
}
