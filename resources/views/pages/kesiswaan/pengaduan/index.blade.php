<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengaduan Orang Tua</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('kesiswaan.pengaduan.index') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">
                            
                            {{-- Search --}}
                            <div class="w-full lg:w-1/3">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari Pengaduan</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                        placeholder="Cari pelapor, siswa, atau isi...">
                                </div>
                            </div>

                            {{-- Status Filter --}}
                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
                                <select name="status" class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>

                            {{-- Filter Buttons --}}
                            <div class="w-full lg:w-auto flex gap-2">
                                <button type="submit"
                                    class="h-10 px-4 bg-red-600 hover:bg-red-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('kesiswaan.pengaduan.index') }}"
                                    class="h-10 px-4 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tgl / Pelapor</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa / Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kategori</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Isi Pengaduan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pengaduans as $item)
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900 font-semibold">{{ $item->created_at->isoFormat('D MMM Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->nama_pelapor }} ({{ $item->hubungan }})</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900">{{ $item->nama_siswa }}</div>
                                        <div class="text-xs text-gray-500">Kelas: {{ $item->kelas_siswa }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-600 truncate w-48">{{ $item->isi_pengaduan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($item->status) {
                                                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'selesai' => 'bg-green-50 text-green-700 border-green-200',
                                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button 
                                            onclick="openDetailModal({{ json_encode($item) }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail / Tindak Lanjut
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada data pengaduan found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $pengaduans->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-8">
                    <div class="flex items-start justify-between absolute top-4 right-4">
                        <button onclick="closeModal()" class="text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-3 text-left">
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                            Detail Pengaduan Orang Tua
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pelapor</p>
                                    <p id="modal-nama-pelapor" class="text-sm text-gray-900 font-medium"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor WA</p>
                                    <p id="modal-nomor-wa" class="text-sm text-gray-900 font-medium"></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Anak / Kelas</p>
                                    <p id="modal-siswa-kelas" class="text-sm text-gray-900 font-medium"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</p>
                                    <p id="modal-kategori" class="text-sm text-gray-900 font-medium"></p>
                                </div>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Isi Pengaduan</p>
                                <p id="modal-isi-pengaduan" class="text-sm text-gray-700 leading-relaxed italic"></p>
                            </div>

                            <hr class="border-gray-100">

                            {{-- Form Tindak Lanjut --}}
                            <form id="status-form" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-4">
                                    <div>
                                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">Update Status</label>
                                        <select name="status" id="modal-status-select" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500">
                                            <option value="pending">Pending</option>
                                            <option value="diproses">Diproses</option>
                                            <option value="selesai">Selesai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="catatan_petugas" class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tindak Lanjut</label>
                                        <textarea name="catatan_petugas" id="modal-catatan" rows="3" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Masukkan catatan penanganan pelapor..."></textarea>
                                    </div>
                                    <div class="pt-2">
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl shadow transition-all transform active:scale-95">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openDetailModal(item) {
            document.getElementById('modal-nama-pelapor').textContent = item.nama_pelapor + ' (' + item.hubungan + ')';
            document.getElementById('modal-nomor-wa').textContent = item.nomor_wa;
            document.getElementById('modal-siswa-kelas').textContent = item.nama_siswa + ' / ' + item.kelas_siswa;
            document.getElementById('modal-kategori').textContent = item.kategori;
            document.getElementById('modal-isi-pengaduan').textContent = '"' + item.isi_pengaduan + '"';
            document.getElementById('modal-status-select').value = item.status;
            document.getElementById('modal-catatan').value = item.catatan_petugas || '';
            
            // Set action URL
            document.getElementById('status-form').action = `/kesiswaan/pengaduan/${item.id}/status`;
            
            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
