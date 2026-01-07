<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Terminal - Security Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #000;
        }
        .telkom-gradient {
            background: linear-gradient(135deg, #FF1F1F 0%, #B00000 100%);
        }
        .animate-float-slow {
            animation: float 20s ease-in-out infinite;
        }
        .animate-float-fast {
            animation: float 12s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -40px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        input:focus {
            box-shadow: 0 0 0 8px rgba(255, 31, 31, 0.15);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 0px;
        }
    </style>
</head>
<body class="min-h-screen relative overflow-hidden text-slate-800" x-data="{ loading: false }">
    
    {{-- Animated Background Objects --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-[80rem] h-[80rem] bg-red-600/10 rounded-full blur-[120px] animate-float-slow"></div>
        <div class="absolute -bottom-1/4 -left-1/4 w-[60rem] h-[60rem] bg-red-800/20 rounded-full blur-[100px] animate-float-fast"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col lg:flex-row">
        
        {{-- Left Section: Branding & Info --}}
        <div class="lg:w-7/12 telkom-gradient p-12 lg:p-24 flex flex-col justify-between text-white relative overflow-hidden">
            {{-- Decorative Grid --}}
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            
            {{-- Top part --}}
            <div class="relative">
                <div class="flex items-center gap-6 mb-16">
                    <div class="w-24 h-24 bg-white rounded-3xl p-4 shadow-2xl transform shadow-red-900/40">
                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/4/44/Logo_Yayasan_Pendidikan_Telkom.png/600px-Logo_Yayasan_Pendidikan_Telkom.png" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h2 class="text-3xl font-black uppercase tracking-widest text-red-50 mb-1">SMK TELKOM</h2>
                        <h3 class="text-xl font-bold opacity-80 tracking-widest uppercase">LAMPUNG</h3>
                    </div>
                </div>

                <div class="space-y-4">
                    <h1 class="text-7xl lg:text-9xl font-black tracking-tighter uppercase leading-tight">
                        GATE<br>TERMINAL
                    </h1>
                    <div class="h-4 w-48 bg-white/20 rounded-full">
                        <div class="h-full w-1/3 bg-white rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>

            {{-- Bottom part: Clock --}}
            <div class="relative" x-data="{ 
                time: '', 
                date: '',
                updateTime() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    this.date = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                }
            }" x-init="updateTime(); setInterval(() => updateTime(), 1000)">
                <div class="text-[12rem] font-black leading-none tracking-tighter mb-4" x-text="time"></div>
                <div class="text-4xl font-bold opacity-80 uppercase tracking-widest" x-text="date"></div>
                
                <div class="mt-16 flex items-center gap-6 p-6 bg-white/10 rounded-3xl backdrop-blur-md border border-white/10 max-w-xl">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="text-xl font-bold tracking-wide italic">"Keamanan adalah prioritas utama kami."</div>
                </div>
            </div>
        </div>

        {{-- Right Section: Login Form --}}
        <div class="lg:w-5/12 bg-white flex flex-col items-center justify-center p-12 lg:p-24 shadow-[-50px_0_100px_rgba(0,0,0,0.2)] relative z-20">
            <div class="w-full max-w-2xl">
                <div class="mb-16">
                    <h2 class="text-5xl font-black text-slate-900 mb-4 tracking-tight uppercase">Login Petugas</h2>
                    <p class="text-2xl text-slate-500 font-medium tracking-wide">Gunakan akun security untuk mengakses terminal.</p>
                </div>

                <form action="{{ route('security.login.submit') }}" method="POST" class="space-y-10" @submit="loading = true">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border-4 border-red-100 p-8 rounded-[2.5rem] flex items-center gap-6 animate-shake mb-12 shadow-lg shadow-red-100/50">
                            <div class="w-16 h-16 bg-red-500 rounded-2xl flex items-center justify-center shrink-0">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <span class="font-black text-2xl text-red-600 uppercase tracking-tight">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <div class="space-y-12">
                        {{-- Email Input --}}
                        <div class="space-y-4">
                            <label for="email" class="text-xl font-black text-slate-400 uppercase tracking-[0.2em] ml-2 block">Email Address</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-10 flex items-center pointer-events-none text-slate-300 group-focus-within:text-red-500 transition-colors">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" required autofocus
                                    class="w-full pl-24 pr-10 py-9 bg-slate-50 border-4 border-slate-100 rounded-[3rem] text-3xl font-black text-slate-800 placeholder:text-slate-200 focus:bg-white focus:border-red-500 transition-all outline-none"
                                    placeholder="mail@smktelkom.id">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="space-y-4">
                            <label for="password" class="text-xl font-black text-slate-400 uppercase tracking-[0.2em] ml-2 block">Secret Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-10 flex items-center pointer-events-none text-slate-300 group-focus-within:text-red-500 transition-colors">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="w-full pl-24 pr-10 py-9 bg-slate-50 border-4 border-slate-100 rounded-[3rem] text-3xl font-black text-slate-800 placeholder:text-slate-200 focus:bg-white focus:border-red-500 transition-all outline-none"
                                    placeholder="••••••••••••">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-6">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="remember" class="sr-only peer">
                                <div class="w-12 h-12 bg-slate-100 rounded-2xl peer-checked:bg-red-500 transition-colors border-4 border-transparent"></div>
                                <svg class="w-8 h-8 text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-4 text-2xl font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Ingat Perangkat</span>
                        </label>
                        <a href="{{ route('login') }}" class="text-2xl font-bold text-slate-400 hover:text-red-600 transition-colors underline decoration-2 underline-offset-8">Portal Umum</a>
                    </div>

                    <button type="submit" 
                        class="w-full telkom-gradient py-10 rounded-[3rem] text-white text-4xl font-black uppercase tracking-[0.2em] shadow-[0_30px_60px_-15px_rgba(255,31,31,0.5)] hover:shadow-[0_40px_80px_-20px_rgba(255,31,31,0.6)] transform hover:-translate-y-2 transition-all active:scale-95 disabled:opacity-70 disabled:pointer-events-none mt-12"
                        :disabled="loading">
                        <span x-show="!loading">MASUK TERMINAL</span>
                        <div x-show="loading" class="flex items-center justify-center gap-4">
                            <svg class="animate-spin h-10 w-10 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>OTENTIKASI...</span>
                        </div>
                    </button>
                    
                    <div class="pt-12 text-center text-slate-300 text-xl font-bold uppercase tracking-widest flex items-center justify-center gap-4">
                        <div class="h-0.5 w-12 bg-slate-100"></div>
                        STELLA GATE MANAGEMENT SYSTEM v2.0
                        <div class="h-0.5 w-12 bg-slate-100"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
