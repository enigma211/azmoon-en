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
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run_fetch')
                ->label('Run Fetch Now (Test)')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Run News Fetcher')
                ->modalDescription('This will manually trigger the AI news fetcher in the background. It may take a few moments depending on the RSS feeds and AI API response time.')
                ->modalSubmitActionLabel('Yes, run it')
                ->action(function () {
                    try {
                        // Run the artisan command
                        Artisan::call('news:fetch');
                        $output = Artisan::output();
                        
                        Notification::make()
                            ->title('Fetch completed')
                            ->body('Command executed successfully. Check the blog to see if new posts were added.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error during fetch')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'avalai_api_key',
            'avalai_base_url',
            'autopilot_prompt',
            'autopilot_rss_feeds',
            'autopilot_category_id',
            'autopilot_min_posts_per_day',
            'autopilot_max_posts_per_day',
            'autopilot_schedule_interval',
        ])->pluck('value', 'key')->toArray();

        // Default values
        $settings['avalai_base_url'] = $settings['avalai_base_url'] ?? 'https://api.avalai.ir/v1';
        $settings['autopilot_min_posts_per_day'] = $settings['autopilot_min_posts_per_day'] ?? 1;
        $settings['autopilot_max_posts_per_day'] = $settings['autopilot_max_posts_per_day'] ?? 3;
        $settings['autopilot_schedule_interval'] = $settings['autopilot_schedule_interval'] ?? '12';
        
        $defaultPrompt = <<<EOT
You are an expert SEO content writer and a CDL (Commercial Driver's License) instructor.
I will provide you with a news article about the trucking industry.

CRITICAL FIRST STEP (GATEKEEPER):
Before rewriting, evaluate if this article is useful or relevant to new CDL drivers, students preparing for their CDL exams, or the daily life of a truck driver. 
If the article is strictly about corporate finance, stock market, executive changes, company acquisitions, or topics irrelevant to a driver's perspective, you MUST classify it as irrelevant.

If it is IRRELEVANT:
Output ONLY this exact JSON object and nothing else:
{
  "is_relevant": false,
  "title": null,
  "meta_description": null,
  "content": null
}

If it IS RELEVANT, proceed to rewrite the entire article so that it is 100% unique and passes all plagiarism checks.

Crucial Requirements for Relevant Articles:
You MUST change the angle of the article to focus on how this news affects new CDL drivers, CDL exam students, or daily truck driving life.

Follow these rules strictly:
1. Create a catchy, SEO-optimized title (maximum 60 characters).
2. Write a very catchy meta description (maximum 155 characters).
3. The main content should be at least 5 paragraphs long. Use Markdown formatting (H2, H3, bullet points).
4. Do not copy any sentences from the original text. Use your own words entirely.
5. Minimum article length must be 700 words. Add your own additional educational descriptions related to CDL if necessary to reach this word count.

Output ONLY a valid JSON object with the following keys:
{
  "is_relevant": true,
  "title": "Your generated title",
  "meta_description": "Your generated meta description",
  "content": "Your generated markdown content"
}

Main article text:

EOT;
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
                        TextInput::make('autopilot_min_posts_per_day')
                            ->label('Minimum Posts Per Day (Target)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('The system will try to fetch at least this many posts if available.'),
                        TextInput::make('autopilot_max_posts_per_day')
                            ->label('Maximum Posts Per Day (Limit)')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('The system will stop after generating this many posts in a single day to prevent spam.'),
                    ]),

                Section::make('Execution Settings')
                    ->description('Configure when and where to fetch news from')
                    ->schema([
                        Select::make('autopilot_schedule_interval')
                            ->label('Fetch Frequency')
                            ->options([
                                '1' => 'Every 1 Hour',
                                '6' => 'Every 6 Hours',
                                '12' => 'Every 12 Hours',
                                '24' => 'Once a Day (Every 24 Hours)',
                            ])
                            ->required()
                            ->helperText('How often should the background task run to check for new RSS articles.'),
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
