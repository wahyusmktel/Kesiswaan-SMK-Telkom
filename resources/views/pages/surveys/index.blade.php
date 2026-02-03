<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Survey Kepuasan</h2>
    </x-slot>

    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Survey Kepuasan</h1>
                <p class="text-sm text-slate-500">Kelola kuesioner dan kumpulkan feedback.</p>
            </div>
            <a href="{{ route('surveys.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Survei Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($surveys as $survey)
                @php
                    $isCreator = $survey->created_by === auth()->id();
                    $hasResponded = $survey->responses->isNotEmpty();
                    $scheduleStatus = $survey->schedule_status;
                    $isOpen = $survey->isOpen();
                @endphp
                <div
                    class="bg-white rounded-2xl shadow-sm border {{ $hasResponded ? 'border-green-100' : 'border-slate-100' }} overflow-hidden hover:shadow-md transition-shadow duration-300 flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2 {{ $hasResponded ? 'bg-green-50' : 'bg-blue-50' }} rounded-lg">
                                <svg class="w-6 h-6 {{ $hasResponded ? 'text-green-600' : 'text-blue-600' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="flex flex-col items-end space-y-1">
                                @if($hasResponded)
                                    <span
                                        class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-full bg-green-100 text-green-700">Selesai</span>
                                @elseif(!$isCreator)
                                    @if($scheduleStatus === 'upcoming')
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-full bg-indigo-100 text-indigo-700">Akan Datang</span>
                                    @elseif($scheduleStatus === 'expired')
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-full bg-red-100 text-red-700">Berakhir</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-full bg-orange-100 text-orange-700">Belum Diisi</span>
                                    @endif
                                @endif

                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest rounded-full {{ $survey->is_active ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $survey->is_active ? 'Aktif' : 'Draft' }}
                                </span>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 mb-2 truncate" title="{{ $survey->title }}">{{ $survey->title }}</h3>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2 h-10">
                            {{ $survey->description ?? 'Tidak ada deskripsi.' }}</p>

                        <div class="flex items-center text-xs font-medium text-slate-400 mb-6">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Oleh: {{ $survey->creator->name }}
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        @if($isCreator)
                            <a href="{{ route('surveys.results', $survey) }}"
                                class="text-blue-600 hover:text-blue-700 font-bold text-xs flex items-center">
                                Lihat Hasil
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <div class="flex space-x-1">
                                @if(!$survey->is_active)
                                    <a href="{{ route('surveys.edit', $survey) }}" title="Edit Draft" class="p-1.5 text-slate-400 hover:text-amber-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.243 3.757a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17.657 3.757z" /></svg>
                                    </a>
                                @endif
                                <a href="{{ route('surveys.show', $survey) }}" title="Link Survey"
                                    class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                                    </svg>
                                </a>
                                <form action="{{ route('surveys.destroy', $survey) }}" method="POST"
                                    onsubmit="return confirm('Hapus survei?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @elseif(!$hasResponded)
                            @if($isOpen)
                                <a href="{{ route('surveys.show', $survey) }}"
                                    class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-xl font-bold text-xs transition-colors">
                                    Isi Survei Sekarang
                                </a>
                            @elseif($scheduleStatus === 'upcoming')
                                <div class="w-full py-2 bg-slate-100 text-slate-400 text-center rounded-xl font-bold text-xs cursor-not-allowed" title="Mulai pada: {{ $survey->start_at->format('d M Y H:i') }}">
                                    Segera Hadir
                                </div>
                            @else
                                <div class="w-full py-2 bg-slate-100 text-slate-400 text-center rounded-xl font-bold text-xs cursor-not-allowed">
                                    Waktu Habis
                                </div>
                            @endif
                        @else
                            <span class="w-full py-2 bg-slate-100 text-slate-400 text-center rounded-xl font-bold text-xs">
                                Sudah Diisi
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum ada survei</h3>
                    <p class="text-slate-500 max-w-sm mx-auto">Anda belum membuat survei apapun. Klik tombol "Buat Survei
                        Baru" untuk memulai.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $surveys->links() }}
        </div>
    </div>
</x-app-layout>