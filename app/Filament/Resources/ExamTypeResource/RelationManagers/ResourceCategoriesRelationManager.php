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
        return 'دسته‌بندی‌های منابع';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('نوع')
                    ->options([
                        'video' => 'ویدیو آموزشی',
                        'document' => 'جزوه آموزشی',
                    ])
                    ->required()
                    ->disabled(),
                
                Forms\Components\TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(2),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'success',
                        'document' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'video' => 'ویدیو',
                        'document' => 'جزوه',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('posts_count')
                    ->label('تعداد پست‌ها')
                    ->counts('posts')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                // دسته‌بندی‌ها به صورت خودکار ایجاد می‌شوند
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
        return false; // دسته‌بندی‌ها به صورت خودکار ایجاد می‌شوند
    }

    public function canDelete($record): bool
    {
        return false; // جلوگیری از حذف دسته‌بندی‌های اصلی
    }
}
