<x-app-layout>
    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
            
            .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }

            @keyframes gradient-xy {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }

            /* Flip Card Logic */
            .flip-card { perspective: 2000px; }
            .flip-card-inner {
                transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
                transform-style: preserve-3d;
            }
            .flip-card.flipped .flip-card-inner { transform: rotateY(180deg); }
            .flip-card-front, .flip-card-back {
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
            }
            .flip-card-back { transform: rotateY(180deg); }

            @media print {
                body * { visibility: hidden; }
                #card-front, #card-back, #card-front *, #card-back * { visibility: visible; }
                #card-front { position: absolute; left: 0; top: 0; width: 85mm; height: 54mm; }
                #card-back { position: absolute; left: 0; top: 60mm; width: 85mm; height: 54mm; }
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-jakarta font-extrabold text-xl text-slate-800 leading-tight uppercase tracking-tight">Identitas Digital</h2>
    </x-slot>

    <div class="py-8 w-full font-jakarta" x-data="{ isFlipped: false }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="relative rounded-[2rem] bg-gradient-to-br from-red-600 via-red-700 to-slate-900 shadow-2xl overflow-hidden p-8 sm:p-10 animate-gradient border border-white/10">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-white/5 transform skew-x-12 blur-3xl"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                    <div class="text-white text-center md:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-sm text-[10px] font-bold uppercase tracking-[0.2em] mb-3 border border-white/10">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            Status: Terverifikasi Aktif
                        </div>
                        <h3 class="text-3xl sm:text-4xl font-black tracking-tight uppercase">
                            {{ $settings->school_name ?? 'Kartu Pelajar Digital' }}
                        </h3>
                        <p class="mt-2 text-red-100 text-lg font-medium opacity-90 italic">
                            Akses identitas resmi dalam satu genggaman.
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-3">
                        <button onclick="downloadPNG()" class="group flex items-center px-6 py-4 bg-white text-slate-900 font-extrabold rounded-2xl shadow-xl hover:bg-slate-50 transition-all transform hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            Download PNG
                        </button>
                        <button onclick="window.print()" class="group flex items-center px-6 py-4 bg-slate-800 text-white font-extrabold rounded-2xl shadow-xl hover:bg-slate-700 transition-all transform hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Cetak Fisik
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center gap-6">
                <div class="flip-card w-full max-w-[420px] aspect-[1.58/1] cursor-pointer" @click="isFlipped = !isFlipped" :class="{ 'flipped': isFlipped }">
                    <div class="flip-card-inner relative w-full h-full shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] rounded-[2rem]">
                        
                        <div id="card-front" class="flip-card-front absolute inset-0 w-full h-full rounded-[2rem] overflow-hidden bg-red-700 text-white border border-white/20 backface-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-600 via-red-700 to-slate-900"></div>
                            <div class="absolute inset-0 opacity-10" style="background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>

                            <div class="relative h-full p-6 flex flex-col justify-between">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-lg">
                                            <img src="{{ $settings->logo ? asset('storage/'.$settings->logo) : 'https://ui-avatars.com/api/?name=ST&background=FF0000&color=fff' }}" class="max-h-full">
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-xs font-black uppercase tracking-widest leading-none truncate">{{ $settings->school_name ?? 'SMK TELKOM' }}</h3>
                                            <p class="text-[7px] font-bold text-white/50 uppercase tracking-widest mt-1">Student Identity Card</p>
                                        </div>
                                    </div>
                                    <div class="px-3 py-1 bg-white/10 rounded-full text-[8px] font-black uppercase tracking-widest border border-white/10">Official</div>
                                </div>

                                <div class="flex items-center gap-5">
                                    <div class="shrink-0 relative">
                                        <div class="absolute -inset-1 bg-white/20 blur-sm rounded-2xl"></div>
                                        <div class="relative w-24 h-32 rounded-2xl overflow-hidden border-2 border-white shadow-xl bg-slate-100">
                                            <img src="{{ $siswa->foto ? asset('storage/'.$siswa->foto) : 'https://ui-avatars.com/api/?name='.urlencode($siswa->nama_lengkap) }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-3">
                                        <div>
                                            <p class="text-[8px] font-black uppercase tracking-widest text-white/40 mb-1">Nama Lengkap</p>
                                            <p class="text-base font-black uppercase leading-tight truncate">{{ $siswa->nama_lengkap }}</p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <p class="text-[8px] font-black uppercase tracking-widest text-white/40 mb-0.5">NIS</p>
                                                <p class="text-[10px] font-bold tracking-wider">{{ $siswa->nis }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[8px] font-black uppercase tracking-widest text-white/40 mb-0.5">Kelas</p>
                                                <p class="text-[10px] font-bold uppercase">{{ $rombel?->kelas?->nama_kelas ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center border-t border-white/10 pt-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                        <span class="text-[8px] font-black uppercase tracking-widest opacity-70 italic">Digital Signature Verified</span>
                                    </div>
                                    <p class="text-[8px] font-black opacity-30 tracking-tighter uppercase">{{ date('Y') }}/ID-SYS</p>
                                </div>
                            </div>
                        </div>

                        <div id="card-back" class="flip-card-back absolute inset-0 w-full h-full rounded-[2rem] overflow-hidden bg-slate-900 text-white backface-hidden border border-slate-700">
                            <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-black"></div>
                            <div class="relative h-full p-8 flex flex-col justify-between">
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-0.5 bg-red-600"></div>
                                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-red-500">Notice & Policy</h4>
                                    </div>
                                    <p class="text-[10px] leading-relaxed text-slate-400 font-medium italic">"Kartu ini adalah properti resmi {{ $settings->school_name ?? 'sekolah' }}. Wajib digunakan untuk proses presensi dan perizinan masuk/keluar area sekolah."</p>
                                </div>

                                <div class="flex items-end justify-between gap-4">
                                    <div class="flex-1 space-y-4">
                                        <div class="space-y-1">
                                            <p class="text-[9px] font-black text-white tracking-widest uppercase">Office Address</p>
                                            <p class="text-[8px] text-slate-500 leading-tight">
                                                {{ $settings->address ?? 'Lampung, Indonesia' }}
                                            </p>
                                        </div>
                                        <img src="{{ $settings->logo ? asset('storage/'.$settings->logo) : 'https://ui-avatars.com/api/?name=ST' }}" class="h-6 grayscale opacity-20">
                                    </div>
                                    <div class="shrink-0 text-center space-y-2">
                                        <div class="bg-white p-2 rounded-2xl shadow-2xl border-4 border-slate-800">
                                            {!! QrCode::size(80)->margin(0)->generate(route('verifikasi.kartu', $siswa->nis)) !!}
                                        </div>
                                        <p class="text-[7px] font-black text-slate-500 uppercase tracking-widest leading-none">Scan To Verify</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] animate-bounce">Tap Kartu Untuk Memutar</p>
            </div>

            <div class="bg-white border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] rounded-[2rem] overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                    <div>
                        <h3 class="font-black text-slate-800 text-lg uppercase tracking-tight">Informasi Penggunaan</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Panduan identitas digital siswa</p>
                    </div>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center shrink-0 border border-red-100">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m5-4.5h1m-9 0h1m3 0a2 2 0 11-4 0 2 2 0 014 0zM4 12H3m15 0h1M5 5l.7.7m12.6 12.6l.7.7M17 5l-.7.7M6.3 17.7l-.7.7" /></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 uppercase tracking-tight text-sm">Validasi QR Code</h4>
                            <p class="text-xs text-slate-500 leading-relaxed mt-1 font-medium">QR Code di sisi belakang kartu terhubung langsung ke database pusat untuk memvalidasi status keaktifan Anda oleh petugas keamanan atau guru.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 uppercase tracking-tight text-sm">Download High-Res</h4>
                            <p class="text-xs text-slate-500 leading-relaxed mt-1 font-medium">Fitur download akan menghasilkan file PNG berkualitas tinggi (300 DPI) yang siap digunakan untuk kebutuhan cetak kartu fisik mandiri.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        async function downloadPNG() {
            const front = document.getElementById('card-front');
            const back = document.getElementById('card-back');
            const style = document.createElement('style');
            style.innerHTML = `
                .flip-card-inner { transform: none !important; transition: none !important; }
                .flip-card-front, .flip-card-back { backface-visibility: visible !important; position: static !important; display: block !important; transform: none !important; }
                .flip-card-back { margin-top: 40px; }
            `;
            document.head.appendChild(style);

            try {
                const config = { scale: 5, useCORS: true, backgroundColor: null };
                const canvasFront = await html2canvas(front, config);
                const canvasBack = await html2canvas(back, config);
                
                const link = (canvas, side) => {
                    const l = document.createElement('a');
                    l.download = `ID_CARD_${side}_{{ $siswa->nis }}.png`;
                    l.href = canvas.toDataURL('image/png');
                    l.click();
                };

                link(canvasFront, 'FRONT');
                setTimeout(() => link(canvasBack, 'BACK'), 500);
            } finally {
                document.head.removeChild(style);
            }
        }
    </script>
    @endpush
</x-app-layout>