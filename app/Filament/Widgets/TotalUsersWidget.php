<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUsersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();

        return [
            Stat::make('تعداد کاربران ثبت‌نام شده', $totalUsers)
                ->description('کل کاربران سیستم')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
