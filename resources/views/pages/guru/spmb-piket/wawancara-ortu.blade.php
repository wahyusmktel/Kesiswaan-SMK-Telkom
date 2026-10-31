<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ request()->routeIs('kaur-spmb.*') ? route('kaur-spmb.administrasi.hasil-wawancara.index') : route('guru.spmb-piket.absensi.index', ['tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id]) }}" 
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">Instrumen Wawancara Calon Orang Tua / Wali</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Penilaian, Komitmen Pendidikan & Klarifikasi Pembiayaan SPMB SMK Telkom Lampung</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sesi->status_wawancara_ortu === 'Selesai' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                    Status: {{ $sesi->status_wawancara_ortu === 'Selesai' ? 'Selesai Diwawancarai' : 'Belum Selesai' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="wawancaraOrtuForm()">
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

            {{-- CANDIDATE & PARENT HEADER CARD --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-lg border border-purple-100 flex-shrink-0">
                            {{ substr($pesertaSeleksi->nama_orang_tua ?: $pesertaSeleksi->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded border border-slate-200">
                                    {{ $pesertaSeleksi->nomor_pendaftaran }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    Orang Tua / Wali
                                </span>
                                <span class="text-xs text-slate-500">T.A {{ $pesertaSeleksi->tahun_ajaran }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mt-1">{{ $pesertaSeleksi->nama_orang_tua ?: 'Belum diisi' }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                <span>Calon Siswa: <strong class="text-slate-800">{{ $pesertaSeleksi->nama_lengkap }}</strong></span>
                                <span>&bull;</span>
                                <span>No WA Ortu: <strong class="text-slate-800">{{ $pesertaSeleksi->no_wa_orang_tua ?: '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>Jadwal Piket: <strong class="text-slate-800">{{ $pesertaSeleksi->tanggal ? \Carbon\Carbon::parse($pesertaSeleksi->tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pewawancara Bertugas</span>
                            <span class="text-xs font-bold text-slate-800">{{ $sesi->pewawancara_ortu_nama ?: $user->name }}</span>
                            @if($sesi->waktu_wawancara_ortu)
                                <span class="text-[10px] text-slate-500 block">Selesai: {{ \Carbon\Carbon::parse($sesi->waktu_wawancara_ortu)->format('d/m/Y H:i') }} WIB</span>
                            @endif
                        </div>
                        <div class="w-9 h-9 rounded-md bg-white border border-slate-200 flex items-center justify-center text-slate-700">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OFFICIAL SPMB FEES INFORMATION BOX (INTEGRATED FROM SUPER ADMIN SPMB_FEES) --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-lg p-5 shadow-sm border border-slate-700">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-700 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-600 text-white flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">Rincian Resmi Biaya Pendidikan SPMB Telkom Lampung</h4>
                            <p class="text-xs text-slate-300">Ditarik langsung dari Master Biaya SPMB Super Admin untuk panduan wawancara finansial.</p>
                        </div>
                    </div>

                    <div class="bg-slate-800/80 px-4 py-2 rounded-lg border border-slate-600 text-right">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Pembiayaan Masuk</span>
                        <span class="text-xl font-black text-amber-400">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Fee Items Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-4">
                    @forelse($spmbFees as $fee)
                        <div class="bg-slate-800/60 p-3 rounded-lg border border-slate-700/80">
                            <p class="text-[11px] font-semibold text-slate-300 truncate" title="{{ $fee->name }}">{{ $fee->name }}</p>
                            <p class="text-sm font-bold text-white mt-1">Rp {{ number_format($fee->amount, 0, ',', '.') }}</p>
                            @if($fee->is_mandatory)
                                <span class="inline-block mt-1 text-[9px] font-bold px-1.5 py-0.2 bg-red-900/60 text-red-300 rounded border border-red-700/50">Wajib</span>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-2 text-center text-xs text-slate-400">
                            Komponen rincian biaya belum diatur di menu Super Admin.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- FORM INSTRUMEN WAWANCARA ORTU --}}
            <form action="{{ request()->routeIs('kaur-spmb.*') ? route('kaur-spmb.administrasi.wawancara.ortu.store', $pesertaSeleksi) : route('guru.spmb-piket.wawancara.ortu.store', $pesertaSeleksi) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-6">
                @csrf

                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    <div class="bg-gradient-to-r from-purple-700 to-indigo-800 px-6 py-4 text-white">
                        <h4 class="font-bold text-sm tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Daftar Butir Pertanyaan Wawancara Orang Tua / Wali (Total: {{ $pertanyaans->count() }} Pertanyaan)
                        </h4>
                        <p class="text-xs text-purple-100 mt-0.5">Instrumen resmi kesepakatan dan klarifikasi pembiayaan calon siswa baru SMK Telkom Lampung.</p>
                    </div>

                    @foreach($pertanyaans as $index => $p)
                        @php
                            $jawabanObj = $jawabans->get($p->id);
                            
                            // Determine Default Value
                            $defaultValue = null;
                            if ($jawabanObj) {
                                if ($p->tipe_input === 'checkbox') {
                                    $defaultValue = is_array($jawabanObj->jawaban_pilihan) ? $jawabanObj->jawaban_pilihan : [];
                                } elseif ($p->tipe_input === 'scale_1_5') {
                                    $defaultValue = $jawabanObj->jawaban_skala;
                                } else {
                                    $defaultValue = $jawabanObj->jawaban_teks;
                                }
                            } else {
                                if ($p->field_key === 'nama_pewawancara') $defaultValue = $sesi->pewawancara_ortu_nama ?: $user->name;
                                elseif ($p->field_key === 'nama_siswa') $defaultValue = $pesertaSeleksi->nama_lengkap;
                                elseif ($p->field_key === 'no_pendaftaran') $defaultValue = $pesertaSeleksi->nomor_pendaftaran;
                                elseif ($p->field_key === 'nama_orang_tua') $defaultValue = $pesertaSeleksi->nama_orang_tua;
                                elseif ($p->field_key === 'no_wa_orang_tua') $defaultValue = $pesertaSeleksi->no_wa_orang_tua;
                                elseif ($p->field_key === 'alamat_orang_tua') $defaultValue = $pesertaSeleksi->alamat;
                            }

                            $otherValue = $jawabanObj?->jawaban_other;
                        @endphp

                        <div class="p-5 hover:bg-slate-50/60 transition-colors {{ $loop->even ? 'bg-slate-50/30' : 'bg-white' }}">
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs flex items-center justify-center mt-0.5">
                                    {{ $p->nomor_urut }}
                                </span>
                                <div class="flex-1 space-y-2">
                                    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1">
                                        <label class="font-bold text-sm text-slate-900">
                                            {{ $p->pertanyaan }}
                                            @if($p->is_required)
                                                <span class="text-red-500 font-black ml-0.5">*</span>
                                            @endif
                                        </label>
                                        @if($p->bobot > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded bg-purple-50 text-purple-800 border border-purple-200">
                                                Bobot: {{ $p->bobot }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($p->petunjuk)
                                        <p class="text-xs text-slate-500 italic">{{ $p->petunjuk }}</p>
                                    @endif

                                    {{-- INPUT CONTROLS BASED ON TIPE_INPUT --}}
                                    <div class="mt-2 pt-1">
                                        {{-- 1. TEXT --}}
                                        @if($p->tipe_input === 'text')
                                            <input type="text" 
                                                   name="jawaban[{{ $p->id }}]" 
                                                   value="{{ old("jawaban.{$p->id}", $defaultValue) }}"
                                                   {{ $p->is_readonly ? 'readonly' : '' }}
                                                   {{ $p->is_required ? 'required' : '' }}
                                                   class="w-full text-xs rounded-lg border-slate-300 {{ $p->is_readonly ? 'bg-slate-100 text-slate-600 cursor-not-allowed' : 'focus:border-purple-500 focus:ring-purple-500' }} shadow-sm"
                                                   placeholder="Ketik jawaban...">

                                        {{-- 2. TEXTAREA --}}
                                        @elseif($p->tipe_input === 'textarea')
                                            <textarea name="jawaban[{{ $p->id }}]" 
                                                      rows="3"
                                                      {{ $p->is_required ? 'required' : '' }}
                                                      class="w-full text-xs rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm"
                                                      placeholder="Tuliskan keterangan lengkap di sini...">{{ old("jawaban.{$p->id}", $defaultValue) }}</textarea>

                                        {{-- 3. DATE --}}
                                        @elseif($p->tipe_input === 'date')
                                            <input type="date" 
                                                   name="jawaban[{{ $p->id }}]" 
                                                   value="{{ old("jawaban.{$p->id}", $defaultValue) }}"
                                                   {{ $p->is_required ? 'required' : '' }}
                                                   class="text-xs rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm">

                                        {{-- 4. RADIO --}}
                                        @elseif($p->tipe_input === 'radio')
                                            <div class="space-y-2">
                                                @foreach(($p->pilihan_opsi ?: []) as $opsi)
                                                    <label class="flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-white hover:bg-purple-50/40 hover:border-purple-200 cursor-pointer transition-colors">
                                                        <input type="radio" 
                                                               name="jawaban[{{ $p->id }}]" 
                                                               value="{{ $opsi }}"
                                                               {{ old("jawaban.{$p->id}", $defaultValue) == $opsi ? 'checked' : '' }}
                                                               {{ $p->is_required ? 'required' : '' }}
                                                               class="text-purple-600 focus:ring-purple-500 border-slate-300">
                                                        <span class="text-xs font-medium text-slate-800">{{ $opsi }}</span>
                                                    </label>
                                                @endforeach

                                                {{-- Other option --}}
                                                @if($p->has_other)
                                                    <div class="flex items-center gap-2 pt-1" x-data="{ isOther: {{ old("jawaban.{$p->id}", $defaultValue) == '__OTHER__' ? 'true' : 'false' }} }">
                                                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                                            <input type="radio" 
                                                                   name="jawaban[{{ $p->id }}]" 
                                                                   value="__OTHER__"
                                                                   x-model="isOther"
                                                                   class="text-purple-600 focus:ring-purple-500 border-slate-300">
                                                            <span>Lainnya (Other):</span>
                                                        </label>
                                                        <input type="text" 
                                                               name="jawaban_other[{{ $p->id }}]" 
                                                               value="{{ old("jawaban_other.{$p->id}", $otherValue) }}"
                                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm"
                                                               placeholder="Sebutkan lainnya...">
                                                    </div>
                                                @endif
                                            </div>

                                        {{-- 5. CHECKBOX (Multi-select) --}}
                                        @elseif($p->tipe_input === 'checkbox')
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @php
                                                    $curVals = is_array(old("jawaban.{$p->id}", $defaultValue)) ? old("jawaban.{$p->id}", $defaultValue) : [];
                                                @endphp
                                                @foreach(($p->pilihan_opsi ?: []) as $opsi)
                                                    <label class="flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-white hover:bg-purple-50/40 hover:border-purple-200 cursor-pointer transition-colors">
                                                        <input type="checkbox" 
                                                               name="jawaban[{{ $p->id }}][]" 
                                                               value="{{ $opsi }}"
                                                               {{ in_array($opsi, $curVals) ? 'checked' : '' }}
                                                               class="rounded text-purple-600 focus:ring-purple-500 border-slate-300">
                                                        <span class="text-xs font-medium text-slate-800">{{ $opsi }}</span>
                                                    </label>
                                                @endforeach
                                            </div>

                                            @if($p->has_other)
                                                <div class="flex items-center gap-2 pt-2">
                                                    <span class="text-xs font-semibold text-slate-700 flex-shrink-0">Lainnya:</span>
                                                    <input type="text" 
                                                           name="jawaban_other[{{ $p->id }}]" 
                                                           value="{{ old("jawaban_other.{$p->id}", $otherValue) }}"
                                                           class="flex-1 text-xs rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500 shadow-sm"
                                                           placeholder="Sebutkan opsi lainnya...">
                                                </div>
                                            @endif

                                        {{-- 6. SCALE 1 - 5 --}}
                                        @elseif($p->tipe_input === 'scale_1_5')
                                            <div class="flex flex-wrap items-center gap-3 py-1">
                                                <span class="text-[11px] font-semibold text-slate-500">1 (Ragu / Kurang Mendukung)</span>
                                                <div class="inline-flex items-center gap-4 bg-slate-100 p-2 rounded-lg border border-slate-200">
                                                    @for($skala = 1; $skala <= 5; $skala++)
                                                        <label class="flex flex-col items-center gap-1 cursor-pointer">
                                                            <span class="text-xs font-bold text-slate-700">{{ $skala }}</span>
                                                            <input type="radio" 
                                                                   name="jawaban[{{ $p->id }}]" 
                                                                   value="{{ $skala }}"
                                                                   {{ old("jawaban.{$p->id}", $defaultValue) == $skala ? 'checked' : '' }}
                                                                   class="text-purple-600 focus:ring-purple-500 border-slate-300">
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-[11px] font-semibold text-slate-500">5 (Sangat Yakin / Mendukung Penuh)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- KESIMPULAN & PENETAPAN PREDIKAT OTOMATIS ORANG TUA --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Hasil & Rekomendasi Wawancara Orang Tua</h4>
                            <p class="text-xs text-slate-500">Kalkulasi kesiapan finansial dan komitmen pendampingan orang tua/wali siswa.</p>
                        </div>
                        @if($sesi->skor_ortu !== null)
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Skor Terakhir</span>
                                <span class="text-lg font-black text-purple-600">{{ number_format($sesi->skor_ortu, 1) }}/100</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Override Predikat --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Predikat Hasil Wawancara Orang Tua
                            </label>
                            <select name="hasil_wawancara_override" class="w-full text-xs font-bold rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500 bg-slate-50">
                                <option value="">-- Gunakan Kalkulasi Otomatis Sistem --</option>
                                <option value="Sangat Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_ortu) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik (A)</option>
                                <option value="Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_ortu) == 'Baik' ? 'selected' : '' }}>Baik (B)</option>
                                <option value="Cukup" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_ortu) == 'Cukup' ? 'selected' : '' }}>Cukup (C)</option>
                                <option value="Kurang Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_ortu) == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik (D)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong bila ingin sistem mengkalkulasi otomatis berdasarkan komitmen finansial & jawaban.</p>
                        </div>

                        {{-- Unggah Bukti / Foto Sesi Wawancara Ortu --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Foto Dokumentasi Wawancara Orang Tua (Opsional)
                            </label>
                            <input type="file" name="foto_ortu" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-slate-300 rounded-lg">
                            @if($sesi->foto_ortu)
                                <div class="mt-2 flex items-center gap-2 text-xs text-purple-600 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <a href="{{ $sesi->foto_ortu_url }}" target="_blank" class="underline hover:text-purple-800">Lihat Foto Dokumentasi Tersimpan</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Catatan Khusus Pewawancara --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Khusus Pewawancara (Komitmen Kesepakatan Biaya, Tanggal Pelunasan, dsb)
                        </label>
                        <textarea name="catatan_pewawancara_ortu" rows="3" class="w-full text-xs rounded-lg border-slate-300 focus:border-purple-500 focus:ring-purple-500" placeholder="Tuliskan catatan khusus komitmen pembayaran orang tua atau hal penting lainnya...">{{ old('catatan_pewawancara_ortu', $sesi->catatan_pewawancara_ortu) }}</textarea>
                    </div>

                    {{-- Action Button --}}
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ request()->routeIs('kaur-spmb.*') ? route('kaur-spmb.administrasi.hasil-wawancara.index') : route('guru.spmb-piket.absensi.index', ['tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id]) }}" 
                           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            Kembali
                        </a>

                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold rounded-lg shadow transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Selesaikan Wawancara Orang Tua
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function wawancaraOrtuForm() {
            return {
                init() {}
            }
        }
    </script>
    @endpush
</x-app-layout>
