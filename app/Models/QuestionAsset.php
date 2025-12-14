<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'type',
        'path',
        'caption',
        'order',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $asset) {
            // Apply sensible defaults; validation in Filament should already require these
            if ($asset->type === null) {
                $asset->type = 'image';
            }
            if ($asset->order === null) {
                $asset->order = 0;
            }
        });
    }
}
