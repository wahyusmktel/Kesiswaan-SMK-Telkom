<x-app-layout>
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animate-gradient { background-size: 200% 200%; animation: gradient-xy 6s ease infinite; }

            @keyframes pulse-ring {
                0% { transform: scale(0.9); opacity: 1; }
                80%, 100% { transform: scale(1.3); opacity: 0; }
            }
            .badge-lulus { @apply bg-green-100 text-green-700 border border-green-200; }
            .badge-tidak-lulus { @apply bg-red-100 text-red-700 border border-red-200; }
            .badge-belum { @apply bg-gray-100 text-gray-500 border border-gray-200; }

            .status-toggle { transition: all 0.2s; }
            .status-toggle:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,0.3); }

            .table-row-hover:hover { background-color: #f8faff; }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengumuman Kelulusan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-3 rounded-xl text-sm font-semibold shadow-sm">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl text-sm shadow-sm">
                    <p class="font-bold mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Terdapat kesalahan pada form:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-red-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Header Banner --}}
            <div class="relative rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-600 shadow-lg overflow-hidden p-7 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="text-white">
                        <p class="text-emerald-100 text-sm font-semibold uppercase tracking-widest mb-1">Waka Kurikulum</p>
                        <h3 class="text-2xl font-extrabold">Manajemen Pengumuman Kelulusan</h3>
                        <p class="text-emerald-50 mt-1 text-sm">
                            Kelola data kelulusan siswa kelas XII dan atur jadwal publikasi pengumuman.
                        </p>
                    </div>
                    <div class="flex gap-3 flex-shrink-0">
                        @if($pengumuman)
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold shadow
                                {{ $pengumuman->sudahDipublikasikan() ? 'bg-white text-emerald-700' : 'bg-white/20 text-white border border-white/30' }}">
                                @if($pengumuman->sudahDipublikasikan())
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    </span>
                                    Sudah Dipublikasikan
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Terjadwal
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                {{-- ============ KOLOM KIRI: Form Pengaturan ============ --}}
                <div class="xl:col-span-1 space-y-5">

                    {{-- Card Form Pengumuman --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800">Pengaturan Pengumuman</h4>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('kurikulum.pengumuman-kelulusan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tahun Pelajaran</label>
                                    <select name="tahun_pelajaran_id" required
                                        class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($tahunPelajaranList as $tp)
                                            <option value="{{ $tp->id }}"
                                                {{ ($pengumuman && $pengumuman->tahun_pelajaran_id == $tp->id) ? 'selected' : ($tahunAktif && $tahunAktif->id == $tp->id ? 'selected' : '') }}>
                                                {{ $tp->tahun }} - Semester {{ $tp->semester }}
                                                {{ $tp->is_active ? '(Aktif)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Judul Pengumuman</label>
                                    <input type="text" name="judul" required
                                        value="{{ $pengumuman->judul ?? 'Pengumuman Kelulusan Siswa' }}"
                                        placeholder="Contoh: Pengumuman Kelulusan Siswa"
                                        class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Keterangan</label>
                                    <textarea name="keterangan" rows="3"
                                        placeholder="Tambahkan keterangan atau pesan untuk siswa..."
                                        class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">{{ $pengumuman->keterangan ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                        Waktu Publikasi
                                        <span class="ml-1 font-normal text-gray-400 lowercase">(countdown akan tampil di halaman siswa)</span>
                                    </label>
                                    <input type="datetime-local" name="waktu_publikasi" required
                                        value="{{ $pengumuman ? $pengumuman->waktu_publikasi->format('Y-m-d\TH:i') : '' }}"
                                        class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                {{-- Toggle Download SKL --}}
                                <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-colors
                                    {{ ($pengumuman && $pengumuman->skl_aktif) ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}"
                                    id="skl-toggle-wrap">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">Download SKL</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            Izinkan siswa download Surat Keterangan Lulus
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 ml-3">
                                        <input type="checkbox" name="skl_aktif" value="1" id="skl_aktif_toggle"
                                            class="sr-only peer"
                                            {{ ($pengumuman && $pengumuman->skl_aktif) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-400 rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-emerald-500"></div>
                                    </label>
                                </div>

                                {{-- ===== KONFIGURASI SKL ===== --}}
                                <div class="border-t border-gray-100 pt-4 mt-2">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Konfigurasi Format SKL
                                    </p>

                                    {{-- Kop Surat --}}
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                            Kop Surat <span class="font-normal text-gray-400 lowercase">(JPG/PNG, lebar penuh)</span>
                                        </label>
                                        @if($pengumuman && $pengumuman->kop_surat_path)
                                            <div class="mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                                <img src="{{ asset('storage/' . $pengumuman->kop_surat_path) }}" class="h-10 object-contain rounded" alt="Kop Surat">
                                                <p class="text-[10px] text-gray-400 mt-1">Upload baru untuk mengganti</p>
                                            </div>
                                        @endif
                                        <input type="file" name="kop_surat" accept="image/jpeg,image/png"
                                            class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl cursor-pointer">
                                    </div>

                                    {{-- Nomor Surat --}}
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                                Prefix Nomor Surat
                                            </label>
                                            <input type="text" name="nomor_surat_prefix"
                                                value="{{ old('nomor_surat_prefix', $pengumuman->nomor_surat_prefix ?? '') }}"
                                                placeholder="cth: SMKTEL-LPG/KURL.15"
                                                class="w-full rounded-xl border-gray-200 text-xs font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                            <p class="text-[10px] text-gray-400 mt-1">0425/<strong>prefix</strong>/V/2026</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                                Nomor Awal
                                            </label>
                                            <input type="text" name="nomor_surat_start" inputmode="numeric" pattern="[0-9]+"
                                                value="{{ old('nomor_surat_start', str_pad($pengumuman->nomor_surat_start ?? 1, 4, '0', STR_PAD_LEFT)) }}"
                                                placeholder="0001"
                                                class="w-full rounded-xl border-gray-200 text-xs font-medium font-mono focus:ring-indigo-500 focus:border-indigo-500">
                                            <p class="text-[10px] text-gray-400 mt-1">cth: <strong>0359</strong> → nomor pertama dicetak <strong>0359</strong></p>
                                        </div>
                                    </div>

                                    {{-- Kota & Tanggal Surat --}}
                                    <div class="grid grid-cols-2 gap-2 mb-3">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Kota</label>
                                            <input type="text" name="kota_surat"
                                                value="{{ old('kota_surat', $pengumuman->kota_surat ?? '') }}"
                                                placeholder="cth: Lampung"
                                                class="w-full rounded-xl border-gray-200 text-xs font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Tanggal Surat</label>
                                            <input type="date" name="tanggal_surat"
                                                value="{{ old('tanggal_surat', $pengumuman?->tanggal_surat?->format('Y-m-d') ?? '') }}"
                                                class="w-full rounded-xl border-gray-200 text-xs font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>

                                    {{-- Kepala Sekolah --}}
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Nama Kepala Sekolah</label>
                                        <input type="text" name="nama_kepala_sekolah"
                                            value="{{ old('nama_kepala_sekolah', $pengumuman->nama_kepala_sekolah ?? '') }}"
                                            placeholder="cth: Drs. Ahmad Fauzi, M.Pd."
                                            class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">NIP Kepala Sekolah</label>
                                        <input type="text" name="nip_kepala_sekolah"
                                            value="{{ old('nip_kepala_sekolah', $pengumuman->nip_kepala_sekolah ?? '') }}"
                                            placeholder="cth: 196804151994031002"
                                            class="w-full rounded-xl border-gray-200 text-sm font-medium focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>

                                    {{-- Tanda Tangan + Stempel --}}
                                    <div>
                                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">
                                            Tanda Tangan &amp; Stempel <span class="font-normal text-gray-400 lowercase">(PNG transparan lebih baik)</span>
                                        </label>
                                        @if($pengumuman && $pengumuman->ttd_stempel_path)
                                            <div class="mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200 flex items-center gap-3">
                                                <img src="{{ asset('storage/' . $pengumuman->ttd_stempel_path) }}" class="h-12 object-contain rounded" alt="TTD Stempel">
                                                <p class="text-[10px] text-gray-400">Upload baru untuk mengganti</p>
                                            </div>
                                        @endif
                                        <input type="file" name="ttd_stempel" accept="image/jpeg,image/png"
                                            class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl cursor-pointer">
                                    </div>
                                </div>
                                {{-- ===== END KONFIGURASI SKL ===== --}}

                                <button type="submit"
                                    class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-sm hover:shadow-md">
                                    Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Card Statistik --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="font-bold text-gray-800">Statistik</h4>
                        </div>
                        <div class="p-6 grid grid-cols-3 gap-3">
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-2xl font-black text-gray-800">{{ $totalSiswa }}</p>
                                <p class="text-[11px] font-semibold text-gray-500 mt-0.5">Total Siswa</p>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-xl">
                                <p class="text-2xl font-black text-green-700">{{ $totalLulus }}</p>
                                <p class="text-[11px] font-semibold text-green-600 mt-0.5">Lulus</p>
                            </div>
                            <div class="text-center p-3 bg-red-50 rounded-xl">
                                <p class="text-2xl font-black text-red-700">{{ $totalTidakLulus }}</p>
                                <p class="text-[11px] font-semibold text-red-500 mt-0.5">Tidak Lulus</p>
                            </div>
                        </div>
                        @if($pengumuman)
                            <div class="px-6 pb-5 space-y-2">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Aksi Bulk</p>
                                <form action="{{ route('kurikulum.pengumuman-kelulusan.bulk-status') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="pengumuman_kelulusan_id" value="{{ $pengumuman->id }}">
                                    <input type="hidden" name="status" value="lulus">
                                    <button type="submit" onclick="return confirm('Tandai SEMUA siswa kelas XII sebagai LULUS?')"
                                        class="flex-1 py-2 px-3 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition">
                                        Semua Lulus
                                    </button>
                                </form>
                                <form action="{{ route('kurikulum.pengumuman-kelulusan.bulk-status') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="pengumuman_kelulusan_id" value="{{ $pengumuman->id }}">
                                    <input type="hidden" name="status" value="tidak_lulus">
                                    <button type="submit" onclick="return confirm('Tandai SEMUA siswa kelas XII sebagai TIDAK LULUS?')"
                                        class="flex-1 py-2 px-3 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition">
                                        Semua Tidak Lulus
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    {{-- Info Publikasi --}}
                    @if($pengumuman)
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 p-5">
                            <h4 class="font-bold text-indigo-900 text-sm mb-3">Info Publikasi</h4>
                            <ul class="space-y-2 text-sm">
                                <li class="flex justify-between gap-2">
                                    <span class="text-indigo-700 font-medium">Waktu Publikasi</span>
                                    <span class="font-bold text-indigo-900 text-right">
                                        {{ $pengumuman->waktu_publikasi->translatedFormat('l, d F Y') }}<br>
                                        <span class="text-indigo-600 font-mono">{{ $pengumuman->waktu_publikasi->format('H:i') }} WIB</span>
                                    </span>
                                </li>
                                <li class="flex justify-between gap-2">
                                    <span class="text-indigo-700 font-medium">Status</span>
                                    <span class="font-bold {{ $pengumuman->sudahDipublikasikan() ? 'text-green-700' : 'text-amber-700' }}">
                                        {{ $pengumuman->sudahDipublikasikan() ? 'Sudah Dipublikasikan' : 'Menunggu Publikasi' }}
                                    </span>
                                </li>
                                <li class="flex justify-between gap-2">
                                    <span class="text-indigo-700 font-medium">Download SKL</span>
                                    @if($pengumuman->skl_aktif)
                                        <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 font-bold text-gray-400">
                                            <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- ============ KOLOM KANAN: Tabel Siswa ============ --}}
                <div class="xl:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-gray-800">Daftar Siswa Kelas XII</h4>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $totalSiswa }} siswa tercatat</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" id="searchSiswa" placeholder="Cari nama siswa..."
                                    class="rounded-lg border-gray-200 text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500 w-44">
                                <select id="filterKelas"
                                    class="rounded-lg border-gray-200 text-sm py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Semua Kelas</option>
                                    @foreach($siswaDaftarList->pluck('kelas')->unique()->sort() as $namaKelas)
                                        <option value="{{ $namaKelas }}">{{ $namaKelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($pengumuman)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide w-10">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Siswa</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wide kelas-col">Kelas</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wide">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wide">SKL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50" id="siswaTableBody">
                                    @forelse($siswaDaftarList as $index => $item)
                                        @php
                                            $currentStatus = $statusMap[$item['siswa']->id] ?? null;
                                        @endphp
                                        <tr class="table-row-hover transition-colors" data-nama="{{ strtolower($item['siswa']->nama_lengkap) }}" data-kelas="{{ $item['kelas'] }}">
                                            <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-800">{{ $item['siswa']->nama_lengkap }}</div>
                                                <div class="text-xs text-gray-400 font-mono">{{ $item['siswa']->nis }}</div>
                                            </td>
                                            <td class="px-4 py-3 kelas-col">
                                                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-1 rounded-lg">
                                                    {{ $item['kelas'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-1.5" id="toggle-{{ $item['siswa']->id }}">
                                                    <button onclick="updateStatus({{ $pengumuman->id }}, {{ $item['siswa']->id }}, 'lulus')"
                                                        class="status-toggle px-3 py-1.5 rounded-lg text-xs font-bold border transition-all
                                                            {{ $currentStatus === 'lulus' ? 'bg-green-500 text-white border-green-500 shadow-sm' : 'bg-white text-gray-400 border-gray-200 hover:border-green-300 hover:text-green-600' }}"
                                                        id="btn-lulus-{{ $item['siswa']->id }}">
                                                        Lulus
                                                    </button>
                                                    <button onclick="updateStatus({{ $pengumuman->id }}, {{ $item['siswa']->id }}, 'tidak_lulus')"
                                                        class="status-toggle px-3 py-1.5 rounded-lg text-xs font-bold border transition-all
                                                            {{ $currentStatus === 'tidak_lulus' ? 'bg-red-500 text-white border-red-500 shadow-sm' : 'bg-white text-gray-400 border-gray-200 hover:border-red-300 hover:text-red-600' }}"
                                                        id="btn-tidak-lulus-{{ $item['siswa']->id }}">
                                                        Tdk Lulus
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($currentStatus === 'lulus')
                                                    <a href="{{ route('kurikulum.pengumuman-kelulusan.download-skl', [$pengumuman->id, $item['siswa']->id]) }}"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition"
                                                        target="_blank">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                        </svg>
                                                        SKL
                                                    </a>
                                                @else
                                                    <span class="text-gray-300 text-xs">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-16 text-center">
                                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                                    <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                    <p class="font-semibold text-sm">Tidak ada siswa kelas XII ditemukan.</p>
                                                    <p class="text-xs">Pastikan data rombel kelas XII sudah diinput untuk tahun pelajaran aktif.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-14 h-14 opacity-25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-bold text-base text-gray-500">Pengumuman belum dibuat</p>
                                    <p class="text-sm text-gray-400">Silakan isi form pengaturan di sebelah kiri untuk membuat pengumuman kelulusan.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toggle SKL visual feedback
            const sklToggle = document.getElementById('skl_aktif_toggle');
            const sklWrap   = document.getElementById('skl-toggle-wrap');
            if (sklToggle && sklWrap) {
                sklToggle.addEventListener('change', function () {
                    if (this.checked) {
                        sklWrap.classList.remove('bg-gray-50', 'border-gray-200');
                        sklWrap.classList.add('bg-emerald-50', 'border-emerald-200');
                    } else {
                        sklWrap.classList.remove('bg-emerald-50', 'border-emerald-200');
                        sklWrap.classList.add('bg-gray-50', 'border-gray-200');
                    }
                });
            }

            // Live search & filter tabel
            const searchInput = document.getElementById('searchSiswa');
            const filterKelas = document.getElementById('filterKelas');
            const rows = document.querySelectorAll('#siswaTableBody tr[data-nama]');

            function filterTable() {
                const q = searchInput.value.toLowerCase();
                const kelas = filterKelas.value;
                rows.forEach(row => {
                    const namaMatch = row.dataset.nama.includes(q);
                    const kelasMatch = !kelas || row.dataset.kelas === kelas;
                    row.style.display = (namaMatch && kelasMatch) ? '' : 'none';
                });
            }

            searchInput?.addEventListener('input', filterTable);
            filterKelas?.addEventListener('change', filterTable);

            // Update status via AJAX
            function updateStatus(pengumumanId, siswaId, status) {
                const btnLulus = document.getElementById(`btn-lulus-${siswaId}`);
                const btnTidakLulus = document.getElementById(`btn-tidak-lulus-${siswaId}`);

                fetch('{{ route('kurikulum.pengumuman-kelulusan.update-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        pengumuman_kelulusan_id: pengumumanId,
                        master_siswa_id: siswaId,
                        status: status,
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (status === 'lulus') {
                            btnLulus.className = btnLulus.className.replace('bg-white text-gray-400 border-gray-200 hover:border-green-300 hover:text-green-600', 'bg-green-500 text-white border-green-500 shadow-sm');
                            btnTidakLulus.className = btnTidakLulus.className.replace('bg-red-500 text-white border-red-500 shadow-sm', 'bg-white text-gray-400 border-gray-200 hover:border-red-300 hover:text-red-600');
                        } else {
                            btnTidakLulus.className = btnTidakLulus.className.replace('bg-white text-gray-400 border-gray-200 hover:border-red-300 hover:text-red-600', 'bg-red-500 text-white border-red-500 shadow-sm');
                            btnLulus.className = btnLulus.className.replace('bg-green-500 text-white border-green-500 shadow-sm', 'bg-white text-gray-400 border-gray-200 hover:border-green-300 hover:text-green-600');
                        }

                        // Reload untuk update kolom SKL
                        setTimeout(() => window.location.reload(), 800);
                    }
                })
                .catch(() => alert('Terjadi kesalahan. Silakan coba lagi.'));
            }
        </script>
    @endpush
</x-app-layout>
