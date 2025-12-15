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
            Stat::make('Registered Users', $totalUsers)
                ->description('Total system users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
