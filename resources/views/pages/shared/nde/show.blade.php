<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Nota Dinas</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('shared.nde.index') }}" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h3 class="text-2xl font-black text-gray-800">Baca Nota Dinas</h3>
                    <p class="text-gray-500">ID: {{ $notaDinas->nomor_nota }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden border-t-8 border-t-red-600">
                        <div class="p-8">
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-bold uppercase tracking-widest mb-2 inline-block">
                                        {{ $notaDinas->jenis->nama }}
                                    </span>
                                    <h1 class="text-3xl font-black text-gray-900 leading-tight">{{ $notaDinas->perihal }}</h1>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Tanggal</p>
                                    <p class="text-lg font-black text-gray-800">{{ \Carbon\Carbon::parse($notaDinas->tanggal)->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>

                            <div class="prose prose-red max-w-none text-gray-700 leading-relaxed whitespace-pre-line border-t border-gray-100 pt-8 min-h-[300px]">
                                {{ $notaDinas->isi }}
                            </div>

                            @if($notaDinas->lampiran)
                                <div class="mt-12 p-6 bg-gray-50 rounded-2xl border border-gray-200 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="p-3 bg-white text-red-600 rounded-xl shadow-sm border border-gray-100 font-bold">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800">Lampiran Dokumen</p>
                                            <p class="text-xs text-gray-400">Klik tombol di samping untuk mengunduh berkas.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('shared.nde.download', $notaDinas->id) }}" 
                                        class="bg-white border border-gray-200 text-gray-800 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-colors shadow-sm">
                                        Unduh Berkas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar Info --}}
                <div class="space-y-6">
                    {{-- Sender Info --}}
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">Pengirim</h4>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white text-xl font-black">
                                {{ substr($notaDinas->pengirim->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-gray-800">{{ $notaDinas->pengirim->name }}</p>
                                <p class="text-xs text-gray-500">{{ $notaDinas->pengirim->roles->pluck('name')->join(', ') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Recipients List --}}
                    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-400">Penerima & Status</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                            @foreach($notaDinas->penerimas as $penerima)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-400">
                                            {{ substr($penerima->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $penerima->name }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase font-black">{{ $penerima->roles->pluck('name')->first() }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        @if($penerima->pivot->is_read)
                                            <div class="flex flex-col items-end">
                                                <span class="text-[10px] font-bold text-green-600 uppercase">Dibaca</span>
                                                <span class="text-[8px] text-gray-400">{{ \Carbon\Carbon::parse($penerima->pivot->read_at)->diffForHumans() }}</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Belum Dibaca</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
