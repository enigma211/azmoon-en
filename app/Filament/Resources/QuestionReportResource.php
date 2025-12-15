<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionReportResource\Pages;
use App\Filament\Resources\QuestionReportResource\RelationManagers;
use App\Models\QuestionReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionReportResource extends Resource
{
    protected static ?string $model = QuestionReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';
    
    protected static ?string $navigationLabel = 'Question Reports';
    
    protected static ?string $modelLabel = 'Question Report';
    
    protected static ?string $pluralModelLabel = 'Question Reports';
    
    protected static ?string $navigationGroup = 'Support';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->disabled(),
                Forms\Components\Select::make('question_id')
                    ->label('Question')
                    ->relationship('question', 'id')
                    ->required()
                    ->disabled(),
                Forms\Components\Placeholder::make('question_preview')
                    ->label('Question Preview')
                    ->content(function ($record) {
                        if (!$record || !$record->question) {
                            return 'Question not found';
                        }
                        $text = strip_tags($record->question->text);
                        return strlen($text) > 200 ? substr($text, 0, 200) . '...' : $text;
                    }),
                Forms\Components\Select::make('exam_id')
                    ->label('Exam')
                    ->relationship('exam', 'title')
                    ->searchable()
                    ->disabled(),
                Forms\Components\Textarea::make('report')
                    ->label('Report Text')
                    ->required()
                    ->disabled()
                    ->rows(4),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('exam.title')
                    ->label('Exam')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('question_id')
                    ->label('Question ID')
                    ->sortable()
                    ->url(fn ($record) => $record->question_id 
                        ? route('filament.admin.resources.questions.edit', ['record' => $record->question_id])
                        : null)
                    ->color('primary')
                    ->icon('heroicon-o-pencil-square'),
                Tables\Columns\TextColumn::make('report')
                    ->label('Report')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'reviewed',
                        'success' => 'resolved',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'reviewed' => 'Reviewed',
                        'resolved' => 'Resolved',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('edit_question')
                    ->label('Edit Question')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn ($record) => $record->question_id 
                        ? route('filament.admin.resources.questions.edit', ['record' => $record->question_id])
                        : null)
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListQuestionReports::route('/'),
            'edit' => Pages\EditQuestionReport::route('/{record}/edit'),
        ];
    }
}
