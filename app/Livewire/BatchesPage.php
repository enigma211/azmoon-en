<?php

namespace App\Livewire;

use App\Models\ExamDomain;
use Livewire\Component;

class BatchesPage extends Component
{
    public ExamDomain $domain;

    public function mount(ExamDomain $domain): void
    {
        $this->domain = $domain;
    }

    public function render()
    {
        return view('livewire.batches-page', [
            'batches' => $this->domain->batches()->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get(),
        ])->layout('layouts.app', [
            'seoTitle' => $this->domain->seo_title ?: $this->domain->title . ' - آزمون کده',
            'seoDescription' => $this->domain->seo_description ?: 'آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.',
        ]);
    }
}
