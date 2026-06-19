<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">🔍 Detail Rencana Pembelajaran</h2>
    </x-slot>

    <div class="py-6 w-full max-w-5xl mx-auto">
        <div class="px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <a href="{{ route('guru-kelas.lesson-plan.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">
                    &larr; Kembali ke Dashboard
                </a>
                <div class="flex gap-2">
                    @if($plan->status != 'done')
                        <a href="{{ route('guru-kelas.lesson-plan.edit', $plan->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit</a>
                    @endif
                    <form action="{{ route('guru-kelas.lesson-plan.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Hapus</button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <div class="mb-6 pb-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->topic }}</h3>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                            <span>📅 {{ $plan->teach_date->translatedFormat('D, d M Y') }}</span>
                            <span>⏱️ {{ $plan->duration_minutes }} menit</span>
                            <span>👨‍🏫 {{ $plan->class->nama_kelas ?? '-' }}</span>
                            <span>📖 {{ $plan->subject->nama_mapel ?? '-' }}</span>
                        </div>
                    </div>
                    <div>
                        {!! $plan->statusBadge() !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-8">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-3 border-l-4 border-blue-500 pl-3">Tujuan Pembelajaran</h4>
                            <div class="bg-blue-50 p-4 rounded-xl text-blue-900 text-sm whitespace-pre-wrap">{{ $plan->learning_objectives }}</div>
                        </div>

                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-3 border-l-4 border-indigo-500 pl-3">Aktivitas Kelas</h4>
                            <div class="space-y-3">
                                @if($plan->activities)
                                    @foreach($plan->activities as $key => $act)
                                        @if(!empty($act))
                                        <div class="bg-gray-50 p-3 rounded-lg text-sm border border-gray-100">
                                            <span class="font-bold text-gray-700 uppercase block mb-1">{{ $key }}</span>
                                            <span class="text-gray-600">{{ $act }}</span>
                                        </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-400">Tidak ada detail aktivitas.</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">Metode</h4>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($plan->methods ?? [] as $m)
                                        <span class="bg-gray-100 px-2 py-1 rounded text-xs font-medium text-gray-700">{{ $m }}</span>
                                    @empty
                                        <span class="text-xs text-gray-400">-</span>
                                    @endforelse
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-2">Media & Kebutuhan</h4>
                                <ul class="text-sm text-gray-600 list-disc list-inside">
                                    @foreach($plan->resources['needs'] ?? [] as $need)
                                        <li>{{ $need }}</li>
                                    @endforeach
                                    @if(!empty($plan->resources['link']))
                                        <li><a href="{{ $plan->resources['link'] }}" target="_blank" class="text-blue-500 hover:underline">Link Materi</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-3">To-Do Persiapan</h4>
                            <ul class="space-y-2 text-sm">
                                @forelse($plan->todos as $todo)
                                    <li class="flex items-start gap-2">
                                        <span class="{{ $todo->is_done ? 'text-green-500' : 'text-gray-300' }}">
                                            {!! $todo->is_done ? '☑' : '☐' !!}
                                        </span>
                                        <span class="{{ $todo->is_done ? 'line-through text-gray-400' : 'text-gray-700' }}">{{ $todo->todo_text }}</span>
                                    </li>
                                @empty
                                    <li class="text-gray-400 italic">Tidak ada to-do.</li>
                                @endforelse
                            </ul>
                        </div>

                        <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                            <h4 class="font-bold text-amber-800 mb-3">Refleksi Pasca Mengajar</h4>
                            @if($plan->reflection)
                                <p class="text-sm text-amber-900 whitespace-pre-wrap">{{ $plan->reflection }}</p>
                            @else
                                <form action="{{ route('guru-kelas.lesson-plan.reflect', $plan->id) }}" method="POST">
                                    @csrf
                                    <textarea name="reflection" rows="3" class="w-full text-sm rounded border-amber-200 focus:ring-amber-500 focus:border-amber-500 bg-white mb-2" placeholder="Apa yang berjalan baik? Apa yang perlu diperbaiki?" required></textarea>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Simpan Refleksi</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
