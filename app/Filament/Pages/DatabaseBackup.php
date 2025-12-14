<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;

class DatabaseBackup extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.pages.database-backup';

    protected static ?string $navigationLabel = 'پشتیبان‌گیری';

    protected static ?string $title = 'پشتیبان‌گیری دیتابیس';

    protected static ?string $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 99;

    public function getBackups(): array
    {
        $disk = Storage::disk('local');
        // Support both our custom path and Spatie default path
        $paths = ['backups', 'laravel-backup'];

        $backups = [];
        foreach ($paths as $backupPath) {
            if (!$disk->exists($backupPath)) {
                continue;
            }

            // Recursively scan directories
            $files = collect($disk->allFiles($backupPath))
                ->filter(fn ($file) => str_ends_with($file, '.sql') || str_ends_with($file, '.zip'));

            foreach ($files as $file) {
                $backups[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => $this->formatBytes($disk->size($file)),
                    'date' => date('Y/m/d H:i', $disk->lastModified($file)),
                ];
            }
        }

        // Sort by date descending
        usort($backups, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return $backups;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('ایجاد بک‌آپ جدید')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('ایجاد بک‌آپ دیتابیس')
                ->modalDescription('آیا مطمئن هستید که می‌خواهید از دیتابیس بک‌آپ بگیرید؟')
                ->modalSubmitActionLabel('بله، بک‌آپ بگیر')
                ->action(function () {
                    try {
                        $this->createBackup();
                        
                        Notification::make()
                            ->title('بک‌آپ با موفقیت ایجاد شد')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطا در ایجاد بک‌آپ')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function createBackup(): void
    {
        // Prefer Spatie's backup command which handles zipping and cleanup
        // Will create a zip under storage/app/laravel-backup by default
        $exitCode = Artisan::call('backup:run', [
            '--only-db' => true,
        ]);

        if ($exitCode !== 0) {
            // Try to surface the last output for debugging
            $output = Artisan::output();
            throw new \Exception('Backup command failed. ' . ($output ?: ''));
        }
    }

    public function downloadBackup(string $path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($path)) {
            Notification::make()
                ->title('فایل بک‌آپ یافت نشد')
                ->danger()
                ->send();

            return redirect()->back();
        }

        return $disk->download($path);
    }

    public function deleteBackup(string $path): void
    {
        $disk = Storage::disk('local');

        if ($disk->exists($path)) {
            $disk->delete($path);

            Notification::make()
                ->title('بک‌آپ با موفقیت حذف شد')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('فایل بک‌آپ یافت نشد')
                ->danger()
                ->send();
        }
    }
}
