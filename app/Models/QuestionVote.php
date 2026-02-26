<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionVote extends Model
{
    protected $fillable = [
        'question_id',
        'ip_address',
        'vote_type', // 1 for like, -1 for dislike
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
