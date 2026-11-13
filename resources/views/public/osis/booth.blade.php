<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bilik Suara E-Voting Pilketos - SMK Telkom Lampung</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #030712; color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #991b1b 100%);
        }
        .scanner-laser {
            background: linear-gradient(to bottom, transparent, rgba(239, 68, 68, 0.8), transparent);
            animation: scan 2s infinite ease-in-out;
        }
        @keyframes scan {
            0%, 100% { top: 0%; opacity: 0.2; }
            50% { top: 90%; opacity: 1; }
        }
    </style>
</head>
<body class="h-full antialiased flex flex-col justify-between selection:bg-red-500 selection:text-white overflow-x-hidden"
      x-data="votingBoothApp()"
      x-init="initApp()"
      @click="focusBarcodeInput()">

    <!-- Header Bilik Suara -->
    <header class="glass-dark border-b border-white/10 px-6 py-4 flex items-center justify-between z-20">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl gradient-red flex items-center justify-center p-2 shadow-lg shadow-red-500/20">
                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            </div>
            <div>
                <h1 class="font-outfit font-black text-lg text-white tracking-tight flex items-center gap-2">
                    BILIK SUARA ELEKTRONIK <span class="text-rose-500 font-bold">•</span>
                    <span class="text-rose-400 font-mono-num" x-text="currentBilik">Bilik 1</span>
                </h1>
                <span class="text-xs text-slate-400">Pemilihan Ketua &amp; Wakil Ketua OSIS SMK Telkom Lampung</span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Pilihan Nomor Bilik (Bisa diubah panitia) -->
            <div class="flex items-center gap-2 bg-slate-900 border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-300">
                <span>Posisi:</span>
                <select x-model="currentBilik" @change="saveBilik()" class="bg-transparent border-none text-white font-bold text-xs focus:ring-0 cursor-pointer pr-6">
                    <option value="Bilik 1" class="bg-slate-900">Bilik 1</option>
                    <option value="Bilik 2" class="bg-slate-900">Bilik 2</option>
                    <option value="Bilik 3" class="bg-slate-900">Bilik 3</option>
                    <option value="Bilik 4" class="bg-slate-900">Bilik 4</option>
                    <option value="Bilik 5" class="bg-slate-900">Bilik 5</option>
                </select>
            </div>

            <a href="{{ route('public.osis.live-count') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                Live Count ↗
            </a>
        </div>
    </header>

    <!-- CONTENT AREA -->
    <main class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 py-8 relative">

        <!-- 1. STANDBY SCREEN (MENUNGGU SCAN BARCODE FISIK) -->
        <div x-show="viewState === 'standby'" class="w-full max-w-2xl text-center space-y-8 animate-fadeIn">
            <!-- Laser Barcode Box Graphic -->
            <div class="w-72 h-44 mx-auto rounded-3xl border-2 border-dashed border-rose-500/40 relative overflow-hidden flex flex-col items-center justify-center p-6 bg-rose-950/10 shadow-2xl shadow-rose-950/50">
                <div class="absolute inset-x-0 h-10 scanner-laser"></div>
                <svg class="w-16 h-16 text-rose-500/80 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
                <span class="font-outfit font-bold text-xs uppercase tracking-widest text-rose-400">Area Scanner Barcode</span>
                <span class="text-[10px] text-slate-400 mt-1 font-mono">Standby menerima input reader 1D</span>
            </div>

            <div class="space-y-3">
                <h2 class="font-outfit font-black text-3xl sm:text-4xl text-white tracking-tight">
                    Silakan Scan Barcode Kartu Pemilih
                </h2>
                <p class="text-slate-400 text-sm max-w-md mx-auto leading-relaxed">
                    Arahkan scanner barcode fisik ke kartu pemilih siswa untuk membuka bilik suara dan memverifikasi identitas.
                </p>
            </div>

            <!-- Auto-focused Barcode Input Form -->
            <form @submit.prevent="submitBarcode()" class="max-w-md mx-auto relative">
                <input type="text"
                       x-ref="barcodeInput"
                       x-model="barcodeCode"
                       placeholder="Menunggu scan barcode atau ketik token..."
                       autocomplete="off"
                       class="w-full bg-slate-900/90 border-2 border-white/10 focus:border-rose-500 rounded-2xl py-4 px-5 text-center font-mono-num text-lg text-white tracking-widest placeholder:text-slate-600 focus:outline-none focus:ring-4 focus:ring-rose-500/20 shadow-xl transition-all">
                
                <button type="submit" 
                        :disabled="isLoading"
                        class="mt-4 px-8 py-3 rounded-xl gradient-red text-white text-xs font-bold uppercase tracking-wider hover:brightness-110 shadow-lg shadow-red-500/20 transition-all flex items-center justify-center gap-2 mx-auto disabled:opacity-50">
                    <span x-show="!isLoading">Verifikasi Barcode</span>
                    <span x-show="isLoading" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Memeriksa...
                    </span>
                </button>
            </form>

            <div class="flex items-center justify-center gap-6 text-xs text-slate-500">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Hardware Scanner Siap</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-400"></span> Sekali Pakai (Anti-Curang)</span>
            </div>
        </div>

        <!-- 2. VOTING SCREEN (SURAT SUARA DIGITAL) -->
        <div x-show="viewState === 'voting'" class="w-full max-w-6xl space-y-8 animate-fadeIn">
            
            <!-- Voter Identity Card (Bar Atas) -->
            <div class="glass-card p-6 rounded-3xl border-rose-500/30 flex flex-col md:flex-row items-center justify-between gap-6 bg-gradient-to-r from-rose-950/20 to-slate-900/40">
                <div class="flex items-center gap-4 text-center md:text-left">
                    <img :src="voter.foto" :alt="voter.nama" class="w-16 h-16 rounded-2xl object-cover border-2 border-rose-500/40 shadow-xl">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Pemilih Terverifikasi
                        </div>
                        <h3 class="font-outfit font-black text-xl text-white" x-text="voter.nama"></h3>
                        <div class="text-xs text-slate-300 font-mono">
                            NIS: <span x-text="voter.nis"></span> &bull; Kelas: <span class="text-rose-400 font-bold" x-text="voter.kelas"></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="cancelSession()" class="px-4 py-2 rounded-xl text-xs font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-colors">
                        Batal / Keluar
                    </button>
                </div>
            </div>

            <!-- Instruksi -->
            <div class="text-center space-y-1">
                <h2 class="font-outfit font-black text-2xl sm:text-3xl text-white">Surat Suara Digital</h2>
                <p class="text-xs sm:text-sm text-slate-400">Tekan tombol <span class="text-rose-400 font-bold">"PILIH PASLON"</span> pada kandidat pilihan Anda, lalu konfirmasi.</p>
            </div>

            <!-- Paslon Ballot Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <template x-for="k in kandidats" :key="k.id">
                    <div class="glass-card rounded-[32px] overflow-hidden p-6 space-y-6 flex flex-col justify-between border-white/10 hover:border-rose-500/50 transition-all group">
                        
                        <!-- Nomor Urut Badge -->
                        <div class="flex items-center justify-between">
                            <div class="w-16 h-16 rounded-2xl gradient-red flex items-center justify-center text-white font-outfit font-black text-3xl shadow-xl shadow-red-500/30">
                                0<span x-text="k.nomor_urut"></span>
                            </div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kandidat Paslon</span>
                        </div>

                        <!-- Foto Paslon -->
                        <div class="flex justify-center">
                            <div class="w-44 h-44 rounded-3xl overflow-hidden border-2 border-white/10 shadow-2xl relative group-hover:scale-105 transition-transform">
                                <img :src="k.foto_paslon" :alt="k.nama_ketua" class="w-full h-full object-cover">
                            </div>
                        </div>

                        <!-- Identitas Kandidat -->
                        <div class="text-center space-y-1">
                            <h3 class="font-outfit font-black text-xl text-white" x-text="k.nama_ketua"></h3>
                            <div class="text-sm text-slate-300 font-semibold" x-show="k.nama_wakil">
                                &amp; <span x-text="k.nama_wakil"></span>
                            </div>
                            <div class="text-xs text-slate-400 font-mono" x-text="k.kelas_ketua + (k.kelas_wakil ? ' • ' + k.kelas_wakil : '')"></div>
                        </div>

                        <!-- Visi Singkat -->
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-xs text-slate-300 line-clamp-3 italic">
                            "<span x-text="k.visi"></span>"
                        </div>

                        <!-- Tombol Coblos -->
                        <button @click="openConfirmModal(k)"
                                class="w-full py-4 rounded-2xl gradient-red text-white font-outfit font-black text-sm uppercase tracking-wider hover:brightness-110 shadow-xl shadow-red-500/30 flex items-center justify-center gap-2 transition-transform active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>PILIH PASLON 0<span x-text="k.nomor_urut"></span></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- 3. SUCCESS SCREEN (SUARA SAH TERCATAT) -->
        <div x-show="viewState === 'success'" class="w-full max-w-lg text-center space-y-6 animate-fadeIn">
            <div class="w-28 h-28 mx-auto rounded-full bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400 flex items-center justify-center shadow-2xl shadow-emerald-500/30">
                <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>

            <div class="space-y-2">
                <h2 class="font-outfit font-black text-3xl sm:text-4xl text-white">Suara Berhasil Digunakan!</h2>
                <p class="text-slate-300 text-sm max-w-sm mx-auto leading-relaxed">
                    Terima kasih telah menggunakan hak suara Anda secara demokratis, rahasia, dan bertanggung jawab.
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-900 border border-white/10 text-xs text-slate-400 font-mono">
                Layar akan kembali ke mode scan otomatis dalam: <span class="text-rose-400 font-bold font-mono-num text-sm" x-text="countdown">4</span> detik.
            </div>

            <button @click="resetToStandby()" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 text-white transition-colors">
                Lanjut ke Pemilih Berikutnya Sekarang
            </button>
        </div>

    </main>

    <!-- MODAL KONFIRMASI COBLOS -->
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div class="glass-card rounded-[32px] p-8 max-w-md w-full border-rose-500/40 text-center space-y-6 shadow-2xl animate-scaleUp">
            
            <div class="w-16 h-16 rounded-2xl gradient-red flex items-center justify-center text-white font-outfit font-black text-2xl mx-auto shadow-xl">
                0<span x-text="selectedCandidate ? selectedCandidate.nomor_urut : ''"></span>
            </div>

            <div class="space-y-2">
                <h3 class="font-outfit font-black text-2xl text-white">Konfirmasi Pilihan Anda</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Apakah Anda yakin ingin memberikan suara kepada Paslon No. 0<span class="text-white font-bold" x-text="selectedCandidate ? selectedCandidate.nomor_urut : ''"></span>:
                </p>
                <div class="font-outfit font-bold text-lg text-rose-400" x-text="selectedCandidate ? selectedCandidate.nama_ketua : ''"></div>
                <div class="text-xs text-slate-300" x-show="selectedCandidate && selectedCandidate.nama_wakil">
                    &amp; <span x-text="selectedCandidate ? selectedCandidate.nama_wakil : ''"></span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs text-left flex items-start gap-2.5">
                <span class="text-base">⚠️</span>
                <span>Pilihan yang telah dikonfirmasi bersifat final dan tidak dapat diubah kembali. Token barcode Anda akan langsung dihanguskan.</span>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-2">
                <button @click="showConfirmModal = false" class="py-3 rounded-xl text-xs font-bold bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-colors">
                    Periksa Kembali
                </button>
                <button @click="submitFinalVote()" 
                        :disabled="isSubmitting"
                        class="py-3 rounded-xl text-xs font-bold gradient-red text-white uppercase tracking-wider hover:brightness-110 shadow-lg shadow-red-500/30 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <span x-show="!isSubmitting">Ya, Saya Yakin</span>
                    <span x-show="isSubmitting">Merekam...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL PERINGATAN ERROR / SUDAH MEMILIH -->
    <div x-show="showErrorModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md">
        <div class="glass-card rounded-[32px] p-8 max-w-md w-full border-red-500 text-center space-y-6 shadow-2xl bg-red-950/20">
            <div class="w-16 h-16 rounded-full bg-red-500/20 border-2 border-red-500 text-red-400 flex items-center justify-center mx-auto text-2xl font-bold">
                ✕
            </div>

            <div class="space-y-2">
                <h3 class="font-outfit font-black text-2xl text-red-400">Akses Ditolak!</h3>
                <p class="text-sm text-slate-200 leading-relaxed font-medium" x-text="errorMessage"></p>
            </div>

            <button @click="showErrorModal = false; resetToStandby()" class="w-full py-3.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-white transition-colors">
                Tutup &amp; Coba Lagi
            </button>
        </div>
    </div>

    <!-- Footer Barcode Indicator -->
    <footer class="glass-dark border-t border-white/10 px-6 py-3 flex items-center justify-between text-xs text-slate-500">
        <div>E-Voting System &bull; Single-Use Barcode Token Protection</div>
        <div class="flex items-center gap-3">
            <button @click="toggleSound()" class="hover:text-slate-300 flex items-center gap-1">
                <span x-text="soundEnabled ? '🔊 Audio Aktif' : '🔇 Audio Mute'"></span>
            </button>
        </div>
    </footer>

    <!-- Alpine.js Booth Logic -->
    <script>
        function votingBoothApp() {
            return {
                viewState: 'standby', // standby, voting, success
                currentBilik: localStorage.getItem('pilketos_bilik') || 'Bilik 1',
                barcodeCode: '',
                isLoading: false,
                isSubmitting: false,
                soundEnabled: true,

                // Sesi Pemilih
                voter: { nama: '', nis: '', kelas: '', foto: '' },
                sessionToken: '',
                kandidats: {!! json_encode($kandidatsJson ?? []) !!},
                selectedCandidate: null,
                showConfirmModal: false,

                // Error Handling
                showErrorModal: false,
                errorMessage: '',

                // Countdown
                countdown: 4,
                countdownTimer: null,

                initApp() {
                    this.focusBarcodeInput();
                },

                saveBilik() {
                    localStorage.setItem('pilketos_bilik', this.currentBilik);
                },

                focusBarcodeInput() {
                    if (this.viewState === 'standby' && this.$refs.barcodeInput) {
                        this.$nextTick(() => {
                            this.$refs.barcodeInput.focus();
                        });
                    }
                },

                // Synthetic Audio Beep via Web Audio API
                playBeep(type = 'success') {
                    if (!this.soundEnabled) return;
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        if (type === 'success') {
                            osc.frequency.setValueAtTime(880, ctx.currentTime); // High beep
                            gain.gain.setValueAtTime(0.15, ctx.currentTime);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.15);
                        } else {
                            osc.frequency.setValueAtTime(220, ctx.currentTime); // Low buzz
                            gain.gain.setValueAtTime(0.25, ctx.currentTime);
                            osc.start();
                            osc.stop(ctx.currentTime + 0.35);
                        }
                    } catch (e) {}
                },

                toggleSound() {
                    this.soundEnabled = !this.soundEnabled;
                },

                async submitBarcode() {
                    const code = this.barcodeCode.trim();
                    if (!code) return;

                    this.isLoading = true;
                    try {
                        const res = await fetch('{{ route("pilketos.booth.scan") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                barcode: code,
                                bilik_id: this.currentBilik
                            })
                        });

                        const data = await res.json();
                        this.isLoading = false;

                        if (res.ok && data.status === 'success') {
                            this.playBeep('success');
                            this.voter = data.voter;
                            this.sessionToken = data.session_token;
                            if (data.kandidats && data.kandidats.length > 0) {
                                this.kandidats = data.kandidats;
                            }
                            this.barcodeCode = '';
                            this.viewState = 'voting';
                        } else {
                            this.playBeep('error');
                            this.errorMessage = data.message || 'Barcode tidak valid.';
                            this.showErrorModal = true;
                            this.barcodeCode = '';
                        }
                    } catch (e) {
                        this.isLoading = false;
                        this.playBeep('error');
                        this.errorMessage = 'Terjadi kesalahan jaringan saat memverifikasi barcode.';
                        this.showErrorModal = true;
                    }
                },

                openConfirmModal(candidate) {
                    this.selectedCandidate = candidate;
                    this.showConfirmModal = true;
                },

                async submitFinalVote() {
                    if (!this.selectedCandidate || !this.voter.id) return;

                    this.isSubmitting = true;
                    try {
                        const res = await fetch('{{ route("pilketos.booth.submit") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                session_token: this.sessionToken,
                                voter_id: this.voter.id,
                                kandidat_id: this.selectedCandidate.id,
                                bilik_id: this.currentBilik
                            })
                        });

                        const data = await res.json();
                        this.isSubmitting = false;
                        this.showConfirmModal = false;

                        if (res.ok && data.status === 'success') {
                            this.playBeep('success');
                            this.viewState = 'success';
                            this.startSuccessCountdown();
                        } else {
                            this.playBeep('error');
                            this.errorMessage = data.message || 'Gagal merekam suara.';
                            this.showErrorModal = true;
                        }
                    } catch (e) {
                        this.isSubmitting = false;
                        this.showConfirmModal = false;
                        this.playBeep('error');
                        this.errorMessage = 'Terjadi kesalahan sistem saat mengirim surat suara.';
                        this.showErrorModal = true;
                    }
                },

                async cancelSession() {
                    if (confirm('Apakah Anda ingin membatalkan sesi pemilihan ini?')) {
                        try {
                            await fetch('{{ route("pilketos.booth.cancel") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });
                        } catch (e) {}
                        this.resetToStandby();
                    }
                },

                startSuccessCountdown() {
                    this.countdown = 4;
                    clearInterval(this.countdownTimer);
                    this.countdownTimer = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(this.countdownTimer);
                            this.resetToStandby();
                        }
                    }, 1000);
                },

                resetToStandby() {
                    clearInterval(this.countdownTimer);
                    this.viewState = 'standby';
                    this.voter = { nama: '', nis: '', kelas: '', foto: '' };
                    this.sessionToken = '';
                    this.selectedCandidate = null;
                    this.showConfirmModal = false;
                    this.barcodeCode = '';
                    this.focusBarcodeInput();
                }
            };
        }
    </script>
</body>
</html>
