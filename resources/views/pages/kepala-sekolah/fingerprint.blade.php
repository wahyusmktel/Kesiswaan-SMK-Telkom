<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">Monitoring Absensi Fingerprint Guru</h2></x-slot>
    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8" x-data="{
        rows: @js($rows), search: '', status: 'all', page: 1,
        get filtered() { return this.rows.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()) && (this.status === 'all' || (this.status === 'present' && r.check_in) || (this.status === 'out' && r.check_out) || (this.status === 'missing' && !r.check_in))); },
        get pages() { return Math.max(1, Math.ceil(this.filtered.length / 25)); },
        get visible() { return this.filtered.slice((this.page - 1) * 25, this.page * 25); }
    }">
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-slate-900 p-6 text-white">
            <div><h3 class="text-2xl font-bold">Kehadiran Guru</h3><p class="mt-1 text-sm text-slate-300">{{ $date->translatedFormat('l, d F Y') }} · Rekap per guru, bukan jumlah scan</p></div>
            <form method="GET" class="flex items-end gap-3"><label><span class="mb-1 block text-xs">Tanggal kehadiran</span><input type="date" name="date" value="{{ $date->toDateString() }}" required class="rounded-lg border-gray-300 text-gray-900"></label><button class="rounded-lg bg-red-600 px-4 py-2.5 font-semibold">Tampilkan</button></form>
        </div>
        @if($errors->any())<p class="text-red-700">{{ $errors->first() }}</p>@endif
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach(['all' => ['Total Guru', $summary['total']], 'present' => ['Total Kehadiran', $summary['present']], 'out' => ['Tercatat Pulang', $summary['out']], 'missing' => ['Belum Ada Scan', $summary['missing']]] as $key => [$label, $value])
                <button type="button" @click="status = '{{ $key }}'; page = 1" :aria-pressed="status === '{{ $key }}'" :class="status === '{{ $key }}' ? 'ring-2 ring-red-500' : ''" class="rounded-2xl border bg-white p-5 text-left shadow-sm">
                    <span class="text-sm font-semibold text-gray-500">{{ $label }}</span><strong class="mt-2 block text-3xl text-gray-900">{{ $value }}</strong><span class="text-xs text-gray-500">Klik untuk memfilter daftar</span>
                </button>
            @endforeach
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border bg-white p-6">
                <h3 class="font-bold">Persentase Kehadiran</h3>
                @php($percent = $summary['total'] ? round($summary['present'] / $summary['total'] * 100, 1) : 0)
                <div class="mt-6 flex justify-center"><div role="img" aria-label="Kehadiran {{ $percent }} persen" class="flex h-44 w-44 items-center justify-center rounded-full" style="background: conic-gradient(#10b981 {{ $percent }}%, #e5e7eb 0)"><div class="flex h-32 w-32 items-center justify-center rounded-full bg-white text-3xl font-bold">{{ $percent }}%</div></div></div>
                <p class="mt-5 text-center text-sm text-gray-500">{{ $summary['present'] }} dari {{ $summary['total'] }} guru memiliki scan pada tanggal terpilih.</p>
            </section>
            <section class="rounded-2xl border bg-white p-6 lg:col-span-2">
                <h3 class="font-bold">Distribusi Jam Hadir</h3><p class="mt-1 text-sm text-gray-500">Scan pertama setiap guru. Arahkan kursor atau fokus ke batang untuk melihat jumlah.</p>
                <div class="mt-6 overflow-x-auto"><div class="flex h-44 min-w-[600px] items-end gap-1" role="img" aria-label="Distribusi kehadiran per jam">
                    @foreach($hours as $hour)
                        <div class="flex h-full flex-1 flex-col justify-end text-center"><div tabindex="0" title="{{ $hour['label'] }}: {{ $hour['count'] }} guru" aria-label="{{ $hour['label'] }}: {{ $hour['count'] }} guru" class="rounded-t bg-emerald-500 transition hover:bg-emerald-700 focus:bg-emerald-700" style="height: {{ $hour['count'] / max(1, $hours->max('count')) * 130 }}px; min-height: 2px"></div><span class="mt-2 text-[10px] text-gray-500">{{ substr($hour['label'], 0, 2) }}</span></div>
                    @endforeach
                </div></div>
            </section>
        </div>
        <section class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b p-5"><div><h3 class="font-bold">Daftar Guru</h3><p class="text-xs text-gray-500">Ringkasan dan chart tetap mencakup seluruh guru pada tanggal terpilih.</p></div><label><span class="sr-only">Cari guru</span><input x-model="search" @input="page = 1" placeholder="Cari nama guru..." class="rounded-lg border-gray-300"></label></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="px-6 py-4">Guru</th><th class="px-6 py-4">Hadir</th><th class="px-6 py-4">Pulang</th></tr></thead><tbody class="divide-y">
                <template x-for="row in visible" :key="row.id"><tr><td class="px-6 py-4 font-semibold" x-text="row.name"></td><td class="px-6 py-4 font-mono text-emerald-700" x-text="row.check_in || '—'"></td><td class="px-6 py-4 font-mono text-blue-700" x-text="row.check_out || '—'"></td></tr></template>
                <tr x-show="filtered.length === 0"><td colspan="3" class="p-10 text-center text-gray-500">Tidak ada data sesuai filter.</td></tr>
            </tbody></table></div>
            <div class="flex items-center justify-between border-t p-5 text-sm"><span x-text="filtered.length + ' guru · Halaman ' + page + ' / ' + pages"></span><div class="flex gap-2"><button @click="page--" :disabled="page <= 1" class="rounded border px-3 py-2 disabled:opacity-40">Sebelumnya</button><button @click="page++" :disabled="page >= pages" class="rounded border px-3 py-2 disabled:opacity-40">Berikutnya</button></div></div>
        </section>
        <p class="rounded-xl bg-blue-50 p-4 text-xs leading-relaxed text-blue-800">Hadir menggunakan scan pertama, pulang menggunakan scan terakhir yang waktunya berbeda pada hari yang sama, mengikuti pola rekap SISFO. Satu scan tidak diisi sebagai pulang. Belum ada scan bukan otomatis alpha; data bisa belum disinkronkan atau akun guru belum dipetakan. Rekap ini tidak memverifikasi bahwa scan terakhir benar-benar saat meninggalkan sekolah.</p>
    </div>
</x-app-layout>
