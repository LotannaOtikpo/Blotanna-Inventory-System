<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

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
        try {
            // Set application timezone dynamically based on settings
            if (Schema::hasTable('settings')) {
                $timezone = Setting::getValue('timezone', 'Africa/Lagos');
                
                // Update Laravel config
                Config::set('app.timezone', $timezone);
                
                // Update PHP default timezone
                date_default_timezone_set($timezone);
            }
        } catch (\Exception $e) {
            // Fallback to default if database is not accessible
        }
    }
}
