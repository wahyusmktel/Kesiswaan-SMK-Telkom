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

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <form action="{{ route('bk.monitoring-catatan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Kelas</label>
                        <select name="kelas_id" class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-shadow">
                            <option value="">Semua Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Cari Nama/NIS</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Contoh: Ahmad"
                               class="w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-shadow">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-100">
                             Cari Data
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Siswa</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Kelas</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Poin Pelanggaran</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Total Terlambat</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswas as $siswa)
                            <tr class="hover:bg-gray-50/50 transition-colors">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                    {{ $siswas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
