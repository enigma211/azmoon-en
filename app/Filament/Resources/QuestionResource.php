<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return 'Questions';
    }

    public static function getModelLabel(): string
    {
        return 'Question';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Questions';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Exams';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('exam_id')
                    ->relationship('exam', 'title')
                    ->label('Exam')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Placeholder::make('preview_link')
                    ->label('Preview')
                    ->content(function ($record) {
                        if (!$record || !$record->exam_id) {
                            return 'Select exam and save first';
                        }
                        $url = route('exam.play', ['exam' => $record->exam_id, 'question_id' => $record->id]);
                        return new \Illuminate\Support\HtmlString(
                            '<a href="' . $url . '" target="_blank" class="inline-flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                View in Exam (New Tab)
                            </a>'
                        );
                    })
                    ->hiddenOn('create'),

                TextInput::make('order_column')
                    ->label('Order')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Forms\Components\Hidden::make('type')
                    ->default('single_choice'),

                Forms\Components\Hidden::make('difficulty')
                    ->default('easy'),

                Forms\Components\Hidden::make('score')
                    ->default(1),

                Forms\Components\Hidden::make('negative_score')
                    ->default(0),

                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                TinyEditor::make('text')
                                    ->label('Question Text')
                                    ->required(),

                                TinyEditor::make('explanation')
                                    ->label('Explanation'),
                            ])
                            ->columnSpan(8),

                        Forms\Components\Section::make()
                            ->schema([
                                FileUpload::make('image_path')
                                    ->label('Image 1')
                                    ->image()
                                    ->disk('public')
                                    ->directory('questions')
                                    ->acceptedFileTypes(['image/jpeg','image/png'])
                                    ->openable()
                                    ->downloadable()
                                    ->nullable(),

                                FileUpload::make('image_path_2')
                                    ->label('Image 2')
                                    ->image()
                                    ->disk('public')
                                    ->directory('questions')
                                    ->acceptedFileTypes(['image/jpeg','image/png'])
                                    ->openable()
                                    ->downloadable()
                                    ->nullable(),

                                FileUpload::make('explanation_image_path')
                                    ->label('Explanation Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('explanations')
                                    ->acceptedFileTypes(['image/jpeg','image/png'])
                                    ->openable()
                                    ->downloadable(),

                                Toggle::make('is_deleted')
                                    ->label('Soft Delete')
                                    ->helperText('If checked, question is hidden but score calculation ignores it')
                                    ->inline(false)
                                    ->default(false),
                            ])
                            ->columnSpan(4),
                    ]),

                Repeater::make('choices')
                    ->label('Choices')
                    ->relationship()
                    ->minItems(4)
                    ->maxItems(4)
                    ->default([
                        ['text' => '', 'is_correct' => false, 'order' => 1],
                        ['text' => '', 'is_correct' => false, 'order' => 2],
                        ['text' => '', 'is_correct' => false, 'order' => 3],
                        ['text' => '', 'is_correct' => false, 'order' => 4],
                    ])
                    ->schema([
                        TinyEditor::make('text')
                            ->label(fn (callable $get) => 'Option ' . ($get('order') ?? 1))
                            ->required(),
                        Toggle::make('is_correct')
                            ->label('Correct Answer')
                            ->inline(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state !== true) return;
                                $order = (int)($get('order') ?? 0);
                                for ($i = 1; $i <= 4; $i++) {
                                    if ($i !== $order) {
                                        $set('choices.' . ($i - 1) . '.is_correct', false);
                                    }
                                }
                            })
                            ->disabled(function ($state, callable $get) {
                                // If another item is already marked correct, disable this toggle unless it's the one already true
                                $choices = collect($get('../../choices') ?? []);
                                $hasTrue = $choices->filter(fn($c) => ($c['is_correct'] ?? false) === true)->count() >= 1;
                                return $hasTrue && !$state; // disable others when one is already true
                            })
                            ->default(false),
                        TextInput::make('order')->numeric()->label('Order')->default(0)->hidden(),
                    ])
                    ->grid(2)
                    ->orderColumn('order')
                    ->reorderable(false)
                    ->collapsible(false)
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_column')->label('Order')->sortable(),
                TextColumn::make('text')
                    ->label('Question')
                    ->html()
                    ->searchable()
                    ->wrap()
                    ->words(20),
                TextColumn::make('correct_answer')
                    ->label('Answer')
                    ->getStateUsing(function (Question $record) {
                        $correctChoice = $record->choices()->where('is_correct', true)->first();
                        return $correctChoice ? 'Option ' . $correctChoice->order : '-';
                    }),
                TextColumn::make('is_deleted')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Deleted' : 'Active'),
                TextColumn::make('exam.title')->label('Exam')->searchable(),
                TextColumn::make('created_at')->label('Created')->dateTime('Y-m-d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->relationship('exam', 'title')
                    ->label('Exam')
                    ->searchable()
                    ->preload()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}

