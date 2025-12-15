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
            'seoTitle' => $this->domain->seo_title ?: $this->domain->title . ' - AllExam24',
            'seoDescription' => $this->domain->seo_description ?: 'AllExam24: The largest exam simulator. Practice with real past questions in an environment similar to the actual exam and get your pass/fail results immediately.',
        ]);
    }
}
