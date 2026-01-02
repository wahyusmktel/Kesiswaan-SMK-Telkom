<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('LMS - Ruang Belajar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Kelas yang Anda Ajar</h3>

                    @if($teachingSchedules->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teachingSchedules as $schedule)
                                <a href="{{ route('guru.lms.course.show', ['rombel' => $schedule->rombel->id, 'mapel' => $schedule->mataPelajaran->id]) }}" 
                                   class="block bg-gray-50 dark:bg-gray-700 p-4 rounded shadow hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <div class="font-bold text-lg text-indigo-600 dark:text-indigo-400">
                                        {{ $schedule->mataPelajaran->nama_mapel }}
                                    </div>
                                    <div class="text-gray-600 dark:text-gray-300">
                                        Kelas: {{ $schedule->rombel->kelas->nama_kelas }} ({{ $schedule->rombel->nama_rombel }})
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500">
                                        Tahun Ajaran: {{ $schedule->rombel->tahun_ajaran }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">Belum ada jadwal pelajaran yang ditentukan untuk Anda.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
