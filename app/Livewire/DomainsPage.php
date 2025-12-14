<?php

namespace App\Livewire;

use App\Models\ExamDomain;
use Livewire\Component;

class DomainsPage extends Component
{
    public function render()
    {
        $domains = ExamDomain::query()
            ->where('is_active', true)
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.domains-page', [
            'domains' => $domains
        ])->layout('layouts.app', [
            'seoTitle' => 'آزمون‌های نظام مهندسی - آزمون کده',
            'seoDescription' => 'آزمون کده: بزرگترین شبیه ساز آزمون نظام مهندسی. با نمونه سوالات واقعی سال‌های گذشته در محیطی مشابه آزمون اصلی تمرین کنید و کارنامه قبولی/مردودی خود را فوراً دریافت نمایید.',
        ]);
    }
}
