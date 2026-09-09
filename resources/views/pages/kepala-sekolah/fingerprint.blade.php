<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-800">Monitoring Absensi Fingerprint Pegawai</h2></x-slot>
    <p class="px-6 pt-4 text-sm text-gray-500">Guru dan TPA berstatus aktif masuk dalam rekap, termasuk saat memilih tanggal lampau. Keaktifan dikelola oleh Kaur SDM / Super Admin.</p>
    <div class="space-y-6 px-4 py-6 sm:px-6 lg:px-8" x-data="{
        rows: @js($rows), search: '', status: 'all', page: 1,
        syncing: false, syncMessage: '', syncJobs: [],
        async pullToday() {
            if (this.syncing) return;
            this.syncing = true; this.syncJobs = []; this.syncMessage = 'Mengantrekan penarikan data hari ini...';
            try {
                const response = await fetch(@js(route('kepala-sekolah.fingerprint-sync.store')), { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @js(csrf_token()) } });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal mengantrekan sinkronisasi.');
                for (let attempt = 0; attempt < 240; attempt++) {
                    const poll = await fetch(data.status_url, { headers: { 'Accept': 'application/json' } });
                    if (!poll.ok) throw new Error('Progres tidak tersedia. Muat ulang halaman untuk melihat data terbaru.');
                    const progress = await poll.json(); this.syncJobs = progress.jobs;
                    this.syncMessage = 'Penarikan berjalan di background. Pastikan worker fingerprint aktif.';
                    if (progress.done) {
                        if (progress.jobs.every(job => job.status === 'finished')) {
                            this.syncMessage = 'Selesai. Memperbarui rekap hari ini...';
                            window.location.assign(@js(route('kepala-sekolah.monitoring.index', 'fingerprint')) + '?date=' + encodeURIComponent(data.date));
                            return;
                        }
                        throw new Error('Sebagian mesin gagal atau progres kedaluwarsa. Hubungi Super Admin; lihat data yang berhasil ditarik dengan tombol Tampilkan.');
                    }
                    await new Promise(resolve => setTimeout(resolve, 3000));
                }
                throw new Error('Proses masih antre/berjalan. Hubungi Super Admin untuk memeriksa worker sebelum mengulang penarikan.');
            } catch (error) { this.syncMessage = error.message; }
            finally { this.syncing = false; }
        },
        get filtered() { return this.rows.filter(r => r.name.toLowerCase().includes(this.search.toLowerCase()) && (this.status === 'all' || (this.status === 'present' && r.check_in) || (this.status === 'out' && r.check_out) || (this.status === 'absent' && r.status === 'Tidak Hadir') || (this.status === 'late' && r.status === 'Terlambat') || (this.status === 'pending' && r.status === 'Menunggu Absensi') || (this.status === 'leave' && r.status === 'Izin'))); },
        get pages() { return Math.max(1, Math.ceil(this.filtered.length / 25)); },
        get visible() { return this.filtered.slice((this.page - 1) * 25, this.page * 25); }
    }">
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-slate-900 p-6 text-white">
            <div><h3 class="text-2xl font-bold">Kehadiran Pegawai</h3><p class="mt-1 text-sm text-slate-300">{{ $date->translatedFormat('l, d F Y') }} · Rekap per pegawai, bukan jumlah scan</p></div>
            <form method="GET" class="flex flex-wrap items-end gap-3"><label><span class="mb-1 block text-xs">Tanggal kehadiran</span><input type="date" name="date" value="{{ $date->toDateString() }}" required class="rounded-lg border-gray-300 text-gray-900"></label><button class="rounded-lg bg-red-600 px-4 py-2.5 font-semibold">Tampilkan</button><button type="button" @click="pullToday()" :disabled="syncing" class="rounded-lg bg-emerald-600 px-4 py-2.5 font-semibold disabled:opacity-50" x-text="syncing ? 'Sedang Menarik...' : 'Tarik Data Hari Ini'"></button></form>
        </div>
        <div x-show="syncMessage" x-cloak class="rounded-xl bg-blue-50 p-4 text-sm text-blue-800" role="status" aria-live="polite">
            <p x-text="syncMessage"></p>
            <template x-for="job in syncJobs" :key="job.name"><p class="mt-2" x-text="job.name + ': ' + job.status + ' (' + job.percent + '%)'"></p></template>
        </div>
        @if($errors->any())<p class="text-red-700">{{ $errors->first() }}</p>@endif
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('kepala-sekolah.fingerprint-report.index', ['period' => 'week', 'date' => $date->toDateString()]) }}" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 font-semibold text-emerald-800">Laporan Mingguan</a>
            <a href="{{ route('kepala-sekolah.fingerprint-report.index', ['period' => 'month', 'date' => $date->toDateString()]) }}" class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 font-semibold text-blue-800">Laporan Bulanan</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-7">
            @foreach(['all' => ['Total Pegawai', $summary['total']], 'present' => ['Total Kehadiran', $summary['present']], 'out' => ['Tercatat Pulang', $summary['out']], 'late' => ['Terlambat', $summary['late']], 'absent' => ['Tidak Hadir', $summary['absent']], 'leave' => ['Izin', $summary['leave']], 'pending' => ['Menunggu', $summary['pending']]] as $key => [$label, $value])
                <button type="button" @click="status = '{{ $key }}'; page = 1" :aria-pressed="status === '{{ $key }}'" :class="status === '{{ $key }}' ? 'ring-2 ring-red-500' : ''" class="rounded-2xl border bg-white p-5 text-left shadow-sm">
                    <span class="text-sm font-semibold text-gray-500">{{ $label }}</span><strong class="mt-2 block text-3xl text-gray-900">{{ $value }}</strong><span class="text-xs text-gray-500">Klik untuk memfilter daftar</span>
                </button>
            @endforeach
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl border bg-white p-6">
                <h3 class="font-bold">Persentase Kehadiran</h3>
                @php($percent = $summary['required'] ? round($summary['required_present'] / $summary['required'] * 100, 1) : null)
                <div class="mt-6 flex justify-center"><div role="img" aria-label="{{ $percent === null ? 'Tidak ada pegawai wajib hadir' : 'Kehadiran '.$percent.' persen' }}" class="flex h-44 w-44 items-center justify-center rounded-full" style="background: conic-gradient(#10b981 {{ $percent ?? 0 }}%, #e5e7eb 0)"><div class="flex h-32 w-32 items-center justify-center rounded-full bg-white text-3xl font-bold">{{ $percent === null ? '—' : $percent.'%' }}</div></div></div>
                <p class="mt-5 text-center text-sm text-gray-500">{{ $summary['required_present'] }} dari {{ $summary['required'] }} pegawai wajib hadir memiliki scan.</p>
                <p class="mt-2 text-center text-xs text-gray-500">{{ $summary['absent'] }} tidak hadir · {{ $summary['leave'] }} izin disetujui · {{ $summary['pending'] }} masih menunggu batas.</p>
                @if($summary['unclassified'])<p class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">{{ $summary['unclassified'] }} pegawai memiliki status kosong/tidak dikenali; tidak masuk perhitungan persentase.</p>@endif
                <p class="mt-3 text-xs leading-relaxed text-gray-500">Tetap, full-time, dan TPA: wajib pada hari kerja. Guru part-time: wajib pada hari dengan jadwal mengajar semester aktif. Kalender libur dan akhir pekan dikecualikan.</p>
            </section>
            <section class="rounded-2xl border bg-white p-6 lg:col-span-2">
                <h3 class="font-bold">Tren Jam Kedatangan</h3><p class="mt-1 text-sm text-gray-500">Line chart jumlah scan pertama pegawai per jam pada tanggal terpilih.</p>
                <div class="mt-5 overflow-x-auto rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 p-4 text-gray-800">
                    <svg class="min-w-[650px]" viewBox="0 0 720 220" role="img" aria-label="Line chart jumlah kedatangan pegawai per jam">
                        <defs><linearGradient id="attendanceArea" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#34d399" stop-opacity=".55"/><stop offset="1" stop-color="#34d399" stop-opacity="0"/></linearGradient><filter id="lineGlow"><feGaussianBlur stdDeviation="3" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>
                        @foreach([30, 78, 126, 175] as $gridY)<line x1="20" y1="{{ $gridY }}" x2="700" y2="{{ $gridY }}" stroke="#64748b" stroke-opacity=".25" stroke-dasharray="4 6"/>@endforeach
                        <polygon points="{{ $areaPoints }}" fill="url(#attendanceArea)"/>
                        <polyline points="{{ $chartPoints }}" fill="none" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        @foreach($hours as $index => $hour)
                            <circle cx="{{ $hour['x'] }}" cy="{{ $hour['y'] }}" r="{{ $hour['count'] ? 5 : 2.5 }}" fill="#047857"><title>{{ $hour['label'] }}: {{ $hour['count'] }} pegawai</title></circle>
                            @if($index % 3 === 0)<text x="{{ $hour['x'] }}" y="205" text-anchor="middle" fill="#94a3b8" font-size="10">{{ substr($hour['label'], 0, 2) }}</text>@endif
                        @endforeach
                    </svg>
                </div>
            </section>
        </div>
        <section class="overflow-hidden rounded-2xl border bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b p-5"><div><h3 class="font-bold">Daftar Pegawai</h3><p class="text-xs text-gray-500">Ringkasan dan chart mencakup guru serta TPA pada tanggal terpilih.</p></div><label><span class="sr-only">Cari pegawai</span><input x-model="search" @input="page = 1" placeholder="Cari nama pegawai..." class="rounded-lg border-gray-300"></label></div>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50"><tr><th class="px-6 py-4">Pegawai</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Hadir</th><th class="px-6 py-4">Pulang</th></tr></thead><tbody class="divide-y">
                <template x-for="row in visible" :key="row.id"><tr><td class="px-6 py-4"><span class="font-semibold" x-text="row.name"></span><span class="mt-1 block text-xs text-gray-500" x-text="row.employment + ' · ' + row.obligation"></span></td><td class="px-6 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-bold" :class="row.status === 'Tidak Hadir' ? 'bg-red-100 text-red-700' : (row.status === 'Terlambat' ? 'bg-orange-100 text-orange-700' : (row.status === 'Menunggu Absensi' ? 'bg-sky-100 text-sky-700' : (row.status === 'Izin' ? 'bg-violet-100 text-violet-700' : 'bg-emerald-100 text-emerald-700')))" x-text="row.status"></span><span x-show="row.deadline" class="mt-1 block text-xs text-gray-500" x-text="'Batas datang ' + row.deadline"></span></td><td class="px-6 py-4 font-mono text-emerald-700" x-text="row.check_in || '—'"></td><td class="px-6 py-4 font-mono text-blue-700" x-text="row.check_out || '—'"></td></tr></template>
                <tr x-show="filtered.length === 0"><td colspan="4" class="p-10 text-center text-gray-500">Tidak ada data sesuai filter.</td></tr>
            </tbody></table></div>
            <div class="flex items-center justify-between border-t p-5 text-sm"><span x-text="filtered.length + ' pegawai · Halaman ' + page + ' / ' + pages"></span><div class="flex gap-2"><button @click="page--" :disabled="page <= 1" class="rounded border px-3 py-2 disabled:opacity-40">Sebelumnya</button><button @click="page++" :disabled="page >= pages" class="rounded border px-3 py-2 disabled:opacity-40">Berikutnya</button></div></div>
        </section>
        <p class="rounded-xl bg-blue-50 p-4 text-xs leading-relaxed text-blue-800">Izin yang telah memperoleh seluruh persetujuan wajib tampil sebagai Izin dan dikeluarkan dari kewajiban serta persentase kehadiran pada tanggal tersebut. Pegawai Tetap memerlukan keputusan akhir Kepala Sekolah. Sebelum batas akhir datang, pegawai tanpa scan berstatus Menunggu Absensi. Setelah batas terlewati status berubah menjadi Tidak Hadir. Jika scan susulan masuk melewati batas, status menjadi Terlambat. Tetap/full-time mengikuti Konfigurasi Waktu Absensi; part-time mengikuti awal jadwal mengajar.</p>
    </div>
</x-app-layout>
