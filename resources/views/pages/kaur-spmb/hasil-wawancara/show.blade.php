<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.index') }}" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">Resume & Transkrip Wawancara</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $pesertaSeleksi->nama_lengkap }} ({{ $pesertaSeleksi->nomor_pendaftaran }})</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('kaur-spmb.administrasi.hasil-wawancara.print', $pesertaSeleksi) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Lembar Wawancara (A4)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="{ tab: 'siswa' }">
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

            {{-- CANDIDATE SUMMARY CARD --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Bio --}}
                    <div class="lg:col-span-2 flex items-start gap-4">
                        <div class="w-14 h-14 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-xl border border-red-100 flex-shrink-0">
                            {{ substr($pesertaSeleksi->nama_lengkap, 0, 1) }}
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded border border-slate-200">
                                    {{ $pesertaSeleksi->nomor_pendaftaran }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $pesertaSeleksi->jenis_kelamin == 'L' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                    {{ $pesertaSeleksi->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <span class="text-xs text-slate-500">T.A {{ $pesertaSeleksi->tahun_ajaran }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">{{ $pesertaSeleksi->nama_lengkap }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs text-slate-600 pt-1">
                                <div>Asal Sekolah: <strong class="text-slate-800">{{ $pesertaSeleksi->asal_sekolah ?: '-' }}</strong></div>
                                <div>No WA Siswa: <strong class="text-slate-800">{{ $pesertaSeleksi->no_wa_siswa ?: '-' }}</strong></div>
                                <div>Nama Ortu: <strong class="text-slate-800">{{ $pesertaSeleksi->nama_orang_tua ?: '-' }}</strong></div>
                                <div>No WA Ortu: <strong class="text-slate-800">{{ $pesertaSeleksi->no_wa_orang_tua ?: '-' }}</strong></div>
                                <div>Jurusan Minat: <strong class="text-slate-800">{{ $pesertaSeleksi->jurusan_pilihan ?: '-' }}</strong></div>
                                <div>Jadwal Piket: <strong class="text-slate-800">{{ $pesertaSeleksi->tanggal ? \Carbon\Carbon::parse($pesertaSeleksi->tanggal->tanggal)->locale('id')->isoFormat('D MMMM Y') : '-' }}</strong></div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Score Cards --}}
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                        {{-- Siswa --}}
                        <div class="bg-white p-3 rounded-lg border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Wawancara Siswa</span>
                            <span class="text-lg font-black text-red-600 block mt-1">
                                {{ $sesi?->hasil_wawancara_siswa ?: '-' }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                Skor: {{ $sesi?->skor_siswa !== null ? number_format($sesi->skor_siswa, 1) : '-' }}
                            </span>
                        </div>
                        {{-- Ortu --}}
                        <div class="bg-white p-3 rounded-lg border border-slate-200 text-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Wawancara Ortu</span>
                            <span class="text-lg font-black text-purple-600 block mt-1">
                                {{ $sesi?->hasil_wawancara_ortu ?: '-' }}
                            </span>
                            <span class="text-[11px] text-slate-500">
                                Skor: {{ $sesi?->skor_ortu !== null ? number_format($sesi->skor_ortu, 1) : '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KAUR SPMB FINAL DECISION PANEL --}}
            <div class="bg-white rounded-lg border-2 border-slate-300 shadow-sm p-5">
                <form action="{{ route('kaur-spmb.administrasi.hasil-wawancara.rekomendasi', $pesertaSeleksi) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-800">Penetapan Keputusan Akhir Kaur SPMB</h4>
                                <p class="text-xs text-slate-500">Tentukan status kelulusan/rekomendasi penerimaan calon siswa baru ini.</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                            {{ ($sesi?->rekomendasi_akhir ?? 'Belum Diputuskan') === 'Diterima' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' :
                               (($sesi?->rekomendasi_akhir ?? '') === 'Diterima Bersyarat' ? 'bg-teal-100 text-teal-800 border border-teal-300' :
                               (($sesi?->rekomendasi_akhir ?? '') === 'Dipertimbangkan' ? 'bg-amber-100 text-amber-800 border border-amber-300' :
                               (($sesi?->rekomendasi_akhir ?? '') === 'Ditolak' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-slate-100 text-slate-700 border border-slate-300'))) }}">
                            Status: {{ $sesi?->rekomendasi_akhir ?: 'Belum Diputuskan' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Keputusan Rekomendasi
                            </label>
                            <select name="rekomendasi_akhir" class="w-full text-xs font-bold rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 bg-slate-50">
                                <option value="Belum Diputuskan" {{ ($sesi?->rekomendasi_akhir ?? 'Belum Diputuskan') === 'Belum Diputuskan' ? 'selected' : '' }}>Belum Diputuskan</option>
                                <option value="Diterima" {{ ($sesi?->rekomendasi_akhir ?? '') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                                <option value="Diterima Bersyarat" {{ ($sesi?->rekomendasi_akhir ?? '') === 'Diterima Bersyarat' ? 'selected' : '' }}>Diterima Bersyarat</option>
                                <option value="Dipertimbangkan" {{ ($sesi?->rekomendasi_akhir ?? '') === 'Dipertimbangkan' ? 'selected' : '' }}>Dipertimbangkan</option>
                                <option value="Ditolak" {{ ($sesi?->rekomendasi_akhir ?? '') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Catatan Resmi Penetapan Kaur SPMB
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="catatan_akhir" value="{{ old('catatan_akhir', $sesi?->catatan_akhir) }}"
                                       placeholder="Contoh: Diterima di jurusan RPL jalur Reguler dengan komitmen pelunasan DSP pada tanggal..."
                                       class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow transition-colors flex-shrink-0">
                                    Simpan Keputusan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- TAB NAVIGATION (SISWA vs ORTU) --}}
            <div class="flex items-center gap-2 border-b border-slate-200">
                <button type="button" @click="tab = 'siswa'"
                        :class="tab === 'siswa' ? 'border-red-600 text-red-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-5 py-3 border-b-2 text-xs flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Transkrip Calon Siswa ({{ $pertanyaansSiswa->count() }} Butir)
                </button>

                <button type="button" @click="tab = 'ortu'"
                        :class="tab === 'ortu' ? 'border-purple-600 text-purple-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-5 py-3 border-b-2 text-xs flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Transkrip Orang Tua / Wali ({{ $pertanyaansOrtu->count() }} Butir)
                </button>
            </div>

            {{-- 1. TAB KONTEN WAWANCARA SISWA --}}
            <div x-show="tab === 'siswa'" class="space-y-4">
                {{-- Header Box Siswa --}}
                <div class="bg-white rounded-lg border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pewawancara Siswa</span>
                        <p class="text-xs font-bold text-slate-800">{{ $sesi?->pewawancara_siswa_nama ?: 'Belum diwawancarai' }}</p>
                        @if($sesi?->waktu_wawancara_siswa)
                            <p class="text-[10px] text-slate-500">Waktu: {{ \Carbon\Carbon::parse($sesi->waktu_wawancara_siswa)->format('d/m/Y H:i') }} WIB</p>
                        @endif
                    </div>

                    @if($sesi?->foto_siswa)
                        <div class="flex items-center gap-2">
                            <img src="{{ $sesi->foto_siswa_url }}" alt="Dokumentasi Siswa" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-sm">
                            <a href="{{ $sesi->foto_siswa_url }}" target="_blank" class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                                Lihat Foto Penuh
                            </a>
                        </div>
                    @endif
                </div>

                @if($sesi?->catatan_pewawancara_siswa)
                    <div class="p-3.5 bg-amber-50 rounded-lg border border-amber-200 text-xs">
                        <strong class="font-bold text-amber-900 block mb-0.5">Catatan Pewawancara Siswa:</strong>
                        <p class="text-amber-800">{{ $sesi->catatan_pewawancara_siswa }}</p>
                    </div>
                @endif

                {{-- Question List Siswa --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    @foreach($pertanyaansSiswa as $p)
                        @php
                            $jawabanObj = $jawabansMap->get($p->id);
                        @endphp
                        <div class="p-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-5 h-5 rounded bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                        {{ $p->nomor_urut }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-xs text-slate-800">{{ $p->pertanyaan }}</p>
                                        @if($p->petunjuk)
                                            <p class="text-[11px] text-slate-400 italic">{{ $p->petunjuk }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($jawabanObj && $jawabanObj->skor_diperoleh > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +{{ $jawabanObj->skor_diperoleh }} poin
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 ml-7 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800">
                                @if($jawabanObj)
                                    @if($p->tipe_input === 'checkbox' && is_array($jawabanObj->jawaban_pilihan))
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($jawabanObj->jawaban_pilihan as $itemPilihan)
                                                <span class="px-2 py-0.5 bg-white border border-slate-300 rounded text-slate-700 font-medium">
                                                    {{ $itemPilihan }}
                                                </span>
                                            @endforeach
                                            @if($jawabanObj->jawaban_other)
                                                <span class="px-2 py-0.5 bg-red-50 border border-red-200 text-red-700 font-medium">
                                                    Lainnya: {{ $jawabanObj->jawaban_other }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($p->tipe_input === 'scale_1_5')
                                        <span class="font-bold text-red-600">Skala {{ $jawabanObj->jawaban_skala }}/5</span>
                                    @else
                                        <div class="font-medium whitespace-pre-line">{{ $jawabanObj->jawaban_teks ?: '-' }}</div>
                                        @if($jawabanObj->jawaban_other)
                                            <div class="text-slate-500 mt-1 italic">Lainnya: {{ $jawabanObj->jawaban_other }}</div>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Belum ada jawaban diisi.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. TAB KONTEN WAWANCARA ORANG TUA --}}
            <div x-show="tab === 'ortu'" class="space-y-4" x-cloak>
                {{-- Header Box Ortu --}}
                <div class="bg-white rounded-lg border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pewawancara Orang Tua / Wali</span>
                        <p class="text-xs font-bold text-slate-800">{{ $sesi?->pewawancara_ortu_nama ?: 'Belum diwawancarai' }}</p>
                        @if($sesi?->waktu_wawancara_ortu)
                            <p class="text-[10px] text-slate-500">Waktu: {{ \Carbon\Carbon::parse($sesi->waktu_wawancara_ortu)->format('d/m/Y H:i') }} WIB</p>
                        @endif
                    </div>

                    @if($sesi?->foto_ortu)
                        <div class="flex items-center gap-2">
                            <img src="{{ $sesi->foto_ortu_url }}" alt="Dokumentasi Ortu" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-sm">
                            <a href="{{ $sesi->foto_ortu_url }}" target="_blank" class="text-xs font-bold text-purple-600 hover:text-purple-800 underline">
                                Lihat Foto Penuh
                            </a>
                        </div>
                    @endif
                </div>

                @if($sesi?->catatan_pewawancara_ortu)
                    <div class="p-3.5 bg-purple-50 rounded-lg border border-purple-200 text-xs">
                        <strong class="font-bold text-purple-900 block mb-0.5">Catatan Kesepakatan & Finansial Orang Tua:</strong>
                        <p class="text-purple-800">{{ $sesi->catatan_pewawancara_ortu }}</p>
                    </div>
                @endif

                {{-- Question List Ortu --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    @foreach($pertanyaansOrtu as $p)
                        @php
                            $jawabanObj = $jawabansMap->get($p->id);
                        @endphp
                        <div class="p-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-5 h-5 rounded bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                        {{ $p->nomor_urut }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-xs text-slate-800">{{ $p->pertanyaan }}</p>
                                        @if($p->petunjuk)
                                            <p class="text-[11px] text-slate-400 italic">{{ $p->petunjuk }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($jawabanObj && $jawabanObj->skor_diperoleh > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        +{{ $jawabanObj->skor_diperoleh }} poin
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 ml-7 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800">
                                @if($jawabanObj)
                                    @if($p->tipe_input === 'checkbox' && is_array($jawabanObj->jawaban_pilihan))
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($jawabanObj->jawaban_pilihan as $itemPilihan)
                                                <span class="px-2 py-0.5 bg-white border border-slate-300 rounded text-slate-700 font-medium">
                                                    {{ $itemPilihan }}
                                                </span>
                                            @endforeach
                                            @if($jawabanObj->jawaban_other)
                                                <span class="px-2 py-0.5 bg-purple-50 border border-purple-200 text-purple-700 font-medium">
                                                    Lainnya: {{ $jawabanObj->jawaban_other }}
                                                </span>
                                            @endif
                                        </div>
                                    @elseif($p->tipe_input === 'scale_1_5')
                                        <span class="font-bold text-purple-600">Skala {{ $jawabanObj->jawaban_skala }}/5</span>
                                    @else
                                        <div class="font-medium whitespace-pre-line">{{ $jawabanObj->jawaban_teks ?: '-' }}</div>
                                        @if($jawabanObj->jawaban_other)
                                            <div class="text-slate-500 mt-1 italic">Lainnya: {{ $jawabanObj->jawaban_other }}</div>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Belum ada jawaban diisi.</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
