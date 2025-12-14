<?php

namespace App\Filament\Resources\ChoiceResource\Pages;

use App\Filament\Resources\ChoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChoices extends ListRecords
{
    protected static string $resource = ChoiceResource::class;
    
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
