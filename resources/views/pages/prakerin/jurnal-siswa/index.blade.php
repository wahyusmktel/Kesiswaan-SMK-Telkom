<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Jurnal & Absensi PKL</h2></x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if ($penempatan)
            <div class="grid gap-6 lg:grid-cols-[360px_1fr]">
                <div class="space-y-6">
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                        <h3 class="font-bold text-gray-900">Informasi PKL</h3>
                        <div class="mt-4 text-sm text-gray-600 space-y-2">
                            <p><strong>Industri:</strong> {{ $penempatan->industri?->nama_industri }}</p>
                            <p><strong>Rombel:</strong> {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</p>
                            <p><strong>Pembimbing Sekolah:</strong> {{ $penempatan->guruPembimbing?->nama_lengkap }}</p>
                            <p><strong>Pembimbing Industri:</strong> {{ $penempatan->nama_pembimbing_industri }}</p>
                        </div>
                        @if($setting?->instruksi_jurnal)
                            <div class="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">{{ $setting->instruksi_jurnal }}</div>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6" x-data="pklAttendance()">
                        <h3 class="font-bold text-gray-900">Absensi Hari Ini</h3>
                        <p class="mt-1 text-sm text-gray-500">Aktifkan izin lokasi. Lokasi memakai GPS browser dan dapat dicek melalui OpenStreetMap.</p>
                        <button type="button" @click="detectLocation" class="mt-4 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold">Ambil Lokasi GPS</button>
                        <p class="mt-3 text-xs text-gray-500" x-text="locationText"></p>
                        <template x-if="latitude && longitude">
                            <a class="mt-2 inline-block text-xs font-semibold text-blue-600" target="_blank" :href="`https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}#map=18/${latitude}/${longitude}`">Lihat lokasi di OpenStreetMap</a>
                        </template>
                        <div class="mt-4 grid gap-3">
                            <form method="POST" action="{{ route('siswa.jurnal-prakerin.check-in') }}" enctype="multipart/form-data" @submit="syncLocation">
                                @csrf
                                <input type="hidden" name="latitude" x-model="latitude"><input type="hidden" name="longitude" x-model="longitude">
                                <input name="photo" type="file" accept="image/*" capture="environment" class="mb-2 w-full text-sm" required>
                                <textarea name="catatan" rows="2" class="mb-2 w-full rounded-xl border-gray-200" placeholder="Catatan check-in"></textarea>
                                <button class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-white font-semibold" @disabled($absensiHariIni?->check_in_at)>Check In</button>
                            </form>
                            <form method="POST" action="{{ route('siswa.jurnal-prakerin.check-out') }}" enctype="multipart/form-data" @submit="syncLocation">
                                @csrf
                                <input type="hidden" name="latitude" x-model="latitude"><input type="hidden" name="longitude" x-model="longitude">
                                <input name="photo" type="file" accept="image/*" capture="environment" class="mb-2 w-full text-sm" required>
                                <textarea name="catatan" rows="2" class="mb-2 w-full rounded-xl border-gray-200" placeholder="Catatan check-out"></textarea>
                                <button class="w-full rounded-xl bg-blue-600 px-4 py-2 text-white font-semibold" @disabled(!$absensiHariIni?->check_in_at || $absensiHariIni?->check_out_at)>Check Out</button>
                            </form>
                        </div>
                        <div class="mt-4 rounded-xl bg-gray-50 p-3 text-xs text-gray-600">
                            Check-in: {{ $absensiHariIni?->check_in_at ?? '-' }} | Check-out: {{ $absensiHariIni?->check_out_at ?? '-' }}
                        </div>
                    </div>

                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Tambah Jurnal</h3>
                        <form method="POST" action="{{ route('siswa.jurnal-prakerin.store') }}" enctype="multipart/form-data" class="space-y-4">@csrf
                            <input type="hidden" name="prakerin_penempatan_id" value="{{ $penempatan->id }}">
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full rounded-xl border-gray-200" required>
                            <textarea name="kegiatan_dilakukan" rows="4" class="w-full rounded-xl border-gray-200" placeholder="Aktivitas kegiatan harian" required>{{ old('kegiatan_dilakukan') }}</textarea>
                            <textarea name="kompetensi_yang_didapat" rows="3" class="w-full rounded-xl border-gray-200" placeholder="Kompetensi yang didapat" required>{{ old('kompetensi_yang_didapat') }}</textarea>
                            <input type="file" name="foto_kegiatan" accept="image/*" class="w-full text-sm">
                            <p class="text-xs text-gray-500">Format gambar, maksimal 10 MB.</p>
                            <button class="w-full rounded-xl bg-red-600 px-4 py-2 text-white font-semibold">Simpan Jurnal</button>
                        </form>
                    </div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Riwayat Jurnal</h3>
                    <div class="space-y-4">@forelse($jurnals as $jurnal)<div class="rounded-xl border border-gray-100 p-4"><div class="flex justify-between gap-3"><p class="font-bold">{{ \Carbon\Carbon::parse($jurnal->tanggal)->isoFormat('dddd, D MMMM Y') }}</p><span class="text-xs px-2 py-1 rounded-full {{ ['menunggu'=>'bg-yellow-100 text-yellow-800','disetujui'=>'bg-green-100 text-green-800','revisi'=>'bg-red-100 text-red-800'][$jurnal->status_verifikasi] }}">{{ Str::title($jurnal->status_verifikasi) }}</span></div><p class="mt-2 text-sm text-gray-700">{{ $jurnal->kegiatan_dilakukan }}</p>@if($jurnal->catatan_pembimbing)<p class="mt-2 text-xs text-red-600">Catatan: {{ $jurnal->catatan_pembimbing }}</p>@endif</div>@empty<p class="text-center text-gray-500">Belum ada jurnal yang diisi.</p>@endforelse</div>
                    <div class="mt-4">{{ $jurnals->links() }}</div>
                </div>
            </div>
        @else
            <div class="bg-white text-center p-12 rounded-2xl shadow-sm"><h3 class="text-xl font-bold text-gray-700">Anda Tidak Terdaftar</h3><p class="text-gray-500 mt-2">Anda belum termapping ke rombel PKL aktif.</p></div>
        @endif
    </div></div>
    <script>
        function pklAttendance() {
            return {
                latitude: '',
                longitude: '',
                locationText: 'Lokasi belum diambil.',
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
