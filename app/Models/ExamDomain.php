<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    public function batches()
    {
        return $this->hasMany(ExamBatch::class, 'exam_domain_id');
    }
}
