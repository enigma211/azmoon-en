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
            ? 'Educational Videos for ' . $this->examType->title . ' Exam - AllExam24'
            : 'Study Materials for ' . $this->examType->title . ' Exam - AllExam24';
        
        $seoDescription = $this->category->description ?: (
            $this->category->type === 'video'
                ? 'Access complete educational videos for ' . $this->examType->title . ' Exam - Specialized and practical training'
                : 'Download study materials and resources for ' . $this->examType->title . ' Exam - Comprehensive and practical content'
        );

        return view('livewire.resource-posts-page', [
            'posts' => $posts,
        ])->layout('layouts.app', [
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
        ]);
    }
}
