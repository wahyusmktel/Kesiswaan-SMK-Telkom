<x-app-layout px="0">

<style>
    .builder-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 1.25rem;
        box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .soal-row, .indikator-row {
        display: flex;
        align-items: flex-start;
        gap: .625rem;
        padding: .625rem .75rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        transition: box-shadow .15s;
    }
    .soal-row:hover, .indikator-row:hover { box-shadow: 0 2px 8px rgba(99,102,241,.12); }
    .row-num {
        width: 1.5rem; height: 1.5rem; flex-shrink: 0;
        background: #6366f1; color: #fff;
        border-radius: .375rem;
        font-size: .65rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        margin-top: .25rem;
    }
    .kategori-card {
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        transition: border-color .2s;
    }
    .kategori-card:focus-within { border-color: #a78bfa; }
    .kategori-header {
        display: flex; align-items: center; gap: .75rem;
        padding: .875rem 1rem;
        background: linear-gradient(to right, #f5f3ff, #ede9fe);
        border-bottom: 1px solid #e5e7eb;
        border-radius: .875rem .875rem 0 0;
    }
    .indikator-add-btn {
        border-radius: 0 0 .875rem .875rem;
    }
    .bobot-pill {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .625rem;
        border-radius: 9999px;
        font-size: .7rem; font-weight: 800;
    }
    .bobot-bar-track {
        height: 10px; border-radius: 999px;
        background: #e5e7eb; overflow: hidden;
        margin-top: .375rem;
    }
    .btn-add {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .5rem 1rem;
        border: 2px dashed #c4b5fd;
        border-radius: .75rem;
        font-size: .8rem; font-weight: 700;
        color: #7c3aed;
        background: transparent;
        cursor: pointer; transition: all .15s;
        width: 100%;
        justify-content: center;
    }
    .btn-add:hover { background: #f5f3ff; border-color: #7c3aed; }
    .del-btn {
        flex-shrink: 0; width: 1.75rem; height: 1.75rem;
        border-radius: .5rem; background: #fee2e2;
        color: #ef4444; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 900; transition: all .15s;
        margin-top: .2rem;
    }
    .del-btn:hover { background: #ef4444; color: #fff; }
    .score-chip {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: .5rem;
        font-size: .7rem; font-weight: 800; flex-shrink: 0;
    }
    input[type="range"] { accent-color: #7c3aed; }

    /* Import modal */
    .import-modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.55);
        backdrop-filter: blur(3px);
        z-index: 9800;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
    }
    .import-modal-box {
        background: #fff;
        border-radius: 1.25rem;
        box-shadow: 0 25px 60px rgba(0,0,0,.25);
        width: 100%;
        max-width: 540px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    .import-modal-box.maximized {
        position: fixed !important;
        inset: 0 !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        border-radius: 0 !important;
        width: 100vw !important;
    }
    .import-modal-box.positioned {
        position: fixed;
    }
    .import-modal-header {
        display: flex; align-items: center; gap: .625rem;
        padding: .875rem 1rem .875rem 1.25rem;
        flex-shrink: 0;
        border-bottom: 1px solid #e5e7eb;
        user-select: none;
    }
    .import-modal-body {
        padding: 1.25rem;
        overflow-y: auto;
        flex: 1;
    }
    .import-modal-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid #e5e7eb;
        display: flex; align-items: center; gap: .75rem;
        flex-shrink: 0;
    }
    .panduan-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: .75rem;
        padding: .875rem 1rem;
        margin-bottom: 1rem;
    }
    .panduan-box p { font-size: .8rem; color: #0c4a6e; }
    .panduan-box ul { margin: .4rem 0 0 1rem; font-size: .78rem; color: #0369a1; }
    .panduan-box ul li { margin-bottom: .2rem; list-style: disc; }
    .file-drop-zone {
        border: 2px dashed #c7d2fe;
        border-radius: .875rem;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #f5f7ff;
    }
    .file-drop-zone:hover, .file-drop-zone.has-file { border-color: #6366f1; background: #eef2ff; }
    .file-drop-zone input[type=file] { display: none; }
    .btn-template {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .5rem .875rem;
        background: #ecfdf5; border: 1px solid #6ee7b7;
        border-radius: .625rem; color: #065f46;
        font-size: .78rem; font-weight: 700;
        cursor: pointer; transition: all .15s; text-decoration: none;
    }
    .btn-template:hover { background: #d1fae5; border-color: #059669; }
    .btn-import-soal {
        display: inline-flex; align-items: center; gap: .375rem;
        padding: .375rem .75rem;
        background: #eff6ff; border: 1px solid #93c5fd;
        border-radius: .625rem; color: #1d4ed8;
        font-size: .7rem; font-weight: 700; cursor: pointer;
        transition: all .15s; flex-shrink: 0;
    }
    .btn-import-soal:hover { background: #dbeafe; border-color: #3b82f6; }
    .btn-import-ind {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .3rem .625rem;
        background: #f0fdf4; border: 1px solid #86efac;
        border-radius: .5rem; color: #166534;
        font-size: .65rem; font-weight: 700; cursor: pointer;
        transition: all .15s; flex-shrink: 0;
    }
    .btn-import-ind:hover { background: #dcfce7; border-color: #22c55e; }
</style>

<div x-data="instrumenBuilder()"
     x-init="init()"
     @mousemove.window="onImportDrag($event)"
     @mouseup.window="stopImportDrag()"
     class="min-h-screen bg-slate-50">

    {{-- ═══ STICKY HEADER ═══ --}}
    <div class="sticky top-0 z-40 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center gap-3 px-4 py-3 max-w-screen-2xl mx-auto">
            <a href="{{ route('kaprodi.ukk.instrumen.index') }}"
                class="flex-shrink-0 w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>

            <input x-model="form.nama_instrumen" type="text"
                placeholder="Nama Instrumen Penilaian…"
                class="flex-1 text-lg font-black text-gray-900 border-0 border-b-2 border-transparent focus:border-violet-500 focus:outline-none bg-transparent py-1 transition-colors placeholder-gray-300">

            <select x-model="form.ukk_ujian_id"
                class="hidden sm:block text-sm font-bold border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700 max-w-[220px]">
                <option value="">— Pilih Ujian UKK —</option>
                @foreach($ujians as $u)
                    <option value="{{ $u->id }}">{{ $u->nama_ujian }}</option>
                @endforeach
            </select>

            <button @click="save()" :disabled="saving"
                class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-black rounded-xl shadow-md hover:from-violet-500 hover:to-indigo-500 transition-all disabled:opacity-50 text-sm">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="saving ? 'Menyimpan…' : '💾 Simpan'"></span>
            </button>
        </div>

        <div class="sm:hidden px-4 pb-2">
            <select x-model="form.ukk_ujian_id"
                class="w-full text-sm font-bold border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 text-gray-700">
                <option value="">— Pilih Ujian UKK —</option>
                @foreach($ujians as $u)
                    <option value="{{ $u->id }}">{{ $u->nama_ujian }}</option>
                @endforeach
            </select>
        </div>

        <div class="px-4 pb-3 max-w-screen-2xl mx-auto">
            <div class="flex items-center gap-3">
                <span class="text-xs font-black text-gray-500 w-20 shrink-0">Bobot Akhir</span>
                <div class="flex-1 flex rounded-full overflow-hidden h-8 border border-gray-200">
                    <div class="flex items-center justify-center text-[11px] font-black text-white bg-blue-500 transition-all duration-300"
                        :style="'width:' + form.bobot_pengetahuan + '%'">
                        <span x-show="form.bobot_pengetahuan >= 12">Pengetahuan <span x-text="form.bobot_pengetahuan"></span>%</span>
                    </div>
                    <div class="flex items-center justify-center text-[11px] font-black text-white bg-emerald-500 flex-1 transition-all duration-300">
                        <span x-show="bobot_keterampilan >= 12">Keterampilan <span x-text="bobot_keterampilan"></span>%</span>
                    </div>
                </div>
                <span class="text-xs font-black shrink-0"
                    :class="form.bobot_pengetahuan >= 0 && form.bobot_pengetahuan <= 100 ? 'text-emerald-600' : 'text-red-600'"
                    >= 100%</span>
            </div>
        </div>
    </div>

    {{-- TOAST --}}
    <div class="fixed top-20 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:280px">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-semibold"
                :class="{ 'bg-emerald-50 border-emerald-200 text-emerald-800':toast.type==='success', 'bg-red-50 border-red-200 text-red-800':toast.type==='error', 'bg-amber-50 border-amber-200 text-amber-800':toast.type==='warning' }"
                :style="'transition:opacity .35s,transform .35s;opacity:'+(toast.visible?1:0)+';transform:translateY('+(toast.visible?'0':'-8px')+')'">
                <span x-text="{success:'✅',error:'❌',warning:'⚠️'}[toast.type]"></span>
                <span class="flex-1" x-text="toast.message"></span>
                <button @click="removeToast(toast.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
            </div>
        </template>
    </div>

    {{-- ═══ MAIN BUILDER ═══ --}}
    <div class="p-4 sm:p-6 max-w-screen-2xl mx-auto grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">

        {{-- ══════════════════════════════════════
             LEFT: PENILAIAN PENGETAHUAN
        ══════════════════════════════════════ --}}
        <div class="builder-section flex flex-col">

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 flex items-center justify-center text-white text-lg">📝</div>
                    <div>
                        <p class="font-black text-gray-900">Penilaian Pengetahuan</p>
                        <p class="text-xs text-gray-400">Tanya jawab / tes tertulis &middot; Penilaian Benar / Salah</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-xs font-bold text-gray-500">Bobot</span>
                    <div class="flex items-center gap-1">
                        <input x-model.number="form.bobot_pengetahuan" type="number" min="0" max="100"
                            class="w-14 text-center text-sm font-black border border-blue-300 rounded-lg px-1 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white"
                            @input="clampBobot()">
                        <span class="text-sm font-black text-blue-600">%</span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 border-b border-gray-100 bg-blue-50/40">
                <input type="range" min="0" max="100" x-model.number="form.bobot_pengetahuan" class="w-full">
                <div class="flex justify-between text-[10px] text-gray-400 font-semibold mt-0.5">
                    <span>0%</span><span>50%</span><span>100%</span>
                </div>
            </div>

            <div class="flex gap-2 px-5 py-2.5 border-b border-gray-100 bg-gray-50/60 flex-wrap items-center">
                <span class="bobot-pill bg-emerald-100 text-emerald-700">✓ Benar — 1 poin</span>
                <span class="bobot-pill bg-red-100 text-red-700">✗ Salah — 0 poin</span>
                <span class="text-xs text-gray-400 ml-auto self-center"><span class="font-bold text-blue-600" x-text="form.soal_pengetahuan.length"></span> soal</span>
                {{-- Import button --}}
                <button @click="openImportModal('soal')" class="btn-import-soal">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </button>
            </div>

            <div class="p-4 flex-1 flex flex-col gap-2 overflow-y-auto" style="min-height:240px; max-height:520px">
                <template x-for="(soal, i) in form.soal_pengetahuan" :key="soal._key">
                    <div class="soal-row">
                        <div class="row-num" x-text="i+1"></div>
                        <textarea x-model="soal.pertanyaan"
                            rows="2"
                            :placeholder="'Pertanyaan ' + (i+1) + '…'"
                            class="flex-1 text-sm text-gray-800 bg-transparent border-0 focus:outline-none resize-none leading-relaxed"></textarea>
                        <button @click="removeSoal(i)" class="del-btn" title="Hapus soal">&times;</button>
                    </div>
                </template>

                <div x-show="form.soal_pengetahuan.length === 0" class="flex-1 flex items-center justify-center py-8 text-gray-400 text-sm">
                    Belum ada soal. Tambahkan manual atau import dari Excel.
                </div>
            </div>

            <div class="p-4 border-t border-gray-100">
                <button @click="addSoal()" class="btn-add">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Soal Pengetahuan
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             RIGHT: PENILAIAN KETERAMPILAN
        ══════════════════════════════════════ --}}
        <div class="builder-section flex flex-col">

            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center text-white text-lg">⚙️</div>
                    <div>
                        <p class="font-black text-gray-900">Penilaian Keterampilan</p>
                        <p class="text-xs text-gray-400">Nilai 0–3 per indikator &middot; Bobot otomatis</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0 px-3 py-1.5 bg-emerald-100 rounded-xl">
                    <span class="text-sm font-black text-emerald-700" x-text="bobot_keterampilan + '%'"></span>
                </div>
            </div>

            <div class="flex gap-1.5 px-4 py-2.5 border-b border-gray-100 bg-gray-50/60 flex-wrap">
                <span class="score-chip bg-gray-100 text-gray-500 text-[10px]">0 Belum</span>
                <span class="score-chip bg-amber-100 text-amber-700 text-[10px]">1 Cukup</span>
                <span class="score-chip bg-blue-100 text-blue-700 text-[10px]">2 Baik</span>
                <span class="score-chip bg-emerald-100 text-emerald-700 text-[10px]">3 Sangat Baik</span>
                <span class="ml-auto text-[10px] text-gray-400 self-center">Total bobot kategori:
                    <span class="font-black" :class="total_bobot_kategori===100 ? 'text-emerald-600' : 'text-red-500'"
                        x-text="total_bobot_kategori + '%'"></span>
                    <span x-show="total_bobot_kategori !== 100" class="text-red-500">(harus 100%)</span>
                    <span x-show="total_bobot_kategori === 100" class="text-emerald-600">✓</span>
                </span>
            </div>

            <div class="p-4 flex-1 flex flex-col gap-3 overflow-y-auto" style="max-height:600px">
                <template x-for="(kat, ki) in form.kategori_keterampilan" :key="kat._key">
                    <div class="kategori-card">

                        <div class="kategori-header">
                            <div class="w-7 h-7 rounded-lg bg-violet-600 text-white flex items-center justify-center text-xs font-black shrink-0"
                                x-text="ki+1"></div>
                            <input x-model="kat.nama_kategori" type="text"
                                placeholder="Nama Kategori…"
                                class="flex-1 font-black text-sm text-gray-800 bg-transparent border-0 border-b border-violet-300/50 focus:border-violet-500 focus:outline-none py-0.5 placeholder-violet-300 min-w-0">

                            <div class="flex items-center gap-1 shrink-0">
                                <span class="text-xs text-violet-600 font-bold">Bobot</span>
                                <input x-model.number="kat.bobot" type="number" min="0" max="100"
                                    class="w-12 text-center text-xs font-black border border-violet-300 rounded-lg px-1 py-1 focus:outline-none focus:ring-1 focus:ring-violet-400 bg-white">
                                <span class="text-xs font-black text-violet-600">%</span>
                            </div>

                            {{-- Import indikator button per kategori --}}
                            <button @click="openImportModal('indikator', ki, kat.nama_kategori)" class="btn-import-ind" title="Import indikator dari Excel">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import
                            </button>

                            <button @click="removeKategori(ki)" class="del-btn" title="Hapus kategori">&times;</button>
                        </div>

                        <div class="px-4 pt-2 pb-1 bg-white">
                            <div class="bobot-bar-track">
                                <div class="h-full rounded-full bg-violet-500 transition-all duration-300"
                                    :style="'width:' + Math.min(kat.bobot, 100) + '%'"></div>
                            </div>
                        </div>

                        <div class="px-3 pt-2 pb-1 bg-white flex flex-col gap-1.5 overflow-y-auto" style="max-height:200px">
                            <template x-for="(ind, ii) in kat.indikator" :key="ind._key">
                                <div class="indikator-row">
                                    <div class="row-num" style="background:#10b981;font-size:.6rem" x-text="ii+1"></div>
                                    <input x-model="ind.nama_indikator" type="text"
                                        :placeholder="'Indikator ' + (ii+1) + '…'"
                                        class="flex-1 text-sm text-gray-700 bg-transparent border-0 focus:outline-none">
                                    <div class="flex gap-0.5 shrink-0">
                                        <span class="score-chip bg-gray-100 text-gray-500" style="width:1.4rem;height:1.4rem;font-size:.6rem">0</span>
                                        <span class="score-chip bg-amber-100 text-amber-600" style="width:1.4rem;height:1.4rem;font-size:.6rem">1</span>
                                        <span class="score-chip bg-blue-100 text-blue-600" style="width:1.4rem;height:1.4rem;font-size:.6rem">2</span>
                                        <span class="score-chip bg-emerald-100 text-emerald-600" style="width:1.4rem;height:1.4rem;font-size:.6rem">3</span>
                                    </div>
                                    <button @click="removeIndikator(ki, ii)" class="del-btn" title="Hapus indikator">&times;</button>
                                </div>
                            </template>

                            <div x-show="kat.indikator.length === 0" class="text-xs text-gray-400 text-center py-2 italic">
                                Belum ada indikator. Tambahkan manual atau import dari Excel.
                            </div>
                        </div>

                        <div class="indikator-add-btn px-3 pb-3 pt-2 bg-white border-t border-gray-100">
                            <button @click="addIndikator(ki)" class="btn-add" style="border-color:#a7f3d0;color:#059669">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Indikator
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="form.kategori_keterampilan.length === 0" class="text-sm text-gray-400 text-center py-8 italic">
                    Belum ada kategori keterampilan. Tambahkan di bawah.
                </div>
            </div>

            <div x-show="form.kategori_keterampilan.length > 0 && total_bobot_kategori !== 100"
                class="mx-4 mb-2 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-semibold flex items-center gap-2">
                <span class="text-base">⚠️</span>
                Total bobot kategori keterampilan harus tepat 100%.
                Saat ini: <span class="font-black" x-text="total_bobot_kategori + '%'"></span>
                (selisih: <span x-text="(100 - total_bobot_kategori) + '%'"></span>)
            </div>

            <div class="p-4 border-t border-gray-100">
                <button @click="addKategori()" class="btn-add" style="border-color:#6ee7b7;color:#059669">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kategori Keterampilan
                </button>
            </div>
        </div>
    </div>

    {{-- Bottom summary bar --}}
    <div class="sticky bottom-0 z-30 bg-white border-t border-gray-200 shadow-lg px-4 py-3 max-w-screen-2xl mx-auto">
        <div class="flex flex-wrap items-center gap-4 justify-between text-sm">
            <div class="flex gap-4 flex-wrap">
                <span class="flex items-center gap-1.5 text-blue-700 font-semibold">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    Pengetahuan: <strong x-text="form.soal_pengetahuan.length + ' soal'"></strong>
                    · Bobot <strong x-text="form.bobot_pengetahuan + '%'"></strong>
                </span>
                <span class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                    Keterampilan: <strong x-text="form.kategori_keterampilan.length + ' kategori'"></strong>
                    · Bobot <strong x-text="bobot_keterampilan + '%'"></strong>
                </span>
            </div>
            <button @click="save()" :disabled="saving"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-black rounded-xl shadow-md hover:from-violet-500 hover:to-indigo-500 transition-all disabled:opacity-50 text-sm">
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="saving ? 'Menyimpan…' : '💾 Simpan Instrumen'"></span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         IMPORT MODAL (shared for soal & indikator)
    ═══════════════════════════════════════════════════ --}}
    <template x-if="importModal.open">
        <div class="import-modal-backdrop" style="z-index:9800">

            <div x-ref="importModalBox"
                 class="import-modal-box"
                 :class="{
                     'maximized': importModal.maximized,
                     'positioned': !importModal.maximized && importModal.pos.x !== null
                 }"
                 :style="!importModal.maximized && importModal.pos.x !== null
                     ? 'left:' + importModal.pos.x + 'px; top:' + importModal.pos.y + 'px; width:540px;'
                     : ''">

                {{-- ── Header (drag handle) ── --}}
                <div class="import-modal-header"
                     :class="!importModal.maximized ? 'cursor-grab active:cursor-grabbing' : 'cursor-default'"
                     @mousedown="startImportDrag($event)"
                     :style="importModal.type === 'soal'
                         ? 'background:linear-gradient(to right,#eff6ff,#dbeafe);border-bottom:1px solid #bfdbfe'
                         : 'background:linear-gradient(to right,#f0fdf4,#dcfce7);border-bottom:1px solid #86efac'">

                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-base flex-shrink-0"
                         :class="importModal.type === 'soal' ? 'bg-blue-500' : 'bg-emerald-500'">
                        📥
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-gray-900 text-sm truncate"
                           x-text="importModal.type === 'soal' ? 'Import Soal Pengetahuan' : 'Import Indikator — ' + (importModal.katName || 'Kategori')"></p>
                        <p class="text-[11px] text-gray-500"
                           x-text="importModal.type === 'soal' ? 'Upload file Excel berisi daftar soal' : 'Upload file Excel berisi daftar indikator keterampilan'"></p>
                    </div>

                    {{-- Maximize button --}}
                    <button @click="toggleImportMaximize()"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-white/60 hover:text-gray-700 transition-colors flex-shrink-0"
                        :title="importModal.maximized ? 'Kembalikan ukuran' : 'Perbesar'"
                        @mousedown.stop>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <template x-if="!importModal.maximized">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </template>
                            <template x-if="importModal.maximized">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                            </template>
                        </svg>
                    </button>

                    {{-- Close button (only way to close) --}}
                    <button @click="closeImportModal()"
                        class="w-7 h-7 rounded-lg flex items-center justify-center bg-red-100 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex-shrink-0 font-black text-base"
                        title="Tutup modal"
                        @mousedown.stop>
                        &times;
                    </button>
                </div>

                {{-- ── Body ── --}}
                <div class="import-modal-body">

                    {{-- Panduan --}}
                    <div class="panduan-box">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base">ℹ️</span>
                            <p class="font-black text-[#0c4a6e] text-xs uppercase tracking-wide">Panduan Import Excel</p>
                        </div>
                        <ul>
                            <li>Gunakan template Excel yang tersedia agar format sesuai.</li>
                            <li x-show="importModal.type === 'soal'">Isi kolom <strong>B</strong> (Pertanyaan) mulai dari baris ke-2 (baris pertama adalah header).</li>
                            <li x-show="importModal.type === 'indikator'">Isi kolom <strong>B</strong> (Nama Indikator) mulai dari baris ke-2 (baris pertama adalah header).</li>
                            <li>Kolom <strong>A</strong> (No) bersifat opsional, bisa dikosongkan.</li>
                            <li>Setiap baris yang tidak kosong akan diimpor sebagai satu item baru.</li>
                            <li>Maksimal <strong>100 baris</strong> per import. Format: <strong>.xlsx, .xls, .csv</strong> (maks. 5 MB).</li>
                            <li>Data yang diimport akan <strong>ditambahkan</strong> ke data yang sudah ada (tidak menggantikan).</li>
                        </ul>
                    </div>

                    {{-- Download template --}}
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-xs text-gray-500 font-semibold">Unduh template:</span>
                        <template x-if="importModal.type === 'soal'">
                            <a href="{{ route('kaprodi.ukk.instrumen.template-soal') }}"
                                target="_blank" class="btn-template" @mousedown.stop>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Template Soal Pengetahuan.xlsx
                            </a>
                        </template>
                        <template x-if="importModal.type === 'indikator'">
                            <a :href="'{{ route('kaprodi.ukk.instrumen.template-indikator') }}' + '?kategori=' + encodeURIComponent(importModal.katName)"
                                target="_blank" class="btn-template" @mousedown.stop>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Template Indikator Keterampilan.xlsx
                            </a>
                        </template>
                    </div>

                    {{-- File upload zone --}}
                    <div class="file-drop-zone"
                         :class="importModal.fileName ? 'has-file' : ''"
                         @click="$refs.fileInput.click()">
                        <input type="file" x-ref="fileInput"
                            accept=".xlsx,.xls,.csv"
                            @change="handleFileSelect($event)">

                        <template x-if="!importModal.fileName">
                            <div>
                                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-600">Klik untuk memilih file</p>
                                <p class="text-xs text-gray-400 mt-0.5">atau drag & drop di sini</p>
                                <p class="text-[10px] text-gray-300 mt-1">.xlsx, .xls, .csv · maks. 5 MB</p>
                            </div>
                        </template>

                        <template x-if="importModal.fileName">
                            <div class="flex items-center justify-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-black text-gray-800" x-text="importModal.fileName"></p>
                                    <p class="text-xs text-emerald-600 font-semibold">File siap diimport · klik untuk ganti</p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Error --}}
                    <div x-show="importModal.fileError" class="mt-3 flex items-start gap-2 p-3 rounded-xl bg-red-50 border border-red-200">
                        <span class="text-red-500 text-base flex-shrink-0">❌</span>
                        <p class="text-xs text-red-700 font-semibold" x-text="importModal.fileError"></p>
                    </div>

                </div>

                {{-- ── Footer ── --}}
                <div class="import-modal-footer">
                    <button @click="closeImportModal()"
                        class="flex-1 py-2.5 border-2 border-gray-200 text-gray-600 font-bold rounded-xl text-sm hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button @click="doImport()"
                        :disabled="importModal.uploading || !importModal.fileName"
                        :class="importModal.fileName && !importModal.uploading
                            ? (importModal.type === 'soal' ? 'bg-blue-600 hover:bg-blue-500' : 'bg-emerald-600 hover:bg-emerald-500')
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        class="flex-1 py-2.5 text-white font-black rounded-xl text-sm transition-all inline-flex items-center justify-center gap-2">
                        <svg x-show="importModal.uploading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="importModal.uploading ? 'Memproses…' : '📥 Import Sekarang'"></span>
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
function instrumenBuilder() {
    const INITIAL      = @json($initialData);
    const IS_EDIT      = @json($instrumen ? true : false);
    const INSTRUMEN_ID = @json($instrumen?->id);

    const IMPORT_SOAL_URL      = '{{ route("kaprodi.ukk.instrumen.import-soal") }}';
    const IMPORT_INDIKATOR_URL = '{{ route("kaprodi.ukk.instrumen.import-indikator") }}';

    let keySeq = 0;
    const mk = () => ++keySeq;

    function mapSoal(arr) {
        return (arr || []).map(s => ({ ...s, _key: mk() }));
    }
    function mapKat(arr) {
        return (arr || []).map(k => ({
            ...k,
            _key: mk(),
            indikator: (k.indikator || []).map(i => ({ ...i, _key: mk() })),
        }));
    }

    return {
        saving: false,
        toasts: [], toastSeq: 0,

        form: {
            nama_instrumen:        INITIAL.nama_instrumen || '',
            ukk_ujian_id:          INITIAL.ukk_ujian_id || '',
            bobot_pengetahuan:     INITIAL.bobot_pengetahuan ?? 30,
            soal_pengetahuan:      mapSoal(INITIAL.soal_pengetahuan),
            kategori_keterampilan: mapKat(INITIAL.kategori_keterampilan),
        },

        // ── Import modal state ──
        importModal: {
            open:       false,
            type:       'soal',   // 'soal' | 'indikator'
            katIdx:     null,
            katName:    '',
            maximized:  false,
            dragging:   false,
            pos:        { x: null, y: null },
            dragOffset: { x: 0, y: 0 },
            uploading:  false,
            fileName:   null,
            fileError:  null,
        },

        get bobot_keterampilan() {
            return 100 - (parseInt(this.form.bobot_pengetahuan) || 0);
        },

        get total_bobot_kategori() {
            return this.form.kategori_keterampilan.reduce((s, k) => s + (parseInt(k.bobot) || 0), 0);
        },

        init() {},

        clampBobot() {
            let v = parseInt(this.form.bobot_pengetahuan) || 0;
            if (v < 0)   v = 0;
            if (v > 100) v = 100;
            this.form.bobot_pengetahuan = v;
        },

        // ── Soal Pengetahuan ──
        addSoal() {
            this.form.soal_pengetahuan.push({ _key: mk(), id: null, pertanyaan: '' });
            this.$nextTick(() => {
                const inputs = this.$el.querySelectorAll('.soal-row textarea');
                inputs[inputs.length - 1]?.focus();
            });
        },
        removeSoal(i) { this.form.soal_pengetahuan.splice(i, 1); },

        // ── Kategori Keterampilan ──
        addKategori() {
            this.form.kategori_keterampilan.push({
                _key: mk(), id: null,
                nama_kategori: '',
                bobot: 0,
                indikator: [],
            });
        },
        removeKategori(i) {
            if (!confirm('Hapus kategori ini beserta semua indikatornya?')) return;
            this.form.kategori_keterampilan.splice(i, 1);
        },

        // ── Indikator ──
        addIndikator(ki) {
            this.form.kategori_keterampilan[ki].indikator.push({ _key: mk(), id: null, nama_indikator: '' });
        },
        removeIndikator(ki, ii) {
            this.form.kategori_keterampilan[ki].indikator.splice(ii, 1);
        },

        // ── Import Modal ──
        openImportModal(type, katIdx = null, katName = '') {
            this.importModal.open      = true;
            this.importModal.type      = type;
            this.importModal.katIdx    = katIdx;
            this.importModal.katName   = katName || '';
            this.importModal.maximized = false;
            this.importModal.uploading = false;
            this.importModal.fileName  = null;
            this.importModal.fileError = null;
            this.importModal.pos       = { x: null, y: null };
            this.$nextTick(() => {
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
            });
        },

        closeImportModal() {
            this.importModal.open = false;
        },

        toggleImportMaximize() {
            this.importModal.maximized = !this.importModal.maximized;
            if (this.importModal.maximized) {
                this.importModal.pos = { x: null, y: null };
            }
        },

        startImportDrag(e) {
            if (this.importModal.maximized) return;
            this.importModal.dragging = true;
            const box = this.$refs.importModalBox;
            const rect = box.getBoundingClientRect();
            this.importModal.pos       = { x: rect.left, y: rect.top };
            this.importModal.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
        },

        onImportDrag(e) {
            if (!this.importModal.dragging) return;
            const maxX = window.innerWidth  - 100;
            const maxY = window.innerHeight - 60;
            this.importModal.pos.x = Math.max(0, Math.min(e.clientX - this.importModal.dragOffset.x, maxX));
            this.importModal.pos.y = Math.max(0, Math.min(e.clientY - this.importModal.dragOffset.y, maxY));
        },

        stopImportDrag() {
            this.importModal.dragging = false;
        },

        handleFileSelect(e) {
            const file = e.target.files?.[0];
            this.importModal.fileError = null;
            this.importModal.fileName  = file ? file.name : null;
        },

        async doImport() {
            if (!this.$refs.fileInput?.files?.[0]) {
                this.importModal.fileError = 'Pilih file Excel terlebih dahulu.';
                return;
            }

            const file = this.$refs.fileInput.files[0];
            if (file.size > 5 * 1024 * 1024) {
                this.importModal.fileError = 'Ukuran file terlalu besar (maks. 5 MB).';
                return;
            }

            this.importModal.uploading  = true;
            this.importModal.fileError  = null;

            const formData = new FormData();
            formData.append('file', file);

            const url = this.importModal.type === 'soal' ? IMPORT_SOAL_URL : IMPORT_INDIKATOR_URL;

            try {
                const res  = await fetch(url, {
                    method:  'POST',
                    headers: {
                        'Accept':           'application/json',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                const data = await res.json();

                if (!res.ok) {
                    this.importModal.fileError = data.message || 'Terjadi kesalahan saat memproses file.';
                    return;
                }

                const items  = data.items || [];
                const count  = items.length;
                const type   = this.importModal.type;
                const katIdx = this.importModal.katIdx;

                if (type === 'soal') {
                    const newSoal = items.map(p => ({ _key: mk(), id: null, pertanyaan: p }));
                    this.form.soal_pengetahuan.push(...newSoal);
                } else {
                    const newInd = items.map(n => ({ _key: mk(), id: null, nama_indikator: n }));
                    this.form.kategori_keterampilan[katIdx].indikator.push(...newInd);
                }

                this.closeImportModal();
                this.toast(
                    'Berhasil mengimport ' + count + ' ' + (type === 'soal' ? 'soal pengetahuan' : 'indikator keterampilan') + '.',
                    'success'
                );

            } catch {
                this.importModal.fileError = 'Koneksi gagal. Silakan coba lagi.';
            } finally {
                this.importModal.uploading = false;
            }
        },

        // ── Validate & Save ──
        validate() {
            if (!this.form.nama_instrumen.trim()) {
                this.toast('Nama instrumen harus diisi.', 'warning'); return false;
            }
            if (!this.form.ukk_ujian_id) {
                this.toast('Pilih Ujian UKK terlebih dahulu.', 'warning'); return false;
            }
            const bp = parseInt(this.form.bobot_pengetahuan) || 0;
            if (bp < 0 || bp > 100) {
                this.toast('Bobot pengetahuan harus antara 0–100%.', 'warning'); return false;
            }
            if (this.form.kategori_keterampilan.length > 0 && this.total_bobot_kategori !== 100) {
                this.toast('Total bobot kategori keterampilan harus 100%. Saat ini: ' + this.total_bobot_kategori + '%.', 'warning');
                return false;
            }
            const emptySoal = this.form.soal_pengetahuan.some(s => !s.pertanyaan.trim());
            if (emptySoal) {
                this.toast('Ada soal pengetahuan yang masih kosong.', 'warning'); return false;
            }
            for (const kat of this.form.kategori_keterampilan) {
                if (!kat.nama_kategori.trim()) {
                    this.toast('Ada nama kategori keterampilan yang masih kosong.', 'warning'); return false;
                }
                const emptyInd = kat.indikator.some(i => !i.nama_indikator.trim());
                if (emptyInd) {
                    this.toast('Ada indikator di kategori "' + kat.nama_kategori + '" yang masih kosong.', 'warning');
                    return false;
                }
            }
            return true;
        },

        async save() {
            if (!this.validate()) return;
            this.saving = true;

            const url    = IS_EDIT ? '/kaprodi/ukk/instrumen/' + INSTRUMEN_ID : '{{ route("kaprodi.ukk.instrumen.store") }}';
            const method = IS_EDIT ? 'PUT' : 'POST';

            const payload = {
                nama_instrumen:        this.form.nama_instrumen,
                ukk_ujian_id:          this.form.ukk_ujian_id,
                bobot_pengetahuan:     this.form.bobot_pengetahuan,
                soal_pengetahuan:      this.form.soal_pengetahuan.map(s => ({ pertanyaan: s.pertanyaan })),
                kategori_keterampilan: this.form.kategori_keterampilan.map(k => ({
                    nama_kategori: k.nama_kategori,
                    bobot:         k.bobot,
                    indikator:     k.indikator.map(i => ({ nama_indikator: i.nama_indikator })),
                })),
            };

            try {
                const res  = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        const first = Object.values(data.errors)[0];
                        this.toast(Array.isArray(first) ? first[0] : first, 'warning');
                    } else {
                        this.toast(data.message || 'Terjadi kesalahan.', 'error');
                    }
                    return;
                }

                this.toast(data.message, 'success');

                if (!IS_EDIT && data.instrumen_id) {
                    setTimeout(() => {
                        window.location.href = '/kaprodi/ukk/instrumen/' + data.instrumen_id + '/edit';
                    }, 900);
                }
            } catch {
                this.toast('Koneksi gagal. Silakan coba lagi.', 'error');
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
