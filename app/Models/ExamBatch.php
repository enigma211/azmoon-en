<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_domain_id',
        'title',
        'slug',
        'is_active',
        'is_free',
        'sort_order',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(ExamDomain::class, 'exam_domain_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
