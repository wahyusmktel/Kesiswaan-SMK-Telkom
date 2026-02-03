<style>
    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.675rem 1rem;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        margin-bottom: 0.125rem;
        position: relative;
    }

    .nav-link-active {
        background-color: rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: #ffffff !important;
        font-weight: 700;
        backdrop-filter: blur(4px);
    }

    .nav-link-inactive {
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
    }

    .nav-link-inactive:hover {
        background-color: #344767;
        /* Dark Gray Hover */
        color: #ffffff !important;
        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.2);
    }

    .nav-icon-container {
        width: 32px;
        height: 32px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .nav-link-active .nav-icon-container {
        background-color: #ffffff;
        color: #ee2d24 !important;
        /* Red icon for active link */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .nav-link:not(.nav-link-active) .nav-icon-container {
        background-color: rgba(255, 255, 255, 0.15);
        color: #ffffff;
    }

    .nav-icon {
        width: 14px;
        height: 14px;
    }

    .nav-text {
        font-size: 0.875rem;
        margin-left: 0.75rem;
        transition: all 0.2s ease;
    }

    /* Dropdown parent menu icon container styling */
    .nav-link-inactive .nav-icon-container {
        background-color: rgba(255, 255, 255, 0.15);
        color: #ffffff;
    }

    .section-title {
        padding: 1.5rem 1rem 0.5rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sidebar-collapsed .nav-link {
        justify-content: center;
        padding: 0.675rem 0;
    }

    .sidebar-collapsed .nav-text,
    .sidebar-collapsed .section-title,
    .sidebar-collapsed .dropdown-arrow,
    .sidebar-collapsed .nav-badge {
        display: none;
    }

    .sidebar-collapsed .nav-link-active {
        width: 48px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Hybrid Submenu Styles */
    .submenu-dropdown {
        position: relative;
    }

    /* Inline Card Submenu (Expanded Sidebar) */
    .submenu-card {
        margin: 0.5rem 0.5rem 0.75rem 0.5rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border-radius: 0.875rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .submenu-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 0.75rem;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        border-radius: 0.5rem;
        transition: all 0.15s ease;
    }

    .submenu-item:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
    }

    .submenu-item-active {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        font-weight: 600;
    }

    .submenu-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        flex-shrink: 0;
    }

    .submenu-item-active .submenu-dot {
        background: #ffffff;
    }

    /* Flyout Submenu (Collapsed Sidebar) - Fixed positioning to avoid overflow */
    .submenu-flyout {
        position: fixed;
        left: 5.5rem;
        min-width: 160px;
        background: #344767;
        border-radius: 0.75rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.35);
        padding: 0.5rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease;
        z-index: 9999;
        pointer-events: none;
    }

    /* Invisible bridge to maintain hover when moving from trigger to flyout */
    .submenu-flyout::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0;
        width: 1.5rem;
        height: 100%;
        background: transparent;
    }

    .submenu-flyout-title {
        padding: 0.5rem 0.75rem 0.375rem;
        font-size: 0.65rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.5);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Show flyout on hover when collapsed */
    .sidebar-collapsed .submenu-dropdown:hover .submenu-flyout {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    /* Hide inline card when collapsed */
    .sidebar-collapsed .submenu-card {
        display: none;
    }

    /* Show flyout only when collapsed */
    .submenu-flyout {
        display: none;
    }

    .sidebar-collapsed .submenu-flyout {
        display: block;
    }
</style>



{{-- ============================================================ --}}
{{-- ROLE: ADMIN / KEPALA SEKOLAH (Tanpa Dashboard Spesifik) --}}
{{-- ============================================================ --}}
@role('Super Admin|Kepala Sekolah')
@if(session('active_role') == 'Super Admin' || session('active_role') == 'Kepala Sekolah')
    <li>
        <a href="{{ route('dashboard') }}" title="Dashboard Admin"
            class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg></div>
            <span class="nav-text">Dashboard Admin</span>
        </a>
    </li>
    @can('view users')
        <li>
            <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->routeIs("users.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg></div>
                <span class="nav-text">Manajemen Pengguna</span>
            </a>
        </li>
    @endcan
    @can('manage settings')
        <li>
            <a href="{{ route('super-admin.settings') }}"
                class="nav-link {{ request()->routeIs("super-admin.settings") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg></div>
                <span class="nav-text">Konfigurasi Aplikasi</span>
            </a>
        </li>
    @endcan
    @can('manage permissions')
        <li>
            <a href="{{ route('super-admin.permissions.index') }}"
                class="nav-link {{ request()->routeIs("super-admin.permissions.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg></div>
                <span class="nav-text italic">Manajemen Hak Akses</span>
            </a>
        </li>
    @endcan
@endif
@endrole

{{-- ============================================================ --}}
{{-- ROLE: SISWA --}}
{{-- ============================================================ --}}
@role('Siswa')
@if(session('active_role') == 'Siswa')
    <li>
        <a href="{{ route('siswa.dashboard.index') }}"
            class="nav-link {{ request()->routeIs("siswa.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                </svg></div>
            <span class="nav-text">Dashboard Siswa</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.lms.index') }}"
            class="nav-link {{ request()->routeIs("siswa.lms.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg></div>
            <span class="nav-text">Ruang Belajar</span>
        </a>
    </li>
    <li>
        <a href="{{ route('izin.index') }}"
            class="nav-link {{ request()->routeIs("izin.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <span class="nav-text">Riwayat Izin</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.izin-keluar-kelas.index') }}"
            class="nav-link {{ request()->routeIs("siswa.izin-keluar-kelas.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg></div>
            <span class="nav-text">Izin Keluar Kelas</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.riwayat-catatan.index') }}"
            class="nav-link {{ request()->routeIs("siswa.riwayat-catatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg></div>
            <span class="nav-text">Riwayat Catatan</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.riwayat-keterlambatan.index') }}"
            class="nav-link {{ request()->routeIs("siswa.riwayat-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <span class="nav-text">Riwayat Keterlambatan</span>
        </a>
    </li>

    @if (Auth::user()->masterSiswa?->penempatan()->where('status', 'aktif')->exists())
        <li>
            <a href="{{ route('siswa.jurnal-prakerin.index') }}"
                class="nav-link {{ request()->routeIs("siswa.jurnal-prakerin.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg></div>
                <span class="nav-text">Jurnal Prakerin</span>
            </a>
        </li>
    @endif

    <div class="section-title">Layanan BK</div>
    <li>
        <a href="{{ route('siswa.bk.index') }}"
            class="nav-link {{ request()->routeIs("siswa.bk.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg></div>
            <span class="nav-text">Konsultasi BK</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.chat.index') }}" title="Chat BK"
            class="nav-link justify-between {{ request()->routeIs('siswa.chat.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg></div>
                <span class="nav-text">Chat BK</span>
            </div>
            <template x-if="unreadChatCount > 0">
                <span class="nav-badge bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"
                    x-text="unreadChatCount"></span>
            </template>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.kartu-pelajar.index') }}"
            class="nav-link {{ request()->routeIs("siswa.kartu-pelajar.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg></div>
            <span class="nav-text">Kartu Pelajar Digital</span>
            <span
                class="nav-badge bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
        </a>
    </li>
    <li>
        <a href="{{ route('siswa.dapodik.index') }}"
            class="nav-link {{ request()->routeIs("siswa.dapodik.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg></div>
            <span class="nav-text">Data Dapodik</span>
        </a>
    </li>
@endif
@endrole

{{-- ============================================================ --}}
{{-- ROLE: WALI KELAS --}}
{{-- ============================================================ --}}
@can('view wali kelas dashboard')
@role('Wali Kelas')
@if(session('active_role') == 'Wali Kelas')
<li>
    <a href="{{ route('wali-kelas.dashboard.index') }}"
        class="nav-link {{ request()->routeIs("wali-kelas.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg></div>
        <span class="nav-text">Dashboard Wali</span>
    </a>
</li>
@can('manage perizinan wali kelas')
    <li>
        <a href="{{ route('wali-kelas.perizinan.index') }}"
            class="nav-link {{ request()->routeIs("wali-kelas.perizinan.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <span class="nav-text">Persetujuan Izin</span>
        </a>
    </li>
    @endcanany
    @can('view monitoring keterlambatan')
        <li>
            <a href="{{ route('monitoring-keterlambatan.index') }}"
                class="nav-link {{ request()->routeIs("monitoring-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Monitoring Terlambat</span>
            </a>
        </li>
    @endcan
    @can('view coaching analytics')
        <li>
            <a href="{{ route('coaching-analytics.index') }}"
                class="nav-link {{ request()->routeIs("coaching-analytics.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg></div>
                <span class="nav-text">Analisa Coaching</span>
            </a>
        </li>
    @endcan
    @endif
    @endrole
@endcan


{{-- ============================================================ --}}
{{-- ROLE: GURU KELAS --}}
{{-- ============================================================ --}}
@can('view guru kelas dashboard')
    @role('Guru Kelas')
    @if(session('active_role') == 'Guru Kelas')
        <li>
            <a href="{{ route('guru-kelas.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("guru-kelas.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg></div>
                <span class="nav-text">Dashboard Guru</span>
            </a>
        </li>
        <li>
            <a href="{{ route('guru.jadwal-saya') }}"
                class="nav-link {{ request()->routeIs("guru.jadwal-saya") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg></div>
                <span class="nav-text">Jadwal Mengajar</span>
            </a>
        </li>
        @can('manage lms')
            <li>
                <a href="{{ route('guru.lms.index') }}"
                    class="nav-link {{ request()->routeIs("guru.lms.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg></div>
                    <span class="nav-text">Ruang Belajar</span>
                </a>
            </li>
        @endcan
        @can('manage perizinan siswa')
            <li>
                <a href="{{ route('guru-kelas.persetujuan-izin-keluar.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('guru-kelas.persetujuan-izin-keluar.*') && !request()->routeIs('guru-kelas.persetujuan-izin-keluar.riwayat')) ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Persetujuan Keluar</span>
                </a>
            </li>
            <li>
                <a href="{{ route('guru-kelas.persetujuan-izin-keluar.riwayat') }}"
                    class="nav-link {{ request()->routeIs("guru-kelas.persetujuan-izin-keluar.riwayat") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Riwayat Persetujuan</span>
                </a>
            </li>
        @endcan
        @can('manage dispensasi')
            <li>
                <a href="{{ route('dispensasi.pengajuan.index') }}"
                    class="nav-link {{ request()->routeIs("dispensasi.pengajuan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg></div>
                    <span class="nav-text">Pengajuan Dispensasi</span>
                </a>
            </li>
        @endcan
        @can('view monitoring keterlambatan')
            <li>
                <a href="{{ route('monitoring-keterlambatan.index') }}"
                    class="nav-link {{ request()->routeIs("monitoring-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Monitoring Terlambat</span>
                </a>
            </li>
        @endcan
        {{-- Menu Pembimbing Prakerin --}}
        @can('monitor prakerin')
            @if (Auth::user()->masterGuru?->penempatan()->where('status', 'aktif')->exists())
                <li>
                    <a href="{{ route('pembimbing-prakerin.monitoring.index') }}"
                        class="nav-link {{ request()->routeIs("pembimbing-prakerin.monitoring.*") ? "nav-link-active" : "nav-link-inactive" }}">
                        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg></div>
                        <span class="nav-text">Monitoring Prakerin</span>
                    </a>
                </li>
            @endif
        @endcan

        <div class="section-title">Layanan Guru</div>
        <li>
            <a href="{{ route('guru.izin.index') }}"
                class="nav-link {{ request()->routeIs("guru.izin.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg></div>
                <span class="nav-text">Pengajuan Izin Guru</span>
            </a>
        </li>
    @endif
    @endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: WAKA KESISWAAN --}}
{{-- ============================================================ --}}
@role('Waka Kesiswaan')
@if(session('active_role') == 'Waka Kesiswaan')
    @can('view kesiswaan dashboard')
        <li>
            <a href="{{ route('kesiswaan.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg></div>
                <span class="nav-text">Dashboard Kesiswaan</span>
            </a>
        </li>
    @endcan

    {{-- Manajemen Pengguna untuk Waka --}}
    @can('manage tahun pelajaran')
        <li>
            <a href="{{ route('master-data.tahun-pelajaran.index') }}"
                class="nav-link {{ request()->routeIs("master-data.tahun-pelajaran.index") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg></div>
                <span class="nav-text">Tahun Pelajaran</span>
            </a>
        </li>
    @endcan
    @can('view users')
        <li>
            <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->routeIs("users.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg></div>
                <span class="nav-text">Manajemen Pengguna</span>
            </a>
        </li>
    @endcan
    @can('view roles')
        <li>
            <a href="{{ route('admin.roles.index') }}"
                class="nav-link {{ request()->routeIs("admin.roles.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg></div>
                <span class="nav-text">Manajemen Role</span>
            </a>
        </li>
    @endcan

    {{-- Dropdown Master Data (Hybrid: Inline + Flyout) --}}
    @canany(['manage kelas', 'manage siswa', 'manage rombel'])
        <li class="submenu-dropdown" x-data="{ 
                                                                        expanded: {{ request()->routeIs(['master-data.kelas.*', 'master-data.siswa.*', 'master-data.rombel.*']) ? 'true' : 'false' }},
                                                                        flyoutTop: 0,
                                                                        updateFlyoutPosition() {
                                                                            const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                            this.flyoutTop = rect.top;
                                                                        }
                                                                    }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs(['master-data.kelas.*', 'master-data.siswa.*', 'master-data.rombel.*']) ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <span class="nav-text">Master Data</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu (Expanded Sidebar) -->
            <div x-show="expanded" x-collapse class="submenu-card">
                @can('manage kelas')
                    <a href="{{ route('master-data.kelas.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.kelas.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Data Kelas
                    </a>
                @endcan
                @can('manage siswa')
                    <a href="{{ route('master-data.siswa.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.siswa.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Data Siswa
                    </a>
                @endcan
                @can('manage rombel')
                    <a href="{{ route('master-data.rombel.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.rombel.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Data Rombel
                    </a>
                @endcan
            </div>

            <!-- Flyout Submenu (Collapsed Sidebar) -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Master Data</div>
                @can('manage kelas')
                    <a href="{{ route('master-data.kelas.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.kelas.*') ? 'submenu-item-active' : '' }}">
                        Data Kelas
                    </a>
                @endcan
                @can('manage siswa')
                    <a href="{{ route('master-data.siswa.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.siswa.*') ? 'submenu-item-active' : '' }}">
                        Data Siswa
                    </a>
                @endcan
                @can('manage rombel')
                    <a href="{{ route('master-data.rombel.index') }}"
                        class="submenu-item {{ request()->routeIs('master-data.rombel.*') ? 'submenu-item-active' : '' }}">
                        Data Rombel
                    </a>
                @endcan
            </div>
        </li>
    @endcanany

    @can('monitoring izin')
        <li>
            <a href="{{ route('kesiswaan.monitoring-izin.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.monitoring-izin.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg></div>
                <span class="nav-text">Monitoring Izin</span>
            </a>
        </li>
    @endcan

    @can('manage penanganan terlambat')
        @can('view monitoring keterlambatan')
            <li>
                <a href="{{ route('monitoring-keterlambatan.index') }}"
                    class="nav-link {{ request()->routeIs("monitoring-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Monitoring Terlambat</span>
                </a>
            </li>
            <li>
                <a href="{{ route('kesiswaan.analisa-keterlambatan.index') }}"
                    class="nav-link {{ request()->routeIs("kesiswaan.analisa-keterlambatan.index") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg></div>
                    <span class="nav-text">Analisa Keterlambatan</span>
                </a>
            </li>
        @endcan
        @can('view coaching analytics')
            <li>
                <a href="{{ route('coaching-analytics.index') }}"
                    class="nav-link {{ request()->routeIs("coaching-analytics.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg></div>
                    <span class="nav-text">Analisa Coaching</span>
                    <span
                        class="nav-badge bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
                </a>
            </li>
        @endcan
    @endcan

    @can('monitoring izin')
        <li>
            <a href="{{ route('kesiswaan.riwayat-izin-keluar.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.riwayat-izin-keluar.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">History Izin Keluar</span>
            </a>
        </li>
    @endcan

    @can('manage dispensasi')
        <li>
            <a href="{{ route('kesiswaan.persetujuan-dispensasi.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.persetujuan-dispensasi.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Persetujuan Dispensasi</span>
            </a>
        </li>
    @endcan

    @can('manage panggilan ortu')
        <li>
            <a href="{{ route('kesiswaan.pengaduan.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.pengaduan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg></div>
                <span class="nav-text">Pengaduan Orang Tua</span>
            </a>
        </li>
    @endcan

    @can('manage kartu akses')
        <li>
            <a href="{{ route('kesiswaan.kartu-akses.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.kartu-akses.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg></div>
                <span class="nav-text">Stella Access Card</span>
                <span
                    class="nav-badge bg-indigo-100 text-indigo-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">NEW</span>
            </a>
        </li>
    @endcan

    {{-- Dropdown Poin & Tata Tertib --}}
    @canany(['manage poin pelanggaran', 'manage poin prestasi', 'manage pemutihan poin'])
        <li class="submenu-dropdown" x-data="{ 
                                                                        expanded: {{ request()->routeIs('kesiswaan.poin-peraturan.*') || request()->routeIs('kesiswaan.input-*') ? 'true' : 'false' }},
                                                                        flyoutTop: 0,
                                                                        updateFlyoutPosition() {
                                                                            const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                            this.flyoutTop = rect.top;
                                                                        }
                                                                    }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs('kesiswaan.poin-peraturan.*') || request()->routeIs('kesiswaan.input-*') ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="nav-text">Poin & Tata Tertib</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu -->
            <div x-show="expanded" x-collapse class="submenu-card">
                @can('manage poin pelanggaran')
                    <a href="{{ route('kesiswaan.poin-peraturan.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.poin-peraturan.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Manajemen Aturan
                    </a>
                    <a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Input Pelanggaran
                    </a>
                @endcan
                @can('manage poin prestasi')
                    <a href="{{ route('kesiswaan.input-prestasi.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Input Prestasi
                    </a>
                @endcan
                @can('manage pemutihan poin')
                    <a href="{{ route('kesiswaan.input-pemutihan.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Pemutihan Poin
                    </a>
                @endcan
            </div>

            <!-- Flyout Submenu -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Poin & Tata Tertib</div>
                @can('manage poin pelanggaran')
                    <a href="{{ route('kesiswaan.poin-peraturan.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.poin-peraturan.*') ? 'submenu-item-active' : '' }}">
                        Manajemen Aturan
                    </a>
                    <a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'submenu-item-active' : '' }}">
                        Input Pelanggaran
                    </a>
                @endcan
                @can('manage poin prestasi')
                    <a href="{{ route('kesiswaan.input-prestasi.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'submenu-item-active' : '' }}">
                        Input Prestasi
                    </a>
                @endcan
                @can('manage pemutihan poin')
                    <a href="{{ route('kesiswaan.input-pemutihan.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'submenu-item-active' : '' }}">
                        Pemutihan Poin
                    </a>
                @endcan
            </div>
        </li>
    @endcanany

    {{-- Dropdown Monitoring BK --}}
    @canany(['manage pembinaan rutin', 'manage jadwal konsultasi', 'manage panggilan ortu'])
        <li class="submenu-dropdown" x-data="{ 
                                                                        expanded: {{ request()->routeIs('kesiswaan.monitoring-bk.*') || request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'true' : 'false' }},
                                                                        flyoutTop: 0,
                                                                        updateFlyoutPosition() {
                                                                            const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                            this.flyoutTop = rect.top;
                                                                        }
                                                                    }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs('kesiswaan.monitoring-bk.*') || request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <span class="nav-text">Monitoring BK</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu -->
            <div x-show="expanded" x-collapse class="submenu-card">
                @can('manage pembinaan rutin')
                    <a href="{{ route('kesiswaan.monitoring-bk.pembinaan') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.monitoring-bk.pembinaan') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Pembinaan Rutin
                    </a>
                @endcan
                @can('manage jadwal konsultasi')
                    <a href="{{ route('kesiswaan.monitoring-bk.konsultasi') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.monitoring-bk.konsultasi') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Konsultasi Siswa
                    </a>
                @endcan
                @can('manage panggilan ortu')
                    <a href="{{ route('kesiswaan.panggilan-ortu.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Panggilan Orang Tua
                    </a>
                @endcan
            </div>

            <!-- Flyout Submenu -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Monitoring BK</div>
                @can('manage pembinaan rutin')
                    <a href="{{ route('kesiswaan.monitoring-bk.pembinaan') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.monitoring-bk.pembinaan') ? 'submenu-item-active' : '' }}">
                        Pembinaan Rutin
                    </a>
                @endcan
                @can('manage jadwal konsultasi')
                    <a href="{{ route('kesiswaan.monitoring-bk.konsultasi') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.monitoring-bk.konsultasi') ? 'submenu-item-active' : '' }}">
                        Konsultasi Siswa
                    </a>
                @endcan
                @can('manage panggilan ortu')
                    <a href="{{ route('kesiswaan.panggilan-ortu.index') }}"
                        class="submenu-item {{ request()->routeIs('kesiswaan.panggilan-ortu.*') ? 'submenu-item-active' : '' }}">
                        Panggilan Orang Tua
                    </a>
                @endcan
            </div>
        </li>
    @endcanany

    @can('manage database maintenance')
        <li>
            <a href="{{ route('kesiswaan.database.index') }}"
                class="nav-link {{ request()->routeIs("kesiswaan.database.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                </div>
                <span class="nav-text">Database</span>
                <span
                    class="nav-badge bg-red-100 text-red-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">SAFE</span>
            </a>
        </li>
    @endcan
@endif
@endrole

{{-- ============================================================ --}}
{{-- ROLE: KURIKULUM --}}
{{-- ============================================================ --}}
@canany(['manage jam pelajaran', 'manage mata pelajaran', 'manage guru', 'manage jadwal pelajaran', 'manage distribusi mapel'])
    @role('Kurikulum')
    @if(session('active_role') == 'Kurikulum')
        <li>
            <a href="{{ route('kurikulum.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("kurikulum.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg></div>
                <span class="nav-text">Dashboard Kurikulum</span>
            </a>
        </li>

        <li class="submenu-dropdown" x-data="{ 
                                                                expanded: {{ request()->routeIs('kurikulum.jam-pelajaran.*') || request()->routeIs('kurikulum.mata-pelajaran.*') || request()->routeIs('kurikulum.master-guru.*') || request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'true' : 'false' }},
                                                                flyoutTop: 0,
                                                                updateFlyoutPosition() {
                                                                    const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                    this.flyoutTop = rect.top;
                                                                }
                                                            }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs('kurikulum.jam-pelajaran.*') || request()->routeIs('kurikulum.mata-pelajaran.*') || request()->routeIs('kurikulum.master-guru.*') || request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="nav-text">Data Kurikulum</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu -->
            <div x-show="expanded" x-collapse class="submenu-card">
                @can('manage jam pelajaran')
                    <a href="{{ route('kurikulum.jam-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.jam-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Pengaturan Jam
                    </a>
                @endcan
                @can('manage mata pelajaran')
                    <a href="{{ route('kurikulum.mata-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.mata-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Mata Pelajaran
                    </a>
                @endcan
                @can('manage guru')
                    <a href="{{ route('kurikulum.master-guru.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.master-guru.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Data Guru
                    </a>
                @endcan
                @can('manage jadwal pelajaran')
                    <a href="{{ route('kurikulum.jadwal-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Jadwal Pelajaran
                    </a>
                @endcan
                @can('manage distribusi mapel')
                    <a href="{{ route('kurikulum.distribusi-mapel.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.distribusi-mapel.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Distribusi Mapel
                    </a>
                @endcan
            </div>

            <!-- Flyout Submenu -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Data Kurikulum</div>
                @can('manage jam pelajaran')
                    <a href="{{ route('kurikulum.jam-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.jam-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        Pengaturan Jam
                    </a>
                @endcan
                @can('manage mata pelajaran')
                    <a href="{{ route('kurikulum.mata-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.mata-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        Mata Pelajaran
                    </a>
                @endcan
                @can('manage guru')
                    <a href="{{ route('kurikulum.master-guru.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.master-guru.*') ? 'submenu-item-active' : '' }}">
                        Data Guru
                    </a>
                @endcan
                @can('manage jadwal pelajaran')
                    <a href="{{ route('kurikulum.jadwal-pelajaran.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.jadwal-pelajaran.*') ? 'submenu-item-active' : '' }}">
                        Jadwal Pelajaran
                    </a>
                @endcan
                @can('manage distribusi mapel')
                    <a href="{{ route('kurikulum.distribusi-mapel.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.distribusi-mapel.*') ? 'submenu-item-active' : '' }}">
                        Distribusi Mapel
                    </a>
                @endcan
            </div>
        </li>

        <li class="submenu-dropdown" x-data="{ 
                                                                expanded: {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') || request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') || request()->routeIs('kurikulum.analisa-semester.*') || request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'true' : 'false' }},
                                                                flyoutTop: 0,
                                                                updateFlyoutPosition() {
                                                                    const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                    this.flyoutTop = rect.top;
                                                                }
                                                            }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') || request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') || request()->routeIs('kurikulum.analisa-semester.*') || request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <span class="nav-text">Absensi Guru</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu -->
            <div x-show="expanded" x-collapse class="submenu-card">
                @can('manage monitoring absensi guru')
                    <a href="{{ route('kurikulum.monitoring-absensi-guru.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Monitoring Harian
                    </a>
                    <a href="{{ route('kurikulum.monitoring-absensi-per-kelas.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Monitoring Per Kelas
                    </a>
                @endcan
                @can('view analisa kurikulum')
                    <a href="{{ route('kurikulum.analisa-semester.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.analisa-semester.*') ? 'submenu-item-active' : '' }}">
                        <span class="submenu-dot"></span>
                        Analisa Semester
                    </a>
                @endcan
                <a href="{{ route('kurikulum.persetujuan-izin-guru.index') }}"
                    class="submenu-item {{ request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'submenu-item-active' : '' }}">
                    <span class="submenu-dot"></span>
                    Persetujuan Izin Guru
                </a>
            </div>

            <!-- Flyout Submenu -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Absensi Guru</div>
                @can('manage monitoring absensi guru')
                    <a href="{{ route('kurikulum.monitoring-absensi-guru.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.monitoring-absensi-guru.*') ? 'submenu-item-active' : '' }}">
                        Monitoring Harian
                    </a>
                    <a href="{{ route('kurikulum.monitoring-absensi-per-kelas.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.monitoring-absensi-per-kelas.*') ? 'submenu-item-active' : '' }}">
                        Monitoring Per Kelas
                    </a>
                @endcan
                @can('view analisa kurikulum')
                    <a href="{{ route('kurikulum.analisa-semester.index') }}"
                        class="submenu-item {{ request()->routeIs('kurikulum.analisa-semester.*') ? 'submenu-item-active' : '' }}">
                        Analisa Semester
                    </a>
                @endcan
                <a href="{{ route('kurikulum.persetujuan-izin-guru.index') }}"
                    class="submenu-item {{ request()->routeIs('kurikulum.persetujuan-izin-guru.*') ? 'submenu-item-active' : '' }}">
                    Persetujuan Izin Guru
                </a>
            </div>
        </li>
    @endif
    @endrole
@endcanany

{{-- ============================================================ --}}
{{-- ROLE: GURU BK --}}
{{-- ============================================================ --}}
@can('view bk dashboard')
@role('Guru BK')
@if(session('active_role') == 'Guru BK')
<li>
    <a href="{{ route('bk.dashboard.index') }}"
        class="nav-link {{ request()->routeIs("bk.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg></div>
        <span class="nav-text">Dashboard BK</span>
    </a>
</li>
@can('monitoring izin')
    <li>
        <a href="{{ route('bk.monitoring.index') }}"
            class="nav-link {{ request()->routeIs("bk.monitoring.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg></div>
            <span class="nav-text">Monitoring Izin</span>
        </a>
    </li>
@endcan
@can('view monitoring keterlambatan')
    <li>
        <a href="{{ route('monitoring-keterlambatan.index') }}"
            class="nav-link {{ request()->routeIs("monitoring-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg></div>
            <span class="nav-text">Monitoring Terlambat</span>
        </a>
    </li>
@endcan
@can('view coaching analytics')
    <li>
        <a href="{{ route('coaching-analytics.index') }}"
            class="nav-link {{ request()->routeIs("coaching-analytics.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg></div>
            <span class="nav-text">Analisa Coaching</span>
        </a>
    </li>
@endcan
@can('manage poin pelanggaran')
    <li>
        <a href="{{ route('bk.monitoring-catatan.index') }}"
            class="nav-link {{ request()->routeIs("bk.monitoring-catatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg></div>
            <span class="nav-text">Monitoring Catatan</span>
        </a>
    </li>
@endcan
<li>
    <a href="{{ route('kesiswaan.pengaduan.index') }}"
        class="nav-link {{ request()->routeIs("kesiswaan.pengaduan.*") ? "nav-link-active" : "nav-link-inactive" }}">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg></div>
        <span class="nav-text">Pengaduan Orang Tua</span>
    </a>
</li>

@canany(['manage poin pelanggaran', 'manage poin prestasi', 'manage pemutihan poin'])
<li class="submenu-dropdown" x-data="{ 
        expanded: {{ request()->routeIs('kesiswaan.input-pelanggaran.*') || request()->routeIs('kesiswaan.input-prestasi.*') || request()->routeIs('kesiswaan.input-pemutihan.*') ? 'true' : 'false' }},
        flyoutTop: 0,
        updateFlyoutPosition() {
            const rect = this.$el.querySelector('button').getBoundingClientRect();
            this.flyoutTop = rect.top;
        }
    }" @mouseenter="updateFlyoutPosition()">
    <button @click="expanded = !expanded"
        class="nav-link w-full {{ request()->routeIs('kesiswaan.input-pelanggaran.*') || request()->routeIs('kesiswaan.input-prestasi.*') || request()->routeIs('kesiswaan.input-pemutihan.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
        <div class="flex items-center">
            <div class="nav-icon-container">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <span class="nav-text">Poin & Tata Tertib</span>
        </div>
        <svg :class="expanded ? 'rotate-180' : ''"
            class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Inline Card Submenu -->
    <div x-show="expanded" x-collapse class="submenu-card">
        <a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'submenu-item-active' : '' }}">
            <span class="submenu-dot"></span>
            Input Pelanggaran
        </a>
        <a href="{{ route('kesiswaan.input-prestasi.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'submenu-item-active' : '' }}">
            <span class="submenu-dot"></span>
            Input Prestasi
        </a>
        <a href="{{ route('kesiswaan.input-pemutihan.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'submenu-item-active' : '' }}">
            <span class="submenu-dot"></span>
            Pemutihan Poin
        </a>
    </div>

    <!-- Flyout Submenu -->
    <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
        <div class="submenu-flyout-title">Poin & Tata Tertib</div>
        <a href="{{ route('kesiswaan.input-pelanggaran.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-pelanggaran.*') ? 'submenu-item-active' : '' }}">
            Input Pelanggaran
        </a>
        <a href="{{ route('kesiswaan.input-prestasi.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-prestasi.*') ? 'submenu-item-active' : '' }}">
            Input Prestasi
        </a>
        <a href="{{ route('kesiswaan.input-pemutihan.index') }}"
            class="submenu-item {{ request()->routeIs('kesiswaan.input-pemutihan.*') ? 'submenu-item-active' : '' }}">
            Pemutihan Poin
        </a>
    </div>
</li>
@endcan

<div class="section-title">Layanan BK</div>
@can('manage jadwal konsultasi')
    <li>
        <a href="{{ route('bk.konsultasi.index') }}"
            class="nav-link {{ request()->routeIs("bk.konsultasi.*") ? "nav-link-active" : "nav-link-inactive" }}">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg></div>
            <span class="nav-text">Konsultasi Siswa</span>
        </a>
    </li>
@endcan
@can('view chat bk')
    <li>
        <a href="{{ route('bk.chat.index') }}"
            class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('bk.chat.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
            <div class="flex items-center">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg></div>
                <span class="nav-text">Chat Konsultasi</span>
            </div>
            <template x-if="unreadChatCount > 0">
                <span class="nav-badge bg-red-600 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full"
                    x-text="unreadChatCount"></span>
            </template>
        </a>
    </li>
@endcan
@endif
@endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: GURU PIKET --}}
{{-- ============================================================ --}}
@can('view piket dashboard')
    @role('Guru Piket')
    @if(session('active_role') == 'Guru Piket')
        <li>
            <a href="{{ route('piket.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("piket.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg></div>
                <span class="nav-text">Dashboard Piket</span>
            </a>
        </li>
        @can('manage penanganan terlambat')
            <li>
                <a href="{{ route('piket.verifikasi-terlambat.index') }}"
                    class="nav-link {{ request()->routeIs("piket.verifikasi-terlambat.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg></div>
                    <span class="nav-text">Verifikasi Terlambat</span>
                </a>
            </li>
        @endcan
        @can('manage penanganan terlambat')
            <li>
                <a href="{{ route('piket.penanganan-terlambat.index') }}"
                    class="nav-link {{ request()->routeIs("piket.penanganan-terlambat.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Penanganan Terlambat</span>
                </a>
            </li>
        @endcan
        <li>
            <a href="{{ route('piket.monitoring.index') }}"
                class="nav-link {{ request()->routeIs("piket.monitoring.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg></div>
                <span class="nav-text">Monitoring Izin</span>
            </a>
        </li>
        @can('view monitoring keterlambatan')
            <li>
                <a href="{{ route('monitoring-keterlambatan.index') }}"
                    class="nav-link {{ request()->routeIs("monitoring-keterlambatan.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Monitoring Terlambat</span>
                </a>
            </li>
        @endcan
        <li>
            <a href="{{ route('piket.persetujuan-izin-keluar.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('piket.persetujuan-izin-keluar.*') && !request()->routeIs('piket.persetujuan-izin-keluar.riwayat')) ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Persetujuan Keluar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('piket.persetujuan-izin-keluar.riwayat') }}"
                class="nav-link {{ request()->routeIs("piket.persetujuan-izin-keluar.riwayat") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Riwayat Keluar</span>
            </a>
        </li>
        @can('manage absensi guru')
            <li>
                <a href="{{ route('piket.absensi-guru.index') }}"
                    class="nav-link {{ request()->routeIs("piket.absensi-guru.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg></div>
                    <span class="nav-text">Absensi Guru</span>
                </a>
            </li>
        @endcan
        @can('monitoring izin')
            <li>
                <a href="{{ route('piket.persetujuan-izin-guru.index') }}"
                    class="nav-link {{ request()->routeIs("piket.persetujuan-izin-guru.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Persetujuan Izin Guru</span>
                </a>
            </li>
            <li>
                <a href="{{ route('piket.monitoring-izin-guru.index') }}"
                    class="nav-link {{ request()->routeIs("piket.monitoring-izin-guru.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg></div>
                    <span class="nav-text">Monitoring Izin Guru</span>
                </a>
            </li>
        @endcan
        <div class="section-title">Pusat Bantuan</div>
        <li>
            <a href="{{ route('docs.piket') }}" target="_blank"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors nav-link-inactive">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg></div>
                <span class="nav-text">Panduan Penggunaan</span>
            </a>
        </li>
    @endif
    @endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: SECURITY --}}
{{-- ============================================================ --}}
@can('manage gate terminal')
    @role('Security')
    @if(session('active_role') == 'Security')
        <li>
            <a href="{{ route('security.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("security.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                    </svg></div>
                <span class="nav-text">Dashboard Security</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.pendataan-terlambat.index') }}"
                class="nav-link {{ request()->routeIs("security.pendataan-terlambat.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Pendataan Terlambat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.riwayat') }}"
                class="nav-link {{ request()->routeIs("security.verifikasi.riwayat") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <span class="nav-text">Riwayat Izin</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ (request()->routeIs('security.verifikasi.*') && !request()->routeIs('security.verifikasi.riwayat') && !request()->routeIs('security.verifikasi.scan')) ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg></div>
                <span class="nav-text">Verifikasi Gerbang</span>
            </a>
        </li>
        <li>
            <a href="{{ route('security.verifikasi.scan') }}"
                class="nav-link {{ request()->routeIs("security.verifikasi.scan") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 18.5v.01M12 10.5v.01M16 18.5v.01M16 14.5v.01M16 10.5v.01M8 18.5v.01M8 14.5v.01M8 10.5v.01M4 11l.001-.001M4 15l.001-.001M4 19l.001-.001M20 19l.001-.001M20 15l.001-.001M20 11l.001-.001" />
                    </svg></div>
                <span class="nav-text">Pindai QR</span>
            </a>
        </li>
    @endif
    @endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: KAUR SDM --}}
{{-- ============================================================ --}}
@can('view sdm dashboard')
    @role('KAUR SDM')
    @if(session('active_role') == 'KAUR SDM')
        <li>
            <a href="{{ route('sdm.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("sdm.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                    </svg></div>
                <span class="nav-text">Dashboard SDM</span>
            </a>
        </li>
        <li>
            <a href="{{ route('sdm.monitoring.index') }}"
                class="nav-link {{ request()->routeIs("sdm.monitoring.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg></div>
                <span class="nav-text">Monitoring Guru</span>
            </a>
        </li>
        @can('manage perizinan guru')
            <li>
                <a href="{{ route('sdm.persetujuan-izin-guru.index') }}"
                    class="nav-link {{ request()->routeIs("sdm.persetujuan-izin-guru.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Persetujuan Izin Guru</span>
                </a>
            </li>
        @endcan
        @can('view rekapitulasi sdm')
            <li>
                <a href="{{ route('sdm.rekapitulasi.index') }}"
                    class="nav-link {{ request()->routeIs("sdm.rekapitulasi.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg></div>
                    <span class="nav-text">Rekapitulasi Laporan</span>
                </a>
            </li>
        @endcan
        @can('manage nde referensi')
            <li>
                <a href="{{ route('sdm.nde-referensi.index') }}"
                    class="nav-link {{ request()->routeIs("sdm.nde-referensi.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg></div>
                    <span class="nav-text">Referensi NDE</span>
                </a>
            </li>
        @endcan
    @endif
    @endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: OPERATOR --}}
{{-- ============================================================ --}}
@can('view operator dashboard')
    @role('Operator')
    @if(session('active_role') == 'Operator')
        <li>
            <a href="{{ route('operator.dashboard.index') }}"
                class="nav-link {{ request()->routeIs("operator.dashboard.*") ? "nav-link-active" : "nav-link-inactive" }}">
                <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg></div>
                <span class="nav-text">Dashboard Operator</span>
            </a>
        </li>

        {{-- Dropdown Master Data --}}
        @can('view master data')
            <li class="submenu-dropdown" x-data="{ 
                                                                    expanded: {{ request()->routeIs(['master-data.kelas.*', 'master-data.siswa.*', 'master-data.rombel.*']) ? 'true' : 'false' }},
                                                                    flyoutTop: 0,
                                                                    updateFlyoutPosition() {
                                                                        const rect = this.$el.querySelector('button').getBoundingClientRect();
                                                                        this.flyoutTop = rect.top;
                                                                    }
                                                                }" @mouseenter="updateFlyoutPosition()">
                <button @click="expanded = !expanded"
                    class="nav-link w-full {{ request()->routeIs(['master-data.kelas.*', 'master-data.siswa.*', 'master-data.rombel.*']) ? 'nav-link-active' : 'nav-link-inactive' }}">
                    <div class="flex items-center">
                        <div class="nav-icon-container">
                            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="nav-text">Master Data</span>
                    </div>
                    <svg :class="expanded ? 'rotate-180' : ''"
                        class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Inline Card Submenu -->
                <div x-show="expanded" x-collapse class="submenu-card">
                    @can('manage kelas')
                        <a href="{{ route('master-data.kelas.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.kelas.*') ? 'submenu-item-active' : '' }}">
                            <span class="submenu-dot"></span>
                            Data Kelas
                        </a>
                    @endcan
                    @can('manage siswa')
                        <a href="{{ route('master-data.siswa.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.siswa.*') ? 'submenu-item-active' : '' }}">
                            <span class="submenu-dot"></span>
                            Data Siswa
                        </a>
                    @endcan
                    @can('manage rombel')
                        <a href="{{ route('master-data.rombel.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.rombel.*') ? 'submenu-item-active' : '' }}">
                            <span class="submenu-dot"></span>
                            Data Rombel
                        </a>
                    @endcan
                </div>

                <!-- Flyout Submenu -->
                <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                    <div class="submenu-flyout-title">Master Data</div>
                    @can('manage kelas')
                        <a href="{{ route('master-data.kelas.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.kelas.*') ? 'submenu-item-active' : '' }}">
                            Data Kelas
                        </a>
                    @endcan
                    @can('manage siswa')
                        <a href="{{ route('master-data.siswa.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.siswa.*') ? 'submenu-item-active' : '' }}">
                            Data Siswa
                        </a>
                    @endcan
                    @can('manage rombel')
                        <a href="{{ route('master-data.rombel.index') }}"
                            class="submenu-item {{ request()->routeIs('master-data.rombel.*') ? 'submenu-item-active' : '' }}">
                            Data Rombel
                        </a>
                    @endcan
                </div>
            </li>
        @endcan

        {{-- Dapodik Management --}}
        @can('manage dapodik')
            <li>
                <a href="{{ route('operator.dapodik.index') }}"
                    class="nav-link {{ request()->routeIs("operator.dapodik.index") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg></div>
                    <span class="nav-text">Manajemen Dapodik</span>
                </a>
            </li>
            <li>
                <a href="{{ route('operator.dapodik.submissions.index') }}"
                    class="nav-link {{ request()->routeIs("operator.dapodik.submissions.*") ? "nav-link-active" : "nav-link-inactive" }}">
                    <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></div>
                    <span class="nav-text">Verifikasi Dapodik</span>
                    @php
                        $pendingCount = \App\Models\DapodikSubmission::where('status', 'pending')->count();
                    @endphp
                    @if ($pendingCount > 0)
                        <span
                            class="nav-badge bg-red-100 text-red-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        @endcan
    @endif
    @endrole
@endcan

{{-- ============================================================ --}}
{{-- ROLE: PRAKERIN (Koordinator) --}}
{{-- ============================================================ --}}
@can('manage prakerin')
    @role('Koordinator Prakerin')
    @if(session('active_role') == 'Koordinator Prakerin')
        <li class="submenu-dropdown" x-data="{ 
                                        expanded: {{ request()->routeIs('prakerin.*') ? 'true' : 'false' }},
                                        flyoutTop: 0,
                                        updateFlyoutPosition() {
                                            const rect = this.$el.querySelector('button').getBoundingClientRect();
                                            this.flyoutTop = rect.top;
                                        }
                                    }" @mouseenter="updateFlyoutPosition()">
            <button @click="expanded = !expanded"
                class="nav-link w-full {{ request()->routeIs('prakerin.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
                <div class="flex items-center">
                    <div class="nav-icon-container">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="nav-text">Prakerin</span>
                </div>
                <svg :class="expanded ? 'rotate-180' : ''"
                    class="dropdown-arrow w-4 h-4 transition-transform transform text-white/60" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Inline Card Submenu -->
            <div x-show="expanded" x-collapse class="submenu-card">
                <a href="{{ route('prakerin.industri.index') }}"
                    class="submenu-item {{ request()->routeIs('prakerin.industri.*') ? 'submenu-item-active' : '' }}">
                    <span class="submenu-dot"></span>
                    Data Industri
                </a>
                <a href="{{ route('prakerin.penempatan.index') }}"
                    class="submenu-item {{ request()->routeIs('prakerin.penempatan.*') ? 'submenu-item-active' : '' }}">
                    <span class="submenu-dot"></span>
                    Penempatan Siswa
                </a>
            </div>

            <!-- Flyout Submenu -->
            <div class="submenu-flyout" :style="'top: ' + flyoutTop + 'px'">
                <div class="submenu-flyout-title">Prakerin</div>
                <a href="{{ route('prakerin.industri.index') }}"
                    class="submenu-item {{ request()->routeIs('prakerin.industri.*') ? 'submenu-item-active' : '' }}">
                    Data Industri
                </a>
                <a href="{{ route('prakerin.penempatan.index') }}"
                    class="submenu-item {{ request()->routeIs('prakerin.penempatan.*') ? 'submenu-item-active' : '' }}">
                    Penempatan Siswa
                </a>
            </div>
        </li>
    @endif
    @endrole
@endcan

<div class="section-title">Komunikasi</div>
<li>
    <a href="{{ route('shared.nde.index') }}"
        class="nav-link {{ request()->routeIs("shared.nde.*") ? "nav-link-active" : "nav-link-inactive" }}">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg></div>
        <span class="nav-text">Nota Dinas</span>
    </a>
</li>

<li>
    <a href="{{ route('changelog.index') }}"
        class="nav-link {{ request()->routeIs("changelog.*") ? "nav-link-active" : "nav-link-inactive" }}">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg></div>
        <span class="nav-text">Change Log</span>
        <span
            class="nav-badge bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">v1.0</span>
    </a>
</li>

{{-- SEPARATOR & PROFILE MENU (Always Visible) --}}
<li class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent mx-6 my-4"></li>

<li>
    <a href="{{ route('profile.edit') }}" title="Profile" class="nav-link nav-link-inactive">
        <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg></div>
        <span class="nav-text">Profile</span>
    </a>
</li>

<li>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" title="Keluar" class="nav-link nav-link-inactive w-full text-left">
            <div class="nav-icon-container"><svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7" />
                </svg></div>
            <span class="nav-text">Keluar</span>
        </button>
    </form>
</li>


<!-- Global Chat Notification Toast -->
<div class="fixed bottom-6 right-6 z-[200]">
    <template x-if="showToast">
        <div x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-10 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="bg-white border border-gray-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4 max-w-sm">
            <div
                class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-100 flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Pesan Baru</p>
                <p class="text-sm text-gray-500 font-medium leading-tight">Ada pesan konsultasi baru masuk.</p>
            </div>
            <button @click="showToast = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
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