<x-app-layout px="0">

<style>
    .score-btn { width:2.75rem; height:2.75rem; border-radius:.75rem; font-size:.75rem; font-weight:800; border:2px solid transparent; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; flex-direction:column; line-height:1; }
    .indikator-row { display:flex; align-items:center; gap:1rem; padding:.875rem 1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:1rem; transition:background .15s; }
    .indikator-row:hover { background:#f1f5f9; }
    .kategori-block { border:1px solid #e2e8f0; border-radius:1.25rem; overflow:hidden; margin-bottom:1rem; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.03); }
    .kategori-head { padding:.875rem 1.25rem; background:linear-gradient(to right,#f8fafc,#f1f5f9); border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:.75rem; }
</style>

<div x-data="penilaianForm()" x-init="init()" class="min-h-screen bg-slate-50 py-6">

    {{-- TOAST --}}
    <div class="fixed top-6 right-6 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:300px">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-2xl shadow-2xl border text-sm font-semibold"
                :class="{'bg-emerald-50 border-emerald-300 text-emerald-900':t.type==='success','bg-red-50 border-red-300 text-red-900':t.type==='error','bg-amber-50 border-amber-300 text-amber-900':t.type==='warning'}"
                :style="'transition:opacity .35s,transform .35s;opacity:'+(t.visible?1:0)+';transform:translateY('+(t.visible?'0':'-8px')+')'">
                <span x-text="t.type==='success'?'✅':(t.type==='warning'?'⚠️':'❌')"></span>
                <span class="flex-1" x-text="t.message"></span>
                <button @click="removeToast(t.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
            </div>
        </template>
    </div>

    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ══ TOP HEADER CARD (NON-STICKY / NON-MELAYANG) ══ --}}
        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Left: Student & Exam info --}}
            <div class="flex items-center gap-4">
                <a href="{{ route('guru-kelas.penilaian-ukk.show', $ujian->id) }}"
                    class="w-11 h-11 rounded-2xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors text-slate-600 shrink-0"
                    title="Kembali ke Daftar Peserta">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="w-12 h-12 rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-black text-lg shadow-md shrink-0">
                    @if($siswa->user?->avatar)
                        <img src="{{ $siswa->user->avatar }}" class="w-full h-full object-cover">
                    @else
                        <span>{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $ujian->jurusan }}
                        </span>
                        @if($ujian->tahunPelajaran)
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600">
                                TP {{ $ujian->tahunPelajaran->tahun }}
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg font-black text-slate-900 mt-1 flex items-center gap-2 flex-wrap">
                        <span>{{ $siswa->nama_lengkap }}</span>
                        <span class="text-xs font-normal text-slate-400">NIS: {{ $siswa->nis }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $ujian->nama_ujian }} @if($ujian->nama_project) &middot; Project: {{ $ujian->nama_project }} @endif</p>
                </div>
            </div>

            {{-- Right: Live Score Displays & Actions --}}
            <div class="flex flex-wrap items-center gap-3 lg:self-center">
                {{-- Score Pills --}}
                <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-200">
                    <div class="text-center px-3 py-1 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-[10px] uppercase tracking-wider text-blue-500 font-bold">Pengetahuan</p>
                        <p class="text-sm font-black text-blue-700" x-text="skorPengetahuan.toFixed(1)"></p>
                    </div>
                    <div class="text-center px-3 py-1 bg-emerald-50 rounded-xl border border-emerald-100">
                        <p class="text-[10px] uppercase tracking-wider text-emerald-500 font-bold">Keterampilan</p>
                        <p class="text-sm font-black text-emerald-700" x-text="skorKeterampilan.toFixed(1)"></p>
                    </div>
                    <div class="text-center px-3.5 py-1 bg-violet-600 rounded-xl text-white shadow-sm">
                        <p class="text-[10px] uppercase tracking-wider text-violet-200 font-bold">Nilai Akhir</p>
                        <p class="text-base font-black text-white" x-text="nilaiAkhir.toFixed(1)"></p>
                    </div>
                </div>

                {{-- Save Button --}}
                <button @click="simpan()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black rounded-2xl shadow-md hover:shadow-lg transition-all active:scale-95 text-xs disabled:opacity-50">
                    <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Penilaian'"></span>
                </button>

                {{-- Cetak PDF Button --}}
                <button @click="isComplete && window.open('{{ route('guru-kelas.penilaian-ukk.pdf', [$ujian->id, $siswa->id]) }}', '_blank')"
                    :disabled="!isComplete"
                    :class="isComplete ? 'bg-violet-600 hover:bg-violet-700 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                    class="inline-flex items-center gap-2 px-5 py-2.5 font-black rounded-2xl transition-all text-xs"
                    :title="isComplete ? 'Cetak lembar PDF penilaian' : 'Selesaikan seluruh penilaian pengetahuan dan keterampilan terlebih dahulu'">
                    🖨️ Cetak PDF
                </button>
            </div>
        </div>

        {{-- ══ VIEW TAB SELECTOR ══ --}}
        <div class="bg-white rounded-2xl p-1.5 border border-gray-200/80 shadow-sm flex items-center gap-2">
            <button type="button" @click="activeTab = 'pengetahuan'"
                :class="activeTab === 'pengetahuan' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                class="flex-1 py-2.5 px-4 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2">
                <span>📝</span> Aspek Pengetahuan (Esai Siswa)
            </button>
            <button type="button" @click="activeTab = 'keterampilan'"
                :class="activeTab === 'keterampilan' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                class="flex-1 py-2.5 px-4 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2">
                <span>⚙️</span> Aspek Keterampilan Praktik
            </button>
            <button type="button" @click="activeTab = 'all'"
                :class="activeTab === 'all' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                class="py-2.5 px-5 rounded-xl font-black text-xs transition-all flex items-center justify-center gap-2">
                <span>📋</span> Tampilkan Semua
            </button>
        </div>

        @if($ujian->instrumens->isEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-12 text-center">
                <p class="text-4xl mb-3">⚠️</p>
                <p class="text-amber-900 font-bold text-base">Instrumen penilaian belum diset untuk ujian ini.</p>
                <p class="text-amber-700 text-xs mt-1">Hubungi Kaprodi untuk menambahkan instrumen penilaian UKK.</p>
            </div>
        @else

            {{-- ══ ASPEK PENGETAHUAN SECTION ══ --}}
            <div x-show="activeTab === 'pengetahuan' || activeTab === 'all'" class="space-y-6">
                @foreach($ujian->instrumens as $ins)
                    @if($ins->soalPengetahuan->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
                        {{-- Section Title --}}
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50/70 to-indigo-50/70 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <h4 class="font-black text-slate-900 text-base">{{ $ins->nama_instrumen }} &mdash; Aspek Pengetahuan</h4>
                                    <p class="text-xs text-slate-500">Bobot Pengetahuan: <strong class="text-blue-700">{{ $ins->bobot_pengetahuan }}%</strong> &middot; Koreksi esai siswa berdasarkan kunci jawaban acuan.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-3 py-1 bg-white rounded-xl border border-blue-200 text-blue-700"
                                    x-text="benarCount({{ json_encode($ins->soalPengetahuan->pluck('id')->all()) }}) + ' / {{ $ins->soalPengetahuan->count() }} Benar'">
                                </span>
                            </div>
                        </div>

                        {{-- Soal Cards (Full Width & Spacious) --}}
                        <div class="p-6 space-y-6">
                            @foreach($ins->soalPengetahuan as $soal)
                            @php
                                $jSiswa = $jawabanP[$soal->id] ?? null;
                                $hasAnswer = !empty(trim((string)$jSiswa));
                            @endphp
                            <div class="bg-slate-50/60 rounded-2xl border border-slate-200/90 p-5 space-y-4 hover:border-indigo-300 transition-all">
                                {{-- Card Header --}}
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <span class="w-7 h-7 rounded-xl bg-slate-800 text-white font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $loop->iteration }}
                                        </span>
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pertanyaan Soal Esai:</span>
                                            <h5 class="text-base font-bold text-slate-900 leading-relaxed mt-0.5">
                                                {{ $soal->pertanyaan }}
                                            </h5>
                                        </div>
                                    </div>

                                    {{-- Status Badge --}}
                                    <div class="shrink-0">
                                        <template x-if="nilaiP[{{ $soal->id }}] === 1">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span>✓</span> Benar (1 Poin)
                                            </span>
                                        </template>
                                        <template x-if="nilaiP[{{ $soal->id }}] === 0">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black bg-rose-100 text-rose-800 border border-rose-300">
                                                <span>✗</span> Salah (0 Poin)
                                            </span>
                                        </template>
                                        <template x-if="nilaiP[{{ $soal->id }}] === undefined || nilaiP[{{ $soal->id }}] === null">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-slate-200 text-slate-600">
                                                Belum Dikoreksi
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Panduan Kunci Jawaban dari Kaprodi --}}
                                <div class="bg-indigo-50/80 border border-indigo-200 rounded-2xl p-4 text-xs">
                                    <div class="flex items-center gap-1.5 font-bold text-indigo-900 mb-1">
                                        <span>🔑</span>
                                        <span>Panduan Kunci Jawaban / Rubrik Acuan Kaprodi:</span>
                                    </div>
                                    @if(!empty($soal->kunci_jawaban))
                                        <p class="text-indigo-950 font-medium whitespace-pre-line leading-relaxed pl-5">{{ $soal->kunci_jawaban }}</p>
                                    @else
                                        <p class="text-indigo-400 italic pl-5">(Kaprodi belum menambahkan kunci jawaban untuk soal ini)</p>
                                    @endif
                                </div>

                                {{-- Jawaban Siswa --}}
                                <div class="bg-white border-2 border-slate-200 rounded-2xl p-4 text-xs shadow-sm">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <span class="font-bold text-slate-800 flex items-center gap-1.5">
                                            <span>✍️</span> Jawaban Siswa ({{ $siswa->nama_lengkap }}):
                                        </span>
                                        @if($hasAnswer)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                                Belum Diisi Siswa
                                            </span>
                                        @endif
                                    </div>
                                    @if($hasAnswer)
                                        <div class="text-slate-900 font-sans text-sm whitespace-pre-line leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                                            {{ $jSiswa }}
                                        </div>
                                    @else
                                        <p class="text-slate-400 italic py-2">(Siswa belum mengisi atau mengirimkan jawaban esai ini)</p>
                                    @endif
                                </div>

                                {{-- Penilaian Penguji & Catatan --}}
                                <div class="pt-2 border-t border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-700">Koreksi Penguji:</span>
                                        <button type="button"
                                            @click="togglePengetahuan({{ $soal->id }}, 1)"
                                            :class="nilaiP[{{ $soal->id }}] === 1
                                                ? 'bg-emerald-600 border-emerald-600 text-white shadow-md'
                                                : 'bg-emerald-50 border-emerald-200 text-emerald-700 hover:bg-emerald-100'"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black border transition-all active:scale-95">
                                            <span>✓</span> Benar (1)
                                        </button>
                                        <button type="button"
                                            @click="togglePengetahuan({{ $soal->id }}, 0)"
                                            :class="nilaiP[{{ $soal->id }}] === 0
                                                ? 'bg-rose-600 border-rose-600 text-white shadow-md'
                                                : 'bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100'"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black border transition-all active:scale-95">
                                            <span>✗</span> Salah (0)
                                        </button>
                                        <button type="button"
                                            x-show="nilaiP[{{ $soal->id }}] !== undefined && nilaiP[{{ $soal->id }}] !== null"
                                            @click="resetPengetahuan({{ $soal->id }})"
                                            class="text-xs text-slate-400 hover:text-slate-600 underline ml-2">
                                            Reset
                                        </button>
                                    </div>

                                    {{-- Catatan Penguji (Input Text Singkat) --}}
                                    <div class="flex-1 max-w-md">
                                        <input type="text"
                                            x-model="catatanP[{{ $soal->id }}]"
                                            placeholder="Catatan koreksi untuk siswa (opsional)…"
                                            class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl px-3 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- ══ ASPEK KETERAMPILAN SECTION ══ --}}
            <div x-show="activeTab === 'keterampilan' || activeTab === 'all'" class="space-y-6">
                @foreach($ujian->instrumens as $ins)
                    @if($ins->kategoriKeterampilan->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
                        {{-- Section Title --}}
                        <div class="px-6 py-4 bg-gradient-to-r from-emerald-50/70 to-teal-50/70 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <h4 class="font-black text-slate-900 text-base">{{ $ins->nama_instrumen }} &mdash; Aspek Keterampilan</h4>
                                    <p class="text-xs text-slate-500">Bobot Keterampilan: <strong class="text-emerald-700">{{ 100 - $ins->bobot_pengetahuan }}%</strong> &middot; Skala: 0=Belum, 1=Cukup, 2=Baik, 3=Sangat Baik.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori List --}}
                        <div class="p-6 space-y-5">
                            @foreach($ins->kategoriKeterampilan as $kat)
                            <div class="kategori-block">
                                <div class="kategori-head">
                                    <span class="w-6 h-6 bg-violet-600 text-white rounded-lg flex items-center justify-center text-xs font-black shrink-0">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h5 class="font-black text-slate-900 text-sm flex-1">{{ $kat->nama_kategori }}</h5>
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-lg bg-violet-100 text-violet-800">
                                        Bobot {{ $kat->bobot }}%
                                    </span>
                                </div>
                                <div class="p-4 space-y-2 bg-white">
                                    @foreach($kat->indikator as $ind)
                                    <div class="indikator-row">
                                        <span class="w-5 h-5 rounded bg-emerald-500 text-white flex items-center justify-center text-[10px] font-black shrink-0">
                                            {{ $loop->iteration }}
                                        </span>
                                        <p class="flex-1 text-sm text-slate-800 font-medium leading-relaxed">{{ $ind->nama_indikator }}</p>
                                        {{-- 0 / 1 / 2 / 3 Selector Buttons --}}
                                        <div class="flex gap-1.5 shrink-0">
                                            @foreach([
                                                0 => ['label'=>'Belum','bg'=>'bg-slate-50','border'=>'border-slate-300','text'=>'text-slate-700','active'=>'bg-slate-600 border-slate-600 text-white'],
                                                1 => ['label'=>'Cukup','bg'=>'bg-amber-50','border'=>'border-amber-300','text'=>'text-amber-800','active'=>'bg-amber-500 border-amber-500 text-white'],
                                                2 => ['label'=>'Baik','bg'=>'bg-blue-50','border'=>'border-blue-300','text'=>'text-blue-800','active'=>'bg-blue-600 border-blue-600 text-white'],
                                                3 => ['label'=>'S.Baik','bg'=>'bg-emerald-50','border'=>'border-emerald-300','text'=>'text-emerald-800','active'=>'bg-emerald-600 border-emerald-600 text-white'],
                                            ] as $val => $cfg)
                                            <button type="button"
                                                @click="setKeterampilan({{ $ind->id }}, {{ $val }})"
                                                :class="nilaiK[{{ $ind->id }}] === {{ $val }} ? '{{ $cfg['active'] }} shadow-sm' : '{{ $cfg['bg'] }} {{ $cfg['border'] }} {{ $cfg['text'] }} hover:brightness-95'"
                                                class="score-btn">
                                                <span class="text-sm font-black">{{ $val }}</span>
                                                <span class="text-[8px] font-bold mt-0.5">{{ $cfg['label'] }}</span>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- ══ BOTTOM SUMMARY & ACTIONS (NON-STICKY) ══ --}}
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h4 class="font-black text-slate-900 text-base">Rekapitulasi Penilaian Siswa</h4>
                    <div class="flex items-center gap-4 text-xs font-bold text-slate-600 mt-1 flex-wrap">
                        <span>Pengetahuan: <strong class="text-blue-600 text-sm" x-text="skorPengetahuan.toFixed(1)"></strong></span>
                        <span>&bull;</span>
                        <span>Keterampilan: <strong class="text-emerald-600 text-sm" x-text="skorKeterampilan.toFixed(1)"></strong></span>
                        <span>&bull;</span>
                        <span>Nilai Akhir UKK: <strong class="text-violet-700 text-base font-black" x-text="nilaiAkhir.toFixed(1)"></strong></span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('guru-kelas.penilaian-ukk.show', $ujian->id) }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition-colors">
                        Kembali ke Daftar
                    </a>
                    <button @click="simpan()" :disabled="saving"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black rounded-2xl shadow-md transition-all active:scale-95 text-xs disabled:opacity-50">
                        <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Penilaian'"></span>
                    </button>
                    <button @click="isComplete && window.open('{{ route('guru-kelas.penilaian-ukk.pdf', [$ujian->id, $siswa->id]) }}', '_blank')"
                        :disabled="!isComplete"
                        :class="isComplete ? 'bg-violet-600 hover:bg-violet-700 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 font-black rounded-2xl transition-all text-xs"
                        :title="isComplete ? 'Cetak PDF' : 'Selesaikan seluruh penilaian pengetahuan dan keterampilan terlebih dahulu'">
                        🖨️ Cetak PDF
                    </button>
                </div>
            </div>

        @endif

    </div>
</div>

@php
    $instrumensData = $ujian->instrumens->map(fn($i) => [
        'id'                => $i->id,
        'bobot_pengetahuan' => $i->bobot_pengetahuan,
        'soal'              => $i->soalPengetahuan->pluck('id')->all(),
        'kategori'          => $i->kategoriKeterampilan->map(fn($k) => [
            'id'        => $k->id,
            'bobot'     => $k->bobot,
            'indikator' => $k->indikator->pluck('id')->all(),
        ])->values()->all(),
    ])->values()->all();
@endphp

@push('scripts')
<script>
function penilaianForm() {
    const INSTRUMENS = @json($instrumensData);
    const INIT_P     = @json($nilaiP);
    const INIT_CAT   = @json($catatanP ?? []);
    const INIT_K     = @json($nilaiK);
    const SAVE_URL   = '{{ route("guru-kelas.penilaian-ukk.simpan", [$ujian->id, $siswa->id]) }}';

    return {
        saving: false,
        activeTab: 'pengetahuan',
        toasts: [],
        toastSeq: 0,
        nilaiP: { ...INIT_P },
        catatanP: { ...INIT_CAT },
        nilaiK: { ...INIT_K },

        init() {},

        togglePengetahuan(soalId, val) {
            if (this.nilaiP[soalId] === val) {
                delete this.nilaiP[soalId];
            } else {
                this.nilaiP[soalId] = val;
            }
            this.nilaiP = { ...this.nilaiP };
        },

        resetPengetahuan(soalId) {
            delete this.nilaiP[soalId];
            this.nilaiP = { ...this.nilaiP };
        },

        setKeterampilan(indId, val) {
            this.nilaiK[indId] = val;
            this.nilaiK = { ...this.nilaiK };
        },

        benarCount(soalIds) {
            return soalIds.filter(id => this.nilaiP[id] === 1).length;
        },

        get isComplete() {
            if (!INSTRUMENS.length) return false;
            const allSoalIds = INSTRUMENS.flatMap(i => i.soal);
            const allIndIds  = INSTRUMENS.flatMap(i => i.kategori.flatMap(k => k.indikator));
            const soalOk = allSoalIds.length === 0 || allSoalIds.every(id => this.nilaiP[id] !== undefined && this.nilaiP[id] !== null);
            const indOk  = allIndIds.length === 0  || allIndIds.every(id => this.nilaiK[id] !== undefined && this.nilaiK[id] !== null);
            return soalOk && indOk;
        },

        get skorPengetahuan() {
            let total = 0, count = 0;
            for (const ins of INSTRUMENS) {
                if (!ins.soal.length) continue;
                const benar = ins.soal.filter(id => this.nilaiP[id] === 1).length;
                total += (benar / ins.soal.length) * 100 * (ins.bobot_pengetahuan / 100);
                count++;
            }
            return count ? total / count * INSTRUMENS.length : total;
        },

        get skorKeterampilan() {
            let total = 0, count = 0;
            for (const ins of INSTRUMENS) {
                if (!ins.kategori.length) continue;
                let insScore = 0;
                for (const kat of ins.kategori) {
                    if (!kat.indikator.length) continue;
                    const avg = kat.indikator.reduce((s, id) => s + (this.nilaiK[id] ?? 0), 0) / kat.indikator.length;
                    insScore += (avg / 3) * 100 * (kat.bobot / 100);
                }
                total += insScore * ((100 - ins.bobot_pengetahuan) / 100);
                count++;
            }
            return count ? total / count * INSTRUMENS.length : total;
        },

        get nilaiAkhir() {
            if (!INSTRUMENS.length) return 0;
            let total = 0;
            for (const ins of INSTRUMENS) {
                const bp = ins.bobot_pengetahuan / 100;
                const bk = 1 - bp;
                let p = 0, k = 0;
                if (ins.soal.length) {
                    const benar = ins.soal.filter(id => this.nilaiP[id] === 1).length;
                    p = (benar / ins.soal.length) * 100;
                }
                if (ins.kategori.length) {
                    let kScore = 0;
                    for (const kat of ins.kategori) {
                        if (!kat.indikator.length) continue;
                        const avg = kat.indikator.reduce((s, id) => s + (this.nilaiK[id] ?? 0), 0) / kat.indikator.length;
                        kScore += (avg / 3) * 100 * (kat.bobot / 100);
                    }
                    k = kScore;
                }
                total += p * bp + k * bk;
            }
            return total / INSTRUMENS.length;
        },

        async simpan() {
            this.saving = true;
            try {
                const res = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        pengetahuan: this.nilaiP,
                        catatan_pengetahuan: this.catatanP,
                        keterampilan: this.nilaiK,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.toast(data.message || 'Terjadi kesalahan saat menyimpan.', 'error');
                    return;
                }
                this.toast(data.message || 'Penilaian berhasil disimpan.', 'success');
            } catch {
                this.toast('Koneksi gagal saat menyimpan penilaian.', 'error');
            } finally {
                this.saving = false;
            }
        },

        toast(msg, type = 'success') {
            const id = ++this.toastSeq;
            this.toasts.push({ id, message: msg, type, visible: false });
            this.$nextTick(() => { const t = this.toasts.find(t => t.id === id); if (t) t.visible = true; });
            setTimeout(() => this.removeToast(id), type === 'success' ? 3500 : 5000);
        },
        removeToast(id) {
            const t = this.toasts.find(t => t.id === id); if (t) t.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 400);
        },
    };
}
</script>
@endpush
</x-app-layout>
