<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Keterlambatan</h2>
    </x-slot>

    @php
        $selectedKelas = $kelas->firstWhere('id', request('kelas_id'));
        $selectedKelasName = $selectedKelas?->nama_kelas ?? 'Semua Kelas';
    @endphp

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('monitoring-keterlambatan.index') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">
                            {{-- Cari Siswa --}}
                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-md border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                        placeholder="Nama / NIS...">
                                </div>
                            </div>

                            {{-- Filter Tanggal --}}
                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Dari</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="block w-full rounded-md border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="block w-full rounded-md border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                            </div>

                            {{-- Filter Status & Kelas Searchable --}}
                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
                                    <select name="status" class="block w-full rounded-md border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="dicatat_security" {{ request('status') == 'dicatat_security' ? 'selected' : '' }}>Dicatat Security</option>
                                        <option value="diverifikasi_piket" {{ request('status') == 'diverifikasi_piket' ? 'selected' : '' }}>Diverifikasi Piket</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai / Masuk</option>
                                    </select>
                                </div>

                                {{-- Searchable Select Kelas --}}
                                <div class="w-1/2 relative" x-data="{
                                    open: false,
                                    search: '',
                                    selectedId: '{{ request('kelas_id') }}',
                                    selectedName: '{{ addslashes($selectedKelasName) }}',
                                    classes: {{ json_encode($kelas->map(fn($k) => ['id' => (string)$k->id, 'name' => $k->nama_kelas])) }},
                                    get filteredClasses() {
                                        if (!this.search.trim()) return this.classes;
                                        return this.classes.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(id, name) {
                                        this.selectedId = id;
                                        this.selectedName = name;
                                        this.open = false;
                                        this.search = '';
                                    },
                                    reset() {
                                        this.selectedId = '';
                                        this.selectedName = 'Semua Kelas';
                                        this.open = false;
                                        this.search = '';
                                    }
                                }" @click.away="open = false">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Kelas</label>
                                    <input type="hidden" name="kelas_id" :value="selectedId">
                                    
                                    <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                                        class="w-full h-10 px-3 bg-white border border-gray-300 rounded-md shadow-sm text-left text-sm flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors">
                                        <span class="truncate text-gray-800 font-medium text-xs sm:text-sm" x-text="selectedName || 'Semua Kelas'"></span>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-1 transition-transform" :class="open ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak
                                        class="absolute z-50 mt-1 w-full sm:w-64 bg-white border border-gray-200 rounded-md shadow-xl py-1 text-sm max-h-64 flex flex-col focus:outline-none"
                                        style="min-width: 100%;">
                                        <div class="p-2 border-b border-gray-100 bg-gray-50/70">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <input type="text" x-ref="searchInput" x-model="search"
                                                    placeholder="Cari nama kelas..."
                                                    class="pl-8 pr-2 py-1 text-xs w-full bg-white border border-gray-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500 shadow-inner">
                                            </div>
                                        </div>

                                        <div class="overflow-y-auto max-h-48 py-1 divide-y divide-gray-50">
                                            <div @click="reset()"
                                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between text-xs font-semibold text-gray-600 transition-colors"
                                                :class="!selectedId ? 'bg-red-50 text-red-700 font-bold' : ''">
                                                <span>Semua Kelas</span>
                                                <span x-show="!selectedId" class="text-red-600 font-bold">✓</span>
                                            </div>
                                            <template x-for="c in filteredClasses" :key="c.id">
                                                <div @click="select(c.id, c.name)"
                                                    class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex items-center justify-between text-xs text-gray-800 transition-colors"
                                                    :class="selectedId == c.id ? 'bg-red-50 text-red-700 font-bold' : ''">
                                                    <span x-text="c.name"></span>
                                                    <span x-show="selectedId == c.id" class="text-red-600 font-bold">✓</span>
                                                </div>
                                            </template>
                                            <div x-show="filteredClasses.length === 0" class="px-3 py-3 text-center text-xs text-gray-400 italic">
                                                Kelas tidak ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 4 Grouped Action Buttons (Filter, Reset, Excel, PDF) --}}
                            <div class="w-full lg:w-auto">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 hidden lg:block opacity-0">Aksi</label>
                                <div class="inline-flex rounded-md shadow-sm w-full sm:w-auto" role="group">
                                    <button type="submit"
                                        class="flex-1 sm:flex-initial h-10 px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-l-md border border-red-600 transition-colors focus:z-10 focus:ring-1 focus:ring-red-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        Filter
                                    </button>
                                    <a href="{{ route('monitoring-keterlambatan.index') }}"
                                        class="flex-1 sm:flex-initial h-10 px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border-t border-b border-r border-gray-300 transition-colors focus:z-10 focus:ring-1 focus:ring-red-500">
                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Reset
                                    </a>
                                    <button type="submit" name="export" value="excel" formaction="{{ route('monitoring-keterlambatan.export') }}"
                                        class="flex-1 sm:flex-initial h-10 px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-t border-b border-r border-emerald-300 transition-colors focus:z-10 focus:ring-1 focus:ring-emerald-500">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Excel
                                    </button>
                                    <button type="submit" name="export" value="pdf" formaction="{{ route('monitoring-keterlambatan.export') }}"
                                        class="flex-1 sm:flex-initial h-10 px-3.5 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-r-md border-t border-b border-r border-rose-300 transition-colors focus:z-10 focus:ring-1 focus:ring-rose-500">
                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3.5 font-bold tracking-wider text-center w-12 text-gray-600">No</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider">Waktu Datang</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider">Pencatat</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-3.5 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($data as $late)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-4 py-4 whitespace-nowrap text-center font-mono text-xs font-semibold text-gray-500">
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $late->siswa?->nama_lengkap ?? 'Siswa Tidak Ditemukan' }}</div>
                                        <div class="text-xs text-gray-500">NIS: {{ $late->siswa?->nis ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $late->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ $late->waktu_dicatat_security ? $late->waktu_dicatat_security->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <div class="text-xs">Sec: {{ $late->security?->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400">Piket: {{ $late->guruPiket?->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($late->status) {
                                                'dicatat_security' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'diverifikasi_piket' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'selesai' => 'bg-green-50 text-green-700 border-green-200',
                                                'terlambat' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                                            {{ str_replace('_', ' ', $late->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('monitoring-keterlambatan.show', $late->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada data keterlambatan ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $data->links('vendor.pagination.compact-light') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
