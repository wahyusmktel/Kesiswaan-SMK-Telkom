@php
    $schoolName = $appSetting?->school_name ?? 'SMK Telkom Lampung';
    $heroImages = collect($appSetting?->transformasi_slider_images ?? [])
        ->map(fn ($path) => Storage::url($path))
        ->values()
        ->all();

    if (count($heroImages) === 0) {
        $heroImages = [
            'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=85&w=1800',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=85&w=1800',
            'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=85&w=1800',
        ];
    }

    $heroSlides = [
        ['title' => 'Selamat Datang di Tahun Ajaran Baru', 'caption' => 'Awali semester dengan ekosistem digital sekolah yang lebih rapi, cepat, dan terhubung.'],
        ['title' => 'Belajar, Berkarya, Terhubung', 'caption' => 'Satu pintu untuk akademik, kesiswaan, SDM, prakerin, berita, dan layanan sekolah.'],
        ['title' => 'Sekolah Digital yang Siap Bergerak', 'caption' => 'Data real-time membantu guru, siswa, dan manajemen mengambil keputusan lebih cepat.'],
    ];

    $modules = [
        ['name' => 'Akademik', 'text' => 'Jadwal, presensi, rombel, nilai, dan transkrip dalam satu alur kerja.', 'color' => '#2563eb'],
        ['name' => 'Kesiswaan', 'text' => 'Perizinan, keterlambatan, poin kedisiplinan, dan kartu pelajar digital.', 'color' => '#dc2626'],
        ['name' => 'SDM', 'text' => 'Data pegawai, izin guru, tanda tangan digital, dan evaluasi kinerja.', 'color' => '#059669'],
        ['name' => 'Prakerin', 'text' => 'Mapping industri, absensi GPS, jurnal, bimbingan, dan monitoring siswa.', 'color' => '#ea580c'],
        ['name' => 'BK & Konseling', 'text' => 'Ruang konsultasi siswa, tindak lanjut, dan pendampingan perkembangan.', 'color' => '#7c3aed'],
        ['name' => 'Berita & Publikasi', 'text' => 'Informasi sekolah, agenda, prestasi, dan komunikasi publik.', 'color' => '#0891b2'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $schoolName }} - Tahun Ajaran Baru</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700;800;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif
    <style>
        * { letter-spacing: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f7f8fb; color: #101828; }
        h1, h2, h3, .font-outfit { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        .new-loader {
            position: fixed; inset: 0; z-index: 99999; display: grid; place-items: center;
            background: radial-gradient(circle at 50% 35%, rgba(239,68,68,.18), transparent 28rem), #07080d;
            transition: opacity .7s ease, visibility .7s ease;
        }
        .new-loader.hide { opacity: 0; visibility: hidden; pointer-events: none; }
        .loader-core { position: relative; width: 136px; height: 136px; }
        .loader-core span { position: absolute; inset: var(--i); border-radius: 999px; border: 1px solid rgba(255,255,255,.16); animation: loaderSpin var(--d) linear infinite; }
        .loader-core span::after { content: ''; position: absolute; top: -4px; left: 50%; width: 8px; height: 8px; border-radius: 999px; background: #ef4444; box-shadow: 0 0 26px #ef4444; }
        .loader-mark { position: absolute; inset: 42px; border-radius: 28px; display: grid; place-items: center; color: white; font-weight: 900; background: linear-gradient(135deg, #ef4444, #111827); box-shadow: 0 28px 80px rgba(239,68,68,.28); }
        @keyframes loaderSpin { to { transform: rotate(360deg); } }

        .ab-nav { transition: all .45s cubic-bezier(.2,.8,.2,1); }
        .ab-nav-inner { transition: all .45s cubic-bezier(.2,.8,.2,1); }
        .ab-nav.is-scrolled { padding: 0; }
        .ab-nav.is-scrolled .ab-nav-inner { max-width: 100%; border-radius: 0; background: rgba(255,255,255,.95); border-color: rgba(15,23,42,.08); box-shadow: 0 18px 60px rgba(15,23,42,.10); }

        .hero-slide { opacity: 0; transform: scale(1.05); transition: opacity .8s ease, transform 1.2s ease; }
        .hero-slide.is-active { opacity: 1; transform: scale(1); }
        .hero-overlay { background: linear-gradient(90deg, rgba(4,7,18,.82), rgba(4,7,18,.38) 50%, rgba(4,7,18,.22)); }

        .mood-card { box-shadow: 0 28px 90px rgba(15,23,42,.12); }
        .module-section { min-height: 360vh; }
        .module-sticky { position: sticky; top: 0; min-height: 100vh; overflow: hidden; display: flex; align-items: center; }
        .module-track { display: flex; gap: 1.5rem; transform: translateX(var(--module-x, 0)); transition: transform .08s linear; }
        .module-card { width: min(76vw, 440px); min-height: 430px; flex: 0 0 auto; }
        .arch-line { background: linear-gradient(90deg, transparent, rgba(239,68,68,.55), transparent); }
        .marquee { white-space: nowrap; animation: marquee 18s linear infinite; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

        @media (max-width: 768px) {
            .module-section { min-height: auto; }
            .module-sticky { position: relative; min-height: auto; padding: 5rem 1rem; overflow-x: auto; }
            .module-track { transform: none !important; }
            .hero-overlay { background: linear-gradient(180deg, rgba(4,7,18,.35), rgba(4,7,18,.88)); }
        }
    </style>
</head>
<body x-data="ajaranBaruLanding()" x-init="init()" class="overflow-x-hidden">
    <div id="ajaranLoader" class="new-loader">
        <div class="text-center">
            <div class="loader-core mx-auto">
                <span style="--i:0;--d:7s"></span>
                <span style="--i:18px;--d:5s"></span>
                <span style="--i:36px;--d:3.8s"></span>
                <div class="loader-mark">TS</div>
            </div>
            <p class="mt-8 text-xs font-black uppercase tracking-[.35em] text-white/45">Preparing New Semester</p>
        </div>
    </div>

    <nav class="ab-nav fixed left-0 right-0 top-0 z-50 px-4 py-4" :class="{ 'is-scrolled': navScrolled }">
        <div class="ab-nav-inner mx-auto flex max-w-7xl items-center justify-between rounded-2xl border border-white/20 bg-white/12 px-4 py-3 text-white backdrop-blur-2xl md:px-6" :class="{ '!text-slate-900': navScrolled }">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-white p-1 shadow-sm">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="{{ $schoolName }}" class="h-full w-full object-contain">
                    @else
                        <span class="font-outfit text-lg font-black text-red-600">TS</span>
                    @endif
                </span>
                <span class="font-outfit text-sm font-black md:text-base">{{ $schoolName }}</span>
            </a>
            <div class="hidden items-center gap-7 text-xs font-black uppercase tracking-widest md:flex">
                <a href="#mood" class="hover:text-red-500">Mood</a>
                <a href="#modules" class="hover:text-red-500">Modules</a>
                <a href="#architecture" class="hover:text-red-500">Architecture</a>
                <a href="#news" class="hover:text-red-500">Berita</a>
            </div>
            @auth
                <a href="{{ url('/dashboard') }}" class="rounded-xl bg-red-600 px-4 py-2 text-xs font-black text-white shadow-lg shadow-red-600/20">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-xl bg-red-600 px-4 py-2 text-xs font-black text-white shadow-lg shadow-red-600/20">Login</a>
            @endauth
        </div>
    </nav>

    <section id="hero" class="relative min-h-screen overflow-hidden">
        @foreach($heroImages as $index => $image)
            @php($copy = $heroSlides[$index % count($heroSlides)])
            <div class="hero-slide absolute inset-0 {{ $index === 0 ? 'is-active' : '' }}" :class="{ 'is-active': heroIndex === {{ $index }} }">
                <img src="{{ $image }}" alt="{{ $copy['title'] }}" class="h-full w-full object-cover">
                <div class="hero-overlay absolute inset-0"></div>
            </div>
        @endforeach
        <div class="relative z-10 flex min-h-screen items-end px-5 pb-20 pt-32 md:items-center md:px-10 md:pb-0">
            <div class="mx-auto w-full max-w-7xl">
                <div class="max-w-3xl text-white">
                    <p class="mb-5 inline-flex rounded-full border border-white/20 bg-white/12 px-4 py-2 text-xs font-black uppercase tracking-[.25em] backdrop-blur-xl">Tahun Ajaran Baru</p>
                    <template x-for="(slide, index) in heroCopies" :key="slide.title">
                        <div x-show="heroIndex === index" x-transition.opacity.duration.500ms>
                            <h1 class="font-outfit text-5xl font-black leading-[.96] md:text-8xl" x-text="slide.title"></h1>
                            <p class="mt-6 max-w-2xl text-base font-semibold leading-8 text-white/78 md:text-xl" x-text="slide.caption"></p>
                        </div>
                    </template>
                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="#mood" class="rounded-2xl bg-red-600 px-6 py-3 text-sm font-black text-white shadow-2xl shadow-red-700/30">Mulai Hari Ini</a>
                        <a href="#modules" class="rounded-2xl border border-white/20 bg-white/10 px-6 py-3 text-sm font-black text-white backdrop-blur-xl">Lihat Ekosistem</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-8 right-6 z-20 flex gap-2 md:right-10">
            <button @click="prevHero()" class="grid h-12 w-12 place-items-center rounded-full border border-white/20 bg-white/12 text-white backdrop-blur-xl">‹</button>
            <button @click="nextHero()" class="grid h-12 w-12 place-items-center rounded-full border border-white/20 bg-white/12 text-white backdrop-blur-xl">›</button>
        </div>
    </section>

    <section id="mood" class="relative z-20 -mt-12 px-5 pb-20">
        <div x-data="happinessMeter()" x-init="init()" class="mood-card mx-auto max-w-6xl rounded-[28px] border border-slate-100 bg-white p-6 md:p-8">
            <div class="flex flex-col gap-7 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.25em] text-red-600">Mood Check Hari Ini</p>
                    <h2 class="mt-2 font-outfit text-2xl font-black text-slate-950 md:text-3xl">Bagaimana perasaanmu memulai hari ini?</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Pilih suasana hati untuk membantu sekolah membaca iklim belajar harian.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="mood in moods" :key="mood.level">
                        <button @click="selectAndSubmit(mood)" :disabled="alreadySubmitted || isSubmitting" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-black text-slate-700 transition hover:border-red-200 hover:bg-red-50 disabled:opacity-50">
                            <span x-text="mood.label"></span>
                        </button>
                    </template>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-4 border-t border-slate-100 pt-5 text-sm font-bold text-slate-500">
                <span x-show="alreadySubmitted" class="text-emerald-600" x-text="submittedMessage || 'Terima kasih sudah berbagi.'"></span>
                <span>Total respon hari ini: <strong class="text-slate-950" x-text="stats.total_today || 0"></strong></span>
            </div>
        </div>
    </section>

    <section id="modules" class="module-section bg-[#080b12] text-white">
        <div class="module-sticky">
            <div class="w-full px-5 md:px-10">
                <div class="mx-auto mb-10 max-w-7xl">
                    <p class="text-xs font-black uppercase tracking-[.28em] text-red-400">Ecosystem Modules</p>
                    <h2 class="mt-3 font-outfit text-4xl font-black md:text-6xl">Scroll untuk menjelajah modul.</h2>
                </div>
                <div class="module-track" :style="`--module-x:${moduleX}px`">
                    @foreach($modules as $module)
                        <article class="module-card rounded-[32px] border border-white/10 bg-white/[.06] p-8 backdrop-blur-xl">
                            <div class="mb-16 h-3 w-24 rounded-full" style="background: {{ $module['color'] }}"></div>
                            <h3 class="font-outfit text-4xl font-black">{{ $module['name'] }}</h3>
                            <p class="mt-5 text-lg font-semibold leading-8 text-white/62">{{ $module['text'] }}</p>
                            <div class="mt-12 grid h-36 place-items-center rounded-3xl border border-white/10 bg-black/20">
                                <div class="h-16 w-16 rounded-3xl" style="background: {{ $module['color'] }}; box-shadow: 0 24px 70px {{ $module['color'] }}66"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="architecture" class="bg-white px-5 py-24">
        <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[.9fr_1.1fr] lg:items-center">
            <div>
                <p class="text-xs font-black uppercase tracking-[.28em] text-red-600">Connected Architecture</p>
                <h2 class="mt-4 font-outfit text-4xl font-black leading-tight text-slate-950 md:text-6xl">Satu data menggerakkan seluruh layanan sekolah.</h2>
                <p class="mt-6 text-lg font-semibold leading-8 text-slate-500">Setiap modul saling terhubung: data siswa, guru, kelas, absensi, prakerin, laporan, dan dokumen digital bergerak dalam arsitektur yang konsisten.</p>
            </div>
            <div class="rounded-[34px] border border-slate-100 bg-slate-50 p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach(['Data Master', 'Layanan Harian', 'Analitik'] as $node)
                        <div class="rounded-3xl bg-white p-6 text-center shadow-sm">
                            <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-red-50"></div>
                            <h3 class="font-outfit text-lg font-black">{{ $node }}</h3>
                        </div>
                    @endforeach
                </div>
                <div class="arch-line my-8 h-px"></div>
                <div class="rounded-3xl bg-slate-950 p-8 text-white">
                    <p class="text-xs font-black uppercase tracking-[.25em] text-red-300">Digital Core</p>
                    <p class="mt-3 text-2xl font-black">Authentication, role access, audit, QR verification, and reporting.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="news" class="bg-slate-50 px-5 py-24" x-data="beritaLanding()" x-init="init()">
        <div class="mx-auto max-w-7xl">
            <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.28em] text-red-600">Berita Terkini</p>
                    <h2 class="mt-3 font-outfit text-4xl font-black text-slate-950 md:text-5xl">Kabar terbaru sekolah.</h2>
                </div>
                <a href="#news" class="w-fit rounded-2xl bg-slate-950 px-5 py-3 text-sm font-black text-white">Lihat Semua</a>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                <template x-for="item in beritas" :key="item.id">
                    <a :href="item.url" class="overflow-hidden rounded-[28px] border border-slate-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="aspect-[16/10] bg-slate-200">
                            <img x-show="item.gambar_url" :src="item.gambar_url" :alt="item.judul" class="h-full w-full object-cover">
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-black uppercase tracking-widest text-red-600" x-text="item.kategori"></p>
                            <h3 class="mt-3 line-clamp-2 font-outfit text-xl font-black text-slate-950" x-text="item.judul"></h3>
                            <p class="mt-3 line-clamp-2 text-sm font-semibold leading-6 text-slate-500" x-text="item.ringkasan || ''"></p>
                        </div>
                    </a>
                </template>
            </div>
            <div x-show="loaded && beritas.length === 0" class="rounded-[28px] border border-slate-100 bg-white p-10 text-center font-bold text-slate-500">Belum ada berita terbaru.</div>
        </div>
    </section>

    <section class="bg-white px-5 py-24">
        <div class="mx-auto max-w-6xl overflow-hidden rounded-[40px] bg-slate-950 p-8 text-white md:p-14">
            <div class="grid gap-10 md:grid-cols-[1.2fr_.8fr] md:items-center">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.28em] text-red-300">Digitalisasikan</p>
                    <h2 class="mt-4 font-outfit text-4xl font-black leading-tight md:text-6xl">Sekolah Anda Sekarang</h2>
                    <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-white/62">Tampilan baru, alur lebih jelas, dan sistem yang siap mendampingi tahun ajaran baru dari hari pertama.</p>
                </div>
                <div class="flex flex-col gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-2xl bg-red-600 px-6 py-4 text-center font-black text-white">Masuk Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-2xl bg-red-600 px-6 py-4 text-center font-black text-white">Masuk Sistem</a>
                        @if($appSetting?->allow_registration)
                            <a href="{{ route('register') }}" class="rounded-2xl border border-white/15 px-6 py-4 text-center font-black text-white">Daftar Akun</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <footer class="border-t border-slate-200 bg-white px-5 py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="font-outfit text-xl font-black text-slate-950">{{ $schoolName }}</p>
                <p class="mt-1 text-sm font-semibold text-slate-500">{{ $appSetting?->address ?? 'Ekosistem digital sekolah terpadu.' }}</p>
            </div>
            <div class="flex gap-5 text-sm font-black text-slate-500">
                <a href="{{ route('privacy') }}">Privasi</a>
                <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </footer>
    <div class="overflow-hidden bg-slate-950 py-4 text-white">
        <div class="marquee font-outfit text-2xl font-black uppercase tracking-[.2em] text-white/20">
            <span>The Real Informatic Scools - The Real Informatic Scools - The Real Informatic Scools - </span>
            <span>The Real Informatic Scools - The Real Informatic Scools - The Real Informatic Scools - </span>
        </div>
    </div>

    <script>
        function ajaranBaruLanding() {
            return {
                navScrolled: false,
                heroIndex: 0,
                heroCount: {{ count($heroImages) }},
                heroCopies: @js($heroSlides),
                moduleX: 0,
                timer: null,
                init() {
                    window.addEventListener('scroll', () => {
                        this.navScrolled = window.scrollY > 40;
                        this.updateModuleScroll();
                    }, { passive: true });
                    this.timer = setInterval(() => this.nextHero(), 5200);
                    setTimeout(() => document.getElementById('ajaranLoader')?.classList.add('hide'), 1100);
                    setTimeout(() => document.getElementById('ajaranLoader')?.remove(), 1900);
                    this.updateModuleScroll();
                },
                nextHero() { this.heroIndex = (this.heroIndex + 1) % this.heroCount; },
                prevHero() { this.heroIndex = (this.heroIndex - 1 + this.heroCount) % this.heroCount; },
                updateModuleScroll() {
                    const section = document.getElementById('modules');
                    if (!section || window.innerWidth < 768) return;
                    const rect = section.getBoundingClientRect();
                    const maxScroll = section.offsetHeight - window.innerHeight;
                    const progress = Math.min(Math.max(-rect.top / maxScroll, 0), 1);
                    const track = section.querySelector('.module-track');
                    const overflow = Math.max((track?.scrollWidth || 0) - window.innerWidth + 80, 0);
                    this.moduleX = -overflow * progress;
                },
            };
        }

        function happinessMeter() {
            return {
                moods: [
                    { level: 'sangat_bahagia', score: 5, label: 'Sangat Bahagia' },
                    { level: 'bahagia', score: 4, label: 'Bahagia' },
                    { level: 'netral', score: 3, label: 'Netral' },
                    { level: 'sedih', score: 2, label: 'Sedih' },
                    { level: 'sangat_sedih', score: 1, label: 'Sangat Sedih' },
                ],
                selectedMood: null,
                alreadySubmitted: false,
                submittedMessage: '',
                isSubmitting: false,
                stats: { total_today: 0, mood_distribution: {} },
                fingerprint: '',
                async init() {
                    this.fingerprint = await this.generateFingerprint();
                    await this.checkStatus();
                    await this.loadStats();
                },
                async generateFingerprint() {
                    const text = [navigator.userAgent, navigator.language, screen.width + 'x' + screen.height, screen.colorDepth, new Date().getTimezoneOffset(), navigator.platform].join('|');
                    let hash = 0;
                    for (let i = 0; i < text.length; i++) hash = ((hash << 5) - hash) + text.charCodeAt(i) | 0;
                    return Math.abs(hash).toString(36);
                },
                async checkStatus() {
                    try {
                        const response = await fetch('/api/happiness/check', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' },
                            body: JSON.stringify({ fingerprint: this.fingerprint }),
                        });
                        const data = await response.json();
                        this.alreadySubmitted = data.already_submitted;
                    } catch (e) {}
                },
                async loadStats() {
                    try {
                        const response = await fetch('/api/happiness/stats');
                        this.stats = await response.json();
                    } catch (e) {}
                },
                async selectAndSubmit(mood) {
                    if (this.alreadySubmitted || this.isSubmitting) return;
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('/api/happiness/store', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}' },
                            body: JSON.stringify({ fingerprint: this.fingerprint, mood_level: mood.level, mood_score: mood.score }),
                        });
                        const data = await response.json();
                        this.alreadySubmitted = true;
                        this.submittedMessage = data.message || 'Terima kasih sudah berbagi.';
                        await this.loadStats();
                    } catch (e) {
                        this.submittedMessage = 'Gagal menyimpan mood.';
                    } finally {
                        this.isSubmitting = false;
                    }
                },
            };
        }

        function beritaLanding() {
            return {
                beritas: [],
                loaded: false,
                async init() {
                    try {
                        const response = await fetch('{{ route('api.berita.latest') }}');
                        this.beritas = await response.json();
                    } catch (e) {
                        this.beritas = [];
                    } finally {
                        this.loaded = true;
                    }
                },
            };
        }
    </script>
</body>
</html>
