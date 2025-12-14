<?php

namespace App\Filament\Resources\EducationalPostResource\Pages;

use App\Filament\Resources\EducationalPostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducationalPost extends EditRecord
{
    protected static string $resource = EducationalPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // محاسبه حجم فایل PDF
        if (isset($data['pdf_file']) && $data['pdf_file']) {
            $filePath = storage_path('app/public/' . $data['pdf_file']);
            if (file_exists($filePath)) {
                $data['file_size'] = round(filesize($filePath) / 1024); // به کیلوبایت
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
