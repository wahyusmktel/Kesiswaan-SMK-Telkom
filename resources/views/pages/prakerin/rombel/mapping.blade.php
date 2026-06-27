<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Mapping Siswa - {{ $rombel->nama_rombel }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5 md:col-span-2">
                    <p class="text-sm text-gray-500">Rombel PKL</p>
                    <h3 class="mt-1 text-xl font-bold text-gray-900">{{ $rombel->nama_rombel }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ $rombel->tanggal_mulai?->format('d M Y') ?? '-' }} - {{ $rombel->tanggal_selesai?->format('d M Y') ?? '-' }}</p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Industri</p>
                    <p class="mt-2 font-bold text-gray-900">{{ $rombel->industri?->nama_industri ?? '-' }}</p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Anggota</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $rombel->penempatans->count() }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.35fr]">
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                    <div class="border-b border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900">Anggota Rombel PKL</h3>
                        <p class="text-sm text-gray-500">Siswa yang sudah ditempatkan pada rombel ini.</p>
                    </div>
                    <div class="max-h-[620px] overflow-y-auto p-6">
                        @forelse($rombel->penempatans as $p)
                            <div class="flex items-center justify-between gap-3 border-b border-gray-100 py-3 last:border-0">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $p->siswa?->nama_lengkap ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $p->siswa?->nis ?? '-' }} - {{ $p->siswa?->rombels->first()?->kelas?->nama_kelas ?? '-' }}</p>
                                </div>
                                <form method="POST" action="{{ route('prakerin.rombel-pkl.mapping.destroy', [$rombel, $p]) }}" class="js-remove-mapping" data-name="{{ $p->siswa?->nama_lengkap }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg px-3 py-1.5 text-sm font-semibold text-red-600 hover:bg-red-50">Lepas</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-gray-500">Belum ada anggota.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden" x-data="{ selected: [] }">
                    <div class="border-b border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900">Tambah Siswa</h3>
                        <p class="text-sm text-gray-500">Siswa yang sudah aktif di rombel PKL lain tidak ditampilkan.</p>
                    </div>

                    <form class="grid gap-3 border-b border-gray-100 p-6 md:grid-cols-[220px_1fr_auto]">
                        <select name="kelas_id" class="js-mapping-select rounded-xl border-gray-200" data-placeholder="Cari kelas...">
                            <option value="">Semua kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                        <input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-200" placeholder="Cari nama atau NIS">
                        <button class="rounded-xl border border-gray-200 px-4 font-semibold text-gray-700 hover:bg-gray-50">Filter</button>
                    </form>

                    <form method="POST" action="{{ route('prakerin.rombel-pkl.mapping.store', $rombel) }}" class="p-6">
                        @csrf
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-sm text-gray-500"><span x-text="selected.length">0</span> siswa dipilih</p>
                            <button type="submit" class="rounded-xl bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Tambahkan ke Rombel PKL
                            </button>
                        </div>
                        <div class="space-y-2 max-h-[520px] overflow-y-auto">
                            @forelse($siswa as $s)
                                <label class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 hover:bg-gray-50">
                                    <input x-model="selected" type="checkbox" name="master_siswa_ids[]" value="{{ $s->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $s->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">{{ $s->nis }} - {{ $s->rombels->first()?->kelas?->nama_kelas ?? '-' }}</p>
                                    </div>
                                </label>
                            @empty
                                <p class="text-gray-500 p-6 text-center">Tidak ada siswa tersedia.</p>
                            @endforelse
                        </div>
                    </form>
                    <div class="border-t border-gray-100 p-6">{{ $siswa->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
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
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('select.js-mapping-select').forEach((select) => {
                    if (select.tomselect) return;
                    new TomSelect(select, {
                        create: false,
                        allowEmptyOption: true,
                        dropdownParent: 'body',
                        placeholder: select.dataset.placeholder || 'Pilih data...'
                    });
                });

                document.querySelectorAll('.js-remove-mapping').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        const name = form.dataset.name || 'siswa ini';

                        if (typeof Swal === 'undefined') {
                            if (confirm('Lepas mapping ' + name + '?')) form.submit();
                            return;
                        }

                        Swal.fire({
                            title: 'Lepas Mapping?',
                            text: 'Siswa ' + name + ' akan dilepas dari rombel PKL ini.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#9ca3af',
                            confirmButtonText: 'Ya, lepas',
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
