<x-app-layout>
    @push('styles')
        <style>
            /* ===================== COUNTDOWN ANIMASI ===================== */
            @keyframes gradient-shift {
                0%   { background-position: 0% 50%; }
                50%  { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50%       { transform: translateY(-12px); }
            }
            @keyframes twinkle {
                0%, 100% { opacity: 1; transform: scale(1); }
                50%       { opacity: 0.4; transform: scale(0.7); }
            }
            @keyframes spin-slow {
                from { transform: rotate(0deg); }
                to   { transform: rotate(360deg); }
            }
            @keyframes bounce-in {
                0%   { transform: scale(0.3); opacity: 0; }
                50%  { transform: scale(1.05); }
                70%  { transform: scale(0.9); }
                100% { transform: scale(1); opacity: 1; }
            }
            @keyframes slide-up {
                from { transform: translateY(30px); opacity: 0; }
                to   { transform: translateY(0); opacity: 1; }
            }
            @keyframes pulse-glow-green {
                0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
                50%       { box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); }
            }
            @keyframes confetti-fall {
                0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
                100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
            }
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                20%  { transform: translateX(-5px); }
                40%  { transform: translateX(5px); }
                60%  { transform: translateX(-5px); }
                80%  { transform: translateX(5px); }
            }

            .animate-gradient-bg {
                background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #4facfe, #00f2fe);
                background-size: 400% 400%;
                animation: gradient-shift 8s ease infinite;
            }
            .animate-float { animation: float 3s ease-in-out infinite; }
            .animate-spin-slow { animation: spin-slow 20s linear infinite; }
            .animate-bounce-in { animation: bounce-in 0.8s cubic-bezier(0.36, 0.07, 0.19, 0.97) both; }
            .animate-slide-up { animation: slide-up 0.6s ease both; }
            .animate-pulse-glow-green { animation: pulse-glow-green 2s infinite; }

            /* Countdown digit box */
            .countdown-box {
                background: rgba(255,255,255,0.15);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255,255,255,0.25);
                border-radius: 20px;
                padding: 20px 24px;
                min-width: 90px;
                text-align: center;
                position: relative;
                overflow: hidden;
                transition: transform 0.3s ease;
            }
            .countdown-box:hover { transform: translateY(-4px); }
            .countdown-box::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
            }
            .countdown-digit {
                font-size: 3.5rem;
                font-weight: 900;
                color: #ffffff;
                line-height: 1;
                font-variant-numeric: tabular-nums;
                text-shadow: 0 2px 8px rgba(0,0,0,0.3);
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .countdown-label {
                font-size: 0.75rem;
                font-weight: 700;
                color: rgba(255,255,255,0.8);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                margin-top: 6px;
            }
            .countdown-separator {
                font-size: 3rem;
                font-weight: 900;
                color: rgba(255,255,255,0.6);
                animation: twinkle 1s ease-in-out infinite;
                align-self: center;
                padding-bottom: 24px;
            }

            /* Stars */
            .star {
                position: absolute;
                border-radius: 50%;
                background: white;
                animation: twinkle 2s ease-in-out infinite;
            }

            /* Result cards */
            .result-lulus {
                background: linear-gradient(135deg, #d1fae5, #a7f3d0);
                border: 2px solid #6ee7b7;
            }
            .result-tidak-lulus {
                background: linear-gradient(135deg, #fee2e2, #fecaca);
                border: 2px solid #fca5a5;
            }

            /* Confetti particle */
            .confetti-piece {
                position: fixed;
                width: 10px;
                height: 10px;
                top: -20px;
                animation: confetti-fall linear forwards;
                z-index: 9999;
                pointer-events: none;
                border-radius: 2px;
            }

            @media (max-width: 640px) {
                .countdown-digit { font-size: 2.2rem; }
                .countdown-box { min-width: 70px; padding: 14px 16px; }
                .countdown-separator { font-size: 2rem; }
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengumuman Kelulusan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">

            {{-- ==================== BUKAN KELAS XII ==================== --}}
            @if(isset($bukan_kelas_xii) && $bukan_kelas_xii)
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Tersedia</h3>
                    <p class="text-gray-500 text-sm">Halaman ini hanya tersedia untuk siswa kelas XII.</p>
                </div>

            {{-- ==================== BELUM ADA PENGUMUMAN ==================== --}}
            @elseif(!$pengumuman)
                <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-12 text-center">
                    <div class="animate-float inline-block mb-5">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Pengumuman Belum Tersedia</h3>
                    <p class="text-gray-500 text-sm">Waka Kurikulum belum menerbitkan pengumuman kelulusan. Silakan pantau halaman ini secara berkala.</p>
                </div>

            {{-- ==================== COUNTDOWN (BELUM WAKTUNYA) ==================== --}}
            @elseif(!$pengumuman->sudahDipublikasikan())
                <div class="relative min-h-[520px] animate-gradient-bg rounded-3xl overflow-hidden shadow-2xl flex flex-col items-center justify-center px-6 py-12">

                    {{-- Dekorasi bintang --}}
                    <div class="star w-1.5 h-1.5 top-[15%] left-[10%]" style="animation-delay:0s;animation-duration:2.1s"></div>
                    <div class="star w-2 h-2 top-[25%] left-[80%]" style="animation-delay:0.4s;animation-duration:1.7s"></div>
                    <div class="star w-1 h-1 top-[60%] left-[5%]" style="animation-delay:0.9s;animation-duration:2.4s"></div>
                    <div class="star w-2.5 h-2.5 top-[70%] right-[8%]" style="animation-delay:0.2s;animation-duration:1.9s"></div>
                    <div class="star w-1 h-1 top-[40%] right-[15%]" style="animation-delay:1.1s;animation-duration:2.6s"></div>
                    <div class="star w-1.5 h-1.5 top-[85%] left-[30%]" style="animation-delay:0.6s;animation-duration:2.2s"></div>

                    {{-- Lingkaran dekoratif --}}
                    <div class="absolute top-[-60px] right-[-60px] w-48 h-48 rounded-full bg-white/10 animate-spin-slow"></div>
                    <div class="absolute bottom-[-40px] left-[-40px] w-36 h-36 rounded-full bg-white/10 animate-spin-slow" style="animation-duration:30s;animation-direction:reverse"></div>

                    <div class="relative z-10 text-center">
                        {{-- Icon jam --}}
                        <div class="animate-float mb-6">
                            <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center border-2 border-white/30 shadow-lg backdrop-blur-sm">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>

                        <p class="text-white/80 text-sm font-bold uppercase tracking-widest mb-2">Pengumuman Kelulusan</p>
                        <h2 class="text-white text-2xl sm:text-3xl font-black mb-1 drop-shadow-lg">{{ $pengumuman->judul }}</h2>
                        <p class="text-white/70 text-sm mb-2">
                            Dibuka pada <span class="font-bold text-white">{{ $pengumuman->waktu_publikasi->translatedFormat('l, d F Y') }}</span>
                            pukul <span class="font-bold text-white font-mono">{{ $pengumuman->waktu_publikasi->format('H:i') }} WIB</span>
                        </p>
                        @if($pengumuman->keterangan)
                            <p class="text-white/60 text-xs mb-8 max-w-md mx-auto">{{ $pengumuman->keterangan }}</p>
                        @else
                            <div class="mb-8"></div>
                        @endif

                        {{-- COUNTDOWN --}}
                        <div class="flex items-end justify-center gap-2 sm:gap-4 flex-wrap" id="countdown-container">
                            <div class="countdown-box">
                                <div class="countdown-digit" id="cd-days">00</div>
                                <div class="countdown-label">Hari</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-box">
                                <div class="countdown-digit" id="cd-hours">00</div>
                                <div class="countdown-label">Jam</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-box">
                                <div class="countdown-digit" id="cd-minutes">00</div>
                                <div class="countdown-label">Menit</div>
                            </div>
                            <div class="countdown-separator">:</div>
                            <div class="countdown-box">
                                <div class="countdown-digit" id="cd-seconds">00</div>
                                <div class="countdown-label">Detik</div>
                            </div>
                        </div>

                        <p class="text-white/50 text-xs mt-6 font-medium">Siapkan dirimu! Hasil pengumuman akan segera terungkap ✨</p>
                    </div>
                </div>

            {{-- ==================== HASIL KELULUSAN ==================== --}}
            @else
                @php
                    $isLulus = $kelulusan && $kelulusan->status === 'lulus';
                    $isSet   = $kelulusan !== null;
                @endphp

                {{-- Confetti container (hanya jika lulus) --}}
                @if($isLulus)
                    <div id="confetti-container"></div>
                @endif

                <div class="space-y-5 animate-slide-up">

                    {{-- Banner Hasil --}}
                    <div class="relative rounded-3xl overflow-hidden shadow-xl
                        {{ $isLulus ? 'bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600' : ($isSet ? 'bg-gradient-to-br from-red-400 via-rose-500 to-pink-600' : 'bg-gradient-to-br from-gray-400 via-slate-500 to-gray-600') }}">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="absolute top-[-50px] right-[-50px] w-40 h-40 rounded-full bg-white/10"></div>
                        <div class="absolute bottom-[-30px] left-[-30px] w-28 h-28 rounded-full bg-white/10"></div>

                        <div class="relative z-10 p-8 sm:p-10 text-center">
                            @if($isLulus)
                                <div class="animate-bounce-in mb-4">
                                    <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center border-4 border-white/40 animate-pulse-glow-green">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-green-100 text-xs font-bold uppercase tracking-widest mb-1">Selamat!</p>
                                <h2 class="text-white text-3xl sm:text-4xl font-black drop-shadow-lg mb-2">LULUS</h2>
                                <p class="text-green-50 text-sm font-medium mb-1">{{ $siswa->nama_lengkap }}</p>
                                <p class="text-green-100/70 text-xs">{{ $rombelXII->kelas->nama_kelas }} &bull; {{ $tahunPelajaran->tahun }}</p>
                                @if($kelulusan->catatan)
                                    <p class="mt-4 text-green-50/80 text-sm bg-white/10 rounded-xl px-4 py-2 inline-block">
                                        {{ $kelulusan->catatan }}
                                    </p>
                                @endif
                            @elseif($isSet)
                                <div class="animate-bounce-in mb-4">
                                    <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center border-4 border-white/40">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-red-100 text-xs font-bold uppercase tracking-widest mb-1">Hasil Kelulusan</p>
                                <h2 class="text-white text-3xl sm:text-4xl font-black drop-shadow-lg mb-2">TIDAK LULUS</h2>
                                <p class="text-red-50 text-sm font-medium mb-1">{{ $siswa->nama_lengkap }}</p>
                                <p class="text-red-100/70 text-xs">{{ $rombelXII->kelas->nama_kelas }} &bull; {{ $tahunPelajaran->tahun }}</p>
                                @if($kelulusan->catatan)
                                    <p class="mt-4 text-red-50/80 text-sm bg-white/10 rounded-xl px-4 py-2 inline-block">
                                        {{ $kelulusan->catatan }}
                                    </p>
                                @endif
                            @else
                                <div class="animate-bounce-in mb-4">
                                    <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center border-4 border-white/40">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h2 class="text-white text-2xl font-black mb-2">Status Belum Ditentukan</h2>
                                <p class="text-white/70 text-sm">Hubungi Waka Kurikulum untuk informasi lebih lanjut.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Download SKL + Bagikan Kartu --}}
                    @if($isLulus)
                        {{-- SKL PDF --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col sm:flex-row items-center gap-4">
                            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h4 class="font-bold text-gray-800">Surat Keterangan Lulus (SKL)</h4>
                                <p class="text-sm text-gray-500 mt-0.5">Download surat keterangan lulus resmi kamu dalam format PDF.</p>
                            </div>
                            <a href="{{ route('siswa.pengumuman-kelulusan.download-skl') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm transition-all shadow-md hover:shadow-lg flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download SKL
                            </a>
                        </div>

                        {{-- Kartu Kelulusan Sosmed --}}
                        <a href="{{ route('siswa.pengumuman-kelulusan.kartu') }}"
                            class="group block rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300">
                            <div class="relative bg-gradient-to-br from-[#0d0d1a] via-[#1a0a2e] to-[#060614] p-6 flex flex-col sm:flex-row items-center gap-4 border border-purple-900/40">
                                {{-- Dekorasi orb di background --}}
                                <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-20 pointer-events-none"
                                    style="background:radial-gradient(circle,#a855f7,transparent 70%)"></div>
                                <div class="absolute bottom-0 left-0 w-28 h-28 rounded-full opacity-15 pointer-events-none"
                                    style="background:radial-gradient(circle,#06b6d4,transparent 70%)"></div>

                                <div class="relative z-10 w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl
                                    bg-gradient-to-br from-purple-600 to-cyan-500 shadow-lg shadow-purple-900/40">
                                    🎓
                                </div>
                                <div class="relative z-10 flex-1 text-center sm:text-left">
                                    <h4 class="font-black text-white text-base">Bagikan Kartu Kelulusan ✨</h4>
                                    <p class="text-purple-300/80 text-sm mt-0.5 font-medium">Bikin kartu kelulusan aesthetic buat di-post ke Reels, Story & TikTok!</p>
                                    <div class="flex flex-wrap gap-1.5 mt-2 justify-center sm:justify-start">
                                        <span class="text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-full">📸 Instagram</span>
                                        <span class="text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 px-2 py-0.5 rounded-full">🎵 TikTok</span>
                                        <span class="text-[10px] font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30 px-2 py-0.5 rounded-full">🎬 Reels</span>
                                        <span class="text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full">⬇ JPG 1080×1920</span>
                                    </div>
                                </div>
                                <div class="relative z-10 flex-shrink-0">
                                    <span class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-purple-600 to-cyan-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-purple-900/40 group-hover:shadow-purple-900/60 group-hover:scale-105 transition-all">
                                        Buat Kartu
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- Info Pengumuman --}}
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 p-5">
                        <h4 class="font-bold text-indigo-900 text-sm mb-3">Detail Pengumuman</h4>
                        <ul class="space-y-2 text-sm">
                            <li class="flex justify-between gap-2">
                                <span class="text-indigo-700 font-medium">Judul</span>
                                <span class="font-bold text-indigo-900 text-right">{{ $pengumuman->judul }}</span>
                            </li>
                            <li class="flex justify-between gap-2">
                                <span class="text-indigo-700 font-medium">Dipublikasikan</span>
                                <span class="font-bold text-indigo-900 text-right">{{ $pengumuman->waktu_publikasi->translatedFormat('d F Y, H:i') }} WIB</span>
                            </li>
                            <li class="flex justify-between gap-2">
                                <span class="text-indigo-700 font-medium">Tahun Pelajaran</span>
                                <span class="font-bold text-indigo-900">{{ $tahunPelajaran->tahun }}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            @if($pengumuman && !$pengumuman->sudahDipublikasikan())
            (function() {
                const targetTime = new Date('{{ $pengumuman->waktu_publikasi->toIso8601String() }}').getTime();

                function pad(n) { return String(n).padStart(2, '0'); }

                let prevSeconds = -1;

                function updateCountdown() {
                    const now = Date.now();
                    const diff = targetTime - now;

                    if (diff <= 0) {
                        // Waktu sudah habis — reload halaman untuk tampilkan hasil
                        window.location.reload();
                        return;
                    }

                    const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    document.getElementById('cd-days').textContent    = pad(days);
                    document.getElementById('cd-hours').textContent   = pad(hours);
                    document.getElementById('cd-minutes').textContent = pad(minutes);

                    const secEl = document.getElementById('cd-seconds');
                    if (seconds !== prevSeconds) {
                        secEl.style.transform = 'scale(1.15)';
                        secEl.style.color = '#fbbf24';
                        setTimeout(() => {
                            secEl.style.transform = 'scale(1)';
                            secEl.style.color = '#ffffff';
                        }, 200);
                        prevSeconds = seconds;
                    }
                    secEl.textContent = pad(seconds);
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            })();
            @endif

            @if(isset($isLulus) && $isLulus)
            (function() {
                const colors = ['#ff6b6b','#ffd93d','#6bcb77','#4d96ff','#ff922b','#cc5de8','#20c997','#f03e3e'];
                const shapes = ['▲','■','●','◆','★'];
                const container = document.getElementById('confetti-container');

                function createConfetti() {
                    for (let i = 0; i < 80; i++) {
                        setTimeout(() => {
                            const piece = document.createElement('div');
                            piece.className = 'confetti-piece';
                            piece.style.left = Math.random() * 100 + 'vw';
                            piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                            piece.style.width = (Math.random() * 8 + 6) + 'px';
                            piece.style.height = (Math.random() * 8 + 6) + 'px';
                            piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                            piece.style.animationDuration = (Math.random() * 3 + 2) + 's';
                            piece.style.animationDelay = Math.random() * 0.5 + 's';
                            container.appendChild(piece);
                            piece.addEventListener('animationend', () => piece.remove());
                        }, i * 30);
                    }
                }

                createConfetti();
                // Ulang confetti setiap 5 detik
                setInterval(createConfetti, 5000);
            })();
            @endif
        </script>
    @endpush
</x-app-layout>
