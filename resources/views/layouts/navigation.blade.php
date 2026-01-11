<div x-data="globalNotificationSystem()" x-init="init()">
<ul class="space-y-1 font-medium">

    {{-- ============================================================ --}}
    {{-- ROLE: ADMIN / KEPALA SEKOLAH (Tanpa Dashboard Spesifik)      --}}
    {{-- ============================================================ --}}
    @role('Super Admin|Kepala Sekolah')
        @if(session('active_role') == 'Super Admin' || session('active_role') == 'Kepala Sekolah')
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="text-sm">Dashboard Admin</span>
                </a>
            </li>
            @can('view users')
            <li>
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm">Manajemen Pengguna</span>
                </a>
            </li>
            @endcan
            @can('manage settings')
            <li>
                <a href="{{ route('super-admin.settings') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('super-admin.settings') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm">Konfigurasi Aplikasi</span>
                </a>
            </li>
            @endcan
            @can('manage permissions')
            <li>
                <a href="{{ route('super-admin.permissions.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('super-admin.permissions.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <span class="text-sm italic">Manajemen Hak Akses</span>
                </a>
            </li>
            @endcan
        @endif
    @endrole

    {{-- ============================================================ --}}
    {{-- ROLE: SISWA                                                  --}}
    {{-- ============================================================ --}}
    @role('Siswa')
    @if(session('active_role') == 'Siswa')
        <li>
            <a href="{{ route('siswa.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg>
                <span class="text-sm">Dashboard Siswa</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.lms.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.lms.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Ruang Belajar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('izin.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('izin.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Izin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.izin-keluar-kelas.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.izin-keluar-kelas.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="text-sm">Izin Keluar Kelas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.riwayat-catatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.riwayat-catatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-sm">Riwayat Catatan</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.riwayat-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.riwayat-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Keterlambatan</span>
            </a>
        </li>

        @if (Auth::user()->masterSiswa?->penempatan()->where('status', 'aktif')->exists())
            <li>
                <a href="{{ route('siswa.jurnal-prakerin.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.jurnal-prakerin.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="text-sm">Jurnal Prakerin</span>
                </a>
            </li>
        @endif

        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Layanan BK</div>
        <li>
            <a href="{{ route('siswa.bk.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.bk.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Konsultasi BK</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.chat.index') }}"
                class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.chat.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm">Chat BK</span>
                </div>
                <template x-if="unreadChatCount > 0">
                    <span class="bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full" x-text="unreadChatCount"></span>
                </template>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.kartu-pelajar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.kartu-pelajar.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                <span class="text-sm">Kartu Pelajar Digital</span>
                <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
            </a>
        </li>
        <li>
            <a href="{{ route('siswa.dapodik.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('siswa.dapodik.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm">Data Dapodik</span>
            </a>
        </li>
    @endif
    @endrole

    {{-- ============================================================ --}}
    {{-- ROLE: WALI KELAS                                             --}}
    {{-- ============================================================ --}}
    @can('view wali kelas dashboard')
    @role('Wali Kelas')
    @if(session('active_role') == 'Wali Kelas')
        <li>
            <a href="{{ route('wali-kelas.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('wali-kelas.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-sm">Dashboard Wali</span>
            </a>
        </li>
        @can('manage perizinan wali kelas')
        <li>
            <a href="{{ route('wali-kelas.perizinan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('wali-kelas.perizinan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Izin</span>
            </a>
        </li>
        @endcan
        @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('monitoring-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Monitoring Terlambat</span>
            </a>
        </li>
        @endcan
        @can('view coaching analytics')
        <li>
            <a href="{{ route('coaching-analytics.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('coaching-analytics.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Analisa Coaching</span>
            </a>
        </li>
        @endcan
    @endif
    @endrole
    @endcan


    {{-- ============================================================ --}}
    {{-- ROLE: GURU KELAS                                             --}}
    {{-- ============================================================ --}}
    @can('view guru kelas dashboard')
    @role('Guru Kelas')
    @if(session('active_role') == 'Guru Kelas')
        <li>
            <a href="{{ route('guru-kelas.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru-kelas.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <span class="text-sm">Dashboard Guru</span>
            </a>
        </li>
        <li>
            <a href="{{ route('guru.jadwal-saya') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru.jadwal-saya') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Jadwal Mengajar</span>
            </a>
        </li>
        @can('manage lms')
        <li>
            <a href="{{ route('guru.lms.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru.lms.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Ruang Belajar</span>
            </a>
        </li>
        @endcan
        @can('manage perizinan siswa')
        <li>
            <a href="{{ route('guru-kelas.persetujuan-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('guru-kelas.persetujuan-izin-keluar.*') && !request()->routeIs('guru-kelas.persetujuan-izin-keluar.riwayat')) ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Keluar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('guru-kelas.persetujuan-izin-keluar.riwayat') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru-kelas.persetujuan-izin-keluar.riwayat') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Persetujuan</span>
            </a>
        </li>
        @endcan
        @can('manage dispensasi')
        <li>
            <a href="{{ route('dispensasi.pengajuan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dispensasi.pengajuan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="text-sm">Pengajuan Dispensasi</span>
            </a>
        </li>
        @endcan
        @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('monitoring-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Monitoring Terlambat</span>
            </a>
        </li>
        @endcan
        {{-- Menu Pembimbing Prakerin --}}
        @can('monitor prakerin')
        @if (Auth::user()->masterGuru?->penempatan()->where('status', 'aktif')->exists())
            <li>
                <a href="{{ route('pembimbing-prakerin.monitoring.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('pembimbing-prakerin.monitoring.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm">Monitoring Prakerin</span>
                </a>
            </li>
        @endif
        @endcan

        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Layanan Guru</div>
        <li>
            <a href="{{ route('guru.izin.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru.izin.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm">Pengajuan Izin Guru</span>
            </a>
        </li>
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: WAKA KESISWAAN                                         --}}
    {{-- ============================================================ --}}
    @role('Waka Kesiswaan')
    @if(session('active_role') == 'Waka Kesiswaan')
        @can('view kesiswaan dashboard')
        <li>
            <a href="{{ route('kesiswaan.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Dashboard Kesiswaan</span>
            </a>
        </li>
        @endcan

        {{-- Manajemen Pengguna untuk Waka --}}
        @can('manage tahun pelajaran')
        <li>
            <a href="{{ route('master-data.tahun-pelajaran.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('master-data.tahun-pelajaran.index') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-sm">Tahun Pelajaran</span>
            </a>
        </li>
        @endcan
        @can('view users')
        <li>
            <a href="{{ route('users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-sm">Manajemen Pengguna</span>
            </a>
        </li>
        @endcan
        @can('view roles')
        <li>
            <a href="{{ route('admin.roles.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="text-sm">Manajemen Role</span>
            </a>
        </li>
        @endcan

        {{-- Dropdown Master Data --}}
        @canany(['manage kelas', 'manage siswa', 'manage rombel'])
        <li x-data="{ expanded: {{ request()->routeIs('master-data.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="text-sm font-medium">Master Data</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage kelas')
                <li><a href="{{ route('master-data.kelas.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.kelas.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Kelas</a></li>
                @endcan
                @can('manage siswa')
                <li><a href="{{ route('master-data.siswa.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.siswa.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Siswa</a></li>
                @endcan
                @can('manage rombel')
                <li><a href="{{ route('master-data.rombel.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.rombel.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                         Rombel</a></li>
                @endcan
            </ul>
        </li>
        @endcanany

        @can('monitoring izin')
        <li>
            <a href="{{ route('kesiswaan.monitoring-izin.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.monitoring-izin.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-sm">Monitoring Izin</span>
            </a>
        </li>
        @endcan

        @can('manage penanganan terlambat')
        @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('monitoring-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Monitoring Terlambat</span>
            </a>
        </li>
        @endcan
        @can('view coaching analytics')
        <li>
            <a href="{{ route('coaching-analytics.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('coaching-analytics.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Analisa Coaching</span>
                <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
            </a>
        </li>
        @endcan
        @endcan

        @can('monitoring izin')
        <li>
            <a href="{{ route('kesiswaan.riwayat-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.riwayat-izin-keluar.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">History Izin Keluar</span>
            </a>
        </li>
        @endcan

        @can('manage dispensasi')
        <li>
            <a href="{{ route('kesiswaan.persetujuan-dispensasi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.persetujuan-dispensasi.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Dispensasi</span>
            </a>
        </li>
        @endcan

        @can('manage panggilan ortu')
        <li>
            <a href="{{ route('kesiswaan.pengaduan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.pengaduan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <span class="text-sm">Pengaduan Orang Tua</span>
            </a>
        </li>
        @endcan

        @can('manage kartu akses')
        <li>
            <a href="{{ route('kesiswaan.kartu-akses.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.kartu-akses.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                <span class="text-sm">Stella Access Card</span>
                <span class="bg-indigo-100 text-indigo-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
            </a>
        </li>
        @endcan

        {{-- Dropdown Poin & Tata Tertib --}}
        @canany(['manage poin pelanggaran', 'manage poin prestasi', 'manage pemutihan poin'])
        <li x-data="{ expanded: {{ request()->routeIs('kesiswaan.poin-peraturan.*') || request()->routeIs('kesiswaan.input-*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Poin & Tata Tertib</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage poin pelanggaran')
                <li><a href="{{ route('kesiswaan.poin-peraturan.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.poin-peraturan.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Manajemen
                        Aturan</a></li>
                <li><a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Input
                        Pelanggaran</a></li>
                @endcan
                @can('manage poin prestasi')
                <li><a href="{{ route('kesiswaan.input-prestasi.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Input
                        Prestasi</a></li>
                @endcan
                @can('manage pemutihan poin')
                <li><a href="{{ route('kesiswaan.input-pemutihan.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Pemutihan
                        Poin</a></li>
                @endcan
            </ul>
        </li>
        @endcanany

        {{-- Dropdown Monitoring BK --}}
        @canany(['manage pembinaan rutin', 'manage jadwal konsultasi', 'manage panggilan ortu'])
        <li x-data="{ expanded: {{ request()->routeIs('kesiswaan.monitoring-bk.*') || request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-sm font-medium">Monitoring BK</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage pembinaan rutin')
                <li><a href="{{ route('kesiswaan.monitoring-bk.pembinaan') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.monitoring-bk.pembinaan') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Pembinaan Rutin</a></li>
                @endcan
                @can('manage jadwal konsultasi')
                <li><a href="{{ route('kesiswaan.monitoring-bk.konsultasi') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.monitoring-bk.konsultasi') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Konsultasi Siswa</a></li>
                @endcan
                @can('manage panggilan ortu')
                <li><a href="{{ route('kesiswaan.panggilan-ortu.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Panggilan Orang Tua</a></li>
                @endcan
            </ul>
        </li>
        @endcanany

        @can('manage database maintenance')
        <li>
            <a href="{{ route('kesiswaan.database.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.database.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                </svg>
                <span class="text-sm tracking-tight capitalize">Maintenance database</span>
                <span class="bg-red-100 text-red-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">SAFE</span>
            </a>
        </li>
        @endcan
    @endif
    @endrole

    {{-- ============================================================ --}}
    {{-- ROLE: KURIKULUM                                              --}}
    {{-- ============================================================ --}}
    @canany(['manage jam pelajaran', 'manage mata pelajaran', 'manage guru', 'manage jadwal pelajaran', 'manage distribusi mapel'])
    @role('Kurikulum')
    @if(session('active_role') == 'Kurikulum')
        <li>
            <a href="{{ route('kurikulum.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kurikulum.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Dashboard Kurikulum</span>
            </a>
        </li>

        <li x-data="{ expanded: {{ request()->routeIs('kurikulum.jam-pelajaran.*') || request()->routeIs('kurikulum.mata-pelajaran.*') || request()->routeIs('kurikulum.master-guru.*') || request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span class="text-sm font-medium">Data Kurikulum</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage jam pelajaran')
                <li><a href="{{ route('kurikulum.jam-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.jam-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Pengaturan
                        Jam</a></li>
                @endcan
                @can('manage mata pelajaran')
                <li><a href="{{ route('kurikulum.mata-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.mata-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Mata
                        Pelajaran</a></li>
                @endcan
                @can('manage guru')
                <li><a href="{{ route('kurikulum.master-guru.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.master-guru.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Guru</a></li>
                @endcan
                @can('manage jadwal pelajaran')
                <li><a href="{{ route('kurikulum.jadwal-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Jadwal
                        Pelajaran</a></li>
                @endcan
                @can('manage distribusi mapel')
                <li><a href="{{ route('kurikulum.distribusi-mapel.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.distribusi-mapel.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Distribusi
                        Mapel</a></li>
                @endcan
            </ul>
        </li>
        <li x-data="{ expanded: {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') || request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') || request()->routeIs('kurikulum.analisa-semester.*') || request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="text-sm font-medium">Absensi Guru</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage monitoring absensi guru')
                <li><a href="{{ route('kurikulum.monitoring-absensi-guru.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Monitoring Harian</a></li>
                <li><a href="{{ route('kurikulum.monitoring-absensi-per-kelas.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Monitoring Per Kelas</a></li>
                @endcan
                @can('view analisa kurikulum')
                <li><a href="{{ route('kurikulum.analisa-semester.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.analisa-semester.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Analisa Semester</a></li>
                @endcan
                <li><a href="{{ route('kurikulum.persetujuan-izin-guru.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Persetujuan Izin Guru</a></li>
            </ul>
        </li>
    @endif
    @endrole
    @endcanany

    {{-- ============================================================ --}}
    {{-- ROLE: GURU BK                                                --}}
    {{-- ============================================================ --}}
    @can('view bk dashboard')
    @role('Guru BK')
    @if(session('active_role') == 'Guru BK')
        <li>
            <a href="{{ route('bk.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-sm">Dashboard BK</span>
            </a>
        </li>
        @can('manage monitoring bk')
        <li>
            <a href="{{ route('bk.monitoring.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.monitoring.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-sm">Monitoring Izin</span>
            </a>
        </li>
        @endcan
        @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('monitoring-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Monitoring Terlambat</span>
            </a>
        </li>
        @endcan
        @can('view coaching analytics')
        <li>
            <a href="{{ route('coaching-analytics.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('coaching-analytics.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Analisa Coaching</span>
            </a>
        </li>
        @endcan
        @can('manage monitoring catatan')
        <li>
            <a href="{{ route('bk.monitoring-catatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.monitoring-catatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-sm">Monitoring Catatan</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('kesiswaan.pengaduan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('kesiswaan.pengaduan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <span class="text-sm">Pengaduan Orang Tua</span>
            </a>
        </li>

        @can('manage poin tata tertib')
        <li x-data="{ expanded: {{ request()->routeIs('kesiswaan.input-pelanggaran.*') || request()->routeIs('kesiswaan.input-prestasi.*') || request()->routeIs('kesiswaan.input-pemutihan.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Poin & Tata Tertib</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                <li><a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Input
                        Pelanggaran</a></li>
                <li><a href="{{ route('kesiswaan.input-prestasi.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Input
                        Prestasi</a></li>
                <li><a href="{{ route('kesiswaan.input-pemutihan.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Pemutihan
                        Poin</a></li>
            </ul>
        </li>
        @endcan

        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Layanan BK</div>
        @can('manage konsultasi bk')
        <li>
            <a href="{{ route('bk.konsultasi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.konsultasi.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Konsultasi Siswa</span>
            </a>
        </li>
        @endcan
        @can('manage chat bk')
        <li>
            <a href="{{ route('bk.chat.index') }}"
                class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.chat.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span class="text-sm">Chat Konsultasi</span>
                </div>
                <template x-if="unreadChatCount > 0">
                    <span class="bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full" x-text="unreadChatCount"></span>
                </template>
            </a>
        </li>
        @endcan
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: GURU PIKET                                             --}}
    {{-- ============================================================ --}}
    @can('view piket dashboard')
    @role('Guru Piket')
    @if(session('active_role') == 'Guru Piket')
        <li>
            <a href="{{ route('piket.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-sm">Dashboard Piket</span>
            </a>
        </li>
        @can('manage verifikasi terlambat')
        <li>
            <a href="{{ route('piket.verifikasi-terlambat.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.verifikasi-terlambat.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-sm">Verifikasi Terlambat</span>
            </a>
        </li>
        @endcan
        @can('manage student late handling')
        <li>
            <a href="{{ route('piket.penanganan-terlambat.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.penanganan-terlambat.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Penanganan Terlambat</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('piket.monitoring.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.monitoring.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="text-sm">Monitoring Izin</span>
            </a>
        </li>
        @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('monitoring-keterlambatan.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Monitoring Terlambat</span>
            </a>
        </li>
        @endcan
        <li>
            <a href="{{ route('piket.persetujuan-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('piket.persetujuan-izin-keluar.*') && !request()->routeIs('piket.persetujuan-izin-keluar.riwayat')) ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Keluar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('piket.persetujuan-izin-keluar.riwayat') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.persetujuan-izin-keluar.riwayat') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Keluar</span>
            </a>
        </li>        
        @can('manage teacher attendance tracking')
        <li>
            <a href="{{ route('piket.absensi-guru.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.absensi-guru.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="text-sm">Absensi Guru</span>
            </a>
        </li>
        @endcan
        @can('monitoring izin')
        <li>
            <a href="{{ route('piket.persetujuan-izin-guru.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.persetujuan-izin-guru.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Izin Guru</span>
            </a>
        </li>
        <li>
            <a href="{{ route('piket.monitoring-izin-guru.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.monitoring-izin-guru.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Monitoring Izin Guru</span>
            </a>
        </li>
        @endcan
        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Pusat Bantuan</div>
        <li>
            <a href="{{ route('docs.piket') }}" target="_blank"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-gray-700 hover:bg-red-50 hover:text-red-700">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-sm">Panduan Penggunaan</span>
            </a>
        </li>
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: SECURITY                                               --}}
    {{-- ============================================================ --}}
    @can('manage gate terminal')
    @role('Security')
    @if(session('active_role') == 'Security')
        <li>
            <a href="{{ route('security.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('security.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg>
                <span class="text-sm">Dashboard Security</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.pendataan-terlambat.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('security.pendataan-terlambat.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Pendataan Terlambat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.riwayat') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('security.verifikasi.riwayat') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Izin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('security.verifikasi.*') && !request()->routeIs('security.verifikasi.riwayat') && !request()->routeIs('security.verifikasi.scan')) ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-sm">Verifikasi Gerbang</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.scan') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('security.verifikasi.scan') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 18.5v.01M12 10.5v.01M16 18.5v.01M16 14.5v.01M16 10.5v.01M8 18.5v.01M8 14.5v.01M8 10.5v.01M4 11l.001-.001M4 15l.001-.001M4 19l.001-.001M20 19l.001-.001M20 15l.001-.001M20 11l.001-.001" />
                </svg>
                <span class="text-sm">Pindai QR</span>
            </a>
        </li>        
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: KAUR SDM                                               --}}
    {{-- ============================================================ --}}
    @can('view sdm dashboard')
    @role('KAUR SDM')
    @if(session('active_role') == 'KAUR SDM')
        <li>
            <a href="{{ route('sdm.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('sdm.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg>
                <span class="text-sm">Dashboard SDM</span>
            </a>
        </li>
        <li>
            <a href="{{ route('sdm.monitoring.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('sdm.monitoring.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <span class="text-sm">Monitoring Guru</span>
            </a>
        </li>
        @can('manage perizinan guru')
        <li>
            <a href="{{ route('sdm.persetujuan-izin-guru.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('sdm.persetujuan-izin-guru.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Persetujuan Izin Guru</span>
            </a>
        </li>
        @endcan
        @can('view rekapitulasi sdm')
        <li>
            <a href="{{ route('sdm.rekapitulasi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('sdm.rekapitulasi.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm">Rekapitulasi Laporan</span>
            </a>
        </li>
        @endcan
        @can('manage nde referensi')
        <li>
            <a href="{{ route('sdm.nde-referensi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('sdm.nde-referensi.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="text-sm">Referensi NDE</span>
            </a>
        </li>
        @endcan
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: OPERATOR                                               --}}
    {{-- ============================================================ --}}
    @can('view operator dashboard')
    @role('Operator')
    @if(session('active_role') == 'Operator')
        <li>
            <a href="{{ route('operator.dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('operator.dashboard.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="text-sm">Dashboard Operator</span>
            </a>
        </li>

        {{-- Dropdown Master Data --}}
        @can('view master data')
        <li x-data="{ expanded: {{ request()->routeIs('master-data.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="text-sm font-medium">Master Data</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                @can('manage kelas')
                <li><a href="{{ route('master-data.kelas.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.kelas.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Kelas</a></li>
                @endcan
                @can('manage siswa')
                <li><a href="{{ route('master-data.siswa.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.siswa.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Siswa</a></li>
                @endcan
                @can('manage rombel')
                <li><a href="{{ route('master-data.rombel.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.rombel.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Rombel</a></li>
                @endcan
            </ul>
        </li>
        @endcan

        {{-- Dapodik Management --}}
        @can('manage dapodik')
        <li>
            <a href="{{ route('operator.dapodik.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('operator.dapodik.index') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
                <span class="text-sm">Manajemen Dapodik</span>
            </a>
        </li>
        <li>
            <a href="{{ route('operator.dapodik.submissions.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('operator.dapodik.submissions.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Verifikasi Dapodik</span>
                @php
                    $pendingCount = \App\Models\DapodikSubmission::where('status', 'pending')->count();
                @endphp
                @if ($pendingCount > 0)
                    <span class="bg-red-100 text-red-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>
        @endcan
    @endif
    @endrole
    @endcan

    {{-- ============================================================ --}}
    {{-- ROLE: PRAKERIN (Koordinator)                                 --}}
    {{-- ============================================================ --}}
    @can('manage prakerin')
    @role('Koordinator Prakerin|Waka Kesiswaan|Kurikulum')
    @if(in_array(session('active_role'), ['Koordinator Prakerin', 'Waka Kesiswaan', 'Kurikulum']))
        <li x-data="{ expanded: {{ request()->routeIs('prakerin.*') ? 'true' : 'false' }} }">
            <button @click="expanded = !expanded"
                class="flex items-center justify-between w-full px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium">Prakerin</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''" class="w-4 h-4 transition-transform transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul x-show="expanded" x-collapse class="pl-10 mt-1 space-y-1">
                <li><a href="{{ route('prakerin.industri.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prakerin.industri.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Industri</a></li>
                <li><a href="{{ route('prakerin.penempatan.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('prakerin.penempatan.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Penempatan
                        Siswa</a></li>
            </ul>
        </li>
    @endif
    @endrole
    @endcan

    <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Komunikasi</div>
    <li>
        <a href="{{ route('shared.nde.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('shared.nde.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span class="text-sm">Nota Dinas</span>
        </a>
    </li>

    <li>
        <a href="{{ route('changelog.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('changelog.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-sm">Change Log</span>
            <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">v1.0</span>
        </a>
    </li>

    {{-- SEPARATOR & PROFILE MENU (Always Visible) --}}
    <li class="pt-4 mt-2 border-t border-gray-100"></li>

    <li>
        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-sm font-medium">Profile</span>
        </a>
    </li>

    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors text-left">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7" />
                </svg>
                <span class="text-sm font-medium">Keluar</span>
            </button>
        </form>
    </li>
</ul>

<!-- Global Chat Notification Toast -->
<div class="fixed bottom-6 right-6 z-[200]">
    <template x-if="showToast">
        <div x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-10 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-white border border-gray-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4 max-w-sm animate-bounce-subtle">
            <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Pesan Baru</p>
                <p class="text-sm text-gray-500 font-medium">Ada pesan konsultasi baru masuk.</p>
            </div>
            <button @click="showToast = false" class="text-gray-300 hover:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>

<script>
    function globalNotificationSystem() {
        return {
            unreadChatCount: 0,
            showToast: false,
            polling: null,

            init() {
                this.checkUnread();
                this.polling = setInterval(() => this.checkUnread(), 5000); // Poll every 5 seconds
                
                // Allow menu badges to listen to this data
                window.addEventListener('update-unread-chat', (e) => {
                    this.unreadChatCount = e.detail.count;
                });
            },

            async checkUnread() {
                try {
                    const response = await fetch('{{ route('api.chat.unread-count') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    
                    if (data.unread_count > this.unreadChatCount) {
                        // Only show toast if count increased AND we are not on chat page
                        if (!window.location.pathname.includes('/chat')) {
                            this.showToast = true;
                            setTimeout(() => { this.showToast = false; }, 5000);
                        }
                    }
                    
                    this.unreadChatCount = data.unread_count;
                    // Provide the data globally by attaching to window or using a shared state
                    window.unreadChatCount = this.unreadChatCount;
                } catch (error) {
                    console.warn('Notification poll failed');
                }
            }
        }
    }
</script>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 2s infinite ease-in-out;
    }
</style>
</div>
