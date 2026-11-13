<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Agenda &amp; Kegiatan OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola kalender event, program kerja, dan pengumuman kegiatan siswa</p>
            </div>
            <a href="{{ route('osis.agenda.create') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Agenda</span>
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

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Agenda</th>
                                <th class="py-4 px-6">Kategori</th>
                                <th class="py-4 px-6">Tanggal Pelaksanaan</th>
                                <th class="py-4 px-6">Lokasi</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($agendas as $ag)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-800">{{ $ag->judul }}</div>
                                        @if($ag->deskripsi)
                                            <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $ag->deskripsi }}</p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                            {{ $ag->kategori }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-xs text-gray-700 font-medium">
                                        {{ $ag->tanggal_mulai->format('d M Y') }}
                                        @if($ag->tanggal_selesai && $ag->tanggal_selesai != $ag->tanggal_mulai)
                                            <br><span class="text-gray-400">s/d {{ $ag->tanggal_selesai->format('d M Y') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-gray-600">{{ $ag->lokasi ?: '-' }}</td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                            {{ $ag->status === 'berlangsung' ? 'bg-emerald-100 text-emerald-700' : ($ag->status === 'selesai' ? 'bg-gray-100 text-gray-600' : 'bg-blue-100 text-blue-700') }}">
                                            {{ $ag->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right space-x-2">
                                        <a href="{{ route('osis.agenda.edit', $ag) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-colors">
                                            Edit
                                        </a>
                                        <form action="{{ route('osis.agenda.destroy', $ag) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus agenda ini?')">
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
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada agenda OSIS.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($agendas->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $agendas->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
