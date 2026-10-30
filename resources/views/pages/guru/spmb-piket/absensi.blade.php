<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Absensi Piket SPMB Pegawai</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="guruAbsensiPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- FLASH NOTIFICATIONS --}}
            @if(session('success'))
                <div class="flex items-center justify-between p-4 text-xs font-medium text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-4 text-xs font-medium text-red-800 bg-red-50 rounded-lg border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            {{-- OFFICER INFORMATION CARD --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wider mb-1">
                                Petugas Piket SPMB
                            </span>
                            <h3 class="text-base font-bold text-slate-900">{{ $activePetugas->nama_petugas ?? $user->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $assignedKelompok ? $assignedKelompok->nama_kelompok . ' (Waktu: ' . ($assignedKelompok->waktu ?: '07.30 - 12.00') . ')' : 'Belum terdaftar di kelompok piket aktif' }}
                            </p>
                        </div>
                    </div>

                    {{-- Next / Today Schedule Badge --}}
                    <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div class="w-9 h-9 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                                {{ $jadwalHariIni ? 'Tugas Piket Hari Ini' : 'Jadwal Tugas Terdekat' }}
                            </p>
                            <p class="text-xs font-bold text-slate-800">
                                @if($jadwalHariIni)
                                    <span class="text-emerald-600 font-black">HARI INI (Sabtu, {{ \Carbon\Carbon::parse($jadwalHariIni->tanggal)->locale('id')->translatedFormat('d M Y') }})</span>
                                @elseif($jadwalBerikutnya)
                                    Sabtu, {{ \Carbon\Carbon::parse($jadwalBerikutnya->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                @else
                                    Tidak ada jadwal mendatang
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- FORM INPUT ABSENSI (7 cols) --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 space-y-5">
                        <div class="border-b border-slate-200 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Form Presensi Kehadiran Piket SPMB
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Isi status kehadiran dan lampirkan foto bukti eviden bertugas di lokasi piket.</p>
                        </div>

                        @if(! $activePetugas)
                            <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800">
                                Akun Anda belum terdaftar dalam daftar petugas piket SPMB. Hubungi Kaur SPMB jika Anda telah ditugaskan.
                            </div>
                        @else
                            <form action="{{ route('guru.spmb-piket.absensi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <input type="hidden" name="spmb_piket_petugas_id" value="{{ $activePetugas->id }}">

                                {{-- Tanggal Piket --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Tanggal Piket Hari Sabtu <span class="text-red-500">*</span>
                                    </label>
                                    <select name="spmb_piket_tanggal_id" required class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                        @if($jadwalHariIni)
                                            <option value="{{ $jadwalHariIni->id }}" selected>
                                                ⭐ HARI INI - Sabtu, {{ \Carbon\Carbon::parse($jadwalHariIni->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                            </option>
                                        @endif
                                        @foreach($tanggalsBelumAbsen as $tgl)
                                            @if(!$jadwalHariIni || $jadwalHariIni->id !== $tgl->id)
                                                <option value="{{ $tgl->id }}">
                                                    Sabtu, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }} ({{ $tgl->waktu ?: '07.30 - 12.00' }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-400 mt-1">Daftar hari Sabtu yang ditugaskan untuk kelompok Anda.</p>
                                </div>

                                {{-- Status Kehadiran --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Status Kehadiran <span class="text-red-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-3 gap-2.5">
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Hadir' ? 'bg-emerald-50 border-emerald-400 text-emerald-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Hadir" x-model="formStatus" class="text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-xs">Hadir</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Izin' ? 'bg-amber-50 border-amber-400 text-amber-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Izin" x-model="formStatus" class="text-amber-600 focus:ring-amber-500">
                                            <span class="text-xs">Izin</span>
                                        </label>
                                        <label class="flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all"
                                            :class="formStatus === 'Sakit' ? 'bg-rose-50 border-rose-400 text-rose-900 font-bold shadow-sm' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                                            <input type="radio" name="status" value="Sakit" x-model="formStatus" class="text-rose-600 focus:ring-rose-500">
                                            <span class="text-xs">Sakit</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Foto Eviden Kehadiran --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Upload Foto Bukti Kehadiran (Eviden)
                                        <span class="text-red-500" x-show="formStatus === 'Hadir'">*</span>
                                    </label>
                                    
                                    <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-slate-300 border-dashed rounded-lg hover:border-red-400 transition-colors bg-slate-50">
                                        <div class="space-y-1 text-center">
                                            <template x-if="!imagePreview">
                                                <svg class="mx-auto h-9 w-9 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </template>

                                            <template x-if="imagePreview">
                                                <div class="relative inline-block mb-2">
                                                    <img :src="imagePreview" class="h-32 w-auto rounded-lg object-contain shadow-sm border border-slate-200">
                                                    <button type="button" @click="clearImage()"
                                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>

                                            <div class="flex text-xs text-slate-600 justify-center">
                                                <label class="relative cursor-pointer bg-white rounded-md font-semibold text-red-600 hover:text-red-500 focus-within:outline-none">
                                                    <span>Ambil / Pilih Foto Eviden</span>
                                                    <input type="file" name="foto_eviden" accept="image/*" capture="environment" @change="previewFile($event)" class="sr-only" x-ref="fileInput">
                                                </label>
                                            </div>
                                            <p class="text-[10px] text-slate-400">
                                                Foto selfie di ruang piket SPMB / foto bukti surat dokter jika sakit (Maks. 5 MB)
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Laporan Kegiatan Piket (Opsional)</label>
                                    <textarea name="keterangan" rows="2" placeholder="Tuliskan catatan aktivitas piket, jumlah pendaftar yang dilayani, atau keterangan izin/sakit..."
                                        class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Kirim Absensi Kehadiran
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- RIWAYAT ABSENSI PETUGAS (5 cols) --}}
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3 border-b border-slate-200 pb-2.5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Riwayat Presensi Anda ({{ $historyAbsensi->count() }})
                            </h3>
                        </div>

                        @if($historyAbsensi->isEmpty())
                            <div class="p-6 text-center text-xs text-slate-400 bg-slate-50 rounded-md border border-dashed border-slate-200">
                                Anda belum memiliki riwayat presensi piket SPMB.
                            </div>
                        @else
                            <div class="space-y-2.5 max-h-[600px] overflow-y-auto pr-1">
                                @foreach($historyAbsensi as $hist)
                                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white hover:border-slate-300 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-800">
                                                Sabtu, {{ $hist->tanggal ? \Carbon\Carbon::parse($hist->tanggal->tanggal)->locale('id')->translatedFormat('d M Y') : '-' }}
                                            </span>
                                            
                                            @if($hist->status === 'Hadir')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Hadir
                                                </span>
                                            @elseif($hist->status === 'Izin')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Izin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Sakit
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                            <span>Waktu Absen: <strong>{{ $hist->formatted_jam_absen }} WIB</strong></span>
                                            
                                            @if($hist->foto_eviden)
                                                <button type="button" @click="openImageModal('{{ $hist->foto_eviden_url }}', 'Sabtu, {{ $hist->tanggal?->formatted_tanggal }}')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Lihat Foto
                                                </button>
                                            @endif
                                        </div>

                                        @if($hist->keterangan)
                                            <p class="mt-1.5 text-[11px] text-slate-600 italic bg-white p-2 rounded border border-slate-200">
                                                "{{ $hist->keterangan }}"
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        {{-- LIGHTBOX MODAL: PREVIEW FOTO EVIDEN --}}
        <template x-teleport="body">
            <div x-show="modalImage.open"
                @click="modalImage.open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div class="relative bg-white rounded-lg p-2 max-w-xl w-full shadow-2xl" @click.stop>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 px-2">
                        <span class="text-xs font-bold text-slate-800" x-text="'Foto Eviden Piket: ' + modalImage.title"></span>
                        <button type="button" @click="modalImage.open = false" class="text-slate-400 hover:text-red-600 text-lg">&times;</button>
                    </div>
                    <div class="p-2 flex items-center justify-center bg-slate-100 rounded mt-2">
                        <img :src="modalImage.url" alt="Foto Eviden" class="max-h-[75vh] w-auto rounded object-contain shadow-sm">
                    </div>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function guruAbsensiPage() {
            return {
                formStatus: 'Hadir',
                imagePreview: null,
                modalImage: {
                    open: false,
                    url: '',
                    title: ''
                },

                previewFile(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.imagePreview = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                clearImage() {
                    this.imagePreview = null;
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                },

                openImageModal(url, title) {
                    this.modalImage.url = url;
                    this.modalImage.title = title;
                    this.modalImage.open = true;
                }
            };
        }
    </script>
</x-app-layout>
