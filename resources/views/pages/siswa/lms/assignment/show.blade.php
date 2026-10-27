<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail & Pengumpulan Tugas</h2>
    </x-slot>

    @php
        $isSubmitted = (bool)$submission;
        $isGraded = $submission && $submission->grade !== null;
        $isOverdue = $assignment->due_date && $assignment->due_date->isPast();
        $isLateSubmission = $submission && $assignment->due_date && $submission->submitted_at->gt($assignment->due_date);
        $canSubmit = !$isGraded && (!$isOverdue || $isSubmitted);
    @endphp

    <div class="py-6 sm:py-8 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Top Navigation / Breadcrumbs --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <nav class="flex items-center text-xs font-semibold text-gray-400 space-x-2">
                    <a href="{{ route('siswa.dashboard.index') }}" class="hover:text-red-600 transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('siswa.lms.index') }}" class="hover:text-red-600 transition">Ruang Belajar</a>
                    <span>/</span>
                    <a href="{{ route('siswa.lms.course.show', $assignment->mata_pelajaran_id) }}" class="hover:text-red-600 transition">
                        {{ $assignment->mataPelajaran?->nama_mapel }}
                    </a>
                    <span>/</span>
                    <span class="text-gray-800 font-bold truncate max-w-xs">{{ $assignment->title }}</span>
                </nav>

                <a href="{{ route('siswa.lms.course.show', $assignment->mata_pelajaran_id) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white border border-gray-300 text-gray-700 hover:text-red-600 rounded-lg text-xs font-bold transition shadow-sm self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Kelas
                </a>
            </div>

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-emerald-900 shadow-sm flex items-center gap-3">
                    <div class="w-7 h-7 rounded-md bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-rose-50 border border-rose-200 p-4 text-rose-900 shadow-sm flex items-center gap-3">
                    <div class="w-7 h-7 rounded-md bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Main Assignment Header Card: Mengotak (rounded-lg) & Gray Soft --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-white text-gray-800 text-xs font-bold border border-gray-200 shadow-sm">
                                {{ $assignment->mataPelajaran?->nama_mapel }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-md bg-gray-200/80 text-gray-700 text-xs font-semibold">
                                {{ $assignment->rombel?->kelas?->nama_kelas ?? 'Kelas Siswa' }}
                            </span>
                            @if ($relatedLeave)
                                <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Tugas Mandiri (Guru Izin: {{ $relatedLeave->jenis_izin }})
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
                            {{ $assignment->title }}
                        </h1>

                        <div class="flex items-center gap-3 text-xs text-gray-500 pt-1">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-md bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-bold">
                                    {{ substr($assignment->guru?->nama_lengkap ?? 'G', 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-700">{{ $assignment->guru?->nama_lengkap ?? 'Guru Pengajar' }}</span>
                            </div>
                            <span>•</span>
                            <span>Diterbitkan {{ $assignment->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                    </div>

                    {{-- Badges Right --}}
                    <div class="flex flex-wrap sm:flex-col items-start sm:items-end gap-2">
                        <span class="px-3 py-1.5 rounded-md bg-white border border-gray-200 text-gray-800 text-xs font-bold flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Poin Maks: {{ $assignment->points }}
                        </span>

                        @if ($assignment->due_date)
                            <span class="px-3 py-1.5 rounded-md text-xs font-bold flex items-center gap-1.5 shadow-sm {{ $isOverdue ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-white text-gray-700 border border-gray-200' }}">
                                <svg class="w-3.5 h-3.5 {{ $isOverdue ? 'text-red-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>
                                    {{ $isOverdue ? 'Tenggat Berakhir: ' : 'Tenggat: ' }}
                                    {{ $assignment->due_date->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </span>
                        @else
                            <span class="px-3 py-1.5 rounded-md bg-white text-gray-600 text-xs font-semibold border border-gray-200">
                                Tanpa Batas Waktu
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2-Column Grid: Instructions & Submission --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Left: Assignment Instructions & Attachments --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 sm:p-7 shadow-sm space-y-5">
                        <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                            <div class="w-7 h-7 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-sm">Petunjuk & Instruksi Pengerjaan</h3>
                        </div>

                        {{-- Prose Description --}}
                        <div class="prose max-w-none text-xs sm:text-sm text-gray-700 leading-relaxed space-y-3">
                            @if ($assignment->description)
                                {!! nl2br(e($assignment->description)) !!}
                            @else
                                <p class="text-gray-400 italic">Tidak ada petunjuk khusus tertulis dari guru.</p>
                            @endif
                        </div>

                        {{-- Teacher Attachment Box --}}
                        @if ($assignment->file_path)
                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2.5">Lampiran Soal / Berkas dari Guru</h4>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-lg bg-gray-50 border border-gray-200">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-md bg-gray-200 text-gray-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate">
                                                {{ basename($assignment->file_path) }}
                                            </p>
                                            <p class="text-[11px] text-gray-400">Berkas Lampiran Soal</p>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($assignment->file_path) }}" target="_blank"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white rounded-md text-xs font-bold transition shadow-sm whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Unduh Berkas
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right: Submission Panel --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6 sm:p-7 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="font-bold text-gray-900 text-sm">Status Pengumpulan</h3>
                            @if ($isGraded)
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Dinilai
                                </span>
                            @elseif ($isSubmitted)
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    Menunggu Penilaian
                                </span>
                            @elseif ($isOverdue)
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">
                                    Terlewat Tenggat
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800">
                                    Belum Menyerahkan
                                </span>
                            @endif
                        </div>

                        {{-- Grade & Feedback Banner if Graded --}}
                        @if ($isGraded)
                            <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Nilai Akhir Siswa</span>
                                    <span class="text-2xl font-black text-emerald-800">{{ $submission->grade }} <span class="text-xs font-normal text-emerald-600">/ {{ $assignment->points }}</span></span>
                                </div>
                                @if ($submission->feedback)
                                    <div class="mt-2 pt-2 border-t border-emerald-200/60">
                                        <span class="text-xs font-bold text-emerald-900 block mb-1">Catatan & Masukan Guru:</span>
                                        <div class="text-xs text-emerald-800 bg-white/80 p-2.5 rounded-md border border-emerald-200/80 leading-relaxed italic">
                                            "{{ $submission->feedback }}"
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Submission Details if Submitted --}}
                        @if ($isSubmitted)
                            <div class="space-y-3 text-xs bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between text-gray-500">
                                    <span>Waktu Diserahkan:</span>
                                    <span class="font-bold text-gray-800">
                                        {{ $submission->submitted_at->translatedFormat('d M Y, H:i') }} WIB
                                        @if ($isLateSubmission)
                                            <span class="text-red-600 font-bold ml-1">(Terlambat)</span>
                                        @endif
                                    </span>
                                </div>

                                @if ($submission->content)
                                    <div class="pt-2 border-t border-gray-200/60">
                                        <span class="text-gray-500 font-semibold block mb-1">Catatan / Jawaban Anda:</span>
                                        <div class="text-gray-700 bg-white p-2.5 rounded-md border border-gray-200 whitespace-pre-line leading-relaxed">
                                            {{ $submission->content }}
                                        </div>
                                    </div>
                                @endif

                                @if ($submission->file_path)
                                    <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                                        <div class="flex items-center gap-2 truncate">
                                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="font-medium text-gray-800 truncate">{{ basename($submission->file_path) }}</span>
                                        </div>
                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-bold whitespace-nowrap ml-2">
                                            Unduh
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Submission Form --}}
                        @if ($canSubmit)
                            <form action="{{ route('siswa.lms.assignment.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-1">
                                @csrf

                                <div class="flex items-center justify-between">
                                    <h4 class="font-bold text-gray-900 text-sm">
                                        {{ $isSubmitted ? 'Perbarui Jawaban Anda' : 'Kirim Pengumpulan Tugas' }}
                                    </h4>
                                    @if ($isSubmitted)
                                        <span class="text-[11px] text-gray-400">Pengumpulan dapat diedit</span>
                                    @endif
                                </div>

                                {{-- Textarea Answer --}}
                                <div>
                                    <label for="content" class="block text-xs font-bold text-gray-700 mb-1">
                                        Catatan Jawaban Teks (Opsional)
                                    </label>
                                    <textarea id="content" name="content" rows="4"
                                        placeholder="Tuliskan keterangan jawaban, link dokumen cloud, atau catatan tugas Anda di sini..."
                                        class="w-full text-xs text-gray-900 bg-gray-50 border border-gray-300 rounded-lg p-2.5 focus:bg-white focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition">{{ old('content', $submission->content ?? '') }}</textarea>
                                </div>

                                {{-- File Upload --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">
                                        Unggah Berkas Tugas (Maks. 10MB)
                                    </label>
                                    <div class="relative border-2 border-dashed border-gray-300 hover:border-gray-400 rounded-lg p-3.5 text-center bg-gray-50/50 hover:bg-gray-50 transition">
                                        <input type="file" id="file" name="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <div class="space-y-1">
                                            <svg class="w-6 h-6 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-xs text-gray-600 font-medium">
                                                <span class="text-gray-900 font-bold">Pilih file</span> atau seret file ke sini
                                            </p>
                                            <p class="text-[10px] text-gray-400">PDF, DOC, ZIP, JPG, PNG (Maksimal 10MB)</p>
                                        </div>
                                    </div>
                                    @if ($submission && $submission->file_path)
                                        <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            File tersimpan: <span class="font-medium text-gray-700">{{ basename($submission->file_path) }}</span>
                                        </p>
                                    @endif
                                </div>

                                <button type="submit"
                                    class="w-full py-2.5 px-4 bg-gray-900 hover:bg-black text-white font-bold text-xs sm:text-sm rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $isSubmitted ? 'Perbarui Pengumpulan' : 'Kirim Tugas Sekarang' }}
                                </button>
                            </form>
                        @elseif ($isGraded)
                            <div class="p-3.5 rounded-lg bg-gray-50 border border-gray-200 text-center text-xs text-gray-500">
                                Tugas ini telah selesai dinilai oleh guru dan pengumpulan telah dikunci.
                            </div>
                        @elseif ($isOverdue && !$isSubmitted)
                            <div class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-center text-xs text-red-600 font-medium">
                                Batas waktu pengumpulan tugas ini telah berakhir dan Anda belum mengumpulkan tugas.
                            </div>
                        @endif

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
