<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Monitoring Pelanggaran & Keterlambatan
                    </h2>
                    <p class="text-gray-500 mt-2">
                        Pantau seluruh catatan kedisiplinan siswa di sekolah.
                    </p>
                </div>
            </div>

            @php
                $selectedKelas = $kelas->firstWhere('id', request('kelas_id'));
                $selectedKelasName = $selectedKelas?->nama_kelas ?? 'Semua Kelas';
            @endphp

            {{-- Filter Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <form action="{{ route('bk.monitoring-catatan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Searchable Select Kelas --}}
                    <div class="relative" x-data="{
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
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Pilih Kelas</label>
                        <input type="hidden" name="kelas_id" :value="selectedId">
                        
                        <button type="button" @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())"
                            class="w-full h-10 px-3 bg-white border border-gray-300 rounded-md shadow-sm text-left text-sm flex items-center justify-between focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <span class="truncate text-gray-800 font-medium text-xs sm:text-sm" x-text="selectedName || 'Semua Kelas'"></span>
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-1 transition-transform" :class="open ? 'transform rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak
                            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-xl py-1 text-sm max-h-64 flex flex-col focus:outline-none">
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

                    {{-- Cari Nama/NIS --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cari Nama/NIS</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Contoh: Ahmad"
                                   class="pl-10 block w-full h-10 rounded-md border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                        </div>
                    </div>

                    {{-- Grouped Action Buttons --}}
                    <div class="flex items-end">
                        <div class="inline-flex rounded-md shadow-sm w-full" role="group">
                            <button type="submit" class="flex-1 h-10 px-4 inline-flex items-center justify-center gap-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-l-md border border-red-600 transition-colors focus:z-10 focus:ring-1 focus:ring-red-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Cari Data
                            </button>
                            <a href="{{ route('bk.monitoring-catatan.index') }}" class="h-10 px-4 inline-flex items-center justify-center gap-1 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 rounded-r-md border-t border-b border-r border-gray-300 transition-colors focus:z-10 focus:ring-1 focus:ring-red-500">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden text-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-4 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center w-12">No</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Siswa</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Kelas</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Poin Pelanggaran</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Total Terlambat</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($siswas as $siswa)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap text-center font-mono text-xs font-semibold text-gray-500">
                                    {{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                            {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                            <div class="text-xs text-gray-500">NIS: {{ $siswa->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $siswa->rombels->first()->kelas->nama_kelas ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $siswa->getCurrentPoints() >= 100 ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $siswa->getCurrentPoints() }} Poin
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        {{ $siswa->keterlambatans->count() }} Kali
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('bk.monitoring-catatan.show', $siswa->id) }}" 
                                       class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-900 font-bold transition-colors">
                                        Detail
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base font-medium">Tidak ada data catatan kedisiplinan siswa ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                    {{ $siswas->links('vendor.pagination.compact-light') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
