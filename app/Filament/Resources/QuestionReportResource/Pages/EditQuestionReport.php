<?php

namespace App\Filament\Resources\QuestionReportResource\Pages;

use App\Filament\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionReport extends EditRecord
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit_question')
                ->label('ویرایش سوال')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(fn () => $this->record->question_id 
                    ? route('filament.admin.resources.questions.edit', ['record' => $this->record->question_id])
                    : null)
                ->openUrlInNewTab()
                ->visible(fn () => $this->record->question_id !== null),
            Actions\DeleteAction::make(),
        ];
    }
}
