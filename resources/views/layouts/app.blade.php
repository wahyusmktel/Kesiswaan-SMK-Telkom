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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        document.addEventListener('alpine:init', () => {
            if (window.Alpine && !window.Alpine.store('detailModal')) {
                window.Alpine.store('detailModal', {
                    on: false,
                    data: {},
                    open(item) {
                        this.data = item || {};
                        this.on = true;
                    },
                    close() {
                        this.on = false;
                        this.data = {};
                    }
                });
            }
        });
    </script>

    @vite(['resources/css/app.css', 'resources/css/authentication.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .plus-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .soft-ui-sidebar {
            background-image: linear-gradient(310deg, #ad0f06ff 0%, #ec2a2aff 100%) !important;
            /* Telkom Red Gradient */
            backdrop-filter: saturate(200%) blur(30px);
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.1);
            border: none;
            color: #ffffff !important;
        }

        .shadow-soft {
            box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05) !important;
        }

        /* Float effect for desktop */
        @media (min-width: 768px) {
            .sidebar-float {
                margin: 1.25rem 0 1.25rem 1.25rem;
                border-radius: 1rem 0 0 1rem !important;
                /* Only left corners rounded */
                height: calc(100vh - 2.5rem) !important;
            }
        }

        /* Transition for layout switch */
        .layout-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 plus-jakarta">
    <div x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }" class="flex flex-col md:flex-row h-screen overflow-hidden">

        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-gray-900/60 backdrop-blur-sm md:hidden">
        </div>

        <aside :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'w-72': !sidebarCollapsed,
                'w-24': sidebarCollapsed
            }"
            class="fixed inset-y-0 left-0 z-40 soft-ui-sidebar sidebar-float transform transition-all duration-300 ease-in-out flex flex-col md:relative md:translate-x-0 md:flex-shrink-0 sidebar-transition group">

            <!-- Sidebar Header -->
            <div class="h-24 px-6 flex items-center justify-between flex-shrink-0 relative">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        @if ($appSetting?->logo)
                            <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo"
                                class="object-contain w-full h-full">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg shadow-sm flex items-center justify-center text-white font-black text-xs">
                                S</div>
                        @endif
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-x-2"
                        x-transition:enter-end="opacity-100 transform translate-x-0" class="flex-1 min-w-0">
                        <h1 class="text-xs font-bold text-white leading-tight truncate uppercase tracking-tighter">
                            {{ $appSetting?->school_name ?? config('app.name', 'Aplikasi Izin') }}
                        </h1>
                        <p class="text-[9px] text-white/70 font-bold uppercase tracking-widest leading-none">Management
                            System</p>
                    </div>
                </div>

                <!-- Mobile Close Button -->
                <button @click="sidebarOpen = false" class="md:hidden text-white/70 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent mx-6 mb-4"></div>

            <!-- Navigation Area -->
            <div class="flex-1 overflow-hidden flex flex-col" x-data="globalNotificationSystem()" x-init="init()">
                @include('layouts.navigation-search')
                <nav class="px-4 space-y-1 overflow-y-auto flex-1 sidebar-scroll">
                    @php($roleMenuSettings = app(\App\Services\RoleMenuService::class)->settingsForActiveRole())
                    <ul id="role-navigation" class="space-y-1 font-medium"
                        :class="sidebarCollapsed ? 'sidebar-collapsed' : ''" style="visibility: hidden;">
                        @include('layouts.navigation')
                    </ul>
                    <script>
                        (() => {
                            const navigation = document.getElementById('role-navigation');
                            if (!navigation) return;

                            const settings = {{ Illuminate\Support\Js::from($roleMenuSettings) }};
                            if (!settings || Object.keys(settings).length === 0) {
                                navigation.style.visibility = 'visible';
                                return;
                            }

                            const slug = value => String(value || '')
                                .normalize('NFD')
                                .replace(/[\u0300-\u036f]/g, '')
                                .toLowerCase()
                                .trim()
                                .replace(/[^a-z0-9]+/g, '-')
                                .replace(/^-+|-+$/g, '');

                            try {
                                let section = 'Menu';
                                const occurrences = {};
                                const allElements = new Map();
                                const groups = [];
                                let currentGroup = { title: null, items: [] };
                                groups.push(currentGroup);

                                // 1. Scan semua node di role-navigation
                                Array.from(navigation.children).forEach((node, defaultOrder) => {
                                    if (node.classList?.contains('section-title')) {
                                        section = node.textContent.trim() || 'Menu';
                                        currentGroup = { title: node, items: [] };
                                        groups.push(currentGroup);
                                        return;
                                    }

                                    if (node.tagName !== 'LI') return;

                                    // Dropdown Menu
                                    if (node.classList?.contains('submenu-dropdown')) {
                                        const labelNode = node.querySelector('button .nav-text');
                                        if (labelNode) {
                                            const label = labelNode.textContent.trim();
                                            const base = slug(`${section} ${label}`);
                                            occurrences[base] = (occurrences[base] || 0) + 1;
                                            const key = occurrences[base] > 1 ? `${base}-${occurrences[base]}` : base;
                                            const configured = settings[key];

                                            node.dataset.menuKey = key;
                                            node.dataset.menuSection = section;
                                            node.hidden = configured?.visible === false;
                                            if (configured?.custom_label) {
                                                labelNode.textContent = configured.custom_label;
                                            }
                                            allElements.set(key, {
                                                node,
                                                key,
                                                label: configured?.custom_label || label,
                                                section: configured?.custom_section || section,
                                                custom_label: configured?.custom_label || null,
                                                custom_section: configured?.custom_section || null,
                                                isDropdown: true,
                                                order: Number.isFinite(Number(configured?.order)) ? Number(configured.order) : defaultOrder,
                                                parent_key: configured?.parent_key || null,
                                                defaultOrder
                                            });
                                            currentGroup.items.push(allElements.get(key));

                                            // Scan child submenu-item
                                            const subLinks = node.querySelectorAll('.submenu-card a.submenu-item');
                                            subLinks.forEach((subA, subIdx) => {
                                                const subClone = subA.cloneNode(true);
                                                subClone.querySelectorAll('.submenu-dot, .nav-badge').forEach(el => el.remove());
                                                const subLabel = subClone.textContent.trim();
                                                if (!subLabel) return;

                                                const subBase = slug(`${section} ${subLabel}`);
                                                occurrences[subBase] = (occurrences[subBase] || 0) + 1;
                                                const subKey = occurrences[subBase] > 1 ? `${subBase}-${occurrences[subBase]}` : subBase;
                                                const subConf = settings[subKey];

                                                subA.dataset.menuKey = subKey;
                                                subA.dataset.parentKey = subConf?.parent_key !== undefined ? subConf.parent_key : key;
                                                if (subConf?.visible === false) {
                                                    subA.hidden = true;
                                                    subA.style.display = 'none';
                                                }
                                                if (subConf?.custom_label) {
                                                    const dot = subA.querySelector('.submenu-dot');
                                                    const badge = subA.querySelector('.nav-badge');
                                                    subA.textContent = '';
                                                    if (dot) subA.appendChild(dot);
                                                    subA.appendChild(document.createTextNode(subConf.custom_label));
                                                    if (badge) subA.appendChild(badge);
                                                }

                                                allElements.set(subKey, {
                                                    node: subA,
                                                    key: subKey,
                                                    label: subConf?.custom_label || subLabel,
                                                    section: subConf?.custom_section || section,
                                                    custom_label: subConf?.custom_label || null,
                                                    custom_section: subConf?.custom_section || null,
                                                    isSubItem: true,
                                                    originalParent: node,
                                                    order: Number.isFinite(Number(subConf?.order)) ? Number(subConf.order) : defaultOrder + (subIdx * 0.01),
                                                    parent_key: subConf?.parent_key !== undefined ? subConf.parent_key : key,
                                                    defaultOrder: defaultOrder + (subIdx * 0.01)
                                                });
                                            });
                                        }
                                        return;
                                    }

                                    // Standalone Menu
                                    const labelNode = node.querySelector('.nav-text');
                                    if (labelNode) {
                                        const label = labelNode.textContent.trim();
                                        const base = slug(`${section} ${label}`);
                                        occurrences[base] = (occurrences[base] || 0) + 1;
                                        const key = occurrences[base] > 1 ? `${base}-${occurrences[base]}` : base;
                                        const configured = settings[key];

                                        node.dataset.menuKey = key;
                                        node.dataset.menuSection = section;
                                        node.hidden = configured?.visible === false;
                                        if (configured?.custom_label) {
                                            labelNode.textContent = configured.custom_label;
                                        }
                                        allElements.set(key, {
                                            node,
                                            key,
                                            label: configured?.custom_label || label,
                                            section: configured?.custom_section || section,
                                            custom_label: configured?.custom_label || null,
                                            custom_section: configured?.custom_section || null,
                                            isStandalone: true,
                                            order: Number.isFinite(Number(configured?.order)) ? Number(configured.order) : defaultOrder,
                                            parent_key: configured?.parent_key || null,
                                            defaultOrder
                                        });
                                        currentGroup.items.push(allElements.get(key));
                                    }
                                });

                                // 2. Terapkan Parent-Child Relocation (WordPress Sub-menu Engine)
                                allElements.forEach((item, key) => {
                                    if (item.parent_key) {
                                        const parentItem = allElements.get(item.parent_key);
                                        if (parentItem && parentItem.node !== item.node) {
                                            let card = parentItem.node.querySelector('.submenu-card');
                                            if (!card) {
                                                // Konversi standalone parent jadi accordion
                                                parentItem.node.classList.add('submenu-dropdown');
                                                const existingLink = parentItem.node.querySelector('a.nav-link');
                                                if (existingLink) {
                                                    const iconHtml = existingLink.querySelector('.nav-icon-container')?.outerHTML || '';
                                                    const labelText = parentItem.custom_label || existingLink.querySelector('.nav-text')?.textContent || parentItem.label;
                                                    parentItem.node.innerHTML = `
                                                        <button class="nav-link w-full nav-link-inactive">
                                                            <div class="flex items-center">
                                                                ${iconHtml}
                                                                <span class="nav-text">${labelText}</span>
                                                            </div>
                                                            <svg class="dropdown-arrow w-4 h-4 ml-auto text-white/60 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                        </button>
                                                        <div class="submenu-card"></div>
                                                    `;
                                                    card = parentItem.node.querySelector('.submenu-card');
                                                    const btn = parentItem.node.querySelector('button');
                                                    btn.addEventListener('click', () => {
                                                        const isExp = card.style.display !== 'none';
                                                        card.style.display = isExp ? 'none' : 'block';
                                                        btn.querySelector('.dropdown-arrow')?.classList.toggle('rotate-180', !isExp);
                                                    });
                                                }
                                            }

                                            // Masukkan item ke dalam .submenu-card parent
                                            if (card) {
                                                if (item.isSubItem) {
                                                    card.appendChild(item.node);
                                                } else if (item.node.tagName === 'LI') {
                                                    const link = item.node.querySelector('a.nav-link');
                                                    if (link) {
                                                        const href = link.getAttribute('href');
                                                        const subA = document.createElement('a');
                                                        subA.href = href;
                                                        subA.className = 'submenu-item';
                                                        subA.dataset.menuKey = item.key;
                                                        const effectiveLabel = item.custom_label || item.label;
                                                        subA.innerHTML = `<span class="submenu-dot"></span>${effectiveLabel}`;
                                                        if (item.node.hidden) {
                                                            subA.hidden = true;
                                                            subA.style.display = 'none';
                                                        }
                                                        card.appendChild(subA);
                                                        item.node.remove();
                                                    }
                                                }
                                            }
                                        }
                                    } else if (item.isSubItem && !item.parent_key) {
                                        // Menu anak yang dijadikan Menu Utama (outdent)
                                        const href = item.node.getAttribute('href');
                                        const li = document.createElement('li');
                                        li.className = 'nav-item';
                                        li.dataset.menuKey = item.key;
                                        li.dataset.menuSection = item.section;
                                        li.hidden = item.node.hidden;
                                        const effectiveLabel = item.custom_label || item.label;
                                        li.innerHTML = `
                                            <a href="${href}" class="nav-link nav-link-inactive">
                                                <div class="nav-icon-container">
                                                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                                <span class="nav-text">${effectiveLabel}</span>
                                            </a>
                                        `;
                                        item.node.remove();
                                        currentGroup.items.push({
                                            node: li,
                                            key: item.key,
                                            label: effectiveLabel,
                                            section: item.section,
                                            order: item.order,
                                            defaultOrder: item.defaultOrder
                                        });
                                        navigation.appendChild(li);
                                    }
                                });

                                // 3. Urutkan item pada tiap section
                                groups.forEach(current => {
                                    if (!current.items.length) {
                                        if (current.title) current.title.hidden = true;
                                        return;
                                    }

                                    const sorted = [...current.items].sort((a, b) =>
                                        (a.order - b.order) || (a.defaultOrder - b.defaultOrder));
                                    const marker = document.createComment('role-menu-order');
                                    const first = current.items[0]?.node?.parentNode ? current.items[0].node : null;
                                    if (first && first.parentNode) {
                                        first.parentNode.insertBefore(marker, first);
                                        sorted.forEach(item => {
                                            if (item.node && item.node.parentNode) {
                                                marker.parentNode.insertBefore(item.node, marker);
                                            }
                                        });
                                        marker.remove();
                                    }

                                    // Sembunyikan dropdown jika semua anaknya hidden
                                    sorted.forEach(item => {
                                        if (item.node && item.node.classList?.contains('submenu-dropdown')) {
                                            const subLinks = Array.from(item.node.querySelectorAll('.submenu-card a.submenu-item'));
                                            if (subLinks.length > 0 && subLinks.every(s => s.hidden || s.style.display === 'none')) {
                                                item.node.hidden = true;
                                            }
                                        }
                                    });

                                    if (current.title) {
                                        const groupCustomSection = current.items.map(i => i.custom_section).find(Boolean);
                                        if (groupCustomSection) {
                                            current.title.textContent = groupCustomSection;
                                        }
                                        current.title.hidden = current.items.every(item => item.node.hidden || item.node.style.display === 'none');
                                    }
                                });
                            } catch (error) {
                                console.warn('Pengaturan menu hierarki tidak dapat diterapkan.', error);
                            } finally {
                                navigation.style.visibility = 'visible';
                            }
                        })();
                    </script>
                    <noscript><style>#role-navigation { visibility: visible !important; }</style></noscript>
                </nav>
            </div>

            <!-- Profile Area (Glassmorphism integrated) -->
            <div class="p-4 mt-auto">
                <div :class="sidebarCollapsed ? 'flex justify-center' : 'flex items-center gap-3 p-3 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 shadow-sm'"
                    class="transition-all duration-300">
                    <div class="w-8 h-8 rounded-lg overflow-hidden flex-shrink-0 shadow-sm">
                        <img class="w-full h-full object-cover"
                            src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=f8fafc&color=475569' }}"
                            alt="{{ Auth::user()->name }}">
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-[11px] font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] text-white/70 font-bold truncate uppercase tracking-wider">
                            {{ session('active_role') }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <div
            class="flex-1 flex flex-col overflow-hidden relative md:my-5 md:mr-5 md:rounded-r-2xl md:shadow-soft bg-white border-y border-r border-gray-100 z-10">

            <header class="flex-shrink-0 bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = true"
                            class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-600 hover:text-red-600 hover:bg-red-50 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Desktop Toggle Button (In Navbar) -->
                        <button @click="toggleSidebar()"
                            class="hidden md:flex items-center justify-center w-10 h-10 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors focus:outline-none">
                            <svg :class="sidebarCollapsed ? 'rotate-180' : ''"
                                class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
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

                        {{-- Kantin Top Bar Notification --}}
                        @if(session('active_role') == 'Kantin')
                        <div x-data="{ count: {{ \App\Models\KantinOrder::where('kantin_id', Auth::id())->where('status', 'pending')->count() }} }" @kantin-orders-updated.window="count = $event.detail.count" class="relative hidden sm:flex items-center">
                            <a href="{{ route('kantin.orders.index') }}" class="p-2 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded-lg transition-colors relative">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span x-show="count > 0" x-transition x-cloak class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full animate-pulse"></span>
                            </a>
                        </div>
                        @endif
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
                                        <div
                                            class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div class="text-left hidden lg:block">
                                            <p
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">
                                                Role Aktif</p>
                                            <p class="text-xs font-bold text-gray-700 leading-none">
                                                {{ session('active_role') }}
                                            </p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
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
                                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pilih
                                                Role</p>
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
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
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
                                    <p
                                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">
                                        Role Aktif</p>
                                    <p class="text-xs font-bold text-gray-700 leading-none">{{ session('active_role') }}</p>
                                </div>
                            @endif

                            {{-- Notifications --}}
                            <div class="relative" x-data="{ notificationOpen: false }">
                                <button @click="notificationOpen = !notificationOpen"
                                    class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all focus:outline-none">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <span class="absolute top-2 right-2 flex h-2 w-2">
                                            <span
                                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
                                        </span>
                                    @endif
                                </button>

                                <div x-show="notificationOpen" @click.outside="notificationOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl py-2 z-50 ring-1 ring-black ring-opacity-5 origin-top-right"
                                    style="display: none;">

                                    <div class="px-4 py-2 border-b border-gray-50 flex items-center justify-between">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Notifikasi</p>
                                        @if(Auth::user()->unreadNotifications->count() > 0)
                                            <form action="{{ route('shared.notifications.mark-all') }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="text-[10px] text-red-600 font-bold hover:underline">Tandai Semua
                                                    Dibaca</button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="max-h-96 overflow-y-auto">
                                        @forelse(Auth::user()->unreadNotifications as $notification)
                                            <a href="{{ route('shared.notifications.read', $notification->id) }}"
                                                class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs text-gray-800 font-medium leading-normal mb-1">
                                                        {{ $notification->data['message'] }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-8 text-center">
                                                <div
                                                    class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                </div>
                                                <p class="text-xs text-gray-500 font-medium">Tidak ada notifikasi baru</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    <div class="px-4 py-2 border-t border-gray-50 text-center">
                                        <a href="{{ route('shared.notifications.index') }}"
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-red-600 transition-colors">Lihat
                                            Semua</a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative" x-data="{ dropdownOpen: false }">
                                <button @click="dropdownOpen = !dropdownOpen"
                                    class="flex items-center justify-center w-10 h-10 rounded-full overflow-hidden bg-gray-200 border-2 border-white shadow-sm cursor-pointer hover:ring-2 hover:ring-red-100 transition-all focus:outline-none">
                                    <img class="w-full h-full object-cover"
                                        src="{{ Auth::user()->avatar ? Storage::url(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff' }}"
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
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                @if($appSetting?->system_announcement_enabled ?? true)
                    @include('shared.navigation-update-modal')
                @endif
                @include('shared.survey-notification-card')

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <footer class="flex-shrink-0 bg-white border-t border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 gap-2">
                        <p>{{ $appSetting?->school_name ?? 'Aplikasi Izin' }} &copy; {{ date('Y') }}</p>
                        <p class="opacity-70">Built with passion for better education. &bull; Versi
                            {{ config('app.version') }}
                        </p>
                    </div>
                </div>
            </footer>

        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => { });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')
    @stack('scripts')
</body>

</html>
