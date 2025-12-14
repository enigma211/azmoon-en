<?php

namespace App\Filament\Resources\ExamBatchResource\Pages;

use App\Filament\Resources\ExamBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamBatch extends EditRecord
{
    protected static string $resource = ExamBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
