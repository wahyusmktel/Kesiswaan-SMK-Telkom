<x-app-layout px="0">

<style>
    .soal-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; padding:.875rem 1rem; transition:box-shadow .15s; }
    .soal-card:hover { box-shadow:0 2px 8px rgba(99,102,241,.1); }
    .score-btn { width:2.75rem; height:2.75rem; border-radius:.625rem; font-size:.75rem; font-weight:800; border:2px solid transparent; cursor:pointer; transition:all .15s; display:flex; align-items:center; justify-content:center; flex-direction:column; line-height:1; }
    .indikator-row { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:.875rem; }
    .kategori-block { border:2px solid #e5e7eb; border-radius:1rem; overflow:hidden; margin-bottom:.75rem; }
    .kategori-head { padding:.75rem 1rem; background:linear-gradient(to right,#f5f3ff,#ede9fe); border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:.625rem; }
</style>

<div x-data="penilaianForm()" x-init="init()" class="min-h-screen bg-slate-50">

    {{-- ══ STICKY HEADER ══ --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center gap-3 px-4 py-3 max-w-screen-xl mx-auto">
            <a href="{{ route('guru-kelas.penilaian-ukk.show', $ujian->id) }}"
                class="flex-shrink-0 w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>

            {{-- Student info --}}
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <div class="w-9 h-9 rounded-full overflow-hidden bg-orange-100 flex items-center justify-center shrink-0">
                    @if($siswa->user?->avatar)
                        <img src="{{ $siswa->user->avatar }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-orange-600 font-black text-sm">{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <p class="font-black text-gray-900 text-sm truncate">{{ $siswa->nama_lengkap }}</p>
                    <p class="text-xs text-gray-400">NIS: {{ $siswa->nis }} &middot; {{ $ujian->nama_ujian }}</p>
                </div>
            </div>

            {{-- Live score display --}}
            <div class="hidden sm:flex items-center gap-3 shrink-0">
                <div class="text-center px-3 py-1.5 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-xs text-blue-400 font-semibold">Pengetahuan</p>
                    <p class="text-base font-black text-blue-700" x-text="skorPengetahuan.toFixed(1)"></p>
                </div>
                <div class="text-center px-3 py-1.5 bg-emerald-50 rounded-xl border border-emerald-100">
                    <p class="text-xs text-emerald-400 font-semibold">Keterampilan</p>
                    <p class="text-base font-black text-emerald-700" x-text="skorKeterampilan.toFixed(1)"></p>
                </div>
                <div class="text-center px-3 py-1.5 bg-violet-50 rounded-xl border border-violet-100">
                    <p class="text-xs text-violet-400 font-semibold">Nilai Akhir</p>
                    <p class="text-base font-black text-violet-700" x-text="nilaiAkhir.toFixed(1)"></p>
                </div>
            </div>

            {{-- Save button --}}
            <button @click="simpan()" :disabled="saving"
                class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black rounded-xl shadow-md hover:from-orange-400 hover:to-amber-400 transition-all disabled:opacity-50 text-sm">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="saving ? 'Menyimpan…' : '💾 Simpan'"></span>
            </button>
        </div>
    </div>

    {{-- TOAST --}}
    <div class="fixed top-20 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:280px">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-semibold"
                :class="{'bg-emerald-50 border-emerald-200 text-emerald-800':t.type==='success','bg-red-50 border-red-200 text-red-800':t.type==='error'}"
                :style="'transition:opacity .35s,transform .35s;opacity:'+(t.visible?1:0)+';transform:translateY('+(t.visible?'0':'-8px')+')'">
                <span x-text="t.type==='success'?'✅':'❌'"></span>
                <span class="flex-1" x-text="t.message"></span>
                <button @click="removeToast(t.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
            </div>
        </template>
    </div>

    {{-- ══ MAIN CONTENT ══ --}}
    <div class="p-4 sm:p-6 max-w-screen-xl mx-auto">

        @if($ujian->instrumens->isEmpty())
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
                <p class="text-4xl mb-3">⚠️</p>
                <p class="text-amber-800 font-bold">Instrumen penilaian belum diset untuk ujian ini.</p>
                <p class="text-amber-600 text-sm mt-1">Hubungi Kaprodi untuk menambahkan instrumen penilaian.</p>
            </div>
        @else
            @foreach($ujian->instrumens as $instrumen)
            <div class="mb-8">
                {{-- Instrumen title --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-violet-600 text-white rounded-xl flex items-center justify-center font-black text-sm">{{ $loop->iteration }}</div>
                    <h4 class="font-black text-gray-800 text-base">{{ $instrumen->nama_instrumen }}</h4>
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-400 font-semibold shrink-0">
                        Pengetahuan {{ $instrumen->bobot_pengetahuan }}% &middot;
                        Keterampilan {{ 100 - $instrumen->bobot_pengetahuan }}%
                    </span>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

                    {{-- ── PENGETAHUAN ── --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center text-white text-sm">📝</div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm">Penilaian Pengetahuan</p>
                                    <p class="text-[11px] text-gray-400">✓ Benar = 1 poin &nbsp;·&nbsp; ✗ Salah = 0 poin</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-blue-500 font-semibold">Bobot</p>
                                <p class="font-black text-blue-700">{{ $instrumen->bobot_pengetahuan }}%</p>
                            </div>
                        </div>

                        @if($instrumen->soalPengetahuan->isEmpty())
                            <div class="p-6 text-center text-gray-400 text-sm italic">Belum ada soal pengetahuan.</div>
                        @else
                        <div class="p-4 flex flex-col gap-2">
                            @foreach($instrumen->soalPengetahuan as $soal)
                            <div class="soal-card flex items-start gap-3">
                                <div class="w-6 h-6 rounded-md bg-blue-500 text-white flex items-center justify-center text-[11px] font-black shrink-0 mt-0.5">
                                    {{ $loop->iteration }}
                                </div>
                                <p class="flex-1 text-sm text-gray-700 leading-relaxed">{{ $soal->pertanyaan }}</p>
                                <div class="flex gap-1.5 shrink-0">
                                    {{-- Benar --}}
                                    <button type="button"
                                        @click="togglePengetahuan({{ $soal->id }}, 1)"
                                        :class="nilaiP[{{ $soal->id }}] === 1
                                            ? 'bg-emerald-500 border-emerald-500 text-white'
                                            : 'bg-emerald-50 border-emerald-200 text-emerald-600 hover:bg-emerald-100'"
                                        class="score-btn">
                                        <span class="text-base">✓</span>
                                        <span class="text-[9px] mt-0.5">Benar</span>
                                    </button>
                                    {{-- Salah --}}
                                    <button type="button"
                                        @click="togglePengetahuan({{ $soal->id }}, 0)"
                                        :class="nilaiP[{{ $soal->id }}] === 0 && nilaiP[{{ $soal->id }}] !== undefined
                                            ? 'bg-red-500 border-red-500 text-white'
                                            : 'bg-red-50 border-red-200 text-red-500 hover:bg-red-100'"
                                        class="score-btn">
                                        <span class="text-base">✗</span>
                                        <span class="text-[9px] mt-0.5">Salah</span>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Pengetahuan summary --}}
                        <div class="px-4 pb-4">
                            <div class="flex items-center justify-between text-xs font-semibold text-gray-500 mb-1.5">
                                <span>Jawaban benar</span>
                                <span class="text-blue-600 font-black"
                                    x-text="benarCount({{ json_encode($instrumen->soalPengetahuan->pluck('id')->all()) }}) + ' / {{ $instrumen->soalPengetahuan->count() }}'">
                                </span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-400 rounded-full transition-all duration-300"
                                    :style="'width:' + (benarCount({{ json_encode($instrumen->soalPengetahuan->pluck('id')->all()) }}) / {{ max(1, $instrumen->soalPengetahuan->count()) }} * 100) + '%'">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- ── KETERAMPILAN ── --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-emerald-500 rounded-xl flex items-center justify-center text-white text-sm">⚙️</div>
                                <div>
                                    <p class="font-black text-gray-900 text-sm">Penilaian Keterampilan</p>
                                    <p class="text-[11px] text-gray-400">0 Belum · 1 Cukup · 2 Baik · 3 Sangat Baik</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-emerald-500 font-semibold">Bobot</p>
                                <p class="font-black text-emerald-700">{{ 100 - $instrumen->bobot_pengetahuan }}%</p>
                            </div>
                        </div>

                        @if($instrumen->kategoriKeterampilan->isEmpty())
                            <div class="p-6 text-center text-gray-400 text-sm italic">Belum ada kategori keterampilan.</div>
                        @else
                        <div class="p-4">
                            @foreach($instrumen->kategoriKeterampilan as $kategori)
                            <div class="kategori-block">
                                <div class="kategori-head">
                                    <div class="w-6 h-6 bg-violet-600 text-white rounded-md flex items-center justify-center text-[11px] font-black shrink-0">
                                        {{ $loop->iteration }}
                                    </div>
                                    <p class="font-black text-gray-800 text-sm flex-1">{{ $kategori->nama_kategori }}</p>
                                    <span class="text-xs font-bold text-violet-600 shrink-0">{{ $kategori->bobot }}%</span>
                                </div>
                                <div class="p-3 flex flex-col gap-1.5 bg-white">
                                    @foreach($kategori->indikator as $ind)
                                    <div class="indikator-row">
                                        <div class="w-5 h-5 rounded bg-emerald-500 text-white flex items-center justify-center text-[10px] font-black shrink-0">
                                            {{ $loop->iteration }}
                                        </div>
                                        <p class="flex-1 text-sm text-gray-700 min-w-0">{{ $ind->nama_indikator }}</p>
                                        {{-- 0/1/2/3 selector --}}
                                        <div class="flex gap-1 shrink-0">
                                            @foreach([0 => ['label'=>'Belum','color'=>'gray'], 1 => ['label'=>'Cukup','color'=>'amber'], 2 => ['label'=>'Baik','color'=>'blue'], 3 => ['label'=>'S.Baik','color'=>'emerald']] as $val => $cfg)
                                            <button type="button"
                                                @click="setKeterampilan({{ $ind->id }}, {{ $val }})"
                                                :class="nilaiK[{{ $ind->id }}] === {{ $val }}
                                                    ? 'bg-{{ $cfg['color'] }}-500 border-{{ $cfg['color'] }}-500 text-white'
                                                    : 'bg-{{ $cfg['color'] }}-50 border-{{ $cfg['color'] }}-200 text-{{ $cfg['color'] }}-600 hover:bg-{{ $cfg['color'] }}-100'"
                                                class="score-btn" style="width:2.4rem;height:2.4rem;font-size:.65rem">
                                                <span class="font-black">{{ $val }}</span>
                                                <span class="text-[8px]">{{ $cfg['label'] }}</span>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                </div>{{-- /grid --}}
            </div>{{-- /instrumen block --}}
            @endforeach

            {{-- ══ BOTTOM SUMMARY BAR ══ --}}
            <div class="sticky bottom-0 z-30 bg-white border-t border-gray-200 shadow-lg px-4 py-3 max-w-screen-xl mx-auto rounded-t-2xl">
                <div class="flex flex-wrap items-center gap-4 justify-between text-sm">
                    <div class="flex gap-4 flex-wrap">
                        <span class="flex items-center gap-2 text-blue-700 font-semibold">
                            <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                            Pengetahuan: <strong x-text="skorPengetahuan.toFixed(1)"></strong>
                        </span>
                        <span class="flex items-center gap-2 text-emerald-700 font-semibold">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                            Keterampilan: <strong x-text="skorKeterampilan.toFixed(1)"></strong>
                        </span>
                        <span class="flex items-center gap-2 text-violet-700 font-black text-base">
                            Nilai Akhir: <strong x-text="nilaiAkhir.toFixed(1)"></strong>
                        </span>
                    </div>
                    <button @click="simpan()" :disabled="saving"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-black rounded-xl shadow-md hover:from-orange-400 hover:to-amber-400 transition-all disabled:opacity-50 text-sm">
                        <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Penilaian'"></span>
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
    const INIT_K     = @json($nilaiK);
    const SAVE_URL   = '{{ route("guru-kelas.penilaian-ukk.simpan", [$ujian->id, $siswa->id]) }}';

    return {
        saving: false,
        toasts: [], toastSeq: 0,
        nilaiP: { ...INIT_P },
        nilaiK: { ...INIT_K },

        init() {},

        // ── Scoring helpers ──
        togglePengetahuan(soalId, val) {
            // Toggle off if same value clicked twice
            if (this.nilaiP[soalId] === val) {
                delete this.nilaiP[soalId];
            } else {
                this.nilaiP[soalId] = val;
            }
            this.nilaiP = { ...this.nilaiP };
        },

        setKeterampilan(indId, val) {
            this.nilaiK[indId] = val;
            this.nilaiK = { ...this.nilaiK };
        },

        benarCount(soalIds) {
            return soalIds.filter(id => this.nilaiP[id] === 1).length;
        },

        // ── Score calculation across all instrumens ──
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

        // ── Save ──
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
                        keterampilan: this.nilaiK,
                    }),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.toast(data.message || 'Terjadi kesalahan.', 'error');
                    return;
                }
                this.toast(data.message, 'success');
            } catch {
                this.toast('Koneksi gagal.', 'error');
            } finally {
                this.saving = false;
            }
        },

        // ── Toast ──
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
