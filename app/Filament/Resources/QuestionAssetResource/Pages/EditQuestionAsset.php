<?php

namespace App\Filament\Resources\QuestionAssetResource\Pages;

use App\Filament\Resources\QuestionAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionAsset extends EditRecord
{
    protected static string $resource = QuestionAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
