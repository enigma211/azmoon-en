<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id',
        'type',
        'title',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->title);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('title') && empty($category->slug)) {
                $category->slug = Str::slug($category->title);
            }
        });
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    public function posts()
    {
        return $this->hasMany(EducationalPost::class);
    }

    public function activePosts()
    {
        return $this->hasMany(EducationalPost::class)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('sort_order')
            ->orderBy('published_at', 'desc');
    }
}
