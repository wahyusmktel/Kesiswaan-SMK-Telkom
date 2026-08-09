<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;

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
        if (class_exists(\Laravel\Passport\Passport::class)) {
            Passport::authorizationView('auth.sso.authorize');
            Passport::tokensCan([
                'profile:read' => 'Membaca identitas dasar, email, foto profil, dan peran akun SISFO.',
            ]);
            Passport::setDefaultScope(['profile:read']);
            Passport::tokensExpireIn(now()->addHour());
            Passport::refreshTokensExpireIn(now()->addDays(30));
        }

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
