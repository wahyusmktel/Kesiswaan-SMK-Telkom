<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Input Izin Meninggalkan Kelas</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Form Izin Siswa (Input Piket)</h3>
                    <p class="text-xs text-gray-500 mt-1">Gunakan form ini jika siswa berhalangan melakukan input mandiri.</p>
                </div>

                <form action="{{ route('piket.persetujuan-izin-keluar.store') }}" method="POST" class="p-6 space-y-5" x-data="piketIzinForm()">
                    @csrf
                    
                    {{-- Student Search --}}
                    <div>
                        <label for="siswa-search" class="block text-sm font-bold text-gray-700 mb-2">Cari Nama Siswa / NIS</label>
                        <select id="siswa-search" name="master_siswa_id" placeholder="Ketik nama atau NIS..." required></select>
                        <x-input-error :messages="$errors->get('master_siswa_id')" class="mt-2" />
                    </div>

                    {{-- Pelajaran Saat Ini (Dynamic) --}}
                    <div x-show="currentSchedule" x-transition class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Pelajaran Saat Ini</p>
                                <h4 class="text-gray-900 font-extrabold" x-text="currentSchedule.mapel"></h4>
                                <p class="text-xs text-gray-500" x-text="`Guru: ${currentSchedule.guru} | Jam Ke-${currentSchedule.jam_ke} (${currentSchedule.waktu})`"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Jenis Izin --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Izin</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'keluar_sekolah' ? 'ring-2 ring-red-500 border-red-500 bg-red-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="keluar_sekolah" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-bold text-gray-900">Keluar Sekolah</p>
                                            <p class="text-gray-500 text-xs">Meninggalkan lingkungan sekolah</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'keluar_sekolah'" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-3 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'dalam_lingkungan' ? 'ring-2 ring-red-500 border-red-500 bg-red-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="dalam_lingkungan" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <div class="text-sm">
                                            <p class="font-bold text-gray-900">Dalam Lingkungan</p>
                                            <p class="text-gray-500 text-xs">Izin masuk UKS, perpus, dll</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'dalam_lingkungan'" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_izin')" class="mt-2" />
                    </div>

                    {{-- Purpose --}}
                    <div>
                        <label for="tujuan" class="block text-sm font-bold text-gray-700 mb-2">Tujuan / Keperluan</label>
                        <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}" 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            placeholder="Contoh: Fotocopy, Ke ATM, Keperluan Keluarga..." required>
                        <x-input-error :messages="$errors->get('tujuan')" class="mt-2" />
                    </div>

                    {{-- Details --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-2">Keterangan Tambahan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                            placeholder="Berikan alasan mendetail jika diperlukan...">{{ old('keterangan') }}</textarea>
                        <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                    </div>

                    {{-- Estimated Return --}}
                    <div class="w-full sm:w-1/3">
                        <label for="estimasi_kembali" class="block text-sm font-bold text-gray-700 mb-2">Estimasi Kembali</label>
                        <input type="time" name="estimasi_kembali" id="estimasi_kembali" value="{{ old('estimasi_kembali', now()->addMinutes(30)->format('H:i')) }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
                        <p class="text-[10px] text-gray-500 mt-1 italic">* Format 24 jam</p>
                        <x-input-error :messages="$errors->get('estimasi_kembali')" class="mt-2" />
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('piket.persetujuan-izin-keluar.index') }}" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" 
                            class="px-6 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-colors shadow-md">
                            Simpan & Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control { border-radius: 0.5rem; padding: 0.5rem 0.75rem; border-color: #d1d5db; }
            .ts-control.focus { border-color: #ef4444; box-shadow: 0 0 0 1px #ef4444; }
            .ts-dropdown { border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); margin-top: 5px; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            function piketIzinForm() {
                return {
                    jenisIzin: 'keluar_sekolah',
                    currentSchedule: null,
                    async fetchSchedule(siswaId) {
                        if (!siswaId) {
                            this.currentSchedule = null;
                            return;
                        }
                        try {
                            const response = await fetch(`{{ url('piket/api/siswa') }}/${siswaId}/schedule`);
                            const data = await response.json();
                            if (response.ok) {
                                this.currentSchedule = data;
                            } else {
                                this.currentSchedule = null;
                            }
                        } catch (e) {
                            console.error('Failed to fetch schedule', e);
                            this.currentSchedule = null;
                        }
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const ts = new TomSelect('#siswa-search', {
                    valueField: 'id',
                    labelField: 'nama_lengkap',
                    searchField: ['nama_lengkap', 'nis'],
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        var url = '{{ route('api.siswa.search') }}?query=' + encodeURIComponent(query);
                        fetch(url)
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            }).catch(() => {
                                callback();
                            });
                    },
                    render: {
                        option: function(item, escape) {
                            return `<div class="py-2 px-3 border-b border-gray-50">
                                <div class="font-bold text-gray-800">${escape(item.nama_lengkap)}</div>
                                <div class="text-xs text-gray-500">NIS: ${escape(item.nis)} | Kelas: ${escape(item.kelas)}</div>
                            </div>`;
                        },
                        item: function(item, escape) {
                            return `<div class="font-medium text-gray-900">${escape(item.nama_lengkap)} (NIS: ${escape(item.nis)})</div>`;
                        }
                    },
                    placeholder: 'Ketik Nama atau NIS Siswa...',
                    maxItems: 1,
                });

                ts.on('change', function(value) {
                    const alpineData = document.querySelector('[x-data="piketIzinForm()"]').__x.$data;
                    alpineData.fetchSchedule(value);
                });
            });
        </script>
    @endpush
</x-app-layout>
