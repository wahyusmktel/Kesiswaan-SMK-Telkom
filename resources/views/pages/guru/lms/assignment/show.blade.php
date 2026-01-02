<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $assignment->title }}</h3>
                            <div class="text-sm text-gray-500 mt-1">
                                Deadline: {{ $assignment->due_date ? $assignment->due_date->format('d M Y H:i') : 'Tanpa Batas' }}
                            </div>
                        </div>
                        <div class="text-right">
                             <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">
                                Poin Max: {{ $assignment->points }}
                            </span>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        {!! nl2br(e($assignment->description)) !!}
                    </div>

                    @if($assignment->file_path)
                        <div class="mt-4 p-4 bg-gray-50 rounded border">
                            <h4 class="font-semibold text-sm mb-2">Lampiran:</h4>
                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Lampiran
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Pengumpulan Siswa</h3>
                    
                    @if($assignment->submissions->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Submit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assignment->submissions as $submission)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->siswa->nama_lengkap ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->submitted_at->format('d M H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($submission->grade)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Dinilai</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Dinilai</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $submission->grade ?? '-' }} / {{ $assignment->points }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('guru.lms.submission.show', $submission->id) }}" class="text-indigo-600 hover:text-indigo-900">Detail & Nilai</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 italic">Belum ada siswa yang mengumpulkan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
