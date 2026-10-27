<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Ruang Belajar Siswa (LMS)</h2>
    </x-slot>

    <div class="py-6 sm:py-8 w-full" x-data="{
        filterTab: 'all',
        searchQuery: '',
        matches(course) {
            const query = this.searchQuery.toLowerCase().trim();
            const matchesSearch = !query || 
                course.name.toLowerCase().includes(query) || 
                course.guru.toLowerCase().includes(query) ||
                course.schedulesText.toLowerCase().includes(query);

            if (!matchesSearch) return false;

            if (this.filterTab === 'all') return true;
            if (this.filterTab === 'today') return course.hasScheduleToday;
            if (this.filterTab === 'izin') return course.hasActiveIzin;
            if (this.filterTab === 'pending') return course.pendingCount > 0;
            return true;
        }
    }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero Banner: Mengotak (rounded-lg) & Warna Gray Soft --}}
            <div class="relative overflow-hidden rounded-lg bg-gray-50 border border-gray-200 p-6 sm:p-7 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-gray-200/70 border border-gray-300 text-gray-700 text-xs font-semibold tracking-wide uppercase">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Ruang Belajar Mandiri & Online
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                            Kelas {{ $rombel?->kelas?->nama_kelas ?? 'Siswa' }}
                        </h1>
                        <p class="text-gray-600 text-sm max-w-2xl leading-relaxed">
                            Akses materi pelajaran, jadwal belajar, dan instruksi penugasan mandiri saat guru berhalangan hadir atau pembelajaran online.
                        </p>
                    </div>

                    {{-- Quick Metric Pills --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 text-center shadow-sm">
                            <span class="block text-2xl sm:text-3xl font-black text-gray-900">{{ $courses->count() }}</span>
                            <span class="text-xs text-gray-500 font-medium">Mata Pelajaran</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 text-center shadow-sm">
                            <span class="block text-2xl sm:text-3xl font-black {{ $totalActiveIzin > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                                {{ $totalActiveIzin }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">Guru Izin Hari Ini</span>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 text-center col-span-2 sm:col-span-1 shadow-sm">
                            <span class="block text-2xl sm:text-3xl font-black {{ $totalPendingAssignments > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $totalPendingAssignments }}
                            </span>
                            <span class="text-xs text-gray-500 font-medium">Tugas Belum Selesai</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alert Guru Izin Hari Ini (Jika ada) --}}
            @if ($totalActiveIzin > 0)
                <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 sm:p-5 text-amber-900 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-sm text-amber-900">Perhatian: Pembelajaran Mandiri Hari Ini</h3>
                        <p class="text-xs sm:text-sm text-amber-800 mt-0.5">
                            Ada <strong>{{ $totalActiveIzin }} guru</strong> yang sedang izin hari ini. Silakan buka mata pelajaran terkait untuk mempelajari materi atau mengerjakan tugas yang telah disiapkan.
                        </p>
                    </div>
                    <button @click="filterTab = 'izin'" class="px-3.5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition shadow-sm whitespace-nowrap">
                        Lihat Kelas Guru Izin
                    </button>
                </div>
            @endif

            {{-- Filter Tabs and Search --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                {{-- Tabs --}}
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 border border-gray-200 rounded-lg overflow-x-auto">
                    <button type="button" @click="filterTab = 'all'"
                        :class="filterTab === 'all' ? 'bg-white text-gray-900 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition whitespace-nowrap">
                        Semua Mapel ({{ $courses->count() }})
                    </button>
                    <button type="button" @click="filterTab = 'today'"
                        :class="filterTab === 'today' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition whitespace-nowrap flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Jadwal Hari Ini ({{ $todayName }})
                    </button>
                    @if ($totalActiveIzin > 0)
                        <button type="button" @click="filterTab = 'izin'"
                            :class="filterTab === 'izin' ? 'bg-white text-rose-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                            class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition whitespace-nowrap flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            Guru Izin ({{ $totalActiveIzin }})
                        </button>
                    @endif
                    <button type="button" @click="filterTab = 'pending'"
                        :class="filterTab === 'pending' ? 'bg-white text-amber-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900 font-medium'"
                        class="px-3.5 py-1.5 rounded-md text-xs sm:text-sm transition whitespace-nowrap flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Ada Tugas Aktif ({{ $courses->where('pending_assignments_count', '>', 0)->count() }})
                    </button>
                </div>

                {{-- Search Box --}}
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="Cari mata pelajaran / guru..."
                        class="w-full pl-9 pr-3.5 py-1.5 text-xs sm:text-sm bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition shadow-sm">
                </div>
            </div>

            {{-- Courses Grid --}}
            @if ($courses->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($courses as $course)
                        @php
                            $schedulesText = $course->schedules->map(fn($s) => "{$s->hari} {$s->time_range}")->implode(' ');
                            $courseDataJson = json_encode([
                                'name' => $course->mapel->nama_mapel,
                                'guru' => $course->guru?->nama_lengkap ?? 'Belum ditentukan',
                                'schedulesText' => $schedulesText,
                                'hasScheduleToday' => $course->has_schedule_today,
                                'hasActiveIzin' => (bool)$course->active_izin,
                                'pendingCount' => $course->pending_assignments_count,
                            ]);
                        @endphp
                        {{-- Card Kelas: Mengotak (rounded-lg), Border Hijau jika Hari Ini, Gray Soft jika Terjadwal --}}
                        <div x-show="matches({{ $courseDataJson }})"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="group relative flex flex-col bg-white rounded-lg border {{ $course->has_schedule_today ? 'border-emerald-500 shadow-sm shadow-emerald-50' : 'border-gray-200' }} shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                            
                            {{-- Border Atas Area: Hijau jika Hari Ini, Gray Soft jika Terjadwal --}}
                            <div class="h-1.5 w-full {{ $course->has_schedule_today ? 'bg-emerald-500 border-b border-emerald-500' : 'bg-gray-200 border-b border-gray-200' }}"></div>

                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    {{-- Badges Header --}}
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        @if ($course->active_izin)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Guru Izin (Tugas Mandiri)
                                            </span>
                                        @elseif ($course->has_schedule_today)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Jadwal Hari Ini
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                Terjadwal
                                            </span>
                                        @endif

                                        @if ($course->pending_assignments_count > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                {{ $course->pending_assignments_count }} Tugas
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Course Title --}}
                                    <h3 class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors leading-snug line-clamp-2">
                                        <a href="{{ route('siswa.lms.course.show', $course->mapel->id) }}">
                                            {{ $course->mapel->nama_mapel }}
                                        </a>
                                    </h3>

                                    {{-- Teacher Info --}}
                                    <div class="flex items-center gap-2 mt-2">
                                        <div class="w-7 h-7 rounded-md bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                            {{ substr($course->guru?->nama_lengkap ?? 'G', 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold text-gray-700 truncate">
                                                {{ $course->guru?->nama_lengkap ?? 'Guru Pengajar' }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Jadwal & Jam Pelajaran: Kotak Mengotak & Gray Soft --}}
                                    <div class="mt-3.5 p-3 rounded-lg bg-gray-50 border border-gray-200/80 space-y-1.5">
                                        <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Jadwal Pembelajaran
                                        </div>

                                        @if ($course->schedules->isNotEmpty())
                                            <div class="space-y-1">
                                                @foreach ($course->schedules as $sched)
                                                    <div class="flex items-center justify-between text-xs {{ $sched->hari === $todayName ? 'font-bold text-emerald-900' : 'text-gray-600' }}">
                                                        <span class="flex items-center gap-1.5">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $sched->hari === $todayName ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                                                            {{ $sched->hari }}
                                                        </span>
                                                        <span class="text-gray-500 font-mono text-[11px]">
                                                            {{ $sched->time_range }} WIB
                                                            @if ($sched->jam_ke)
                                                                <span class="text-gray-400 font-sans">({{ $sched->jam_ke }})</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 italic">Jadwal belum ditentukan.</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card Footer / Action --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <span class="inline-flex items-center gap-1" title="Materi Pembelajaran">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                            {{ $course->materials_count }} Materi
                                        </span>
                                        <span class="inline-flex items-center gap-1" title="Tugas & Latihan">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                            {{ $course->assignments_count }} Tugas
                                        </span>
                                    </div>

                                    <a href="{{ route('siswa.lms.course.show', $course->mapel->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $course->has_schedule_today ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }} font-bold text-xs transition">
                                        Masuk Kelas
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-lg bg-white border border-gray-200 p-10 text-center shadow-sm">
                    <div class="w-12 h-12 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">Belum Ada Mata Pelajaran Terjadwal</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                        Mata pelajaran dan jadwal mengajar untuk kelas {{ $rombel?->kelas?->nama_kelas ?? 'Anda' }} belum ditambahkan dalam sistem.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
