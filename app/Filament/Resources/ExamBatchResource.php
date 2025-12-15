<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamBatchResource\Pages;
use App\Filament\Resources\ExamBatchResource\RelationManagers;
use App\Models\ExamBatch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class ExamBatchResource extends Resource
{
    protected static ?string $model = \App\Models\ExamBatch::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Exam Batches';
    }

    public static function getModelLabel(): string
    {
        return 'Exam Batch';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Exam Batches';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Exams';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Batch Info')
                    ->schema([
                        Select::make('exam_domain_id')
                            ->relationship('domain', 'title')
                            ->label('Exam Domain')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('title')
                            ->label('Title')
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Can be auto-generated if left empty.')
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number = shown first. e.g.: 1, 2, 3...')
                            ->required(),
                    ])->columns(2),

                Section::make('SEO Settings')
                    ->description('This info is used for search engines.')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title (Meta Title)')
                            ->maxLength(60)
                            ->helperText('Recommended 50-60 chars.'),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO Description (Meta Description)')
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('Recommended 200-250 chars.'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('title')->label('Title')->sortable()->searchable(),
                TextColumn::make('domain.title')->label('Domain')->sortable()->searchable(),
                TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->label('Updated')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_domain_id')
                    ->label('Exam Domain')
                    ->relationship('domain', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListExamBatches::route('/'),
            'create' => Pages\CreateExamBatch::route('/create'),
            'edit' => Pages\EditExamBatch::route('/{record}/edit'),
        ];
    }
}
