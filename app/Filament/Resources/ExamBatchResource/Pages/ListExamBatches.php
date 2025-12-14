<?php

namespace App\Filament\Resources\ExamBatchResource\Pages;

use App\Filament\Resources\ExamBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamBatches extends ListRecords
{
    protected static string $resource = ExamBatchResource::class;
    
    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 50;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
