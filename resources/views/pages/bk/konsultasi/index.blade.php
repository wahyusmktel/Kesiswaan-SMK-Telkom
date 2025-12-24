<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Konsultasi Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="{ openModal: false, selectedJadwal: null }">
                    <h3 class="text-lg font-bold mb-4">Request Konsultasi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perihal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rencana Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    @role('Guru BK')
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($jadwals as $j)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $j->siswa->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">{{ $j->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $j->perihal }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($j->tanggal_rencana)->translatedFormat('d M Y') }}<br>
                                        {{ date('H:i', strtotime($j->jam_rencana)) }} WIB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $j->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $j->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $j->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $j->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                        ">
                                            {{ ucfirst($j->status) }}
                                        </span>
                                    </td>
                                    @role('Guru BK')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        @if($j->status == 'pending')
                                            <button @click="selectedJadwal = {{ $j }}; openModal = true" class="text-blue-600 hover:text-blue-900">Approve</button>
                                            <form action="{{ route('bk.konsultasi.update-status', $j->id) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        @elseif($j->status == 'approved')
                                            <button @click="selectedJadwal = {{ $j }}; openModal = true" class="text-green-600 hover:text-green-900 font-bold underline">Selesaikan</button>
                                            <a href="{{ route('bk.konsultasi.print-jadwal', $j->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 flex items-center gap-1 mt-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                                                Cetak Jadwal
                                            </a>
                                        @elseif($j->status == 'completed')
                                            <a href="{{ route('bk.konsultasi.print-jadwal', $j->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5m1.5 0H13m-4 4h1.5m1.5 0H13m-4 4h1.5m1.5 0H13"/></svg>
                                                Cetak Jadwal
                                            </a>
                                            <a href="{{ route('bk.konsultasi.print-report', $j->id) }}" target="_blank" class="text-red-600 hover:text-red-900 font-bold flex items-center gap-1 mt-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Berita Acara
                                            </a>
                                        @endif
                                    </td>
                                    @endrole
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada permintaan konsultasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $jadwals->links() }}
                    </div>

                    <!-- Modal Update Status -->
                    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div @click="openModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                            <div class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form :action="'{{ route('bk.konsultasi.update-status', 999) }}'.replace('999', selectedJadwal ? selectedJadwal.id : '')" method="POST">
                                    @csrf @method('PATCH')
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                            Update Konsultasi: <span x-text="selectedJadwal?.siswa.nama_lengkap"></span>
                                        </h3>
                                        <div class="mt-4 space-y-4">
                                            <div x-show="selectedJadwal?.status == 'pending'">
                                                <label class="block text-sm font-medium text-gray-700">Tentukan Tempat Pertemuan</label>
                                                <input type="text" name="tempat" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Contoh: Ruang BK, Perpustakaan, dsb.">
                                                <input type="hidden" name="status" value="approved">
                                            </div>
                                            <div x-show="selectedJadwal?.status == 'approved'">
                                                <label class="block text-sm font-medium text-gray-700">Hasil Konsultasi (Internal BK)</label>
                                                <textarea name="catatan_bk" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Tuliskan hasil atau tindak lanjut bimbingan..."></textarea>
                                                <input type="hidden" name="status" value="completed">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Update
                                        </button>
                                        <button @click="openModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
