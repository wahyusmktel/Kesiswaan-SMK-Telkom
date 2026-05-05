<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $berita->judul }} — SISFO SMK Telkom Lampung</title>
    <meta name="description" content="{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 160) }}">

    <!-- OG Tags -->
    <meta property="og:title" content="{{ $berita->judul }}">
    <meta property="og:description" content="{{ $berita->ringkasan ?? Str::limit(strip_tags($berita->konten), 160) }}">
    @if ($berita->gambar)
        <meta property="og:image" content="{{ Storage::url($berita->gambar) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif

    <style>
        :root {
            --telkom-red: #E21F26;
            --telkom-red-dark: #B91319;
            --telkom-red-light: #FF4D54;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0F172A;
            color: #F8FAFC;
        }

        h1, h2, h3, h4, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--telkom-red) 0%, var(--telkom-red-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px -5px rgba(226, 31, 38, 0.4);
        }

        .text-gradient {
            background: linear-gradient(135deg, #FFF 0%, #AAA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--telkom-red);
            filter: blur(120px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 50%;
        }

        .prose-dark {
            color: #CBD5E1;
            line-height: 1.85;
        }

        .prose-dark p {
            margin-bottom: 1.25em;
        }

        .prose-dark strong {
            color: #F1F5F9;
        }

        /* Light Theme Overrides */
        .theme-light-red {
            background-color: #F8FAFC;
            color: #0F172A;
        }
        .theme-light-red .glass {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .theme-light-red .text-gradient {
            background: linear-gradient(135deg, #0F172A 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .theme-light-red .prose-dark {
            color: #475569;
        }
        .theme-light-red .prose-dark strong {
            color: #0F172A;
        }
        .theme-light-red .btn-primary {
            color: #FFFFFF !important;
        }
    </style>
</head>

<body class="antialiased overflow-x-hidden {{ $appSetting?->theme === 'light-red' ? 'theme-light-red' : '' }}">
    {{-- Premium Tech Preloader --}}
    @include('components.preloader')

    <!-- Blobs -->
    <div class="blob top-[-100px] left-[-100px]"></div>
    <div class="blob bottom-[10%] right-[-100px]" style="background: #3B82F6; opacity: 0.1;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between glass py-3 px-6 rounded-2xl">
            <div class="flex items-center gap-3">
                <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-lg">
                        @if($appSetting?->logo)
                            <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                        @else
                            <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo"
                                class="object-contain w-full h-full">
                        @endif
                    </div>
                    <div class="flex flex-col leading-tight hidden sm:block">
                        <span class="font-outfit font-black text-xl tracking-tighter">SISFO <span class="text-red-500">TS</span></span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('welcome') }}" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">← Beranda</a>
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}"
                        class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Masuk</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Article Content -->
    <article class="pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            {{-- Category & Date --}}
            <div class="flex items-center gap-3 mb-6">
                @php
                    $catColors = [
                        'Akademik' => 'bg-blue-600/10 border-blue-600/20 text-blue-400',
                        'Kesiswaan' => 'bg-red-600/10 border-red-600/20 text-red-400',
                        'Kegiatan' => 'bg-emerald-600/10 border-emerald-600/20 text-emerald-400',
                        'Prestasi' => 'bg-amber-600/10 border-amber-600/20 text-amber-400',
                        'Pengumuman' => 'bg-purple-600/10 border-purple-600/20 text-purple-400',
                        'Lainnya' => 'bg-slate-600/10 border-slate-600/20 text-slate-400',
                    ];
                @endphp
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-black uppercase tracking-widest {{ $catColors[$berita->kategori] ?? $catColors['Lainnya'] }}">
                    {{ $berita->kategori }}
                </span>
                <span class="text-sm text-slate-500 font-medium">{{ $berita->published_at->translatedFormat('d F Y') }}</span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl md:text-5xl font-extrabold text-gradient leading-tight mb-6">
                {{ $berita->judul }}
            </h1>

            {{-- Author --}}
            <div class="flex items-center gap-4 mb-10 pb-8 border-b border-white/5">
                <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-800 border-2 border-white/10">
                    <img class="w-full h-full object-cover"
                        src="{{ $berita->author?->avatar ? Storage::url($berita->author->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($berita->author?->name ?? 'Admin') . '&background=1e293b&color=94a3b8' }}"
                        alt="{{ $berita->author?->name }}">
                </div>
                <div>
                    <p class="text-sm font-bold text-white">{{ $berita->author?->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-500 font-medium">{{ $berita->published_at->diffForHumans() }} · {{ ceil(str_word_count(strip_tags($berita->konten)) / 200) }} min read</p>
                </div>
            </div>

            {{-- Featured Image --}}
            @if ($berita->gambar)
                <div class="glass rounded-[32px] overflow-hidden mb-12 border-white/10">
                    <img src="{{ Storage::url($berita->gambar) }}" alt="{{ $berita->judul }}"
                        class="w-full aspect-video object-cover">
                </div>
            @endif

            {{-- Ringkasan --}}
            @if ($berita->ringkasan)
                <div class="glass p-6 rounded-2xl mb-10 border-l-4 border-red-500">
                    <p class="text-lg font-medium text-slate-300 italic leading-relaxed">{{ $berita->ringkasan }}</p>
                </div>
            @endif

            {{-- Content --}}
            <div class="prose-dark text-base md:text-lg whitespace-pre-line">
                {!! nl2br(e($berita->konten)) !!}
            </div>

            {{-- Share --}}
            <div class="mt-12 pt-8 border-t border-white/5">
                <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">Bagikan Berita</p>
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/?text={{ urlencode($berita->judul . ' - ' . route('berita.show', $berita->slug)) }}"
                        target="_blank"
                        class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-emerald-500/20 transition-all group">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($berita->judul) }}&url={{ urlencode(route('berita.show', $berita->slug)) }}"
                        target="_blank"
                        class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-blue-500/20 transition-all group">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href).then(() => alert('Link berhasil disalin!'))"
                        class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:bg-white/10 transition-all group">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </article>

    {{-- Related News --}}
    @if ($relatedNews->count() > 0)
        <section class="pb-20 px-6">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-2xl font-extrabold text-gradient mb-8">Berita Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($relatedNews as $related)
                        <a href="{{ route('berita.show', $related->slug) }}"
                            class="glass rounded-[24px] overflow-hidden group hover:border-red-500/30 transition-all hover:-translate-y-1">
                            <div class="aspect-video overflow-hidden">
                                @if ($related->gambar)
                                    <img src="{{ Storage::url($related->gambar) }}" alt="{{ $related->judul }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 space-y-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-red-500">{{ $related->kategori }}</span>
                                <h3 class="text-base font-bold text-white group-hover:text-red-400 transition-colors line-clamp-2">{{ $related->judul }}</h3>
                                <p class="text-xs text-slate-500 font-medium">{{ $related->published_at->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="space-y-3 text-center md:text-left">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo"
                            class="w-8 h-8 border-r border-slate-700 pr-3 object-contain">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo"
                            class="w-8 h-8 border-r border-slate-700 pr-3 object-contain">
                    @endif
                    <div class="flex flex-col leading-tight">
                        <span class="font-outfit font-black text-lg">SISFO <span class="text-red-500">TS</span></span>
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Powered by SMK Telkom Lampung</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 font-medium tracking-tight">© {{ date('Y') }} Sistem Informasi SMK Telkom Lampung.</p>
            </div>
        </div>
    </footer>
</body>

</html>
