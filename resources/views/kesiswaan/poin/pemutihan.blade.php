<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pemutihan Poin Pelanggaran') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        siswaSearch: '', 
        selectedSiswa: null, 
        selectedSiswaName: '',
        siswas: [],
        searchSiswa() {
            if (this.siswaSearch.length < 3) return;
            fetch(`{{ route('api.siswa.search') }}?query=${this.siswaSearch}`)
                .then(res => res.json())
                .then(data => {
                    this.siswas = data;
                });
        },
        selectSiswa(siswa) {
            this.selectedSiswa = siswa.id;
            this.selectedSiswaName = `${siswa.nis} - ${siswa.nama_lengkap}`;
            this.siswas = [];
            this.siswaSearch = '';
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 tracking-tight">Riwayat Pemutihan Poin</h3>
                        <p class="text-sm text-gray-500">Pencatatan pengurangan poin pelanggaran siswa karena aksi positif atau program sekolah</p>
                    </div>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-expungement')" 
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all text-sm font-bold shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Lakukan Pemutihan
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 text-center">Poin Dikurangi (-)</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pemutihans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $p->siswa->nama_lengkap }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium">NIS: {{ $p->siswa->nis }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 font-medium text-xs">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-50 text-blue-600 font-bold text-xs ring-1 ring-blue-100">
                                        -{{ $p->poin_dikurangi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-600">{{ $p->keterangan }}</div>
                                    @if($p->pengaju)
                                        <div class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-tighter">Diajukan oleh: {{ $p->pengaju->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($p->status == 'diajukan')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-amber-100 uppercase tracking-tighter">Diajukan</span>
                                    @elseif($p->status == 'disetujui')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-600 ring-1 ring-green-100 uppercase tracking-tighter">Disetujui</span>
                                        @if($p->penyetuju)
                                            <div class="text-[9px] text-gray-400 mt-0.5 font-bold uppercase tracking-tighter">Oleh: {{ $p->penyetuju->name }}</div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600 ring-1 ring-red-100 uppercase tracking-tighter">Ditolak</span>
                                        @if($p->penyetuju)
                                            <div class="text-[9px] text-gray-400 mt-0.5 font-bold uppercase tracking-tighter">Oleh: {{ $p->penyetuju->name }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-1">
                                        @if($p->status == 'diajukan' && auth()->user()->hasRole('Waka Kesiswaan'))
                                            <form action="{{ route('kesiswaan.input-pemutihan.approve', $p->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Setujui" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                            <form action="{{ route('kesiswaan.input-pemutihan.reject', $p->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" title="Tolak" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('kesiswaan.input-pemutihan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pemutihan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data pemutihan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $pemutihans->links() }}
                </div>
            </div>
        </div>

        {{-- Form Modal --}}
        <x-modal name="add-expungement" focusable>
            <form method="post" action="{{ route('kesiswaan.input-pemutihan.store') }}" class="p-8">
                @csrf
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight text-blue-600">Lakukan Pemutihan Poin</h2>
                    <p class="text-sm text-gray-500 mt-1">Gunakan form ini untuk mengurangi poin pelanggaran siswa.</p>
                </div>

                <div class="space-y-6">
                    {{-- Student Search --}}
                    <div class="relative">
                        <x-input-label for="siswa_search" value="Cari Siswa" />
                        <div x-show="!selectedSiswa" class="mt-1">
                            <input type="text" x-model="siswaSearch" @input.debounce.300ms="searchSiswa()" 
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm pr-10 pl-4 py-2.5 text-sm" 
                                placeholder="Ketik nama atau NIS (min 3 karakter)...">
                        </div>

                        <div x-show="siswas.length > 0" class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden max-h-60 overflow-y-auto">
                            <template x-for="siswa in siswas" :key="siswa.id">
                                <button type="button" @click="selectSiswa(siswa)" 
                                    class="w-full text-left px-4 py-3 hover:bg-blue-50 transition-colors border-b border-gray-50 last:border-0">
                                    <div class="font-bold text-gray-800 text-sm" x-text="siswa.nama_lengkap"></div>
                                    <div class="text-[10px] text-gray-500" x-text="'NIS: ' + siswa.nis"></div>
                                </button>
                            </template>
                        </div>

                        <div x-show="selectedSiswa" class="mt-1 p-4 bg-blue-50 border border-blue-100 rounded-xl flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest leading-none mb-1">SISWA TERPILIH</p>
                                <span class="text-gray-800 font-bold text-sm" x-text="selectedSiswaName"></span>
                            </div>
                            <button type="button" @click="selectedSiswa = null; selectedSiswaName = ''" class="text-blue-500 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <input type="hidden" name="master_siswa_id" :value="selectedSiswa" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="tanggal" value="Tanggal Pemutihan" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full rounded-xl" required value="{{ date('Y-m-d') }}" />
                        </div>
                        <div>
                            <x-input-label for="poin_dikurangi" value="Poin Dikurangi (-)" />
                            <x-text-input id="poin_dikurangi" name="poin_dikurangi" type="number" class="mt-1 block w-full rounded-xl" required min="1" value="10" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="keterangan" value="Keterangan / Alasan Pemutihan" />
                        <textarea id="keterangan" name="keterangan" class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm text-sm" placeholder="Contoh: Program pembersihan masjid sekolah..."></textarea>
                    </div>
                </div>

                <div class="mt-10 flex flex-col md:flex-row justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="justify-center py-2.5 px-6 rounded-xl border-2">Batal</x-secondary-button>
                    <x-primary-button class="justify-center py-2.5 px-8 rounded-xl bg-blue-600 hover:bg-blue-700 font-bold shadow-lg shadow-blue-100">Simpan Pemutihan</x-primary-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
