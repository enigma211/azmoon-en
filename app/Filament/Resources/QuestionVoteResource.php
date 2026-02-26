<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionVoteResource\Pages;
use App\Models\QuestionVote;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuestionVoteResource extends Resource
{
    protected static ?string $model = QuestionVote::class;

    protected static ?string $navigationIcon = 'heroicon-o-hand-thumb-up';
    
    protected static ?string $navigationGroup = 'Exam Management';
    
    protected static ?string $navigationLabel = 'Question Votes';
    
    protected static ?int $navigationSort = 5;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('question_id')
                    ->relationship('question', 'id')
                    ->disabled(),
                Forms\Components\TextInput::make('ip_address')
                    ->disabled(),
                Forms\Components\Select::make('vote_type')
                    ->options([
                        1 => 'Like',
                        -1 => 'Dislike',
                    ])
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_id')
                    ->label('Question ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('question.exam.title')
                    ->label('Exam Title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('question.text')
                    ->label('Question Text')
                    ->limit(50)
                    ->html()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('vote_type')
                    ->label('Vote')
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        1 => 'Like',
                        -1 => 'Dislike',
                        default => 'Unknown',
                    })
                    ->colors([
                        'success' => 1,
                        'danger' => -1,
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('vote_type')
                    ->options([
                        1 => 'Likes',
                        -1 => 'Dislikes',
                    ]),
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label('Exam')
                    ->relationship('question.exam', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageQuestionVotes::route('/'),
        ];
    }
}
