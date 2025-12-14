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
        // ایجاد خودکار دو دسته‌بندی: ویدیو و جزوه
        $examType = $this->record;

        // دسته‌بندی ویدیوهای آموزشی
        ResourceCategory::create([
            'exam_type_id' => $examType->id,
            'type' => 'video',
            'title' => 'ویدیوهای آموزشی',
            'slug' => $examType->slug . '-videos',
            'description' => 'ویدیوهای آموزشی ' . $examType->title,
            'icon' => 'play-circle',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // دسته‌بندی جزوات آموزشی
        ResourceCategory::create([
            'exam_type_id' => $examType->id,
            'type' => 'document',
            'title' => 'جزوات آموزشی',
            'slug' => $examType->slug . '-documents',
            'description' => 'جزوات و فایل‌های آموزشی ' . $examType->title,
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
