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
    
    protected static ?string $navigationLabel = 'Educational Posts';
    
    protected static ?string $modelLabel = 'Educational Post';
    
    protected static ?string $pluralModelLabel = 'Educational Posts';
    
    protected static ?string $navigationGroup = 'Educational Resources';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Main Information')
                    ->schema([
                        Forms\Components\Select::make('resource_category_id')
                            ->label('Category')
                            ->options(function () {
                                return ResourceCategory::with('examType')
                                    ->get()
                                    ->mapWithKeys(function ($category) {
                                        $type = $category->type === 'video' ? 'Video' : 'Document';
                                        return [$category->id => $category->examType->title . ' - ' . $type];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('video_embed_code', null)),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Automatically generated from title')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Short Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->image()
                            ->directory('educational-posts/thumbnails')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Video Content')
                    ->schema([
                        Forms\Components\Textarea::make('video_embed_code')
                            ->label('Video Embed Code (Aparat)')
                            ->rows(5)
                            ->helperText('Copy embed code from Aparat')
                            ->placeholder('<script src="https://www.aparat.com/embed/...">')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => 
                        $get('resource_category_id') && 
                        ResourceCategory::find($get('resource_category_id'))?->type === 'video'
                    ),

                Forms\Components\Section::make('Document File')
                    ->schema([
                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('PDF File')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('educational-posts/documents')
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->helperText('Max size: 10MB')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => 
                        $get('resource_category_id') && 
                        ResourceCategory::find($get('resource_category_id'))?->type === 'document'
                    ),

                Forms\Components\Section::make('Text Content')
                    ->schema([
                        TinyEditor::make('content')
                            ->label('Content')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Post')
                            ->default(false)
                            ->helperText('Show on home page'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Image')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category.examType.title')
                    ->label('Exam Type')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('category.type')
                    ->label('Content Type')
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
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->sortable()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->sortable()
                    ->alignCenter()
                    ->visible(fn ($record) => $record && $record->category?->type === 'document'),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('Content Type')
                    ->options([
                        'video' => 'Video',
                        'document' => 'Document',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('category', function ($q) use ($data) {
                                $q->where('type', $data['value']);
                            });
                        }
                    }),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
                
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All')
                    ->trueLabel('Featured')
                    ->falseLabel('Normal'),
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
