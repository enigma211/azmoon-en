<?php

namespace App\Livewire;

use App\Models\ExamType;
use Livewire\Component;

class EducationalResourcesPage extends Component
{
    public function render()
    {
        $examTypes = ExamType::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('livewire.educational-resources-page', [
            'examTypes' => $examTypes,
        ])->layout('layouts.app', [
            'seoTitle' => 'منابع آموزشی - آزمون کده',
            'seoDescription' => 'دسترسی به ویدیوها و جزوات آموزشی آزمون نظام مهندسی و سایر آزمون‌ها',
        ]);
    }
}
