<?php

namespace Database\Factories;

use App\Models\FlashcardDeck;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlashcardDeckFactory extends Factory
{
    protected $model = FlashcardDeck::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'is_active' => true,
        ];
    }
}
