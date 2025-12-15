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
                'title' => 'Electrical Installations Design',
                'duration_minutes' => 225,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Electrical Installations Supervision',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Electrical Installations Execution',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Mechanical Installations Design',
                'duration_minutes' => 225,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Mechanical Installations Supervision',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Mechanical Installations Execution',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Civil Engineering Execution',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Civil Engineering Supervision',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Civil Engineering Calculations',
                'duration_minutes' => 270,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Architecture Supervision',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Architecture Execution',
                'duration_minutes' => 150,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Surveying',
                'duration_minutes' => 195,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Traffic',
                'duration_minutes' => 135,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Urban Planning',
                'duration_minutes' => 135,
                'pass_threshold' => 50,
            ],
            [
                'title' => 'Civil Engineering Retrofitting',
                'duration_minutes' => 120,
                'pass_threshold' => 60,
            ],
            [
                'title' => 'Civil Engineering Excavation',
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
                'seo_title' => 'Exam Questions Sample ' . $fullTitle,
                'seo_description' => 'Building Engineering Organization Exam Questions Field ' . $fullTitle,
            ]);
        }
    }
}
