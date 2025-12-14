<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    
    protected static function booted(): void
    {
        static::creating(function (self $choice) {
            // Apply sensible defaults without blocking creation. Validation in Filament handles required fields.
            if ($choice->is_correct === null) {
                $choice->is_correct = false;
            }
            if ($choice->order === null) {
                $choice->order = 0;
            }
        });
    }
}
