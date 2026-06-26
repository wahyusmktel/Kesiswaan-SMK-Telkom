<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Pembimbing Industri</h2>
            <p class="text-sm text-gray-500">Kelola pembimbing internal sekolah dan pembimbing external dari industri.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Total Pembimbing</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $pembimbings->total() }}</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Guru Internal</p>
                    <p class="mt-2 text-3xl font-extrabold text-red-700">{{ $guru->count() }}</p>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Mitra Industri</p>
                    <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $industri->count() }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="text-base font-bold text-gray-900">Tambah Pembimbing</h3>
                    <p class="text-sm text-gray-500">Pilih tipe pembimbing untuk menampilkan field yang relevan.</p>
                </div>
                <form method="POST" action="{{ route('prakerin.pembimbing.store') }}"
                    class="p-6 space-y-5"
                    x-data="pembimbingForm({ tipe: '{{ old('tipe', 'internal') }}' })"
                    x-init="initSelects($el)">
                    @csrf
                    @include('pages.prakerin.pembimbing.partials.form-fields', [
                        'prefix' => 'create',
                        'schoolName' => $schoolName,
                        'guru' => $guru,
                        'industri' => $industri,
                        'pembimbing' => null,
                    ])
                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                            Simpan Pembimbing
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Daftar Pembimbing</h3>
                        <p class="text-sm text-gray-500">Internal berasal dari {{ $schoolName }}, external berasal dari industri.</p>
                    </div>
                    <form class="grid gap-2 sm:grid-cols-[minmax(220px,1fr)_180px_auto]">
                        <input name="search" value="{{ request('search') }}"
                            class="rounded-xl border-gray-200 text-sm focus:border-red-300 focus:ring-red-100"
                            placeholder="Cari nama pembimbing...">
                        <select name="tipe" class="js-search-select rounded-xl border-gray-200 text-sm">
                            <option value="">Semua tipe</option>
                            <option value="internal" @selected(request('tipe') === 'internal')>Internal</option>
                            <option value="external" @selected(request('tipe') === 'external')>External</option>
                        </select>
                        <button class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Filter</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Asal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Kontak</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($pembimbings as $p)
                                <tr class="hover:bg-gray-50/80">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $p->nama }}</p>
                                        <p class="text-xs text-gray-500">{{ $p->jabatan ?: 'Jabatan belum diisi' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $p->tipe === 'internal' ? 'bg-red-50 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $p->tipe === 'internal' ? 'Internal' : 'External' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $p->tipe === 'internal' ? $schoolName : ($p->industri?->nama_industri ?? 'Data industri tidak ditemukan') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <p>{{ $p->telepon ?: '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $p->email ?: '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2" x-data="{ editOpen: false }">
                                            <button type="button" @click="editOpen = true"
                                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('prakerin.pembimbing.destroy', $p) }}"
                                                class="js-delete-pembimbing" data-name="{{ $p->nama }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </form>

                                            <div x-show="editOpen" x-cloak @click.self="editOpen = false" @keydown.escape.window="editOpen = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4 py-6">
                                                <div class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                                                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                                        <div>
                                                            <h3 class="text-base font-bold text-gray-900">Edit Pembimbing</h3>
                                                            <p class="text-xs text-gray-500">Perbarui data pembimbing prakerin.</p>
                                                        </div>
                                                        <button type="button" @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <form method="POST" action="{{ route('prakerin.pembimbing.update', $p) }}"
                                                        class="p-6 space-y-5"
                                                        x-data="pembimbingForm({ tipe: '{{ old('tipe', $p->tipe) }}' })"
                                                        x-init="initSelects($el)">
                                                        @csrf
                                                        @method('PUT')
                                                        @include('pages.prakerin.pembimbing.partials.form-fields', [
                                                            'prefix' => 'edit_' . $p->id,
                                                            'schoolName' => $schoolName,
                                                            'guru' => $guru,
                                                            'industri' => $industri,
                                                            'pembimbing' => $p,
                                                        ])
                                                        <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                                                            <button type="button" @click="editOpen = false"
                                                                class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-50">
                                                                Batal
                                                            </button>
                                                            <button class="rounded-xl bg-red-600 px-5 py-2 text-sm font-bold text-white hover:bg-red-700">
                                                                Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada pembimbing.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-6">{{ $pembimbings->links() }}</div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            [x-cloak] { display: none !important; }
            .ts-control {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                min-height: 42px;
                padding: 0.45rem 0.75rem !important;
                font-size: 0.875rem;
            }
            .ts-control.focus {
                border-color: #fca5a5 !important;
                box-shadow: 0 0 0 3px rgba(254, 202, 202, 0.65) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            function initPembimbingSearchSelects(root = document) {
                root.querySelectorAll('.js-search-select').forEach((select) => {
                    if (select.tomselect) return;
                    new TomSelect(select, {
                        create: false,
                        allowEmptyOption: true,
                        dropdownParent: 'body',
                        placeholder: select.dataset.placeholder || 'Pilih data...'
                    });
                });
            }

            function pembimbingForm(config) {
                return {
                    tipe: config.tipe || 'internal',
                    initSelects(root) {
                        this.$nextTick(() => initPembimbingSearchSelects(root));
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                initPembimbingSearchSelects();

                document.querySelectorAll('.js-delete-pembimbing').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        const name = form.dataset.name || 'data pembimbing ini';

                        if (typeof Swal === 'undefined') {
                            if (confirm('Hapus ' + name + '?')) form.submit();
                            return;
                        }

                        Swal.fire({
                            title: 'Hapus Pembimbing?',
                            text: 'Anda akan menghapus ' + name + '. Tindakan ini tidak dapat dibatalkan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#9ca3af',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(function (result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
