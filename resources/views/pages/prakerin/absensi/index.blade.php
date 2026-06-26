<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Rekap Absensi PKL</h2>
    </x-slot>

    <div class="py-8" x-data="pklAbsensiMapModal()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900">Filter Rekap Absensi</h3>
                    <p class="text-sm text-gray-500">Pantau absensi siswa PKL berdasarkan tanggal, rombel, atau nama siswa.</p>
                </div>
                <form class="grid gap-3 p-6 md:grid-cols-[180px_240px_1fr_auto]">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="rounded-xl border-gray-200">
                    <select name="rombel_id" class="js-absensi-select rounded-xl border-gray-200" data-placeholder="Cari rombel...">
                        <option value="">Semua rombel</option>
                        @foreach($rombels as $r)
                            <option value="{{ $r->id }}" @selected(request('rombel_id') == $r->id)>{{ $r->nama_rombel }}</option>
                        @endforeach
                    </select>
                    <input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-200" placeholder="Cari siswa atau NIS">
                    <button class="rounded-xl border border-gray-200 px-4 font-semibold text-gray-700 hover:bg-gray-50">Filter</button>
                </form>
            </div>

            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Rombel/Industri</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check In</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check Out</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis as $a)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $a->penempatan?->siswa?->nama_lengkap ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $a->penempatan?->siswa?->nis ?? '-' }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $a->tanggal?->format('d M Y') ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <p class="font-semibold">{{ $a->penempatan?->rombelPkl?->nama_rombel ?? '-' }}</p>
                                        <p class="text-gray-500">{{ $a->penempatan?->rombelPkl?->industri?->nama_industri ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $a->check_in_at ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $a->check_out_at ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-bold uppercase text-gray-600">{{ $a->status ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @if($a->check_in_latitude && $a->check_in_longitude)
                                                <button
                                                    type="button"
                                                    class="w-fit rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                                    @click="openLocation(
                                                        @js((float) $a->check_in_latitude),
                                                        @js((float) $a->check_in_longitude),
                                                        @js('Check-in - ' . ($a->penempatan?->siswa?->nama_lengkap ?? 'Siswa')),
                                                        @js($a->check_in_at ?? '-')
                                                    )"
                                                >
                                                    Lihat OSM Check-in
                                                </button>
                                            @endif
                                            @if($a->check_out_latitude && $a->check_out_longitude)
                                                <button
                                                    type="button"
                                                    class="w-fit rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                                                    @click="openLocation(
                                                        @js((float) $a->check_out_latitude),
                                                        @js((float) $a->check_out_longitude),
                                                        @js('Check-out - ' . ($a->penempatan?->siswa?->nama_lengkap ?? 'Siswa')),
                                                        @js($a->check_out_at ?? '-')
                                                    )"
                                                >
                                                    Lihat OSM Check-out
                                                </button>
                                            @endif
                                            @if(! $a->check_in_latitude && ! $a->check_out_latitude)
                                                <span class="text-xs text-gray-500">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada data absensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-6">{{ $absensis->links() }}</div>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4">
            <div @click.outside="close()" class="w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 px-6 py-4">
                    <div>
                        <h3 class="font-bold text-gray-900" x-text="title"></h3>
                        <p class="text-sm text-gray-500">
                            <span x-text="timeLabel"></span>
                            <span class="mx-1">-</span>
                            <span x-text="coordinateLabel"></span>
                        </p>
                    </div>
                    <button type="button" @click="close()" class="rounded-full p-2 text-gray-500 hover:bg-gray-100">x</button>
                </div>
                <div class="h-[420px] bg-gray-100">
                    <iframe x-bind:src="embedUrl" class="h-full w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="flex justify-end gap-2 border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <a x-bind:href="osmUrl" target="_blank" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-white">Buka di OSM</a>
                    <button type="button" @click="close()" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tutup</button>
                </div>
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
            function pklAbsensiMapModal() {
                return {
                    open: false,
                    title: '',
                    timeLabel: '',
                    coordinateLabel: '',
                    embedUrl: '',
                    osmUrl: '',

                    openLocation(lat, lng, title, timeLabel) {
                        const latitude = Number(lat);
                        const longitude = Number(lng);
                        const delta = 0.004;
                        const bbox = [
                            longitude - delta,
                            latitude - delta,
                            longitude + delta,
                            latitude + delta
                        ].join(',');

                        this.title = title;
                        this.timeLabel = timeLabel;
                        this.coordinateLabel = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                        this.embedUrl = `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${latitude},${longitude}`;
                        this.osmUrl = `https://www.openstreetmap.org/?mlat=${latitude}&mlon=${longitude}#map=18/${latitude}/${longitude}`;
                        this.open = true;
                    },

                    close() {
                        this.open = false;
                        this.embedUrl = '';
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.js-absensi-select').forEach((select) => {
                    if (select.tomselect) return;
                    new TomSelect(select, {
                        create: false,
                        allowEmptyOption: true,
                        dropdownParent: 'body',
                        placeholder: select.dataset.placeholder || 'Pilih data...'
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
