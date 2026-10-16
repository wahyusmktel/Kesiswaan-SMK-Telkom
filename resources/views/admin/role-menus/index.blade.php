<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-red-600">
                    <span>Pusat Sistem & Master Data</span>
                    <span>/</span>
                    <span>Tata Kelola Navigasi</span>
                </div>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Pengaturan Menu Sidebar
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6" x-data="roleMenuManager({{ Illuminate\Support\Js::from($menus) }})" @keydown.escape.window="if (isMaximized) toggleMaximize()">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">✓</span>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <section :class="{ 'overflow-visible': isMaximized, 'overflow-hidden': !isMaximized }" class="rounded-3xl border border-slate-200 bg-white shadow-sm transition-all">
            {{-- Header & Role Switcher Bar --}}
            <div class="border-b border-slate-100 bg-slate-50/50 p-6">
                <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                            <label for="role-selector" class="block text-xs font-black uppercase tracking-wider text-slate-500">
                                Pilih Role Pengguna yang Akan Diatur:
                            </label>
                            @if($selectedRole)
                                <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1 shadow-sm">
                                    <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-slate-700">Role Aktif: <span class="text-red-600">{{ $selectedRole->name }}</span></span>
                                </div>
                            @endif
                        </div>
                        <select id="role-selector"
                            class="w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm transition focus:border-red-500 focus:ring-red-500"
                            onchange="window.location.href = '{{ route('super-admin.role-menus.index') }}?role=' + this.value">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected($selectedRole?->is($role))>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Quick Pills untuk Role Populer --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] font-bold text-slate-400 mr-1">Cepat:</span>
                            @foreach($roles->take(8) as $quickRole)
                                <a href="{{ route('super-admin.role-menus.index', ['role' => $quickRole->id]) }}"
                                   class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold transition {{ $selectedRole?->is($quickRole) ? 'bg-red-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                                    {{ $quickRole->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Counter Metrics --}}
                    <div class="grid grid-cols-4 gap-2.5 text-center">
                        <div class="rounded-2xl border border-slate-200 bg-white px-3 py-3 shadow-sm">
                            <div class="text-xl font-black text-slate-800" x-text="items.length">0</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</div>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-3 py-3 shadow-sm">
                            <div class="text-xl font-black text-emerald-700" x-text="visibleCount">0</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Tampil</div>
                        </div>
                        <div class="rounded-2xl border border-slate-300 bg-slate-100 px-3 py-3 shadow-sm">
                            <div class="text-xl font-black text-slate-600" x-text="hiddenCount">0</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sembunyi</div>
                        </div>
                        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-3 py-3 shadow-sm">
                            <div class="text-xl font-black text-indigo-700" x-text="submenuCount">0</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Sub-menu</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($selectedRole)
                <form id="role-menu-form" method="POST" action="{{ route('super-admin.role-menus.update', $selectedRole) }}">
                    @csrf
                    @method('PUT')

                    {{-- Hidden Form Inputs --}}
                    <template x-for="(item, index) in items" :key="item.key">
                        <div>
                            <input type="hidden" :name="`menus[${index}][key]`" :value="item.key">
                            <input type="hidden" :name="`menus[${index}][parent_key]`" :value="item.parent_key || ''">
                            <input type="hidden" :name="`menus[${index}][custom_label]`" :value="item.custom_label || ''">
                            <input type="hidden" :name="`menus[${index}][custom_section]`" :value="item.custom_section || ''">
                            <input type="hidden" :name="`menus[${index}][visible]`" :value="item.visible ? 1 : 0">
                            <input type="hidden" :name="`menus[${index}][order]`" :value="index">
                        </div>
                    </template>

                    <div class="grid min-h-[600px] lg:grid-cols-[minmax(0,0.85fr)_minmax(460px,1.15fr)]">
                        {{-- Sisi Kiri: Petunjuk & Kontrol Filter --}}
                        <aside class="border-b border-slate-100 bg-slate-50/70 p-6 lg:border-b-0 lg:border-r space-y-6">
                            <div>
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-red-700">
                                    Nesting & Hierarki Menu
                                </span>
                                <h3 class="mt-2 text-lg font-extrabold text-slate-800">
                                    Sidebar {{ $selectedRole->name }}
                                </h3>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                    Atur hierarki menu seperti di WordPress. Klik tombol <span class="font-bold text-indigo-600">↳ Sub-menu</span> untuk mengelompokkan menu menjadi dropdown anak, atau <span class="font-bold text-slate-700">↰ Menu Utama</span> untuk menjadikannya mandiri.
                                </p>
                            </div>

                            {{-- Search Filter --}}
                            <div>
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Cari Menu</label>
                                <div class="relative mt-2">
                                    <svg class="absolute left-3.5 top-3.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                    </svg>
                                    <input x-model="search" type="search" placeholder="Cari nama menu atau grup..."
                                        class="w-full rounded-xl border-slate-200 bg-white py-2.5 pl-10 pr-4 text-xs font-medium focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                            </div>

                            {{-- Bulk Action Buttons --}}
                            <div class="grid grid-cols-2 gap-2.5">
                                <button type="button" @click="setAll(true)"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Tampilkan Semua</span>
                                </button>
                                <button type="button" @click="setAll(false)"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-300 transition shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                    <span>Sembunyikan Semua</span>
                                </button>
                            </div>

                            {{-- Petunjuk Pengaturan Ala WordPress --}}
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2.5 text-xs text-slate-600">
                                <p class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                    Panduan Sub-menu Ala WordPress:
                                </p>
                                <ul class="list-disc list-inside space-y-1.5 text-slate-500 text-[11px] leading-relaxed">
                                    <li><strong>Menu Utama:</strong> Berada di rata kiri level 1.</li>
                                    <li><strong>Jadikan Sub-menu:</strong> Klik tombol <span class="font-bold text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded">↳ Sub-menu</span> untuk mengelompokkannya menjorok ke kanan di bawah menu induk di atasnya.</li>
                                    <li><strong>Jadikan Menu Utama:</strong> Klik tombol <span class="font-bold text-slate-700 bg-slate-100 px-1 py-0.5 rounded">↰ Menu Utama</span> untuk melepasnya kembali menjadi menu mandiri.</li>
                                    <li><strong>Geser Posisi:</strong> Gunakan panah <span class="font-bold text-slate-700">↑</span> atau <span class="font-bold text-slate-700">↓</span> atau seret handle grip kartu.</li>
                                    <li><strong>Sembunyikan:</strong> Tekan switch toggle. Menu yang disembunyikan otomatis berpenampilan <span class="font-bold text-slate-600 bg-slate-200 px-1 py-0.5 rounded">gray muda</span>.</li>
                                </ul>
                            </div>
                        </aside>

                        {{-- Sisi Kanan: Daftar Kartu Menu Interaktif dengan Indentasi Sub-menu --}}
                        <div :class="isMaximized 
                            ? 'fixed inset-0 z-50 flex flex-col bg-slate-100 p-4 sm:p-6 lg:p-8 overflow-hidden shadow-2xl animate-in fade-in duration-200' 
                            : 'p-6 flex flex-col'">
                            
                            {{-- Header Kotak Struktur Hierarki Menu --}}
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-3 shrink-0"
                                 :class="{ 'border-b border-slate-200/80 pb-4 bg-white/95 backdrop-blur rounded-2xl px-5 py-3.5 shadow-sm max-w-5xl mx-auto w-full': isMaximized }">
                                <div class="flex items-center gap-3">
                                    <template x-if="isMaximized">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-600 to-rose-600 text-white shadow-md shadow-red-500/30 shrink-0">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
                                        </div>
                                    </template>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-xs font-black uppercase tracking-wider text-slate-400">Struktur Hierarki Menu</div>
                                            <template x-if="isMaximized">
                                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-black text-red-700 uppercase tracking-wider">
                                                    Mode Fokus Maksimal
                                                </span>
                                            </template>
                                        </div>
                                        <p class="mt-0.5 text-xs text-slate-500">
                                            Tampilan urutan dan pengelompokan menu untuk role <span class="font-bold text-slate-700">{{ $selectedRole->name }}</span>.
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-2.5">
                                    {{-- Search Box di Mode Maximize --}}
                                    <template x-if="isMaximized">
                                        <div class="relative w-56 sm:w-72">
                                            <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                            </svg>
                                            <input x-model="search" type="search" placeholder="Cari nama menu..."
                                                class="w-full rounded-xl border-slate-200 bg-white py-2 pl-9 pr-7 text-xs font-medium focus:border-red-500 focus:ring-red-500 shadow-sm">
                                            <button type="button" x-show="search" @click="search = ''" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</button>
                                        </div>
                                    </template>

                                    {{-- Quick Actions di Mode Maximize --}}
                                    <template x-if="isMaximized">
                                        <div class="hidden sm:flex items-center gap-1.5">
                                            <button type="button" @click="setAll(true)"
                                                class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100 shadow-sm transition">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Tampil Semua</span>
                                            </button>
                                            <button type="button" @click="setAll(false)"
                                                class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-300 shadow-sm transition">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                                <span>Sembunyi Semua</span>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Save Button di Header Mode Maximize --}}
                                    <template x-if="isMaximized">
                                        <button type="submit" form="role-menu-form"
                                            class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-4 py-2 text-xs font-black text-white shadow-md shadow-red-500/25 hover:from-red-700 hover:to-rose-700 transition">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Simpan</span>
                                        </button>
                                    </template>

                                    <div x-show="!isMaximized" class="hidden sm:flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-600 shadow-sm">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Mode Hirarki WordPress</span>
                                    </div>

                                    {{-- Tombol Maximize / Minimize --}}
                                    <button type="button" @click="toggleMaximize()"
                                        :class="isMaximized 
                                            ? 'bg-slate-900 text-white border-slate-800 hover:bg-slate-800 shadow-md ring-2 ring-slate-900/10' 
                                            : 'bg-white text-slate-700 border-slate-200 hover:border-red-300 hover:bg-red-50 hover:text-red-700 shadow-sm'"
                                        class="inline-flex items-center gap-1.5 rounded-xl border px-3.5 py-1.5 text-xs font-extrabold transition active:scale-95"
                                        :title="isMaximized ? 'Kembalikan ke ukuran normal (Esc)' : 'Perbesar kotak ini ke mode layar penuh untuk fokus mengatur hierarki menu (Maximize)'">
                                        <template x-if="!isMaximized">
                                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                            </svg>
                                        </template>
                                        <template x-if="isMaximized">
                                            <svg class="h-3.5 w-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 4H5a1 1 0 0 0-1 1v4m5-5-5 5m0 0h4M15 4h4a1 1 0 0 1 1 1v4m-5-5 5 5m0 0h-4M9 20H5a1 1 0 0 1-1-1v-4m5 5-5-5m0 0h4m10 5h4a1 1 0 0 0 1-1v-4m-5 5 5-5m0 0h-4" />
                                            </svg>
                                        </template>
                                        <span x-text="isMaximized ? 'Kecilkan (Esc)' : 'Maximize'"></span>
                                    </button>
                                </div>
                            </div>

                            <div :class="isMaximized 
                                ? 'flex-1 overflow-y-auto pr-3 space-y-6 sidebar-scroll w-full max-w-5xl mx-auto py-2' 
                                : 'max-h-[680px] space-y-6 overflow-y-auto pr-2 sidebar-scroll'">
                                <template x-for="section in sections" :key="section">
                                    <section x-show="sectionItems(section).some(item => matchesSearch(item))" class="space-y-2.5">
                                        {{-- Header Section / Kelompok Menu Dinamis --}}
                                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-b border-slate-100 pb-1.5">
                                            <div class="flex items-center gap-2">
                                                {{-- Tampilan Normal / Non-Edit Section --}}
                                                <div x-show="editingSection !== section" class="flex items-center gap-2 group/section">
                                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-black uppercase tracking-wider text-slate-700 border border-slate-200 shadow-sm" x-text="section"></span>
                                                    <button type="button" @click="startEditSection(section)"
                                                        class="opacity-70 group-hover/section:opacity-100 p-1 text-slate-400 hover:text-red-600 hover:bg-slate-200/60 rounded-lg transition"
                                                        title="Ubah Nama Kelompok / Grup Ini">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    </button>
                                                </div>

                                                {{-- Mode Edit Nama Section --}}
                                                <div x-show="editingSection === section" class="flex items-center gap-1.5" @click.outside="cancelEditSection()">
                                                    <input type="text" x-model="tempSectionName" @keydown.enter.prevent="saveSection(section)" @keydown.escape.prevent="cancelEditSection()"
                                                        class="rounded-lg border border-red-400 bg-white px-2.5 py-1 text-xs font-bold text-slate-800 shadow-sm focus:border-red-600 focus:ring-1 focus:ring-red-600 w-48 sm:w-64"
                                                        placeholder="Nama kelompok baru...">
                                                    <button type="button" @click="saveSection(section)" class="p-1 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-bold shadow-sm" title="Simpan Nama Kelompok">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                    <button type="button" @click="cancelEditSection()" class="p-1 rounded-lg bg-slate-200 text-slate-600 hover:bg-slate-300 text-xs font-bold" title="Batal">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-slate-400" x-text="sectionItems(section).filter(i => i.visible).length + '/' + sectionItems(section).length + ' menu'"></span>
                                            </div>
                                        </div>

                                        {{-- Container Kartu Menu dengan Dukungan Indentasi --}}
                                        <div class="space-y-2">
                                            <template x-for="item in sectionItems(section)" :key="item.key">
                                                <div x-show="matchesSearch(item)" class="relative transition-all duration-200"
                                                     :class="item.parent_key ? 'pl-7 sm:pl-10' : 'pl-0'">

                                                    {{-- Garis Cabang Pohon / Tree Indicator untuk Sub-menu --}}
                                                    <template x-if="item.parent_key">
                                                        <div class="absolute left-2 sm:left-4 top-1/2 -mt-4 h-6 w-4 sm:w-5 border-b-2 border-l-2 border-indigo-300 rounded-bl-lg pointer-events-none"></div>
                                                    </template>

                                                    <article
                                                        draggable="true"
                                                        @dragstart="draggedKey = item.key; $event.dataTransfer.effectAllowed = 'move'"
                                                        @dragend="draggedKey = null; dragOverKey = null"
                                                        @dragover.prevent="dragOverKey = item.key"
                                                        @dragleave="if (dragOverKey === item.key) dragOverKey = null"
                                                        @drop.prevent="dropBefore(item)"
                                                        :class="{
                                                            'border-slate-200 bg-white shadow-sm hover:border-red-300 hover:shadow': item.visible && !item.parent_key,
                                                            'border-indigo-200 bg-indigo-50/40 shadow-sm hover:border-indigo-300': item.visible && item.parent_key,
                                                            'border-slate-200/90 bg-slate-100 text-slate-400': !item.visible,
                                                            'ring-2 ring-red-500 border-red-500 bg-red-50/70 scale-[1.01] shadow-md': dragOverKey === item.key && draggedKey !== item.key
                                                        }"
                                                        class="group flex flex-wrap sm:flex-nowrap items-center gap-3 rounded-2xl border p-3 sm:p-3.5 transition-all duration-200">
                                                        
                                                        {{-- Kontrol Geser: Tombol Panah & Handle --}}
                                                        <div class="flex items-center gap-1.5 shrink-0">
                                                            <div class="flex flex-col items-center gap-0.5">
                                                                <button type="button" @click.stop="moveUp(item)" title="Geser ke atas"
                                                                    class="p-1 rounded text-slate-400 hover:text-red-600 hover:bg-slate-200/60 transition active:scale-95">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                </button>
                                                                <button type="button" @click.stop="moveDown(item)" title="Geser ke bawah"
                                                                    class="p-1 rounded text-slate-400 hover:text-red-600 hover:bg-slate-200/60 transition active:scale-95">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                </button>
                                                            </div>
                                                            <div class="cursor-grab active:cursor-grabbing rounded-xl p-2 transition"
                                                                 :class="item.visible ? 'bg-slate-100 text-slate-500 group-hover:bg-red-50 group-hover:text-red-600' : 'bg-slate-200 text-slate-400'"
                                                                 title="Seret kartu untuk mengubah urutan">
                                                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M7 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3ZM16 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm-1.5 7.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm1.5 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        {{-- Icon Menu --}}
                                                        <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-xl transition-all shrink-0"
                                                             :class="!item.visible 
                                                                 ? 'bg-slate-200 text-slate-400 border border-slate-300/60'
                                                                 : (item.parent_key 
                                                                     ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm shadow-indigo-400/20'
                                                                     : 'bg-gradient-to-br from-red-600 to-rose-600 text-white shadow-sm shadow-red-500/20')">
                                                            <template x-if="item.parent_key">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                            </template>
                                                            <template x-if="!item.parent_key">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10"/></svg>
                                                            </template>
                                                        </div>

                                                        {{-- Detail Menu & Indikator Level --}}
                                                        <div class="min-w-0 flex-1">
                                                            {{-- Tampilan Normal / Non-Edit Label --}}
                                                            <div x-show="editingItemKey !== item.key" class="flex items-center gap-2 group/title">
                                                                <div class="truncate text-sm transition-colors"
                                                                     :class="item.visible ? 'font-extrabold text-slate-800' : 'font-semibold text-slate-500 italic'"
                                                                     x-text="item.custom_label || item.label">
                                                                </div>
                                                                <template x-if="item.custom_label && item.custom_label !== item.label">
                                                                    <span class="text-[9px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-1.5 py-0.5 rounded" :title="'Nama asli bawaan: ' + item.label">
                                                                        Kustom
                                                                    </span>
                                                                </template>
                                                                <button type="button" @click="startEditItem(item)"
                                                                    class="opacity-60 group-hover/title:opacity-100 p-1 text-slate-400 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition"
                                                                    title="Kustomisasi Nama Menu / Judul Parent">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                </button>
                                                                <span x-show="item.locked" class="text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                                                    Wajib
                                                                </span>
                                                            </div>

                                                            {{-- Mode Edit Label Menu --}}
                                                            <div x-show="editingItemKey === item.key" class="flex items-center gap-1.5 my-0.5" @click.outside="cancelEditItem()">
                                                                <input type="text" x-model="tempItemLabel" @keydown.enter.prevent="saveItemLabel(item)" @keydown.escape.prevent="cancelEditItem()"
                                                                    class="rounded-lg border border-indigo-400 bg-white px-2.5 py-1 text-xs font-bold text-slate-800 shadow-sm focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 w-44 sm:w-60"
                                                                    :placeholder="item.label">
                                                                <button type="button" @click="saveItemLabel(item)" class="p-1 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-xs font-bold shadow-sm" title="Terapkan Nama Kustom">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                                <template x-if="item.custom_label">
                                                                    <button type="button" @click="resetItemLabel(item)" class="p-1 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs font-bold" title="Kembalikan ke nama asli bawaan">
                                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                                    </button>
                                                                </template>
                                                                <button type="button" @click="cancelEditItem()" class="p-1 rounded-lg bg-slate-200 text-slate-600 hover:bg-slate-300 text-xs font-bold" title="Batal">
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>

                                                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                                {{-- Badge Status Tampil / Sembunyi --}}
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                                      :class="item.visible 
                                                                          ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' 
                                                                          : 'bg-slate-200 text-slate-500 border border-slate-300'">
                                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                                          :class="item.visible ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400'"></span>
                                                                    <span x-text="item.visible ? 'Ditampilkan' : 'Disembunyikan (Gray Muda)'"></span>
                                                                </span>

                                                                {{-- Badge Hierarki (Menu Utama vs Sub-menu vs Parent Dekorasi) --}}
                                                                <template x-if="item.parent_key">
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                                        <span>↳ Sub-menu dari:</span>
                                                                        <strong class="text-indigo-900" x-text="getParentLabel(item.parent_key)"></strong>
                                                                    </span>
                                                                </template>
                                                                <template x-if="!item.parent_key && hasChildren(item)">
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200" title="Menu ini berfungsi sebagai induk dropdown (dekorasi) yang membuka sub-menu di bawahnya">
                                                                        <span>↳ Induk Dropdown (Dekorasi)</span>
                                                                    </span>
                                                                </template>
                                                                <template x-if="!item.parent_key && !hasChildren(item)">
                                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                                        Menu Utama
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        {{-- Aksi Cepat Ala WordPress: Indent (Jadikan Sub-menu) & Outdent (Menu Utama) --}}
                                                        <div class="flex items-center gap-2 shrink-0 ml-auto sm:ml-0">
                                                            {{-- Tombol Jadikan Sub-menu --}}
                                                            <template x-if="!item.parent_key">
                                                                <button type="button" @click="indent(item)"
                                                                    :disabled="!canIndent(item)"
                                                                    :title="canIndent(item) ? 'Kelompokkan menjadi sub-menu di bawah menu sebelumnya' : 'Harus ada menu di atasnya untuk dijadikan sub-menu'"
                                                                    class="inline-flex items-center gap-1 rounded-xl border border-indigo-200 bg-white px-2.5 py-1.5 text-[11px] font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-50 disabled:cursor-not-allowed disabled:opacity-40">
                                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                                    <span>↳ Sub-menu</span>
                                                                </button>
                                                            </template>

                                                            {{-- Tombol Kembalikan ke Menu Utama --}}
                                                            <template x-if="item.parent_key">
                                                                <button type="button" @click="outdent(item)"
                                                                    title="Keluarkan dari sub-menu dan jadikan menu utama mandiri"
                                                                    class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-[11px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-100">
                                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                                                                    <span>↰ Menu Utama</span>
                                                                </button>
                                                            </template>

                                                            {{-- Tombol Toggle Tampilkan / Sembunyikan --}}
                                                            <button type="button" @click="toggle(item)" :disabled="item.locked"
                                                                :title="item.locked ? 'Menu ini wajib selalu tampil' : (item.visible ? 'Klik untuk sembunyikan menu' : 'Klik untuk tampilkan menu')"
                                                                :class="item.visible ? 'bg-emerald-500' : 'bg-slate-300'"
                                                                class="relative h-7 w-12 rounded-full transition-colors duration-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-40 shadow-inner">
                                                                <span :class="item.visible ? 'translate-x-5' : 'translate-x-1'"
                                                                      class="absolute left-0 top-1 h-5 w-5 rounded-full bg-white shadow-md transition-transform duration-200"></span>
                                                            </button>
                                                        </div>
                                                    </article>
                                                </div>
                                            </template>
                                        </div>
                                    </section>
                                </template>

                                {{-- State Kosong Pencarian --}}
                                <div x-show="!items.some(item => matchesSearch(item))" class="rounded-2xl border border-dashed border-slate-200 py-12 text-center text-xs text-slate-500">
                                    <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Tidak ada menu yang sesuai dengan kata kunci pencarian.
                                </div>
                            </div>

                            {{-- Floating Bottom Bar di Mode Maximize --}}
                            <template x-if="isMaximized">
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/90 bg-white/95 backdrop-blur px-5 py-3 shadow-md shrink-0 max-w-5xl mx-auto w-full">
                                    <div class="flex items-center gap-2 text-xs text-slate-600">
                                        <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Mode Fokus Aktif &bull; Tekan tombol <kbd class="rounded bg-slate-100 border border-slate-300 px-1.5 py-0.5 font-mono text-[10px] text-slate-700 font-bold">Esc</kbd> untuk keluar.</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <button type="button" @click="toggleMaximize()"
                                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition shadow-sm">
                                            Tutup Mode Fokus
                                        </button>
                                        <button type="submit" form="role-menu-form"
                                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-5 py-2 text-xs font-black text-white shadow-lg shadow-red-500/25 hover:from-red-700 hover:to-rose-700 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Simpan Pengaturan Menu</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Footer Action Bar --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <button type="submit" form="reset-role-menu-form"
                            onclick="return confirm('Kembalikan semua menu role ini ke tampilan, urutan, dan hierarki bawaan sistem?')"
                            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 shadow-sm hover:bg-slate-100 hover:text-slate-800 transition">
                            ↺ Kembalikan Bawaan
                        </button>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-slate-500" x-show="visibleCount < items.length">
                                <span class="font-bold text-slate-700" x-text="hiddenCount"></span> menu disembunyikan
                            </span>
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-6 py-2.5 text-xs font-black text-white shadow-lg shadow-red-500/25 hover:from-red-700 hover:to-rose-700 hover:shadow-red-500/40 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Pengaturan Menu</span>
                            </button>
                        </div>
                    </div>
                </form>

                <form id="reset-role-menu-form" method="POST" action="{{ route('super-admin.role-menus.reset', $selectedRole) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @else
                <div class="p-12 text-center text-slate-500">Belum ada role yang dapat diatur.</div>
            @endif
        </section>
    </div>

    @push('scripts')
        <script>
            window.roleMenuManager = initialItems => ({
                items: initialItems,
                search: '',
                draggedKey: null,
                dragOverKey: null,
                isMaximized: false,
                editingSection: null,
                tempSectionName: '',
                editingItemKey: null,
                tempItemLabel: '',

                startEditSection(section) {
                    this.editingSection = section;
                    this.tempSectionName = section;
                },

                cancelEditSection() {
                    this.editingSection = null;
                    this.tempSectionName = '';
                },

                saveSection(oldSection) {
                    const newName = this.tempSectionName.trim();
                    if (newName && newName !== oldSection) {
                        this.items.forEach(item => {
                            if (item.section === oldSection) {
                                item.section = newName;
                                item.custom_section = newName;
                            }
                        });
                    }
                    this.editingSection = null;
                    this.tempSectionName = '';
                },

                startEditItem(item) {
                    this.editingItemKey = item.key;
                    this.tempItemLabel = item.custom_label || item.label;
                },

                cancelEditItem() {
                    this.editingItemKey = null;
                    this.tempItemLabel = '';
                },

                saveItemLabel(item) {
                    const newLabel = this.tempItemLabel.trim();
                    if (newLabel && newLabel !== item.label) {
                        item.custom_label = newLabel;
                    } else if (newLabel === item.label) {
                        item.custom_label = null;
                    }
                    this.editingItemKey = null;
                    this.tempItemLabel = '';
                },

                resetItemLabel(item) {
                    item.custom_label = null;
                    this.editingItemKey = null;
                    this.tempItemLabel = '';
                },

                hasChildren(item) {
                    return this.items.some(i => i.parent_key === item.key);
                },

                toggleMaximize() {
                    this.isMaximized = !this.isMaximized;
                    if (this.isMaximized) {
                        document.body.classList.add('overflow-hidden');
                    } else {
                        document.body.classList.remove('overflow-hidden');
                    }
                },

                get visibleCount() {
                    return this.items.filter(item => item.visible).length;
                },

                get hiddenCount() {
                    return this.items.length - this.visibleCount;
                },

                get submenuCount() {
                    return this.items.filter(item => !!item.parent_key).length;
                },

                get sections() {
                    return [...new Set(this.items.map(item => item.section))];
                },

                sectionItems(section) {
                    return this.items.filter(item => item.section === section);
                },

                matchesSearch(item) {
                    const query = this.search.trim().toLowerCase();
                    if (!query) return true;
                    const label = (item.custom_label || item.label || '').toLowerCase();
                    const orig = (item.label || '').toLowerCase();
                    const section = (item.section || '').toLowerCase();
                    return label.includes(query) || orig.includes(query) || section.includes(query);
                },

                getParentLabel(parentKey) {
                    if (!parentKey) return '';
                    const parent = this.items.find(i => i.key === parentKey);
                    return parent ? (parent.custom_label || parent.label) : parentKey;
                },

                toggle(item) {
                    if (!item.locked) item.visible = !item.visible;
                },

                setAll(visible) {
                    this.items.forEach(item => {
                        if (!item.locked) item.visible = visible;
                    });
                },

                canIndent(item) {
                    const sectionList = this.sectionItems(item.section);
                    const idx = sectionList.findIndex(i => i.key === item.key);
                    return idx > 0;
                },

                // Jadikan Sub-menu dari item di atasnya (WordPress style indent)
                indent(item) {
                    const sectionList = this.sectionItems(item.section);
                    const idx = sectionList.findIndex(i => i.key === item.key);
                    if (idx <= 0) return;

                    // Ambil item tepat di atasnya
                    const prevItem = sectionList[idx - 1];
                    // Jika item di atasnya punya parent, gabung ke parent yang sama; jika tidak, jadikan prevItem sebagai parent
                    item.parent_key = prevItem.parent_key || prevItem.key;
                },

                // Jadikan Menu Utama mandiri (WordPress style outdent)
                outdent(item) {
                    item.parent_key = null;
                },

                moveUp(item) {
                    const list = this.sectionItems(item.section);
                    const idx = list.findIndex(i => i.key === item.key);
                    if (idx <= 0) return;
                    const prevItem = list[idx - 1];

                    const sourceIndex = this.items.findIndex(i => i.key === item.key);
                    const targetIndex = this.items.findIndex(i => i.key === prevItem.key);

                    const [source] = this.items.splice(sourceIndex, 1);
                    this.items.splice(targetIndex, 0, source);
                },

                moveDown(item) {
                    const list = this.sectionItems(item.section);
                    const idx = list.findIndex(i => i.key === item.key);
                    if (idx < 0 || idx >= list.length - 1) return;
                    const nextItem = list[idx + 1];

                    const sourceIndex = this.items.findIndex(i => i.key === item.key);
                    const targetIndex = this.items.findIndex(i => i.key === nextItem.key);

                    const [source] = this.items.splice(sourceIndex, 1);
                    this.items.splice(targetIndex, 0, source);
                },

                dropBefore(target) {
                    if (!this.draggedKey || this.draggedKey === target.key) return;

                    const sourceIndex = this.items.findIndex(item => item.key === this.draggedKey);
                    const targetIndex = this.items.findIndex(item => item.key === target.key);
                    if (sourceIndex < 0 || targetIndex < 0) return;

                    if (this.items[sourceIndex].section !== target.section) {
                        this.items[sourceIndex].section = target.section;
                    }

                    const [source] = this.items.splice(sourceIndex, 1);
                    const refreshedTarget = this.items.findIndex(item => item.key === target.key);
                    this.items.splice(refreshedTarget, 0, source);
                    this.draggedKey = null;
                    this.dragOverKey = null;
                },
            });
        </script>
    @endpush
</x-app-layout>
