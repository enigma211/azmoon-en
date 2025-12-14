<?php

namespace App\Livewire;

use App\Models\ExamType;
use App\Models\ResourceCategory;
use App\Models\EducationalPost;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ResourcePostDetailPage extends Component
{
    public $examType;
    public $category;
    public $post;

    public function mount($examTypeSlug, $categorySlug, $postSlug)
    {
        $this->examType = ExamType::where('slug', $examTypeSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $this->category = ResourceCategory::where('slug', $categorySlug)
            ->where('exam_type_id', $this->examType->id)
            ->where('is_active', true)
            ->firstOrFail();

        $this->post = EducationalPost::with('category')
            ->where('slug', $postSlug)
            ->where('resource_category_id', $this->category->id)
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // افزایش تعداد بازدید
        $this->post->incrementViewCount();
    }

    public function downloadPdf()
    {
        if ($this->post->pdf_file && Storage::disk('public')->exists($this->post->pdf_file)) {
            $this->post->incrementDownloadCount();
            return Storage::disk('public')->download($this->post->pdf_file, $this->post->title . '.pdf');
        }
    }

    public function render()
    {
        return view('livewire.resource-post-detail-page')->layout('layouts.app', [
            'seoTitle' => $this->post->title . ' - ' . $this->category->title . ' - آزمون کده',
            'seoDescription' => $this->post->description ?: 'مشاهده ' . $this->post->title,
        ]);
    }
}
