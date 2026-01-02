@extends('pages.docs.layout')

@section('title', 'Pusat Panduan')

@section('content')
<div class="space-y-12">
    <div class="text-center space-y-4">
        <h1 class="text-4xl md:text-5xl font-black text-gradient">Portal Panduan Penggunaan</h1>
        <p class="text-slate-400 max-w-2xl mx-auto font-medium">Temukan langkah-langkah penggunaan seluruh fitur SISFO SMK Telkom Lampung berdasarkan peran role Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8">
        <!-- Guru Piket -->
        <a href="{{ route('docs.piket') }}" class="glass-card p-8 rounded-[32px] group hover:bg-white/10 transition-all border border-white/10 hover:border-red-500/50">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 bg-red-600/10 rounded-2xl flex items-center justify-center group-hover:bg-red-600/20 transition-colors">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-red-500 bg-red-500/10 px-3 py-1 rounded-full">Tersedia</span>
            </div>
            <div class="mt-6 space-y-2">
                <h3 class="text-xl font-bold group-hover:text-red-500 transition-colors">Role: Guru Piket</h3>
                <p class="text-sm text-slate-400 leading-relaxed font-medium">Panduan lengkap mengenai penanganan keterlambatan, monitoring izin keluar, dan persetujuan izin guru.</p>
            </div>
        </a>

        <!-- Coming Soon Generic -->
        <div class="glass-card p-8 rounded-[32px] border border-white/5 opacity-60">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 bg-slate-600/10 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 bg-slate-500/10 px-3 py-1 rounded-full">Segera Hadir</span>
            </div>
            <div class="mt-6 space-y-2">
                <h3 class="text-xl font-bold italic">Role Lainnya</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium font-outfit uppercase tracking-tighter">Dalam Proses Penyusunan Tim IT SMK Telkom Lampung</p>
            </div>
        </div>
    </div>
</div>
@endsection
