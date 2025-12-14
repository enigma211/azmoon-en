<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.admin-dashboard';
    protected static ?string $navigationLabel = 'داشبورد مدیریتی';
    protected static ?string $navigationGroup = 'گزارش‌ها و KPI';
    
    // Hide from navigation
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
