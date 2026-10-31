<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Hasil & Rekapitulasi Wawancara SPMB</h2>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring Penilaian Calon Siswa & Orang Tua serta Penetapan Kelulusan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kaur-spmb.administrasi.instrumen-wawancara.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg border border-slate-300 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Setting Instrumen
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="hasilWawancaraPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- FLASH NOTIFICATIONS --}}
            @if(session('success'))
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 shadow-sm">
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
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-red-800 bg-red-50 rounded-lg border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            {{-- STATS OVERVIEW CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Casis --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peserta Seleksi</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalKandidat) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">T.P {{ $selectedYear }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Siswa Selesai --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Wawancara Siswa</p>
                        <h3 class="text-2xl font-bold text-red-600 mt-1">{{ number_format($siswaSelesaiCount) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalKandidat > 0 ? round(($siswaSelesaiCount / $totalKandidat) * 100) : 0 }}% selesai dinilai</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                {{-- Ortu Selesai --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Wawancara Orang Tua</p>
                        <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($ortuSelesaiCount) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $totalKandidat > 0 ? round(($ortuSelesaiCount / $totalKandidat) * 100) : 0 }}% selesai dinilai</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Lengkap Keduanya --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lengkap (Siswa & Ortu)</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($lengkapCount) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Siap diterbitkan kelulusan</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- PREDIKAT BREAKDOWN PILLS --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Distribusi Predikat Calon Siswa:</span>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Sangat Baik (A): <strong>{{ $predikatSiswaCounts['Sangat Baik'] ?? 0 }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-800 border border-blue-200 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Baik (B): <strong>{{ $predikatSiswaCounts['Baik'] ?? 0 }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Cukup (C): <strong>{{ $predikatSiswaCounts['Cukup'] ?? 0 }}</strong>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-800 border border-red-200 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Kurang Baik (D): <strong>{{ $predikatSiswaCounts['Kurang Baik'] ?? 0 }}</strong>
                    </span>
                </div>
            </div>

            {{-- FILTER TOOLBAR --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                <form method="GET" action="{{ route('kaur-spmb.administrasi.hasil-wawancara.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Tahun Ajaran --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">T.P:</span>
                            <select name="tahun_ajaran" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Piket --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">Jadwal:</span>
                            <select name="tanggal_id" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50 max-w-xs truncate">
                                <option value="">Semua Tanggal Piket</option>
                                @foreach($tanggalOptions as $tgl)
                                    <option value="{{ $tgl->id }}" {{ $tanggalId == $tgl->id ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Wawancara --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">Status:</span>
                            <select name="status" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50">
                                <option value="">Semua Status</option>
                                <option value="selesai" {{ $statusWawancara == 'selesai' ? 'selected' : '' }}>Lengkap Selesai</option>
                                <option value="sebagian" {{ $statusWawancara == 'sebagian' ? 'selected' : '' }}>Sebagian (Siswa/Ortu)</option>
                                <option value="belum" {{ $statusWawancara == 'belum' ? 'selected' : '' }}>Belum Selesai</option>
                            </select>
                        </div>

                        {{-- Predikat --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">Predikat:</span>
                            <select name="predikat" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50">
                                <option value="">Semua Predikat</option>
                                <option value="Sangat Baik" {{ $hasilPredikat == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                <option value="Baik" {{ $hasilPredikat == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Cukup" {{ $hasilPredikat == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                <option value="Kurang Baik" {{ $hasilPredikat == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik</option>
                            </select>
                        </div>

                        {{-- Rekomendasi Akhir --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">Keputusan:</span>
                            <select name="rekomendasi" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50">
                                <option value="">Semua Keputusan</option>
                                <option value="Diterima" {{ $rekomendasi == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Diterima Bersyarat" {{ $rekomendasi == 'Diterima Bersyarat' ? 'selected' : '' }}>Diterima Bersyarat</option>
                                <option value="Dipertimbangkan" {{ $rekomendasi == 'Dipertimbangkan' ? 'selected' : '' }}>Dipertimbangkan</option>
                                <option value="Ditolak" {{ $rekomendasi == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="Belum Diputuskan" {{ $rekomendasi == 'Belum Diputuskan' ? 'selected' : '' }}>Belum Diputuskan</option>
                            </select>
                        </div>
                    </div>

                    {{-- Search Field --}}
                    <div class="flex items-center gap-2 w-full lg:w-auto">
                        <div class="relative w-full lg:w-64">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / no tes..."
                                class="w-full text-xs rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 pl-8 pr-3 py-1.5">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-md text-xs font-semibold shadow-sm transition-colors">
                            Cari
                        </button>
                        @if($search || $tanggalId || $statusWawancara || $hasilPredikat || $rekomendasi)
                            <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.index', ['tahun_ajaran' => $selectedYear]) }}" 
                               class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-red-600 border border-slate-300 rounded-md bg-white hover:bg-slate-50">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- TABLE OF RESULTS --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Calon Siswa & No Tes</th>
                                <th class="py-3 px-4">Jadwal Piket</th>
                                <th class="py-3 px-4 text-center">Wawancara Siswa</th>
                                <th class="py-3 px-4 text-center">Wawancara Ortu</th>
                                <th class="py-3 px-4 text-center">Keputusan Akhir</th>
                                <th class="py-3 px-4 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($pesertas as $index => $item)
                                @php
                                    $sesi = $item->wawancaraSesi;
                                    $siswaDone = $sesi && $sesi->status_wawancara_siswa === 'Selesai';
                                    $ortuDone = $sesi && $sesi->status_wawancara_ortu === 'Selesai';
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3 px-4 text-center text-slate-500 font-bold">
                                        {{ $pesertas->firstItem() + $index }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900 text-sm">{{ $item->nama_lengkap }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[10px] px-1.5 py-0.2 bg-slate-100 text-slate-700 rounded border border-slate-200">
                                                {{ $item->nomor_pendaftaran }}
                                            </span>
                                            <span class="text-[11px] text-slate-500">
                                                Wali: {{ $item->nama_orang_tua ?: '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($item->tanggal)
                                            <div class="font-semibold text-slate-800">
                                                {{ \Carbon\Carbon::parse($item->tanggal->tanggal)->locale('id')->isoFormat('D MMM Y') }}
                                            </div>
                                            <div class="text-[10px] text-slate-400">
                                                {{ $item->tanggal->kelompok?->nama_kelompok ?? 'Piket SPMB' }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Belum dijadwalkan</span>
                                        @endif
                                    </td>
                                    {{-- Status & Hasil Siswa --}}
                                    <td class="py-3 px-4 text-center">
                                        @if($siswaDone)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold 
                                                {{ $sesi->hasil_wawancara_siswa === 'Sangat Baik' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                                   ($sesi->hasil_wawancara_siswa === 'Baik' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 
                                                   ($sesi->hasil_wawancara_siswa === 'Cukup' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200')) }}">
                                                {{ $sesi->hasil_wawancara_siswa ?: 'Selesai' }}
                                            </div>
                                            <div class="text-[9px] text-slate-400 mt-0.5 truncate max-w-[120px] mx-auto" title="{{ $sesi->pewawancara_siswa_nama }}">
                                                {{ $sesi->pewawancara_siswa_nama ?: 'Pewawancara' }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    {{-- Status & Hasil Ortu --}}
                                    <td class="py-3 px-4 text-center">
                                        @if($ortuDone)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold 
                                                {{ $sesi->hasil_wawancara_ortu === 'Sangat Baik' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 
                                                   ($sesi->hasil_wawancara_ortu === 'Baik' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 
                                                   ($sesi->hasil_wawancara_ortu === 'Cukup' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200')) }}">
                                                {{ $sesi->hasil_wawancara_ortu ?: 'Selesai' }}
                                            </div>
                                            <div class="text-[9px] text-slate-400 mt-0.5 truncate max-w-[120px] mx-auto" title="{{ $sesi->pewawancara_ortu_nama }}">
                                                {{ $sesi->pewawancara_ortu_nama ?: 'Pewawancara' }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                Belum
                                            </span>
                                        @endif
                                    </td>
                                    {{-- Keputusan Rekomendasi Akhir --}}
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $rekom = $sesi?->rekomendasi_akhir ?? 'Belum Diputuskan';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                            {{ $rekom === 'Diterima' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' :
                                               ($rekom === 'Diterima Bersyarat' ? 'bg-teal-100 text-teal-800 border border-teal-300' :
                                               ($rekom === 'Dipertimbangkan' ? 'bg-amber-100 text-amber-800 border border-amber-300' :
                                               ($rekom === 'Ditolak' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-slate-100 text-slate-600 border border-slate-200'))) }}">
                                            {{ $rekom }}
                                        </span>
                                    </td>
                                    {{-- Aksi --}}
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Detail Transkrip --}}
                                            <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.show', $item) }}" 
                                               class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded border border-slate-300 transition-colors"
                                               title="Buka Resume & Transkrip Wawancara">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- Cetak Lembar Dokumen A4 --}}
                                            <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.print', $item) }}" target="_blank"
                                               class="p-1.5 bg-red-50 hover:bg-red-600 hover:text-white text-red-600 rounded border border-red-200 transition-colors"
                                               title="Cetak Lembar Wawancara Resmi A4">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>

                                            {{-- Dropdown Quick Action --}}
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" type="button" 
                                                    class="p-1.5 bg-white hover:bg-slate-100 text-slate-600 rounded border border-slate-300 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-cloak
                                                    class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-20 text-left">
                                                    <a href="{{ route('kaur-spmb.administrasi.wawancara.siswa', $item) }}" 
                                                       class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-red-50 hover:text-red-700">
                                                        Wawancara Siswa
                                                    </a>
                                                    <a href="{{ route('kaur-spmb.administrasi.wawancara.ortu', $item) }}" 
                                                       class="block px-3 py-1.5 text-xs text-slate-700 hover:bg-purple-50 hover:text-purple-700">
                                                        Wawancara Ortu
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 italic">
                                        Tidak ada data calon peserta seleksi yang sesuai filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($pesertas->hasPages())
                    <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
                        {{ $pesertas->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function hasilWawancaraPage() {
            return {
                init() {}
            }
        }
    </script>
    @endpush
</x-app-layout>
