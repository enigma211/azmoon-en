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
            'seoTitle' => 'Engineering Exams - ExamApp',
            'seoDescription' => 'ExamApp: The largest exam simulation platform. Practice with real past questions in an environment similar to the actual exam and get your pass/fail results immediately.',
        ]);
    }
}
