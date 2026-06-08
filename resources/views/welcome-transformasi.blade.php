@php
    $sliderImages = collect($appSetting?->transformasi_slider_images ?? [])
        ->map(fn ($path) => Storage::url($path))
        ->values()
        ->all();

    if (count($sliderImages) === 0) {
        $sliderImages = [
            'https://images.unsplash.com/photo-1581092160562-40aa08e78837?auto=format&fit=crop&q=85&w=1600',
            'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&q=85&w=1600',
            'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=85&w=1600',
            'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=85&w=1600',
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SISFO - Transformasi SMK Telkom Lampung</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif

    <style>
        :root {
            --tf-red: #e60012;
            --tf-dark: #0b0d12;
            --tf-ink: #111827;
            --tf-muted: #64748b;
        }

        * {
            letter-spacing: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--tf-ink);
            background:
                radial-gradient(circle at 14% 6%, rgba(230, 0, 18, .14), transparent 28rem),
                radial-gradient(circle at 88% 22%, rgba(14, 165, 233, .10), transparent 26rem),
                linear-gradient(180deg, #ffffff 0%, #f7f8fb 48%, #ffffff 100%);
            overflow-x: hidden;
        }

        h1, h2, h3, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .tf-nav {
            background: rgba(255, 255, 255, .88);
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 22px 70px rgba(15, 23, 42, .08);
            backdrop-filter: blur(22px);
        }

        .tf-pill {
            border: 1px solid rgba(15, 23, 42, .08);
            background: rgba(255,255,255,.76);
            box-shadow: 0 16px 42px rgba(15,23,42,.08);
        }

        .tf-red-btn {
            color: #fff;
            background: var(--tf-red);
            box-shadow: 0 22px 44px -20px rgba(230, 0, 18, .8);
        }

        .tf-red-btn:hover {
            background: #c90010;
            transform: translateY(-2px);
        }

        .tf-showcase {
            min-height: 420vh;
        }

        .tf-sticky {
            position: sticky;
            top: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .tf-stage {
            width: min(92vw, 1240px);
            height: min(72vh, 720px);
            border-radius: 44px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 50px 140px rgba(15, 23, 42, .22);
            border: 1px solid rgba(255,255,255,.65);
            transform: scale(var(--stage-scale, 1));
            transition: border-radius .2s linear;
            background: #111827;
        }

        .tf-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transform: scale(1.08);
            transition: opacity .55s ease, transform .55s ease;
        }

        .tf-slide.is-active {
            opacity: 1;
            transform: scale(var(--image-scale, 1));
        }

        .tf-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tf-stage::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(0,0,0,.72), rgba(0,0,0,.18) 48%, rgba(0,0,0,.08));
            pointer-events: none;
        }

        .tf-slide-copy {
            position: absolute;
            z-index: 4;
            left: clamp(1.5rem, 5vw, 4.5rem);
            bottom: clamp(1.5rem, 6vw, 5rem);
            width: min(34rem, calc(100% - 3rem));
            color: #fff;
            transform: translateY(var(--copy-y, 0));
        }

        .tf-indicator {
            position: absolute;
            z-index: 5;
            right: clamp(1.25rem, 4vw, 3.5rem);
            bottom: clamp(1.25rem, 4vw, 3.5rem);
            display: flex;
            gap: .55rem;
        }

        .tf-dot {
            width: .7rem;
            height: .7rem;
            border-radius: 999px;
            background: rgba(255,255,255,.45);
            transition: width .3s ease, background .3s ease;
        }

        .tf-dot.is-active {
            width: 2.2rem;
            background: #fff;
        }

        .tf-card {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, .08);
            box-shadow: 0 24px 80px rgba(15, 23, 42, .08);
        }

        .tf-dark-band {
            background:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px),
                #0b0d12;
            background-size: 42px 42px;
        }

        @media (max-width: 768px) {
            .tf-showcase {
                min-height: auto;
            }

            .tf-sticky {
                position: relative;
                min-height: auto;
                padding: 6rem 1rem 2rem;
            }

            .tf-stage {
                width: 100%;
                height: 70vh;
                border-radius: 28px;
            }

            .tf-stage::after {
                background: linear-gradient(180deg, rgba(0,0,0,.25), rgba(0,0,0,.78));
            }

            .tf-slide-copy {
                left: 1.25rem;
                right: 1.25rem;
                bottom: 1.5rem;
            }
        }
    </style>
</head>

<body>
    @include('components.preloader')

    <nav class="fixed left-0 right-0 top-0 z-50 px-4 py-4">
        <div class="tf-nav mx-auto flex max-w-7xl items-center justify-between rounded-full px-4 py-3 md:px-6">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-white p-1 shadow-sm">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="h-full w-full object-contain">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="h-full w-full object-contain">
                    @endif
                </div>
                <div class="hidden leading-tight sm:block">
                    <div class="font-outfit text-lg font-black">SISFO TS</div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Transformasi Digital</div>
                </div>
            </a>

            <div class="hidden items-center gap-7 text-sm font-bold text-slate-600 md:flex">
                <a href="#showcase" class="hover:text-red-600">Showcase</a>
                <a href="#industry" class="hover:text-red-600">Ekosistem</a>
                <a href="{{ route('gallery-photo.index') }}" class="hover:text-red-600">Galeri</a>
                <a href="{{ route('forum-stella.index') }}" class="hover:text-red-600">Forum</a>
            </div>

            @auth
                <a href="{{ url('/dashboard') }}" class="tf-red-btn rounded-full px-5 py-2.5 text-sm font-black transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="tf-red-btn rounded-full px-5 py-2.5 text-sm font-black transition">Masuk</a>
            @endauth
        </div>
    </nav>

    <header class="relative overflow-hidden px-6 pb-20 pt-36">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-end gap-10 lg:grid-cols-[1fr_.72fr]">
            <div>
                <div class="tf-pill mb-6 inline-flex rounded-full px-4 py-2 text-xs font-black uppercase tracking-widest text-red-600">
                    Sekolah industri masa kini
                </div>
                <h1 class="font-outfit max-w-5xl text-5xl font-black leading-[.95] text-slate-950 md:text-7xl lg:text-8xl">
                    Transformasi layanan sekolah dalam satu pengalaman digital.
                </h1>
            </div>
            <div class="tf-card rounded-[2rem] p-6">
                <p class="text-sm font-bold leading-7 text-slate-500">
                    SISFO TS menghubungkan akademik, kesiswaan, kepegawaian, galeri, forum, dan monitoring kehadiran dalam platform yang terasa modern, cepat, dan siap dipakai setiap hari.
                </p>
                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="text-2xl font-black text-slate-950">24/7</div>
                        <div class="text-[10px] font-black uppercase text-slate-400">Online</div>
                    </div>
                    <div class="rounded-2xl bg-red-50 p-4">
                        <div class="text-2xl font-black text-red-600">360</div>
                        <div class="text-[10px] font-black uppercase text-red-400">Layanan</div>
                    </div>
                    <div class="rounded-2xl bg-slate-950 p-4">
                        <div class="text-2xl font-black text-white">AI</div>
                        <div class="text-[10px] font-black uppercase text-slate-400">Ready</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="showcase" class="tf-showcase relative" data-transformasi-showcase>
        <div class="tf-sticky">
            <div class="tf-stage" data-stage>
                @foreach($sliderImages as $index => $image)
                    <div class="tf-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide>
                        <img src="{{ $image }}" alt="Transformasi slide {{ $index + 1 }}">
                    </div>
                @endforeach

                <div class="tf-slide-copy" data-copy>
                    <div class="mb-4 inline-flex rounded-full bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-widest backdrop-blur">
                        Scroll to transform
                    </div>
                    <h2 class="font-outfit text-4xl font-black leading-tight md:text-6xl">Showcase sekolah yang bergerak bersama datanya.</h2>
                    <p class="mt-5 max-w-xl text-sm font-semibold leading-7 text-white/78 md:text-base">
                        Gambar mengecil, fokus berpindah, dan cerita transformasi muncul bertahap saat halaman discroll.
                    </p>
                </div>

                <div class="tf-indicator">
                    @foreach($sliderImages as $index => $image)
                        <span class="tf-dot {{ $index === 0 ? 'is-active' : '' }}" data-dot></span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="industry" class="px-6 py-24">
        <div class="mx-auto max-w-7xl">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[.8fr_1.2fr]">
                <div>
                    <span class="text-xs font-black uppercase tracking-widest text-red-600">Ekosistem Operasional</span>
                    <h2 class="font-outfit mt-4 text-4xl font-black leading-tight text-slate-950 md:text-6xl">Dibangun untuk ritme sekolah industri.</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach([
                        ['title' => 'Fingerprint & Kehadiran', 'desc' => 'Monitoring jam masuk, pulang, apresiasi, dan evaluasi diri.'],
                        ['title' => 'Akademik Terhubung', 'desc' => 'Jadwal, guru, kelas, dan data Dapodik berada dalam alur yang sama.'],
                        ['title' => 'Forum & Galeri', 'desc' => 'Ruang sosial sekolah dengan album, komentar, dan interaksi.'],
                        ['title' => 'Layanan Cepat', 'desc' => 'Perizinan, aduan, tanda tangan digital, dan dokumen lebih ringkas.'],
                    ] as $item)
                        <div class="tf-card rounded-[1.75rem] p-6">
                            <div class="mb-8 h-2 w-12 rounded-full bg-red-600"></div>
                            <h3 class="font-outfit text-2xl font-black text-slate-950">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm font-semibold leading-7 text-slate-500">{{ $item['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="tf-dark-band px-6 py-24 text-white">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 lg:grid-cols-[1fr_auto]">
            <div>
                <p class="text-xs font-black uppercase tracking-widest text-red-400">Siap digunakan</p>
                <h2 class="font-outfit mt-4 text-4xl font-black md:text-6xl">Masuk ke pusat kendali sekolah.</h2>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('login') }}" class="rounded-full bg-white px-8 py-4 text-center text-sm font-black text-slate-950">Masuk Sistem</a>
                <a href="{{ route('gallery-photo.index') }}" class="rounded-full border border-white/15 px-8 py-4 text-center text-sm font-black text-white">Lihat Galeri</a>
            </div>
        </div>
    </section>

    <footer class="px-6 py-10">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 border-t border-slate-200 pt-8 text-sm font-semibold text-slate-500 md:flex-row md:items-center md:justify-between">
            <span>{{ $appSetting?->school_name ?? 'SMK Telkom Lampung' }}</span>
            <span>SISFO Transformasi - {{ now()->year }}</span>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const showcase = document.querySelector('[data-transformasi-showcase]');
            const stage = document.querySelector('[data-stage]');
            const slides = [...document.querySelectorAll('[data-slide]')];
            const dots = [...document.querySelectorAll('[data-dot]')];
            const copy = document.querySelector('[data-copy]');

            if (!showcase || !stage || !slides.length) return;

            const render = () => {
                const rect = showcase.getBoundingClientRect();
                const scrollable = Math.max(1, rect.height - window.innerHeight);
                const progress = Math.min(1, Math.max(0, -rect.top / scrollable));
                const active = Math.min(slides.length - 1, Math.floor(progress * slides.length));
                const local = (progress * slides.length) - active;
                const scale = 1 - (progress * 0.22);
                const imageScale = 1.08 - (local * 0.12);
                const radius = 44 - (progress * 22);

                stage.style.setProperty('--stage-scale', scale.toFixed(3));
                stage.style.setProperty('--image-scale', imageScale.toFixed(3));
                stage.style.borderRadius = `${radius}px`;
                if (copy) copy.style.setProperty('--copy-y', `${progress * -42}px`);

                slides.forEach((slide, index) => slide.classList.toggle('is-active', index === active));
                dots.forEach((dot, index) => dot.classList.toggle('is-active', index === active));
            };

            render();
            window.addEventListener('scroll', render, { passive: true });
            window.addEventListener('resize', render);
        });
    </script>
</body>

</html>
