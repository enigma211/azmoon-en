<?php

namespace Database\Factories;

use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    public function definition()
    {
        return [
            'deck_id' => FlashcardDeck::factory(),
            'front_content' => $this->faker->sentence,
            'back_content' => $this->faker->paragraph,
        ];
    }
}
