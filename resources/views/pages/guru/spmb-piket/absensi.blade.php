<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Absensi Piket SPMB Pegawai</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="guruAbsensiPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- FLASH NOTIFICATIONS --}}
            @if(session('success'))
                <div class="flex items-center justify-between p-4 text-xs font-medium text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-4 text-xs font-medium text-red-800 bg-red-50 rounded-lg border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            {{-- REPLACEMENT TEACHER BANNER --}}
            @if($isPenggantiOnSelected && $penggantiInfo)
                <div class="p-4 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 shadow-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="text-xs">
                        <p class="font-bold text-amber-950">Penugasan Sebagai Guru Pengganti</p>
                        <p class="mt-0.5">
                            Anda terdaftar sebagai <strong>Guru Pengganti</strong> untuk <strong>{{ $penggantiInfo->nama_pengganti }}</strong> (menggantikan pegawai asli: <em>{{ $penggantiInfo->petugas?->nama_petugas ?? 'Petugas Utama' }}</em>) pada tanggal piket ini dengan alasan: "<em>{{ $penggantiInfo->alasan ?? 'Penugasan Pengganti' }}</em>". Anda memiliki wewenang penuh untuk melakukan absensi, presensi siswa, dan mengunggah laporan fisik.
                        </p>
                    </div>
                </div>
            @endif

            {{-- OFFICER INFORMATION CARD --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wider">
                                    Petugas Piket SPMB
                                </span>
                                @if($isPenggantiOnSelected)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Petugas Pengganti
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mt-1">{{ $activePetugas->nama_petugas ?? $user->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $assignedKelompok ? $assignedKelompok->nama_kelompok . ' (Waktu: ' . ($assignedKelompok->waktu ?: '07.30 - 12.00') . ')' : 'Petugas Penugasan SPMB' }}
                            </p>
                        </div>
                    </div>

                    {{-- Next / Today Schedule Badge --}}
                    <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div class="w-9 h-9 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                {{ $jadwalHariIni ? 'Tugas Piket Hari Ini' : 'Jadwal Tugas Terdekat' }}
                            </p>
                            <p class="text-xs font-bold text-slate-800">
                                @if($jadwalHariIni)
                                    <span class="text-emerald-600 font-black">HARI INI (Sabtu, {{ \Carbon\Carbon::parse($jadwalHariIni->tanggal)->locale('id')->translatedFormat('d M Y') }})</span>
                                @elseif($jadwalBerikutnya)
                                    Sabtu, {{ \Carbon\Carbon::parse($jadwalBerikutnya->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                @else
                                    Tidak ada jadwal mendatang
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- FORM INPUT ABSENSI MANDIRI PEGAWAI (7 cols) --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 space-y-5">
                        <div class="border-b border-slate-200 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Form Presensi Kehadiran Piket SPMB
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Isi status kehadiran dan lampirkan foto bukti eviden bertugas di lokasi piket.</p>
                        </div>

                        @if(! $activePetugas && ! $isOfficer)
                            <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800">
                                Akun Anda belum terdaftar dalam daftar petugas piket SPMB. Hubungi Kaur SPMB jika Anda telah ditugaskan.
                            </div>
                        @else
                            <form action="{{ route('guru.spmb-piket.absensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="spmb_piket_petugas_id" value="{{ $activePetugas?->id }}">

                                {{-- Tanggal Piket --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Tanggal Piket Hari Sabtu <span class="text-red-500">*</span>
                                    </label>
                                    <select name="spmb_piket_tanggal_id" required class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                        @if($jadwalHariIni)
                                            <option value="{{ $jadwalHariIni->id }}" selected>
                                                ⭐ HARI INI - Sabtu, {{ \Carbon\Carbon::parse($jadwalHariIni->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                            </option>
                                        @endif
                                        @foreach($tanggalsBelumAbsen as $tgl)
                                            @if(!$jadwalHariIni || $jadwalHariIni->id !== $tgl->id)
                                                <option value="{{ $tgl->id }}">
                                                    Sabtu, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }} ({{ $tgl->waktu ?: '07.30 - 12.00' }})
                                                </option>
                                            @endif
                                        @endforeach
                                        @foreach($allTanggals as $tgl)
                                            @if(in_array($tgl->id, $sudahAbsenTanggalIds))
                                                <option value="{{ $tgl->id }}" class="text-slate-400">
                                                    Sabtu, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }} (Sudah Diisi - Perbarui)
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-400 mt-1">Daftar hari Sabtu yang ditugaskan untuk Anda.</p>
                                </div>

                                {{-- Status Kehadiran --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Status Kehadiran <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-3 gap-2.5">
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Hadir' ? 'bg-emerald-50 border-emerald-400 text-emerald-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Hadir" x-model="formStatus" class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-xs">Hadir</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Izin' ? 'bg-amber-50 border-amber-400 text-amber-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Izin" x-model="formStatus" class="text-amber-600 focus:ring-amber-500">
                                            <span class="text-xs">Izin</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Sakit' ? 'bg-rose-50 border-rose-400 text-rose-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Sakit" x-model="formStatus" class="text-rose-600 focus:ring-rose-500">
                                            <span class="text-xs">Sakit</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Foto Eviden Kehadiran --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Foto Bukti Kehadiran (Eviden Pegawai)
                                        <span class="text-red-500" x-show="formStatus === 'Hadir'">*</span>
                                    </label>
                                    
                                    <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-slate-300 border-dashed rounded-lg hover:border-red-400 transition-colors bg-slate-50">
                                        <div class="space-y-1 text-center">
                                            <template x-if="!imagePreview">
                                                <svg class="mx-auto h-9 w-9 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </template>

                                            <template x-if="imagePreview">
                                                <div class="relative inline-block mb-2">
                                                    <img :src="imagePreview" class="h-32 w-auto rounded-lg object-contain shadow-sm border border-slate-200">
                                                    <button type="button" @click="clearImage()"
                                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <div class="flex text-xs text-slate-600 justify-center">
                                                <label class="relative cursor-pointer bg-white rounded-md font-semibold text-red-600 hover:text-red-500 focus-within:outline-none">
                                                    <span>Ambil / Pilih Foto Eviden</span>
                                                    <input type="file" name="foto_eviden" accept="image/*" capture="environment" @change="previewFile($event)" class="sr-only" x-ref="fileInput">
                                                </label>
                                            </div>
                                            <p class="text-[10px] text-slate-400">
                                                Foto selfie di ruang piket SPMB / foto bukti surat dokter jika sakit (Maks. 5 MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kehadiran (Opsional)</label>
                                    <textarea name="keterangan" rows="2" placeholder="Tuliskan catatan aktivitas pribadi atau keterangan izin/sakit..."
                                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Kirim Absensi Kehadiran
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- RIWAYAT ABSENSI PETUGAS (5 cols) --}}
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3 border-b border-slate-200 pb-2.5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Presensi Anda ({{ $historyAbsensi->count() }})
                            </h3>
                        </div>

                        @if($historyAbsensi->isEmpty())
                            <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-md border border-dashed border-slate-200">
                                Anda belum memiliki riwayat presensi piket SPMB.
                            </div>
                        @else
                            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                                @foreach($historyAbsensi as $hist)
                                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white hover:border-slate-300 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800">
                                                Sabtu, {{ $hist->tanggal ? \Carbon\Carbon::parse($hist->tanggal->tanggal)->locale('id')->translatedFormat('d M Y') : '-' }}
                                            </span>
                                            
                                            @if($hist->status === 'Hadir')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Hadir
                                                </span>
                                            @elseif($hist->status === 'Izin')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Izin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Sakit
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                            <span>Waktu Absen: <strong>{{ $hist->formatted_jam_absen }} WIB</strong></span>
                                            
                                            @if($hist->foto_eviden)
                                                <button type="button" @click="openImageModal('{{ $hist->foto_eviden_url }}', 'Sabtu, {{ $hist->tanggal?->formatted_tanggal }}')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat Foto
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- DAFTAR CALON SISWA & ORANG TUA SELEKSI PEKAN INI         --}}
            {{-- ======================================================== --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/60">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                                <h3 class="text-sm font-bold text-slate-900">Daftar Calon Siswa &amp; Orang Tua Seleksi</h3>
                                @if($selectedTanggal)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                        Sabtu, {{ \Carbon\Carbon::parse($selectedTanggal->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Data peserta yang dijadwalkan hadir seleksi. Lakukan presensi digital dan cetak lembar tanda tangan manual.
                            </p>
                        </div>

                        {{-- Action Buttons Toolbar --}}
                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Tanggal Selector --}}
                            @if($allTanggals->count() > 1)
                                <form method="GET" action="{{ route('guru.spmb-piket.absensi.index') }}" class="flex items-center gap-1.5">
                                    <label for="filterTanggal" class="text-xs font-medium text-slate-600 flex-shrink-0">Pekan:</label>
                                    <select id="filterTanggal" name="tanggal_id" onchange="this.form.submit()"
                                        class="text-xs rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-8 bg-white font-medium shadow-sm">
                                        @foreach($allTanggals as $tgl)
                                            <option value="{{ $tgl->id }}" {{ ($selectedTanggal && $selectedTanggal->id == $tgl->id) ? 'selected' : '' }}>
                                                Sabtu, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            @endif

                            @if($selectedTanggal)
                                {{-- Cetak Hadir Siswa Manual --}}
                                <a href="{{ route('guru.spmb-piket.cetak-hadir-siswa', ['tanggal_id' => $selectedTanggal->id]) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors"
                                    title="Cetak lembar presensi calon siswa bertanda tangan basah pulpen">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak Absen Casis (Fisik)
                                </a>

                                {{-- Cetak Hadir Ortu Manual --}}
                                <a href="{{ route('guru.spmb-piket.cetak-hadir-ortu', ['tanggal_id' => $selectedTanggal->id]) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors"
                                    title="Cetak lembar presensi orang tua bertanda tangan basah pulpen">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    Cetak Absen Ortu (Fisik)
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Stats Summary --}}
                    @if($selectedTanggal && $pesertaSeleksis->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-3 border-t border-slate-200/80">
                            <div class="bg-white p-2.5 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Total Calon Siswa</span>
                                <p class="text-base font-bold text-slate-900">{{ $pesertaSeleksis->count() }} <span class="text-xs font-normal text-slate-500">orang</span></p>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Siswa Hadir</span>
                                <p class="text-base font-bold text-emerald-600">{{ $pesertaSeleksis->where('status_kehadiran_siswa', 'Hadir')->count() }} <span class="text-xs font-normal text-slate-500">siswa</span></p>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Orang Tua Hadir</span>
                                <p class="text-base font-bold text-blue-600">{{ $pesertaSeleksis->where('status_kehadiran_ortu', 'Hadir')->count() }} <span class="text-xs font-normal text-slate-500">ortu</span></p>
                            </div>
                            <div class="bg-white p-2.5 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-500 uppercase font-semibold">Menunggu / Terjadwal</span>
                                <p class="text-base font-bold text-amber-600">{{ $pesertaSeleksis->whereIn('status_kehadiran_siswa', ['Terjadwal', 'Menunggu'])->count() }} <span class="text-xs font-normal text-slate-500">orang</span></p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Calon Siswa</th>
                                <th class="py-3 px-4">Orang Tua / Wali</th>
                                <th class="py-3 px-4 text-center">Status Siswa</th>
                                <th class="py-3 px-4 text-center">Status Ortu</th>
                                <th class="py-3 px-4">Catatan</th>
                                <th class="py-3 px-4 text-center w-28">Presensi Digital</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($pesertaSeleksis as $index => $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 text-center text-slate-500 font-bold">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $item->nama_lengkap }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[10px] px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded border border-slate-200">{{ $item->nomor_pendaftaran }}</span>
                                            <span class="text-[11px] text-slate-500">({{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }})</span>
                                        </div>
                                        @if($item->no_wa_siswa)
                                            <div class="mt-1">
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_wa_siswa) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:text-emerald-800 font-medium">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                    {{ $item->no_wa_siswa }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-slate-900">{{ $item->nama_orang_tua ?: '-' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $item->hubungan_wali ?: 'Orang Tua' }}</div>
                                        @if($item->no_wa_orang_tua)
                                            <div class="mt-1">
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_wa_orang_tua) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:text-emerald-800 font-medium">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                                    {{ $item->no_wa_orang_tua }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($item->status_kehadiran_siswa === 'Hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Hadir
                                            </span>
                                            @if($item->waktu_kehadiran_siswa)
                                                <div class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->waktu_kehadiran_siswa)->format('H:i') }} WIB</div>
                                            @endif
                                        @elseif($item->status_kehadiran_siswa === 'Tidak Hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Tidak Hadir
                                            </span>
                                        @elseif($item->status_kehadiran_siswa === 'Menunggu')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($item->status_kehadiran_ortu === 'Hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Hadir
                                            </span>
                                            @if($item->waktu_kehadiran_ortu)
                                                <div class="text-[9px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->waktu_kehadiran_ortu)->format('H:i') }} WIB</div>
                                            @endif
                                        @elseif($item->status_kehadiran_ortu === 'Tidak Hadir')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Tidak Hadir
                                            </span>
                                        @elseif($item->status_kehadiran_ortu === 'Menunggu')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        {{ $item->catatan ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <button type="button" @click="bukaModalKehadiran({{ json_encode($item) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-slate-700 hover:text-white bg-slate-100 hover:bg-slate-800 rounded border border-slate-300 transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Update
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                        Belum ada calon peserta seleksi yang dijadwalkan pada tanggal piket ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- LAPORAN LAPANGAN & UNGGAH EVIDEN ABSENSI FISIK (MANUAL)   --}}
            {{-- ======================================================== --}}
            @if($selectedTanggal)
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Laporan Pelaksanaan Piket &amp; Unggah Bukti Eviden Presensi Manual Fisik
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Setelah seluruh presensi cetak ditandatangani pulpen, foto dan unggah lembar presensi manual ke sistem. Foto ini akan otomatis terlampir di dokumen resmi Berita Acara Kaur SPMB.
                            </p>
                        </div>
                        @if($selectedTanggal->laporan_petugas_at)
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Laporan Tersimpan
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Oleh {{ $selectedTanggal->laporanPetugasBy?->name ?? 'Petugas' }} ({{ $selectedTanggal->laporan_petugas_at->format('d/m/Y H:i') }} WIB)
                                </p>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('guru.spmb-piket.laporan-eviden') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        <input type="hidden" name="spmb_piket_tanggal_id" value="{{ $selectedTanggal->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Foto Eviden Absen Siswa --}}
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50/70 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-800">
                                        1. Foto Lembar Absensi Fisik Siswa (Manual)
                                    </label>
                                    @if($selectedTanggal->foto_eviden_absen_siswa)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Ada File
                                        </span>
                                    @endif
                                </div>

                                @if($selectedTanggal->foto_eviden_absen_siswa)
                                    <div class="flex items-center gap-3 p-2 bg-white rounded border border-slate-200">
                                        <img src="{{ $selectedTanggal->foto_eviden_absen_siswa_url }}" alt="Eviden Siswa" class="h-16 w-20 object-cover rounded border">
                                        <div class="text-xs space-y-1">
                                            <p class="font-medium text-slate-800">Foto lembar tanda tangan calon siswa</p>
                                            <button type="button" @click="openImageModal('{{ $selectedTanggal->foto_eviden_absen_siswa_url }}', 'Lembar Absensi Manual Calon Siswa')"
                                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Pratinjau Foto Penuh
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        {{ $selectedTanggal->foto_eviden_absen_siswa ? 'Unggah Ulang / Ganti Foto Lembar Casis' : 'Pilih / Foto Lembar Casis' }}
                                    </label>
                                    <input type="file" name="foto_eviden_absen_siswa" accept="image/*" capture="environment"
                                        class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 border border-slate-300 rounded-md bg-white">
                                    <p class="text-[10px] text-slate-400 mt-1">Pastikan tulisan & tanda tangan basah terbaca jelas (Maks. 8 MB).</p>
                                </div>
                            </div>

                            {{-- Foto Eviden Absen Ortu --}}
                            <div class="p-4 rounded-lg border border-slate-200 bg-slate-50/70 space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-800">
                                        2. Foto Lembar Absensi Fisik Orang Tua (Manual)
                                    </label>
                                    @if($selectedTanggal->foto_eviden_absen_ortu)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Ada File
                                        </span>
                                    @endif
                                </div>

                                @if($selectedTanggal->foto_eviden_absen_ortu)
                                    <div class="flex items-center gap-3 p-2 bg-white rounded border border-slate-200">
                                        <img src="{{ $selectedTanggal->foto_eviden_absen_ortu_url }}" alt="Eviden Ortu" class="h-16 w-20 object-cover rounded border">
                                        <div class="text-xs space-y-1">
                                            <p class="font-medium text-slate-800">Foto lembar tanda tangan orang tua/wali</p>
                                            <button type="button" @click="openImageModal('{{ $selectedTanggal->foto_eviden_absen_ortu_url }}', 'Lembar Absensi Manual Orang Tua')"
                                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:underline">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Pratinjau Foto Penuh
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">
                                        {{ $selectedTanggal->foto_eviden_absen_ortu ? 'Unggah Ulang / Ganti Foto Lembar Ortu' : 'Pilih / Foto Lembar Ortu' }}
                                    </label>
                                    <input type="file" name="foto_eviden_absen_ortu" accept="image/*" capture="environment"
                                        class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 border border-slate-300 rounded-md bg-white">
                                    <p class="text-[10px] text-slate-400 mt-1">Pastikan tulisan & tanda tangan basah terbaca jelas (Maks. 8 MB).</p>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Petugas Lapangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Catatan Ringkasan &amp; Kendala Petugas Lapangan
                            </label>
                            <textarea name="catatan_petugas" rows="3"
                                placeholder="Contoh: Sesi seleksi berlangsung kondusif. Dari 15 peserta terjadwal, 14 peserta hadir bersama orang tua lengkap mengikuti tes TPA dan wawancara. 1 peserta berhalangan karena sakit dan dijadwalkan ulang pekan depan..."
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">{{ old('catatan_petugas', $selectedTanggal->catatan_petugas) }}</textarea>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Catatan ini mewakili laporan lapangan dari seluruh petugas piket dan akan otomatis disajikan pada saat Kaur SPMB menyusun Berita Acara Mingguan.
                            </p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Laporan &amp; Eviden Fisik Piket
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>

        {{-- MODAL INTERAKTIF: UPDATE KEHADIRAN DIGITAL CALON PESERTA --}}
        <template x-teleport="body">
            <div x-show="modalKehadiran.open"
                @click="modalKehadiran.open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-[9999]"
                style="display: none;">

                <div class="bg-white rounded-lg max-w-lg w-full shadow-xl overflow-hidden" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-slate-50">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Catat Presensi Digital Peserta Seleksi</h3>
                            <p class="text-xs text-slate-500" x-text="modalKehadiran.nama + ' (' + modalKehadiran.nomor + ')'"></p>
                        </div>
                        <button type="button" @click="modalKehadiran.open = false" class="text-slate-400 hover:text-red-600 text-lg">&times;</button>
                    </div>

                    <form :action="modalKehadiran.actionUrl" method="POST" class="p-5 space-y-4">
                        @csrf
                        @method('PATCH')

                        {{-- Quick Presets --}}
                        <div>
                            <span class="text-[11px] font-semibold text-slate-500 uppercase">Pilihan Cepat:</span>
                            <div class="grid grid-cols-3 gap-2 mt-1">
                                <button type="button" @click="modalKehadiran.status_siswa = 'Hadir'; modalKehadiran.status_ortu = 'Hadir'"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-800 border border-emerald-300 hover:bg-emerald-100">
                                    Keduanya Hadir
                                </button>
                                <button type="button" @click="modalKehadiran.status_siswa = 'Hadir'; modalKehadiran.status_ortu = 'Tidak Hadir'"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100">
                                    Hanya Siswa
                                </button>
                                <button type="button" @click="modalKehadiran.status_siswa = 'Tidak Hadir'; modalKehadiran.status_ortu = 'Tidak Hadir'"
                                    class="px-2.5 py-1.5 text-xs font-semibold rounded bg-red-50 text-red-800 border border-red-300 hover:bg-red-100">
                                    Tidak Hadir
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2">
                            {{-- Status Calon Siswa --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran Siswa</label>
                                <select name="status_kehadiran_siswa" x-model="modalKehadiran.status_siswa"
                                    class="w-full text-xs border border-slate-300 rounded-md py-2 px-2.5 focus:border-red-500 focus:ring-red-500">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Terjadwal">Terjadwal</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>

                            {{-- Status Orang Tua --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran Orang Tua</label>
                                <select name="status_kehadiran_ortu" x-model="modalKehadiran.status_ortu"
                                    class="w-full text-xs border border-slate-300 rounded-md py-2 px-2.5 focus:border-red-500 focus:ring-red-500">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Terjadwal">Terjadwal</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>
                        </div>

                        {{-- Catatan Khusus --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kehadiran (Opsional)</label>
                            <input type="text" name="catatan" x-model="modalKehadiran.catatan" placeholder="Contoh: Datang terlambat 15 menit, wali diwakilkan paman..."
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                            <button type="button" @click="modalKehadiran.open = false"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-md">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm">
                                Simpan Status Presensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- LIGHTBOX MODAL: PREVIEW FOTO EVIDEN --}}
        <template x-teleport="body">
            <div x-show="modalImage.open"
                @click="modalImage.open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div class="relative bg-white rounded-lg p-2 max-w-xl w-full shadow-2xl" @click.stop>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 px-2">
                        <span class="text-xs font-bold text-slate-800" x-text="'Foto Eviden: ' + modalImage.title"></span>
                        <button type="button" @click="modalImage.open = false" class="text-slate-400 hover:text-red-600 text-lg">&times;</button>
                    </div>
                    <div class="p-2 flex items-center justify-center bg-slate-100 rounded mt-2">
                        <img :src="modalImage.url" alt="Foto Eviden" class="max-h-[75vh] w-auto rounded object-contain shadow-sm">
                    </div>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function guruAbsensiPage() {
            return {
                formStatus: 'Hadir',
                imagePreview: null,
                modalImage: {
                    open: false,
                    url: '',
                    title: ''
                },
                modalKehadiran: {
                    open: false,
                    actionUrl: '',
                    nama: '',
                    nomor: '',
                    status_siswa: 'Hadir',
                    status_ortu: 'Hadir',
                    catatan: ''
                },

                bukaModalKehadiran(item) {
                    this.modalKehadiran.actionUrl = '{{ url('/guru/spmb-piket/peserta-kehadiran') }}/' + item.id;
                    this.modalKehadiran.nama = item.nama_lengkap;
                    this.modalKehadiran.nomor = item.nomor_pendaftaran;
                    this.modalKehadiran.status_siswa = item.status_kehadiran_siswa || 'Hadir';
                    this.modalKehadiran.status_ortu = item.status_kehadiran_ortu || 'Hadir';
                    this.modalKehadiran.catatan = item.catatan || '';
                    this.modalKehadiran.open = true;
                },

                previewFile(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.imagePreview = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                clearImage() {
                    this.imagePreview = null;
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                },

                openImageModal(url, title) {
                    this.modalImage.url = url;
                    this.modalImage.title = title;
                    this.modalImage.open = true;
                }
            };
        }
    </script>
</x-app-layout>
