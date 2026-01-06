<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\LeitnerProgress;
use App\Models\User;
use App\Livewire\Flashcard\StudyDeck;
use Livewire\Livewire;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;

test('leitner promotion: correct answer moves card to next box', function () {
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();
    $card = Flashcard::factory()->create(['deck_id' => $deck->id]);

    actingAs($user);

    // Initial state: no progress
    $component = Livewire::test(StudyDeck::class, ['deck' => $deck]);

    // Process "Correct" answer
    $component->call('processResult', true);

    // Should move to box 2 (since default starts at 1, then +1)
    $progress = LeitnerProgress::where('user_id', $user->id)
        ->where('flashcard_id', $card->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->box_number)->toBe(2);

    // 1 -> 2 promotion uses box 2 interval (default 3 days from code)
    // Wait, let's check intervals in StudyDeck.php:
    // 1 => 1, 2 => 3, 3 => 7, 4 => 15, 5 => 30
    // nextBox is 2, so interval is 3 days.
    $expectedDate = Carbon::now()->addDays(3);
    expect($progress->next_review_at->toDateString())->toBe($expectedDate->toDateString());
});

test('leitner reset: wrong answer resets card to box 1', function () {
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();
    $card = Flashcard::factory()->create(['deck_id' => $deck->id]);

    // Manually set progress to box 4
    LeitnerProgress::create([
        'user_id' => $user->id,
        'flashcard_id' => $card->id,
        'box_number' => 4,
        'next_review_at' => Carbon::now()->subDay(),
    ]);

    actingAs($user);

    $component = Livewire::test(StudyDeck::class, ['deck' => $deck]);

    // Process "Incorrect" answer
    $component->call('processResult', false);

    $progress = LeitnerProgress::where('user_id', $user->id)
        ->where('flashcard_id', $card->id)
        ->first();

    expect($progress->box_number)->toBe(1);
    expect($progress->next_review_at->isToday())->toBeTrue();
});

test('leitner cycle: multiple correct answers move card through boxes', function () {
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();
    $card = Flashcard::factory()->create(['deck_id' => $deck->id]);

    actingAs($user);

    $component = Livewire::test(StudyDeck::class, ['deck' => $deck]);

    // Box 1 -> 2
    $component->call('processResult', true);
    expect(LeitnerProgress::first()->box_number)->toBe(2);

    // Box 2 -> 3
    // We need to reload or continue. StudyDeck loads next card. 
    // Since there's only one card, it might mark session as completed.
    // Let's manually push it through.

    $progress = LeitnerProgress::first();
    $progress->update(['next_review_at' => Carbon::now()->subMinutes(1)]);

    $component->call('loadNextCard');
    $component->call('processResult', true);

    $progress->refresh();
    expect($progress->box_number)->toBe(3);
});

test('leitner sessions: respects cards due count', function () {
    $user = User::factory()->create();
    $deck = FlashcardDeck::factory()->create();

    // 2 cards due, 1 card new
    $card1 = Flashcard::factory()->create(['deck_id' => $deck->id]);
    $card2 = Flashcard::factory()->create(['deck_id' => $deck->id]);
    $card3 = Flashcard::factory()->create(['deck_id' => $deck->id]);

    LeitnerProgress::create([
        'user_id' => $user->id,
        'flashcard_id' => $card1->id,
        'box_number' => 1,
        'next_review_at' => Carbon::now()->subDay(),
    ]);

    LeitnerProgress::create([
        'user_id' => $user->id,
        'flashcard_id' => $card2->id,
        'box_number' => 1,
        'next_review_at' => Carbon::now()->addDay(), // Not due
    ]);

    actingAs($user);

    $component = Livewire::test(StudyDeck::class, ['deck' => $deck]);

    // cardsDueCount: card1
    // cardsNewCount: card3 (card2 has progress but not due)
    expect($component->get('cardsDueCount'))->toBe(1);
    expect($component->get('cardsNewCount'))->toBe(1);
});
