<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">e-Rapor SISFO</h2>
                <p class="text-xs font-medium text-gray-500">Pusat kesiapan dan pengelolaan rapor terintegrasi</p>
            </div>
        </div>
    </x-slot>

    @php
        $statusStyles = [
            'ready' => [
                'label' => 'Siap',
                'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
                'dot' => 'bg-emerald-500',
                'panel' => 'from-emerald-500 to-teal-600',
                'icon' => 'text-emerald-600 bg-emerald-50',
            ],
            'warning' => [
                'label' => 'Perlu perhatian',
                'badge' => 'bg-amber-100 text-amber-700 ring-amber-200',
                'dot' => 'bg-amber-500',
                'panel' => 'from-amber-500 to-orange-600',
                'icon' => 'text-amber-600 bg-amber-50',
            ],
            'blocked' => [
                'label' => 'Belum siap',
                'badge' => 'bg-red-100 text-red-700 ring-red-200',
                'dot' => 'bg-red-500',
                'panel' => 'from-red-600 to-rose-700',
                'icon' => 'text-red-600 bg-red-50',
            ],
        ];
        $overall = $statusStyles[$overall_status];
        $readyCount = $checks->where('status', 'ready')->count();
        $progress = $checks->count() > 0 ? (int) round(($readyCount / $checks->count()) * 100) : 0;
    @endphp

    <div class="w-full py-6">
        <div class="mx-auto w-full space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $overall['panel'] }} p-6 text-white shadow-xl sm:p-8">
                <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                <div class="absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>

                <div class="relative grid gap-8 lg:grid-cols-[1.6fr_1fr] lg:items-end">
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider">
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                            Fase 1 · Fondasi Referensi
                        </div>
                        <h3 class="max-w-2xl text-3xl font-black tracking-tight sm:text-4xl">
                            Satu data sekolah, satu alur rapor.
                        </h3>
                        <p class="mt-3 max-w-2xl text-sm font-medium leading-relaxed text-white/85 sm:text-base">
                            e-Rapor akan memakai guru, siswa, kelas, rombel, mata pelajaran, dan akun yang sudah tersedia
                            di SISFO—tanpa sinkronisasi Dapodik terpisah.
                        </p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-black text-gray-900 shadow-sm">
                                <span class="h-2.5 w-2.5 rounded-full {{ $overall['dot'] }}"></span>
                                {{ $overall['label'] }}
                            </span>
                            <span class="rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold">
                                {{ $issues_count }} item perlu ditindaklanjuti
                            </span>
                            @can('configure erapor')
                                <a href="{{ route('erapor.configuration.index') }}" class="rounded-xl border border-white/30 bg-white/15 px-4 py-2 text-sm font-bold text-white hover:bg-white/25">
                                    Konfigurasi periode
                                </a>
                                <a href="{{ route('erapor.references.index') }}" class="rounded-xl border border-white/30 bg-white/15 px-4 py-2 text-sm font-bold text-white hover:bg-white/25">
                                    Kelola referensi
                                </a>
                                <a href="{{ route('erapor.assignments.index') }}" class="rounded-xl border border-white/30 bg-white/15 px-4 py-2 text-sm font-bold text-white hover:bg-white/25">
                                    Kelola penugasan
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/20 bg-white/10 p-5 backdrop-blur">
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-white/70">Kesiapan dasar</p>
                                <p class="mt-1 text-4xl font-black">{{ $progress }}%</p>
                            </div>
                            <p class="text-sm font-bold text-white/80">{{ $readyCount }}/{{ $checks->count() }} pemeriksaan</p>
                        </div>
                        <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-black/15">
                            <div class="h-full rounded-full bg-white transition-all" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-4 border-t border-white/15 pt-4 text-sm">
                            <p class="font-bold">
                                {{ $period ? $period->tahun.' · Semester '.$period->semester : 'Periode aktif belum ditetapkan' }}
                            </p>
                            <p class="mt-1 text-xs font-medium text-white/65">
                                Diperiksa {{ $generated_at->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
                @foreach ([
                    ['label' => 'Guru', 'value' => $stats['teachers'], 'classes' => 'bg-indigo-50 text-indigo-600'],
                    ['label' => 'Siswa aktif', 'value' => $stats['students'], 'classes' => 'bg-blue-50 text-blue-600'],
                    ['label' => 'Rombel aktif', 'value' => $stats['rombels'], 'classes' => 'bg-emerald-50 text-emerald-600'],
                    ['label' => 'Mapel terjadwal', 'value' => $stats['subjects'], 'classes' => 'bg-amber-50 text-amber-600'],
                    ['label' => 'Penugasan unik', 'value' => $stats['assignments'], 'classes' => 'bg-rose-50 text-rose-600'],
                ] as $stat)
                    <article class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:p-5">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $stat['label'] }}</p>
                                <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($stat['value']) }}</p>
                            </div>
                            <div class="hidden h-11 w-11 items-center justify-center rounded-xl sm:flex {{ $stat['classes'] }}">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h3 class="text-lg font-black text-gray-900">Kesiapan data SISFO</h3>
                            <p class="mt-1 text-sm text-gray-500">Pemeriksaan ini hanya membaca data dan aman dijalankan ulang.</p>
                        </div>
                        <span class="w-fit rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $overall['badge'] }}">
                            {{ $overall['label'] }}
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach ($checks as $check)
                            @php
                                $style = $statusStyles[$check['status']];
                                $canOpenAction = $check['route']
                                    && Route::has($check['route'])
                                    && (!$check['permission'] || auth()->user()->can($check['permission']))
                                    && (empty($check['roles']) || in_array(session('active_role'), $check['roles'], true));
                            @endphp
                            <article class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:items-start sm:justify-between sm:px-6">
                                <div class="flex min-w-0 gap-4">
                                    <div class="flex h-10 w-10 flex-none items-center justify-center rounded-xl {{ $style['icon'] }}">
                                        @if ($check['status'] === 'ready')
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @elseif ($check['status'] === 'warning')
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-black text-gray-900">{{ $check['label'] }}</h4>
                                            <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold ring-1 {{ $style['badge'] }}">
                                                {{ $style['label'] }}
                                            </span>
                                        </div>
                                        <p class="mt-1.5 text-sm leading-relaxed text-gray-500">{{ $check['message'] }}</p>
                                    </div>
                                </div>

                                @if ($canOpenAction)
                                    <a href="{{ route($check['route']) }}"
                                        class="inline-flex flex-none items-center justify-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                        Perbaiki data
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>

                <aside class="space-y-6">
                    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white">
                                <span class="text-sm font-black">01</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Tahap saat ini</p>
                                <h3 class="font-black text-gray-900">Fondasi data</h3>
                            </div>
                        </div>
                        <ol class="mt-6 space-y-4">
                            @foreach ([
                                ['label' => 'Readiness data induk', 'state' => 'done'],
                                ['label' => 'Referensi kurikulum', 'state' => 'active'],
                                ['label' => 'Penugasan e-Rapor', 'state' => 'active'],
                                ['label' => 'Konfigurasi periode', 'state' => 'active'],
                                ['label' => 'Input dan kalkulasi nilai', 'state' => 'later'],
                                ['label' => 'Validasi dan penerbitan', 'state' => 'later'],
                            ] as $index => $phase)
                                <li class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full text-xs font-black
                                        {{ $phase['state'] === 'active' ? 'bg-red-600 text-white' : ($phase['state'] === 'done' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-400') }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm font-bold {{ in_array($phase['state'], ['active', 'done']) ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $phase['label'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                        <div class="flex gap-3">
                            <svg class="mt-0.5 h-5 w-5 flex-none text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-black text-blue-900">Belum mengambil data Dapodik</h4>
                                <p class="mt-1 text-xs font-medium leading-relaxed text-blue-700">
                                    Seluruh data induk berasal dari database SISFO. JSON e-Rapor hanya menjadi referensi
                                    kurikulum berversi dan tidak membuat salinan guru atau siswa.
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>
            </section>
        </div>
    </div>
</x-app-layout>
