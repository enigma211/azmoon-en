<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EducationalPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_category_id',
        'title',
        'slug',
        'description',
        'content',
        'video_embed_code',
        'pdf_file',
        'file_size',
        'thumbnail',
        'view_count',
        'download_count',
        'sort_order',
        'is_active',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::deleting(function ($post) {
            // حذف فایل PDF هنگام حذف پست
            if ($post->pdf_file && Storage::disk('public')->exists($post->pdf_file)) {
                Storage::disk('public')->delete($post->pdf_file);
            }
            // حذف تصویر شاخص
            if ($post->thumbnail && Storage::disk('public')->exists($post->thumbnail)) {
                Storage::disk('public')->delete($post->thumbnail);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }

    public function isVideo(): bool
    {
        return $this->category?->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->category?->type === 'document';
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function getPdfUrl(): ?string
    {
        if ($this->pdf_file) {
            return Storage::disk('public')->url($this->pdf_file);
        }
        return null;
    }

    public function getThumbnailUrl(): ?string
    {
        if ($this->thumbnail) {
            return Storage::disk('public')->url($this->thumbnail);
        }
        return null;
    }

    public function getFileSizeFormatted(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        if ($this->file_size < 1024) {
            return $this->file_size . ' KB';
        }

        return round($this->file_size / 1024, 2) . ' MB';
    }
}
