<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('hubin.rombel-pkl.index') }}" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Kembali ke Daftar Rombel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Mapping Siswa - {{ $rombel->nama_rombel }}</h2>
                <p class="text-xs text-gray-500">Penempatan peserta didik khusus tingkat kelas XII ke rombongan belajar PKL</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Flash / Error Messages --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Terjadi kesalahan validasi:
                </div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Detail Header Card Rombel PKL --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            ROMBEL PKL
                        </span>
                        @if($rombel->status === 'aktif')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                AKTIF
                            </span>
                        @elseif($rombel->status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                DRAFT
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                SELESAI
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $rombel->nama_rombel }}</h1>
                    <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1 font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Industri: {{ $rombel->industri?->nama_industri ?? '-' }}
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Guru Pembimbing: <strong class="text-gray-700">{{ $rombel->pembimbingInternal?->nama ?? '-' }}</strong>
                        </span>
                        <span>&bull;</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Pembimbing Industri: <strong class="text-gray-700">{{ $rombel->pembimbingExternal?->nama ?? ($rombel->industri?->nama_pic ?? '-') }}</strong>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6">
                    <div class="text-center">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Anggota</p>
                        <p class="text-3xl font-extrabold text-indigo-600 mt-0.5">{{ $rombel->penempatans->count() }}</p>
                        <p class="text-[11px] text-gray-400">Siswa PKL</p>
                    </div>
                    <a href="{{ route('hubin.rombel-pkl.index') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Daftar Rombel</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 2 Panels Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Panel Kiri: Anggota Rombel Saat Ini (5 Kolom) --}}
            <div class="lg:col-span-5 bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Anggota Rombel PKL</h3>
                        <p class="text-xs text-gray-500">Siswa yang ditempatkan pada rombel ini</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                        {{ $rombel->penempatans->count() }} Siswa
                    </span>
                </div>

                <div class="max-h-[600px] overflow-y-auto divide-y divide-gray-100 p-2">
                    @forelse($rombel->penempatans as $penempatan)
                        <div class="p-3 hover:bg-gray-50/70 rounded-xl transition flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs flex-shrink-0">
                                    {{ substr($penempatan->siswa?->nama_lengkap ?? 'S', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-xs truncate">
                                        {{ $penempatan->siswa?->nama_lengkap ?? '-' }}
                                    </p>
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-500 mt-0.5">
                                        <span class="font-mono text-gray-600">NIS: {{ $penempatan->siswa?->nis ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700">
                                            {{ $penempatan->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'Kelas XII' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <form
                                method="POST"
                                action="{{ route('hubin.rombel-pkl.mapping.destroy', [$rombel->id, $penempatan->id]) }}"
                                onsubmit="return confirmRemoveStudent(event, '{{ $penempatan->siswa?->nama_lengkap }}')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    title="Lepas Siswa dari Rombel"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Lepas</span>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="font-semibold text-gray-600 text-sm">Belum ada siswa di rombel ini</p>
                            <p class="text-xs text-gray-400 mt-1">Pilih siswa kelas XII dari panel sebelah kanan untuk ditambahkan ke rombel ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Panel Kanan: Tambah Siswa Khusus Kelas XII (7 Kolom) --}}
            <div class="lg:col-span-7 bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden" x-data="mappingPicker()">
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Tambah Siswa Kelas XII</h3>
                            <p class="text-xs text-gray-500">Pilih siswa tingkat kelas XII yang belum terdaftar di rombel PKL manapun</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                            Khusus Kelas XII
                        </span>
                    </div>

                    {{-- Notice Banner --}}
                    <div class="mt-3 rounded-xl bg-blue-50/80 border border-blue-100 p-3 text-xs text-blue-800 flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            Hanya menampilkan siswa aktif tingkat kelas XII yang <strong>belum memiliki rombel PKL</strong>. Siswa yang sudah masuk rombel otomatis dikeluarkan dari daftar ini.
                        </span>
                    </div>
                </div>

                {{-- Toolbar Pencarian & Filter Kelas XII --}}
                <div class="p-5 border-b border-gray-100 bg-gray-50/40">
                    <form method="GET" action="{{ route('hubin.rombel-pkl.mapping', $rombel->id) }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                        {{-- Filter Kelas XII (Searchable) --}}
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Filter Kelas XII</label>
                            <select name="kelas_id" class="js-mapping-tomselect w-full rounded-xl border-gray-200 text-xs" data-placeholder="Semua Kelas XII...">
                                <option value="">Semua Kelas XII</option>
                                @foreach($kelasXii as $k)
                                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>
                                        {{ $k->nama_kelas }} ({{ $k->jurusan ?? 'Kelas XII' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Nama / NIS --}}
                        <div class="sm:col-span-5">
                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Cari Nama / NIS</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Ketik nama atau NIS siswa..."
                                    class="w-full rounded-xl border-gray-200 bg-white pl-9 pr-3 text-xs focus:border-red-500 focus:ring-red-500"
                                >
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="sm:col-span-2 flex items-center gap-1.5">
                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center px-3 py-2 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-semibold text-xs shadow-sm transition">
                                Filter
                            </button>
                            @if(request()->anyFilled(['kelas_id', 'search']))
                                <a href="{{ route('hubin.rombel-pkl.mapping', $rombel->id) }}" title="Reset"
                                    class="p-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-500 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Form Penambahan Siswa --}}
                <form method="POST" action="{{ route('hubin.rombel-pkl.mapping.store', $rombel->id) }}">
                    @csrf
                    {{-- Bulk Actions Bar --}}
                    <div class="px-5 py-3 bg-gray-50/80 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700">
                            <input
                                type="checkbox"
                                @change="toggleSelectAll($event)"
                                :checked="isAllSelected()"
                                class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span>Pilih Semua di Halaman Ini</span>
                        </label>

                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 font-medium">
                                <strong class="text-rose-600" x-text="selectedIds.length">0</strong> siswa dipilih
                            </span>
                            <button
                                type="submit"
                                :disabled="selectedIds.length === 0"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold text-xs shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>+ Tambahkan ke Rombel PKL</span>
                            </button>
                        </div>
                    </div>

                    {{-- Daftar Siswa Tersedia --}}
                    <div class="max-h-[500px] overflow-y-auto divide-y divide-gray-100 p-2">
                        @forelse($siswa as $s)
                            <label class="p-3 hover:bg-gray-50/70 rounded-xl transition flex items-center gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="master_siswa_ids[]"
                                    value="{{ $s->id }}"
                                    x-model="selectedIds"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 text-gray-700 font-bold text-xs flex-shrink-0">
                                    {{ substr($s->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-gray-900 text-xs truncate">{{ $s->nama_lengkap }}</p>
                                    <div class="flex items-center gap-2 text-[11px] text-gray-500 mt-0.5">
                                        <span class="font-mono text-gray-600">NIS: {{ $s->nis ?? '-' }}</span>
                                        <span>&bull;</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">
                                            {{ $s->rombels->first()?->kelas?->nama_kelas ?? 'Kelas XII' }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-semibold text-gray-600 text-sm">Tidak ada siswa yang belum dimapping</p>
                                <p class="text-xs text-gray-400 mt-1">Semua siswa kelas XII yang sesuai filter telah memiliki rombel PKL atau belum terdaftar.</p>
                            </div>
                        @endforelse
                    </div>
                </form>

                @if($siswa->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $siswa->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                min-height: 38px;
                padding: 0.35rem 0.65rem !important;
                font-size: 0.75rem !important;
            }
            .ts-control:focus {
                border-color: #ef4444 !important;
                box-shadow: 0 0 0 1px #ef4444 !important;
            }
            .ts-dropdown {
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
                border-color: #f3f4f6 !important;
                z-index: 60 !important;
                font-size: 0.75rem !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.js-mapping-tomselect').forEach(function(el) {
                    if (!el.tomselect) {
                        new TomSelect(el, {
                            create: false,
                            allowEmptyOption: true,
                            maxOptions: 50,
                            placeholder: el.getAttribute('data-placeholder') || 'Pilih...',
                        });
                    }
                });
            });

            function mappingPicker() {
                const currentPageIds = @js($siswa->pluck('id')->map(fn($id) => (string)$id)->values());

                return {
                    selectedIds: [],
                    currentPageIds: currentPageIds,

                    toggleSelectAll(e) {
                        if (e.target.checked) {
                            this.selectedIds = [...new Set([...this.selectedIds, ...this.currentPageIds])];
                        } else {
                            this.selectedIds = this.selectedIds.filter(id => !this.currentPageIds.includes(String(id)));
                        }
                    },

                    isAllSelected() {
                        if (this.currentPageIds.length === 0) return false;
                        return this.currentPageIds.every(id => this.selectedIds.map(String).includes(String(id)));
                    }
                };
            }

            function confirmRemoveStudent(event, studentName) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: 'Lepas Siswa?',
                    text: `Lepas "${studentName}" dari rombel PKL ini? Siswa akan kembali tersedia di daftar mapping.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Lepaskan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }
        </script>
    @endpush
</x-app-layout>
