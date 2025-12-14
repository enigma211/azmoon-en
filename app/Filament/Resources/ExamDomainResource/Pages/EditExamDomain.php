<?php

namespace App\Filament\Resources\ExamDomainResource\Pages;

use App\Filament\Resources\ExamDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamDomain extends EditRecord
{
    protected static string $resource = ExamDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
