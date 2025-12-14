<?php

namespace App\Filament\Resources\ExamDomainResource\Pages;

use App\Filament\Resources\ExamDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamDomains extends ListRecords
{
    protected static string $resource = ExamDomainResource::class;
    
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
