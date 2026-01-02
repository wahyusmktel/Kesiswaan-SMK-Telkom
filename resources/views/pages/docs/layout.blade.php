<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dokumentasi') — SISFO SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
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

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #FFF 0%, #AAA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-red {
            background: linear-gradient(135deg, var(--telkom-red-light) 0%, var(--telkom-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--telkom-red);
            filter: blur(120px);
            opacity: 0.1;
            z-index: -1;
            border-radius: 50%;
        }

        .step-card {
            @apply relative pl-8 border-l-2 border-white/10 pb-12 last:pb-0;
        }

        .step-number {
            @apply absolute -left-[11px] top-0 w-5 h-5 bg-red-600 rounded-full flex items-center justify-center text-[10px] font-black text-white ring-4 ring-slate-900;
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">
    <div class="blob top-[-100px] left-[-100px]"></div>
    <div class="blob bottom-[10%] right-[-100px]" style="background: #3B82F6; opacity: 0.05;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between glass py-3 px-6 rounded-2xl">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-lg">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                    @endif
                </div>
                <div class="flex flex-col leading-tight hidden sm:block">
                    <span class="font-outfit font-black text-xl tracking-tighter">PANDUAN <span class="text-red-500">SISFO</span></span>
                </div>
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-white transition-colors uppercase tracking-widest">Login Sistem</a>
            </div>
        </div>
    </nav>

    <main class="pt-32 pb-20 px-6">
        <div class="max-w-5xl mx-auto">
            @yield('content')
        </div>
    </main>

    <footer class="py-12 border-t border-white/5 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-xs text-slate-500 font-medium tracking-tight">© {{ date('Y') }} Sistem Informasi SMK Telkom Lampung. <br> Built with passion for better education.</p>
        </div>
    </footer>
</body>
</html>
