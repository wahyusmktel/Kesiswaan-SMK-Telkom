<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Terminal - SMK Telkom Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }
        .telkom-gradient {
            background: linear-gradient(135deg, #FF1F1F 0%, #C41212 100%);
        }
        .telkom-text {
            color: #FF1F1F;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="overflow-hidden bg-slate-50 min-h-screen flex flex-col">
    {{-- Header --}}
    <div class="telkom-gradient text-white px-12 py-8 flex justify-between items-center shadow-lg relative z-10">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-inner">
                <img src="https://upload.wikimedia.org/wikipedia/id/thumb/4/44/Logo_Yayasan_Pendidikan_Telkom.png/600px-Logo_Yayasan_Pendidikan_Telkom.png" alt="Logo" class="w-12 h-12 object-contain">
            </div>
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight">GATE TERMINAL</h1>
                <p class="text-red-100 text-lg opacity-90">SMK Telkom Lampung</p>
            </div>
        </div>
        
        <div class="text-right" x-data="{ 
            time: '', 
            date: '',
            updateTime() {
                const now = new Date();
                this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            }
        }" x-init="updateTime(); setInterval(() => updateTime(), 1000)">
            <div class="text-5xl font-mono font-bold" x-text="time"></div>
            <div class="text-lg opacity-80" x-text="date"></div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex items-center justify-center p-12 relative overflow-hidden">
        {{-- Decorative Circles --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-red-100 rounded-full opacity-30 animate-float"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-red-100 rounded-full opacity-20 animate-float" style="animation-delay: -2s"></div>

        <div class="grid grid-cols-2 gap-16 max-w-7xl w-full relative z-10">
            {{-- Lateness Option --}}
            <a href="{{ route('security.terminal.lateness') }}" 
                class="group relative bg-white p-12 rounded-[3rem] shadow-2xl hover:shadow-[0_20px_60px_-15px_rgba(255,31,31,0.3)] transition-all duration-500 transform hover:-translate-y-4 border-2 border-transparent hover:border-red-200 overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-red-50 rounded-bl-[10rem] transition-all group-hover:bg-red-100"></div>
                
                <div class="relative">
                    <div class="w-24 h-24 telkom-gradient rounded-3xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    
                    <h2 class="text-5xl font-extrabold text-slate-800 mb-6 uppercase tracking-tight">Pendataan<br><span class="telkom-text">Keterlambatan</span></h2>
                    <p class="text-slate-500 text-2xl leading-relaxed max-w-md">Catat siswa yang datang terlambat dengan memindai kartu akses.</p>
                </div>

                <div class="mt-12 flex items-center text-red-600 font-bold text-2xl">
                    <span>Mulai Scan</span>
                    <svg class="w-8 h-8 ml-4 transform group-hover:translate-x-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>

            {{-- Permit Option --}}
            <a href="{{ route('security.terminal.permit') }}" 
                class="group relative bg-white p-12 rounded-[3rem] shadow-2xl hover:shadow-[0_20px_60px_-15px_rgba(255,31,31,0.3)] transition-all duration-500 transform hover:-translate-y-4 border-2 border-transparent hover:border-red-200 overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-red-50 rounded-bl-[10rem] transition-all group-hover:bg-red-100"></div>
                
                <div class="relative">
                    <div class="w-24 h-24 telkom-gradient rounded-3xl flex items-center justify-center mb-8 shadow-lg group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    
                    <h2 class="text-5xl font-extrabold text-slate-800 mb-6 uppercase tracking-tight">Izin Keluar<br><span class="telkom-text">& Masuk Sekolah</span></h2>
                    <p class="text-slate-500 text-2xl leading-relaxed max-w-md">Verifikasi siswa yang izin meninggalkan sekolah atau kembali.</p>
                </div>

                <div class="mt-12 flex items-center text-red-600 font-bold text-2xl">
                    <span>Mulai Scan</span>
                    <svg class="w-8 h-8 ml-4 transform group-hover:translate-x-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <div class="bg-white px-12 py-6 text-slate-400 font-medium text-lg flex justify-between items-center shadow-inner">
        <div>&copy; 2026 SMK Telkom Lampung - Stella Gate Terminal System</div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
            <span>System Active</span>
        </div>
    </div>
</body>
</html>
