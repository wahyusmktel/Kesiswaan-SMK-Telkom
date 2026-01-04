<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        
        try {
            // Cek apakah tabel app_settings sudah ada
            if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                \Illuminate\Support\Facades\View::share('appSetting', \App\Models\AppSetting::first());
            } else {
                // Jika tabel belum ada, share null atau object kosong
                \Illuminate\Support\Facades\View::share('appSetting', null);
            }
        } catch (\Exception $e) {
            // Jika terjadi error, share null
            \Illuminate\Support\Facades\View::share('appSetting', null);
        }
    }
}
