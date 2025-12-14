<?php

namespace App\Filament\Resources\EducationalPostResource\Pages;

use App\Filament\Resources\EducationalPostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEducationalPosts extends ListRecords
{
    protected static string $resource = EducationalPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
