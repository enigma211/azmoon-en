<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dynamically schedule the news fetch command based on settings
if (Schema::hasTable('settings')) {
    $interval = Setting::where('key', 'autopilot_schedule_interval')->value('value') ?? '24';
    
    $scheduledCommand = Schedule::command('news:fetch');
    
    switch ($interval) {
        case '1':
            $scheduledCommand->hourly();
            break;
        case '6':
            $scheduledCommand->everySixHours();
            break;
        case '12':
            $scheduledCommand->twiceDaily();
            break;
        case '24':
        default:
            $scheduledCommand->dailyAt('02:00');
            break;
    }
} else {
    Schedule::command('news:fetch')->dailyAt('02:00'); // Fallback if settings table doesn't exist yet
}
