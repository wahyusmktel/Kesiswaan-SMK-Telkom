<x-app-layout>
    {{-- CSS untuk Animasi Gradient --}}
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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Siswa</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if($panggilanAktif)
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-3xl p-1 shadow-xl shadow-red-200 animate-in fade-in slide-in-from-top duration-700">
                <div class="bg-white rounded-[22px] p-6 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0 animate-pulse">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="text-xl font-black text-gray-900 leading-tight">Panggilan Orang Tua!</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Kamu mendapatkan surat panggilan resmi ({{ $panggilanAktif->nomor_surat }}) untuk menghadap pada 
                            <span class="font-bold text-red-600">{{ \Carbon\Carbon::parse($panggilanAktif->tanggal_panggilan)->translatedFormat('l, d F Y') }}</span> 
                            pukul <span class="font-bold text-red-600">{{ date('H:i', strtotime($panggilanAktif->jam_panggilan)) }} WIB</span>.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('kesiswaan.panggilan-ortu.print', $panggilanAktif->id) }}" target="_blank" 
                            class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-100">
                            Unduh Surat
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Alert Izin Keluar Sekolah Aktif Hari Ini --}}
            @if(isset($izinKeluarAktif) && $izinKeluarAktif)
            <div x-data="{ showIzinQrModal: false }" class="bg-gradient-to-r from-red-600 via-rose-700 to-slate-900 rounded-3xl p-1 shadow-xl shadow-red-100 animate-in fade-in slide-in-from-top duration-700">
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
                        <h3 class="text-xl font-black text-gray-900 leading-tight">Surat Izin Keluar Sekolah Tersedia!</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Keperluan: <strong class="text-gray-800">{{ $izinKeluarAktif->tujuan }}</strong> &bull; 
                            Estimasi Kembali: <strong class="text-red-600">{{ \Carbon\Carbon::parse($izinKeluarAktif->estimasi_kembali)->format('H:i') }} WIB</strong>. 
                            Silakan tunjukkan QR Code ini kepada petugas Security di gerbang sekolah.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 flex-shrink-0">
                        <a href="{{ route('siswa.izin-keluar-kelas.print', $izinKeluarAktif->id) }}" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 bg-red-50 text-red-700 border border-red-200 rounded-xl font-bold text-sm hover:bg-red-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Surat Izin
                        </a>
                        <button type="button" 
                            @click="showIzinQrModal = true"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 transition-all shadow-lg shadow-red-200 cursor-pointer">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Tunjukkan Surat Digital
                        </button>
                    </div>
                </div>

                {{-- Modal QR Code Surat Izin Keluar --}}
                <div x-show="showIzinQrModal" 
                    x-cloak 
                    class="fixed inset-0 z-50 overflow-y-auto" 
                    role="dialog" 
                    aria-modal="true">
                    
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showIzinQrModal = false"></div>

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
                                    @click="showIzinQrModal = false" 
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
                                        <span class="text-sm font-black text-gray-900">{{ Auth::user()->name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 font-bold uppercase">Keperluan</span>
                                        <span class="text-xs font-bold text-red-600 text-right max-w-[200px] truncate">{{ $izinKeluarAktif->tujuan }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500 font-bold uppercase">Estimasi Kembali</span>
                                        <span class="text-xs font-black text-gray-900">{{ \Carbon\Carbon::parse($izinKeluarAktif->estimasi_kembali)->format('H:i') }} WIB</span>
                                    </div>
                                </div>

                                {{-- QR Code & Barcode Box --}}
                                <div class="inline-block p-4 sm:p-5 bg-white rounded-2xl border-2 border-dashed border-red-300 shadow-sm mb-4 w-full max-w-sm">
                                    <div class="w-44 h-44 sm:w-48 sm:h-48 flex items-center justify-center mx-auto [&>svg]:w-full [&>svg]:h-full">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->generate(route('security.verifikasi.process-scan', $izinKeluarAktif->uuid)) !!}
                                    </div>

                                    @php
                                        $siswaNis = $izinKeluarAktif->siswa->masterSiswa?->nis ?? null;
                                        $barcodeBase64 = null;
                                        if ($siswaNis) {
                                            $barcodeGen = new \Picqer\Barcode\BarcodeGeneratorPNG();
                                            $barcodeBase64 = 'data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($siswaNis, $barcodeGen::TYPE_CODE_128, 2, 50));
                                        }
                                    @endphp

                                    @if ($barcodeBase64)
                                        <div class="mt-4 pt-3 border-t border-dashed border-gray-200 flex flex-col items-center">
                                            <img src="{{ $barcodeBase64 }}" alt="Barcode NIPD {{ $siswaNis }}" class="h-11 w-auto max-w-full object-contain">
                                            <span class="font-mono font-bold text-xs text-gray-700 tracking-widest mt-1">
                                                *{{ $siswaNis }}*
                                            </span>
                                        </div>
                                    @endif

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
                                <a href="{{ route('siswa.izin-keluar-kelas.print', $izinKeluarAktif->id) }}" target="_blank"
                                    class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 transition text-center flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Unduh PDF
                                </a>
                                <button type="button" 
                                    @click="showIzinQrModal = false"
                                    class="flex-1 py-2.5 bg-gray-900 text-white font-bold text-sm rounded-xl hover:bg-gray-800 transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Alert Keterlambatan Hari Ini --}}
            @if($terlambatHariIni)
            <div x-data="{ showQrModal: false }" class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-3xl p-1 shadow-xl shadow-orange-100 animate-in fade-in slide-in-from-top duration-700">
                <div class="bg-white rounded-[22px] p-6 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-16 h-16 rounded-2xl bg-orange-50 flex items-center justify-center flex-shrink-0 animate-bounce">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h3 class="text-xl font-black text-gray-900 leading-tight">Surat Izin Masuk Kelas Tersedia!</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Kamu tercatat terlambat hari ini pada pukul <span class="font-bold text-orange-600">{{ $terlambatHariIni->waktu_dicatat_security->format('H:i') }} WIB</span>. 
                            Silakan unduh surat izin masuk atau tunjukkan QR Code kepada guru pengajar di kelas.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 flex-shrink-0">
                        <a href="{{ route('siswa.riwayat-keterlambatan.print', $terlambatHariIni->id) }}" 
                            class="inline-flex items-center px-5 py-2.5 bg-orange-50 text-orange-700 border border-orange-200 rounded-xl font-bold text-sm hover:bg-orange-100 transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Unduh Surat Izin
                        </a>
                        <button type="button" 
                            @click="showQrModal = true"
                            class="inline-flex items-center px-5 py-2.5 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700 transition-all shadow-lg shadow-orange-200 cursor-pointer">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Tunjukkan Surat
                        </button>
                    </div>
                </div>

                {{-- Modal QR Code Surat Izin (Hanya dapat ditutup via tombol exit / tutup) --}}
                <div x-show="showQrModal" 
                    x-cloak 
                    class="fixed inset-0 z-50 overflow-y-auto" 
                    role="dialog" 
                    aria-modal="true">
                    
                    {{-- Backdrop / Overlay: sengaja tidak ada @click agar tidak bisa ditutup selain dari tombol X / Tutup --}}
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        {{-- Modal Content Card: sengaja tidak ada @click.outside --}}
                        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                            
                            {{-- Header Modal --}}
                            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 bg-gradient-to-r from-orange-50 to-amber-50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-base font-extrabold text-gray-900 leading-tight">Surat Izin Masuk Kelas</h4>
                                        <p class="text-xs text-gray-500 font-medium">Verifikasi Masuk Kelas</p>
                                    </div>
                                </div>
                                {{-- Tombol Exit (X) --}}
                                <button type="button" 
                                    @click="showQrModal = false" 
                                    class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-white transition-all focus:outline-none cursor-pointer"
                                    title="Tutup">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Isi Modal --}}
                            <div class="p-6 text-center">
                                {{-- Ringkasan Siswa --}}
                                <div class="mb-5 bg-gray-50 rounded-2xl p-3.5 border border-gray-100 flex items-center justify-between text-left">
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Siswa</p>
                                        <p class="text-sm font-black text-gray-800">{{ Auth::user()->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Waktu Terlambat</p>
                                        <p class="text-sm font-black text-orange-600">{{ $terlambatHariIni->waktu_dicatat_security->format('H:i') }} WIB</p>
                                    </div>
                                </div>

                                {{-- QR Code Box --}}
                                <div class="inline-block p-4 bg-white rounded-2xl border-2 border-dashed border-orange-200 shadow-sm mb-4">
                                    <div class="w-48 h-48 sm:w-56 sm:h-56 flex items-center justify-center mx-auto [&>svg]:w-full [&>svg]:h-full">
                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(220)->margin(1)->generate(route('guru-kelas.verifikasi-terlambat.scan', $terlambatHariIni->uuid)) !!}
                                    </div>
                                    <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-xs font-black uppercase tracking-wider border border-orange-100">
                                        <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                                        PINDAI OLEH GURU KELAS
                                    </div>
                                </div>

                                {{-- Kalimat Instruksi --}}
                                <div class="bg-amber-50 rounded-2xl p-4 border border-amber-200/70 text-left mb-6">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-xs text-amber-900 leading-relaxed font-medium">
                                            <span class="font-bold">Instruksi:</span> Tunjukkan QR Code izin ini kepada Guru Pengajar di kelas untuk dipindai sebagai verifikasi izin masuk kelas secara langsung (alternatif tanpa cetak surat fisik).
                                        </p>
                                    </div>
                                </div>

                                {{-- Tombol Tutup --}}
                                <button type="button" 
                                    @click="showQrModal = false"
                                    class="w-full inline-flex justify-center items-center px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl font-bold text-sm transition-all focus:outline-none cursor-pointer">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Alert Jadwal Konsultasi --}}
            @if($konsultasiHariIni)
            <div class="mb-6 bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-100 text-white relative overflow-hidden animate-in slide-in-from-top duration-700">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-black leading-tight">Jadwal Konsultasi Hari Ini!</h4>
                            <p class="text-indigo-100 text-sm font-medium">Kamu memiliki jadwal bimbingan pada pukul <b>{{ date('H:i', strtotime($konsultasiHariIni->jam_rencana)) }} WIB</b> di <b>{{ $konsultasiHariIni->tempat ?? 'Ruang BK' }}</b>.</p>
                        </div>
                    </div>
                    <a href="{{ route('siswa.bk.index') }}" class="px-6 py-3 bg-white text-indigo-600 rounded-2xl font-black text-sm hover:bg-indigo-50 transition-all shadow-lg active:scale-95 whitespace-nowrap">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div
                        class="relative rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 shadow-lg overflow-hidden p-6 sm:p-10 animate-gradient h-full">
                        <div class="absolute right-0 top-0 h-full w-1/2 bg-white/10 transform skew-x-12"></div>
                        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
                            <div class="text-white">
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👋
                                </h3>
                                <p class="mt-2 text-indigo-100 font-medium">Bagaimana kabarmu hari ini? Jangan lupa jaga
                                    kesehatan ya.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                                <a href="{{ route('siswa.izin-keluar-kelas.index') }}"
                                    class="inline-flex items-center px-4 py-3 bg-white text-rose-600 rounded-xl font-bold text-sm shadow-md hover:bg-rose-50 hover:shadow-lg transition-all transform hover:-translate-y-1 group">
                                    <svg class="w-5 h-5 mr-2 text-rose-500 group-hover:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Izin Keluar Kelas
                                </a>
                                <a href="{{ route('izin.index') }}"
                                    class="inline-flex items-center px-4 py-3 bg-white text-indigo-600 rounded-xl font-bold text-sm shadow-md hover:bg-indigo-50 hover:shadow-lg transition-all transform hover:-translate-y-1 group">
                                    <svg class="w-5 h-5 mr-2 text-pink-500 group-hover:scale-110 transition-transform"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Buat Izin Tidak Masuk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    @if ($kegiatanSaatIni)
                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group h-full">
                            <div class="absolute -right-4 -bottom-4 opacity-20 transform group-hover:scale-110 transition-transform">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                            </div>
                            <div class="relative z-10">
                                <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block border border-white/30">Kegiatan Saat Ini</span>
                                <h4 class="text-xl font-black leading-tight mb-1">{{ str_replace('_', ' ', strtoupper($kegiatanSaatIni->tipe_kegiatan)) }}</h4>
                                <p class="text-amber-50 text-xs font-bold font-mono">
                                    {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_selesai)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6 flex flex-col items-center justify-center text-center h-full group hover:border-indigo-300 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3 group-hover:bg-indigo-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-300 group-hover:text-indigo-400 Transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Tidak Ada Kegiatan Spesial</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Chat History --}}
            @if($chat_rooms->isNotEmpty())
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Percakapan Chat BK</h3>
                    <a href="{{ route('siswa.chat.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($chat_rooms as $room)
                    <a href="{{ route('siswa.chat.index', ['room_id' => $room->id]) }}" class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/50 hover:bg-gray-50 transition-all border border-transparent hover:border-indigo-100">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-black text-sm relative shadow-md">
                            {{ substr($room->guruBK->name, 0, 1) }}
                            @if($room->unread_count > 0)
                            <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 border-2 border-white rounded-full flex items-center justify-center text-[10px] font-bold">
                                {{ $room->unread_count }}
                            </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $room->guruBK->name }}</h4>
                            <p class="text-xs text-gray-500 truncate mt-0.5 font-medium">
                                {{ $room->messages->first()?->message ?? ($room->messages->first()?->type != 'text' ? '[Lampiran]' : 'Mulai chat...') }}
                            </p>
                            <span class="text-[9px] text-gray-400 font-bold uppercase mt-1 block">
                                {{ $room->last_message_at ? \Carbon\Carbon::parse($room->last_message_at)->diffForHumans() : '' }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-2xl p-6 border border-indigo-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Total Diajukan</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-3xl font-black text-gray-800">{{ $totalDiajukan }}</h3>
                            <span class="ml-2 text-xs text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-indigo-50 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 w-full animate-pulse"></div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Disetujui</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-3xl font-black text-gray-800">{{ $totalDisetujui }}</h3>
                            <span class="ml-2 text-xs text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-green-50 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 w-full"
                            style="width: {{ $totalDiajukan > 0 ? ($totalDisetujui / $totalDiajukan) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-rose-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Total Keterlambatan</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-3xl font-black text-gray-800">{{ $totalKeterlambatan }}</h3>
                            <span class="ml-2 text-xs text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-rose-50 rounded-full overflow-hidden">
                        <div class="h-full bg-rose-500 w-full">
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-orange-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[10px] font-bold text-orange-500 uppercase tracking-wider">Panggilan Ortu</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-3xl font-black text-gray-800">{{ $totalPanggilan }}</h3>
                            <span class="ml-2 text-xs text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-orange-50 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500 w-full"
                            style="width: 100%">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Widget Poin Pelanggaran --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="md:col-span-2 bg-white rounded-2xl p-6 border border-red-100 shadow-sm relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-red-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                    <div class="relative z-10 flex items-center gap-6">
                        <div class="flex-shrink-0 w-20 h-20 rounded-2xl {{ $poinData['status']['class'] }} flex items-center justify-center text-white shadow-lg">
                            <span class="text-3xl font-black">{{ $poinData['current_points'] }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Poin Pelanggaran Saat Ini</p>
                            <h4 class="text-xl font-black text-gray-800 mt-1">{{ $poinData['status']['label'] }}</h4>
                            <p class="text-xs text-gray-400 mt-1">Akumulasi dari seluruh pelanggaran dikurangi prestasi & pemutihan.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pelanggaran</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-red-600">{{ $poinData['total_pelanggaran'] }}</span>
                        <span class="text-xs text-gray-400 font-medium">Points</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bonus Prestasi</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-green-600">+{{ $poinData['total_prestasi'] }}</span>
                        <span class="text-xs text-gray-400 font-medium">Points</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Statistik Izin Saya</h3>
                            <p class="text-sm text-gray-500">Proporsi status pengajuan izin</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                    </div>

                    <div class="relative h-64 w-full flex items-center justify-center">
                        <canvas id="statusIzinChart"></canvas>
                        <div id="emptyChartMessage"
                            class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="text-sm">Belum ada data izin</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    {{-- Riwayat Poin --}}
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-bold text-gray-800">Aktivitas Poin</h4>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="space-y-4">
                            @forelse($poinData['recent_activities'] as $activity)
                            <div class="flex items-start gap-3 p-3 rounded-xl {{ $activity['bg'] }} transition-all hover:scale-[1.02]">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $activity['color'] }} bg-white shadow-sm ring-1 ring-black/5">
                                    {{ $activity['points'] > 0 ? '+' : '' }}{{ $activity['points'] }}
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $activity['title'] }}</p>
                                    <p class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($activity['date'])->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-6">
                                <p class="text-xs text-gray-400 italic">Belum ada aktivitas poin</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                        <h4 class="font-bold text-gray-800 mb-2">Panduan Izin</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Izin Sakit wajib melampirkan surat dokter.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Izin maksimal 3 hari berturut-turut.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Cek status izin secara berkala di menu Riwayat.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfigurasi Font Default
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const statusData = @json($statusChartData);

                // Cek jika data kosong
                if (!statusData.data || statusData.data.length === 0 || statusData.data.every(val => val === 0)) {
                    document.getElementById('emptyChartMessage').classList.remove('hidden');
                } else if (document.getElementById('statusIzinChart')) {
                    const ctxStatus = document.getElementById('statusIzinChart').getContext('2d');
                    new Chart(ctxStatus, {
                        type: 'doughnut', // Ubah jadi Doughnut biar lebih modern
                        data: {
                            labels: statusData.labels.map(label => label.charAt(0).toUpperCase() + label.slice(
                                1)),
                            datasets: [{
                                data: statusData.data,
                                // Warna disesuaikan dengan tema (Indigo, Green, Red)
                                backgroundColor: [
                                    '#6366f1', // Diajukan (Indigo)
                                    '#22c55e', // Disetujui (Green)
                                    '#ef4444', // Ditolak (Red)
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%', // Lubang tengah
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#1e293b',
                                    bodyColor: '#475569',
                                    borderColor: '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 4
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
