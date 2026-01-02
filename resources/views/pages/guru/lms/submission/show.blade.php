<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nilai Tugas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Submission Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Detail Pengumpulan</h3>
                        
                        <div class="mb-4">
                            <span class="block text-gray-500 text-sm">Nama Siswa</span>
                            <span class="font-semibold">{{ $submission->siswa->nama_lengkap }}</span>
                        </div>

                        <div class="mb-4">
                            <span class="block text-gray-500 text-sm">Waktu Submit</span>
                            <span>{{ $submission->submitted_at->format('d M Y H:i') }}</span>
                            @if($submission->submitted_at > $submission->assignment->due_date)
                                <span class="text-xs text-red-500 font-bold ml-2">(Terlambat)</span>
                            @endif
                        </div>

                        <div class="mb-4">
                            <span class="block text-gray-500 text-sm">Isi Jawaban</span>
                            <div class="p-3 bg-gray-50 rounded border mt-1">
                                {!! nl2br(e($submission->content)) !!}
                            </div>
                        </div>

                        @if($submission->file_path)
                            <div class="mb-4">
                                <span class="block text-gray-500 text-sm">Lampiran File</span>
                                <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="mt-1 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Download File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Grading Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Penilaian</h3>

                        <form action="{{ route('guru.lms.submission.grade', $submission->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-4">
                                <x-input-label for="grade" :value="__('Nilai (0-100)')" />
                                <x-text-input id="grade" class="block mt-1 w-full" type="number" name="grade" :value="old('grade', $submission->grade)" min="0" max="100" required />
                                <x-input-error :messages="$errors->get('grade')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="feedback" :value="__('Feedback / Komentar')" />
                                <textarea id="feedback" name="feedback" rows="5" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ old('feedback', $submission->feedback) }}</textarea>
                                <x-input-error :messages="$errors->get('feedback')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan Nilai') }}</x-primary-button>
                                <a href="{{ route('guru.lms.assignment.show', $submission->lms_assignment_id) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                    {{ __('Kembali') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
