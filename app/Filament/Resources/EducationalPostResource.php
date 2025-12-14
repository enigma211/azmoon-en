<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationalPostResource\Pages;
use App\Models\EducationalPost;
use App\Models\ResourceCategory;
use Filament\Forms;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EducationalPostResource extends Resource
{
    protected static ?string $model = EducationalPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'پست‌های آموزشی';
    
    protected static ?string $modelLabel = 'پست آموزشی';
    
    protected static ?string $pluralModelLabel = 'پست‌های آموزشی';
    
    protected static ?string $navigationGroup = 'منابع آموزشی';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات اصلی')
                    ->schema([
                        Forms\Components\Select::make('resource_category_id')
                            ->label('دسته‌بندی')
                            ->options(function () {
                                return ResourceCategory::with('examType')
                                    ->get()
                                    ->mapWithKeys(function ($category) {
                                        $type = $category->type === 'video' ? 'ویدیو' : 'جزوه';
                                        return [$category->id => $category->examType->title . ' - ' . $type];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('video_embed_code', null)),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('نامک (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('به صورت خودکار از عنوان ساخته می‌شود')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('توضیحات کوتاه')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('تصویر شاخص')
                            ->image()
                            ->directory('educational-posts/thumbnails')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('محتوای ویدیو')
                    ->schema([
                        Forms\Components\Textarea::make('video_embed_code')
                            ->label('کد Embed ویدیو (آپارات)')
                            ->rows(5)
                            ->helperText('کد embed را از سایت آپارات کپی کنید')
                            ->placeholder('<script src="https://www.aparat.com/embed/...">')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => 
                        $get('resource_category_id') && 
                        ResourceCategory::find($get('resource_category_id'))?->type === 'video'
                    ),

                Forms\Components\Section::make('فایل جزوه')
                    ->schema([
                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('فایل PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('educational-posts/documents')
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->helperText('حداکثر حجم: 10 مگابایت')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => 
                        $get('resource_category_id') && 
                        ResourceCategory::find($get('resource_category_id'))?->type === 'document'
                    ),

                Forms\Components\Section::make('محتوای متنی')
                    ->schema([
                        TinyEditor::make('content')
                            ->label('محتوا')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('تنظیمات')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('تاریخ انتشار')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('پست ویژه')
                            ->default(false)
                            ->helperText('در صفحه اصلی نمایش داده شود'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('تصویر')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category.examType.title')
                    ->label('نوع آزمون')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('category.type')
                    ->label('نوع محتوا')
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
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label('بازدید')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('download_count')
                    ->label('دانلود')
                    ->sortable()
                    ->alignCenter()
                    ->visible(fn ($record) => $record && $record->category?->type === 'document'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('ویژه')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاریخ انتشار')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع محتوا')
                    ->options([
                        'video' => 'ویدیو',
                        'document' => 'جزوه',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('category', function ($q) use ($data) {
                                $q->where('type', $data['value']);
                            });
                        }
                    }),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('وضعیت')
                    ->placeholder('همه')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('ویژه')
                    ->placeholder('همه')
                    ->trueLabel('ویژه')
                    ->falseLabel('عادی'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducationalPosts::route('/'),
            'create' => Pages\CreateEducationalPost::route('/create'),
            'edit' => Pages\EditEducationalPost::route('/{record}/edit'),
        ];
    }
}
