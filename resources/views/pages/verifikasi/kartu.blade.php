<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Kartu Pelajar - SMK Telkom Lampung</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .telkom-gradient {
            background: linear-gradient(135deg, #FF0000 0%, #B20000 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 py-12">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-24 -left-24 w-96 h-96 telkom-gradient rounded-full blur-3xl opacity-10"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 telkom-gradient rounded-full blur-3xl opacity-10"></div>
    </div>

    <div class="w-full max-w-lg">
        <div class="glass-panel rounded-[2.5rem] overflow-hidden shadow-2xl">
            <!-- Header Status -->
            <div class="telkom-gradient p-8 text-center text-white">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4 ring-8 ring-white/10">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight mb-2 uppercase">Verified / Sah</h1>
                <p class="text-white/80 font-medium tracking-wide">Kartu Pelajar ini asli dan terdaftar di sistem sekolah.</p>
            </div>

            <!-- Content Body -->
            <div class="p-8 md:p-10 space-y-8">
                <!-- Info Section -->
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl overflow-hidden shadow-xl border-4 border-white bg-gray-100 flex-shrink-0">
                        @if($siswa->foto)
                            <img src="{{ asset('storage/'.$siswa->foto) }}" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=FF0000&color=fff&size=200" alt="{{ $siswa->nama_lengkap }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Nama Lengkap</p>
                        <h2 class="text-xl md:text-2xl font-black text-gray-900 leading-tight mb-4 truncate">{{ $siswa->nama_lengkap }}</h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">NIS</p>
                                <p class="text-sm font-bold text-gray-900 tracking-wider">{{ $siswa->nis }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-1">Kelas</p>
                                <p class="text-sm font-bold text-gray-900">{{ $rombel?->kelas?->nama_kelas ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Info -->
                <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-100">
                    <div class="flex items-center gap-4 mb-4">
                        @if($settings->logo)
                            <img src="{{ asset('storage/'.$settings->logo) }}" class="w-10 h-10 object-contain">
                        @else
                            <img src="{{ asset('images/logo.png') }}" class="w-10 h-10 object-contain" onerror="this.src='https://ui-avatars.com/api/?name=ST&background=FF0000&color=fff'">
                        @endif
                        <div>
                            <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">{{ $settings->school_name ?? 'SMK Telkom Lampung' }}</h3>
                            <p class="text-[10px] text-gray-500 font-medium">Verified Identity Management System</p>
                        </div>
                    </div>
                    <p class="text-[11px] leading-relaxed text-gray-500 font-medium italic">
                        "{{ $settings->address ?? 'Eksplorasi Masa Depan Anda Bersama Kami. SMK Telkom Lampung berkomitmen untuk memberikan layanan pendidikan terbaik.' }}"
                    </p>
                </div>

                <!-- Footer Info -->
                <div class="text-center pt-2">
                    <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold">Terverifikasi pada {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="/" class="inline-flex items-center text-sm font-bold text-red-600 hover:text-red-700 transition-colors uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
