<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Master Data Industri Prakerin') }}</h2>
            <p class="text-sm text-gray-500">Kelola data mitra industri untuk kebutuhan prakerin secara cepat dan rapi.</p>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8" x-data="{ createOpen: false }">
            <div class="bg-white border border-gray-100 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Prakerin</span>
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Master Data</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Daftar Industri</h3>
                                <p class="text-sm text-gray-500">Pantau mitra industri, kota, dan PIC yang terhubung.</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" placeholder="Cari industri atau kota..."
                                    class="w-full sm:w-64 rounded-xl border border-gray-200 bg-white px-10 py-2 text-sm text-gray-700 shadow-sm focus:border-red-300 focus:ring focus:ring-red-100">
                            </div>
                            <button type="button" @click="createOpen = true"
                                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-200">
                                + Tambah Industri
                            </button>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Industri</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->total() }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Data Ditampilkan</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->count() }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-red-50/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Halaman</p>
                            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $industri->currentPage() }} / {{ $industri->lastPage() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white border border-gray-100 shadow-sm sm:rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Data Industri</p>
                        <p class="text-xs text-gray-500">Kelola data industri prakerin dengan cepat.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                        {{ $industri->total() }} data
                    </span>
                </div>
                <div class="relative" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 600)">
                    <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white/90 px-6 py-4">
                        <div class="space-y-4 animate-pulse">
                            <div class="h-4 w-1/3 rounded-full bg-gray-200"></div>
                            <div class="space-y-3">
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                                <div class="h-10 rounded-xl bg-gray-100"></div>
                            </div>
                        </div>
                    </div>
                    <div x-show="!loading" x-transition.opacity x-cloak class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Industri</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kota</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak PIC</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($industri as $item)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-gray-900">{{ $item->nama_industri }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->alamat ?? 'Alamat belum tersedia' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <p class="font-medium">{{ $item->kabupaten_name ?: $item->kota }}</p>
                                            <p class="text-xs text-gray-500">{{ collect([$item->kecamatan_name, $item->desa_name])->filter()->join(', ') }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700">{{ $item->nama_pic ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->email_pic ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-2" x-data="{ editOpen: false }">
                                                <button type="button" @click="editOpen = true"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-gray-50">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1 0v14m0 0H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v5" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form action="{{ route('prakerin.industri.destroy', $item->id) }}"
                                                    method="POST" class="inline-block js-delete" data-industri="{{ $item->nama_industri }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-3h4m-4 0a1 1 0 00-1 1v1h6V5a1 1 0 00-1-1m-4 0h4" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>

                                                <div x-show="editOpen" @click.outside="editOpen = false" @keydown.escape.window="editOpen = false"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4 py-6"
                                                    style="display: none;">
                                                    <div class="max-h-[92vh] w-full max-w-6xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                                                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                                                            <div>
                                                                <h3 class="text-base font-semibold text-gray-900">Edit Industri</h3>
                                                                <p class="text-xs text-gray-500">Perbarui data industri sesuai kebutuhan.</p>
                                                            </div>
                                                            <button type="button" @click="editOpen = false" class="text-gray-400 hover:text-gray-600">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <form method="POST" action="{{ route('prakerin.industri.update', $item->id) }}" class="px-6 py-6 space-y-5">
                                                            @csrf
                                                            @method('PUT')
                                                            @include('pages.prakerin.industri.partials.interactive-form', ['industri' => $item, 'prefix' => 'edit_industri_' . $item->id])
                                                            <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                                                                <button type="button" @click="editOpen = false"
                                                                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                                                    Batal
                                                                </button>
                                                                <x-primary-button class="justify-center">Simpan Perubahan</x-primary-button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="mx-auto flex max-w-sm flex-col items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">Belum ada data industri</p>
                                                    <p class="text-xs text-gray-500">Tambahkan industri baru untuk memulai.</p>
                                                </div>
                                                <button type="button" @click="createOpen = true"
                                                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                                                    + Tambah Industri
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $industri->links() }}
                </div>
            </div>

            <div x-show="createOpen" @click.outside="createOpen = false" @keydown.escape.window="createOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4 py-6"
                style="display: none;">
                <div class="max-h-[92vh] w-full max-w-6xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Tambah Industri</h3>
                            <p class="text-xs text-gray-500">Lengkapi data industri baru untuk prakerin.</p>
                        </div>
                        <button type="button" @click="createOpen = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('prakerin.industri.store') }}" class="px-6 py-6 space-y-5">
                        @csrf
                        @include('pages.prakerin.industri.partials.interactive-form', ['prefix' => 'create_industri'])
                        <div class="flex flex-col gap-2 border-t border-gray-100 pt-4 sm:flex-row sm:justify-end">
                            <button type="button" @click="createOpen = false"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                                Batal
                            </button>
                            <x-primary-button class="justify-center">Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const WILAYAH_API_BASE = @js(url('/prakerin/wilayah'));
        const DEFAULT_COORDINATE = [-5.3971, 105.2668];

        function normalizeWilayahResponse(payload) {
            const rows = Array.isArray(payload) ? payload : (payload.data || []);
            return rows.map((item) => ({
                code: item.code || item.id || item.kode,
                name: item.name || item.nama,
            })).filter((item) => item.code && item.name);
        }

        async function fetchWilayah(path) {
            try {
                const response = await fetch(`${WILAYAH_API_BASE}${path}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat data wilayah');
                return normalizeWilayahResponse(await response.json());
            } catch (error) {
                console.warn('Gagal memuat data wilayah:', error);
                return [];
            }
        }

        window.prakerinIndustryForm = function(config) {
            return {
                prefix: config.prefix,
                address: config.initialAddress || '',
                searchQuery: '',
                latitude: config.initialLat || '',
                longitude: config.initialLng || '',
                provinceCode: config.provinceCode || '',
                provinceName: config.provinceName || '',
                regencyCode: config.regencyCode || '',
                regencyName: config.regencyName || '',
                districtCode: config.districtCode || '',
                districtName: config.districtName || '',
                villageCode: config.villageCode || '',
                villageName: config.villageName || '',
                provinces: [],
                regencies: [],
                districts: [],
                villages: [],
                loading: {
                    regencies: false,
                    districts: false,
                    villages: false,
                },
                map: null,
                marker: null,
                resizeObserver: null,

                async init() {
                    await this.loadProvinces();
                    if (this.provinceCode) await this.loadRegencies();
                    if (this.regencyCode) await this.loadDistricts();
                    if (this.districtCode) await this.loadVillages();
                    this.$nextTick(() => this.initMap());
                },

                async loadProvinces() {
                    this.provinces = await fetchWilayah('/provinces');
                    this.provinceName = this.findName(this.provinces, this.provinceCode, this.provinceName);
                },

                async loadRegencies() {
                    if (!this.provinceCode) return;
                    this.loading.regencies = true;
                    try {
                        this.regencies = await fetchWilayah(`/regencies/${this.provinceCode}`);
                        this.regencyName = this.findName(this.regencies, this.regencyCode, this.regencyName);
                    } finally {
                        this.loading.regencies = false;
                    }
                },

                async loadDistricts() {
                    if (!this.regencyCode) return;
                    this.loading.districts = true;
                    try {
                        this.districts = await fetchWilayah(`/districts/${this.regencyCode}`);
                        this.districtName = this.findName(this.districts, this.districtCode, this.districtName);
                    } finally {
                        this.loading.districts = false;
                    }
                },

                async loadVillages() {
                    if (!this.districtCode) return;
                    this.loading.villages = true;
                    try {
                        this.villages = await fetchWilayah(`/villages/${this.districtCode}`);
                        this.villageName = this.findName(this.villages, this.villageCode, this.villageName);
                    } finally {
                        this.loading.villages = false;
                    }
                },

                async onProvinceChange() {
                    this.provinceName = this.findName(this.provinces, this.provinceCode, '');
                    this.regencyCode = '';
                    this.regencyName = '';
                    this.districtCode = '';
                    this.districtName = '';
                    this.villageCode = '';
                    this.villageName = '';
                    this.regencies = [];
                    this.districts = [];
                    this.villages = [];
                    await this.loadRegencies();
                },

                async onRegencyChange() {
                    this.regencyName = this.findName(this.regencies, this.regencyCode, '');
                    this.districtCode = '';
                    this.districtName = '';
                    this.villageCode = '';
                    this.villageName = '';
                    this.districts = [];
                    this.villages = [];
                    await this.loadDistricts();
                },

                async onDistrictChange() {
                    this.districtName = this.findName(this.districts, this.districtCode, '');
                    this.villageCode = '';
                    this.villageName = '';
                    this.villages = [];
                    await this.loadVillages();
                },

                onVillageChange() {
                    this.villageName = this.findName(this.villages, this.villageCode, '');
                },

                findName(rows, code, fallback) {
                    return rows.find((row) => row.code === code)?.name || fallback || '';
                },

                initMap() {
                    if (this.map || typeof L === 'undefined') return;
                    const mapElement = document.getElementById(`${this.prefix}_map`);
                    if (!mapElement) return;

                    const lat = parseFloat(this.latitude) || DEFAULT_COORDINATE[0];
                    const lng = parseFloat(this.longitude) || DEFAULT_COORDINATE[1];

                    this.map = L.map(mapElement).setView([lat, lng], this.latitude && this.longitude ? 16 : 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                    this.marker.on('dragend', () => {
                        const point = this.marker.getLatLng();
                        this.setCoordinate(point.lat, point.lng, true);
                    });
                    this.map.on('click', (event) => this.setCoordinate(event.latlng.lat, event.latlng.lng, true));

                    this.resizeObserver = new ResizeObserver(() => {
                        setTimeout(() => this.map.invalidateSize(), 80);
                    });
                    this.resizeObserver.observe(mapElement);

                    setTimeout(() => this.map.invalidateSize(), 250);
                },

                setCoordinate(lat, lng, shouldReverse = false) {
                    this.latitude = Number(lat).toFixed(7);
                    this.longitude = Number(lng).toFixed(7);
                    if (this.marker) this.marker.setLatLng([this.latitude, this.longitude]);
                    if (this.map) this.map.setView([this.latitude, this.longitude], Math.max(this.map.getZoom(), 16));
                    if (shouldReverse) this.reverseGeocode();
                },

                async reverseGeocode() {
                    if (!this.latitude || !this.longitude) return;
                    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${this.latitude}&lon=${this.longitude}&accept-language=id`;
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (data.display_name) this.address = data.display_name;
                },

                async searchLocation() {
                    const query = this.searchQuery.trim();
                    if (!query) return;
                    const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&accept-language=id&q=${encodeURIComponent(query)}`;
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!response.ok) return;
                    const data = await response.json();
                    if (!data.length) return;
                    this.setCoordinate(data[0].lat, data[0].lon, true);
                },

                useBrowserLocation() {
                    if (!navigator.geolocation) return;
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.setCoordinate(position.coords.latitude, position.coords.longitude, true);
                    });
                },
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-delete').forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const industriName = form.getAttribute('data-industri') || 'data ini';

                    Swal.fire({
                        title: 'Hapus Industri?',
                        text: 'Anda akan menghapus ' + industriName + '. Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        [x-cloak] { display: none !important; }
        .leaflet-container { font-family: inherit; }
    </style>
@endpush
</x-app-layout>
