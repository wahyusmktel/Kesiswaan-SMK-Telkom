<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-lg text-gray-800 leading-tight">Riwayat Peminjaman Aset</h2>
            <p class="text-xs text-gray-400 mt-0.5">Status permintaan peminjaman aset Anda</p>
        </div>
    </x-slot>

    <div class="space-y-5">

        {{-- Flash Message --}}
        @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="flex items-center gap-3 px-5 py-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Error --}}
        @if($error)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-semibold text-red-700">{{ $error }}</p>
        </div>
        @endif

        {{-- Filter tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('inventaris-aset.borrow-history') }}"
                class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !request('status') ? 'bg-red-600 text-white shadow-md shadow-red-500/20' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                Semua
            </a>
            @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'returned' => 'Dikembalikan'] as $key => $label)
            <a href="{{ route('inventaris-aset.borrow-history', ['status' => $key]) }}"
                class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ request('status') == $key ? 'bg-red-600 text-white shadow-md shadow-red-500/20' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        {{-- List --}}
        @if($requests && !empty($requests['data']))
        <div class="space-y-4">
            @foreach($requests['data'] as $req)
            @php
                $statusConfig = [
                    'pending'  => ['bg' => 'bg-amber-50',   'border' => 'border-amber-200',  'badge_bg' => 'bg-amber-100',   'badge_text' => 'text-amber-700',   'dot' => 'bg-amber-500 animate-pulse', 'icon_bg' => 'bg-amber-100',   'icon_text' => 'text-amber-600',   'label' => 'Menunggu Persetujuan'],
                    'approved' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200','badge_bg' => 'bg-emerald-100', 'badge_text' => 'text-emerald-700', 'dot' => 'bg-emerald-500',             'icon_bg' => 'bg-emerald-100', 'icon_text' => 'text-emerald-600', 'label' => 'Disetujui'],
                    'rejected' => ['bg' => 'bg-red-50',     'border' => 'border-red-200',    'badge_bg' => 'bg-red-100',     'badge_text' => 'text-red-700',     'dot' => 'bg-red-500',                 'icon_bg' => 'bg-red-100',     'icon_text' => 'text-red-600',     'label' => 'Ditolak'],
                    'returned' => ['bg' => 'bg-gray-50',    'border' => 'border-gray-200',   'badge_bg' => 'bg-gray-100',    'badge_text' => 'text-gray-600',    'dot' => 'bg-gray-400',                'icon_bg' => 'bg-gray-100',    'icon_text' => 'text-gray-500',    'label' => 'Dikembalikan'],
                ];
                $sc = $statusConfig[$req['status']] ?? $statusConfig['pending'];
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Status bar --}}
                <div class="px-5 py-3 {{ $sc['bg'] }} {{ $sc['border'] }} border-b flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest {{ $sc['badge_text'] }}">
                            <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                            {{ $sc['label'] }}
                        </span>
                    </div>
                    <span class="text-[10px] text-gray-400 font-medium">
                        {{ \Carbon\Carbon::parse($req['created_at'])->timezone('Asia/Jakarta')->diffForHumans() }}
                    </span>
                </div>

                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 {{ $sc['icon_bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 {{ $sc['icon_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black text-red-600 uppercase tracking-widest">{{ $req['asset']['asset_code_ypt'] ?? '-' }}</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $req['asset']['name'] ?? 'Aset tidak ditemukan' }}</p>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $req['purpose'] }}</p>

                            <div class="flex flex-wrap gap-3 mt-3">
                                @if($req['start_date'])
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">{{ \Carbon\Carbon::parse($req['start_date'])->format('d M Y') }}</span>
                                    @if($req['end_date'])
                                    <span>— {{ \Carbon\Carbon::parse($req['end_date'])->format('d M Y') }}</span>
                                    @endif
                                </div>
                                @endif

                                @if($req['approved_by'])
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Admin: <span class="font-semibold">{{ $req['approved_by'] }}</span></span>
                                </div>
                                @endif
                            </div>

                            {{-- Alasan penolakan --}}
                            @if($req['status'] === 'rejected' && $req['rejection_reason'])
                            <div class="mt-3 bg-red-50 border border-red-100 rounded-xl p-3">
                                <p class="text-[10px] font-black text-red-500 uppercase tracking-widest mb-1">Alasan Penolakan</p>
                                <p class="text-xs text-red-700">{{ $req['rejection_reason'] }}</p>
                            </div>
                            @endif

                            {{-- Info pengembalian --}}
                            @if($req['status'] === 'returned' && $req['returned_at'])
                            <div class="mt-3 bg-gray-50 border border-gray-100 rounded-xl p-3">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pengembalian</p>
                                <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($req['returned_at'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Aksi --}}
                    <div class="mt-4 pt-3 border-t border-gray-50 flex justify-end gap-2">
                        <a href="{{ route('inventaris-aset.show', $req['asset']['id']) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-bold rounded-xl transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat Aset
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(isset($requests['last_page']) && $requests['last_page'] > 1)
        <div class="flex items-center justify-between">
            <p class="text-xs text-gray-500">
                Halaman <span class="font-bold text-gray-700">{{ $requests['current_page'] }}</span>
                dari <span class="font-bold text-gray-700">{{ $requests['last_page'] }}</span>
            </p>
            <div class="flex gap-1">
                @if($requests['current_page'] > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $requests['current_page'] - 1]) }}"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all text-sm font-bold">‹</a>
                @endif
                @if($requests['current_page'] < $requests['last_page'])
                <a href="{{ request()->fullUrlWithQuery(['page' => $requests['current_page'] + 1]) }}"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all text-sm font-bold">›</a>
                @endif
            </div>
        </div>
        @endif

        @elseif(!$error)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm font-bold text-gray-500">Belum ada riwayat peminjaman</p>
            <a href="{{ route('inventaris-aset.index') }}"
                class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-xl hover:bg-red-700 transition-all">
                Lihat Daftar Aset
            </a>
        </div>
        @endif

    </div>
</x-app-layout>
