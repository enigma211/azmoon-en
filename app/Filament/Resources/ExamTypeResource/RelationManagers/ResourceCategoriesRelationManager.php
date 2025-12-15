<?php

namespace App\Filament\Resources\ExamTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResourceCategoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'resourceCategories';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return 'Resource Categories';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'video' => 'Educational Video',
                        'document' => 'Educational Document',
                    ])
                    ->required()
                    ->disabled(),
                
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'success',
                        'document' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'video' => 'Video',
                        'document' => 'Document',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('Posts Count')
                    ->counts('posts')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                // Categories are automatically created
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public function canCreate(): bool
    {
        return false; // Categories are automatically created
    }

    public function canDelete($record): bool
    {
        return false; // Prevent deletion of main categories
    }
}
