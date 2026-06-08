<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Integrasi mesin GF1600 / ZKTeco untuk data pegawai</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="fingerprintSyncModal()">
        @include('pages.fingerprint.partials.flash')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Mesin</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $devices->total() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">User Fingerprint</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ \App\Models\FingerprintUser::count() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Log Absensi</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ \App\Models\FingerprintAttendance::count() }}</p>
            </div>
        </div>

        @role('Super Admin|KAUR SDM')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-black text-gray-900">Tarik Log Otomatis</h3>
                                <p class="text-sm text-gray-500">Jadwalkan penarikan data mesin setiap hari tanpa menekan tombol manual.</p>
                            </div>
                        </div>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full px-4 py-2 text-xs font-black uppercase tracking-widest {{ $autoSyncSetting->is_enabled ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-100 text-gray-500 ring-1 ring-gray-200' }}">
                        {{ $autoSyncSetting->is_enabled ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('fingerprint.auto-sync-settings.update') }}" class="p-6 grid grid-cols-1 xl:grid-cols-[1.2fr_1fr] gap-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <label class="flex items-start gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <input type="checkbox" name="is_enabled" value="1" @checked($autoSyncSetting->is_enabled) class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>
                                <span class="block text-sm font-black text-gray-900">Aktifkan penarikan otomatis</span>
                                <span class="block text-xs text-gray-500 mt-1">Sistem akan mengecek jadwal setiap menit melalui Laravel Scheduler, lalu mengirim job ke queue fingerprint sekali per hari.</span>
                            </span>
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jam Penarikan</span>
                                <input type="time" name="run_time" value="{{ substr((string) $autoSyncSetting->run_time, 0, 5) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                            <label class="block">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang Data</span>
                                <select name="range_type" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="1_day" @selected($autoSyncSetting->range_type === '1_day')>Hari ini</option>
                                    <option value="2_days" @selected($autoSyncSetting->range_type === '2_days')>2 hari terakhir</option>
                                    <option value="1_month" @selected($autoSyncSetting->range_type === '1_month')>1 bulan terakhir</option>
                                    <option value="2_months" @selected($autoSyncSetting->range_type === '2_months')>2 bulan terakhir</option>
                                    <option value="all" @selected($autoSyncSetting->range_type === 'all')>Semua data</option>
                                </select>
                            </label>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <span class="block text-xs font-black uppercase tracking-widest text-gray-400">Mesin Yang Ditarik</span>
                                <span class="text-xs font-semibold text-gray-400">Kosongkan pilihan untuk semua mesin aktif</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($allDevices as $deviceOption)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-4 py-3 shadow-sm">
                                        <input type="checkbox" name="device_ids[]" value="{{ $deviceOption->id }}" @checked(in_array($deviceOption->id, $autoSyncSetting->device_ids ?? [])) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-bold text-gray-900">{{ $deviceOption->name }}</span>
                                            <span class="block text-xs {{ $deviceOption->is_active ? 'text-emerald-600' : 'text-gray-400' }}">{{ $deviceOption->is_active ? 'Aktif' : 'Nonaktif' }} - {{ $deviceOption->ip_address }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">
                                Simpan Jadwal Otomatis
                            </button>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-gray-950 p-5 text-white">
                        <p class="text-xs font-black uppercase tracking-widest text-red-300">Status Terakhir</p>
                        <p class="mt-2 text-2xl font-black">
                            {{ $autoSyncSetting->last_dispatched_at ? $autoSyncSetting->last_dispatched_at->format('d M Y H:i') : 'Belum Pernah Jalan' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-300">Queue worker `fingerprint` akan memproses job otomatis ini di belakang layar, sama seperti tombol Tarik Log manual.</p>

                        <div class="mt-5 space-y-3">
                            @forelse(($autoSyncSetting->last_progress_ids ?? []) as $lastProgress)
                                <div class="rounded-2xl bg-white/10 border border-white/10 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-bold truncate">{{ $lastProgress['device_name'] ?? 'Mesin fingerprint' }}</p>
                                        <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ ($lastProgress['status'] ?? '') === 'queued' ? 'bg-emerald-400/15 text-emerald-200' : 'bg-amber-400/15 text-amber-200' }}">
                                            {{ $lastProgress['status'] ?? '-' }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $lastProgress['message'] ?? (($lastProgress['range'] ?? '-') . ' - ' . ($lastProgress['progress_id'] ?? '-')) }}
                                    </p>
                                </div>
                            @empty
                                <div class="rounded-2xl bg-white/10 border border-white/10 p-4 text-sm text-gray-300">
                                    Belum ada riwayat dispatch otomatis.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </form>
            </div>
        @endrole

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-gray-900">Daftar Mesin Fingerprint</h3>
                    <p class="text-sm text-gray-500">Kelola koneksi perangkat, user, dan log absensi.</p>
                </div>
                <a href="{{ route('fingerprint.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                    Tambah Mesin
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Koneksi</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Serial/Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($devices as $device)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $device->name }}</div>
                                    <div class="text-xs font-semibold {{ $device->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $device->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-mono text-sm text-gray-900">{{ $device->ip_address }}:{{ $device->port }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">{{ $device->serial_number ?: '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $device->location ?: 'Lokasi belum diisi' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $device->fingerprint_users_count }} user</div>
                                    <div class="text-xs text-gray-400">{{ $device->attendances_count }} log</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <form method="POST" action="{{ route('fingerprint.test-connection', $device->id) }}">
                                            @csrf
                                            <button class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100">Test Koneksi</button>
                                        </form>
                                        <form method="POST" action="{{ route('fingerprint.sync-users', $device->id) }}">
                                            @csrf
                                            <button class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Tarik User</button>
                                        </form>
                                        <button type="button" @click="syncAction = '{{ route('fingerprint.sync-attendances', $device->id) }}'; syncOpen = true" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">Tarik Log</button>
                                        <a href="{{ route('fingerprint.edit', $device) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Edit</a>
                                        <form method="POST" action="{{ route('fingerprint.destroy', $device) }}" onsubmit="return confirm('Hapus mesin fingerprint ini? Data user dan log dari mesin ini ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada mesin fingerprint. Tambahkan GF1600 terlebih dahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($devices->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $devices->links() }}</div>
            @endif
        </div>

        @include('pages.fingerprint.partials.user-table', [
            'fingerprintUsers' => $fingerprintUsers,
            'allDevices' => $allDevices,
            'title' => 'User Mesin Fingerprint',
            'showMappingButton' => true,
            'action' => route('fingerprint.index'),
        ])

        <div x-show="syncOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm p-4">
            <div class="min-h-full flex items-center justify-center">
                <form @submit.prevent="startSync($event)" :action="syncAction" class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-black text-gray-900">Konfirmasi Tarik Log</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="isRunning ? 'Proses berjalan di background worker. Halaman ini tetap aman digunakan.' : 'Tarik log hanya memproses user mesin yang sudah dimapping ke pegawai sistem.'"></p>
                    </div>
                    <div x-show="!isRunning && !isDone" class="p-6 space-y-4">
                        <label class="block">
                            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang Data</span>
                            <select name="range_type" x-model="rangeType" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="1_day">Hari ini</option>
                                <option value="2_days">2 hari terakhir</option>
                                <option value="1_month">1 bulan terakhir</option>
                                <option value="2_months">2 bulan terakhir</option>
                                <option value="custom">Kustom</option>
                                <option value="all">Semua data</option>
                            </select>
                        </label>
                        <div x-show="rangeType === 'custom'" class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span class="block text-xs font-bold text-gray-500 mb-1">Tanggal Awal</span>
                                <input type="date" name="date_from" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                            <label class="block">
                                <span class="block text-xs font-bold text-gray-500 mb-1">Tanggal Akhir</span>
                                <input type="date" name="date_to" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                        </div>
                    </div>
                    <div x-show="isRunning || isDone" class="p-6 space-y-5">
                        <div class="relative mx-auto h-28 w-28 rounded-full bg-gradient-to-br from-amber-100 to-red-100 flex items-center justify-center overflow-hidden">
                            <div class="absolute inset-3 rounded-full border-4 border-white/80"></div>
                            <div class="absolute h-16 w-16 rounded-2xl bg-gray-900 shadow-xl transition-transform duration-500"
                                :class="progress.status === 'running' ? 'animate-bounce' : ''">
                                <div class="absolute left-4 top-5 h-2 w-2 rounded-full bg-white"></div>
                                <div class="absolute right-4 top-5 h-2 w-2 rounded-full bg-white"></div>
                                <div class="absolute bottom-4 left-1/2 h-1.5 w-8 -translate-x-1/2 rounded-full bg-amber-400"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
                                <span x-text="progress.character"></span>
                                <span x-text="progress.percent + '%'"></span>
                            </div>
                            <div class="h-4 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 via-red-500 to-rose-600 transition-all duration-500"
                                    :style="'width:' + progress.percent + '%'"></div>
                            </div>
                            <p class="mt-3 text-sm font-semibold text-gray-700" x-text="progress.message"></p>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <div class="rounded-2xl bg-gray-50 p-3 text-center">
                                <p class="text-lg font-black text-gray-900" x-text="progress.processed"></p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Proses</p>
                            </div>
                            <div class="rounded-2xl bg-emerald-50 p-3 text-center">
                                <p class="text-lg font-black text-emerald-700" x-text="progress.created"></p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Baru</p>
                            </div>
                            <div class="rounded-2xl bg-blue-50 p-3 text-center">
                                <p class="text-lg font-black text-blue-700" x-text="progress.updated"></p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-blue-500">Update</p>
                            </div>
                            <div class="rounded-2xl bg-amber-50 p-3 text-center">
                                <p class="text-lg font-black text-amber-700" x-text="progress.skipped"></p>
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-500">Lewat</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 flex justify-end gap-3">
                        <button type="button" @click="closeSync()" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50" x-text="isRunning ? 'Tutup' : (isDone ? 'Selesai' : 'Batal')"></button>
                        <button x-show="!isRunning && !isDone" class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-700">Mulai Tarik Log</button>
                        <a x-show="isDone && progress.status === 'finished'" href="{{ route('fingerprint.logs') }}" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Lihat Rekap</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function fingerprintSyncModal() {
            return {
                syncOpen: false,
                syncAction: '',
                rangeType: '1_month',
                isRunning: false,
                isDone: false,
                poller: null,
                progress: {
                    status: 'idle',
                    percent: 0,
                    message: 'Menunggu konfirmasi.',
                    character: 'Stella sedang bersiap',
                    processed: 0,
                    created: 0,
                    updated: 0,
                    skipped: 0,
                },
                async startSync(event) {
                    const form = event.currentTarget;
                    this.isRunning = true;
                    this.isDone = false;
                    this.progress = {
                        status: 'queued',
                        percent: 0,
                        message: 'Mengirim job ke antrean worker...',
                        character: 'Stella membuka antrean',
                        processed: 0,
                        created: 0,
                        updated: 0,
                        skipped: 0,
                    };

                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        this.progress = {
                            ...this.progress,
                            status: 'failed',
                            percent: 100,
                            message: data.message || 'Gagal memulai tarik log.',
                            character: 'Stella berhenti di gerbang',
                        };
                        this.isRunning = false;
                        this.isDone = true;
                        return;
                    }

                    this.pollProgress(data.status_url);
                },
                pollProgress(url) {
                    clearInterval(this.poller);
                    const tick = async () => {
                        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const data = await response.json();
                        this.progress = {
                            status: data.status || 'queued',
                            percent: Number(data.percent || 0),
                            message: data.message || 'Menunggu progress...',
                            character: data.character || 'Stella sedang bekerja',
                            processed: Number(data.processed || 0),
                            created: Number(data.created || 0),
                            updated: Number(data.updated || 0),
                            skipped: Number(data.skipped || 0),
                        };

                        if (['finished', 'failed', 'missing'].includes(this.progress.status)) {
                            clearInterval(this.poller);
                            this.isRunning = false;
                            this.isDone = true;
                        }
                    };

                    tick();
                    this.poller = setInterval(tick, 1200);
                },
                closeSync() {
                    this.syncOpen = false;
                    if (!this.isRunning) {
                        this.isDone = false;
                    }
                },
            }
        }
    </script>
</x-app-layout>
