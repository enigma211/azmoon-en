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
    protected static ?string $navigationLabel = 'Visit Logs';
    protected static ?string $modelLabel = 'Visit Log';
    protected static ?string $pluralModelLabel = 'Visit Logs';
    protected static ?string $navigationGroup = 'Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip')
                    ->label('IP Address')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->label('Browser')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Visit Date')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Browser / Device')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Visit Time')
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
