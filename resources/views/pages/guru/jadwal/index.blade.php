<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">Jadwal Mengajar Saya</h2>
                <p class="text-sm text-gray-500 font-medium">Semester {{ $tahunAktif->semester }} | T.P {{ $tahunAktif->tahun }}</p>
            </div>
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">Pengajar</p>
                    <p class="text-sm font-bold text-gray-700">{{ $masterGuru->nama_lengkap }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @php
                $hariUrutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $colors = [
                    'Senin' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
                    'Selasa' => 'bg-gradient-to-r from-emerald-500 to-teal-600',
                    'Rabu' => 'bg-gradient-to-r from-amber-500 to-orange-600',
                    'Kamis' => 'bg-gradient-to-r from-rose-500 to-pink-600',
                    'Jumat' => 'bg-gradient-to-r from-cyan-500 to-blue-600',
                    'Sabtu' => 'bg-gradient-to-r from-purple-500 to-violet-600',
                ];
                $bgLight = [
                    'Senin' => 'bg-blue-50/50',
                    'Selasa' => 'bg-emerald-50/50',
                    'Rabu' => 'bg-amber-50/50',
                    'Kamis' => 'bg-rose-50/50',
                    'Jumat' => 'bg-cyan-50/50',
                    'Sabtu' => 'bg-purple-50/50',
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($hariUrutan as $hari)
                    @php 
                        $jadwalHari = $jadwalGrouped->get($hari, collect());
                    @endphp
                    <div class="flex flex-col h-full bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transform transition-all hover:shadow-xl hover:-translate-y-1">
                        {{-- Header Hari --}}
                        <div class="{{ $colors[$hari] }} p-6 text-white relative">
                            <div class="absolute right-0 top-0 opacity-10 scale-150 p-4">
                                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                            </div>
                            <div class="relative z-10 flex items-center justify-between">
                                <h3 class="text-2xl font-black italic tracking-tighter uppercase">{{ $hari }}</h3>
                                <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-black border border-white/30 uppercase tracking-widest">
                                    {{ $jadwalHari->count() }} Sesi
                                </span>
                            </div>
                        </div>

                        {{-- Body Jadwal --}}
                        <div class="flex-1 p-4 {{ $bgLight[$hari] }} min-h-[300px]">
                            @forelse($jadwalHari as $jadwal)
                                <div class="mb-4 last:mb-0 bg-white rounded-2xl p-4 shadow-sm border border-gray-100 group hover:border-indigo-200 transition-colors">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2 py-0.5 rounded-full mb-1 inline-block">Jam Ke-{{ $jadwal->jam_ke }}</span>
                                            <h4 class="text-lg font-black text-gray-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ $jadwal->rombel->kelas->nama_kelas }}</h4>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-bold text-gray-400 font-mono">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                            </p>
                                            <p class="text-[10px] font-black text-gray-300">SAMPAI</p>
                                            <p class="text-xs font-bold text-gray-500 font-mono">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 pt-3 border-t border-gray-50">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-gray-600 truncate uppercase tracking-tight">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="h-full flex flex-col items-center justify-center py-10 opacity-30 grayscale">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-widest italic">Tidak Ada Jadwal</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
