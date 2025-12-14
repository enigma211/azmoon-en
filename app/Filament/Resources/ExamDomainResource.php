<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamDomainResource\Pages;
use App\Filament\Resources\ExamDomainResource\RelationManagers;
use App\Models\ExamDomain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExamDomainResource extends Resource
{
    protected static ?string $model = \App\Models\ExamDomain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return 'دامنه‌های آزمون';
    }

    public static function getModelLabel(): string
    {
        return 'دامنه آزمون';
    }

    public static function getPluralModelLabel(): string
    {
        return 'دامنه‌های آزمون';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'آزمون‌ها';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات دامنه')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('اسلاگ')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            // Allow Unicode letters/numbers and dashes (supports Persian). No auto-changes.
                            ->regex('/^[\p{L}\p{N}\-]+$/u')
                            ->helperText('اسلاگ را دستی وارد کنید: حروف/اعداد و خط‌تیره.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('فعال؟')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تنظیمات سئو')
                    ->description('این اطلاعات برای بهبود رتبه صفحه در موتورهای جستجو استفاده می‌شود.')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('عنوان سئو (Meta Title)')
                            ->maxLength(60)
                            ->helperText('توصیه می‌شود بین 50 تا 60 کاراکتر باشد.'),

                        Forms\Components\Textarea::make('seo_description')
                            ->label('توضیحات سئو (Meta Description)')
                            ->rows(3)
                            ->maxLength(250)
                            ->helperText('توصیه می‌شود بین 200 تا 250 کاراکتر باشد.'),
                    ])->collapsible(),
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
                    ->label('اسلاگ')
                    ->badge()
                    ->copyable()
                    ->copyMessage('کپی شد')
                    ->copyMessageDuration(1500)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->formatStateUsing(fn ($state) => formatDate($state))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListExamDomains::route('/'),
            'create' => Pages\CreateExamDomain::route('/create'),
            'edit' => Pages\EditExamDomain::route('/{record}/edit'),
        ];
    }
}
