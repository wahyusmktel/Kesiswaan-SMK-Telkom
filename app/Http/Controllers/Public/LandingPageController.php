<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Berita;
use App\Models\DapodikGuru;
use App\Models\LandingHeroSlide;
use App\Models\LandingTicker;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index(Request $request)
    {
        $setting = AppSetting::first();
        $canPreviewVue = $request->query('preview') === 'stella-vue'
            && (app()->environment('local') || $request->user()?->hasRole('Super Admin'));

        if ($setting?->theme === 'stella-vue' || $canPreviewVue) {
            return $this->vueLanding($setting);
        }

        $today = now();
        $birthdaySiswa = MasterSiswa::with(['rombels.kelas'])
            ->whereNotNull('tanggal_lahir')
            ->whereMonth('tanggal_lahir', $today->month)
            ->whereDay('tanggal_lahir', $today->day)
            ->get(['id', 'nama_lengkap', 'nis', 'tanggal_lahir', 'jenis_kelamin', 'tempat_lahir']);
        $birthdayGuru = DapodikGuru::whereNotNull('tanggal_lahir')
            ->whereMonth('tanggal_lahir', $today->month)
            ->whereDay('tanggal_lahir', $today->day)
            ->get(['nama']);

        $landingView = match ($setting?->theme) {
            'transformasi' => 'welcome-transformasi',
            'ajaran-baru' => 'welcome-ajaran-baru',
            default => 'welcome',
        };

        return view($landingView, compact('birthdaySiswa', 'birthdayGuru'));
    }

    private function vueLanding(?AppSetting $setting)
    {
        $setting ??= new AppSetting(['school_name' => 'SMK Telkom Lampung']);

        $slides = LandingHeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (LandingHeroSlide $slide) => [
                'id' => $slide->id,
                'eyebrow' => $slide->eyebrow ?: 'SISFO TS',
                'title' => $slide->title,
                'description' => $slide->description,
                'image' => $slide->image_url,
                'ctaLabel' => $slide->cta_label,
                'ctaUrl' => $slide->cta_url,
            ])
            ->values();

        if ($slides->isEmpty()) {
            $slides = collect([
                [
                    'id' => 'default-1',
                    'eyebrow' => 'Ekosistem Sekolah Digital',
                    'title' => 'Satu sekolah. Satu data. Lebih banyak dampak.',
                    'description' => 'SISFO TS menghubungkan pembelajaran, layanan siswa, SDM, dan komunikasi sekolah dalam pengalaman digital yang utuh.',
                    'image' => asset('images/landing-vue/hero-school-ecosystem.png'),
                    'ctaLabel' => 'Jelajahi Layanan',
                    'ctaUrl' => '#layanan',
                ],
                [
                    'id' => 'default-2',
                    'eyebrow' => 'Connected by Design',
                    'title' => 'Operasional sekolah yang bergerak bersama.',
                    'description' => 'Dari data akademik hingga prakerin dan layanan konseling, setiap proses terhubung untuk keputusan yang lebih cepat.',
                    'image' => asset('images/landing-vue/integrated-modules.png'),
                    'ctaLabel' => 'Lihat Arsitektur',
                    'ctaUrl' => '#architecture',
                ],
            ]);
        }

        $tickers = LandingTicker::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'text', 'url'])
            ->values();

        if ($tickers->isEmpty()) {
            $tickers = collect([
                ['id' => 'default', 'text' => 'Selamat datang di ekosistem digital SMK Telkom Lampung.', 'url' => null],
            ]);
        }

        $news = Berita::published()
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(fn (Berita $item) => [
                'id' => $item->id,
                'title' => $item->judul,
                'summary' => $item->ringkasan,
                'category' => $item->kategori,
                'date' => $item->published_at?->translatedFormat('d M Y'),
                'image' => $item->gambar_url ?: asset('images/landing-vue/integrated-modules.png'),
                'url' => route('berita.show', $item->slug),
            ])
            ->values();

        $staffCount = User::query()
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'Guru Kelas']))
            ->whereHas('roles')
            ->count();

        $payload = [
            'school' => [
                'name' => $setting->school_name ?: 'SMK Telkom Lampung',
                'logo' => $setting->logo ? asset('storage/'.ltrim($setting->logo, '/')) : null,
                'address' => $setting->address,
                'email' => $setting->email,
                'phone' => $setting->phone,
            ],
            'authenticated' => auth()->check(),
            'slides' => $slides,
            'tickers' => $tickers,
            'statistics' => [
                ['label' => 'Siswa Aktif', 'value' => MasterSiswa::active()->count(), 'suffix' => ''],
                ['label' => 'Alumni', 'value' => MasterSiswa::alumni()->count(), 'suffix' => ''],
                [
                    'label' => 'Guru',
                    'value' => User::whereHas('roles', fn ($query) => $query->where('name', 'Guru Kelas'))->count(),
                    'suffix' => '',
                ],
                ['label' => 'Tenaga Kependidikan', 'value' => $staffCount, 'suffix' => ''],
            ],
            'news' => $news,
            'routes' => [
                'home' => route('welcome'),
                'complaint' => route('pengaduan.create'),
                'digireligi' => route('digireligi'),
                'gallery' => route('gallery-photo.index'),
                'forum' => route('forum-stella.index'),
                'login' => route('login'),
                'dashboard' => url('/dashboard'),
                'privacy' => route('privacy'),
                'terms' => route('terms'),
                'security' => route('security'),
            ],
            'assets' => [
                'modules' => asset('images/landing-vue/integrated-modules.png'),
                'serviceModules' => asset('images/landing-vue/service-modules-atlas.webp'),
            ],
        ];

        return view('welcome-vue', compact('payload', 'setting'));
    }
}
