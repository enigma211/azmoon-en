<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeitnerProgress extends Model
{
    protected $table = 'leitner_progress';
    
    protected $fillable = [
        'user_id',
        'flashcard_id',
        'box_number',
        'next_review_at',
    ];

    protected $casts = [
        'next_review_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }
}
