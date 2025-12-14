<?php

namespace App\Livewire;

use App\Models\ExamType;
use App\Models\ResourceCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ResourcePostsPage extends Component
{
    use WithPagination;

    public $examType;
    public $category;

    public function mount($examTypeSlug, $categorySlug)
    {
        $this->examType = ExamType::where('slug', $examTypeSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->category = ResourceCategory::where('slug', $categorySlug)
            ->where('exam_type_id', $this->examType->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function render()
    {
        $posts = $this->category->activePosts()->paginate(12);

        // Create better SEO title and description
        $seoTitle = $this->category->type === 'video' 
            ? 'ویدیوهای آموزشی آزمون ' . $this->examType->title . ' - آزمون کده'
            : 'جزوات آموزشی آزمون ' . $this->examType->title . ' - آزمون کده';
        
        $seoDescription = $this->category->description ?: (
            $this->category->type === 'video'
                ? 'دسترسی به ویدیوهای آموزشی کامل آزمون ' . $this->examType->title . ' - آموزش تخصصی و کاربردی'
                : 'دانلود جزوات و منابع آموزشی آزمون ' . $this->examType->title . ' - مطالب جامع و کاربردی'
        );

        return view('livewire.resource-posts-page', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
        ]);
    }
}
