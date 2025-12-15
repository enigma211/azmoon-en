<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return 'Sliders';
    }

    public static function getModelLabel(): string
    {
        return 'Slider';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sliders';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image Guide')
                    ->description('📱 For optimal mobile display, use 16:9 aspect ratio. Recommended size: 1080x600 pixels')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('image_guide')
                            ->label('')
                            ->content('
                                ✅ Recommended size: 1080x600 pixels (16:9 aspect ratio)
                                ✅ Format: JPG or PNG
                                ✅ Max size: 2MB (Recommended: under 500KB)
                                ✅ Place important text in the center
                                ✅ Use contrasting colors for better readability
                            '),
                    ]),

                Forms\Components\Section::make('Slider Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->helperText('Optional title for the slider'),

                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->required()
                            ->directory('sliders')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('📱 Recommended size: 1080x600 pixels (16:9) | Max size: 2MB'),

                        Forms\Components\TextInput::make('link')
                            ->label('Link')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Optional link to open when clicking the slider'),

                        Forms\Components\TextInput::make('order')
                            ->label('Sort Order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower number = Shows first'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ]),
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

                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->size(80),

                Tables\Columns\TextColumn::make('order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->formatStateUsing(fn ($state) => formatDate($state))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
