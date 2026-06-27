<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Jurnal & Absensi PKL</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            @if ($penempatan)
                <div class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr_0.9fr]">
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                        <p class="text-sm text-gray-500">Tempat PKL</p>
                        <h3 class="mt-1 text-xl font-bold text-gray-900">{{ $penempatan->industri?->nama_industri ?? '-' }}</h3>
                        <div class="mt-4 grid gap-2 text-sm text-gray-600 sm:grid-cols-2">
                            <p><span class="font-semibold text-gray-800">Rombel:</span> {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">Pembimbing sekolah:</span> {{ $penempatan->guruPembimbing?->nama_lengkap ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">Pembimbing industri:</span> {{ $penempatan->nama_pembimbing_industri ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">Status:</span> {{ $penempatan->status }}</p>
                        </div>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                        <p class="text-sm text-gray-500">Periode Pelaksanaan</p>
                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ $effectiveSchedule['tanggal_mulai'] ? \Carbon\Carbon::parse($effectiveSchedule['tanggal_mulai'])->format('d M Y') : '-' }}
                            -
                            {{ $effectiveSchedule['tanggal_selesai'] ? \Carbon\Carbon::parse($effectiveSchedule['tanggal_selesai'])->format('d M Y') : '-' }}
                        </p>
                        <span class="mt-3 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $effectiveSchedule['period_source'] }}</span>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                        <p class="text-sm text-gray-500">Waktu Absensi</p>
                        <div class="mt-2 text-sm font-semibold text-gray-900">
                            <p>Check-in: {{ $effectiveSchedule['jam_check_in_mulai'] ? substr((string) $effectiveSchedule['jam_check_in_mulai'], 0, 5) : '-' }} - {{ $effectiveSchedule['jam_check_in_selesai'] ? substr((string) $effectiveSchedule['jam_check_in_selesai'], 0, 5) : '-' }}</p>
                            <p>Check-out: {{ $effectiveSchedule['jam_check_out_mulai'] ? substr((string) $effectiveSchedule['jam_check_out_mulai'], 0, 5) : '-' }} - {{ $effectiveSchedule['jam_check_out_selesai'] ? substr((string) $effectiveSchedule['jam_check_out_selesai'], 0, 5) : '-' }}</p>
                        </div>
                        <span class="mt-3 inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $effectiveSchedule['attendance_source'] }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-900">
                    <p class="font-bold">Panduan singkat</p>
                    <p class="mt-1">Lakukan check-in terlebih dahulu sebelum mengisi jurnal kegiatan hari ini. Setelah selesai PKL pada hari yang sama, lakukan check-out. Riwayat absensi bisa diunduh dalam bentuk PDF untuk laporan.</p>
                    @if($setting?->instruksi_jurnal)
                        <p class="mt-3 rounded-xl bg-white/70 p-3">{{ $setting->instruksi_jurnal }}</p>
                    @endif
                </div>

                <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6" x-data="pklAttendance()" x-init="init()">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-gray-900">Absensi Hari Ini</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ now()->format('d M Y') }}</p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                Check-in: {{ $absensiHariIni?->check_in_at ?? '-' }}
                            </span>
                        </div>

                        <div class="mt-5 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                            <p>Sistem akan mencoba mengambil lokasi otomatis saat halaman dibuka. Tombol Ambil Lokasi GPS digunakan untuk memastikan ulang keakuratan sebelum check-in atau check-out.</p>
                            <button type="button" @click="detectLocation" class="mt-3 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Ambil Lokasi GPS</button>
                            <p class="mt-3 text-xs text-gray-500" x-text="locationText"></p>
                            <template x-if="latitude && longitude">
                                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white">
                                    <iframe class="h-64 w-full border-0" :src="mapEmbedUrl" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-t border-gray-100 px-4 py-3">
                                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-700">
                                            <span class="h-3 w-3 rounded-full bg-red-600"></span>
                                            <span>Legenda: lokasi Anda saat ini</span>
                                        </div>
                                        <a class="text-xs font-semibold text-blue-600" target="_blank" :href="osmUrl">Buka di OpenStreetMap</a>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-5 grid gap-4 lg:grid-cols-2">
                            <form method="POST" action="{{ route('siswa.jurnal-prakerin.check-in') }}" enctype="multipart/form-data" @submit="syncLocation" class="rounded-xl border border-gray-100 p-4">
                                @csrf
                                <h4 class="font-semibold text-gray-900">Check-in</h4>
                                <p class="mt-1 text-xs text-gray-500">Upload foto kondisi saat ini sebagai bukti hadir di lokasi PKL.</p>
                                <input type="hidden" name="latitude" x-model="latitude">
                                <input type="hidden" name="longitude" x-model="longitude">
                                <input name="photo" type="file" accept="image/*" capture="environment" class="mt-3 w-full text-sm" required>
                                <textarea name="catatan" rows="2" class="mt-3 w-full rounded-xl border-gray-200" placeholder="Catatan check-in"></textarea>
                                <button class="mt-3 w-full rounded-xl bg-emerald-600 px-4 py-2 text-white font-semibold disabled:opacity-50" @disabled($absensiHariIni?->check_in_at)>Check In</button>
                            </form>
                            <form method="POST" action="{{ route('siswa.jurnal-prakerin.check-out') }}" enctype="multipart/form-data" @submit="syncLocation" class="rounded-xl border border-gray-100 p-4">
                                @csrf
                                <h4 class="font-semibold text-gray-900">Check-out</h4>
                                <p class="mt-1 text-xs text-gray-500">Upload foto kondisi saat ini sebagai bukti selesai kegiatan PKL hari ini.</p>
                                <input type="hidden" name="latitude" x-model="latitude">
                                <input type="hidden" name="longitude" x-model="longitude">
                                <input name="photo" type="file" accept="image/*" capture="environment" class="mt-3 w-full text-sm" required>
                                <textarea name="catatan" rows="2" class="mt-3 w-full rounded-xl border-gray-200" placeholder="Catatan check-out"></textarea>
                                <button class="mt-3 w-full rounded-xl bg-blue-600 px-4 py-2 text-white font-semibold disabled:opacity-50" @disabled(!$absensiHariIni?->check_in_at || $absensiHariIni?->check_out_at)>Check Out</button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 p-6">
                            <div>
                                <h3 class="font-bold text-gray-900">Riwayat Jurnal</h3>
                                <p class="text-sm text-gray-500">Jurnal kegiatan terbaru.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('siswa.jurnal-prakerin.pdf') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Export PDF</a>
                                <a href="{{ route('siswa.jurnal-prakerin.create') }}" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tambah Jurnal</a>
                            </div>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse($jurnals as $jurnal)
                                @php($statusLabel = $jurnal->status_verifikasi === 'disetujui' ? 'Sudah Ditinjau' : Str::title($jurnal->status_verifikasi))
                                <div class="p-5">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                                        <span class="text-xs px-2 py-1 rounded-full {{ ['menunggu'=>'bg-yellow-100 text-yellow-800','disetujui'=>'bg-emerald-100 text-emerald-800','revisi'=>'bg-red-100 text-red-800'][$jurnal->status_verifikasi] ?? 'bg-gray-100 text-gray-700' }}">{{ $statusLabel }}</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-700">{{ $jurnal->kegiatan_dilakukan }}</p>
                                    @if($jurnal->catatan_pembimbing)
                                        <p class="mt-2 text-xs text-red-600">Catatan: {{ $jurnal->catatan_pembimbing }}</p>
                                    @endif
                                </div>
                            @empty
                                <p class="p-8 text-center text-gray-500">Belum ada jurnal yang diisi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-gray-100 p-6">
                        <div>
                            <h3 class="font-bold text-gray-900">Riwayat Absensi Terbaru</h3>
                            <p class="text-sm text-gray-500">Lima absensi terakhir.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('siswa.jurnal-prakerin.absensi') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Lihat Semua</a>
                            <a href="{{ route('siswa.jurnal-prakerin.absensi.pdf') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Export PDF</a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check-in</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check-out</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($absensis as $absensi)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $absensi->tanggal?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $absensi->check_in_at ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $absensi->check_out_at ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $absensi->catatan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat absensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white text-center p-12 rounded-2xl shadow-sm">
                    <h3 class="text-xl font-bold text-gray-700">Anda Tidak Terdaftar</h3>
                    <p class="text-gray-500 mt-2">Anda belum termapping ke rombel PKL aktif.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function pklAttendance() {
            return {
                latitude: '',
                longitude: '',
                locationText: 'Lokasi belum diambil.',
                init() {
                    this.detectLocation();
                },
                get osmUrl() {
                    return this.latitude && this.longitude
                        ? `https://www.openstreetmap.org/?mlat=${this.latitude}&mlon=${this.longitude}#map=18/${this.latitude}/${this.longitude}`
                        : '#';
                },
                get mapEmbedUrl() {
                    if (!this.latitude || !this.longitude) return '';
                    const lat = Number(this.latitude);
                    const lng = Number(this.longitude);
                    const delta = 0.003;
                    const bbox = [lng - delta, lat - delta, lng + delta, lat + delta].join(',');
                    return `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}`;
                },
                detectLocation() {
                    if (!navigator.geolocation) {
                        this.locationText = 'Browser tidak mendukung GPS.';
                        return;
                    }
                    this.locationText = 'Mengambil lokasi...';
                    navigator.geolocation.getCurrentPosition((pos) => {
                        this.latitude = pos.coords.latitude.toFixed(7);
                        this.longitude = pos.coords.longitude.toFixed(7);
                        this.locationText = `Lokasi siap: ${this.latitude}, ${this.longitude}`;
                    }, () => {
                        this.locationText = 'Izin lokasi ditolak. Absensi membutuhkan lokasi aktif.';
                    }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
                },
                syncLocation(event) {
                    if (!this.latitude || !this.longitude) {
                        event.preventDefault();
                        this.detectLocation();
                        alert('Ambil dan izinkan lokasi GPS terlebih dahulu sebelum absensi.');
                    }
                }
            }
        }
    </script>
</x-app-layout>
