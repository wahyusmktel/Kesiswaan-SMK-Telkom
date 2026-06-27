<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Tambah Jurnal PKL</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Jurnal Kegiatan Harian</h3>
                    <p class="text-sm text-gray-500">{{ $penempatan->industri?->nama_industri ?? '-' }} - {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</p>
                </div>
                <a href="{{ route('siswa.jurnal-prakerin.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
            </div>

            <div class="grid gap-6 xl:grid-cols-[0.75fr_1.25fr]">
                <div class="space-y-4">
                    <div class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-900">
                        <p class="font-bold">Panduan pengisian jurnal</p>
                        <p class="mt-1">Jurnal hanya bisa disimpan jika Anda sudah check-in absensi pada tanggal yang dipilih. Isi kegiatan dengan jelas agar pembimbing mudah melakukan verifikasi.</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                        <h4 class="font-bold text-gray-900">Status Hari Ini</h4>
                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <p><span class="font-semibold text-gray-800">Tanggal:</span> {{ \Carbon\Carbon::parse($today)->format('d M Y') }}</p>
                            <p><span class="font-semibold text-gray-800">Check-in:</span> {{ $absensiHariIni?->check_in_at ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">Check-out:</span> {{ $absensiHariIni?->check_out_at ?? '-' }}</p>
                            <p><span class="font-semibold text-gray-800">Jurnal hari ini:</span> {{ $jurnalHariIni ? 'Sudah dibuat' : 'Belum dibuat' }}</p>
                        </div>
                    </div>
                    @if($setting?->instruksi_jurnal)
                        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                            <h4 class="font-bold text-gray-900">Instruksi Sekolah</h4>
                            <p class="mt-2 text-sm text-gray-600">{{ $setting->instruksi_jurnal }}</p>
                        </div>
                    @endif
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="border-b border-gray-100 p-6">
                        <h4 class="font-bold text-gray-900">Form Jurnal</h4>
                        <p class="text-sm text-gray-500">Gunakan tanggal hari ini setelah melakukan check-in, atau pilih tanggal lain yang sudah memiliki check-in.</p>
                    </div>
                    <form method="POST" action="{{ route('siswa.jurnal-prakerin.store') }}" enctype="multipart/form-data" class="space-y-5 p-6">
                        @csrf
                        @if($errors->any())
                            <div class="rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-800">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <label class="block space-y-1">
                            <span class="text-sm font-semibold text-gray-700">Tanggal jurnal</span>
                            <input type="date" name="tanggal" value="{{ old('tanggal', $today) }}" class="w-full rounded-xl border-gray-200" required>
                        </label>
                        <label class="block space-y-1">
                            <span class="text-sm font-semibold text-gray-700">Kegiatan dilakukan</span>
                            <textarea name="kegiatan_dilakukan" rows="7" class="w-full rounded-xl border-gray-200" placeholder="Tuliskan kegiatan utama, alat/aplikasi yang digunakan, dan hasil pekerjaan hari ini." required>{{ old('kegiatan_dilakukan') }}</textarea>
                        </label>
                        <label class="block space-y-1">
                            <span class="text-sm font-semibold text-gray-700">Kompetensi yang didapat</span>
                            <textarea name="kompetensi_yang_didapat" rows="5" class="w-full rounded-xl border-gray-200" placeholder="Tuliskan kemampuan, materi, atau pengalaman kerja yang didapat hari ini." required>{{ old('kompetensi_yang_didapat') }}</textarea>
                        </label>
                        <label class="block space-y-1">
                            <span class="text-sm font-semibold text-gray-700">Foto kegiatan</span>
                            <input type="file" name="foto_kegiatan" accept="image/*" class="w-full text-sm">
                            <span class="text-xs text-gray-500">Format gambar, maksimal 10 MB.</span>
                        </label>
                        <div class="flex flex-wrap justify-end gap-2 border-t border-gray-100 pt-5">
                            <a href="{{ route('siswa.jurnal-prakerin.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                            <button class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Simpan Jurnal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
