<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;
    
    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 50;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('import')
                ->label('ایمپورت سوالات')
                ->icon('heroicon-m-arrow-down-tray')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('فایل CSV/XLSX')
                        ->acceptedFileTypes(['text/csv','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->directory('imports')
                        ->disk('public')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $path = Storage::disk('public')->path($data['file']);
                    Excel::import(new QuestionsImport, $path);
                    $this->notify('success', 'ایمپورت با موفقیت انجام شد.');
                }),
        ];
    }
}
