<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamDomainResource\Pages;
use App\Filament\Resources\ExamDomainResource\RelationManagers;
use App\Models\ExamDomain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamDomainResource extends Resource
{
    protected static ?string $model = \App\Models\ExamDomain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'Exam Domains';
    }

    public static function getModelLabel(): string
    {
        return 'Exam Domain';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Exam Domains';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Exams';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Domain Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            // Allow Unicode letters/numbers and dashes (supports Persian). No auto-changes.
                            ->regex('/^[\p{L}\p{N}\-]+$/u')
                            ->helperText('Enter slug manually: letters/numbers and dashes.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active?')
                            ->default(true),

                        Forms\Components\Toggle::make('generate_us_states')
                            ->label('Generate Batches for 50 US States')
                            ->helperText('If checked, an Exam Batch will be created for each US State automatically.')
                            ->default(false)
                            ->dehydrated(false),

                        TinyEditor::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('SEO Settings')
                    ->description('This information is used to improve page ranking in search engines.')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO Title (Meta Title)')
                            ->maxLength(255)
                            ->helperText('Recommended length is 50-60 characters.'),

                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO Description (Meta Description)')
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('Recommended length is 200-250 characters.'),
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

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->copyable()
                    ->copyMessage('Copied')
                    ->copyMessageDuration(1500)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->formatStateUsing(fn ($state) => formatDate($state))
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
            'index' => Pages\ListExamDomains::route('/'),
            'create' => Pages\CreateExamDomain::route('/create'),
            'edit' => Pages\EditExamDomain::route('/{record}/edit'),
        ];
    }
}
