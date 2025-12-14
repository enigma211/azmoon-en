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
        return 'اسلایدرها';
    }

    public static function getModelLabel(): string
    {
        return 'اسلایدر';
    }

    public static function getPluralModelLabel(): string
    {
        return 'اسلایدرها';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'محتوا';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('راهنمای تصویر')
                    ->description('📱 برای نمایش بهینه در موبایل عمودی، از تصاویر با نسبت 16:9 استفاده کنید. سایز پیشنهادی: 1080×600 پیکسل')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('image_guide')
                            ->label('')
                            ->content('
                                ✅ سایز پیشنهادی: 1080×600 پیکسل (نسبت 16:9)
                                ✅ فرمت: JPG یا PNG
                                ✅ حجم: حداکثر 2 مگابایت (توصیه: کمتر از 500KB)
                                ✅ متن مهم را در مرکز تصویر قرار دهید
                                ✅ از رنگ‌های متضاد برای خوانایی بهتر استفاده کنید
                            '),
                    ]),

                Forms\Components\Section::make('اطلاعات اسلایدر')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->maxLength(255)
                            ->helperText('عنوان اختیاری برای اسلایدر'),

                        Forms\Components\FileUpload::make('image')
                            ->label('تصویر')
                            ->image()
                            ->required()
                            ->directory('sliders')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('📱 سایز پیشنهادی: 1080×600 پیکسل (نسبت 16:9) | حداکثر حجم: 2 مگابایت | برای موبایل عمودی بهینه شده است'),

                        Forms\Components\TextInput::make('link')
                            ->label('لینک')
                            ->url()
                            ->maxLength(255)
                            ->helperText('لینک اختیاری که با کلیک روی اسلایدر باز می‌شود'),

                        Forms\Components\TextInput::make('order')
                            ->label('ترتیب نمایش')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('عدد کوچکتر = نمایش زودتر'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال')
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
                    ->label('عنوان')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('تصویر')
                    ->size(80),

                Tables\Columns\TextColumn::make('order')
                    ->label('ترتیب')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
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
