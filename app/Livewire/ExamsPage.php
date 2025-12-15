<?php

namespace App\Livewire;

use App\Models\ExamBatch;
use Livewire\Component;

class ExamsPage extends Component
{
    public ExamBatch $batch;

    public function mount(ExamBatch $batch): void
    {
        $this->batch = $batch->load('exams');
    }

    public function render()
    {
        return view('livewire.exams-page', [
            'exams' => $this->batch->exams()->orderBy('sort_order')->get(),
        ])->layout('layouts.app', [
            'seoTitle' => $this->batch->seo_title ?: $this->batch->title,
            'seoDescription' => $this->batch->seo_description ?: 'AllExam24: The largest exam simulator. Practice with real past questions in an environment similar to the actual exam and get your pass/fail results immediately.',
        ]);
    }
}
