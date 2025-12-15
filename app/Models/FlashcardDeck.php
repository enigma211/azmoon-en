<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashcardDeck extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'is_active',
    ];

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class, 'deck_id');
    }
}
