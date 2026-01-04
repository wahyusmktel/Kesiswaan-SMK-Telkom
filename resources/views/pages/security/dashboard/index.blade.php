<x-app-layout>
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight italic">Dashboard Security</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Widget Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Total Verifikasi Today --}}
                <div class="rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-slate-50 via-white to-slate-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-slate-800 uppercase tracking-widest">Total Operasional Hari Ini</p>
                            <h3 class="mt-2 text-3xl font-black text-slate-900">{{ $totalVerifikasiHariIni }}</h3>
                            <p class="text-xs text-slate-500 mt-1">Verifikasi Keluar / Kembali</p>
                        </div>
                        <div class="p-3 bg-white/80 backdrop-blur rounded-xl text-slate-700 shadow-sm border border-slate-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Siswa Sedang Di Luar --}}
                <div class="rounded-2xl p-6 border border-blue-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-blue-50 via-white to-blue-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-blue-800 uppercase tracking-widest">Siswa Sedang Di Luar</p>
                            <h3 class="mt-2 text-3xl font-black text-blue-900">{{ $siswaDiLuar }}</h3>
                            <p class="text-xs text-blue-500 mt-1">Menunggu Verifikasi Kembali</p>
                        </div>
                        <div class="p-3 bg-white/80 backdrop-blur rounded-xl text-blue-700 shadow-sm border border-blue-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Pendataan Terlambat --}}
                <div class="rounded-2xl p-6 border border-orange-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-orange-50 via-white to-orange-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-orange-800 uppercase tracking-widest">Siswa Terlambat Hari Ini</p>
                            <h3 class="mt-2 text-3xl font-black text-orange-900">{{ $terlambatHariIni }}</h3>
                            <p class="text-xs text-orange-500 mt-1">Dicatat di Gerbang</p>
                        </div>
                        <div class="p-3 bg-white/80 backdrop-blur rounded-xl text-orange-700 shadow-sm border border-orange-100">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-slate-900 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-transparent opacity-50"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-6 bg-blue-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-white tracking-tight uppercase">Aksi Cepat Keamanan</h3>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('security.verifikasi.scan') }}" 
                           class="flex flex-col items-center justify-center p-6 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 hover:scale-105 transition-all group/btn shadow-xl">
                            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-4 group-hover/btn:rotate-12 transition-transform shadow-lg shadow-blue-600/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 18.5v.01M12 10.5v.01M16 18.5v.01M16 14.5v.01M16 10.5v.01M8 18.5v.01M8 14.5v.01M8 10.5v.01M4 11l.001-.001M4 15l.001-.001M4 19l.001-.001M20 19l.001-.001M20 15l.001-.001M20 11l.001-.001" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-white">SCAN QR CODE</span>
                        </a>

                        <a href="{{ route('security.pendataan-terlambat.index') }}" 
                           class="flex flex-col items-center justify-center p-6 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 hover:scale-105 transition-all group/btn shadow-xl">
                            <div class="w-14 h-14 bg-orange-600 rounded-2xl flex items-center justify-center text-white mb-4 group-hover/btn:rotate-12 transition-transform shadow-lg shadow-orange-600/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-white">DATA TERLAMBAT</span>
                        </a>

                        <a href="{{ route('security.verifikasi.index') }}" 
                           class="flex flex-col items-center justify-center p-6 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 hover:scale-105 transition-all group/btn shadow-xl">
                            <div class="w-14 h-14 bg-slate-600 rounded-2xl flex items-center justify-center text-white mb-4 group-hover/btn:rotate-12 transition-transform shadow-lg shadow-slate-600/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-white">VERIFIKASI MANUAL</span>
                        </a>

                        <a href="{{ route('security.verifikasi.riwayat') }}" 
                           class="flex flex-col items-center justify-center p-6 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl hover:bg-white/20 hover:scale-105 transition-all group/btn shadow-xl">
                            <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mb-4 group-hover/btn:rotate-12 transition-transform shadow-lg shadow-indigo-600/30">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-white">RIWAYAT IZIN</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Izin --}}
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[450px]">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                            <h4 class="font-black text-slate-800 text-sm uppercase tracking-wider">Aktivitas Verifikasi Izin</h4>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 custom-scrollbar">
                        @forelse ($recentIzin as $izin)
                            <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-50 hover:bg-slate-50 transition-colors group">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 font-black shrink-0">
                                    {{ substr($izin->siswa->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h5 class="font-bold text-slate-900 leading-tight">{{ $izin->siswa->name }}</h5>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $izin->security_verified_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 italic">"{{ Str::limit($izin->tujuan, 35) }}"</p>
                                    <div class="mt-2 text-[10px] font-black uppercase tracking-widest text-blue-600">
                                        {{ str_replace('_', ' ', $izin->status) }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-slate-300">
                                <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm italic">Belum ada verifikasi izin hari ini.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Terlambat --}}
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[450px]">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 bg-orange-600 rounded-full"></div>
                            <h4 class="font-black text-slate-800 text-sm uppercase tracking-wider">Pendataan Terlambat Terbaru</h4>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 custom-scrollbar">
                        @forelse ($recentTerlambat as $terlambat)
                            <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-50 hover:bg-slate-50 transition-colors">
                                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700 font-black shrink-0 text-xs">
                                    {{ $terlambat->siswa->rombels->first()?->kelas->nama_kelas ?? '?' }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h5 class="font-bold text-slate-900 leading-tight">{{ $terlambat->siswa->nama_lengkap }}</h5>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $terlambat->waktu_dicatat_security->format('H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 italic">"{{ Str::limit($terlambat->alasan_siswa, 35) }}"</p>
                                    <div class="mt-2 text-[10px] font-black uppercase tracking-widest text-slate-500 flex items-center gap-1">
                                        <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                                        {{ $terlambat->status }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-slate-300">
                                <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm italic">Belum ada data terlambat hari ini.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
