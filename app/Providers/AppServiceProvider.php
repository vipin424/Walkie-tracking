<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Register QrCode alias for Blade templates
        if (!class_exists('QrCode')) {
            class_alias('SimpleSoftwareIO\QrCode\Facades\QrCode', 'QrCode');
        }
    }
}
