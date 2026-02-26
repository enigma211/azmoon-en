<?php

namespace App\Filament\Resources\QuestionVoteResource\Pages;

use App\Filament\Resources\QuestionVoteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageQuestionVotes extends ManageRecords
{
    protected static string $resource = QuestionVoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
