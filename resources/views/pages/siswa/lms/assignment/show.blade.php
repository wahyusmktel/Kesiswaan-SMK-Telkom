<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assignment Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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

                        <div class="prose max-w-none text-sm">
                            {!! nl2br(e($assignment->description)) !!}
                        </div>

                        @if($assignment->file_path)
                            <div class="mt-4 p-4 bg-gray-50 rounded border">
                                <h4 class="font-semibold text-sm mb-2">Lampiran Soal/Materi:</h4>
                                <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download Lampiran
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submission Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Status Pengumpulan</h3>

                        @if($submission)
                            <div class="mb-6 border-b pb-6">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold w-1/3">Status:</span>
                                    @if($submission->grade !== null)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded font-bold text-sm">Sudah Dinilai</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded font-bold text-sm">Menunggu Penilaian</span>
                                    @endif
                                </div>
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold w-1/3">Waktu Kirim:</span>
                                    <span>{{ $submission->submitted_at->format('d M Y H:i') }}</span>
                                    @if($submission->submitted_at > $assignment->due_date)
                                        <span class="text-red-500 font-bold ml-2 text-xs">(Terlambat)</span>
                                    @endif
                                </div>
                                
                                @if($submission->grade !== null)
                                    <div class="flex items-center mb-2">
                                        <span class="font-semibold w-1/3">Nilai:</span>
                                        <span class="text-xl font-bold">{{ $submission->grade }} / {{ $assignment->points }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="font-semibold block mb-1">Feedback Guru:</span>
                                        <div class="p-3 bg-gray-50 rounded italic text-gray-600">
                                            {{ $submission->feedback ?? 'Tidak ada feedback.' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Form Submission -->
                        @if(!$submission || ($submission->grade === null && $assignment->due_date > now()))
                            <form action="{{ route('siswa.lms.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <h4 class="font-semibold mb-2">
                                    {{ $submission ? 'Edit Jawaban Anda' : 'Kirim Jawaban' }}
                                </h4>
                                
                                <div class="mb-4">
                                    <x-input-label for="content" :value="__('Jawaban Teks / Catatan')" />
                                    <textarea id="content" name="content" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('content', $submission->content ?? '') }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="file" :value="__('Upload File Jawaban')" />
                                    <input id="file" type="file" name="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                    <p class="mt-1 text-sm text-gray-500">Max size: 10MB</p>
                                    @if($submission && $submission->file_path)
                                        <div class="mt-2 text-sm text-blue-600">
                                            File terkirim: <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="underline">Lihat File</a>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-end">
                                    <x-primary-button>{{ $submission ? __('Update Jawaban') : __('Kirim Tugas') }}</x-primary-button>
                                </div>
                            </form>
                        @elseif($submission && $submission->grade === null)
                            <div class="text-center p-4 bg-gray-100 rounded text-gray-500">
                                Waktu pengumpulan sudah habis dan Anda sudah mengumpulkan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
