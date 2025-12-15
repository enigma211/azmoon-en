<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Blog List';
    protected static ?string $modelLabel = 'Blog';
    protected static ?string $pluralModelLabel = 'Blog';
    protected static ?string $navigationGroup = 'Blog & Articles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'title')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('title')->label('Title')->required(),
                                Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                            ]),

                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->default(fn () => 'post-' . rand(1000, 9999))
                            ->readOnly()
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true),

                        Forms\Components\Textarea::make('summary')
                            ->label('Summary')
                            ->rows(3)
                            ->columnSpanFull(),

                        TinyEditor::make('content')
                            ->label('Content')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('posts'),

                        Forms\Components\TagsInput::make('meta_keywords')
                            ->label('SEO Keywords (Press Enter)')
                            ->separator(',')
                            ->suggestions(function () {
                                $keywords = Post::pluck('meta_keywords')->filter()->flatMap(function ($item) {
                                    return explode(',', $item);
                                })->unique()->values()->toArray();
                                return $keywords;
                            }),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->default(now()),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Featured Image')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Image')
                            ->image()
                            ->directory('posts')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.title')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publish Date')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
