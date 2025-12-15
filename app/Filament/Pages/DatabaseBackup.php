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

    protected static ?string $navigationLabel = 'Backups';

    protected static ?string $title = 'Database Backup';

    protected static ?string $navigationGroup = 'Settings';

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
                ->label('Create New Backup')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Create Database Backup')
                ->modalDescription('Are you sure you want to create a database backup?')
                ->modalSubmitActionLabel('Yes, create backup')
                ->action(function () {
                    try {
                        $this->createBackup();
                        
                        Notification::make()
                            ->title('Backup created successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error creating backup')
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
                ->title('Backup file not found')
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
                ->title('Backup deleted successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Backup file not found')
                ->danger()
                ->send();
        }
    }
}
