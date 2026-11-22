<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Set Penguji &amp; Jadwal UKK</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="pengujiManager()" x-init="init()" @show-toast.window="toast($event.detail.msg, $event.detail.type)">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- TOAST NOTIFICATIONS --}}
            <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:300px">
                <template x-for="toast in toasts" :key="toast.id">
                    <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-semibold"
                        :class="{
                            'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type==='success',
                            'bg-red-50 border-red-200 text-red-800':            toast.type==='error',
                            'bg-amber-50 border-amber-200 text-amber-800':      toast.type==='warning',
                        }"
                        :style="'transition:opacity .35s,transform .35s;opacity:'+(toast.visible?1:0)+';transform:translateY('+(toast.visible?'0px':'-8px')+')'">
                        <span x-text="{success:'✅',error:'❌',warning:'⚠️'}[toast.type]"></span>
                        <span class="flex-1" x-text="toast.message"></span>
                        <button @click="removeToast(toast.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
                    </div>
                </template>
            </div>

            {{-- PAGE HEADER --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-black">👨‍🏫 Set Penguji &amp; Jadwal Pelaksanaan UKK</h3>
                        <p class="text-orange-100 text-sm mt-1">
                            Atur jadwal ujian (tanggal, waktu, ruangan) dan tentukan penguji per kelas &middot; Checklist langsung peserta ujian agar tidak tertukar
                            @if($tahunAktif)
                                &middot; TP <span class="font-bold">{{ $tahunAktif->tahun }}</span>
                                Semester <span class="font-bold">{{ $tahunAktif->semester }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 bg-white/20 rounded-xl px-4 py-2 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-white font-bold text-sm">{{ $guruKelas->count() }} Guru Tersedia</span>
                    </div>
                </div>
            </div>

            @if(!$tahunAktif)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center mb-6">
                    <p class="text-4xl mb-3">⚠️</p>
                    <p class="text-amber-800 font-bold">Tidak ada tahun pelajaran aktif.</p>
                    <p class="text-amber-600 text-sm mt-1">Aktifkan tahun pelajaran terlebih dahulu.</p>
                </div>
            @endif

            {{-- UJIAN LIST CARDS --}}
            <template x-for="ujian in ujians" :key="ujian.id">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-6 overflow-hidden hover:shadow-md transition-shadow">

                    {{-- Card Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-slate-50">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="text-base font-black text-gray-900" x-text="ujian.nama_ujian"></span>
                                <span class="px-2.5 py-0.5 rounded-lg bg-orange-50 text-orange-700 text-xs font-bold border border-orange-200"
                                    x-text="ujian.jurusan"></span>
                                <template x-if="ujian.is_bilingual">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-purple-100 text-purple-800 text-xs font-black border border-purple-200">
                                        🌐 Wawancara Bilingual (EN)
                                    </span>
                                </template>
                            </div>
                            <template x-if="ujian.nama_project">
                                <p class="text-xs text-gray-500 flex items-center gap-1.5">
                                    <span>🔧</span>
                                    <span class="font-medium" x-text="ujian.nama_project"></span>
                                </p>
                            </template>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200"
                                x-text="ujian.rombels.length + ' Kelas Terdaftar'">
                            </span>
                            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200"
                                x-text="ujian.pengujis.length + ' Total Penugasan'">
                            </span>
                        </div>
                    </div>

                    {{-- Card Body: Rombel Sections --}}
                    <div class="p-5 space-y-4">
                        <template x-if="ujian.rombels.length === 0">
                            <div class="text-center py-6 text-gray-400 italic text-sm bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                Belum ada kelas yang dimapping pada ujian ini. Silakan mapping kelas di menu Set UKK.
                            </div>
                        </template>

                        <template x-for="rombel in ujian.rombels" :key="rombel.id">
                            <div class="bg-slate-50/70 rounded-2xl border border-slate-200/90 p-4 hover:border-orange-200 transition-all">

                                {{-- Rombel Header (Class Name + Jadwal Pill + Action) --}}
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-sm shrink-0">
                                            🏫
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-black text-gray-900 text-sm" x-text="rombel.nama_kelas"></h4>
                                                <span class="px-2 py-0.5 rounded-md bg-gray-200 text-gray-700 text-[11px] font-bold"
                                                    x-text="rombel.total_siswa + ' Siswa'"></span>
                                            </div>
                                            <p class="text-[11px] text-gray-400">Jadwal &amp; Penguji khusus kelas ini</p>
                                        </div>
                                    </div>

                                    {{-- Jadwal Display & Edit Button --}}
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <template x-if="rombel.tanggal_pelaksanaan">
                                            <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl border border-slate-200 text-xs shadow-xs">
                                                <span class="text-blue-700 font-bold" x-text="'📅 ' + rombel.tanggal_formatted"></span>
                                                <template x-if="rombel.jam_mulai">
                                                    <span class="text-slate-600 font-medium" x-text="'⏰ ' + rombel.jam_mulai + (rombel.jam_selesai ? ' - ' + rombel.jam_selesai : '') + ' WIB'"></span>
                                                </template>
                                                <template x-if="rombel.ruangan">
                                                    <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200" x-text="'📍 ' + rombel.ruangan"></span>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!rombel.tanggal_pelaksanaan">
                                            <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold flex items-center gap-1.5">
                                                <span>⚠️</span>
                                                <span>Jadwal Belum Diatur</span>
                                            </span>
                                        </template>

                                        <button type="button" @click="openJadwalModal(ujian, rombel)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-orange-100 hover:bg-orange-200 text-orange-800 text-xs font-bold transition-colors">
                                            <span>✏️</span>
                                            <span>Atur Jadwal</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Penguji Assigned to This Rombel --}}
                                <div class="pt-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-bold text-gray-500 mr-1">Penguji:</span>

                                        <template x-for="p in getPengujiForRombel(ujian, rombel.id)" :key="p.id">
                                            <div class="flex items-center gap-2 pl-1.5 pr-2 py-1 rounded-2xl border transition-all text-xs"
                                                :class="{
                                                    'bg-purple-50 border-purple-200 text-purple-900': p.peran === 'pengetahuan',
                                                    'bg-amber-50 border-amber-200 text-amber-900':     p.peran === 'keterampilan',
                                                    'bg-white border-slate-200 text-slate-800':       p.peran === 'semua' || !p.peran
                                                }">

                                                {{-- Avatar --}}
                                                <div class="w-6 h-6 rounded-full overflow-hidden bg-slate-100 shrink-0 flex items-center justify-center font-black text-[10px]">
                                                    <template x-if="p.avatar">
                                                        <img :src="p.avatar" :alt="p.nama" class="w-full h-full object-cover">
                                                    </template>
                                                    <template x-if="!p.avatar">
                                                        <span x-text="p.nama.charAt(0).toUpperCase()"></span>
                                                    </template>
                                                </div>

                                                <span class="font-bold text-xs" x-text="p.nama"></span>

                                                {{-- Role Badge / Select --}}
                                                <select :value="p.peran || 'semua'"
                                                    @change="changePeran(ujian, p, $event.target.value)"
                                                    class="text-[11px] font-bold py-0.5 px-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-orange-400 cursor-pointer bg-white"
                                                    :class="{
                                                        'text-purple-700 border-purple-300 font-extrabold': p.peran === 'pengetahuan',
                                                        'text-amber-700 border-amber-300 font-extrabold':   p.peran === 'keterampilan',
                                                        'text-slate-700 font-bold':                         p.peran === 'semua' || !p.peran
                                                    }">
                                                    <option value="pengetahuan">🌐 Pengetahuan (Bilingual)</option>
                                                    <option value="keterampilan">🛠️ Keterampilan</option>
                                                    <option value="semua">👥 Semua Aspek</option>
                                                </select>

                                                {{-- Siswa Checklist / Rentang Button (Click to View & Edit) --}}
                                                <button type="button" @click="openRentangModal(ujian, p, rombel)"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[11px] font-bold border transition-colors cursor-pointer"
                                                    :class="p.siswa_ids && p.siswa_ids.length > 0
                                                        ? 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200'
                                                        : (p.no_urut_mulai && p.no_urut_selesai
                                                            ? 'bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200'
                                                            : 'bg-slate-100 hover:bg-slate-200 text-slate-600 border-slate-200')"
                                                    title="Klik untuk checklist / ubah peserta ujian yang diuji">
                                                    <span>🎯</span>
                                                    <span x-text="p.siswa_ids && p.siswa_ids.length > 0
                                                        ? p.siswa_ids.length + ' Siswa'
                                                        : (p.no_urut_mulai && p.no_urut_selesai
                                                            ? 'No. ' + p.no_urut_mulai + ' - ' + p.no_urut_selesai
                                                            : 'Semua Siswa')">
                                                    </span>
                                                </button>

                                                {{-- Custom Jadwal Sesi Pill if Set --}}
                                                <template x-if="p.tanggal_pelaksanaan">
                                                    <button type="button" @click="openRentangModal(ujian, p, rombel)"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 transition-colors cursor-pointer"
                                                        title="Jadwal Khusus Sesi Ini (Klik untuk ubah)">
                                                        <span x-text="'📅 ' + p.tanggal_formatted"></span>
                                                        <template x-if="p.jam_mulai">
                                                            <span x-text="'⏰ ' + p.jam_mulai"></span>
                                                        </template>
                                                        <template x-if="p.ruangan">
                                                            <span x-text="'📍 ' + p.ruangan"></span>
                                                        </template>
                                                    </button>
                                                </template>

                                                {{-- Delete Button --}}
                                                <button @click="removePenguji(ujian, p)"
                                                    class="w-5 h-5 rounded-full bg-black/5 hover:bg-red-500 hover:text-white flex items-center justify-center text-xs font-black transition-colors"
                                                    title="Hapus penugasan dari kelas ini">
                                                    &times;
                                                </button>
                                            </div>
                                        </template>

                                        <template x-if="getPengujiForRombel(ujian, rombel.id).length === 0">
                                            <span class="text-xs text-gray-400 italic">Belum ada penguji yang ditugaskan ke kelas ini.</span>
                                        </template>

                                        {{-- Add Penguji Button for this Rombel --}}
                                        <button type="button" @click="openPengujiModal(ujian, rombel)"
                                            class="inline-flex items-center gap-1 px-3 py-1 border border-dashed border-orange-300 hover:border-orange-500 rounded-xl text-xs font-bold text-orange-600 hover:bg-orange-50 transition-all">
                                            <span>+</span>
                                            <span x-text="'Tambah Penguji ' + rombel.nama_kelas"></span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </template>
                    </div>

                </div>
            </template>

            <template x-if="ujians.length === 0">
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-5xl mb-4">📋</p>
                    <p class="text-gray-500 font-semibold mb-2">Belum ada ujian UKK pada tahun pelajaran aktif</p>
                    <a href="{{ route('kaprodi.ukk.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-400 transition-all text-sm">
                        + Buat Ujian UKK
                    </a>
                </div>
            </template>

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- MODAL PILIH PENGUJI (DENGAN CHECKLIST NAMA SISWA) --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="modalPengujiOpen" style="display:none"
                class="fixed inset-0 z-[1000] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="modalPengujiOpen = false">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl flex flex-col max-h-[92vh] overflow-hidden"
                    @click.stop>

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50 rounded-t-2xl shrink-0">
                        <div>
                            <p class="font-black text-gray-900 text-base">Pilih Penguji UKK</p>
                            <p class="text-xs text-gray-500">
                                Penugasan untuk kelas: <strong class="text-orange-700" x-text="activeRombelName"></strong>
                                <span class="text-gray-400" x-text="'(Total ' + (activeRombel ? activeRombel.total_siswa : 0) + ' Siswa)'"></span>
                            </p>
                        </div>
                        <button @click="modalPengujiOpen = false"
                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 font-bold transition-colors">
                            &times;
                        </button>
                    </div>

                    {{-- Role Selector --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-slate-50/60 shrink-0">
                        <label class="block text-xs font-black text-gray-700 mb-1.5">1. Pilih Tugas / Aspek yang Diuji:</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                :class="selectedPeran === 'pengetahuan' ? 'bg-purple-50 border-purple-500 text-purple-900 font-black shadow-xs' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                <input type="radio" value="pengetahuan" x-model="selectedPeran" class="sr-only">
                                <span class="text-base mb-0.5">🌐</span>
                                <span>Pengetahuan</span>
                                <span class="text-[10px] text-purple-600 font-normal">(Bilingual / B. Inggris)</span>
                            </label>
                            <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                :class="selectedPeran === 'keterampilan' ? 'bg-amber-50 border-amber-500 text-amber-900 font-black shadow-xs' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                <input type="radio" value="keterampilan" x-model="selectedPeran" class="sr-only">
                                <span class="text-base mb-0.5">🛠️</span>
                                <span>Keterampilan</span>
                                <span class="text-[10px] text-amber-600 font-normal">(Praktik Produktif)</span>
                            </label>
                            <label class="flex flex-col items-center justify-center p-2 rounded-xl border-2 text-center cursor-pointer transition-all text-xs"
                                :class="selectedPeran === 'semua' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 font-black shadow-xs' : 'bg-white border-gray-200 text-gray-600 font-semibold hover:border-gray-300'">
                                <input type="radio" value="semua" x-model="selectedPeran" class="sr-only">
                                <span class="text-base mb-0.5">👥</span>
                                <span>Semua Aspek</span>
                                <span class="text-[10px] text-emerald-600 font-normal">(P &amp; K Produktif)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Checklist Nama Siswa --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-white shrink-0">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-black text-gray-700">2. Pembagian Peserta Siswa yang Diuji:</label>
                            <span class="text-[11px] font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-lg border border-orange-200"
                                x-text="(rentangMode === 'semua' ? (activeRombel ? activeRombel.total_siswa : 0) : selectedSiswaIds.length) + ' Siswa Terpilih'">
                            </span>
                        </div>

                        <div class="flex items-center gap-4 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                <input type="radio" value="semua" x-model="rentangMode" class="text-orange-500 focus:ring-orange-400">
                                <span>Semua Siswa di Kelas Ini <span class="text-gray-400" x-text="'(1 - ' + (activeRombel ? activeRombel.total_siswa : 0) + ')'"></span></span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                <input type="radio" value="checklist" x-model="rentangMode" class="text-orange-500 focus:ring-orange-400">
                                <span>Pilih / Checklist Siswa Tertentu</span>
                            </label>
                        </div>

                        {{-- Checklist Box --}}
                        <div x-show="rentangMode === 'checklist'" class="border border-slate-200 rounded-2xl p-2.5 bg-slate-50/70 space-y-2">
                            {{-- Toolbar: Quick Range & Actions --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-slate-200/80 text-xs">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-slate-600 text-[11px]">Rentang Cepat:</span>
                                    <input type="number" min="1" :max="activeRombel ? activeRombel.total_siswa : 100" x-model.number="quickMulai"
                                        placeholder="1"
                                        class="w-12 px-1.5 py-0.5 text-center text-xs border border-gray-300 rounded-lg bg-white font-bold">
                                    <span class="text-slate-400 text-xs">s/d</span>
                                    <input type="number" min="1" :max="activeRombel ? activeRombel.total_siswa : 100" x-model.number="quickSelesai"
                                        placeholder="15"
                                        class="w-12 px-1.5 py-0.5 text-center text-xs border border-gray-300 rounded-lg bg-white font-bold">
                                    <button type="button" @click="applyQuickRange()"
                                        class="px-2.5 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-bold text-[11px] shadow-xs transition-colors">
                                        ✓ Centang Rentang
                                    </button>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="selectAllSiswa()"
                                        class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-[10px] font-bold">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="clearAllSiswa()"
                                        class="px-2 py-0.5 bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 rounded-lg text-[10px] font-bold">
                                        Kosongkan
                                    </button>
                                </div>
                            </div>

                            {{-- Search in Siswa list --}}
                            <div class="relative">
                                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                </svg>
                                <input type="text" x-model="siswaSearch" placeholder="Cari nama atau nomor urut siswa di kelas ini…"
                                    class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                            </div>

                            {{-- Student Items List --}}
                            <div class="max-h-44 overflow-y-auto space-y-1 pr-1">
                                <template x-for="s in filteredSiswaList" :key="s.id">
                                    <label class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl border transition-all cursor-pointer text-xs"
                                        :class="selectedSiswaIds.includes(s.id)
                                            ? 'bg-orange-50/90 border-orange-300 text-orange-950 font-bold shadow-xs'
                                            : 'bg-white border-slate-100 text-slate-700 hover:bg-slate-100/70'">
                                        <input type="checkbox" :value="s.id" x-model="selectedSiswaIds"
                                            class="rounded text-orange-500 focus:ring-orange-400 w-4 h-4">
                                        <span class="w-7 text-center text-[10px] font-black rounded-md px-1 py-0.5 shrink-0"
                                            :class="selectedSiswaIds.includes(s.id) ? 'bg-orange-200 text-orange-800' : 'bg-slate-100 text-slate-600'"
                                            x-text="'#' + s.nomor_urut"></span>
                                        <span class="truncate flex-1 font-bold" x-text="s.nama"></span>
                                        <span class="text-[10px] text-gray-400 shrink-0 font-normal" x-text="'NIS: ' + s.nis"></span>
                                        <template x-if="isSiswaAssignedToOtherInActive(s.id)">
                                            <span class="text-[9px] font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200 shrink-0"
                                                :title="'Sudah diuji oleh ' + isSiswaAssignedToOtherInActive(s.id)">
                                                ⚠️ <span x-text="isSiswaAssignedToOtherInActive(s.id)"></span>
                                            </span>
                                        </template>
                                    </label>
                                </template>

                                <div x-show="filteredSiswaList.length === 0" class="text-center py-4 text-xs text-gray-400 italic">
                                    Tidak ada siswa yang cocok dengan pencarian.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pengaturan Jadwal Sesi (Opsional) --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-slate-50/40 shrink-0">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-black text-gray-700">3. Jadwal Sesi Penguji Ini (Opsional):</label>
                            <span class="text-[10px] font-semibold text-gray-500" x-text="sesiJadwalMode === 'default' ? 'Mengikuti Jadwal Umum Kelas' : 'Jadwal Khusus Sesi Ini'"></span>
                        </div>

                        <div class="flex items-center gap-4 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                <input type="radio" value="default" x-model="sesiJadwalMode" class="text-orange-500 focus:ring-orange-400">
                                <span>Ikuti Jadwal Kelas</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                <input type="radio" value="custom" x-model="sesiJadwalMode" class="text-orange-500 focus:ring-orange-400">
                                <span>Jadwal Khusus Sesi Ini (Hari 2 / Sesi Lain)</span>
                            </label>
                        </div>

                        <div x-show="sesiJadwalMode === 'custom'" class="border border-slate-200 rounded-2xl p-3 bg-white space-y-2.5">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Ujian:</label>
                                    <input type="date" x-model="sesiTanggal"
                                        class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Jam Mulai:</label>
                                    <input type="time" x-model="sesiJamMulai"
                                        class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Jam Selesai:</label>
                                    <input type="time" x-model="sesiJamSelesai"
                                        class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Ruangan / Lab:</label>
                                <input type="text" x-model="sesiRuangan" placeholder="Contoh: Lab Komputer 1 / Lab Fiber Optik"
                                    class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-orange-400">
                            </div>
                        </div>
                    </div>

                    {{-- Search & Select Guru --}}
                    <div class="px-4 py-2 border-b border-gray-100 shrink-0 bg-slate-50/40">
                        <label class="block text-xs font-black text-gray-700 mb-1">4. Pilih Guru yang Bertugas:</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                            <input x-model="guruSearch" type="text" placeholder="Cari nama guru yang ditugaskan…"
                                class="w-full pl-9 pr-4 py-1.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                        </div>
                    </div>

                    {{-- Guru List --}}
                    <div class="overflow-y-auto flex-1 p-3 flex flex-col gap-1.5 max-h-48">
                        <template x-for="g in filteredGuru" :key="g.id">
                            <button type="button" @click="assignPenguji(g)"
                                :disabled="isSubmittingPenguji"
                                class="flex items-center gap-3 w-full px-3 py-2 rounded-xl text-left transition-all text-xs"
                                :class="isAssignedInActiveRombel(g.id)
                                    ? 'bg-amber-50/70 border border-amber-200 hover:bg-amber-100/70'
                                    : 'bg-gray-50 hover:bg-orange-50 hover:border-orange-200 border border-transparent'">

                                {{-- Avatar --}}
                                <div class="w-8 h-8 rounded-full overflow-hidden bg-orange-100 shrink-0 flex items-center justify-center font-black text-xs text-orange-600">
                                    <template x-if="g.avatar">
                                        <img :src="g.avatar" :alt="g.nama" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!g.avatar">
                                        <span x-text="g.nama.charAt(0).toUpperCase()"></span>
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-800 truncate" x-text="g.nama"></p>
                                    <p class="text-[10px] text-gray-400" x-text="g.kode"></p>
                                </div>

                                <template x-if="isAssignedInActiveRombel(g.id)">
                                    <span class="text-[11px] font-bold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-md border border-amber-300 shrink-0">
                                        + Tambah Sesi (Hari 2 / Sesi Lain)
                                    </span>
                                </template>
                                <template x-if="!isAssignedInActiveRombel(g.id)">
                                    <span class="text-[11px] font-black text-orange-600 shrink-0">+ Tugaskan Guru Ini</span>
                                </template>
                            </button>
                        </template>

                        <div x-show="filteredGuru.length === 0" class="text-center py-4 text-gray-400 text-xs italic">
                            Tidak ada guru yang cocok dengan pencarian.
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="px-5 py-2.5 border-t border-gray-100 shrink-0 flex justify-end">
                        <button @click="modalPengujiOpen = false"
                            class="px-5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- MODAL UBAH PEMBAGIAN SISWA PENGUJI (EDIT CHECKLIST NAMA SISWA) --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="modalRentangOpen" style="display:none"
                class="fixed inset-0 z-[1000] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="modalRentangOpen = false">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[92vh] overflow-hidden"
                    @click.stop>

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50 rounded-t-2xl shrink-0">
                        <div>
                            <p class="font-black text-gray-900 text-base">Atur Pembagian Siswa Penguji</p>
                            <p class="text-xs text-gray-500">
                                Penguji: <strong class="text-orange-700" x-text="editingPenguji?.nama"></strong> &middot; Kelas: <span class="font-bold" x-text="editingRombel?.nama_kelas"></span>
                            </p>
                        </div>
                        <button @click="modalRentangOpen = false"
                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 font-bold transition-colors">
                            &times;
                        </button>
                    </div>

                    <form @submit.prevent="submitRentang()" class="p-5 space-y-3.5 overflow-y-auto flex-1">
                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1.5">Tugas / Aspek:</label>
                            <select x-model="editingPengujiPeran"
                                class="w-full px-3 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50 font-bold">
                                <option value="pengetahuan">🌐 Pengetahuan (Bilingual)</option>
                                <option value="keterampilan">🛠️ Keterampilan</option>
                                <option value="semua">👥 Semua Aspek</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-black text-gray-700">Cakupan Siswa yang Diuji:</label>
                                <span class="text-[11px] font-bold text-orange-600 bg-orange-50 px-2.5 py-0.5 rounded-lg border border-orange-200"
                                    x-text="(editingRentangMode === 'semua' ? (editingRombel ? editingRombel.total_siswa : 0) : editingSelectedSiswaIds.length) + ' Siswa Terpilih'">
                                </span>
                            </div>

                            <div class="space-y-1.5 mb-2.5">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                    <input type="radio" value="semua" x-model="editingRentangMode" class="text-orange-500 focus:ring-orange-400">
                                    <span>Semua Siswa di Kelas Ini <span class="text-gray-400" x-text="'(1 - ' + (editingRombel ? editingRombel.total_siswa : 0) + ')'"></span></span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                    <input type="radio" value="checklist" x-model="editingRentangMode" class="text-orange-500 focus:ring-orange-400">
                                    <span>Pilih / Checklist Siswa Tertentu</span>
                                </label>
                            </div>

                            {{-- Checklist Box in Edit Modal --}}
                            <div x-show="editingRentangMode === 'checklist'" class="border border-slate-200 rounded-2xl p-2.5 bg-slate-50/70 space-y-2">
                                <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-slate-200/80 text-xs">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-bold text-slate-600 text-[11px]">Rentang Cepat:</span>
                                        <input type="number" min="1" :max="editingRombel ? editingRombel.total_siswa : 100" x-model.number="editingQuickMulai"
                                            placeholder="1"
                                            class="w-12 px-1.5 py-0.5 text-center text-xs border border-gray-300 rounded-lg bg-white font-bold">
                                        <span class="text-slate-400 text-xs">s/d</span>
                                        <input type="number" min="1" :max="editingRombel ? editingRombel.total_siswa : 100" x-model.number="editingQuickSelesai"
                                            placeholder="15"
                                            class="w-12 px-1.5 py-0.5 text-center text-xs border border-gray-300 rounded-lg bg-white font-bold">
                                        <button type="button" @click="editingApplyQuickRange()"
                                            class="px-2.5 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-bold text-[11px] shadow-xs transition-colors">
                                            ✓ Centang Rentang
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="editingSelectAllSiswa()"
                                            class="px-2 py-0.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-[10px] font-bold">
                                            Pilih Semua
                                        </button>
                                        <button type="button" @click="editingClearAllSiswa()"
                                            class="px-2 py-0.5 bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 rounded-lg text-[10px] font-bold">
                                            Kosongkan
                                        </button>
                                    </div>
                                </div>

                                {{-- Search in Siswa list --}}
                                <div class="relative">
                                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                                    </svg>
                                    <input type="text" x-model="editingSiswaSearch" placeholder="Cari nama atau nomor urut siswa…"
                                        class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                                </div>

                                {{-- Student Items List --}}
                                <div class="max-h-52 overflow-y-auto space-y-1 pr-1">
                                    <template x-for="s in editingFilteredSiswaList" :key="s.id">
                                        <label class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl border transition-all cursor-pointer text-xs"
                                            :class="editingSelectedSiswaIds.includes(s.id)
                                                ? 'bg-orange-50/90 border-orange-300 text-orange-950 font-bold shadow-xs'
                                                : 'bg-white border-slate-100 text-slate-700 hover:bg-slate-100/70'">
                                            <input type="checkbox" :value="s.id" x-model="editingSelectedSiswaIds"
                                                class="rounded text-orange-500 focus:ring-orange-400 w-4 h-4">
                                            <span class="w-7 text-center text-[10px] font-black rounded-md px-1 py-0.5 shrink-0"
                                                :class="editingSelectedSiswaIds.includes(s.id) ? 'bg-orange-200 text-orange-800' : 'bg-slate-100 text-slate-600'"
                                                x-text="'#' + s.nomor_urut"></span>
                                            <span class="truncate flex-1 font-bold" x-text="s.nama"></span>
                                            <span class="text-[10px] text-gray-400 shrink-0 font-normal" x-text="'NIS: ' + s.nis"></span>
                                            <template x-if="isSiswaAssignedToOtherInEditing(s.id)">
                                                <span class="text-[9px] font-bold text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded border border-amber-200 shrink-0"
                                                    :title="'Sudah diuji oleh ' + isSiswaAssignedToOtherInEditing(s.id)">
                                                    ⚠️ <span x-text="isSiswaAssignedToOtherInEditing(s.id)"></span>
                                                </span>
                                            </template>
                                        </label>
                                    </template>

                                    <div x-show="editingFilteredSiswaList.length === 0" class="text-center py-4 text-xs text-gray-400 italic">
                                        Tidak ada siswa yang cocok dengan pencarian.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pengaturan Jadwal Sesi di Edit Modal --}}
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-black text-gray-700">Jadwal Sesi Penguji Ini:</label>
                                <span class="text-[10px] font-semibold text-gray-500" x-text="editingSesiJadwalMode === 'default' ? 'Mengikuti Jadwal Kelas' : 'Jadwal Khusus Sesi Ini'"></span>
                            </div>

                            <div class="flex items-center gap-4 mb-2">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                    <input type="radio" value="default" x-model="editingSesiJadwalMode" class="text-orange-500 focus:ring-orange-400">
                                    <span>Ikuti Jadwal Kelas</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                                    <input type="radio" value="custom" x-model="editingSesiJadwalMode" class="text-orange-500 focus:ring-orange-400">
                                    <span>Jadwal Khusus Sesi Ini (Hari 2 / Waktu Lain)</span>
                                </label>
                            </div>

                            <div x-show="editingSesiJadwalMode === 'custom'" class="border border-slate-200 rounded-2xl p-3 bg-slate-50/70 space-y-2.5">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Ujian:</label>
                                        <input type="date" x-model="editingSesiTanggal"
                                            class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Jam Mulai:</label>
                                        <input type="time" x-model="editingSesiJamMulai"
                                            class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Jam Selesai:</label>
                                        <input type="time" x-model="editingSesiJamSelesai"
                                            class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Ruangan / Lab:</label>
                                    <input type="text" x-model="editingSesiRuangan" placeholder="Contoh: Lab Komputer 1 / Lab Fiber Optik"
                                        class="w-full px-2.5 py-1 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-orange-400">
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2 shrink-0">
                            <button type="button" @click="modalRentangOpen = false"
                                class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingRentang"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-xs shadow-md transition-all disabled:opacity-50">
                                <span x-text="isSubmittingRentang ? 'Menyimpan…' : '💾 Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- MODAL ATUR JADWAL ROMBEL (SINGLE ROOT MODAL) --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="modalJadwalOpen" style="display:none"
                class="fixed inset-0 z-[1000] bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="modalJadwalOpen = false">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden"
                    @click.stop>

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50 to-amber-50 rounded-t-2xl shrink-0">
                        <div>
                            <p class="font-black text-gray-900 text-base">Atur Jadwal Pelaksanaan UKK</p>
                            <p class="text-xs text-gray-500">
                                Kelas: <strong class="text-orange-700" x-text="jadwalForm.rombelName"></strong>
                            </p>
                        </div>
                        <button @click="modalJadwalOpen = false"
                            class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 font-bold transition-colors">
                            &times;
                        </button>
                    </div>

                    {{-- Form Fields --}}
                    <form @submit.prevent="submitJadwal()" class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1.5">Tanggal Pelaksanaan:</label>
                            <input type="date" x-model="jadwalForm.tanggal_pelaksanaan"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-black text-gray-700 mb-1.5">Jam Mulai:</label>
                                <input type="time" x-model="jadwalForm.jam_mulai"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-700 mb-1.5">Jam Selesai:</label>
                                <input type="time" x-model="jadwalForm.jam_selesai"
                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-gray-700 mb-1.5">Ruangan / Tempat Ujian:</label>
                            <input type="text" x-model="jadwalForm.ruangan" placeholder="Contoh: Lab Jaringan 1 / Bengkel TKJ"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-gray-50">
                        </div>

                        {{-- Modal Footer --}}
                        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                            <button type="button" @click="modalJadwalOpen = false"
                                class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-sm font-bold text-gray-700 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingJadwal"
                                class="px-5 py-2 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-black text-sm shadow-md transition-all disabled:opacity-50">
                                <span x-text="isSubmittingJadwal ? 'Menyimpan…' : '💾 Simpan Jadwal'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- JSON DATA STAGED IN SAFE SCRIPT TAGS --}}
    <script id="raw-ujians-data" type="application/json">
        {!! json_encode($ujiansJson, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
    <script id="raw-gurus-data" type="application/json">
        {!! json_encode($guruKelas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>

    @push('scripts')
    <script>
    function pengujiManager() {
        return {
            toasts: [],
            toastSeq: 0,
            ujians: [],
            allGuru: [],

            // Modal Penguji State
            modalPengujiOpen: false,
            activeUjian: null,
            activeRombel: null,
            activeRombelName: '',
            selectedPeran: 'semua',
            rentangMode: 'semua', // 'semua' | 'checklist'
            selectedSiswaIds: [],
            quickMulai: 1,
            quickSelesai: 15,
            siswaSearch: '',
            guruSearch: '',
            sesiJadwalMode: 'default', // 'default' | 'custom'
            sesiTanggal: '',
            sesiJamMulai: '',
            sesiJamSelesai: '',
            sesiRuangan: '',
            isSubmittingPenguji: false,

            // Modal Edit Rentang State
            modalRentangOpen: false,
            editingUjian: null,
            editingRombel: null,
            editingPenguji: null,
            editingPengujiPeran: 'semua',
            editingRentangMode: 'semua', // 'semua' | 'checklist'
            editingSelectedSiswaIds: [],
            editingQuickMulai: 1,
            editingQuickSelesai: 15,
            editingSiswaSearch: '',
            editingSesiJadwalMode: 'default', // 'default' | 'custom'
            editingSesiTanggal: '',
            editingSesiJamMulai: '',
            editingSesiJamSelesai: '',
            editingSesiRuangan: '',
            isSubmittingRentang: false,

            // Modal Jadwal State
            modalJadwalOpen: false,
            jadwalForm: {
                ujianId: null,
                rombelId: null,
                rombelName: '',
                tanggal_pelaksanaan: '',
                jam_mulai: '',
                jam_selesai: '',
                ruangan: '',
            },
            isSubmittingJadwal: false,

            init() {
                try {
                    const ujiansEl = document.getElementById('raw-ujians-data');
                    const gurusEl  = document.getElementById('raw-gurus-data');
                    this.ujians   = ujiansEl ? JSON.parse(ujiansEl.textContent) : [];
                    this.allGuru  = gurusEl ? JSON.parse(gurusEl.textContent) : [];
                } catch (e) {
                    console.error("Error parsing initial data:", e);
                }
            },

            toast(msg, type = 'success') {
                const id = ++this.toastSeq;
                this.toasts.push({ id, message: msg, type, visible: false });
                this.$nextTick(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = true;
                });
                setTimeout(() => this.removeToast(id), type === 'success' ? 3500 : 5000);
            },

            removeToast(id) {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 400);
            },

            get filteredGuru() {
                const q = this.guruSearch.toLowerCase().trim();
                if (!q) return this.allGuru;
                return this.allGuru.filter(g =>
                    (g.nama && g.nama.toLowerCase().includes(q)) ||
                    (g.kode && g.kode.toLowerCase().includes(q))
                );
            },

            get filteredSiswaList() {
                if (!this.activeRombel || !this.activeRombel.siswas) return [];
                const q = this.siswaSearch.toLowerCase().trim();
                if (!q) return this.activeRombel.siswas;
                return this.activeRombel.siswas.filter(s =>
                    s.nama.toLowerCase().includes(q) ||
                    (s.nis && String(s.nis).includes(q)) ||
                    String(s.nomor_urut).includes(q)
                );
            },

            selectAllSiswa() {
                if (!this.activeRombel || !this.activeRombel.siswas) return;
                this.selectedSiswaIds = this.activeRombel.siswas.map(s => s.id);
            },

            clearAllSiswa() {
                this.selectedSiswaIds = [];
            },

            applyQuickRange() {
                if (!this.activeRombel || !this.activeRombel.siswas) return;
                const m = parseInt(this.quickMulai) || 1;
                const s = parseInt(this.quickSelesai) || 1;
                const min = Math.min(m, s);
                const max = Math.max(m, s);
                const rangeIds = this.activeRombel.siswas
                    .filter(st => st.nomor_urut >= min && st.nomor_urut <= max)
                    .map(st => st.id);
                this.selectedSiswaIds = rangeIds;
                this.toast(`Nomor urut ${min} s.d. ${max} terpilih (${rangeIds.length} siswa).`, 'success');
            },

            isSiswaAssignedToOtherInActive(siswaId) {
                if (!this.activeUjian || !this.activeRombel) return null;
                const otherPengujis = this.activeUjian.pengujis.filter(p => p.rombel_id === this.activeRombel.id);
                for (const p of otherPengujis) {
                    if (p.siswa_ids && p.siswa_ids.includes(siswaId)) {
                        return p.nama;
                    }
                }
                return null;
            },

            get editingFilteredSiswaList() {
                if (!this.editingRombel || !this.editingRombel.siswas) return [];
                const q = this.editingSiswaSearch.toLowerCase().trim();
                if (!q) return this.editingRombel.siswas;
                return this.editingRombel.siswas.filter(s =>
                    s.nama.toLowerCase().includes(q) ||
                    (s.nis && String(s.nis).includes(q)) ||
                    String(s.nomor_urut).includes(q)
                );
            },

            editingSelectAllSiswa() {
                if (!this.editingRombel || !this.editingRombel.siswas) return;
                this.editingSelectedSiswaIds = this.editingRombel.siswas.map(s => s.id);
            },

            editingClearAllSiswa() {
                this.editingSelectedSiswaIds = [];
            },

            editingApplyQuickRange() {
                if (!this.editingRombel || !this.editingRombel.siswas) return;
                const m = parseInt(this.editingQuickMulai) || 1;
                const s = parseInt(this.editingQuickSelesai) || 1;
                const min = Math.min(m, s);
                const max = Math.max(m, s);
                const rangeIds = this.editingRombel.siswas
                    .filter(st => st.nomor_urut >= min && st.nomor_urut <= max)
                    .map(st => st.id);
                this.editingSelectedSiswaIds = rangeIds;
                this.toast(`Nomor urut ${min} s.d. ${max} terpilih (${rangeIds.length} siswa).`, 'success');
            },

            isSiswaAssignedToOtherInEditing(siswaId) {
                if (!this.editingUjian || !this.editingRombel || !this.editingPenguji) return null;
                const otherPengujis = this.editingUjian.pengujis.filter(p => p.rombel_id === this.editingRombel.id && p.id !== this.editingPenguji.id);
                for (const p of otherPengujis) {
                    if (p.siswa_ids && p.siswa_ids.includes(siswaId)) {
                        return p.nama;
                    }
                }
                return null;
            },

            getPengujiForRombel(ujian, rombelId) {
                if (!ujian || !ujian.pengujis) return [];
                return ujian.pengujis.filter(p => p.rombel_id === rombelId || p.rombel_id === null);
            },

            isAssignedInActiveRombel(userId) {
                if (!this.activeUjian || !this.activeRombel) return false;
                return this.activeUjian.pengujis.some(p =>
                    p.user_id === userId && (p.rombel_id === this.activeRombel.id || p.rombel_id === null)
                );
            },

            openPengujiModal(ujian, rombel) {
                this.activeUjian = ujian;
                this.activeRombel = rombel;
                this.activeRombelName = rombel.nama_kelas;
                this.selectedPeran = ujian.is_bilingual ? 'pengetahuan' : 'semua';
                this.rentangMode = 'semua';
                this.selectedSiswaIds = rombel && rombel.siswas ? rombel.siswas.map(s => s.id) : [];
                this.quickMulai = 1;
                this.quickSelesai = rombel ? rombel.total_siswa : 15;
                this.siswaSearch = '';
                this.guruSearch = '';

                // Sesi Jadwal Default
                this.sesiJadwalMode = 'default';
                this.sesiTanggal = rombel ? (rombel.tanggal_pelaksanaan || '') : '';
                this.sesiJamMulai = rombel ? (rombel.jam_mulai || '') : '';
                this.sesiJamSelesai = rombel ? (rombel.jam_selesai || '') : '';
                this.sesiRuangan = rombel ? (rombel.ruangan || '') : '';

                this.modalPengujiOpen = true;
            },

            async assignPenguji(guru) {
                if (this.isSubmittingPenguji) return;
                this.isSubmittingPenguji = true;

                let payloadSiswaIds = null;
                let payloadMulai = null;
                let payloadSelesai = null;

                if (this.rentangMode === 'checklist') {
                    payloadSiswaIds = this.selectedSiswaIds;
                    if (this.activeRombel && this.activeRombel.siswas) {
                        const selectedObjects = this.activeRombel.siswas.filter(st => this.selectedSiswaIds.includes(st.id));
                        if (selectedObjects.length > 0) {
                            payloadMulai = Math.min(...selectedObjects.map(st => st.nomor_urut));
                            payloadSelesai = Math.max(...selectedObjects.map(st => st.nomor_urut));
                        }
                    }
                }

                const payload = {
                    user_id: guru.id,
                    rombel_id: this.activeRombel.id,
                    peran: this.selectedPeran,
                    no_urut_mulai: payloadMulai,
                    no_urut_selesai: payloadSelesai,
                    siswa_ids: payloadSiswaIds,
                };

                if (this.sesiJadwalMode === 'custom') {
                    payload.tanggal_pelaksanaan = this.sesiTanggal || null;
                    payload.jam_mulai = this.sesiJamMulai || null;
                    payload.jam_selesai = this.sesiJamSelesai || null;
                    payload.ruangan = this.sesiRuangan || null;
                }

                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.activeUjian.id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.toast(data.message || 'Gagal menugaskan penguji.', 'error');
                        return;
                    }
                    this.activeUjian.pengujis.push(data.penguji);
                    this.toast(data.message, 'success');
                    this.modalPengujiOpen = false;
                } catch {
                    this.toast('Terjadi kesalahan jaringan.', 'error');
                } finally {
                    this.isSubmittingPenguji = false;
                }
            },

            openRentangModal(ujian, penguji, rombel) {
                this.editingUjian = ujian;
                this.editingRombel = rombel;
                this.editingPenguji = penguji;
                this.editingPengujiPeran = penguji.peran || 'semua';
                this.editingSiswaSearch = '';
                this.editingQuickMulai = 1;
                this.editingQuickSelesai = rombel ? rombel.total_siswa : 15;

                // Jadwal Sesi State in Edit Modal
                this.editingSesiJadwalMode = penguji.tanggal_pelaksanaan ? 'custom' : 'default';
                this.editingSesiTanggal = penguji.tanggal_pelaksanaan || (rombel ? rombel.tanggal_pelaksanaan : '') || '';
                this.editingSesiJamMulai = penguji.jam_mulai || (rombel ? rombel.jam_mulai : '') || '';
                this.editingSesiJamSelesai = penguji.jam_selesai || (rombel ? rombel.jam_selesai : '') || '';
                this.editingSesiRuangan = penguji.ruangan || (rombel ? rombel.ruangan : '') || '';

                if (penguji.siswa_ids && penguji.siswa_ids.length > 0) {
                    this.editingRentangMode = 'checklist';
                    this.editingSelectedSiswaIds = [...penguji.siswa_ids];
                } else if (penguji.no_urut_mulai && penguji.no_urut_selesai && rombel && rombel.siswas) {
                    this.editingRentangMode = 'checklist';
                    this.editingQuickMulai = penguji.no_urut_mulai;
                    this.editingQuickSelesai = penguji.no_urut_selesai;
                    this.editingSelectedSiswaIds = rombel.siswas
                        .filter(s => s.nomor_urut >= penguji.no_urut_mulai && s.nomor_urut <= penguji.no_urut_selesai)
                        .map(s => s.id);
                } else {
                    this.editingRentangMode = 'semua';
                    this.editingSelectedSiswaIds = rombel && rombel.siswas ? rombel.siswas.map(s => s.id) : [];
                }
                this.modalRentangOpen = true;
            },

            async submitRentang() {
                if (this.isSubmittingRentang) return;
                this.isSubmittingRentang = true;

                let payloadSiswaIds = null;
                let payloadMulai = null;
                let payloadSelesai = null;

                if (this.editingRentangMode === 'checklist') {
                    payloadSiswaIds = this.editingSelectedSiswaIds;
                    if (this.editingRombel && this.editingRombel.siswas) {
                        const selectedObjects = this.editingRombel.siswas.filter(st => this.editingSelectedSiswaIds.includes(st.id));
                        if (selectedObjects.length > 0) {
                            payloadMulai = Math.min(...selectedObjects.map(st => st.nomor_urut));
                            payloadSelesai = Math.max(...selectedObjects.map(st => st.nomor_urut));
                        }
                    }
                }

                const payload = {
                    peran: this.editingPengujiPeran,
                    rombel_id: this.editingPenguji.rombel_id,
                    penguji_id: this.editingPenguji.id,
                    no_urut_mulai: payloadMulai,
                    no_urut_selesai: payloadSelesai,
                    siswa_ids: payloadSiswaIds,
                };

                if (this.editingSesiJadwalMode === 'custom') {
                    payload.tanggal_pelaksanaan = this.editingSesiTanggal || null;
                    payload.jam_mulai = this.editingSesiJamMulai || null;
                    payload.jam_selesai = this.editingSesiJamSelesai || null;
                    payload.ruangan = this.editingSesiRuangan || null;
                } else {
                    payload.tanggal_pelaksanaan = null;
                    payload.jam_mulai = null;
                    payload.jam_selesai = null;
                    payload.ruangan = null;
                }

                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.editingUjian.id + '/' + this.editingPenguji.user_id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.toast(data.message || 'Gagal menyimpan perubahan.', 'error');
                        return;
                    }
                    this.editingPenguji.peran = this.editingPengujiPeran;
                    this.editingPenguji.no_urut_mulai = payloadMulai;
                    this.editingPenguji.no_urut_selesai = payloadSelesai;
                    this.editingPenguji.siswa_ids = payloadSiswaIds || [];
                    this.editingPenguji.tanggal_pelaksanaan = data.tanggal_pelaksanaan || '';
                    this.editingPenguji.tanggal_formatted = data.tanggal_formatted || '';
                    this.editingPenguji.jam_mulai = data.jam_mulai || '';
                    this.editingPenguji.jam_selesai = data.jam_selesai || '';
                    this.editingPenguji.ruangan = data.ruangan || '';
                    this.toast(data.message, 'success');
                    this.modalRentangOpen = false;
                } catch {
                    this.toast('Gagal memperbarui pembagian siswa.', 'error');
                } finally {
                    this.isSubmittingRentang = false;
                }
            },

            async changePeran(ujian, pengujiItem, newPeran) {
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + ujian.id + '/' + pengujiItem.user_id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            peran: newPeran,
                            rombel_id: pengujiItem.rombel_id,
                            penguji_id: pengujiItem.id,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.toast(data.message || 'Gagal mengubah peran.', 'error');
                        return;
                    }
                    pengujiItem.peran = newPeran;
                    this.toast(data.message, 'success');
                } catch {
                    this.toast('Gagal memperbarui peran.', 'error');
                }
            },

            async removePenguji(ujian, pengujiItem) {
                if (!confirm('Hapus penugasan penguji ' + pengujiItem.nama + ' dari kelas ini?')) return;
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + ujian.id + '/' + pengujiItem.user_id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            rombel_id: pengujiItem.rombel_id,
                            penguji_id: pengujiItem.id,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.toast(data.message || 'Gagal menghapus penguji.', 'error');
                        return;
                    }
                    ujian.pengujis = ujian.pengujis.filter(p => p.id !== pengujiItem.id);
                    this.toast(data.message, 'success');
                } catch {
                    this.toast('Gagal menghapus penguji.', 'error');
                }
            },

            openJadwalModal(ujian, rombel) {
                this.jadwalForm = {
                    ujianId: ujian.id,
                    rombelId: rombel.id,
                    rombelName: rombel.nama_kelas,
                    tanggal_pelaksanaan: rombel.tanggal_pelaksanaan || '',
                    jam_mulai: rombel.jam_mulai || '',
                    jam_selesai: rombel.jam_selesai || '',
                    ruangan: rombel.ruangan || '',
                };
                this.modalJadwalOpen = true;
            },

            async submitJadwal() {
                if (this.isSubmittingJadwal) return;
                this.isSubmittingJadwal = true;
                try {
                    const res = await fetch('/kaprodi/ukk/penguji/' + this.jadwalForm.ujianId + '/jadwal/' + this.jadwalForm.rombelId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            tanggal_pelaksanaan: this.jadwalForm.tanggal_pelaksanaan,
                            jam_mulai: this.jadwalForm.jam_mulai,
                            jam_selesai: this.jadwalForm.jam_selesai,
                            ruangan: this.jadwalForm.ruangan,
                        }),
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        this.toast(data.message || 'Gagal menyimpan jadwal.', 'error');
                        return;
                    }

                    // Update local reactive state
                    const u = this.ujians.find(x => x.id === this.jadwalForm.ujianId);
                    if (u) {
                        const r = u.rombels.find(x => x.id === this.jadwalForm.rombelId);
                        if (r) {
                            r.tanggal_pelaksanaan = this.jadwalForm.tanggal_pelaksanaan;
                            if (this.jadwalForm.tanggal_pelaksanaan) {
                                const parts = this.jadwalForm.tanggal_pelaksanaan.split('-');
                                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                                r.tanggal_formatted = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                            } else {
                                r.tanggal_formatted = '';
                            }
                            r.jam_mulai = this.jadwalForm.jam_mulai;
                            r.jam_selesai = this.jadwalForm.jam_selesai;
                            r.ruangan = this.jadwalForm.ruangan;
                        }
                    }

                    this.toast(data.message, 'success');
                    this.modalJadwalOpen = false;
                } catch {
                    this.toast('Gagal menyimpan jadwal.', 'error');
                } finally {
                    this.isSubmittingJadwal = false;
                }
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
