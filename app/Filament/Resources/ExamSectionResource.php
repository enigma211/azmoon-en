<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamSectionResource\Pages;
use App\Filament\Resources\ExamSectionResource\RelationManagers;
use App\Models\ExamSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamSectionResource extends Resource
{
    protected static ?string $model = ExamSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'بخش‌های آزمون';
    }

    public static function getModelLabel(): string
    {
        return 'بخش آزمون';
    }

    public static function getPluralModelLabel(): string
    {
        return 'بخش‌های آزمون';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'آزمون‌ها';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide from admin navigation menu
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات بخش آزمون')
                    ->schema([
                        Forms\Components\Select::make('exam_id')
                            ->relationship('exam', 'title')
                            ->label('آزمون')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->label('عنوان بخش')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->default(0)
                            ->helperText('عدد کوچکتر یعنی نمایش بالاتر'),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam.title')
                    ->label('آزمون')
                    ->toggleable()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('ترتیب')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->formatStateUsing(fn ($state) => formatDateTime($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListExamSections::route('/'),
            'create' => Pages\CreateExamSection::route('/create'),
            'edit' => Pages\EditExamSection::route('/{record}/edit'),
        ];
    }
}
