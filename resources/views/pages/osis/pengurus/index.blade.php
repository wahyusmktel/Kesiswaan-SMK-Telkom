<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Daftar Pengurus OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola data pengurus harian (BPH) dan koordinator seksi bidang (Sekbid)</p>
            </div>
            <a href="{{ route('osis.pengurus.create') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pengurus</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Filter & Search -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <form action="{{ route('osis.pengurus.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau jabatan..."
                           class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20">

                    <select name="sekbid" class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-800">
                        <option value="">Semua Sekbid / Divisi</option>
                        @foreach($sekbids as $s)
                            <option value="{{ $s }}" {{ request('sekbid') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'sekbid']))
                        <a href="{{ route('osis.pengurus.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Table Pengurus -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Pengurus</th>
                                <th class="py-4 px-6">Jabatan</th>
                                <th class="py-4 px-6">Sekbid / Bidang</th>
                                <th class="py-4 px-6">Kelas</th>
                                <th class="py-4 px-6">Urutan</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($penguruses as $p)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 shrink-0">
                                            <div>
                                                <div class="font-bold text-gray-800">{{ $p->nama }}</div>
                                                @if($p->masterSiswa)
                                                    <span class="text-[10px] text-gray-400 font-mono">NIS: {{ $p->masterSiswa->nis }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-gray-700">{{ $p->jabatan }}</td>
                                    <td class="py-4 px-6 text-xs text-gray-600">{{ $p->sekbid ?: '-' }}</td>
                                    <td class="py-4 px-6 text-xs font-mono text-gray-600">{{ $p->kelas ?: '-' }}</td>
                                    <td class="py-4 px-6 text-xs font-mono font-bold text-gray-500">{{ $p->urutan }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2">
                                        <a href="{{ route('osis.pengurus.edit', $p) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('osis.pengurus.destroy', $p) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus pengurus ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada data pengurus OSIS.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($penguruses->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $penguruses->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
