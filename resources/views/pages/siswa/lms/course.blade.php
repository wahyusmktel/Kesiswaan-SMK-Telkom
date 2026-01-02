<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $mapel->nama_mapel }} - {{ $rombel->nama_rombel }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Materials Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Materi Pelajaran</h3>
                        @if($materials->count() > 0)
                            <ul class="space-y-4">
                                @foreach($materials as $material)
                                    <li class="border rounded p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-md">{{ $material->title }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ Str::limit($material->content, 100) }}
                                                </p>
                                                @if($material->file_path)
                                                    <div class="mt-2 text-xs text-blue-500">
                                                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="inline-flex items-center hover:underline">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                            Download Lampiran
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $material->created_at->format('d M Y') }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada materi yang dibagikan.</p>
                        @endif
                    </div>
                </div>

                <!-- Assignments Section -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Tugas & PR</h3>
                        @if($assignments->count() > 0)
                            <ul class="space-y-4">
                                @foreach($assignments as $assignment)
                                    <li class="border rounded p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-md">
                                                    <a href="{{ route('siswa.lms.assignment.show', $assignment->id) }}" class="hover:text-blue-600 hover:underline">
                                                        {{ $assignment->title }}
                                                    </a>
                                                </h4>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                    Deadline: <span class="font-medium {{ $assignment->due_date < now() ? 'text-red-500' : 'text-green-500' }}">
                                                        {{ $assignment->due_date ? $assignment->due_date->format('d M Y H:i') : 'Tanpa Batas Waktu' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <a href="{{ route('siswa.lms.assignment.show', $assignment->id) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    Lihat Tugas
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">Belum ada tugas yang diberikan.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
