<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Rekapitulasi Izin Guru') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Filter Card --}}
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Filter Data</h3>
                        <p class="text-xs text-gray-500">Tentukan rentang laporan yang Anda inginkan.</p>
                    </div>
                </div>

                <form action="{{ route('sdm.rekapitulasi.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest">Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest">Guru</label>
                        <select name="guru_id" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Guru</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest">Kategori</label>
                        <select name="kategori" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            <option value="sekolah" {{ request('kategori') == 'sekolah' ? 'selected' : '' }}>Lingkungan Sekolah</option>
                            <option value="luar" {{ request('kategori') == 'luar' ? 'selected' : '' }}>Luar Sekolah / Absen</option>
                            <option value="terlambat" {{ request('kategori') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-2xl font-black shadow-md hover:bg-indigo-500 transition-all active:scale-95">Filter</button>
                        <a href="{{ route('sdm.rekapitulasi.index') }}" class="p-3 bg-gray-100 text-gray-600 rounded-2xl hover:bg-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table & Export --}}
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/50">
                    <div>
                        <h4 class="font-black text-gray-900">Hasil Rekapitulasi</h4>
                        <p class="text-xs text-gray-500">Menampilkan {{ $izins->total() }} data yang sesuai filter.</p>
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <a href="{{ route('sdm.rekapitulasi.export-excel', request()->all()) }}" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-green-600 text-white rounded-xl font-bold text-xs gap-2 hover:bg-green-700 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </a>
                        <a href="{{ route('sdm.rekapitulasi.export-pdf', request()->all()) }}" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-xl font-bold text-xs gap-2 hover:bg-red-700 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Export PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Guru</th>
                                <th class="px-6 py-4">Waktu Izin</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($izins as $izin)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-black text-gray-900">{{ $izin->guru->nama_lengkap }}</p>
                                        <p class="text-[10px] text-gray-400">NIP: {{ $izin->guru->nip ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-bold text-gray-700">
                                            @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                                {{ $izin->tanggal_mulai->translatedFormat('d F Y') }}
                                            @else
                                                {{ $izin->tanggal_mulai->translatedFormat('d/m/Y') }} - {{ $izin->tanggal_selesai->translatedFormat('d/m/Y') }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->kategori_penyetujuan === 'terlambat')
                                            <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-100 text-[10px] font-black uppercase">Terlambat</span>
                                        @elseif($izin->kategori_penyetujuan === 'sekolah')
                                            <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-black uppercase">Sekolah</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-100 text-[10px] font-black uppercase">Luar</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] font-black uppercase">{{ $izin->jenis_izin }}</span>
                                    </td>
                                    <td class="px-6 py-4 truncate max-w-xs text-gray-500 text-xs italic">
                                        "{{ $izin->deskripsi }}"
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Belum ada data pengajuan dalam periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($izins->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $izins->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
