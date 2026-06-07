@php
    $summary = $summary ?? \App\Support\MyFingerprintAttendance::today(auth()->user());
    $hasCheckin = (bool) $summary['first_scan'];
    $hasCheckout = (bool) $summary['last_scan'];
@endphp

<div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-gray-400">Absensi Fingerprint Hari Ini</p>
            <h3 class="mt-2 text-xl font-black text-gray-900">
                @if(!$summary['is_mapped'])
                    Belum terhubung ke mesin
                @elseif($hasCheckin && $hasCheckout)
                    Masuk dan pulang sudah tercatat
                @elseif($hasCheckin)
                    Sudah absen masuk
                @else
                    Belum ada absensi hari ini
                @endif
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                {{ $summary['device']?->name ? 'Mesin terakhir: ' . $summary['device']->name : 'Data diambil dari log fingerprint yang sudah disinkronkan.' }}
            </p>
        </div>
        <a href="{{ route('fingerprint-saya.index') }}" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">
            Lihat Riwayat
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-2xl bg-emerald-50 p-4">
            <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Masuk</p>
            <p class="mt-2 text-2xl font-black text-emerald-700">{{ $summary['first_scan']?->format('H:i:s') ?? '-' }}</p>
        </div>
        <div class="rounded-2xl bg-blue-50 p-4">
            <p class="text-xs font-black uppercase tracking-widest text-blue-600">Pulang</p>
            <p class="mt-2 text-2xl font-black text-blue-700">{{ $summary['last_scan']?->format('H:i:s') ?? '-' }}</p>
        </div>
        <div class="rounded-2xl bg-gray-50 p-4">
            <p class="text-xs font-black uppercase tracking-widest text-gray-500">Total Scan</p>
            <p class="mt-2 text-2xl font-black text-gray-900">{{ $summary['total_scan'] }}</p>
        </div>
    </div>
</div>
