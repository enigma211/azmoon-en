<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitLogResource\Pages;
use App\Filament\Resources\VisitLogResource\RelationManagers;
use App\Models\VisitLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitLogResource extends Resource
{
    protected static ?string $model = VisitLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationLabel = 'لیست بازدیدها';
    protected static ?string $modelLabel = 'بازدید';
    protected static ?string $pluralModelLabel = 'لیست بازدیدها';
    protected static ?string $navigationGroup = 'گزارشات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip')
                    ->label('آی‌پی')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->label('مرورگر')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('تاریخ بازدید')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                    ->label('آی‌پی')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('مرورگر / دستگاه')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('زمان بازدید')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageVisitLogs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
       return false;
    }
}
