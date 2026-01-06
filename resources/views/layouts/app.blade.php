<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appSetting?->school_name ?? config('app.name', 'Aplikasi Izin') }} — Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/css/authentication.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm lg:hidden">
        </div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 w-72 bg-white shadow-lg border-r border-gray-200 transform transition-transform duration-300 ease-out flex flex-col lg:static lg:translate-x-0">

            <div class="h-20 px-6 flex items-center justify-between border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center p-1 shadow-sm overflow-hidden border border-gray-100">
                        @if($appSetting?->logo)
                            <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                        @else
                            <div class="w-full h-full gradient-red rounded-lg"></div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] text-red-600 uppercase tracking-widest font-extrabold leading-none mb-1">DASHBOARD</p>
                        <h1 class="text-sm font-bold text-gray-800 leading-tight truncate max-w-[120px]">{{ $appSetting?->school_name ?? config('app.name', 'Aplikasi Izin') }}</h1>
                    </div>
                </div>

                <button @click="sidebarOpen = false"
                    class="lg:hidden text-gray-500 hover:text-red-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="p-4 space-y-2 overflow-y-auto flex-1">
                @include('layouts.navigation')
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden relative">

            <header class="flex-shrink-0 bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = true"
                            class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-600 hover:text-red-600 hover:bg-red-50 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        @isset($header)
                            <div class="hidden sm:block">
                                {{ $header }}
                            </div>
                        @endisset
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Cari data..."
                                class="bg-transparent border-0 focus:ring-0 text-sm text-gray-700 placeholder-gray-500 w-40 lg:w-64 p-0">
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 text-red-700 border border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                                <span class="text-xs font-bold uppercase">Live</span>
                            </span>

                            {{-- Role Switcher --}}
                            @if(Auth::user()->roles->count() > 1)
                            <div class="relative" x-data="{ roleOpen: false }">
                                <button @click="roleOpen = !roleOpen"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-50 border border-gray-100 hover:bg-white hover:shadow-sm transition-all group focus:outline-none">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="text-left hidden lg:block">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Role Aktif</p>
                                        <p class="text-xs font-bold text-gray-700 leading-none">{{ session('active_role') }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="roleOpen" @click.outside="roleOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-2 z-[60] ring-1 ring-black ring-opacity-5"
                                    style="display: none;">
                                    <div class="px-4 py-2 border-b border-gray-50 mb-1">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih Role</p>
                                    </div>
                                    @foreach(Auth::user()->roles as $role)
                                        <form action="{{ route('role.switch') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="role" value="{{ $role->name }}">
                                            <button type="submit" 
                                                class="w-full flex items-center justify-between px-4 py-2 text-sm {{ session('active_role') == $role->name ? 'text-red-700 bg-red-50 font-bold' : 'text-gray-600 hover:bg-gray-50' }} transition-colors">
                                                <span>{{ $role->name }}</span>
                                                @if(session('active_role') == $role->name)
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>
                            @else
                                {{-- Jika hanya 1 role, tampilkan label saja --}}
                                <div class="hidden lg:flex flex-col text-right">
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Role Aktif</p>
                                    <p class="text-xs font-bold text-gray-700 leading-none">{{ session('active_role') }}</p>
                                </div>
                            @endif

                            <div class="relative" x-data="{ dropdownOpen: false }">
                                <button @click="dropdownOpen = !dropdownOpen"
                                    class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden bg-gray-200 border-2 border-white shadow-sm cursor-pointer hover:ring-2 hover:ring-red-100 transition-all focus:outline-none">
                                    <img class="w-full h-full object-cover"
                                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random&color=fff"
                                        alt="{{ Auth::user()->name }}">
                                </button>

                                <div x-show="dropdownOpen" @click.outside="dropdownOpen = false"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 ring-1 ring-black ring-opacity-5 origin-top-right"
                                    style="display: none;">

                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    </div>

                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}"
                                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profil Saya
                                        </a>
                                    </div>

                                    <div class="py-1 border-t border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                Keluar
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>

            <footer class="flex-shrink-0 bg-white border-t border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 gap-2">
                        <p>{{ $appSetting?->school_name ?? 'Aplikasi Izin' }} &copy; {{ date('Y') }}</p>
                        <p class="opacity-70">Built with passion for better education. &bull; Versi {{ config('app.version') }}</p>
                    </div>
                </div>
            </footer>

        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    @stack('scripts')
</body>

</html>
