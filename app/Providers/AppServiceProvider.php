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
                \Illuminate\Support\Facades\View::share('appSetting', null);
            }

            // Auto-deploy banner pengumuman navigasi jika belum ada di public/images
            $bannerDest = public_path('images/announcements/navigation-update-banner.png');
            $bannerSrc = 'C:\\Users\\TJ\\.gemini\\antigravity\\brain\\ae0bbda7-c9e5-49a3-a810-d7d82ed44dff\\navigation_update_banner_1791900532898.jpg';
            if (!file_exists($bannerDest) && file_exists($bannerSrc)) {
                @mkdir(dirname($bannerDest), 0777, true);
                @copy($bannerSrc, $bannerDest);
            }
        } catch (\Exception $e) {
            // Jika terjadi error, share null
            \Illuminate\Support\Facades\View::share('appSetting', null);
        }
    }
}
