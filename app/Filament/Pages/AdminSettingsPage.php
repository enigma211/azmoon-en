<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;
use Filament\Notifications\Notification;

class AdminSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.admin-settings-page';
    protected static ?string $navigationLabel = 'تنظیمات سیستم';
    protected static ?string $title = 'تنظیمات سیستم';
    protected static ?string $navigationGroup = 'سیستم';
    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public static function getSlug(): string
    {
        return 'system-settings';
    }

    public function mount(): void
    {
        $settings = $this->getSettingsProperty();
        $this->form->fill($settings->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('تنظیمات برندینگ')
                    ->description('تنظیمات هویت بصری و عناوین اصلی سایت')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('نام سایت')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('hero_title')
                            ->label('تیتر اصلی صفحه اول')
                            ->helperText('متنی که در بنر اصلی صفحه اول نمایش داده می‌شود')
                            ->maxLength(255),
                        TextInput::make('hero_description')
                            ->label('توضیحات زیر تیتر')
                            ->helperText('توضیحات کوتاهی که زیر تیتر اصلی نمایش داده می‌شود')
                            ->maxLength(500),
                    ]),

                Section::make('تنظیمات سئو')
                    ->description('تنظیمات موتورهای جستجو و متا تگ‌ها')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('عنوان سئو (Title Tag)')
                            ->helperText('عنوانی که در تب مرورگر و نتایج گوگل نمایش داده می‌شود')
                            ->maxLength(70),
                        TextInput::make('site_description')
                            ->label('توضیحات متا (Meta Description)')
                            ->helperText('توضیحاتی که در نتایج جستجو زیر عنوان نمایش داده می‌شود')
                            ->maxLength(160),
                        TextInput::make('seo_keywords')
                            ->label('کلمات کلیدی')
                            ->helperText('کلمات کلیدی را با ویرگول جدا کنید')
                            ->maxLength(500),
                    ]),

                Section::make('محتوای صفحات')
                    ->description('مدیریت متن صفحات ثابت سایت')
                    ->collapsed()
                    ->schema([
                        TinyEditor::make('terms_content')
                            ->label('متن قوانین و مقررات')
                            ->columnSpanFull(),
                        
                        TinyEditor::make('about_content')
                            ->label('متن درباره ما')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = $this->getSettingsProperty();
        $settings->update($this->form->getState());

        Notification::make() 
            ->title('تنظیمات با موفقیت ذخیره شد')
            ->success()
            ->send();
    }

    public function getSettingsProperty(): SystemSetting
    {
        $settings = SystemSetting::first();
        if (! $settings) {
            $settings = SystemSetting::create([
                'key' => 'global',
            ]);
        }
        return $settings;
    }
}
