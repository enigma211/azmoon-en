<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class BrandingSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static string $view = 'filament.pages.branding-settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 11;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Branding Settings (Logo & Icon)';
    }

    public static function getSlug(): string
    {
        return 'branding-settings';
    }

    public function mount(): void
    {
        $settings = SystemSetting::first();
        $this->form->fill([
            'logo' => $settings?->logo,
            'favicon' => $settings?->favicon,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Logo & Icon Settings')
                    ->description('Upload site logo and favicon here.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Site Logo (PNG)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('branding')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('PNG file with max size 2MB')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),

                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon (PNG or ICO)')
                            ->image()
                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->directory('branding')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(512)
                            ->helperText('PNG or ICO file, max 512KB (Recommended: 32x32 or 64x64 pixels)'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = SystemSetting::first();
        if (!$settings) {
            $settings = SystemSetting::create(['key' => 'global']);
        }
        
        $settings->update([
            'logo' => $data['logo'],
            'favicon' => $data['favicon'],
        ]);

        // Sync favicon to public root for SEO and browser compatibility
        if (!empty($data['favicon'])) {
            try {
                $path = Storage::disk('public')->path($data['favicon']);
                if (file_exists($path)) {
                    @copy($path, public_path('favicon.png'));
                    @copy($path, public_path('favicon.ico'));
                }
            } catch (\Exception $e) {
                // Ignore copy errors to prevent blocking the save
            }
        } else {
            // Remove public favicons if deleted from settings
            @unlink(public_path('favicon.png'));
            @unlink(public_path('favicon.ico'));
        }

        Notification::make()
            ->success()
            ->title('Branding settings saved')
            ->body('Logo and favicon updated successfully.')
            ->send();
    }
}
