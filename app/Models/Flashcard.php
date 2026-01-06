<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;
    protected $fillable = [
        'deck_id',
        'front_content',
        'back_content',
    ];

    public function deck()
    {
        return $this->belongsTo(FlashcardDeck::class, 'deck_id');
    }

    public function progress()
    {
        return $this->hasOne(LeitnerProgress::class);
    }
}
