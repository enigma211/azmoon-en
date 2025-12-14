<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'type',
        'text',
        'image_path',
        'image_path_2',
        'order_column',
        'difficulty',
        'score',
        'negative_score',
        'explanation',
        'explanation_image_path',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function questionAssets()
    {
        return $this->hasMany(QuestionAsset::class);
    }
}
