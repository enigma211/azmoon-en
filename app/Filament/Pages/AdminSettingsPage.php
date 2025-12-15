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
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $title = 'System Settings';
    protected static ?string $navigationGroup = 'System';
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
                Section::make('Branding Settings')
                    ->description('Visual identity and main site titles settings')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('hero_title')
                            ->label('Hero Title')
                            ->helperText('Text displayed in the main hero banner')
                            ->maxLength(255),
                        TextInput::make('hero_description')
                            ->label('Hero Description')
                            ->helperText('Short description displayed below the main title')
                            ->maxLength(500),
                    ]),

                Section::make('SEO Settings')
                    ->description('Search engine and meta tag settings')
                    ->schema([
                        TextInput::make('seo_title')
                            ->label('SEO Title (Title Tag)')
                            ->helperText('Title displayed in browser tab and Google results')
                            ->maxLength(70),
                        TextInput::make('site_description')
                            ->label('Meta Description')
                            ->helperText('Description displayed in search results below the title')
                            ->maxLength(160),
                        TextInput::make('seo_keywords')
                            ->label('Keywords')
                            ->helperText('Separate keywords with commas')
                            ->maxLength(500),
                    ]),

                Section::make('Page Content')
                    ->description('Manage static page content')
                    ->collapsed()
                    ->schema([
                        TinyEditor::make('terms_content')
                            ->label('Terms and Conditions Content')
                            ->columnSpanFull(),
                        
                        TinyEditor::make('about_content')
                            ->label('About Us Content')
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
            ->title('Settings saved successfully')
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
