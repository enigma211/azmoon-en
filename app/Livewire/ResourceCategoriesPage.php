<?php

namespace App\Livewire;

use App\Models\ExamType;
use Livewire\Component;

class ResourceCategoriesPage extends Component
{
    public $examType;

    public function mount($slug)
    {
        $this->examType = ExamType::where('slug', $slug)
            ->where('is_active', true)
            ->with(['resourceCategories' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.resource-categories-page')->layout('layouts.app', [
            'seoTitle' => 'Educational Resources for ' . $this->examType->title . ' - Videos and Notes - AllExam24',
            'seoDescription' => $this->examType->description ?: 'Access complete educational videos and study notes for ' . $this->examType->title . ' - Comprehensive and specialized resources for exam success',
        ]);
    }
}
