<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = [
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

        static::creating(function ($examType) {
            if (empty($examType->slug)) {
                $examType->slug = Str::slug($examType->title);
            }
        });

        static::updating(function ($examType) {
            if ($examType->isDirty('title') && empty($examType->slug)) {
                $examType->slug = Str::slug($examType->title);
            }
        });
    }

    public function resourceCategories()
    {
        return $this->hasMany(ResourceCategory::class);
    }

    public function videoCategory()
    {
        return $this->hasOne(ResourceCategory::class)->where('type', 'video');
    }

    public function documentCategory()
    {
        return $this->hasOne(ResourceCategory::class)->where('type', 'document');
    }
}
