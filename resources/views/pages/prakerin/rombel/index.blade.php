<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Rombel PKL & Mapping Siswa</h2>
    </x-slot>

    @php
        $externalOptions = $external->map(function ($p) {
            $name = trim((string) ($p->nama ?: $p->guru?->nama_lengkap ?: 'Pembimbing External #' . $p->id));
            $phone = trim((string) $p->telepon);

            return [
                'id' => (string) $p->id,
                'industry_id' => (string) $p->prakerin_industri_id,
                'label' => $phone !== '' ? $name . ' - ' . $phone : $name,
            ];
        })->values();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Total Rombel</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $rombels->total() }}</p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Industri Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $industri->count() }}</p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Pembimbing External</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $external->count() }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Buat Rombel PKL</h3>
                    <p class="text-sm text-gray-500">Pilih industri terlebih dahulu untuk menampilkan pembimbing external yang sesuai.</p>
                </div>
                <form
                    method="POST"
                    action="{{ route('prakerin.rombel-pkl.store') }}"
                    class="grid gap-4 lg:grid-cols-3"
                    x-data="rombelPklForm({
                        industryId: '{{ old('prakerin_industri_id') }}',
                        externalId: '{{ old('pembimbing_external_id') }}',
                    })"
                    x-init="init()"
                >
                    @csrf
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Nama rombel PKL</span>
                        <input name="nama_rombel" value="{{ old('nama_rombel') }}" class="w-full rounded-xl border-gray-200" placeholder="Contoh: PKL XI RPL - Telkom" required>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Industri</span>
                        <select x-ref="industrySelect" x-model="industryId" name="prakerin_industri_id" class="js-rombel-select w-full rounded-xl border-gray-200" data-placeholder="Cari industri..." required>
                            <option value="">Pilih industri</option>
                            @foreach($industri as $i)
                                <option value="{{ $i->id }}" @selected(old('prakerin_industri_id') == $i->id)>{{ $i->nama_industri }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Pembimbing internal</span>
                        <select name="pembimbing_internal_id" class="js-rombel-select w-full rounded-xl border-gray-200" data-placeholder="Cari guru internal..." required>
                            <option value="">Pilih pembimbing internal</option>
                            @foreach($internal as $p)
                                <option value="{{ $p->id }}" @selected(old('pembimbing_internal_id') == $p->id)>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Pembimbing external</span>
                        <select x-ref="externalSelect" name="pembimbing_external_id" class="w-full rounded-xl border-gray-200" required>
                            <option value="">Pilih industri dahulu</option>
                        </select>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Tanggal mulai</span>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full rounded-xl border-gray-200" required>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Tanggal selesai</span>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full rounded-xl border-gray-200" required>
                    </label>
                    <label class="space-y-1">
                        <span class="text-sm font-semibold text-gray-700">Status</span>
                        <select name="status" class="js-rombel-select w-full rounded-xl border-gray-200">
                            <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                            <option value="aktif" @selected(old('status') === 'aktif')>Aktif</option>
                            <option value="selesai" @selected(old('status') === 'selesai')>Selesai</option>
                        </select>
                    </label>
                    <div class="lg:col-span-2 flex items-end">
                        <button class="w-full sm:w-auto rounded-xl bg-red-600 px-5 py-2.5 text-white font-semibold hover:bg-red-700">
                            Buat Rombel PKL
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse($rombels as $r)
                    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5" x-data="{ editOpen: false }">
                        <div class="flex justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $r->nama_rombel }}</h3>
                                <p class="text-sm text-gray-500">{{ $r->industri?->nama_industri ?? '-' }}</p>
                            </div>
                            <span class="h-fit rounded-full bg-gray-100 px-3 py-1 text-xs font-bold uppercase text-gray-600">{{ $r->status }}</span>
                        </div>
                        <div class="mt-4 text-sm text-gray-600 space-y-1">
                            <p>Internal: <span class="font-medium text-gray-800">{{ $r->pembimbingInternal?->nama ?? '-' }}</span></p>
                            <p>External: <span class="font-medium text-gray-800">{{ $r->pembimbingExternal?->nama ?? '-' }}</span></p>
                            <p>Periode: {{ $r->tanggal_mulai?->format('d M Y') ?? '-' }} - {{ $r->tanggal_selesai?->format('d M Y') ?? '-' }}</p>
                            <p>Anggota: <span class="font-medium text-gray-800">{{ $r->penempatans_count }} siswa</span></p>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-2">
                            <a href="{{ route('prakerin.rombel-pkl.mapping', $r) }}" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Mapping Siswa</a>
                            <button type="button" @click="editOpen = true; $nextTick(() => window.dispatchEvent(new CustomEvent('rombel-edit-open')))" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Edit</button>
                            <form method="POST" action="{{ route('prakerin.rombel-pkl.destroy', $r) }}" class="js-delete-rombel" data-name="{{ $r->nama_rombel }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-xl border border-red-100 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </div>

                        <div x-cloak x-show="editOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 p-4">
                            <div @click.outside="editOpen = false" class="w-full max-w-3xl rounded-2xl bg-white shadow-xl">
                                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                    <div>
                                        <h4 class="font-bold text-gray-900">Edit Rombel PKL</h4>
                                        <p class="text-sm text-gray-500">{{ $r->nama_rombel }}</p>
                                    </div>
                                    <button type="button" @click="editOpen = false" class="rounded-full p-2 text-gray-500 hover:bg-gray-100">x</button>
                                </div>
                                <form
                                    method="POST"
                                    action="{{ route('prakerin.rombel-pkl.update', $r) }}"
                                    class="grid gap-4 p-6 sm:grid-cols-2"
                                    x-data="rombelPklForm({
                                        industryId: '{{ old('prakerin_industri_id', $r->prakerin_industri_id) }}',
                                        externalId: '{{ old('pembimbing_external_id', $r->pembimbing_external_id) }}',
                                    })"
                                    x-init="init()"
                                >
                                    @csrf
                                    @method('PUT')
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Nama rombel PKL</span>
                                        <input name="nama_rombel" value="{{ old('nama_rombel', $r->nama_rombel) }}" class="w-full rounded-xl border-gray-200" required>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Industri</span>
                                        <select x-ref="industrySelect" x-model="industryId" name="prakerin_industri_id" class="js-rombel-select w-full rounded-xl border-gray-200" data-placeholder="Cari industri..." required>
                                            <option value="">Pilih industri</option>
                                            @foreach($industri as $i)
                                                <option value="{{ $i->id }}" @selected(old('prakerin_industri_id', $r->prakerin_industri_id) == $i->id)>{{ $i->nama_industri }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Pembimbing internal</span>
                                        <select name="pembimbing_internal_id" class="js-rombel-select w-full rounded-xl border-gray-200" data-placeholder="Cari guru internal..." required>
                                            <option value="">Pilih pembimbing internal</option>
                                            @foreach($internal as $p)
                                                <option value="{{ $p->id }}" @selected(old('pembimbing_internal_id', $r->pembimbing_internal_id) == $p->id)>{{ $p->nama }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Pembimbing external</span>
                                        <select x-ref="externalSelect" name="pembimbing_external_id" class="w-full rounded-xl border-gray-200" required>
                                            <option value="">Pilih industri dahulu</option>
                                        </select>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Tanggal mulai</span>
                                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $r->tanggal_mulai?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-200" required>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Tanggal selesai</span>
                                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $r->tanggal_selesai?->format('Y-m-d')) }}" class="w-full rounded-xl border-gray-200" required>
                                    </label>
                                    <label class="space-y-1">
                                        <span class="text-sm font-semibold text-gray-700">Status</span>
                                        <select name="status" class="js-rombel-select w-full rounded-xl border-gray-200">
                                            <option value="draft" @selected(old('status', $r->status) === 'draft')>Draft</option>
                                            <option value="aktif" @selected(old('status', $r->status) === 'aktif')>Aktif</option>
                                            <option value="selesai" @selected(old('status', $r->status) === 'selesai')>Selesai</option>
                                        </select>
                                    </label>
                                    <div class="sm:col-span-2 flex justify-end gap-2">
                                        <button type="button" @click="editOpen = false" class="rounded-xl border border-gray-200 px-4 py-2 font-semibold text-gray-700">Batal</button>
                                        <button class="rounded-xl bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 text-gray-500">Belum ada rombel PKL.</div>
                @endforelse
            </div>

            {{ $rombels->links() }}
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
            .ts-wrapper.disabled .ts-control {
                background: #f9fafb !important;
                color: #9ca3af !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            const ROMBEL_EXTERNAL_OPTIONS = @json($externalOptions);

            function initRombelSearchSelects(root = document) {
                root.querySelectorAll('.js-rombel-select').forEach((select) => {
                    if (select.tomselect) return;
                    new TomSelect(select, {
                        create: false,
                        allowEmptyOption: true,
                        dropdownParent: 'body',
                        placeholder: select.dataset.placeholder || 'Pilih data...'
                    });
                });
            }

            function rombelPklForm(config) {
                return {
                    industryId: String(config.industryId || ''),
                    externalId: String(config.externalId || ''),
                    externalSelect: null,

                    init() {
                        this.$nextTick(() => {
                            initRombelSearchSelects(this.$root);
                            this.externalSelect = new TomSelect(this.$refs.externalSelect, {
                                create: false,
                                allowEmptyOption: true,
                                dropdownParent: 'body',
                                placeholder: 'Pilih pembimbing external...'
                            });
                            const updateIndustry = (value) => {
                                this.industryId = String(value || '');
                                this.externalId = '';
                                this.refreshExternalOptions();
                            };

                            this.$refs.industrySelect.addEventListener('change', (event) => updateIndustry(event.target.value));
                            if (this.$refs.industrySelect.tomselect) {
                                this.$refs.industrySelect.tomselect.on('change', updateIndustry);
                            }
                            this.refreshExternalOptions();
                        });
                    },

                    refreshExternalOptions() {
                        if (!this.externalSelect) return;

                        const rows = ROMBEL_EXTERNAL_OPTIONS.filter((item) => String(item.industry_id || '') === String(this.industryId || ''));
                        const currentValue = rows.some((item) => String(item.id || '') === String(this.externalId || '')) ? this.externalId : '';

                        this.externalSelect.clear(true);
                        this.externalSelect.clearOptions();
                        rows.forEach((item) => {
                            const value = String(item.id || '');
                            const text = String(item.label || 'Pembimbing External');
                            if (!value) return;

                            this.externalSelect.addOption({
                                value: value,
                                text: text,
                            });
                        });

                        if (!this.industryId || rows.length === 0) {
                            this.externalSelect.disable();
                        } else {
                            this.externalSelect.enable();
                        }

                        this.externalSelect.refreshOptions(false);
                        this.externalSelect.setValue(currentValue, true);
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                initRombelSearchSelects();

                document.querySelectorAll('.js-delete-rombel').forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        event.preventDefault();
                        const name = form.dataset.name || 'rombel PKL ini';

                        if (typeof Swal === 'undefined') {
                            if (confirm('Hapus ' + name + '?')) form.submit();
                            return;
                        }

                        Swal.fire({
                            title: 'Hapus Rombel PKL?',
                            text: 'Anda akan menghapus ' + name + '.',
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
