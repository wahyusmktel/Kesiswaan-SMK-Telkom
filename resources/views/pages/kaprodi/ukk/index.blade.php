<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Data Uji Kualifikasi Kejuruan (UKK)</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="ukkPage()" x-init="init()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- TOAST CONTAINER --}}
            <div class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none" style="min-width:300px">
                <template x-for="toast in toasts" :key="toast.id">
                    <div class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-semibold"
                        :class="{
                            'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type === 'success',
                            'bg-red-50 border-red-200 text-red-800':            toast.type === 'error',
                            'bg-amber-50 border-amber-200 text-amber-800':      toast.type === 'warning',
                            'bg-blue-50 border-blue-200 text-blue-800':         toast.type === 'info',
                            'opacity-100 translate-y-0': toast.visible,
                            'opacity-0 -translate-y-2':  !toast.visible,
                        }"
                        style="transition: opacity 0.35s ease, transform 0.35s ease">
                        <span x-text="{ success:'✅', error:'❌', warning:'⚠️', info:'ℹ️' }[toast.type]"></span>
                        <span class="flex-1" x-text="toast.message"></span>
                        <button @click="removeToast(toast.id)" class="opacity-60 hover:opacity-100 ml-1 text-lg leading-none">&times;</button>
                    </div>
                </template>
            </div>

            {{-- PAGE HEADER --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-black">📋 Set UKK</h3>
                        <p class="text-indigo-200 text-sm mt-1">Kelola data Uji Kualifikasi Kejuruan per jurusan dan kelas</p>
                    </div>
                    <button @click="openAdd()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 font-bold rounded-xl shadow-md hover:bg-indigo-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Ujian UKK
                    </button>
                </div>
            </div>

            {{-- STATS --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-xl shrink-0">📝</div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Ujian</p>
                        <p class="text-2xl font-black text-gray-800">{{ $ujians->total() }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-xl shrink-0">📅</div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Tahun Aktif</p>
                        <p class="text-sm font-black text-gray-800">{{ $tahunAktif ? $tahunAktif->tahun . ' ' . $tahunAktif->semester : '—' }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center gap-3 col-span-2 sm:col-span-1">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-xl shrink-0">🏫</div>
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">Total Jurusan</p>
                        <p class="text-2xl font-black text-gray-800">{{ $jurusans->count() }}</p>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Daftar Ujian UKK</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-5 py-4 font-bold">#</th>
                                <th class="px-5 py-4 font-bold">Nama Ujian</th>
                                <th class="px-5 py-4 font-bold">Tahun Pelajaran</th>
                                <th class="px-5 py-4 font-bold">Jurusan</th>
                                <th class="px-5 py-4 font-bold">Kelas Terpetakan</th>
                                <th class="px-5 py-4 font-bold">Nama Project</th>
                                <th class="px-5 py-4 font-bold">Tgl. Pelaksanaan</th>
                                <th class="px-5 py-4 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ujians as $i => $ujian)
                                <tr class="bg-white hover:bg-gray-50/70 transition-colors">
                                    <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $ujians->firstItem() + $i }}</td>
                                    <td class="px-5 py-4 font-semibold text-gray-800 max-w-xs">{{ $ujian->nama_ujian }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($ujian->tahunPelajaran)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">
                                                {{ $ujian->tahunPelajaran->tahun }} — {{ $ujian->tahunPelajaran->semester }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex px-2 py-1 rounded-lg bg-purple-50 text-purple-700 text-xs font-bold border border-purple-100">
                                            {{ $ujian->jurusan }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($ujian->rombels_count > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($ujian->rombels->take(3) as $rombel)
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                                        {{ $rombel->kelas?->nama_kelas ?? '?' }}
                                                    </span>
                                                @endforeach
                                                @if($ujian->rombels_count > 3)
                                                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 text-xs font-bold">
                                                        +{{ $ujian->rombels_count - 3 }} lagi
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum ada kelas</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-gray-700 max-w-[180px]">{{ $ujian->nama_project ?? '—' }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-600">
                                        {{ $ujian->tanggal_pelaksanaan?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="openEdit(
                                                    {{ $ujian->id }},
                                                    {{ json_encode($ujian->nama_ujian) }},
                                                    {{ $ujian->tahun_pelajaran_id }},
                                                    {{ json_encode($ujian->jurusan) }},
                                                    {{ json_encode($ujian->nama_project) }},
                                                    {{ json_encode($ujian->tanggal_pelaksanaan?->format('Y-m-d')) }},
                                                    {{ json_encode($ujian->rombels->pluck('id')) }}
                                                )"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold hover:bg-amber-600 hover:text-white hover:border-amber-600 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </button>
                                            <button @click="deleteUjian({{ $ujian->id }}, {{ json_encode($ujian->nama_ujian) }})"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3 text-gray-400">
                                            <span class="text-5xl">📋</span>
                                            <p class="font-semibold">Belum ada data ujian UKK</p>
                                            <button @click="openAdd()" class="text-indigo-600 font-bold text-sm hover:underline">
                                                + Tambah ujian pertama
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($ujians->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">{{ $ujians->links() }}</div>
                @endif
            </div>
        </div>

        {{-- ====================================================
             MODAL BACKDROP
        ==================================================== --}}
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[1000] bg-black/50 backdrop-blur-sm"
            style="display:none">
        </div>

        {{-- ====================================================
             MODAL PANEL
        ==================================================== --}}
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[1001] flex items-center justify-center p-4"
            style="display:none"
            @mousemove.window="onDrag($event)"
            @mouseup.window="stopDrag()">

            <div class="bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden"
                :class="modalMaximized ? 'w-screen h-screen rounded-none' : 'w-full max-w-2xl max-h-[90vh]'"
                :style="!modalMaximized && modalPos.x !== null
                    ? 'position:fixed;left:' + modalPos.x + 'px;top:' + modalPos.y + 'px;width:672px;max-height:90vh'
                    : ''"
                x-ref="modalBox">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-700 text-white shrink-0 select-none"
                    :class="!modalMaximized ? 'cursor-grab active:cursor-grabbing' : ''"
                    @mousedown="startDrag($event)">
                    <div class="flex items-center gap-2.5">
                        <span class="text-lg">📋</span>
                        <div>
                            <p class="font-black text-sm leading-tight" x-text="editMode ? 'Edit Data UKK' : 'Tambah Data UKK'"></p>
                            <p class="text-indigo-200 text-xs">Isi seluruh field yang diperlukan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        {{-- Maximize --}}
                        <button type="button" @click.stop="toggleMaximize()"
                            class="w-7 h-7 rounded-lg bg-white/20 hover:bg-white/30 flex items-center justify-center transition-all" title="Perbesar / Perkecil">
                            <svg x-show="!modalMaximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/>
                            </svg>
                            <svg x-show="modalMaximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                            </svg>
                        </button>
                        {{-- Close --}}
                        <button type="button" @click.stop="closeModal()"
                            class="w-7 h-7 rounded-lg bg-white/20 hover:bg-red-500 flex items-center justify-center transition-all font-black text-base leading-none" title="Tutup">
                            &times;
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- Nama Ujian --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Nama Ujian <span class="text-red-500">*</span>
                        </label>
                        <input x-model="form.nama_ujian" type="text"
                            placeholder="Contoh: Uji Kualifikasi Kejuruan (UKK) Level 1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <p x-show="errors.nama_ujian" x-text="errors.nama_ujian" class="text-red-500 text-xs mt-1 font-medium"></p>
                    </div>

                    {{-- Tahun Pelajaran (readonly) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Tahun Pelajaran &amp; Semester</label>
                        <div class="relative">
                            <input type="text" :value="tahunAktifLabel" readonly
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">Readonly</span>
                        </div>
                    </div>

                    {{-- Jurusan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">
                            Jurusan <span class="text-red-500">*</span>
                        </label>
                        <select x-model="form.jurusan" @change="onJurusanChange()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all bg-white">
                            <option value="">— Pilih Jurusan —</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                            @endforeach
                        </select>
                        <p x-show="errors.jurusan" x-text="errors.jurusan" class="text-red-500 text-xs mt-1 font-medium"></p>
                    </div>

                    {{-- Maping Kelas --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Maping Kelas (Rombongan Belajar)</label>

                        <div x-show="!form.jurusan" class="p-4 rounded-xl border-2 border-dashed border-gray-200 text-center text-sm text-gray-400">
                            Pilih jurusan terlebih dahulu untuk melihat daftar kelas.
                        </div>

                        <div x-show="form.jurusan && loadingRombel" class="flex items-center gap-3 p-4 text-sm text-indigo-600">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Memuat daftar kelas...
                        </div>

                        <div x-show="form.jurusan && !loadingRombel && rombels.length === 0"
                            class="p-4 rounded-xl border-2 border-dashed border-amber-200 bg-amber-50 text-sm text-amber-700 font-medium text-center">
                            Tidak ada rombel aktif untuk jurusan ini.
                        </div>

                        <div x-show="form.jurusan && !loadingRombel && rombels.length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-4 rounded-xl border border-gray-200 bg-gray-50/50 max-h-56 overflow-y-auto">
                            <template x-for="rombel in rombels" :key="rombel.id">
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all select-none"
                                    :class="form.rombel_ids.includes(rombel.id)
                                        ? 'bg-indigo-50 border-indigo-400 shadow-sm'
                                        : 'bg-white border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/30'">
                                    <input type="checkbox" :value="rombel.id"
                                        :checked="form.rombel_ids.includes(rombel.id)"
                                        @change="toggleRombel(rombel.id)"
                                        class="w-4 h-4 rounded accent-indigo-600 shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate" x-text="rombel.nama_kelas"></p>
                                        <p class="text-xs text-gray-400"><span x-text="rombel.siswa_count"></span> siswa</p>
                                    </div>
                                    <span x-show="form.rombel_ids.includes(rombel.id)"
                                        class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">✓</span>
                                </label>
                            </template>
                        </div>
                        <p x-show="form.jurusan && !loadingRombel && rombels.length > 0" class="text-xs text-gray-400 mt-1.5">
                            <span class="font-bold text-indigo-600" x-text="form.rombel_ids.length"></span>
                            dari <span x-text="rombels.length"></span> kelas dipilih
                        </p>
                    </div>

                    {{-- Nama Project UKK --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Project UKK</label>
                        <input x-model="form.nama_project" type="text"
                            placeholder="Contoh: Aplikasi Web E-Commerce"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>

                    {{-- Tanggal Pelaksanaan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Tanggal Pelaksanaan</label>
                        <input x-model="form.tanggal_pelaksanaan" type="date"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="shrink-0 px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3">
                    <button type="button" @click="closeModal()"
                        class="px-5 py-2.5 border-2 border-gray-300 text-gray-700 font-bold rounded-xl text-sm hover:bg-gray-100 transition-all">
                        Tutup
                    </button>
                    <button type="button" @click="submitForm()" :disabled="submitting"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl text-sm shadow-md hover:from-indigo-500 hover:to-purple-500 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span x-text="editMode
                            ? (submitting ? 'Menyimpan...' : 'Simpan Perubahan')
                            : (submitting ? 'Menambahkan...' : 'Tambah Ujian')">
                        </span>
                    </button>
                </div>

            </div>{{-- /modalBox --}}
        </div>{{-- /modal panel --}}

    </div>{{-- /x-data --}}

    @push('scripts')
    <script>
    function ukkPage() {
        return {
            showModal:      false,
            modalMaximized: false,
            editMode:       false,
            editId:         null,
            submitting:     false,
            loadingRombel:  false,

            dragging:   false,
            dragOffset: { x: 0, y: 0 },
            modalPos:   { x: null, y: null },

            form: {
                nama_ujian:          '',
                jurusan:             '',
                nama_project:        '',
                tanggal_pelaksanaan: '',
                rombel_ids:          [],
            },
            errors: {},

            rombels:         [],
            tahunAktifId:    {{ $tahunAktif?->id ?? 'null' }},
            tahunAktifLabel: '{{ $tahunAktif ? $tahunAktif->tahun . " — " . $tahunAktif->semester : "Tidak ada tahun aktif" }}',

            toasts:   [],
            toastSeq: 0,

            init() {},

            openAdd() {
                this.editMode  = false;
                this.editId    = null;
                this.form      = { nama_ujian:'', jurusan:'', nama_project:'', tanggal_pelaksanaan:'', rombel_ids:[] };
                this.errors    = {};
                this.rombels   = [];
                this.modalPos  = { x: null, y: null };
                this.showModal = true;
            },

            openEdit(id, namaUjian, tahunPelajaranId, jurusan, namaProject, tanggalPelaksanaan, rombelIds) {
                this.editMode  = true;
                this.editId    = id;
                this.form      = {
                    nama_ujian:          namaUjian,
                    jurusan:             jurusan,
                    nama_project:        namaProject || '',
                    tanggal_pelaksanaan: tanggalPelaksanaan || '',
                    rombel_ids:          rombelIds || [],
                };
                this.errors    = {};
                this.modalPos  = { x: null, y: null };
                this.showModal = true;
                if (jurusan) this.onJurusanChange();
            },

            closeModal() {
                this.showModal      = false;
                this.modalMaximized = false;
            },

            toggleMaximize() {
                this.modalMaximized = !this.modalMaximized;
                if (this.modalMaximized) this.modalPos = { x: null, y: null };
            },

            startDrag(e) {
                if (this.modalMaximized) return;
                if (e.target.closest('button')) return;
                this.dragging = true;
                const rect = this.$refs.modalBox.getBoundingClientRect();
                this.modalPos   = { x: rect.left, y: rect.top };
                this.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
            },

            onDrag(e) {
                if (!this.dragging) return;
                this.modalPos.x = e.clientX - this.dragOffset.x;
                this.modalPos.y = e.clientY - this.dragOffset.y;
            },

            stopDrag() { this.dragging = false; },

            toggleRombel(id) {
                const idx = this.form.rombel_ids.indexOf(id);
                if (idx > -1) this.form.rombel_ids.splice(idx, 1);
                else          this.form.rombel_ids.push(id);
            },

            async onJurusanChange() {
                if (!this.form.jurusan) { this.rombels = []; return; }
                this.loadingRombel = true;
                this.rombels = [];
                try {
                    const res = await fetch(
                        '{{ route("kaprodi.ukk.rombel-by-jurusan") }}?jurusan=' + encodeURIComponent(this.form.jurusan),
                        { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } }
                    );
                    this.rombels = await res.json();
                } catch {
                    this.toast('Gagal memuat data kelas.', 'error');
                } finally {
                    this.loadingRombel = false;
                }
            },

            async submitForm() {
                this.errors     = {};
                this.submitting = true;

                const url    = this.editMode ? '/kaprodi/ukk/set-ukk/' + this.editId : '{{ route("kaprodi.ukk.store") }}';
                const method = this.editMode ? 'PUT' : 'POST';

                try {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type':     'application/json',
                            'Accept':           'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            nama_ujian:          this.form.nama_ujian,
                            tahun_pelajaran_id:  this.tahunAktifId,
                            jurusan:             this.form.jurusan,
                            nama_project:        this.form.nama_project || null,
                            tanggal_pelaksanaan: this.form.tanggal_pelaksanaan || null,
                            rombel_ids:          this.form.rombel_ids,
                        }),
                    });
                    const data = await res.json();

                    if (!res.ok) {
                        if (res.status === 422 && data.errors) {
                            Object.keys(data.errors).forEach(k => this.errors[k] = data.errors[k][0]);
                            this.toast('Periksa kembali isian form.', 'warning');
                        } else {
                            this.toast(data.message || 'Terjadi kesalahan.', 'error');
                        }
                        return;
                    }

                    this.toast(data.message, 'success');
                    this.closeModal();
                    setTimeout(() => window.location.reload(), 900);
                } catch {
                    this.toast('Koneksi gagal. Silakan coba lagi.', 'error');
                } finally {
                    this.submitting = false;
                }
            },

            async deleteUjian(id, nama) {
                if (!confirm('Hapus ujian "' + nama + '"?\nSeluruh mapping kelas juga akan dihapus.')) return;
                try {
                    const res = await fetch('/kaprodi/ukk/set-ukk/' + id, {
                        method: 'DELETE',
                        headers: {
                            'Accept':           'application/json',
                            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await res.json();
                    if (!res.ok) { this.toast(data.message || 'Gagal menghapus.', 'error'); return; }
                    this.toast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 900);
                } catch {
                    this.toast('Koneksi gagal.', 'error');
                }
            },

            toast(message, type = 'info') {
                const id = ++this.toastSeq;
                this.toasts.push({ id, message, type, visible: false });
                this.$nextTick(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = true;
                });
                setTimeout(() => this.removeToast(id), 4500);
            },

            removeToast(id) {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 400);
            },
        };
    }
    </script>
    @endpush
</x-app-layout>
