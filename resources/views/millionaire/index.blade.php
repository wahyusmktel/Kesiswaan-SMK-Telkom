@extends('notted.app')

@section('content')
<div class="col-span-1 lg:col-span-9 flex flex-col gap-6">
    <!-- Header -->
    <div class="bg-indigo-600 rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-200">
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-black mb-2 tracking-tight">Who Wants to Be a <span class="text-amber-400">Millionaire!</span></h1>
            <p class="text-indigo-100 font-medium max-w-xl">Uji wawasanmu dan raih posisi puncak! Pilih kategori soal yang ingin kamu mainkan hari ini.</p>
        </div>
        <!-- Decoration -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute right-10 top-1/2 -translate-y-1/2 opacity-20">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.58-1.05-2.88-2.65-3.29V5h-2v1.35c-1.51.33-2.72 1.33-2.72 2.8 0 1.9 1.57 2.84 3.86 3.39 1.97.47 2.38 1.15 2.38 1.83 0 .5-.21 1.25-2.19 1.25-1.65 0-2.32-.73-2.42-1.64h-1.72c.1 1.89 1.38 3 3.14 3.4v1.6h2v-1.6c1.64-.31 2.82-1.32 2.82-2.72 0-2.2-1.85-2.93-3.83-3.41z"/>
            </svg>
        </div>
    </div>

    @if(session('active_role') == 'Guru Kelas')
    <div class="flex justify-between items-center px-4">
        <h2 class="text-lg font-bold text-slate-800 uppercase tracking-widest">Kategori Tersedia</h2>
        <a href="{{ route('notted.millionaire.manage.index') }}" class="flex items-center gap-2 px-6 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold text-xs hover:bg-slate-50 transition-all shadow-sm">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Kelola Soal
        </a>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($sets as $set)
        <div class="bg-white rounded-[32px] p-6 border border-slate-200 shadow-sm hover:border-indigo-400 hover:shadow-xl transition-all group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">{{ $set->name }}</h3>
                <p class="text-sm text-slate-500 line-clamp-2 mb-4">{{ $set->description }}</p>
                <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6 border-y border-slate-50 py-3">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $set->questions_count }} Pertanyaan
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ $set->user->name }}
                    </span>
                </div>
            </div>
            
            <a href="{{ route('notted.millionaire.play', $set->id) }}" 
               class="w-full py-4 notted-gradient text-white rounded-2xl text-xs font-black uppercase tracking-widest text-center shadow-lg shadow-indigo-100 group-hover:shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                Mulai Game
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                </svg>
            </a>
        </div>
        @empty
        <div class="col-span-1 md:col-span-3 bg-white p-12 rounded-[40px] border-2 border-dashed border-slate-200 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada kategori soal</h3>
            <p class="text-sm text-slate-500">Guru Kelas akan segera menambahkan kategori soal untuk dimainkan.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
