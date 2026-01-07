<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner Keterlambatan - Gate Terminal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; overflow: hidden; }
        .telkom-gradient { background: linear-gradient(135deg, #FF1F1F 0%, #C41212 100%); }
        .success-glow { box-shadow: 0 0 50px rgba(34, 197, 94, 0.4); }
        .error-glow { box-shadow: 0 0 50px rgba(239, 68, 68, 0.4); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col" x-data="scannerApp()">
    {{-- Header --}}
    <div class="telkom-gradient text-white px-12 py-6 flex justify-between items-center shadow-lg relative z-10">
        <div class="flex items-center gap-6">
            <a href="{{ route('security.terminal.index') }}" class="p-3 bg-white/20 hover:bg-white/30 rounded-2xl transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight">PENDATAAN KETERLAMBATAN</h1>
                <p class="text-red-100 opacity-90">Silakan scan kartu akses siswa</p>
            </div>
        </div>
        
        <div class="text-right">
            <div class="text-4xl font-mono font-bold" x-text="time"></div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center p-12 relative">
        {{-- Scanner Input (Hidden but focused) --}}
        <input type="text" x-ref="barcodeInput" @keydown.enter="processScan" 
            class="absolute opacity-0 pointer-events-none" autofocus>

        {{-- Scanning State --}}
        <div x-show="state === 'idle'" class="text-center animate-pulse" x-cloak>
            <div class="w-64 h-64 bg-red-50 rounded-full flex items-center justify-center mb-8 mx-auto border-4 border-red-100">
                <svg class="w-32 h-32 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <h2 class="text-4xl font-extrabold text-slate-400 uppercase tracking-widest">Menunggu Pemindaian...</h2>
        </div>

        {{-- Processing State --}}
        <div x-show="state === 'processing'" class="text-center" x-cloak>
            <div class="w-32 h-32 border-8 border-red-200 border-t-red-600 rounded-full animate-spin mx-auto mb-8"></div>
            <h2 class="text-4xl font-extrabold text-slate-600 uppercase tracking-widest">Memproses Data...</h2>
        </div>

        {{-- Success State --}}
        <div x-show="state === 'success'" class="max-w-4xl w-full bg-white rounded-[3rem] shadow-2xl p-12 text-center border-4 border-green-500 success-glow" x-cloak>
            <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-5xl font-black text-slate-800 mb-8">BERHASIL DICATAT</h2>
            
            <div class="flex items-center gap-8 bg-slate-50 p-8 rounded-[2rem] text-left">
                <div class="w-32 h-40 bg-slate-200 rounded-2xl flex items-center justify-center overflow-hidden border-2 border-slate-300">
                    <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <div class="text-red-600 font-bold text-2xl mb-1" x-text="result.siswa.nis"></div>
                    <div class="text-4xl font-black text-slate-900 mb-2 uppercase" x-text="result.siswa.nama_lengkap"></div>
                    <div class="text-2xl text-slate-500" x-text="result.siswa.rombels[0]?.kelas?.nama_kelas || '-'"></div>
                </div>
            </div>
            <p class="mt-8 text-2xl text-slate-400">Kembali ke mode scan dalam <span x-text="countdown"></span> detik...</p>
        </div>

        {{-- Error State --}}
        <div x-show="state === 'error'" class="max-w-4xl w-full bg-white rounded-[3rem] shadow-2xl p-12 text-center border-4 border-red-500 error-glow" x-cloak>
            <div class="w-24 h-24 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="text-5xl font-black text-red-600 mb-4">PENGIRIMAN GAGAL</h2>
            <p class="text-3xl text-slate-600 mb-8" x-text="errorMessage"></p>
            <button @click="resetScanner" class="bg-red-600 text-white px-12 py-6 rounded-2xl font-bold text-2xl shadow-lg hover:bg-red-700 transition-all">
                Coba Lagi
            </button>
        </div>
    </div>

    {{-- Script --}}
    <script>
        function scannerApp() {
            return {
                state: 'idle', // idle, processing, success, error
                time: '',
                result: { siswa: {} },
                errorMessage: '',
                countdown: 5,
                
                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    
                    // Keep input focused
                    document.addEventListener('click', () => {
                        this.$refs.barcodeInput.focus();
                    });
                },

                updateTime() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                async processScan(e) {
                    const nipd = e.target.value.trim();
                    if (!nipd) return;

                    this.state = 'processing';
                    e.target.value = '';

                    try {
                        const response = await fetch('{{ route('security.terminal.process-lateness') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ nipd })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.result = data;
                            this.state = 'success';
                            this.playBeep('success');
                            this.startCountdown();
                        } else {
                            this.errorMessage = data.message || 'Terjadi kesalahan sistem.';
                            this.state = 'error';
                            this.playBeep('error');
                            this.startCountdown(2); // Auto reset in 2 seconds on error
                        }
                    } catch (error) {
                        this.errorMessage = 'Gagal terhubung ke server.';
                        this.state = 'error';
                        this.startCountdown(2); // Auto reset in 2 seconds on error
                    }
                },

                resetScanner() {
                    this.state = 'idle';
                    this.errorMessage = '';
                    this.result = { siswa: {} };
                    this.$nextTick(() => this.$refs.barcodeInput.focus());
                },

                startCountdown(seconds) {
                    this.countdown = seconds || 5;
                    const timer = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(timer);
                            this.resetScanner();
                        }
                    }, 1000);
                },

                playBeep(type) {
                    // Simple tone generator
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    if (type === 'success') {
                        oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                        oscillator.start();
                        oscillator.stop(audioCtx.currentTime + 0.1);
                        
                        setTimeout(() => {
                            const osc2 = audioCtx.createOscillator();
                            osc2.connect(gainNode);
                            osc2.frequency.setValueAtTime(1108.73, audioCtx.currentTime); // C#6
                            osc2.start();
                            osc2.stop(audioCtx.currentTime + 0.2);
                        }, 100);
                    } else {
                        oscillator.frequency.setValueAtTime(220, audioCtx.currentTime); // A3
                        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                        oscillator.start();
                        oscillator.stop(audioCtx.currentTime + 0.3);
                    }
                }
            }
        }
    </script>
</body>
</html>
