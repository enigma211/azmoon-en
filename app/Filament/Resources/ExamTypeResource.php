<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamTypeResource\Pages;
use App\Filament\Resources\ExamTypeResource\RelationManagers;
use App\Models\ExamType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamTypeResource extends Resource
{
    protected static ?string $model = ExamType::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'انواع آزمون';
    
    protected static ?string $modelLabel = 'نوع آزمون';
    
    protected static ?string $pluralModelLabel = 'انواع آزمون';
    
    protected static ?string $navigationGroup = 'منابع آموزشی';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('نامک (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('به صورت خودکار از عنوان ساخته می‌شود'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('icon')
                            ->label('آیکون')
                            ->helperText('نام آیکون Heroicon (مثال: academic-cap)')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('slug')
                    ->label('نامک')
                    ->searchable()
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('resourceCategories_count')
                    ->label('تعداد دسته‌بندی‌ها')
                    ->counts('resourceCategories')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('وضعیت')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
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
            RelationManagers\ResourceCategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamTypes::route('/'),
            'create' => Pages\CreateExamType::route('/create'),
            'edit' => Pages\EditExamType::route('/{record}/edit'),
        ];
    }
}
