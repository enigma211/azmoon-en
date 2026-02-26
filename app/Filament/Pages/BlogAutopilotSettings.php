<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use App\Models\Setting;
use App\Models\Category;

class BlogAutopilotSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Autopilot Settings';
    protected static ?string $title = 'Blog Autopilot Settings';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.admin-settings-page'; // Reuse the view from AdminSettingsPage

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'avalai_api_key',
            'avalai_base_url',
            'autopilot_prompt',
            'autopilot_rss_feeds',
            'autopilot_category_id',
        ])->pluck('value', 'key')->toArray();

        // Default values
        $settings['avalai_base_url'] = $settings['avalai_base_url'] ?? 'https://api.avalai.ir/v1';
        
        $defaultPrompt = "Please rewrite the following news article to be an engaging blog post for CDL drivers in English. Suggest an attractive title and provide a short summary. Format the output in JSON with 'title', 'summary', and 'content' keys. Ensure the 'content' is well-formatted HTML (using paragraphs, headings, and lists where appropriate). Here is the article:\n\n";
        $settings['autopilot_prompt'] = $settings['autopilot_prompt'] ?? $defaultPrompt;

        $this->form->fill($settings);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('API Configuration')
                    ->description('Settings for the AI provider (Avalai)')
                    ->schema([
                        TextInput::make('avalai_api_key')
                            ->label('Avalai API Key')
                            ->password()
                            ->required(),
                        TextInput::make('avalai_base_url')
                            ->label('Avalai Base URL')
                            ->required()
                            ->helperText('Use https://api.avalai.ir/v1 or https://api.avalapis.ir/v1'),
                    ]),

                Section::make('Autopilot Content Strategy')
                    ->description('Configure how the AI should rewrite the content')
                    ->schema([
                        Textarea::make('autopilot_prompt')
                            ->label('System Prompt')
                            ->rows(6)
                            ->required()
                            ->helperText("This prompt will be sent to the AI along with the fetched news content. Ask it to output JSON with 'title', 'summary', and 'content' keys."),
                        Select::make('autopilot_category_id')
                            ->label('Default Category for Auto-Generated Posts')
                            ->options(Category::pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('RSS Sources')
                    ->description('List the RSS feeds to fetch news from')
                    ->schema([
                        Textarea::make('autopilot_rss_feeds')
                            ->label('RSS Feed URLs')
                            ->rows(5)
                            ->placeholder("https://cdllife.com/feed/\nhttps://www.truckinginfo.com/rss")
                            ->helperText('Enter one RSS feed URL per line.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        Notification::make()
            ->title('Autopilot settings saved successfully')
            ->success()
            ->send();
    }
}
