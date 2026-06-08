<li>
    <a href="{{ route('fingerprint-saya.index') }}"
        class="nav-link {{ request()->routeIs('fingerprint-saya.*') ? 'nav-link-active' : 'nav-link-inactive' }}">
        <div class="nav-icon-container">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.657 0 3-1.567 3-3.5S13.657 4 12 4 9 5.567 9 7.5 10.343 11 12 11zm0 0v9m-5-7.5c-1.657 0-3 1.343-3 3V20m16 0v-4.5c0-1.657-1.343-3-3-3" />
            </svg>
        </div>
        <span class="nav-text">Fingerprint Saya</span>
        <span class="nav-badge bg-blue-100 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-full ml-auto">MESIN</span>
    </a>
</li>
