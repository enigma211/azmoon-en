<?php

namespace App\Filament\Resources\ExamTypeResource\Pages;

use App\Filament\Resources\ExamTypeResource;
use App\Models\ResourceCategory;
use Filament\Resources\Pages\CreateRecord;

class CreateExamType extends CreateRecord
{
    protected static string $resource = ExamTypeResource::class;

    protected function afterCreate(): void
    {
        // Automatically create two categories: video and document
        $examType = $this->record;

        // Educational Videos Category
        ResourceCategory::create([
            'exam_type_id' => $examType->id,
            'type' => 'video',
            'title' => 'Educational Videos',
            'slug' => $examType->slug . '-videos',
            'description' => 'Educational videos for ' . $examType->title,
            'icon' => 'play-circle',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // Educational Documents Category
        ResourceCategory::create([
            'exam_type_id' => $examType->id,
            'type' => 'document',
            'title' => 'Educational Documents',
            'slug' => $examType->slug . '-documents',
            'description' => 'Educational documents and files for ' . $examType->title,
            'icon' => 'document-text',
            'sort_order' => 2,
            'is_active' => true,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
