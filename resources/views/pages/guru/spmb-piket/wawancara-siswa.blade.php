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
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">Instrumen Wawancara Calon Siswa</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Penilaian & Penggalian Potensi Calon Siswa Baru SMK Telkom Lampung</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $sesi->status_wawancara_siswa === 'Selesai' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                    Status: {{ $sesi->status_wawancara_siswa === 'Selesai' ? 'Selesai Diwawancarai' : 'Belum Selesai' }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="wawancaraSiswaForm()">
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

            {{-- CANDIDATE SUMMARY CARD --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg border border-red-100 flex-shrink-0">
                            {{ substr($pesertaSeleksi->nama_lengkap, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded border border-slate-200">
                                    {{ $pesertaSeleksi->nomor_pendaftaran }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $pesertaSeleksi->jenis_kelamin == 'L' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-pink-50 text-pink-700 border border-pink-200' }}">
                                    {{ $pesertaSeleksi->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                                <span class="text-xs text-slate-500">T.A {{ $pesertaSeleksi->tahun_ajaran }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mt-1">{{ $pesertaSeleksi->nama_lengkap }}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                                <span>Asal Sekolah: <strong class="text-slate-700">{{ $pesertaSeleksi->asal_sekolah ?: '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>No WA: <strong class="text-slate-700">{{ $pesertaSeleksi->no_wa_siswa ?: '-' }}</strong></span>
                                <span>&bull;</span>
                                <span>Jadwal Piket: <strong class="text-slate-700">{{ $pesertaSeleksi->tanggal ? \Carbon\Carbon::parse($pesertaSeleksi->tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') : '-' }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pewawancara Bertugas</span>
                            <span class="text-xs font-bold text-slate-800">{{ $sesi->pewawancara_siswa_nama ?: $user->name }}</span>
                            @if($sesi->waktu_wawancara_siswa)
                                <span class="text-[10px] text-slate-500 block">Selesai: {{ \Carbon\Carbon::parse($sesi->waktu_wawancara_siswa)->format('d/m/Y H:i') }} WIB</span>
                            @endif
                        </div>
                        <div class="w-9 h-9 rounded-md bg-white border border-slate-200 flex items-center justify-center text-slate-700">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM INSTRUMEN WAWANCARA --}}
            <form action="{{ request()->routeIs('kaur-spmb.*') ? route('kaur-spmb.administrasi.wawancara.siswa.store', $pesertaSeleksi) : route('guru.spmb-piket.wawancara.siswa.store', $pesertaSeleksi) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="space-y-6">
                @csrf

                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    <div class="bg-gradient-to-r from-red-600 to-rose-700 px-6 py-4 text-white">
                        <h4 class="font-bold text-sm tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Daftar Butir Pertanyaan Wawancara Siswa (Total: {{ $pertanyaans->count() }} Pertanyaan)
                        </h4>
                        <p class="text-xs text-red-100 mt-0.5">Instrumen resmi Google Form SMK Telkom Lampung yang terdigitalisasi dan terotomatisasi.</p>
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
                                if ($p->field_key === 'no_pendaftaran') $defaultValue = $pesertaSeleksi->nomor_pendaftaran;
                                elseif ($p->field_key === 'nama_lengkap') $defaultValue = $pesertaSeleksi->nama_lengkap;
                                elseif ($p->field_key === 'no_wa_siswa') $defaultValue = $pesertaSeleksi->no_wa_siswa;
                                elseif ($p->field_key === 'asal_sekolah') $defaultValue = $pesertaSeleksi->asal_sekolah;
                                elseif ($p->field_key === 'alamat') $defaultValue = $pesertaSeleksi->alamat;
                                elseif ($p->field_key === 'tanggal_lahir') $defaultValue = $pesertaSeleksi->tanggal_lahir ? \Carbon\Carbon::parse($pesertaSeleksi->tanggal_lahir)->format('Y-m-d') : null;
                                elseif ($p->field_key === 'jurusan_pilihan') $defaultValue = array_filter([$pesertaSeleksi->jurusan_pilihan, $pesertaSeleksi->jurusan_pilihan_2]);
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
                                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-800 border border-amber-200">
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
                                                   class="w-full text-xs rounded-lg border-slate-300 {{ $p->is_readonly ? 'bg-slate-100 text-slate-600 cursor-not-allowed' : 'focus:border-red-500 focus:ring-red-500' }} shadow-sm"
                                                   placeholder="Ketik jawaban...">

                                        {{-- 2. TEXTAREA --}}
                                        @elseif($p->tipe_input === 'textarea')
                                            <textarea name="jawaban[{{ $p->id }}]" 
                                                      rows="3"
                                                      {{ $p->is_required ? 'required' : '' }}
                                                      class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 shadow-sm"
                                                      placeholder="Tuliskan uraian lengkap di sini...">{{ old("jawaban.{$p->id}", $defaultValue) }}</textarea>

                                        {{-- 3. DATE --}}
                                        @elseif($p->tipe_input === 'date')
                                            <input type="date" 
                                                   name="jawaban[{{ $p->id }}]" 
                                                   value="{{ old("jawaban.{$p->id}", $defaultValue) }}"
                                                   {{ $p->is_required ? 'required' : '' }}
                                                   class="text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 shadow-sm">

                                        {{-- 4. RADIO --}}
                                        @elseif($p->tipe_input === 'radio')
                                            <div class="space-y-2">
                                                @foreach(($p->pilihan_opsi ?: []) as $opsi)
                                                    <label class="flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-white hover:bg-red-50/40 hover:border-red-200 cursor-pointer transition-colors">
                                                        <input type="radio" 
                                                               name="jawaban[{{ $p->id }}]" 
                                                               value="{{ $opsi }}"
                                                               {{ old("jawaban.{$p->id}", $defaultValue) == $opsi ? 'checked' : '' }}
                                                               {{ $p->is_required ? 'required' : '' }}
                                                               class="text-red-600 focus:ring-red-500 border-slate-300">
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
                                                                   class="text-red-600 focus:ring-red-500 border-slate-300">
                                                            <span>Lainnya (Other):</span>
                                                        </label>
                                                        <input type="text" 
                                                               name="jawaban_other[{{ $p->id }}]" 
                                                               value="{{ old("jawaban_other.{$p->id}", $otherValue) }}"
                                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 shadow-sm"
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
                                                    <label class="flex items-center gap-2.5 p-2 rounded-lg border border-slate-200 bg-white hover:bg-red-50/40 hover:border-red-200 cursor-pointer transition-colors">
                                                        <input type="checkbox" 
                                                               name="jawaban[{{ $p->id }}][]" 
                                                               value="{{ $opsi }}"
                                                               {{ in_array($opsi, $curVals) ? 'checked' : '' }}
                                                               class="rounded text-red-600 focus:ring-red-500 border-slate-300">
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
                                                           class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 shadow-sm"
                                                           placeholder="Sebutkan opsi lainnya...">
                                                </div>
                                            @endif

                                        {{-- 6. SCALE 1 - 5 --}}
                                        @elseif($p->tipe_input === 'scale_1_5')
                                            <div class="flex flex-wrap items-center gap-3 py-1">
                                                <span class="text-[11px] font-semibold text-slate-500">1 (Sangat Rendah / Ragu)</span>
                                                <div class="inline-flex items-center gap-4 bg-slate-100 p-2 rounded-lg border border-slate-200">
                                                    @for($skala = 1; $skala <= 5; $skala++)
                                                        <label class="flex flex-col items-center gap-1 cursor-pointer">
                                                            <span class="text-xs font-bold text-slate-700">{{ $skala }}</span>
                                                            <input type="radio" 
                                                                   name="jawaban[{{ $p->id }}]" 
                                                                   value="{{ $skala }}"
                                                                   {{ old("jawaban.{$p->id}", $defaultValue) == $skala ? 'checked' : '' }}
                                                                   class="text-red-600 focus:ring-red-500 border-slate-300">
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-[11px] font-semibold text-slate-500">5 (Sangat Tinggi / Yakin)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- KESIMPULAN & PENETAPAN PREDIKAT OTOMATIS --}}
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6 space-y-5">
                    <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-base text-slate-900">Hasil & Rekomendasi Wawancara</h4>
                            <p class="text-xs text-slate-500">Sistem akan mengalkulasi skor jawaban otomatis. Anda juga dapat menentukan predikat manual.</p>
                        </div>
                        @if($sesi->skor_siswa !== null)
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Skor Terakhir</span>
                                <span class="text-lg font-black text-red-600">{{ number_format($sesi->skor_siswa, 1) }}/100</span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Override Predikat --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Predikat Hasil Wawancara Calon Siswa
                            </label>
                            <select name="hasil_wawancara_override" class="w-full text-xs font-bold rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500 bg-slate-50">
                                <option value="">-- Gunakan Kalkulasi Otomatis Sistem --</option>
                                <option value="Sangat Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_siswa) == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik (A)</option>
                                <option value="Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_siswa) == 'Baik' ? 'selected' : '' }}>Baik (B)</option>
                                <option value="Cukup" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_siswa) == 'Cukup' ? 'selected' : '' }}>Cukup (C)</option>
                                <option value="Kurang Baik" {{ old('hasil_wawancara_override', $sesi->hasil_wawancara_siswa) == 'Kurang Baik' ? 'selected' : '' }}>Kurang Baik (D)</option>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong bila ingin sistem mengkalkulasi otomatis berdasarkan bobot jawaban.</p>
                        </div>

                        {{-- Unggah Bukti / Foto Sesi Wawancara Siswa --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Foto Dokumentasi Wawancara Calon Siswa (Opsional)
                            </label>
                            <input type="file" name="foto_siswa" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-slate-300 rounded-lg">
                            @if($sesi->foto_siswa)
                                <div class="mt-2 flex items-center gap-2 text-xs text-emerald-600 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <a href="{{ $sesi->foto_siswa_url }}" target="_blank" class="underline hover:text-emerald-800">Lihat Foto Dokumentasi Tersimpan</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Catatan Khusus Pewawancara --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan & Observasi Khusus Pewawancara (Catatan Karakter, Kerapihan, dsb)
                        </label>
                        <textarea name="catatan_pewawancara_siswa" rows="3" class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500" placeholder="Tuliskan catatan khusus terkait kepribadian, minat bakat, atau temuan penting lainnya saat wawancara berlangsung...">{{ old('catatan_pewawancara_siswa', $sesi->catatan_pewawancara_siswa) }}</textarea>
                    </div>

                    {{-- Action Button --}}
                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ request()->routeIs('kaur-spmb.*') ? route('kaur-spmb.administrasi.hasil-wawancara.index') : route('guru.spmb-piket.absensi.index', ['tanggal_id' => $pesertaSeleksi->spmb_piket_tanggal_id]) }}" 
                           class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            Kembali
                        </a>

                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Selesaikan Wawancara Siswa
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function wawancaraSiswaForm() {
            return {
                init() {
                    // Initialization if needed
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
