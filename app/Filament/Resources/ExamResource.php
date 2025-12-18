<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResource\Pages;
use App\Filament\Resources\ExamResource\RelationManagers;
use App\Models\Exam;
use App\Filament\Resources\QuestionResource;
use Filament\Forms;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Exams';
    }

    public static function getModelLabel(): string
    {
        return 'Exam';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Exams';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Exams';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('Basic Info')
                    ->schema([
                        Forms\Components\Select::make('exam_domain_id')
                            ->relationship('domain', 'title')
                            ->label('Exam Domain (Global)')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText('Select a Domain to make this exam available in ALL batches (states) of that domain.'),

                        Forms\Components\Select::make('exam_batch_id')
                            ->relationship('batch', 'title')
                            ->label('Exam Batch (Specific)')
                            ->searchable()
                            ->preload()
                            ->helperText('Select a Batch if this exam is specific to one state/batch.')
                            ->visible(fn (Forms\Get $get) => empty($get('exam_domain_id'))),

                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number is shown first'),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[\p{L}\p{N}\-]+$/u')
                            ->helperText('Unique slug for the exam. Use letters, numbers, and dashes.'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->default('')
                            ->maxLength(1000)
                            ->helperText('Exam description (optional)'),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Duration (Minutes)')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->helperText('Exam duration in minutes'),

                        Forms\Components\TextInput::make('pass_threshold')
                            ->label('Pass Score')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->helperText('Minimum score to pass (out of 100)'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(true)
                            ->helperText('If active, exam is accessible to students'),
                    ])
                    ->columns(3),

                Forms\Components\Fieldset::make('Scoring')
                    ->schema([
                        Forms\Components\TextInput::make('total_score')
                            ->label('Total Score')
                            ->numeric()
                            ->default(100)
                            ->required()
                            ->minValue(1)
                            ->step(0.01)
                            ->helperText('Total score if all questions are answered correctly (e.g. 100)'),

                        Forms\Components\TextInput::make('negative_score_ratio')
                            ->label('Negative Score Ratio')
                            ->numeric()
                            ->default(3)
                            ->required()
                            ->minValue(0)
                            ->helperText('How many wrong answers deduct one correct answer? (e.g. 3 means 3 wrong = -1 correct)'),

                        Forms\Components\Placeholder::make('scoring_info')
                            ->label('Info')
                            ->content('System automatically calculates score per question based on total score.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Assumptions')
                    ->schema([
                        TinyEditor::make('assumptions_text')
                            ->label('Assumptions Text')
                            ->helperText('Assumptions for all questions (e.g., g=10 m/s²)')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('assumptions_image')
                            ->label('Assumptions Image')
                            ->image()
                            ->directory('assumptions')
                            ->helperText('Image for assumptions (e.g., table, chart)')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('SEO Settings')
                    ->description('This info is used for search engines.')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO Title')
                            ->maxLength(255)
                            ->helperText('Recommended between 50 and 60 chars.'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO Description')
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('Recommended between 200 and 250 chars.'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('batch.title')
                    ->label('Batch')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('pass_threshold')
                    ->label('Pass Score')
                    ->suffix('/100')
                    ->sortable()
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions')
                    ->getStateUsing(fn ($record) => $record->questions()->where('is_deleted', false)->count())
                    ->url(fn ($record) => QuestionResource::getUrl('index', [
                        'tableFilters' => [
                            'exam_id' => [
                                'value' => $record->id,
                            ],
                        ],
                    ]))
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('exam_batch_id')
                    ->label('Batch')
                    ->relationship('batch', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
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
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
