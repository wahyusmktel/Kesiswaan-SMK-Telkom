<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pencatatan Pelanggaran Siswa') }}
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
                        <h3 class="text-lg font-bold text-gray-800 tracking-tight">Riwayat Pelanggaran</h3>
                        <p class="text-sm text-gray-500">Daftar pencatatan pelanggaran siswa terbaru</p>
                    </div>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-violation')" 
                        class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all text-sm font-bold shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Catat Pelanggaran
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100 shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Siswa</th>
                                <th class="px-6 py-4">Kategori & Aturan</th>
                                <th class="px-6 py-4 text-center">Poin</th>
                                <th class="px-6 py-4">Tanggal & Pelapor</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($pelanggarans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $p->siswa->nama_lengkap }}</div>
                                    <div class="text-[10px] text-gray-500 font-medium">NIS: {{ $p->siswa->nis }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="inline-flex px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold mb-1">
                                        {{ $p->peraturan->category->name }}
                                    </div>
                                    <div class="text-gray-700 font-semibold text-xs truncate max-w-xs">{{ $p->peraturan->pasal }} - {{ $p->peraturan->deskripsi }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-600 font-bold text-xs ring-1 ring-red-100">
                                        {{ $p->peraturan->bobot_poin }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 font-medium text-xs">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d F Y') }}</div>
                                    <div class="text-[10px] text-gray-400 capitalize">Oleh: {{ $p->pelapor->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('kesiswaan.input-pelanggaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus catatan pelanggaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data pelanggaran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $pelanggarans->links() }}
                </div>
            </div>
        </div>

        {{-- Form Modal --}}
        <x-modal name="add-violation" focusable>
            <form method="post" action="{{ route('kesiswaan.input-pelanggaran.store') }}" class="p-8">
                @csrf
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Catat Pelanggaran Siswa</h2>
                    <p class="text-sm text-gray-500 mt-1">Masukkan data pelanggaran yang dilakukan siswa sesuai tata tertib.</p>
                </div>

                <div class="space-y-6">
                    {{-- Student Search --}}
                    <div class="relative">
                        <x-input-label for="siswa_search" value="Cari Siswa" />
                        <div x-show="!selectedSiswa" class="mt-1">
                            <input type="text" x-model="siswaSearch" @input.debounce.300ms="searchSiswa()" 
                                class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm pr-10 pl-4 py-2.5 text-sm" 
                                placeholder="Ketik nama atau NIS (min 3 karakter)...">
                            <div class="absolute right-3 top-[38px] text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        <div x-show="siswas.length > 0" class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden max-h-60 overflow-y-auto">
                            <template x-for="siswa in siswas" :key="siswa.id">
                                <button type="button" @click="selectSiswa(siswa)" 
                                    class="w-full text-left px-4 py-3 hover:bg-red-50 transition-colors border-b border-gray-50 last:border-0">
                                    <div class="font-bold text-gray-800 text-sm" x-text="siswa.nama_lengkap"></div>
                                    <div class="text-[10px] text-gray-500" x-text="'NIS: ' + siswa.nis"></div>
                                </button>
                            </template>
                        </div>

                        <div x-show="selectedSiswa" class="mt-1 p-4 bg-red-50 border border-red-100 rounded-xl flex justify-between items-center animate-in fade-in zoom-in duration-200">
                            <div>
                                <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest leading-none mb-1">SISWA TERPILIH</p>
                                <span class="text-gray-800 font-bold text-sm" x-text="selectedSiswaName"></span>
                            </div>
                            <button type="button" @click="selectedSiswa = null; selectedSiswaName = ''" class="text-red-500 hover:text-red-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <input type="hidden" name="master_siswa_id" :value="selectedSiswa" required>
                    </div>

                    <div>
                        <x-input-label for="poin_peraturan_id" value="Jenis Pelanggaran" />
                        <select id="poin_peraturan_id" name="poin_peraturan_id" class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm text-sm p-2.5" required>
                            <option value="">-- Pilih Aturan Pelanggaran --</option>
                            @foreach($peraturans as $reg)
                                <option value="{{ $reg->id }}">[{{ $reg->bobot_poin }} Pkt] {{ $reg->pasal }} - {{ $reg->deskripsi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="tanggal" value="Tanggal Kejadian" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full rounded-xl" required value="{{ date('Y-m-d') }}" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="catatan" value="Catatan Tambahan (Opsional)" />
                        <textarea id="catatan" name="catatan" class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm text-sm" placeholder="Contoh: Kejadian di kantin setelah istirahat..."></textarea>
                    </div>
                </div>

                <div class="mt-10 flex flex-col md:flex-row justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="justify-center py-2.5 px-6 rounded-xl border-2">Batal</x-secondary-button>
                    <x-danger-button class="justify-center py-2.5 px-8 rounded-xl bg-red-600 hover:bg-red-700 font-bold shadow-lg shadow-red-100">Simpan Pelanggaran</x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
