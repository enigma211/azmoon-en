<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChoiceResource\Pages;
use App\Filament\Resources\ChoiceResource\RelationManagers;
use App\Models\Choice;
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
use Filament\Tables\Columns\TextColumn;

class ChoiceResource extends Resource
{
    protected static ?string $model = Choice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Choices';
    }

    public static function getModelLabel(): string
    {
        return 'Choice';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Choices';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Question Bank';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide from admin navigation per request; choices are managed inside Question form
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('question_id')
                    ->relationship('question', 'text')
                    ->label('Question')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('text')
                    ->label('Choice Text')
                    ->required(),

                Toggle::make('is_correct')
                    ->label('Correct Choice')
                    ->default(false),

                TextInput::make('order')
                    ->label('Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('question.text')->label('Question')->limit(40)->searchable(),
                TextColumn::make('text')->label('Choice')->limit(40)->searchable(),
                TextColumn::make('is_correct')->label('Correct')->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                TextColumn::make('order')->label('Order')->sortable(),
                TextColumn::make('updated_at')->dateTime('Y-m-d H:i')->label('Updated')->sortable(),
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
            'index' => Pages\ListChoices::route('/'),
            'create' => Pages\CreateChoice::route('/create'),
            'edit' => Pages\EditChoice::route('/{record}/edit'),
        ];
    }
}
