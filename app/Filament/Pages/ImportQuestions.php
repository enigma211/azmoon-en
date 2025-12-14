<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;

class ImportQuestions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string $view = 'filament.pages.import-questions';
    protected static ?string $navigationLabel = 'ایمپورت سوالات';
    protected static ?string $navigationGroup = 'آزمون‌ها';
    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(Exam::query())
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان آزمون')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('questions_count')
                    ->label('تعداد سوالات')
                    ->getStateUsing(fn (Exam $record) => $record->questions()
                        ->where(function ($q) {
                            $q->where('is_deleted', false)->orWhereNull('is_deleted');
                        })
                        ->count())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->formatStateUsing(fn ($state) => formatDateTime($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('import')
                    ->label('وارد کردن سوال')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->form([
                        Section::make()
                            ->schema([
                                FileUpload::make('csv_file')
                                    ->label('فایل CSV')
                                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                                    ->required()
                                    ->helperText('فایل CSV باید شامل 7 ستون باشد: شماره سوال، متن سوال، گزینه 1-4، شماره گزینه صحیح')
                                    ->disk('local')
                                    ->directory('temp-imports')
                                    ->maxSize(5120),
                            ])
                    ])
                    ->action(function (Exam $record, array $data): void {
                        $this->importQuestions($record->id, $data['csv_file']);
                    }),
                Action::make('import_explanations')
                    ->label('وارد کردن پاسخ تشریحی')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->form([
                        Section::make()
                            ->schema([
                                FileUpload::make('explanation_csv_file')
                                    ->label('فایل CSV پاسخ تشریحی')
                                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                                    ->required()
                                    ->helperText('فایل CSV باید شامل 2 ستون باشد: شماره سوال، متن پاسخ تشریحی')
                                    ->disk('local')
                                    ->directory('temp-imports')
                                    ->maxSize(5120),
                            ])
                    ])
                    ->action(function (Exam $record, array $data): void {
                        $this->importExplanations($record->id, $data['explanation_csv_file']);
                    }),
                Action::make('view_questions')
                    ->label('سوالات')
                    ->icon('heroicon-o-list-bullet')
                    ->color('info')
                    ->url(fn (Exam $record): string => 
                        \App\Filament\Resources\QuestionResource::getUrl('index', [
                            'tableFilters' => [
                                'exam_id' => [
                                    'value' => $record->id
                                ]
                            ]
                        ])
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function importExplanations(int $examId, string $csvFile): void
    {
        try {
            $filePath = Storage::disk('local')->path($csvFile);
            
            if (!file_exists($filePath)) {
                throw new \Exception('فایل یافت نشد');
            }

            $file = fopen($filePath, 'r');
            
            // Skip UTF-8 BOM if present
            $bom = fread($file, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($file);
            }
            
            $importedCount = 0;
            $errors = [];
            $lineNumber = 0;

            while (($row = fgetcsv($file)) !== false) {
                $lineNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has at least 2 columns
                if (count($row) < 2) {
                    $errors[] = "خط {$lineNumber}: تعداد ستون‌ها کافی نیست (باید حداقل 2 ستون باشد)";
                    continue;
                }

                try {
                    $orderColumn = (int) trim($row[0]);
                    $explanationText = trim($row[1]);

                    // Find the question
                    $question = Question::where('exam_id', $examId)
                        ->where('order_column', $orderColumn)
                        ->first();

                    if ($question) {
                        $question->update([
                            'explanation' => $explanationText
                        ]);
                        $importedCount++;
                    } else {
                        $errors[] = "خط {$lineNumber}: سوال شماره {$orderColumn} در این آزمون یافت نشد";
                    }
                } catch (\Exception $e) {
                    $errors[] = "خط {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            // Delete temporary file
            Storage::disk('local')->delete($csvFile);

            // Show result notification
            if ($importedCount > 0) {
                $message = "{$importedCount} پاسخ تشریحی با موفقیت وارد شد";
                if (!empty($errors)) {
                    $message .= "\n\nخطاها:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... و " . (count($errors) - 5) . " خطای دیگر";
                    }
                }
                
                Notification::make()
                    ->title('ایمپورت موفق')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('خطا')
                    ->body('هیچ پاسخی وارد نشد. خطاها: ' . implode(', ', $errors))
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('خطا در پردازش فایل')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function importQuestions(int $examId, string $csvFile): void
    {
        try {
            $filePath = Storage::disk('local')->path($csvFile);
            
            if (!file_exists($filePath)) {
                throw new \Exception('فایل یافت نشد');
            }

            $file = fopen($filePath, 'r');
            
            // Skip UTF-8 BOM if present
            $bom = fread($file, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($file);
            }
            
            $importedCount = 0;
            $errors = [];
            $lineNumber = 0;

            while (($row = fgetcsv($file)) !== false) {
                $lineNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Validate row has 7 columns
                if (count($row) < 7) {
                    $errors[] = "خط {$lineNumber}: تعداد ستون‌ها کافی نیست (باید 7 ستون باشد)";
                    continue;
                }

                try {
                    $orderColumn = (int) trim($row[0]);
                    $questionText = trim($row[1]);
                    $choice1 = trim($row[2]);
                    $choice2 = trim($row[3]);
                    $choice3 = trim($row[4]);
                    $choice4 = trim($row[5]);
                    $correctAnswer = (int) trim($row[6]);

                    // Validate correct answer is between 1-4
                    if ($correctAnswer < 1 || $correctAnswer > 4) {
                        $errors[] = "خط {$lineNumber}: شماره گزینه صحیح باید بین 1 تا 4 باشد";
                        continue;
                    }

                    // Create question
                    $question = Question::create([
                        'exam_id' => $examId,
                        'type' => 'single_choice',
                        'text' => $questionText,
                        'order_column' => $orderColumn,
                        'difficulty' => 'easy',
                        'score' => 1,
                        'negative_score' => 0,
                    ]);

                    // Create choices
                    $choices = [$choice1, $choice2, $choice3, $choice4];
                    foreach ($choices as $index => $choiceText) {
                        $choiceOrder = $index + 1;
                        Choice::create([
                            'question_id' => $question->id,
                            'text' => $choiceText,
                            'is_correct' => ($choiceOrder === $correctAnswer),
                            'order' => $choiceOrder,
                        ]);
                    }

                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "خط {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            // Delete temporary file
            Storage::disk('local')->delete($csvFile);

            // Show result notification
            if ($importedCount > 0) {
                $message = "{$importedCount} سوال با موفقیت وارد شد";
                if (!empty($errors)) {
                    $message .= "\n\nخطاها:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... و " . (count($errors) - 5) . " خطای دیگر";
                    }
                }
                
                Notification::make()
                    ->title('ایمپورت موفق')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('خطا')
                    ->body('هیچ سوالی وارد نشد. خطاها: ' . implode(', ', $errors))
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('خطا در پردازش فایل')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
