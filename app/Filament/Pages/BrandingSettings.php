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

class BrandingSettings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static string $view = 'filament.pages.branding-settings';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 11;

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'تنظیمات برندینگ (لوگو و آیکن)';
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
                Forms\Components\Section::make('تنظیمات لوگو و آیکن')
                    ->description('لوگو و favicon سایت را در اینجا آپلود کنید.')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('لوگو سایت (PNG)')
                            ->image()
                            ->acceptedFileTypes(['image/png'])
                            ->directory('branding')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('فایل PNG با حداکثر حجم 2MB')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ]),

                        Forms\Components\FileUpload::make('favicon')
                            ->label('Favicon (PNG یا ICO)')
                            ->image()
                            ->acceptedFileTypes(['image/png', 'image/x-icon', 'image/vnd.microsoft.icon'])
                            ->directory('branding')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(512)
                            ->helperText('فایل PNG یا ICO با حداکثر حجم 512KB (توصیه: 32x32 یا 64x64 پیکسل)'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('ذخیره تغییرات')
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
            'logo' => $data['logo'] ?? $settings->logo,
            'favicon' => $data['favicon'] ?? $settings->favicon,
        ]);

        Notification::make()
            ->success()
            ->title('تنظیمات برندینگ ذخیره شد')
            ->body('لوگو و favicon با موفقیت به‌روزرسانی شدند.')
            ->send();
    }
}
