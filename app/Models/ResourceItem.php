<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'file_path',
        'exam_domain_id',
        'exam_batch_id',
        'exam_id',
    ];

    public function domain()
    {
        return $this->belongsTo(ExamDomain::class, 'exam_domain_id');
    }

    public function batch()
    {
        return $this->belongsTo(ExamBatch::class, 'exam_batch_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
}
