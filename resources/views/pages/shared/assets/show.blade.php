<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventaris-aset.index') }}"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg text-gray-800 leading-tight">Detail Aset</h2>
                <p class="text-xs text-gray-400 mt-0.5">Informasi lengkap inventaris aset</p>
            </div>
        </div>
    </x-slot>

    @if($error)
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 flex items-start gap-4">
        <div class="w-10 h-10 flex-shrink-0 bg-red-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div>
            <h4 class="font-bold text-red-800 text-sm">Gagal Memuat Data</h4>
            <p class="text-red-600 text-xs mt-1">{{ $error }}</p>
        </div>
    </div>
    @elseif($asset)

    @php
        $statusMap = [
            'Tersedia'  => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            'Dipinjam'  => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'dot' => 'bg-blue-500'],
            'Digunakan' => ['bg' => 'bg-blue-100',    'text' => 'text-blue-700',    'dot' => 'bg-blue-500'],
            'Rusak'     => ['bg' => 'bg-red-100',     'text' => 'text-red-700',     'dot' => 'bg-red-500'],
        ];
        $sc = $statusMap[$asset['current_status']] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400'];
    @endphp

    <div class="space-y-6">

        {{-- Disposed Banner --}}
        @if($isDisposed)
        <div class="bg-gray-900 text-white rounded-2xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <p class="text-sm font-semibold">Aset ini telah <span class="text-red-400 font-black">dimusnahkan/dibuang</span> dan tidak lagi aktif dalam inventaris.</p>
        </div>
        @endif

        {{-- Header Card --}}
        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-3xl p-6 text-white shadow-xl shadow-red-500/20">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-red-200 text-xs font-black uppercase tracking-widest mb-1">{{ $asset['asset_code_ypt'] ?? 'Belum ada kode' }}</p>
                        <h1 class="text-xl font-black leading-tight">{{ $asset['name'] }}</h1>
                        @if($asset['category'])
                        <p class="text-red-200 text-sm mt-1 font-medium">{{ $asset['category'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col items-start sm:items-end gap-2">
                    {{-- Status --}}
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-xs font-black uppercase tracking-widest">
                        <span class="w-2 h-2 rounded-full {{ $sc['dot'] }} animate-pulse"></span>
                        {{ $asset['current_status'] ?? '-' }}
                    </span>
                    @if($asset['created_at'])
                    <span class="text-red-200 text-xs font-medium">Tahun {{ \Carbon\Carbon::parse($asset['created_at'])->format('Y') }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tombol Ajukan Peminjaman --}}
        @if(!$isDisposed && ($asset['current_status'] ?? '') === 'Tersedia')
        <div class="flex justify-end">
            <a href="{{ route('inventaris-aset.borrow-form', $asset['asset_id']) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-emerald-500/20 hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Ajukan Peminjaman
            </a>
        </div>
        @elseif(!$isDisposed && in_array($asset['current_status'] ?? '', ['Dipinjam', 'Digunakan']))
        <div class="flex justify-end">
            <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 border border-blue-200 text-blue-600 text-sm font-bold rounded-2xl">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Aset Sedang Dipinjam
            </div>
        </div>
        @endif

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Informasi Lokasi --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide">Lokasi & Institusi</h3>
                </div>
                <div class="space-y-4">
                    @php
                    $locationFields = [
                        ['label' => 'Institusi',   'value' => $asset['institution']],
                        ['label' => 'Gedung',      'value' => $asset['building']],
                        ['label' => 'Ruangan',     'value' => $asset['room']],
                        ['label' => 'Fakultas/Div','value' => $asset['faculty']],
                        ['label' => 'Jurusan/Dept','value' => $asset['department']],
                    ];
                    @endphp
                    @foreach($locationFields as $f)
                    @if($f['value'])
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest flex-shrink-0">{{ $f['label'] }}</span>
                        <span class="text-sm text-gray-700 font-semibold text-right">{{ $f['value'] }}</span>
                    </div>
                    @if(!$loop->last)<div class="h-px bg-gray-50"></div>@endif
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Informasi Aset --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide">Detail Aset</h3>
                </div>
                <div class="space-y-4">
                    @php
                    $detailFields = [
                        ['label' => 'Penanggung Jawab', 'value' => $asset['person_in_charge']],
                        ['label' => 'Fungsi Aset',      'value' => $asset['asset_function']],
                        ['label' => 'Sumber Pendanaan', 'value' => $asset['funding_source']],
                        ['label' => 'No. Urut',         'value' => $asset['sequence_number']],
                        ['label' => 'Status Aktif',     'value' => $asset['status']],
                    ];
                    @endphp
                    @foreach($detailFields as $f)
                    @if($f['value'])
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest flex-shrink-0">{{ $f['label'] }}</span>
                        <span class="text-sm text-gray-700 font-semibold text-right">{{ $f['value'] }}</span>
                    </div>
                    @if(!$loop->last)<div class="h-px bg-gray-50"></div>@endif
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Informasi Keuangan --}}
            @if($asset['purchase_cost'])
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-wide">Informasi Keuangan</h3>
                </div>
                <div class="space-y-4">
                    @php
                    $financeFields = [
                        ['label' => 'Harga Perolehan', 'value' => $asset['purchase_cost'] ? 'Rp ' . number_format($asset['purchase_cost'], 0, ',', '.') : null],
                        ['label' => 'Nilai Sisa',      'value' => $asset['salvage_value'] !== null ? 'Rp ' . number_format($asset['salvage_value'], 0, ',', '.') : null],
                        ['label' => 'Masa Manfaat',    'value' => $asset['useful_life'] ? $asset['useful_life'] . ' Tahun' : null],
                        ['label' => 'Nilai Buku',      'value' => $asset['book_value'] ? 'Rp ' . number_format($asset['book_value'], 0, ',', '.') : null],
                    ];
                    @endphp
                    @foreach($financeFields as $f)
                    @if($f['value'])
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest flex-shrink-0">{{ $f['label'] }}</span>
                        <span class="text-sm text-gray-700 font-semibold text-right font-mono">{{ $f['value'] }}</span>
                    </div>
                    @if(!$loop->last)<div class="h-px bg-gray-50"></div>@endif
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Informasi Pembuangan (jika disposed) --}}
            @if($isDisposed)
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-wide">Informasi Penghapusan</h3>
                </div>
                <div class="space-y-3">
                    @if($asset['disposal_date'])
                    <div class="flex justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Tanggal</span>
                        <span class="text-sm text-gray-700 font-semibold">{{ \Carbon\Carbon::parse($asset['disposal_date'])->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($asset['disposal_method'])
                    <div class="h-px bg-gray-200"></div>
                    <div class="flex justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Metode</span>
                        <span class="text-sm text-gray-700 font-semibold">{{ $asset['disposal_method'] }}</span>
                    </div>
                    @endif
                    @if($asset['disposal_reason'])
                    <div class="h-px bg-gray-200"></div>
                    <div class="flex justify-between gap-4">
                        <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Alasan</span>
                        <span class="text-sm text-gray-700 font-semibold text-right">{{ $asset['disposal_reason'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Footer info --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-gray-400">
            <span>Data disinkronkan dari Sistem Manajemen Aset</span>
            <div class="flex items-center gap-4">
                <a href="{{ route('inventaris-aset.borrow-history') }}"
                    class="inline-flex items-center gap-1.5 text-red-500 hover:text-red-700 font-bold transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Peminjaman Saya
                </a>
                @if($asset['updated_at'])
                <span>Diperbarui: {{ \Carbon\Carbon::parse($asset['updated_at'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                @endif
            </div>
        </div>

    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-sm font-bold text-gray-500">Data aset tidak ditemukan</p>
        <a href="{{ route('inventaris-aset.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-xl hover:bg-red-700 transition-all">
            Kembali ke Daftar Aset
        </a>
    </div>
    @endif

</x-app-layout>
