<?php

namespace App\Filament\Resources\ExamSectionResource\Pages;

use App\Filament\Resources\ExamSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamSections extends ListRecords
{
    protected static string $resource = ExamSectionResource::class;
    
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
