<x-app-layout>
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Izin Keluar Kelas</h2>
    </x-slot>

    @php
        $siswaNis = Auth::user()->masterSiswa?->nis;
        $barcodeBase64 = null;
        if ($siswaNis) {
            $barcodeGen = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($siswaNis, $barcodeGen::TYPE_CODE_128, 2, 50));
        }
    @endphp

    <div class="py-6 w-full" x-data="izinKeluarData()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Alert Izin Keluar Sekolah Aktif Hari Ini --}}
            @if (isset($izinKeluarAktif) && $izinKeluarAktif)
                <div class="bg-gradient-to-r from-red-600 via-rose-700 to-slate-900 rounded-3xl p-1 shadow-xl shadow-red-100 animate-in fade-in slide-in-from-top duration-700">
                    <div class="bg-white rounded-[22px] p-6 flex flex-col md:flex-row items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0 animate-bounce">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold mb-2">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                {{ $izinKeluarAktif->status === 'disetujui_guru_piket' ? 'Izin Keluar Disetujui Piket' : 'Sedang di Luar Sekolah' }}
                            </div>
                            <h3 class="text-xl font-black text-gray-900 leading-tight">Surat Izin Keluar Sekolah Digital Siap Digunakan!</h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Keperluan: <strong class="text-gray-800">{{ $izinKeluarAktif->tujuan }}</strong> &bull; 
                                Estimasi Kembali: <strong class="text-red-600">{{ \Carbon\Carbon::parse($izinKeluarAktif->estimasi_kembali)->format('H:i') }} WIB</strong>.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Silakan tunjukkan QR Code ini kepada petugas Security di gerbang sekolah saat keluar dan kembali.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 flex-shrink-0">
                            <a href="{{ route('siswa.izin-keluar-kelas.print', $izinKeluarAktif->id) }}" target="_blank"
                                class="inline-flex items-center px-5 py-2.5 bg-red-50 text-red-700 border border-red-200 rounded-xl font-bold text-sm hover:bg-red-100 transition-all shadow-sm gap-2">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Surat Izin
                            </a>
                            <button type="button" 
                                @click="openQrModal({
                                    nama: '{{ addslashes(Auth::user()->name) }}',
                                    nis: '{{ $siswaNis ?? '-' }}',
                                    barcodeImage: '{{ $barcodeBase64 }}',
                                    tujuan: '{{ addslashes($izinKeluarAktif->tujuan) }}',
                                    jenis: '{{ $izinKeluarAktif->jenis_izin }}',
                                    status: '{{ $izinKeluarAktif->status }}',
                                    estimasi: '{{ \Carbon\Carbon::parse($izinKeluarAktif->estimasi_kembali)->format('H:i') }} WIB',
                                    qrImage: 'data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->generate(route('security.verifikasi.process-scan', $izinKeluarAktif->uuid))) }}',
                                    printUrl: '{{ route('siswa.izin-keluar-kelas.print', $izinKeluarAktif->id) }}'
                                })"
                                class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-200 cursor-pointer gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                Tunjukkan Surat Digital
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($jadwalSaatIni)
                <div
                    class="relative rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-500 shadow-lg overflow-hidden p-6 sm:p-8 animate-gradient">
                    <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-xl"></div>

                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-white text-center md:text-left">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-bold uppercase tracking-wider mb-2">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                Sedang Berlangsung
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                {{ $jadwalSaatIni->mataPelajaran->nama_mapel }}</h3>
                            <p class="mt-1 text-indigo-100 text-lg">Guru: {{ $jadwalSaatIni->guru->nama_lengkap }}</p>
                        </div>

                        <button @click="openModal()"
                            class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-700 font-bold rounded-xl shadow-xl hover:shadow-2xl hover:bg-indigo-50 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6 mr-2 text-indigo-600 group-hover:scale-110 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Minta Izin Keluar
                        </button>
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 p-8 text-center">
                    <div class="inline-flex p-4 rounded-full bg-gray-200 mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700">Tidak Ada Jam Pelajaran Aktif</h3>
                    <p class="text-gray-500">Anda hanya bisa mengajukan izin keluar saat jam pelajaran berlangsung.</p>
                </div>
            @endif

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Riwayat Izin Keluar</h3>
                    <span class="text-xs text-gray-500">Menampilkan 10 data terakhir</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tujuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jenis</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Waktu Pengajuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Foto Bukti</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($riwayatIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $izin->tujuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        @if(($izin->jenis_izin ?? 'keluar_sekolah') === 'keluar_sekolah')
                                            <span class="text-red-500 font-bold">Keluar</span>
                                        @else
                                            <span class="text-blue-500 font-bold">Internal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('D MMM Y, HH:mm') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'disetujui_guru_piket'
                                                     => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                'diverifikasi_security'
                                                     => 'bg-orange-100 text-orange-700 border-orange-200', // Sedang di luar
                                                'selesai'
                                                     => 'bg-green-100 text-green-700 border-green-200', // Sudah kembali
                                                'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                                default => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            };
                                            $statusLabel = str_replace('_', ' ', $izin->status);
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wide {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if ($izin->status == 'ditolak')
                                            <span class="text-red-600 italic flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                         stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                {{ $izin->alasan_penolakan }}
                                            </span>
                                        @else
                                            {{ $izin->keterangan ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($izin->foto)
                                            <a href="{{ asset('storage/' . $izin->foto) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 shadow-sm transition-colors">
                                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Lihat Foto
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada foto</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if(in_array($izin->status, ['disetujui_guru_piket', 'diverifikasi_security', 'selesai']))
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button"
                                                    @click="openQrModal({
                                                        nama: '{{ addslashes(Auth::user()->name) }}',
                                                        nis: '{{ $siswaNis ?? '-' }}',
                                                        barcodeImage: '{{ $barcodeBase64 }}',
                                                        tujuan: '{{ addslashes($izin->tujuan) }}',
                                                        jenis: '{{ $izin->jenis_izin }}',
                                                        status: '{{ $izin->status }}',
                                                        estimasi: '{{ $izin->estimasi_kembali ? \Carbon\Carbon::parse($izin->estimasi_kembali)->format('H:i') . ' WIB' : '-' }}',
                                                        qrImage: 'data:image/svg+xml;base64,{{ base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->generate(route('security.verifikasi.process-scan', $izin->uuid))) }}',
                                                        printUrl: '{{ route('siswa.izin-keluar-kelas.print', $izin->id) }}'
                                                    })"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition-colors shadow-sm"
                                                    title="Tunjukkan QR Code">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                    QR
                                                </button>
                                                <a href="{{ route('siswa.izin-keluar-kelas.print', $izin->id) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 transition-colors shadow-sm"
                                                    title="Unduh PDF">
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    PDF
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>Belum ada riwayat izin keluar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $riwayatIzin->links() }}
                </div>
            </div>
        </div>

        @include('pages.siswa.izin-keluar-kelas.partials.form-modal')

        {{-- Modal QR Code Surat Izin Digital --}}
        <div x-show="isQrOpen" 
            x-cloak 
            class="fixed inset-0 z-50 overflow-y-auto" 
            role="dialog" 
            aria-modal="true">
            
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeQrModal()"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                    
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-gray-900 leading-tight">Surat Izin Keluar Sekolah</h4>
                                <p class="text-xs text-gray-500 font-medium">Verifikasi Security Gerbang</p>
                            </div>
                        </div>
                        <button type="button" 
                            @click="closeQrModal()" 
                            class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-white transition-all focus:outline-none cursor-pointer"
                            title="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Isi Modal --}}
                    <div class="p-6 text-center">
                        <div class="mb-4 bg-gray-50 rounded-2xl p-4 border border-gray-100 text-left space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Nama Siswa</span>
                                <span class="text-sm font-black text-gray-900" x-text="qrData.nama"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Keperluan</span>
                                <span class="text-xs font-bold text-red-600 text-right max-w-[200px] truncate" x-text="qrData.tujuan"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 font-bold uppercase">Estimasi Kembali</span>
                                <span class="text-xs font-black text-gray-900" x-text="qrData.estimasi"></span>
                            </div>
                        </div>

                        {{-- QR Code & Barcode Box --}}
                        <div class="inline-block p-4 sm:p-5 bg-white rounded-2xl border-2 border-dashed border-red-300 shadow-sm mb-4 w-full max-w-sm">
                            <div class="w-44 h-44 sm:w-48 sm:h-48 flex items-center justify-center mx-auto">
                                <template x-if="qrData.qrImage">
                                    <img :src="qrData.qrImage" alt="QR Code Izin Keluar" class="w-full h-full object-contain">
                                </template>
                            </div>

                            <template x-if="qrData.barcodeImage">
                                <div class="mt-4 pt-3 border-t border-dashed border-gray-200 flex flex-col items-center">
                                    <img :src="qrData.barcodeImage" alt="Barcode NIPD" class="h-11 w-auto max-w-full object-contain">
                                    <span class="font-mono font-bold text-xs text-gray-700 tracking-widest mt-1" x-text="`*${qrData.nis}*`"></span>
                                </div>
                            </template>

                            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-black uppercase tracking-wider border border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                Scan Security Gerbang (QR / Barcode)
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 leading-relaxed">
                            Tunjukkan QR Code ini (kamera HP) atau Barcode (alat scanner barcode PC) kepada petugas Security saat melewati gerbang untuk verifikasi keluar dan kembali.
                        </p>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="bg-gray-50 px-6 py-4 flex gap-3 border-t border-gray-100">
                        <template x-if="qrData.printUrl">
                            <a :href="qrData.printUrl" target="_blank"
                                class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 transition text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Unduh PDF
                            </a>
                        </template>
                        <button type="button" 
                            @click="closeQrModal()"
                            class="flex-1 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-gray-800 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function izinKeluarData() {
                return {
                    isOpen: false,
                    isQrOpen: false,
                    qrData: {
                        nama: '',
                        nis: '',
                        tujuan: '',
                        jenis: '',
                        status: '',
                        estimasi: '',
                        qrImage: '',
                        barcodeImage: '',
                        printUrl: ''
                    },
                    openModal() {
                        this.isOpen = true;
                    },
                    closeModal() {
                        this.isOpen = false;
                    },
                    openQrModal(data) {
                        this.qrData = data;
                        this.isQrOpen = true;
                    },
                    closeQrModal() {
                        this.isQrOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
