<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_batch_id',
        'title',
        'slug',
        'description',
        'duration_minutes',
        'pass_threshold',
        'is_published',
        'total_score',
        'negative_score_ratio',
        'assumptions_text',
        'assumptions_image',
        'seo_title',
        'seo_description',
        'sort_order',
        'thumbnail',
        'badge_text',
        'badge_color',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function batch()
    {
        return $this->belongsTo(ExamBatch::class, 'exam_batch_id');
    }

    public function domain()
    {
        return $this->belongsTo(ExamDomain::class, 'exam_domain_id');
    }

    public function sections()
    {
        return $this->hasMany(ExamSection::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
