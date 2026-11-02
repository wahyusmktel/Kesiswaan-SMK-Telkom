<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Instrumen Tes Potensi Akademik (TPA)</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="instrumenTpaPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ACTION TOOLBAR (OUTSIDE HEADER) --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold tracking-wide uppercase bg-red-100 text-red-700">Bank Soal TPA</span>
                        <h3 class="text-sm font-bold text-slate-900">PENGELOLAAN BANK SOAL & KATEGORI UJIAN SELEKSI SISFO</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Menggantikan LMS Moodle untuk 4 Mata Uji: Bahasa Indonesia, Matematika, Bahasa Inggris, dan Dasar TIK</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="showSimulasiModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Simulasi Ujian
                    </button>
                    @if($currentKategori)
                        <button type="button" @click="openSettingKategoriModal({{ json_encode($currentKategori) }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Setting Mata Uji ({{ $currentKategori->nama }})
                        </button>
                    @endif
                    <button type="button" @click="openAddSoalModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Tambah Soal Baru
                    </button>
                </div>
            </div>

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

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Bank Soal --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Bank Soal</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalBankSoal) }} <span class="text-xs font-normal text-slate-500">butir</span></h3>
                        <p class="text-[11px] text-emerald-600 font-medium mt-0.5">{{ number_format($totalSoalAktif) }} soal aktif siap diujikan</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                {{-- Kategori Terpilih --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Soal Mapel Ini</p>
                        <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $currentKategori ? $currentKategori->soals_count : 0 }} <span class="text-xs font-normal text-slate-500">soal</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $currentKategori ? $currentKategori->nama : '-' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                {{-- Passing Grade & Target --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Passing Grade</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $currentKategori ? $currentKategori->passing_grade : $avgPassingGrade }} <span class="text-xs font-normal text-slate-500">/ 100</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Ambang batas kelulusan TPA</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>

                {{-- Durasi & Total Sesi --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi Ujian Mapel</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $currentKategori ? $currentKategori->durasi_menit : 20 }} <span class="text-xs font-normal text-slate-500">menit</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tampil {{ $currentKategori ? $currentKategori->jumlah_soal_ditampilkan : 10 }} soal per sesi</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 4 KATEGORI TABS --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-2">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach($kategoris as $kat)
                        @php
                            $isSelected = $currentKategori && $currentKategori->id === $kat->id;
                            $colorMap = [
                                'red' => [
                                    'active' => 'bg-red-600 text-white shadow-sm ring-2 ring-red-500/20',
                                    'inactive' => 'bg-red-50/60 hover:bg-red-50 text-red-900 border-red-200',
                                    'badge_active' => 'bg-white/20 text-white',
                                    'badge_inactive' => 'bg-red-100 text-red-700',
                                ],
                                'blue' => [
                                    'active' => 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-500/20',
                                    'inactive' => 'bg-blue-50/60 hover:bg-blue-50 text-blue-900 border-blue-200',
                                    'badge_active' => 'bg-white/20 text-white',
                                    'badge_inactive' => 'bg-blue-100 text-blue-700',
                                ],
                                'purple' => [
                                    'active' => 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-500/20',
                                    'inactive' => 'bg-purple-50/60 hover:bg-purple-50 text-purple-900 border-purple-200',
                                    'badge_active' => 'bg-white/20 text-white',
                                    'badge_inactive' => 'bg-purple-100 text-purple-700',
                                ],
                                'emerald' => [
                                    'active' => 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-500/20',
                                    'inactive' => 'bg-emerald-50/60 hover:bg-emerald-50 text-emerald-900 border-emerald-200',
                                    'badge_active' => 'bg-white/20 text-white',
                                    'badge_inactive' => 'bg-emerald-100 text-emerald-700',
                                ],
                            ];
                            $colors = $colorMap[$kat->warna_tema] ?? $colorMap['red'];
                        @endphp
                        <a href="{{ route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $kat->kode]) }}"
                            class="flex flex-col p-3 rounded-lg border transition-all text-left {{ $isSelected ? $colors['active'] : 'border-slate-200 hover:border-slate-300 ' . $colors['inactive'] }}">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold tracking-wider uppercase opacity-90">{{ $kat->kode }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isSelected ? $colors['badge_active'] : $colors['badge_inactive'] }}">
                                    {{ $kat->soals_count }} Soal
                                </span>
                            </div>
                            <span class="text-sm font-bold mt-1 leading-snug">{{ $kat->nama }}</span>
                            <span class="text-[11px] opacity-80 mt-0.5">Durasi: {{ $kat->durasi_menit }} mnt | Pass: {{ $kat->passing_grade }}%</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- TOOLBAR PENCARIAN & FILTER SOAL --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Daftar Soal: <span class="text-red-600">{{ $currentKategori ? $currentKategori->nama : 'Semua Kategori' }}</span>
                    </h4>
                    @if($currentKategori)
                        <span class="px-2 py-0.5 text-[11px] rounded bg-slate-100 text-slate-600 font-medium">
                            Menampilkan {{ $soals->total() }} butir
                        </span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('kaur-spmb.administrasi.instrumen-tpa.index') }}" class="flex items-center gap-2">
                        @if($currentKategori)
                            <input type="hidden" name="kategori" value="{{ $currentKategori->kode }}">
                        @endif
                        <div class="relative">
                            <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="Cari pertanyaan / opsi..."
                                class="w-56 sm:w-64 pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <button type="submit" class="px-3 py-1.5 text-xs bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-medium">
                            Cari
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('kaur-spmb.administrasi.instrumen-tpa.index', ['kategori' => $currentKategori?->kode]) }}"
                                class="px-2.5 py-1.5 text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-medium">
                                Reset
                            </a>
                        @endif
                    </form>

                    {{-- Tombol Sinkron Seeder --}}
                    <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-tpa.reset-seeder') }}"
                        onsubmit="return confirm('Apakah Anda yakin ingin menyinkronkan ulang bank soal standar dari sistem seeder? Soal bawaan yang terhapus akan dikembalikan.')">
                        @csrf
                        <button type="submit" title="Pulihkan Bank Soal Standar (Seeder)"
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-lg border border-slate-300 transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Sync Seeder
                        </button>
                    </form>
                </div>
            </div>

            {{-- DAFTAR KARTU SOAL --}}
            <div class="space-y-4">
                @forelse($soals as $index => $soal)
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden hover:border-slate-300 transition-colors">
                        {{-- Header Kartu Soal --}}
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-slate-800 text-white text-xs font-bold flex items-center justify-center">
                                    {{ $soal->nomor_urut }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider
                                    {{ $soal->kategori?->kode === 'BINDO' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $soal->kategori?->kode === 'MTK' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $soal->kategori?->kode === 'BING' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $soal->kategori?->kode === 'TIK' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                    {{ $soal->kategori?->nama }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold
                                    {{ $soal->tingkat_kesulitan === 'Mudah' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $soal->tingkat_kesulitan === 'Sedang' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $soal->tingkat_kesulitan === 'Sulit' ? 'bg-rose-100 text-rose-700' : '' }}">
                                    {{ $soal->tingkat_kesulitan }}
                                </span>
                                <span class="text-xs text-slate-400">| Bobot: {{ $soal->bobot }} pt</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                {{-- Toggle Active --}}
                                <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-tpa.soal.toggle', $soal) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[11px] font-semibold transition-colors
                                        {{ $soal->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}"
                                        title="Klik untuk mengubah status aktif">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $soal->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ $soal->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <button type="button" @click="openEditSoalModal({{ json_encode($soal) }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-indigo-50 text-indigo-700 border border-slate-200 hover:border-indigo-200 text-xs font-semibold rounded shadow-sm transition-colors">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>

                                {{-- Delete Button --}}
                                <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-tpa.soal.destroy', $soal) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus butir soal No. {{ $soal->nomor_urut }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 border border-slate-200 hover:border-red-200 text-xs font-semibold rounded shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Isi Pertanyaan & Opsi --}}
                        <div class="p-4 space-y-4">
                            {{-- Teks Pertanyaan --}}
                            <div class="text-sm font-medium text-slate-900 leading-relaxed whitespace-pre-line">
                                {!! nl2br(e($soal->pertanyaan)) !!}
                            </div>

                            {{-- Gambar Soal (Jika ada) --}}
                            @if($soal->gambar)
                                <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg inline-block">
                                    <img src="{{ $soal->gambar_url }}" alt="Gambar Soal {{ $soal->nomor_urut }}" class="max-h-48 rounded object-contain cursor-pointer"
                                        @click="previewImage = '{{ $soal->gambar_url }}'; showImageModal = true">
                                    <p class="text-[11px] text-slate-400 text-center mt-1">Klik gambar untuk memperbesar</p>
                                </div>
                            @endif

                            {{-- Opsi Pilihan Ganda A - E --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach(['A' => $soal->opsi_a, 'B' => $soal->opsi_b, 'C' => $soal->opsi_c, 'D' => $soal->opsi_d, 'E' => $soal->opsi_e] as $abjad => $teksOpsi)
                                    @if($teksOpsi !== null && trim($teksOpsi) !== '')
                                        @php
                                            $isKunci = strtoupper($soal->kunci_jawaban) === $abjad;
                                        @endphp
                                        <div class="flex items-start gap-2.5 p-2.5 rounded-lg border text-xs transition-all
                                            {{ $isKunci ? 'bg-emerald-50/80 border-emerald-300 font-semibold text-emerald-950 ring-1 ring-emerald-400/30' : 'bg-slate-50/60 border-slate-200 text-slate-700' }}">
                                            <span class="w-6 h-6 rounded flex items-center justify-center font-bold flex-shrink-0 text-[11px]
                                                {{ $isKunci ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-300 text-slate-700' }}">
                                                {{ $abjad }}
                                            </span>
                                            <div class="flex-1 pt-0.5 leading-snug">
                                                <span>{{ $teksOpsi }}</span>
                                                @if($isKunci)
                                                    <span class="inline-flex items-center gap-1 ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-900">
                                                        <svg class="w-3 h-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        KUNCI JAWABAN
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Pembahasan Box --}}
                            @if($soal->pembahasan)
                                <div class="bg-indigo-50/50 border border-indigo-100 rounded-lg p-3 text-xs text-indigo-900">
                                    <div class="flex items-center gap-1.5 font-bold text-indigo-800 mb-1">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Pembahasan / Keterangan:</span>
                                    </div>
                                    <p class="leading-relaxed whitespace-pre-line text-indigo-900/90">{{ $soal->pembahasan }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-slate-200 p-8 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">Belum ada butir soal pada kategori ini</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            Klik tombol <strong>+ Tambah Soal Baru</strong> untuk membuat soal pertama, atau klik <strong>Sync Seeder</strong> untuk memuat bank soal standar.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if($soals->hasPages())
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                    {{ $soals->links() }}
                </div>
            @endif

        </div>

        {{-- ============================================================== --}}
        {{-- MODAL TAMBAH SOAL --}}
        {{-- ============================================================== --}}
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="min-h-screen px-4 text-center flex items-center justify-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showAddModal = false"></div>

                <div class="inline-block w-full max-w-3xl my-8 p-6 text-left bg-white rounded-xl shadow-2xl transform transition-all relative z-10">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-red-100 text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-slate-900">Tambah Butir Soal TPA Baru</h3>
                        </div>
                        <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('kaur-spmb.administrasi.instrumen-tpa.soal.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Uji (Kategori) <span class="text-red-500">*</span></label>
                                <select name="spmb_tpa_kategori_id" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}" {{ $currentKategori && $currentKategori->id === $kat->id ? 'selected' : '' }}>
                                            {{ $kat->nama }} ({{ $kat->kode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tingkat Kesulitan --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                                <select name="tingkat_kesulitan" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                    <option value="Mudah">Mudah</option>
                                    <option value="Sedang" selected>Sedang</option>
                                    <option value="Sulit">Sulit</option>
                                </select>
                            </div>

                            {{-- Bobot --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Bobot Nilai</label>
                                <input type="number" name="bobot" value="1" min="1" max="100" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        {{-- Pertanyaan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teks Pertanyaan / Soal <span class="text-red-500">*</span></label>
                            <textarea name="pertanyaan" rows="4" required placeholder="Tuliskan butir pertanyaan, teks bacaan, atau instruksi soal..."
                                class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        {{-- Upload Gambar (Opsional) --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lampiran Gambar / Rumus / Diagram (Opsional)</label>
                            <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP, SVG (Maks. 2MB)</p>
                        </div>

                        {{-- Pilihan Opsi A - E --}}
                        <div class="space-y-2.5 pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-800">Pilihan Jawaban (A, B, C, D, E)</label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi A <span class="text-red-500">*</span></label>
                                    <input type="text" name="opsi_a" required placeholder="Teks pilihan A" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi B <span class="text-red-500">*</span></label>
                                    <input type="text" name="opsi_b" required placeholder="Teks pilihan B" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi C <span class="text-red-500">*</span></label>
                                    <input type="text" name="opsi_c" required placeholder="Teks pilihan C" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi D <span class="text-red-500">*</span></label>
                                    <input type="text" name="opsi_d" required placeholder="Teks pilihan D" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi E (Opsional)</label>
                                    <input type="text" name="opsi_e" placeholder="Teks pilihan E (kosongkan jika hanya 4 pilihan)" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        </div>

                        {{-- Kunci Jawaban --}}
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-800 mb-1.5">Kunci Jawaban Yang Benar <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-3">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                    <label class="inline-flex items-center gap-1.5 p-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 text-xs font-bold">
                                        <input type="radio" name="kunci_jawaban" value="{{ $opt }}" {{ $opt === 'A' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                                        <span>Pilihan {{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pembahasan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pembahasan / Penjelasan Jawaban (Opsional)</label>
                            <textarea name="pembahasan" rows="3" placeholder="Tuliskan pembahasan atau alasan mengapa kunci jawaban tersebut benar..."
                                class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        {{-- Checkbox Aktif --}}
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <label for="add_is_active" class="text-xs font-medium text-slate-700">Aktifkan butir soal ini agar langsung muncul di paket ujian</label>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MODAL EDIT SOAL --}}
        {{-- ============================================================== --}}
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="min-h-screen px-4 text-center flex items-center justify-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showEditModal = false"></div>

                <div class="inline-block w-full max-w-3xl my-8 p-6 text-left bg-white rounded-xl shadow-2xl transform transition-all relative z-10"
                    x-data="{ editSoal: {} }" @open-edit-soal.window="editSoal = $event.detail">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-slate-900">Edit Butir Soal No. <span x-text="editSoal.nomor_urut"></span></h3>
                        </div>
                        <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'{{ url('kaur-spmb/administrasi/instrumen-tpa/soal') }}/' + editSoal.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Mata Uji (Kategori) <span class="text-red-500">*</span></label>
                                <select name="spmb_tpa_kategori_id" x-model="editSoal.spmb_tpa_kategori_id" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id }}">{{ $kat->nama }} ({{ $kat->kode }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tingkat Kesulitan --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                                <select name="tingkat_kesulitan" x-model="editSoal.tingkat_kesulitan" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                    <option value="Mudah">Mudah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Sulit">Sulit</option>
                                </select>
                            </div>

                            {{-- Bobot --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Bobot Nilai</label>
                                <input type="number" name="bobot" x-model="editSoal.bobot" min="1" max="100" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        {{-- Pertanyaan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teks Pertanyaan / Soal <span class="text-red-500">*</span></label>
                            <textarea name="pertanyaan" x-model="editSoal.pertanyaan" rows="4" required
                                class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        {{-- Upload Gambar & Preview Current --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Lampiran Gambar / Rumus / Diagram</label>
                            <template x-if="editSoal.gambar">
                                <div class="mb-2 p-2 bg-slate-50 border border-slate-200 rounded flex items-center justify-between gap-3">
                                    <span class="text-xs text-slate-600">Gambar tersimpan: <strong x-text="editSoal.gambar"></strong></span>
                                    <label class="inline-flex items-center gap-1 text-xs text-red-600 cursor-pointer">
                                        <input type="checkbox" name="hapus_gambar" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                        <span>Hapus Gambar</span>
                                    </label>
                                </div>
                            </template>
                            <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        {{-- Opsi A - E --}}
                        <div class="space-y-2.5 pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-800">Pilihan Jawaban</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi A</label>
                                    <input type="text" name="opsi_a" x-model="editSoal.opsi_a" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi B</label>
                                    <input type="text" name="opsi_b" x-model="editSoal.opsi_b" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi C</label>
                                    <input type="text" name="opsi_c" x-model="editSoal.opsi_c" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi D</label>
                                    <input type="text" name="opsi_d" x-model="editSoal.opsi_d" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Opsi E</label>
                                    <input type="text" name="opsi_e" x-model="editSoal.opsi_e" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        </div>

                        {{-- Kunci Jawaban --}}
                        <div class="pt-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-800 mb-1.5">Kunci Jawaban Yang Benar</label>
                            <div class="flex items-center gap-3">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                    <label class="inline-flex items-center gap-1.5 p-2 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 text-xs font-bold">
                                        <input type="radio" name="kunci_jawaban" value="{{ $opt }}" x-model="editSoal.kunci_jawaban" class="text-red-600 focus:ring-red-500">
                                        <span>Pilihan {{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pembahasan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pembahasan / Penjelasan Jawaban</label>
                            <textarea name="pembahasan" x-model="editSoal.pembahasan" rows="3"
                                class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        {{-- Checkbox Aktif --}}
                        <div class="flex items-center gap-2 pt-1">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editSoal.is_active" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <label for="edit_is_active" class="text-xs font-medium text-slate-700">Aktifkan butir soal ini dalam paket ujian</label>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">
                                Perbarui Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MODAL PENGATURAN KATEGORI --}}
        {{-- ============================================================== --}}
        <div x-show="showSettingKategoriModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="min-h-screen px-4 text-center flex items-center justify-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showSettingKategoriModal = false"></div>

                <div class="inline-block w-full max-w-lg my-8 p-6 text-left bg-white rounded-xl shadow-2xl transform transition-all relative z-10"
                    x-data="{ katData: {} }" @open-setting-kategori.window="katData = $event.detail">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-indigo-100 text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <h3 class="text-base font-bold text-slate-900">Setting Mata Uji: <span x-text="katData.nama"></span></h3>
                        </div>
                        <button type="button" @click="showSettingKategoriModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'{{ url('kaur-spmb/administrasi/instrumen-tpa/kategori') }}/' + katData.id" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Mata Uji</label>
                            <input type="text" name="nama" x-model="katData.nama" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Penjelasan Singkat</label>
                            <textarea name="deskripsi" x-model="katData.deskripsi" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi Ujian (Menit)</label>
                                <input type="number" name="durasi_menit" x-model="katData.durasi_menit" min="5" max="180" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Passing Grade (0-100)</label>
                                <input type="number" name="passing_grade" x-model="katData.passing_grade" min="0" max="100" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jml Soal Tampil / Sesi</label>
                                <input type="number" name="jumlah_soal_ditampilkan" x-model="katData.jumlah_soal_ditampilkan" min="1" max="100" required class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Tema</label>
                                <select name="warna_tema" x-model="katData.warna_tema" class="w-full text-xs rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                    <option value="red">Merah (Red)</option>
                                    <option value="blue">Biru (Blue)</option>
                                    <option value="purple">Ungu (Purple)</option>
                                    <option value="emerald">Hijau (Emerald)</option>
                                    <option value="amber">Kuning (Amber)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
                            <button type="button" @click="showSettingKategoriModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MODAL PREVIEW SIMULASI UJIAN (CASIS PERSPECTIVE) --}}
        {{-- ============================================================== --}}
        <div x-show="showSimulasiModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="min-h-screen px-4 text-center flex items-center justify-center">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showSimulasiModal = false"></div>

                <div class="inline-block w-full max-w-4xl my-8 p-6 text-left bg-white rounded-xl shadow-2xl transform transition-all relative z-10"
                    x-data="{ activeSimulasiIdx: 0, simulasiList: {{ json_encode($soals->items()) }}, selectedJawaban: {} }">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Preview Simulasi Lembar Ujian TPA (Siswa)</h3>
                                <p class="text-xs text-slate-500">Tampilan antarmuka yang akan dilihat oleh Calon Siswa Baru saat ujian berlangsung</p>
                            </div>
                        </div>
                        <button type="button" @click="showSimulasiModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="mt-4">
                        <template x-if="simulasiList.length === 0">
                            <div class="py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold">Tidak ada butir soal untuk disimulasikan pada kategori ini.</p>
                            </div>
                        </template>

                        <template x-if="simulasiList.length > 0">
                            <div class="space-y-4">
                                {{-- Nomor Soal Palette --}}
                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex flex-wrap items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-700 mr-2">Navigasi Soal:</span>
                                    <template x-for="(item, idx) in simulasiList" :key="item.id">
                                        <button type="button" @click="activeSimulasiIdx = idx"
                                            :class="activeSimulasiIdx === idx ? 'bg-red-600 text-white font-bold ring-2 ring-red-300' : (selectedJawaban[item.id] ? 'bg-emerald-600 text-white font-semibold' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100')"
                                            class="w-7 h-7 text-xs rounded transition-all flex items-center justify-center"
                                            x-text="idx + 1">
                                        </button>
                                    </template>
                                </div>

                                {{-- Kartu Soal Simulasi Aktif --}}
                                <div class="p-5 rounded-lg border border-slate-200 bg-white space-y-4">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                        <span class="text-xs font-bold text-red-600 uppercase" x-text="'Soal Nomor ' + (activeSimulasiIdx + 1) + ' dari ' + simulasiList.length"></span>
                                        <span class="text-xs text-slate-400" x-text="'Kategori: ' + (simulasiList[activeSimulasiIdx]?.kategori?.nama || '-')"></span>
                                    </div>

                                    <div class="text-sm font-medium text-slate-900 leading-relaxed whitespace-pre-line"
                                        x-text="simulasiList[activeSimulasiIdx]?.pertanyaan">
                                    </div>

                                    {{-- Gambar jika ada --}}
                                    <template x-if="simulasiList[activeSimulasiIdx]?.gambar">
                                        <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg inline-block">
                                            <img :src="simulasiList[activeSimulasiIdx]?.gambar.startsWith('http') || simulasiList[activeSimulasiIdx]?.gambar.startsWith('/images') || simulasiList[activeSimulasiIdx]?.gambar.startsWith('images') ? (simulasiList[activeSimulasiIdx]?.gambar.startsWith('/') ? simulasiList[activeSimulasiIdx]?.gambar : '/' + simulasiList[activeSimulasiIdx]?.gambar) : '/storage/' + simulasiList[activeSimulasiIdx]?.gambar"
                                                alt="Gambar Soal" class="max-h-56 rounded object-contain">
                                        </div>
                                    </template>

                                    {{-- Radio Pilihan Jawaban Siswa --}}
                                    <div class="space-y-2 pt-2">
                                        <template x-for="abjad in ['A', 'B', 'C', 'D', 'E']" :key="abjad">
                                            <template x-if="simulasiList[activeSimulasiIdx]?.['opsi_' + abjad.toLowerCase()]">
                                                <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-all"
                                                    :class="selectedJawaban[simulasiList[activeSimulasiIdx]?.id] === abjad ? 'bg-red-50 border-red-300 ring-1 ring-red-400/20' : 'bg-slate-50/50 hover:bg-slate-50 border-slate-200'">
                                                    <input type="radio" :name="'jawaban_' + simulasiList[activeSimulasiIdx]?.id" :value="abjad"
                                                        x-model="selectedJawaban[simulasiList[activeSimulasiIdx]?.id]"
                                                        class="text-red-600 focus:ring-red-500 mt-0.5">
                                                    <div class="text-xs">
                                                        <span class="font-bold text-slate-800" x-text="abjad + '.'"></span>
                                                        <span class="text-slate-700 ml-1" x-text="simulasiList[activeSimulasiIdx]?.['opsi_' + abjad.toLowerCase()]"></span>
                                                    </div>
                                                </label>
                                            </template>
                                        </template>
                                    </div>
                                </div>

                                {{-- Navigasi Bawah --}}
                                <div class="flex items-center justify-between pt-2">
                                    <button type="button" @click="if(activeSimulasiIdx > 0) activeSimulasiIdx--"
                                        :disabled="activeSimulasiIdx === 0"
                                        :class="activeSimulasiIdx === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100'"
                                        class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg">
                                        &larr; Soal Sebelumnya
                                    </button>

                                    <div class="text-xs text-slate-500">
                                        Terjawab: <strong class="text-slate-800" x-text="Object.keys(selectedJawaban).length"></strong> dari <span x-text="simulasiList.length"></span>
                                    </div>

                                    <button type="button" @click="if(activeSimulasiIdx < simulasiList.length - 1) activeSimulasiIdx++"
                                        :disabled="activeSimulasiIdx === simulasiList.length - 1"
                                        :class="activeSimulasiIdx === simulasiList.length - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-700 bg-red-600 text-white'"
                                        class="px-4 py-2 text-xs font-semibold rounded-lg shadow-sm">
                                        Soal Selanjutnya &rarr;
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-end pt-4 mt-4 border-t border-slate-200">
                        <button type="button" @click="showSimulasiModal = false" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg">
                            Tutup Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================== --}}
        {{-- MODAL ZOOM GAMBAR --}}
        {{-- ============================================================== --}}
        <div x-show="showImageModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="min-h-screen px-4 text-center flex items-center justify-center">
                <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" @click="showImageModal = false"></div>
                <div class="inline-block max-w-3xl my-8 p-3 text-left bg-white rounded-xl shadow-2xl relative z-10">
                    <img :src="previewImage" alt="Preview Gambar Soal" class="max-h-[80vh] w-auto rounded object-contain mx-auto">
                    <div class="text-right mt-2">
                        <button type="button" @click="showImageModal = false" class="px-3 py-1 bg-slate-800 text-white text-xs rounded font-medium">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function instrumenTpaPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showSettingKategoriModal: false,
                showSimulasiModal: false,
                showImageModal: false,
                previewImage: '',

                openAddSoalModal() {
                    this.showAddModal = true;
                },

                openEditSoalModal(soal) {
                    this.showEditModal = true;
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('open-edit-soal', { detail: soal }));
                    });
                },

                openSettingKategoriModal(kategori) {
                    this.showSettingKategoriModal = true;
                    this.$nextTick(() => {
                        window.dispatchEvent(new CustomEvent('open-setting-kategori', { detail: kategori }));
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
