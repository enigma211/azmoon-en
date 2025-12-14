<?php

namespace App\Filament\Resources\ExamBatchResource\Pages;

use App\Filament\Resources\ExamBatchResource;
use App\Models\Exam;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateExamBatch extends CreateRecord
{
    protected static string $resource = ExamBatchResource::class;

    protected function afterCreate(): void
    {
        // Check if auto-generate toggle is enabled
        if ($this->data['auto_generate_engineering_exams'] ?? false) {
            $this->generateEngineeringExams();
        }
    }

    protected function generateEngineeringExams(): void
    {
        $batchTitle = $this->record->title;
        $batchId = $this->record->id;

        $exams = [
            [
                'title' => 'تاسیسات برقی طراحی',
                'duration_minutes' => 225,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'تاسیسات برقی نظارت',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'تاسیسات برقی اجرا',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'تاسیسات مکانیکی طراحی',
                'duration_minutes' => 225,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'تاسیسات مکانیکی نظارت',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'تاسیسات مکانیکی اجرا',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'عمران اجرا',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'عمران نظارت',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'عمران محاسبات',
                'duration_minutes' => 270,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'معماری نظارت',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'معماری اجرا',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'نقشه برداری',
                'duration_minutes' => 195,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'ترافیک',
                'duration_minutes' => 135,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'شهرسازی',
                'duration_minutes' => 135,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'عمران بهسازی',
                'duration_minutes' => 120,
                'pass_threshold' => 60,
            ],
            [
                'title' => 'عمران گودبرداری',
                'duration_minutes' => 120,
                'pass_threshold' => 60,
            ],
        ];

        foreach ($exams as $examData) {
            $fullTitle = $examData['title'] . ' ' . $batchTitle;
            
            Exam::create([
                'exam_batch_id' => $batchId,
                'title' => $fullTitle,
                'slug' => Str::random(6),
                'duration_minutes' => $examData['duration_minutes'],
                'pass_threshold' => $examData['pass_threshold'],
                'is_published' => false,
                'seo_title' => 'نمونه سوالات آزمون ' . $fullTitle,
                'seo_description' => 'نمونه سوالات آزمون نظام مهندسی ساختمان رشته ' . $fullTitle,
            ]);
        }
    }
}
