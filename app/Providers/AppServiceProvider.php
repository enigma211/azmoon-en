<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || str_contains(config('app.url'), 'https')) {
            URL::forceScheme('https');
        }

        try {
            $settings = \App\Models\SystemSetting::first();
            if ($settings && $settings->site_name) {
                config(['app.name' => $settings->site_name]);
            }
        } catch (\Exception $e) {
            // Ignored during migrations or if table doesn't exist
        }

        Blade::directive('formatDate', function ($expression) {
            // Usage: @formatDate($date, 'Y/m/d') or @formatDate($date)
            // Relies on the global formatDate() helper in app/helpers.php which now uses Carbon
            return "<?php echo formatDate($expression); ?>";
        });
    }
}
