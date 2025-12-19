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

        // Replace [year] placeholder with current year
        $year = date('Y');
        $fields = ['seo_title', 'seo_description', 'description', 'title'];

        foreach ($fields as $field) {
            if ($this->batch->$field) {
                $this->batch->$field = str_replace('[year]', $year, $this->batch->$field);
            }
        }
    }

    public function render()
    {
        $exams = \App\Models\Exam::query()
            ->where('is_published', true)
            ->where(function ($query) {
                $query->where('exam_batch_id', $this->batch->id)
                      ->orWhere('exam_domain_id', $this->batch->exam_domain_id);
            })
            ->orderBy('sort_order')
            ->get();

        return view('livewire.exams-page', [
            'exams' => $exams,
        ])->layout('layouts.app', [
            'seoTitle' => $this->batch->seo_title ?: $this->batch->title,
            'seoDescription' => $this->batch->seo_description ?: 'AllExam24: The largest exam simulator. Practice with real past questions in an environment similar to the actual exam and get your pass/fail results immediately.',
        ]);
    }
}
