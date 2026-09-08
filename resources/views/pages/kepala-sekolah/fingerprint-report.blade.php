<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold">Laporan Absensi Fingerprint {{ $period === 'week' ? 'Mingguan' : 'Bulanan' }}</h2></x-slot>
    <div class="space-y-5 p-6" x-data="{search: ''}">
        <a class="text-sm font-semibold text-blue-700" href="{{ route('kepala-sekolah.monitoring.index', ['section' => 'fingerprint', 'date' => $date->toDateString()]) }}">← Monitoring harian</a>
        <form class="flex flex-wrap items-end gap-3 rounded-xl border bg-white p-4">
            <label>Periode<select name="period" class="block rounded-lg border-gray-300"><option value="week" @selected($period === 'week')>Mingguan (Senin–Minggu)</option><option value="month" @selected($period === 'month')>Bulanan</option></select></label>
            <label>Tanggal acuan<input type="date" name="date" required value="{{ $date->toDateString() }}" class="block rounded-lg border-gray-300"></label>
            <label>Nama pegawai<input name="search" value="{{ request('search') }}" class="block rounded-lg border-gray-300" maxlength="100"></label>
            <button class="rounded-lg bg-red-600 px-4 py-2 text-white">Tampilkan</button>
        </form>
        <p class="text-sm text-gray-600">{{ $start->format('d/m/Y') }}–{{ $end->format('d/m/Y') }} · {{ $rows->count() }} pegawai aktif. Hari mendatang belum dihitung.</p>
        <div class="grid gap-3 sm:grid-cols-4">
            @foreach(['present' => 'Hari Hadir', 'late' => 'Hari Terlambat', 'absent' => 'Hari Tidak Hadir', 'leave' => 'Hari Izin'] as $key => $label)
                <div class="rounded-xl border bg-white p-5"><span class="text-sm text-gray-500">{{ $label }}</span><strong class="mt-2 block text-3xl text-gray-900">{{ $rows->sum($key) }}</strong></div>
            @endforeach
        </div>
        <section class="rounded-2xl border bg-white p-5">
            <h3 class="font-bold">Tren Kehadiran Harian</h3>
            <p class="mt-2 text-sm"><span class="text-emerald-700">● Hadir</span> · <span class="text-amber-700">● Terlambat</span> · <span class="text-red-700">● Tidak Hadir</span></p>
            <div class="overflow-x-auto bg-slate-50 rounded-xl mt-4">
                <svg viewBox="0 0 760 220" class="min-w-[650px] w-full" role="img" aria-label="Tren jumlah pegawai hadir, terlambat dan tidak hadir">
                    @foreach([0, 1, 2, 3] as $tick)
                        <line x1="45" y1="{{ 180 - $tick * 50 }}" x2="725" y2="{{ 180 - $tick * 50 }}" stroke="#e2e8f0"/><text x="5" y="{{ 184 - $tick * 50 }}" font-size="11" fill="#475569">{{ round($chartMax * $tick / 3, 1) }}</text>
                    @endforeach
                    @foreach($lines as $line)<polyline points="{{ $line['points'] }}" fill="none" stroke="{{ $line['color'] }}" stroke-width="3" stroke-linejoin="round"/>@endforeach
                    @foreach($trend as $i => $day)
                        @foreach($lines as $key => $line)<circle tabindex="0" cx="{{ 45 + $i * 680 / max(1, count($trend)-1) }}" cy="{{ 180 - $day[$key] * 150 / $chartMax }}" r="3" fill="{{ $line['color'] }}"><title>{{ $day['label'] }}: hadir {{ $day['present'] }}, terlambat {{ $day['late'] }}, tidak hadir {{ $day['absent'] }}</title></circle>@endforeach
                        @if($i % max(1, (int) ceil(count($trend)/8)) === 0)<text x="{{ 45 + $i * 680 / max(1, count($trend)-1) }}" y="205" text-anchor="middle" font-size="11" fill="#475569">{{ $day['label'] }}</text>@endif
                    @endforeach
                </svg>
            </div>
            @if(!$trend)<p class="p-3 text-sm text-gray-500">Periode ini belum berlangsung.</p>@endif
        </section>
        <div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-left text-sm">
            <thead class="bg-gray-50"><tr>@foreach(['Pegawai', 'Hadir', 'Terlambat', 'Tidak Hadir', 'Izin', 'Rata-rata Datang', 'Rata-rata Pulang'] as $header)<th class="p-4">{{ $header }}</th>@endforeach</tr></thead>
            <tbody class="divide-y">@forelse($rows as $row)<tr><td class="p-4 font-semibold">{{ $row['name'] }}<span class="block text-xs font-normal text-gray-500">{{ $row['employment'] }}</span></td>@foreach(['present', 'late', 'absent', 'leave', 'average_in', 'average_out'] as $key)<td class="p-4">{{ $row[$key] }}</td>@endforeach</tr>@empty<tr><td colspan="7" class="p-8 text-center">Tidak ada pegawai sesuai filter.</td></tr>@endforelse</tbody>
        </table></div>
        <p class="rounded-xl bg-blue-50 p-4 text-sm text-blue-900">Hadir dihitung per hari dengan scan, termasuk hadir opsional; terlambat merupakan bagian dari hadir. Tidak hadir hanya untuk hari wajib setelah batas datang, dengan pengecualian izin yang disetujui, libur, dan jadwal part-time. Rata-rata menggunakan scan pertama dan scan terakhir yang berbeda pada tiap hari; hari tanpa scan pulang tidak dimasukkan ke rata-rata pulang. Status kepegawaian dan jadwal menggunakan data terbaru seperti monitoring harian.</p>
    </div>
</x-app-layout>
