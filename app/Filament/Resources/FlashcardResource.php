<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashcardResource\Pages;
use App\Models\Flashcard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class FlashcardResource extends Resource
{
    protected static ?string $model = Flashcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Flashcards (Leitner)';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('deck_id')
                    ->relationship('deck', 'title')
                    ->required(),
                Forms\Components\Section::make('Content')
                    ->schema([
                        TinyEditor::make('front_content')
                            ->label('Front (Question)')
                            ->required()
                            ->columnSpanFull(),
                        TinyEditor::make('back_content')
                            ->label('Back (Answer)')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deck.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('front_content')
                    ->html()
                    ->limit(50)
                    ->label('Front'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('deck')
                    ->relationship('deck', 'title'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFlashcards::route('/'),
            'create' => Pages\CreateFlashcard::route('/create'),
            'edit' => Pages\EditFlashcard::route('/{record}/edit'),
        ];
    }
}
