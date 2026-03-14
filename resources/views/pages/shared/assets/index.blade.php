<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-lg text-gray-800 leading-tight">Inventaris Aset</h2>
            <p class="text-xs text-gray-400 mt-0.5">Data aset dari Sistem Manajemen Aset terintegrasi</p>
        </div>
    </x-slot>

    <div x-data="assetPage()" x-init="init()" class="space-y-6">

        {{-- Error Alert --}}
        @if($error)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex items-start gap-4">
            <div class="w-10 h-10 flex-shrink-0 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-red-800 text-sm">Koneksi Gagal</h4>
                <p class="text-red-600 text-xs mt-1">{{ $error }}</p>
                <p class="text-red-500 text-xs mt-2 font-medium">Pastikan Aplikasi Manajemen Aset sedang berjalan di URL yang dikonfigurasi.</p>
            </div>
        </div>
        @endif

        @if(!$error && $assets)

        {{-- Stats Cards --}}
        @if($stats)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $statItems = [
                    ['label' => 'Total Aset', 'value' => $stats['total_assets'], 'color' => 'red', 'bg' => 'bg-red-50', 'icon_color' => 'text-red-600',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'],
                    ['label' => 'Tersedia', 'value' => $stats['total_aktif'], 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'icon_color' => 'text-emerald-600',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'],
                    ['label' => 'Dipinjam/Dipakai', 'value' => $stats['total_dipinjam'], 'color' => 'blue', 'bg' => 'bg-blue-50', 'icon_color' => 'text-blue-600',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'],
                    ['label' => 'Rusak', 'value' => $stats['total_rusak'], 'color' => 'amber', 'bg' => 'bg-amber-50', 'icon_color' => 'text-amber-600',
                     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'],
                ];
            @endphp
            @foreach($statItems as $s)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow group">
                <div class="w-12 h-12 {{ $s['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 {{ $s['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $s['icon'] !!}
                    </svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">{{ $s['label'] }}</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($s['value']) }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <form method="GET" action="{{ route('inventaris-aset.index') }}" class="flex flex-col lg:flex-row gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Cari Aset</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama atau kode aset..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="w-full lg:w-48">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Kategori</label>
                    <select name="category_id" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat['id'] }}" {{ request('category_id') == $cat['id'] ? 'selected' : '' }}>
                                {{ $cat['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="w-full lg:w-44">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                    <select name="status" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all">
                        <option value="">Semua Status</option>
                        @foreach(['Tersedia', 'Dipinjam', 'Digunakan', 'Rusak'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="w-full lg:w-36">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Tahun</label>
                    <select name="purchase_year" class="w-full py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ request('purchase_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit & Reset --}}
                <div class="flex gap-2 flex-shrink-0">
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-red-500/20 hover:-translate-y-0.5">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'status', 'purchase_year']))
                    <a href="{{ route('inventaris-aset.index') }}"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-all">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Table Header Info --}}
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Daftar Inventaris Aset</h3>
                    @if($assets->total() > 0)
                    <p class="text-xs text-gray-400 mt-0.5">
                        Menampilkan {{ $assets->firstItem() }} – {{ $assets->lastItem() }} dari {{ $assets->total() }} aset
                    </p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        Live Data
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode & Nama Aset</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">Kategori</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest hidden lg:table-cell">Lokasi</th>
                            <th class="px-4 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($assets as $asset)
                        @php
                            $statusMap = [
                                'Tersedia'  => 'bg-emerald-100 text-emerald-700',
                                'Dipinjam'  => 'bg-blue-100 text-blue-700',
                                'Digunakan' => 'bg-blue-100 text-blue-700',
                                'Rusak'     => 'bg-red-100 text-red-700',
                            ];
                            $cls = $statusMap[$asset->current_status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div>
                                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest">{{ $asset->asset_code_ypt ?? '-' }}</span>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5 group-hover:text-red-600 transition-colors">
                                        {{ $asset->name }}
                                    </p>
                                    @if($asset->created_at)
                                    <span class="text-[10px] text-gray-400 font-medium">Tahun {{ $asset->created_at->format('Y') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <span class="text-xs text-gray-600 font-medium">{{ $asset->category ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 hidden lg:table-cell">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span class="text-xs text-gray-600">{{ $asset->building ?? '-' }}</span>
                                    </div>
                                    @if($asset->room)
                                    <div class="flex items-center gap-1.5 ml-5">
                                        <span class="text-[10px] text-gray-400">{{ $asset->room }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $cls }}">
                                    {{ $asset->current_status ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('inventaris-aset.show', $asset->asset_id) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold rounded-xl transition-all group/btn">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">Tidak ada data aset ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter pencarian Anda</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($assets->hasPages())
                <div class="px-6 py-4 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
                    <p class="text-xs text-gray-500">
                        Halaman <span class="font-bold text-gray-700">{{ $assets->currentPage() }}</span>
                        dari <span class="font-bold text-gray-700">{{ $assets->lastPage() }}</span>
                    </p>
                    <div class="flex justify-end mt-4">
                        {{ $assets->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>

        @endif {{-- end if !$error --}}

    </div>

    @push('scripts')
    <script>
        function assetPage() {
            return {
                init() {}
            };
        }
    </script>
    @endpush

    @push('styles')
    <style>
        .group:hover .group-hover\:text-red-600 { color: #dc2626; }
    </style>
    @endpush
</x-app-layout>
