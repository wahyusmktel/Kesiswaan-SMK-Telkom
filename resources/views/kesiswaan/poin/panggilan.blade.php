<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Panggilan Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        siswaSearch: '', 
        selectedSiswa: null, 
        selectedSiswaName: '',
        siswas: [],
        searchSiswa() {
            if (this.siswaSearch.length < 1) return;
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
        },
        openCallModal(siswa = null) {
            if (siswa) {
                this.selectSiswa(siswa);
            } else {
                this.selectedSiswa = null;
                this.selectedSiswaName = '';
            }
            $dispatch('open-modal', 'add-call');
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Bagian Siswa Butuh Panggilan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-red-100 bg-red-50/10">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Siswa Butuh Panggilan Mendesak (Poin >= 100)
                    </h3>
                    <p class="text-sm text-gray-600">Daftar siswa yang telah mencapai ambang batas poin kritis dan memerlukan tindakan pemanggilan orang tua.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($butuhPanggilan as $siswa)
                    <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div class="w-12 h-12 rounded-xl bg-red-600 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-red-100">
                                    {{ $siswa->getCurrentPoints() }}
                                </div>
                                <span class="px-2 py-1 rounded-md bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-wider">
                                    CRITICAL
                                </span>
                            </div>
                            <h4 class="font-bold text-gray-900 leading-tight">{{ $siswa->nama_lengkap }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }} | NIS: {{ $siswa->nis }}</p>
                        </div>
                        <button @click="openCallModal(@js($siswa))" 
                            class="mt-6 w-full py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition-colors shadow-sm">
                            Buat Surat Panggilan
                        </button>
                    </div>
                    @empty
                    <div class="col-span-full py-10 text-center bg-white rounded-2xl border border-dashed border-gray-200 text-gray-400 italic">
                        Tidak ada siswa dalam kategori sangat kritis saat ini.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Panggilan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Panggilan Orang Tua</h3>
                        <p class="text-sm text-gray-500">Daftar surat panggilan yang telah dibuat dan status kehadirannya.</p>
                    </div>
                    <button @click="openCallModal()" 
                        class="px-5 py-2.5 bg-gray-800 text-white rounded-xl hover:bg-gray-900 transition-all text-sm font-bold shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Panggilan Manual
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100 uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Nomor Surat / Siswa</th>
                                <th class="px-6 py-4">Jadwal Panggilan</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($panggilans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $p->nomor_surat }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->siswa->nama_lengkap }} ({{ $p->siswa->rombels->first()?->kelas->nama_kelas }})</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 font-medium">{{ \Carbon\Carbon::parse($p->tanggal_panggilan)->translatedFormat('d F Y') }}</div>
                                    <div class="text-[10px] text-gray-400">Pukul {{ date('H:i', strtotime($p->jam_panggilan)) }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('kesiswaan.panggilan-ortu.update-status', $p->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" 
                                            class="text-[10px] font-bold py-1 px-2 rounded-lg border-gray-200 {{ $p->status == 'hadir' ? 'bg-green-50 text-green-700' : ($p->status == 'tidak_hadir' ? 'bg-red-50 text-red-700' : 'bg-blue-50 text-blue-700') }}">
                                            <option value="terkirim" {{ $p->status == 'terkirim' ? 'selected' : '' }}>TERKIRIM</option>
                                            <option value="hadir" {{ $p->status == 'hadir' ? 'selected' : '' }}>HADIR</option>
                                            <option value="tidak_hadir" {{ $p->status == 'tidak_hadir' ? 'selected' : '' }}>TIDAK HADIR</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('kesiswaan.panggilan-ortu.print', $p->id) }}" target="_blank" 
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Cetak Surat">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                                        </a>
                                        <form action="{{ route('kesiswaan.panggilan-ortu.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus catatan panggilan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Belum ada riwayat panggilan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $panggilans->links() }}
                </div>
            </div>
        </div>

        {{-- Form Modal --}}
        <x-modal name="add-call" focusable>
            <form method="post" action="{{ route('kesiswaan.panggilan-ortu.store') }}" class="p-8">
                @csrf
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Buat Surat Panggilan</h2>
                    <p class="text-sm text-gray-500 mt-1" x-show="selectedSiswa">Lengkapi detail panggilan untuk <span class="font-bold text-red-600" x-text="selectedSiswaName"></span></p>
                    <p class="text-sm text-gray-500 mt-1" x-show="!selectedSiswa">Cari siswa dan lengkapi detail panggilan.</p>
                </div>

                <div class="space-y-6">
                    {{-- Student Search (Hanya tampil jika belum terpilih) --}}
                    <div x-show="!selectedSiswa" class="relative">
                        <x-input-label for="siswa_search" value="Cari Siswa" />
                        <div class="mt-1">
                            <input type="text" x-model="siswaSearch" @input.debounce.300ms="searchSiswa()" 
                                class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm pr-10 pl-4 py-2.5 text-sm" 
                                placeholder="Ketik nama atau NIS (min 1 karakter)...">
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
                    </div>

                    {{-- Siswa Terpilih --}}
                    <div x-show="selectedSiswa" class="p-4 bg-red-50 border border-red-100 rounded-xl flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-bold text-red-600 uppercase tracking-widest leading-none mb-1">SISWA TERPILIH</p>
                            <span class="text-gray-800 font-bold text-sm" x-text="selectedSiswaName"></span>
                        </div>
                        <button type="button" @click="selectedSiswa = null; selectedSiswaName = ''" class="text-red-500 hover:text-red-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <input type="hidden" name="master_siswa_id" :value="selectedSiswa" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nomor_surat" value="Nomor Surat" />
                            <x-text-input id="nomor_surat" name="nomor_surat" type="text" class="mt-1 block w-full rounded-xl" required placeholder="Contoh: 001/SMK/PK/XII/2025" />
                        </div>
                        <div>
                            <x-input-label for="perihal" value="Perihal" />
                            <x-text-input id="perihal" name="perihal" type="text" class="mt-1 block w-full rounded-xl" required value="Panggilan Orang Tua Ke-1" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="tanggal_panggilan" value="Tanggal Panggilan" />
                            <x-text-input id="tanggal_panggilan" name="tanggal_panggilan" type="date" class="mt-1 block w-full rounded-xl" required min="{{ date('Y-m-d') }}" />
                        </div>
                        <div>
                            <x-input-label for="jam_panggilan" value="Jam Panggilan" />
                            <x-text-input id="jam_panggilan" name="jam_panggilan" type="time" class="mt-1 block w-full rounded-xl" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="tempat_panggilan" value="Tempat Panggilan" />
                        <x-text-input id="tempat_panggilan" name="tempat_panggilan" type="text" class="mt-1 block w-full rounded-xl" required value="Ruang Waka Kesiswaan" />
                    </div>
                </div>

                <div class="mt-10 flex flex-col md:flex-row justify-end gap-3">
                    <x-secondary-button x-on:click="$dispatch('close')" class="justify-center py-2.5 px-6 rounded-xl border-2">Batal</x-secondary-button>
                    <x-danger-button class="justify-center py-2.5 px-8 rounded-xl bg-red-600 hover:bg-red-700 font-bold shadow-lg shadow-red-100 uppercase tracking-widest text-[10px]">Simpan & Generate Surat</x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
