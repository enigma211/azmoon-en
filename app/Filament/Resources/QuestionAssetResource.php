<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionAssetResource\Pages;
use App\Filament\Resources\QuestionAssetResource\RelationManagers;
use App\Models\QuestionAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class QuestionAssetResource extends Resource
{
    protected static ?string $model = QuestionAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Question Assets';
    }

    public static function getModelLabel(): string
    {
        return 'Question Asset';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Question Assets';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Question Bank';
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hide this resource from the admin menu per request
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

                Select::make('type')
                    ->label('Type')
                    ->options([
                        'image' => 'Image',
                        'file' => 'File',
                        'pdf' => 'PDF',
                        'video' => 'Video',
                    ])
                    ->default('image')
                    ->required(),

                FileUpload::make('path')
                    ->label('File')
                    ->directory('question-assets')
                    ->image()
                    ->openable()
                    ->downloadable()
                    ->required(),

                TextInput::make('caption')
                    ->label('Caption')
                    ->nullable(),

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
                TextColumn::make('type')->label('Type')->sortable(),
                TextColumn::make('caption')->label('Caption')->limit(40),
                TextColumn::make('updated_at')->formatStateUsing(fn ($state) => formatDateTime($state))->label('Updated At')->sortable(),
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
            'index' => Pages\ListQuestionAssets::route('/'),
            'create' => Pages\CreateQuestionAsset::route('/create'),
            'edit' => Pages\EditQuestionAsset::route('/{record}/edit'),
        ];
    }
}
