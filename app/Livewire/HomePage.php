<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Slider;
use App\Models\ExamDomain;

class HomePage extends Component
{
    public function render()
    {
        $sliders = Slider::query()
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        $domains = ExamDomain::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        $settings = \App\Models\SystemSetting::first();

        return view('livewire.home-page', [
                'sliders' => $sliders,
                'domains' => $domains,
                'heroTitle' => $settings?->hero_title ?? 'Advanced Question Bank Search',
                'heroDescription' => $settings?->hero_description,
            ])
            ->layout('layouts.app', [
                'seoTitle' => $settings?->seo_title ?? 'ExamApp - The Largest Exam Simulator',
                'seoDescription' => $settings?->site_description ?? 'ExamApp: The largest exam simulation platform. Practice with real past questions in an environment similar to the actual exam and get your pass/fail results immediately.',
                'seoKeywords' => $settings?->seo_keywords,
            ]);
    }
}
