<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Kelas & Materi</h2>
    </x-slot>

    <div class="py-6 sm:py-8 w-full" x-data="{
        activeTab: 'all',
        searchQuery: '',
    }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Top Navigation / Breadcrumbs --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <nav class="flex items-center text-xs font-semibold text-gray-400 space-x-2">
                    <a href="{{ route('siswa.dashboard.index') }}" class="hover:text-red-600 transition">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('siswa.lms.index') }}" class="hover:text-red-600 transition">Ruang Belajar</a>
                    <span>/</span>
                    <span class="text-gray-800 font-bold truncate max-w-xs">{{ $mapel->nama_mapel }}</span>
                </nav>

                <a href="{{ route('siswa.lms.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white border border-gray-300 text-gray-700 hover:text-red-600 rounded-lg text-xs font-bold transition shadow-sm self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Mapel
                </a>
            </div>

            {{-- Course Hero Card: Mengotak (rounded-lg) & Warna Gray Soft --}}
            <div class="relative overflow-hidden rounded-lg bg-gray-50 border border-gray-200 text-gray-900 p-6 sm:p-7 shadow-sm">
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="space-y-2.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-white border border-gray-200 text-gray-700 text-xs font-bold uppercase tracking-wider shadow-sm">
                                {{ $rombel?->kelas?->nama_kelas ?? 'Kelas Siswa' }}
                            </span>
                            @if ($activeIzin)
                                <span class="px-2.5 py-0.5 rounded-md bg-amber-100 border border-amber-300 text-amber-900 text-xs font-bold flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    Guru Izin Hari Ini (Belajar Mandiri)
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight text-gray-900">
                            {{ $mapel->nama_mapel }}
                        </h1>

                        <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-gray-600">
                            {{-- Guru info --}}
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-md bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-xs">
                                    {{ substr($guru?->nama_lengkap ?? 'G', 0, 1) }}
                                </div>
                                <span class="font-semibold text-gray-800">{{ $guru?->nama_lengkap ?? 'Guru Pengajar' }}</span>
                            </div>

                            <span class="hidden sm:inline text-gray-300">•</span>

                            {{-- Jadwal Jam Pelajaran --}}
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>
                                    @if ($schedulesByDay->isNotEmpty())
                                        {{ $schedulesByDay->map(fn($s) => "{$s->hari} ({$s->time_range} WIB)")->implode(', ') }}
                                    @else
                                        Jadwal Belum Ditentukan
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Summary Stats Pills --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-2.5 min-w-[260px]">
                        <div class="bg-white border border-gray-200 rounded-lg p-3 text-center shadow-sm">
                            <span class="block text-2xl font-black text-gray-900">{{ $materials->count() }}</span>
                            <span class="text-[11px] text-gray-500 font-medium">Materi Pelajaran</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 text-center shadow-sm">
                            <span class="block text-2xl font-black text-indigo-700">{{ $totalAssignments }}</span>
                            <span class="text-[11px] text-gray-500 font-medium">Total Tugas</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 text-center shadow-sm">
                            <span class="block text-2xl font-black text-emerald-700">{{ $submittedCount }}</span>
                            <span class="text-[11px] text-gray-500 font-medium">Tugas Diserahkan</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 text-center shadow-sm">
                            <span class="block text-2xl font-black text-red-600">{{ $totalAssignments - $submittedCount }}</span>
                            <span class="text-[11px] text-gray-500 font-medium">Belum Dikerjakan</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alert Informasi Guru Izin / Belajar Mandiri --}}
            @if ($activeIzin || $recentIzins->isNotEmpty())
                @php
                    $displayIzin = $activeIzin ?? $recentIzins->first();
                    $isTodayIzin = $displayIzin->tanggal_mulai && $displayIzin->tanggal_mulai->isToday();
                @endphp
                <div class="rounded-lg p-4 sm:p-5 {{ $isTodayIzin ? 'bg-amber-50 border border-amber-200 text-amber-900' : 'bg-gray-50 border border-gray-200 text-gray-800' }} shadow-sm">
                    <div class="flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-lg {{ $isTodayIzin ? 'bg-amber-100 text-amber-700' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h4 class="font-bold text-sm {{ $isTodayIzin ? 'text-amber-900' : 'text-gray-900' }}">
                                    {{ $isTodayIzin ? 'Pemberitahuan: Guru Berhalangan Hadir Hari Ini' : 'Catatan Pembelajaran Mandiri / Guru Izin' }}
                                </h4>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $isTodayIzin ? 'bg-amber-200 text-amber-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $displayIzin->categoryLabel() }} • {{ $displayIzin->jenis_izin }}
                                </span>
                            </div>
                            <p class="text-xs {{ $isTodayIzin ? 'text-amber-800' : 'text-gray-600' }} leading-relaxed">
                                Guru pengajar ({{ $guru?->nama_lengkap }}) berhalangan hadir pada tanggal 
                                <strong>{{ $displayIzin->tanggal_mulai ? $displayIzin->tanggal_mulai->translatedFormat('d F Y') : '-' }}</strong>.
                                Siswa diharapkan untuk mempelajari materi dan mengerjakan latihan/tugas mandiri yang telah disediakan di bawah ini dengan tertib.
                            </p>
                            @if ($displayIzin->deskripsi)
                                <div class="mt-2 text-xs italic {{ $isTodayIzin ? 'text-amber-800 bg-amber-100/60 border-amber-200' : 'text-gray-700 bg-white border-gray-200' }} p-2.5 rounded-lg border">
                                    "{{ $displayIzin->deskripsi }}"
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tabs & Filters Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-3">
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 border border-gray-200 rounded-lg">
                    <button type="button" @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition">
                        Semua Aktivitas ({{ $materials->count() + $totalAssignments }})
                    </button>
                    <button type="button" @click="activeTab = 'materials'"
                        :class="activeTab === 'materials' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Materi ({{ $materials->count() }})
                    </button>
                    <button type="button" @click="activeTab = 'assignments'"
                        :class="activeTab === 'assignments' ? 'bg-white text-gray-900 font-bold shadow-sm' : 'text-gray-600 hover:text-gray-900 font-semibold'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Tugas & Latihan ({{ $totalAssignments }})
                    </button>
                </div>
            </div>

            {{-- Tab Contents: Materials & Assignments --}}
            <div class="space-y-6">

                {{-- Section Materi --}}
                <div x-show="activeTab === 'all' || activeTab === 'materials'" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-gray-600"></span>
                            Materi Pembelajaran
                        </h3>
                        <span class="text-xs text-gray-400">{{ $materials->count() }} materi diterbitkan</span>
                    </div>

                    @if ($materials->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($materials as $material)
                                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-3 mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-sm leading-snug">
                                                    {{ $material->title }}
                                                </h4>
                                            </div>
                                            <span class="text-[11px] text-gray-400 font-medium whitespace-nowrap">
                                                {{ $material->created_at->translatedFormat('d M Y') }}
                                            </span>
                                        </div>

                                        @if ($material->content)
                                            <p class="text-xs text-gray-600 line-clamp-3 mt-2 leading-relaxed">
                                                {{ $material->content }}
                                            </p>
                                        @endif
                                    </div>

                                    @if ($material->file_path)
                                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                            <span class="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                Lampiran Tersedia
                                            </span>
                                            <a href="{{ Storage::url($material->file_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md text-xs font-bold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                                Unduh
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-lg bg-white border border-gray-200 text-center">
                            <p class="text-xs text-gray-400">Belum ada materi pelajaran yang diunggah untuk kelas ini.</p>
                        </div>
                    @endif
                </div>

                {{-- Section Tugas & PR --}}
                <div x-show="activeTab === 'all' || activeTab === 'assignments'" class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                            Tugas & Latihan Siswa
                        </h3>
                        <span class="text-xs text-gray-400">{{ $totalAssignments }} tugas tercatat</span>
                    </div>

                    @if ($assignments->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($assignments as $assignment)
                                @php
                                    $submission = $assignment->submissions->first();
                                    $isSubmitted = (bool)$submission;
                                    $isGraded = $submission && $submission->grade !== null;
                                    $isOverdue = $assignment->due_date && $assignment->due_date->isPast();
                                @endphp
                                <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-start justify-between gap-3 mb-2.5">
                                            {{-- Status Submission Pill --}}
                                            @if ($isGraded)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Dinilai: {{ $submission->grade }} / {{ $assignment->points }}
                                                </span>
                                            @elseif ($isSubmitted)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Sudah Dikumpulkan
                                                </span>
                                            @elseif ($isOverdue)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                    Tenggat Terlewat
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Belum Dikerjakan
                                                </span>
                                            @endif

                                            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                                {{ $assignment->points }} Poin
                                            </span>
                                        </div>

                                        <h4 class="font-bold text-gray-900 text-sm leading-snug">
                                            <a href="{{ route('siswa.lms.assignment.show', $assignment->id) }}" class="hover:text-red-600 transition">
                                                {{ $assignment->title }}
                                            </a>
                                        </h4>

                                        @if ($assignment->description)
                                            <p class="text-xs text-gray-500 line-clamp-2 mt-1.5 leading-relaxed">
                                                {{ Str::limit(strip_tags($assignment->description), 120) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                        <div class="text-xs text-gray-500 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>
                                                @if ($assignment->due_date)
                                                    Tenggat: <strong class="{{ $isOverdue ? 'text-red-600' : 'text-gray-700' }}">{{ $assignment->due_date->translatedFormat('d M Y, H:i') }} WIB</strong>
                                                @else
                                                    Tanpa Batas Waktu
                                                @endif
                                            </span>
                                        </div>

                                        <a href="{{ route('siswa.lms.assignment.show', $assignment->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs transition shadow-sm">
                                            {{ $isSubmitted ? 'Lihat Jawaban' : 'Kerjakan Tugas' }}
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-lg bg-white border border-gray-200 text-center">
                            <p class="text-xs text-gray-400">Belum ada tugas atau PR yang diberikan untuk kelas ini.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
