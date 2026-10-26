<x-app-layout>
    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');
            
            .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

            .card-stage {
                perspective: 1200px;
            }

            .card-container {
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-container:hover {
                transform: translateY(-4px);
            }

            /* Normal modal card scaling */
            .modal-card-wrapper {
                transform-origin: center center;
                transform: scale(1.05);
                transition: transform 0.3s ease;
            }
            @media (min-width: 480px) {
                .modal-card-wrapper {
                    transform: scale(1.22);
                }
            }
            @media (min-width: 640px) {
                .modal-card-wrapper {
                    transform: scale(1.42);
                }
            }
            @media (min-width: 1024px) {
                .modal-card-wrapper {
                    transform: scale(1.58);
                }
            }

            /* Fullscreen Active Styling */
            #modal-dialog-container:fullscreen,
            #modal-dialog-container:-webkit-full-screen,
            .fullscreen-dialog-active {
                position: fixed !important;
                inset: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                max-width: 100vw !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                margin: 0 !important;
                background: #020617 !important;
                border: none !important;
                z-index: 999999 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                padding: 12px 16px !important;
                overflow: hidden !important;
            }

            /* Fullscreen Card Rotation & Scaling to match phone screen landscape */
            @media (orientation: portrait) {
                .fullscreen-card-wrapper {
                    transform-origin: center center;
                    transform: rotate(90deg) scale(1.35);
                    transition: transform 0.3s ease;
                }
                @media (min-height: 650px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(90deg) scale(1.52);
                    }
                }
                @media (min-height: 750px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(90deg) scale(1.72);
                    }
                }
                @media (min-height: 850px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(90deg) scale(1.92);
                    }
                }
            }

            @media (orientation: landscape) {
                .fullscreen-card-wrapper {
                    transform-origin: center center;
                    transform: rotate(0deg) scale(1.25);
                    transition: transform 0.3s ease;
                }
                @media (min-height: 480px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(0deg) scale(1.48);
                    }
                }
                @media (min-height: 650px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(0deg) scale(1.78);
                    }
                }
                @media (min-height: 800px) {
                    .fullscreen-card-wrapper {
                        transform: rotate(0deg) scale(2.05);
                    }
                }
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-jakarta font-extrabold text-xl text-slate-800 leading-tight uppercase tracking-tight">Kartu Pelajar Digital</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Identitas Resmi Siswa Telkom Schools SMK Telkom Lampung</p>
            </div>
            <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Status: Terverifikasi Aktif
            </div>
        </div>
    </x-slot>

    <div class="py-8 w-full font-jakarta"
        x-data="{
            currentSide: 'front',
            showModal: false,
            modalSide: 'front',
            isFullScreen: false,

            init() {
                const onFsChange = () => {
                    if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                        this.isFullScreen = false;
                    }
                };
                document.addEventListener('fullscreenchange', onFsChange);
                document.addEventListener('webkitfullscreenchange', onFsChange);
            },

            openModal() {
                this.showModal = true;
                this.modalSide = 'front';
                this.isFullScreen = false;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                if (this.isFullScreen) {
                    this.exitNativeFullScreen();
                    this.isFullScreen = false;
                }
                this.showModal = false;
                document.body.style.overflow = '';
            },

            toggleFullScreen() {
                this.isFullScreen = !this.isFullScreen;
                if (this.isFullScreen) {
                    this.enterNativeFullScreen();
                } else {
                    this.exitNativeFullScreen();
                }
            },

            enterNativeFullScreen() {
                const el = document.getElementById('modal-dialog-container');
                if (el) {
                    try {
                        if (el.requestFullscreen) {
                            el.requestFullscreen().catch(() => {});
                        } else if (el.webkitRequestFullscreen) {
                            el.webkitRequestFullscreen();
                        } else if (el.msRequestFullscreen) {
                            el.msRequestFullscreen();
                        }
                    } catch (e) {}
                }

                try {
                    if (screen.orientation && screen.orientation.lock) {
                        screen.orientation.lock('landscape').catch(() => {});
                    }
                } catch (e) {}
            },

            exitNativeFullScreen() {
                try {
                    if (document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement) {
                        if (document.exitFullscreen) {
                            document.exitFullscreen().catch(() => {});
                        } else if (document.webkitExitFullscreen) {
                            document.webkitExitFullscreen();
                        } else if (document.msExitFullscreen) {
                            document.msExitFullscreen();
                        }
                    }
                } catch (e) {}

                try {
                    if (screen.orientation && screen.orientation.unlock) {
                        screen.orientation.unlock();
                    }
                } catch (e) {}
            }
        }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Hero Banner --}}
            <div class="relative rounded-[2rem] bg-gradient-to-br from-red-600 via-rose-700 to-slate-900 shadow-xl overflow-hidden p-6 sm:p-8 border border-white/10 text-white">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-white/5 transform skew-x-12 blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm text-[10px] font-bold uppercase tracking-[0.2em] mb-2 border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Standar Telkom Schools
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black tracking-tight uppercase">
                            {{ $siswa->nama_lengkap }}
                        </h3>
                        <p class="mt-1 text-red-100 text-sm font-medium opacity-90">
                            NIS: <span class="font-bold tracking-wider">{{ $siswa->nis }}</span> &bull; 
                            Kelas: <span class="font-bold uppercase">{{ $rombel?->kelas?->nama_kelas ?? '-' }}</span>
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap justify-center gap-3">
                        <a href="{{ route('siswa.kartu-pelajar.cetak') }}?design=standar_ts&side=both" target="_blank"
                            class="inline-flex items-center px-5 py-3 bg-white text-slate-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-lg hover:bg-slate-100 transition-all transform hover:-translate-y-0.5 gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Fisik / PDF
                        </a>

                        <button type="button" id="btn-download-png" onclick="downloadPNG()"
                            class="inline-flex items-center px-5 py-3 bg-slate-900/80 backdrop-blur-md text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg hover:bg-slate-900 border border-white/20 transition-all transform hover:-translate-y-0.5 gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PNG (2 Sisi)
                        </button>

                        <button type="button" @click="openModal()"
                            class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg border border-white/20 transition-all transform hover:-translate-y-0.5 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Tampilkan Kartu Pelajar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Interactive Card Preview Area --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-10 flex flex-col items-center">
                
                {{-- Switch Sisi Kartu (Depan / Belakang) --}}
                <div class="flex justify-center mb-6">
                    <div class="inline-flex p-1.5 bg-slate-100 rounded-2xl shadow-inner gap-1.5 border border-slate-200">
                        <button type="button" @click="currentSide = 'front'"
                            :class="currentSide === 'front' ? 'bg-white text-red-600 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                            </svg>
                            Tampak Depan (Biodata)
                        </button>
                        <button type="button" @click="currentSide = 'back'"
                            :class="currentSide === 'back' ? 'bg-white text-red-600 shadow-md font-extrabold' : 'text-slate-600 hover:text-slate-900 font-bold'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tampak Belakang (Ketentuan)
                        </button>
                    </div>
                </div>

                {{-- The Card Container --}}
                <div class="card-stage flex justify-center w-full py-2">
                    <div class="card-container cursor-pointer select-none"
                        @click="currentSide = currentSide === 'front' ? 'back' : 'front'"
                        title="Klik untuk membalik kartu">
                        
                        {{-- Tampak Depan --}}
                        <div x-show="currentSide === 'front'" x-cloak class="rounded-xl shadow-2xl overflow-hidden border border-slate-200">
                            <div id="card-front-export">
                                @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts', [
                                    'siswa' => $siswa,
                                    'barcode' => $barcode,
                                    'kepsekData' => $kepsekData,
                                    'qrCodeKepsek' => $qrCodeKepsek
                                ])
                            </div>
                        </div>

                        {{-- Tampak Belakang --}}
                        <div x-show="currentSide === 'back'" x-cloak style="display: none;" class="rounded-xl shadow-2xl overflow-hidden border border-slate-200">
                            <div id="card-back-export">
                                @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts-back')
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Hint Tap --}}
                <p class="mt-5 text-xs text-slate-400 font-bold uppercase tracking-wider flex items-center gap-1.5 select-none animate-pulse">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Klik / Tap kartu untuk memutar tampak depan & belakang
                </p>

            </div>

            {{-- Informasi Penggunaan & Panduan Cetak --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Card 1 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-tight">Desain Resmi Standar TS</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mt-1">Kartu pelajar resmi ini memuat logo Telkom Schools, identitas lengkap, barcode NIS, dan tanda tangan digital Kepala Sekolah SMK Telkom Lampung.</p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m5-4.5h1m-9 0h1m3 0a2 2 0 11-4 0 2 2 0 014 0zM4 12H3m15 0h1M5 5l.7.7m12.6 12.6l.7.7M17 5l-.7.7M6.3 17.7l-.7.7" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-tight">Presensi & Akses Stella</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mt-1">Barcode pada kartu dapat dipindai untuk presensi kehadiran, izin meninggalkan kelas, peminjaman buku perpustakaan, hingga verifikasi status siswa.</p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm uppercase tracking-tight">Ukuran Standar Fisik CR80</h4>
                        <p class="text-xs text-slate-500 leading-relaxed mt-1">Dimensi kartu tepat 85.6mm x 54mm (standar kartu ID/KTP/ATM). Siap dicetak mandiri menggunakan printer kartu atau kertas foto berformat dupleks.</p>
                    </div>
                </div>

            </div>

        </div>

        {{-- Modal Popup Tampilkan Kartu Pelajar (Teleported to Body to eliminate sidebar stacking conflicts) --}}
        <template x-teleport="body">
            <div x-show="showModal"
                x-cloak
                @keydown.escape.window="closeModal()"
                class="fixed inset-0 z-[999999] flex items-center justify-center p-2 sm:p-6 overflow-hidden"
                style="display: none;">
                
                {{-- Backdrop --}}
                <div x-show="!isFullScreen" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
                    @click="closeModal()"></div>

                {{-- Modal Dialog --}}
                <div id="modal-dialog-container"
                    :class="isFullScreen ? 'fullscreen-dialog-active' : 'relative bg-slate-900 border border-white/15 rounded-3xl shadow-2xl max-w-4xl w-full p-5 sm:p-8 my-auto overflow-hidden'"
                    class="text-white z-10 transition-all duration-300"
                    @click.stop>
                    
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-white/10 gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-red-600/20 text-red-400 border border-red-500/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm sm:text-base font-black uppercase tracking-tight text-white truncate">Kartu Pelajar Digital</h3>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        Aktif
                                    </span>
                                </div>
                                <p class="text-[11px] sm:text-xs text-slate-400 truncate">SMK Telkom Lampung &bull; {{ $siswa->nis }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Tombol Layar Penuh di Header --}}
                            <button type="button" @click="toggleFullScreen()"
                                :title="isFullScreen ? 'Keluar Layar Penuh' : 'Layar Penuh (Landscape)'"
                                class="h-9 sm:h-10 px-3 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white flex items-center gap-1.5 text-xs font-bold transition-all flex-shrink-0">
                                <svg x-show="!isFullScreen" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="isFullScreen" style="display: none;" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m0 0v4m0-4h-4m-6 6l-5 5m0 0v-4m0 4h4m6-6l5 5m0 0v-4m0 4h-4" />
                                </svg>
                                <span class="hidden sm:inline" x-text="isFullScreen ? 'Keluar Layar Penuh' : 'Layar Penuh'"></span>
                            </button>

                            <button type="button" @click="closeModal()"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-all flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Switch Sisi Kartu di dalam Modal --}}
                    <div class="flex justify-center my-3 sm:my-4">
                        <div class="inline-flex p-1 bg-white/10 backdrop-blur-md rounded-2xl shadow-inner gap-1 border border-white/10">
                            <button type="button" @click="modalSide = 'front'"
                                :class="modalSide === 'front' ? 'bg-red-600 text-white shadow-md font-extrabold' : 'text-slate-300 hover:text-white font-bold'"
                                class="px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                Depan (Biodata & Barcode)
                            </button>
                            <button type="button" @click="modalSide = 'back'"
                                :class="modalSide === 'back' ? 'bg-red-600 text-white shadow-md font-extrabold' : 'text-slate-300 hover:text-white font-bold'"
                                class="px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Belakang (Ketentuan)
                            </button>
                        </div>
                    </div>

                    {{-- Landscape Card Display Area --}}
                    <div class="flex-1 flex items-center justify-center overflow-hidden py-4 sm:py-8 min-h-[220px]">
                        <div :class="isFullScreen ? 'fullscreen-card-wrapper' : 'modal-card-wrapper'"
                            class="cursor-pointer select-none transition-transform duration-300"
                            @click="modalSide = modalSide === 'front' ? 'back' : 'front'"
                            title="Klik untuk membalik kartu (Depan / Belakang)">
                            
                            <div x-show="modalSide === 'front'" class="rounded-xl shadow-2xl overflow-hidden border border-slate-200">
                                @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts', [
                                    'siswa' => $siswa,
                                    'barcode' => $barcode,
                                    'kepsekData' => $kepsekData,
                                    'qrCodeKepsek' => $qrCodeKepsek
                                ])
                            </div>

                            <div x-show="modalSide === 'back'" style="display: none;" class="rounded-xl shadow-2xl overflow-hidden border border-slate-200">
                                @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts-back')
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="pt-3 sm:pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                        <div class="flex items-center gap-2 text-center sm:text-left">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Klik pada kartu untuk membalik sisi depan / belakang.</span>
                        </div>

                        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                            {{-- Tombol Fullscreen Landscape --}}
                            <button type="button" @click="toggleFullScreen()"
                                :class="isFullScreen ? 'bg-amber-500 hover:bg-amber-400 text-slate-950' : 'bg-red-600 hover:bg-red-500 text-white'"
                                class="px-4 py-2.5 rounded-xl font-bold transition shadow-lg flex items-center justify-center gap-2 flex-1 sm:flex-initial">
                                
                                <svg x-show="!isFullScreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="isFullScreen" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m0 0v4m0-4h-4m-6 6l-5 5m0 0v-4m0 4h4m6-6l5 5m0 0v-4m0 4h-4" />
                                </svg>
                                <span x-text="isFullScreen ? 'Keluar Fullscreen' : 'Layar Penuh (Landscape)'"></span>
                            </button>

                            <button type="button" @click="closeModal()"
                                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold transition">
                                Tutup
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </template>

    </div>

    @push('scripts')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        async function downloadPNG() {
            const btn = document.getElementById('btn-download-png');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses PNG...
            `;
            btn.disabled = true;

            const frontEl = document.getElementById('card-front-export');
            const backEl = document.getElementById('card-back-export');

            // Save display states
            const frontParent = frontEl.parentElement;
            const backParent = backEl.parentElement;
            const frontWasHidden = frontParent.style.display === 'none';
            const backWasHidden = backParent.style.display === 'none';

            // Temporarily unhide both for capture
            frontParent.style.display = 'block';
            backParent.style.display = 'block';

            try {
                const config = {
                    scale: 4,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false
                };

                const canvasFront = await html2canvas(frontEl, config);
                const canvasBack = await html2canvas(backEl, config);

                const download = (canvas, side) => {
                    const a = document.createElement('a');
                    a.download = `KARTU_PELAJAR_${side}_{{ $siswa->nis }}.png`;
                    a.href = canvas.toDataURL('image/png');
                    a.click();
                };

                download(canvasFront, 'DEPAN');
                setTimeout(() => download(canvasBack, 'BELAKANG'), 500);
            } catch (err) {
                console.error(err);
                alert('Gagal membuat file PNG. Anda dapat menggunakan opsi "Cetak Fisik / PDF" untuk menyimpan sebagai PDF.');
            } finally {
                // Restore visibility
                if (frontWasHidden) frontParent.style.display = 'none';
                if (backWasHidden) backParent.style.display = 'none';

                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    </script>
    @endpush
</x-app-layout>
