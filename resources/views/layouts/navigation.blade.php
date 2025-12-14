<ul class="space-y-1 font-medium">

    <li>
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span class="text-sm">Dashboard</span>
        </a>
    </li>

    @role('Waka Kesiswaan|Kepala Sekolah')
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
    @endrole

    @role('Siswa')
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
    @endrole

    @role('Wali Kelas')
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
    @endrole

    @role('Waka Kesiswaan')
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
                <li><a href="{{ route('master-data.kelas.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.kelas.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Kelas</a></li>
                <li><a href="{{ route('master-data.siswa.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.siswa.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Siswa</a></li>
                <li><a href="{{ route('master-data.rombel.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('master-data.rombel.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Rombel</a></li>
            </ul>
        </li>

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
    @endrole

    @role('Guru BK')
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
    @endrole

    @role('Guru Piket')
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
        <li>
            <a href="{{ route('piket.persetujuan-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('piket.persetujuan-izin-keluar.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
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
    @endrole

    @role('Kurikulum')
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
                <li><a href="{{ route('kurikulum.jam-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.jam-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Pengaturan
                        Jam</a></li>
                <li><a href="{{ route('kurikulum.mata-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.mata-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Mata
                        Pelajaran</a></li>
                <li><a href="{{ route('kurikulum.master-guru.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.master-guru.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Data
                        Guru</a></li>
                <li><a href="{{ route('kurikulum.jadwal-pelajaran.index') }}"
                        class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'text-red-700 bg-red-50' : 'text-gray-600 hover:text-red-700' }}">Jadwal
                        Pelajaran</a></li>
            </ul>
        </li>
    @endrole

    @role('Guru Kelas')
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
            <a href="{{ route('guru-kelas.persetujuan-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('guru-kelas.persetujuan-izin-keluar.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
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
    @endrole

    @role('Security')
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
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('security.verifikasi.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-red-50 hover:text-red-700' }}">
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
    @endrole

    @role('Koordinator Prakerin|Waka Kesiswaan|Kurikulum')
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
    @endrole

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
