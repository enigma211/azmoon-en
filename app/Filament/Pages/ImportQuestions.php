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
    protected static ?string $navigationLabel = 'Import Questions';
    protected static ?string $navigationGroup = 'Exams';
    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(Exam::query())
            ->columns([
                TextColumn::make('title')
                    ->label('Exam Title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('questions_count')
                    ->label('Questions Count')
                    ->getStateUsing(fn (Exam $record) => $record->questions()
                        ->where(function ($q) {
                            $q->where('is_deleted', false)->orWhereNull('is_deleted');
                        })
                        ->count())
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->formatStateUsing(fn ($state) => formatDateTime($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('import')
                    ->label('Import Questions')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('primary')
                    ->form([
                        Section::make()
                            ->schema([
                                FileUpload::make('csv_file')
                                    ->label('CSV File')
                                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                                    ->required()
                                    ->helperText('CSV Format: No, Question, Option A, Option B, Option C, Option D, Correct (A/B/C/D), Explanation')
                                    ->disk('local')
                                    ->directory('temp-imports')
                                    ->maxSize(5120),
                            ])
                    ])
                    ->action(function (Exam $record, array $data): void {
                        $this->importQuestions($record->id, $data['csv_file']);
                    }),
                Action::make('import_explanations')
                    ->label('Import Explanations (Legacy)')
                    ->icon('heroicon-o-document-text')
                    ->color('secondary')
                    ->form([
                        Section::make()
                            ->schema([
                                FileUpload::make('explanation_csv_file')
                                    ->label('Explanation CSV File')
                                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                                    ->required()
                                    ->helperText('CSV file must have 2 columns: Question No, Explanation Text')
                                    ->disk('local')
                                    ->directory('temp-imports')
                                    ->maxSize(5120),
                            ])
                    ])
                    ->action(function (Exam $record, array $data): void {
                        $this->importExplanations($record->id, $data['explanation_csv_file']);
                    }),
                Action::make('view_questions')
                    ->label('Questions')
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
                throw new \Exception('File not found');
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
                    $errors[] = "Line {$lineNumber}: Not enough columns (must be at least 2 columns)";
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
                        $errors[] = "Line {$lineNumber}: Question number {$orderColumn} not found in this exam";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Line {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            // Delete temporary file
            Storage::disk('local')->delete($csvFile);

            // Show result notification
            if ($importedCount > 0) {
                $message = "{$importedCount} explanations imported successfully";
                if (!empty($errors)) {
                    $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... and " . (count($errors) - 5) . " more errors";
                    }
                }
                
                Notification::make()
                    ->title('Import Successful')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error')
                    ->body('No explanations imported. Errors: ' . implode(', ', $errors))
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('File Processing Error')
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
                throw new \Exception('File not found');
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
                    $errors[] = "Line {$lineNumber}: Not enough columns (must be 7 columns)";
                    continue;
                }

                try {
                    $orderColumn = (int) trim($row[0]);
                    $questionText = trim($row[1]);
                    $choice1 = trim($row[2]);
                    $choice2 = trim($row[3]);
                    $choice3 = trim($row[4]);
                    $choice4 = trim($row[5]);
                    $correctAnswerRaw = trim($row[6]);
                    $explanation = isset($row[7]) ? trim($row[7]) : null;

                    // Clean up Explanation prefix if present
                    if ($explanation) {
                        $explanation = preg_replace('/^Explanation:\s*/i', '', $explanation);
                    }

                    // Convert correct answer (A/B/C/D or 1/2/3/4) to index
                    $correctAnswer = null;
                    $cleanRaw = strtoupper($correctAnswerRaw);
                    
                    // Map letters to numbers
                    $map = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];

                    // 1. Check for exact match (A, B, C, D)
                    if (isset($map[$cleanRaw])) {
                        $correctAnswer = $map[$cleanRaw];
                    }
                    // 2. Check for numeric (1, 2, 3, 4)
                    elseif (is_numeric($cleanRaw) && $cleanRaw >= 1 && $cleanRaw <= 4) {
                        $correctAnswer = (int) $cleanRaw;
                    }
                    // 3. Check for pattern "Correct Answer: X" or similar where X is at the end
                    elseif (preg_match('/(?:^|[\s:])([A-D])[\.\)]?$/', $cleanRaw, $matches)) {
                        $correctAnswer = $map[$matches[1]];
                    }

                    if (!$correctAnswer) {
                        $errors[] = "Line {$lineNumber}: Invalid correct answer '{$correctAnswerRaw}'. Must be A, B, C, D or 1-4.";
                        continue;
                    }

                    // Create question
                    $question = Question::create([
                        'exam_id' => $examId,
                        'type' => 'single_choice',
                        'text' => $questionText,
                        'explanation' => $explanation,
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
                    $errors[] = "Line {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            // Delete temporary file
            Storage::disk('local')->delete($csvFile);

            // Show result notification
            if ($importedCount > 0) {
                $message = "{$importedCount} questions imported successfully";
                if (!empty($errors)) {
                    $message .= "\n\nErrors:\n" . implode("\n", array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= "\n... and " . (count($errors) - 5) . " more errors";
                    }
                }
                
                Notification::make()
                    ->title('Import Successful')
                    ->body($message)
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Error')
                    ->body('No questions imported. Errors: ' . implode(', ', $errors))
                    ->danger()
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('File Processing Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
