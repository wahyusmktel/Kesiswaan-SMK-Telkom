<div x-data="navigationAnnouncementModal()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md transition-all duration-300">

    <div x-show="isOpen"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         @click.outside="closeModal()"
         class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-100 flex flex-col max-h-[92vh]">

        {{-- Top Bar with Timer & Close Button --}}
        <div class="relative flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-red-600 via-rose-600 to-red-700 text-white">
            <div class="flex items-center gap-2.5">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/20 text-white text-xs font-black">
                    📢
                </span>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider">{{ $appSetting?->system_announcement_title ?: 'Pemberitahuan Sistem SISFO' }}</h3>
                    <p class="text-[10px] text-white/80">{{ $appSetting?->system_announcement_subtitle ?: 'SMK Telkom Lampung • Pembaruan Navigasi & Menu Baru' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Countdown Badge --}}
                <span class="inline-flex items-center gap-1.5 rounded-full bg-black/25 px-2.5 py-1 text-[11px] font-mono font-bold text-white/90">
                    <svg class="h-3.5 w-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span x-text="timeLeft + 'd'"></span>
                </span>

                <button type="button" @click="closeModal()"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Linear Progress Bar (30s) --}}
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
                <div class="h-full bg-amber-300 transition-all duration-1000 ease-linear"
                     :style="`width: ${(timeLeft / 30) * 100}%`"></div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="overflow-y-auto p-5 sm:p-6 space-y-4">
            {{-- Banner Image --}}
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-red-950 shadow-md">
                <img src="{{ asset('images/announcements/navigation-update-banner.svg') }}"
                     alt="Pembaruan Navigasi & Menu Baru"
                     class="w-full h-auto object-cover max-h-64 sm:max-h-72"
                     onerror="if(!this.src.endsWith('.png')){ this.src='{{ asset('images/announcements/navigation-update-banner.png') }}'; } else { this.style.display='none'; document.getElementById('fallback-banner-header').style.display='block'; }">

                {{-- Fallback Graphic jika gambar belum ter-render sempurna --}}
                <div id="fallback-banner-header" style="display: none;" class="p-6 text-white text-center space-y-2">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/20 text-red-200 text-xs font-bold border border-red-500/30">
                        ✨ TAMPILAN LEBIH MODERN & RAPIH
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white">
                        Pembaruan Tata Letak Menu Navigasi SISFO
                    </h2>
                    <p class="text-xs text-slate-300 max-w-lg mx-auto">
                        Kini seluruh fitur telah dikelompokkan secara rapi berdasarkan fungsinya untuk memudahkan pencarian menu dan kenyamanan kerja Anda.
                    </p>
                </div>
            </div>

            {{-- Ringkasan Perubahan --}}
            <div class="space-y-3">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span>Apa Saja Yang Baru?</span>
                    <span class="rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5">Versi Terbaru</span>
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs text-slate-600">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-100 text-red-700 font-bold shrink-0">
                            📂
                        </span>
                        <div>
                            <p class="font-bold text-slate-900">Pengelompokan (Grouping) Rapi</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Menu disusun per modul: Akademik, Kesiswaan, Sarpras, Keuangan & Sistem.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-700 font-bold shrink-0">
                            📑
                        </span>
                        <div>
                            <p class="font-bold text-slate-900">Dropdown Accordion Interaktif</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Submenu dapat dibuka-tutup sehingga sidebar tidak lagi panjang dan melelahkan.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-purple-700 font-bold shrink-0">
                            💰
                        </span>
                        <div>
                            <p class="font-bold text-slate-900">Modul Anggaran, RAB & Realisasi</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Penyusunan RAB, Reimburse, Laporan Realisasi, dan arsip bukti eviden nota.</p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-2.5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 font-bold shrink-0">
                            ⚡
                        </span>
                        <div>
                            <p class="font-bold text-slate-900">Akses Lebih Cepat</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Tetap mendukung mode collapse sidebar dengan flyout tooltip responsif.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100 bg-slate-50/80 px-5 py-3.5">
            <label class="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer select-none">
                <input type="checkbox" x-model="dontShowAgain"
                       class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-4 w-4">
                <span>Jangan tampilkan lagi pemberitahuan ini</span>
            </label>

            <button type="button" @click="closeModal()"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 px-6 py-2.5 text-xs font-black text-white shadow-md shadow-red-500/20 hover:from-red-700 hover:to-rose-700 transition">
                <span>Saya Mengerti, Tutup</span>
                <span class="text-[10px] opacity-80" x-text="'(' + timeLeft + 's)'"></span>
            </button>
        </div>

    </div>
</div>

<script>
    function navigationAnnouncementModal() {
        return {
            isOpen: false,
            timeLeft: 30,
            timer: null,
            dontShowAgain: false,
            storageKey: 'sisfo_nav_announcement_{{ $appSetting?->system_announcement_version ?: 'v1' }}',

            init() {
                // Periksa apakah pengguna sudah pernah memilih 'Jangan tampilkan lagi'
                const isDismissed = localStorage.getItem(this.storageKey);
                const isSessionDismissed = sessionStorage.getItem(this.storageKey + '_session');

                if (!isDismissed && !isSessionDismissed) {
                    // Munculkan popup dengan sedikit jeda 600ms agar halaman siap
                    setTimeout(() => {
                        this.isOpen = true;
                        this.startCountdown();
                    }, 600);
                }
            },

            startCountdown() {
                this.timer = setInterval(() => {
                    if (this.timeLeft > 1) {
                        this.timeLeft--;
                    } else {
                        this.closeModal();
                    }
                }, 1000);
            },

            closeModal() {
                if (this.timer) {
                    clearInterval(this.timer);
                }
                this.isOpen = false;

                // Simpan state
                if (this.dontShowAgain) {
                    localStorage.setItem(this.storageKey, 'true');
                } else {
                    sessionStorage.setItem(this.storageKey + '_session', 'true');
                }
            }
        };
    }
</script>
