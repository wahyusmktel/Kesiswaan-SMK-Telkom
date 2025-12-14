<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Buat Pengajuan Baru</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('dispensasi.pengajuan.store') }}" method="POST">
                @csrf

                <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-8">

                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <span
                                    class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold">1</span>
                                Detail Kegiatan
                            </h3>

                            <div class="space-y-5">
                                <div>
                                    <label for="nama_kegiatan" class="block text-sm font-bold text-gray-700 mb-1">Nama
                                        Kegiatan</label>
                                    <input type="text" id="nama_kegiatan" name="nama_kegiatan"
                                        value="{{ old('nama_kegiatan') }}" required
                                        placeholder="Contoh: Lomba Futsal Antar Sekolah"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                </div>

                                <div>
                                    <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-1">Deskripsi
                                        / Keterangan</label>
                                    <textarea id="keterangan" name="keterangan" rows="4" required
                                        placeholder="Jelaskan tujuan, lokasi, dan detail lainnya..."
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">{{ old('keterangan') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label for="waktu_mulai"
                                            class="block text-sm font-bold text-gray-700 mb-1">Waktu Mulai</label>
                                        <input type="datetime-local" id="waktu_mulai" name="waktu_mulai"
                                            value="{{ old('waktu_mulai') }}" required
                                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="waktu_selesai"
                                            class="block text-sm font-bold text-gray-700 mb-1">Waktu Selesai</label>
                                        <input type="datetime-local" id="waktu_selesai" name="waktu_selesai"
                                            value="{{ old('waktu_selesai') }}" required
                                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3
                                class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                                <span
                                    class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold">2</span>
                                Peserta Kegiatan
                            </h3>

                            <div>
                                <label for="pilih_siswa" class="block text-sm font-bold text-gray-700 mb-1">Pilih
                                    Siswa</label>
                                <div class="relative">
                                    <select id="pilih_siswa" name="siswa_ids[]" multiple
                                        placeholder="Ketik nama atau NIS siswa..." autocomplete="off">
                                        @foreach ($siswa as $s)
                                            <option value="{{ $s->id }}">
                                                {{ $s->nama_lengkap }}
                                                ({{ $s->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Ketik nama siswa untuk mencari. Bisa memilih banyak sekaligus.
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('dispensasi.pengajuan.index') }}"
                            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-500 hover:shadow-indigo-500/30 transition-all transform hover:-translate-y-0.5">
                            Kirim Pengajuan
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            /* Container Input Utama */
            .ts-control {
                border-radius: 0.75rem !important;
                /* rounded-xl */
                padding: 0.6rem 1rem !important;
                border-color: #d1d5db !important;
                /* border-gray-300 */
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                background-color: #fff !important;
                font-size: 0.875rem !important;
                /* text-sm */
                min-height: 48px;
                /* Agar tidak gepeng */
            }

            /* Saat Fokus (Mirip Input Tailwind Default) */
            .ts-control.focus {
                border-color: #6366f1 !important;
                /* indigo-500 */
                box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
                /* ring-indigo */
                z-index: 10;
            }

            /* Dropdown Menu */
            .ts-dropdown {
                border-radius: 0.75rem !important;
                border: 1px solid #e5e7eb !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                margin-top: 4px !important;
                z-index: 50;
                overflow: hidden !important;
            }

            /* Item di dalam Dropdown */
            .ts-dropdown .option {
                padding: 10px 15px !important;
                font-size: 0.875rem;
            }

            .ts-dropdown .active {
                background-color: #e0e7ff !important;
                /* indigo-50 */
                color: #4338ca !important;
                /* indigo-700 */
                font-weight: 600;
            }

            /* Item yang SUDAH DIPILIH (Chips/Badge) */
            .ts-control .item {
                background-color: #e0e7ff !important;
                /* indigo-50 */
                color: #4338ca !important;
                /* indigo-700 */
                border: 1px solid #c7d2fe !important;
                /* indigo-200 */
                border-radius: 9999px !important;
                /* rounded-full */
                padding: 2px 10px !important;
                padding-right: 25px !important;
                /* Space buat tombol silang */
                font-weight: 500;
                font-size: 0.75rem !important;
                /* text-xs */
                margin: 2px 4px 2px 0 !important;
            }

            /* Tombol Hapus (Silang) di Chips */
            .ts-control .item .remove {
                border-left: none !important;
                color: #6366f1 !important;
                padding-left: 5px !important;
            }

            .ts-control .item .remove:hover {
                background: transparent !important;
                color: #dc2626 !important;
                /* red-600 */
                font-weight: bold;
            }

            /* Placeholder Text */
            .ts-control input::placeholder {
                color: #9ca3af;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            new TomSelect("#pilih_siswa", {
                plugins: {
                    'remove_button': {
                        title: 'Hapus siswa ini',
                    },
                    'caret_position': {},
                    'no_backspace_delete': {}
                },
                maxItems: null,
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                placeholder: 'Ketik nama siswa...',
                render: {
                    // Kustomisasi tampilan opsi di dropdown
                    option: function(data, escape) {
                        // Kita pecah teks "Nama (Kelas)" menjadi dua baris
                        let text = escape(data.text);
                        let splitText = text.split('(');
                        let nama = splitText[0];
                        let kelas = splitText.length > 1 ? '(' + splitText[1] : '';

                        return '<div class="py-2 flex flex-col">' +
                            '<span class="font-bold text-gray-800">' + nama + '</span>' +
                            '<span class="text-xs text-gray-500">' + kelas + '</span>' +
                            '</div>';
                    },
                    // Kustomisasi tampilan item yang sudah dipilih (Chips)
                    item: function(data, escape) {
                        return '<div title="' + escape(data.text) + '">' + escape(data.text) + '</div>';
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
