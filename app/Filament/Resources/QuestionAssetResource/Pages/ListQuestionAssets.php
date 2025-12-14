<?php

namespace App\Filament\Resources\QuestionAssetResource\Pages;

use App\Filament\Resources\QuestionAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionAssets extends ListRecords
{
    protected static string $resource = QuestionAssetResource::class;
    
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
