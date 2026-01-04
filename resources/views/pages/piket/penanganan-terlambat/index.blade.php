<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Penanganan Siswa Terlambat</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="penangananTerlambat()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2">
                    <div
                        class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden h-full flex flex-col">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-700 mb-2">1. Cari Siswa</h3>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSiswa()"
                                    class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm h-11"
                                    placeholder="Ketik Nama atau NIS siswa..." autofocus>
                            </div>
                        </div>

                        <div class="flex-1 p-6 overflow-y-auto max-h-[500px]">
                            <div x-show="isLoading" class="flex justify-center items-center py-8">
                                <svg class="animate-spin h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>

                            <div x-show="!isLoading && results.length === 0 && searchQuery.length > 2"
                                class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Siswa tidak ditemukan.
                            </div>

                            <div x-show="!isLoading && results.length === 0 && searchQuery.length <= 2"
                                class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Ketik minimal 3 huruf untuk mencari.
                            </div>

                            <div x-show="!isLoading && results.length > 0"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <template x-for="siswa in results" :key="siswa.id">
                                    <div @click="selectSiswa(siswa)"
                                        class="cursor-pointer border rounded-xl p-4 hover:border-red-300 hover:bg-red-50 transition-all group relative"
                                        :class="{
                                            'border-red-500 bg-red-50 ring-1 ring-red-500': selectedSiswa?.id === siswa
                                                .id,
                                            'border-gray-200': selectedSiswa?.id !== siswa.id
                                        }">

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm flex-shrink-0 group-hover:bg-white group-hover:text-red-500 transition-colors">
                                                <span x-text="siswa.nama_lengkap.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-red-700"
                                                    x-text="siswa.nama_lengkap"></h4>
                                                <p class="text-xs text-gray-500 mt-0.5">NIS: <span
                                                        x-text="siswa.nis"></span></p>
                                                <p class="text-xs text-gray-500">Kelas: <span
                                                        x-text="siswa.kelas || 'Belum ada'"></span></p>
                                            </div>
                                        </div>

                                        <div x-show="selectedSiswa?.id === siswa.id"
                                            class="absolute top-3 right-3 text-red-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Lateness List --}}
                    <div class="mt-6 bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700">Terlambat Hari Ini</h3>
                            <span class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs font-black">{{ $terlambatHariIni->count() }} Siswa</span>
                        </div>
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3 text-center">Waktu</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($terlambatHariIni as $late)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 leading-none mb-1">{{ $late->siswa->user->name }}</div>
                                                <div class="text-[10px] text-gray-400 font-medium">{{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }} • {{ $late->siswa->nis }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded font-black text-[10px] border border-gray-200">
                                                    {{ $late->waktu_dicatat_security->format('H:i') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('piket.penanganan-terlambat.print', $late->id) }}" target="_blank" class="inline-flex items-center p-1.5 bg-white text-gray-400 hover:text-red-600 rounded-lg border border-gray-200 hover:border-red-200 hover:bg-red-50 transition-all" title="Cetak Ulang Slip">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">
                                                Belum ada data keterlambatan hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden sticky top-24">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-700">2. Proses Keterlambatan</h3>
                        </div>

                        <div class="p-6">
                            <form action="{{ route('piket.penanganan-terlambat.store') }}" method="POST"
                                x-show="selectedSiswa">
                                @csrf
                                <input type="hidden" name="master_siswa_id" :value="selectedSiswa?.id">

                                <div class="bg-red-50 border border-red-100 rounded-lg p-4 mb-6">
                                    <p class="text-xs font-bold text-red-500 uppercase tracking-wider mb-1">Siswa
                                        Terpilih</p>
                                    <p class="text-lg font-bold text-gray-800" x-text="selectedSiswa?.nama_lengkap"></p>
                                    <p class="text-sm text-gray-600"><span x-text="selectedSiswa?.nis"></span> • <span
                                            x-text="selectedSiswa?.kelas"></span></p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Alasan
                                            Terlambat</label>
                                        <textarea name="alasan" rows="3" required
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                            placeholder="Contoh: Ban bocor, bangun kesiangan..."></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tindak Lanjut <span
                                                class="font-normal text-gray-400">(Opsional)</span></label>
                                        <textarea name="tindak_lanjut" rows="2"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                            placeholder="Contoh: Diberi nasehat, lari keliling lapangan..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-red-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none shadow-lg hover:shadow-red-500/30 transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Simpan & Cetak
                                    </button>
                                    <button type="button" @click="resetSelection()"
                                        class="mt-3 w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                                </div>
                            </form>

                            <div x-show="!selectedSiswa" class="text-center py-12 text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                </svg>
                                <p class="font-medium">Pilih siswa di sebelah kiri untuk mulai memproses.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function penangananTerlambat() {
                return {
                    searchQuery: '{{ request('search') }}',
                    isLoading: false,
                    results: [],
                    selectedSiswa: null,

                    init() {
                        // Jika ada parameter search dari redirect, otomatis cari
                        if (this.searchQuery) {
                            this.searchSiswa();
                        }
                    },

                    async searchSiswa() {
                        if (this.searchQuery.length < 3) {
                            this.results = [];
                            return;
                        }

                        this.isLoading = true;
                        this.selectedSiswa = null; // Reset selection saat searching baru

                        try {
                            // Kita panggil endpoint API internal (atau route biasa yang return JSON)
                            // Karena di controller belum ada return JSON, kita simulasikan logic search manual
                            // IDEALNYA: Buat route API khusus. TAPI, kita pakai trik reload partial via fetch ke halaman ini

                            // Solusi Cepat Tanpa Ubah Controller Menjadi API:
                            // Kita pakai fetch ke URL yang sama dengan parameter ?search=... & format=json
                            // TAPI: Karena controller return VIEW, kita harus ubah sedikit controller-nya
                            // ATAU: Kita gunakan route pencarian khusus.

                            // SEMENTARA: Kita gunakan window.location untuk reload page (Classic Way)
                            // Agar tidak ribet ubah controller jadi API.
                            // window.location.href = "{{ route('piket.penanganan-terlambat.index') }}?search=" + this.searchQuery;

                            // TAPI: Requestmu minta Modern & AJAX. Jadi mari kita buat endpoint search simpel di script ini
                            // Asumsi: Kita buat route baru di web.php atau modif controller.

                            const response = await fetch(`{{ route('api.siswa.search') }}?query=${this.searchQuery}`);
                            const data = await response.json();
                            this.results = data;

                        } catch (error) {
                            console.error('Error fetching data:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectSiswa(siswa) {
                        this.selectedSiswa = siswa;
                        // Scroll ke form di mobile
                        if (window.innerWidth < 1024) {
                            setTimeout(() => {
                                window.scrollTo({
                                    top: document.body.scrollHeight,
                                    behavior: 'smooth'
                                });
                            }, 100);
                        }
                    },

                    resetSelection() {
                        this.selectedSiswa = null;
                        this.searchQuery = '';
                        this.results = [];
                    }
                }
            }
        </script>
        @if (session('print_url'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Buka tab baru otomatis untuk print surat
                    window.open("{{ session('print_url') }}", '_blank');
                });
            </script>
        @endif
    @endpush
</x-app-layout>
