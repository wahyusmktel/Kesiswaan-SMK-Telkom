<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Konfigurasi e-Rapor</h2>
            <p class="mt-1 text-xs font-medium text-gray-500">
                {{ $period ? $period->tahun.' · Semester '.$period->semester : 'Tahun pelajaran aktif belum ditetapkan' }}
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Navigasi e-Rapor">
                <a href="{{ route('erapor.index') }}"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Ringkasan
                </a>
                <a href="{{ route('erapor.references.index') }}"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Referensi
                </a>
                <a href="{{ route('erapor.assignments.index') }}"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Penugasan
                </a>
            </nav>

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['Rombel periode aktif', $readiness['rombels'], 'text-gray-900'],
                    ['Kurikulum ditetapkan', $readiness['configured_rombels'], 'text-emerald-600'],
                    ['Belum dikonfigurasi', $readiness['unconfigured_rombels'], 'text-amber-600'],
                    ['Penugasan tanpa KKM', $readiness['assignments_without_passing_grade'], 'text-red-600'],
                ] as [$label, $value, $color])
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-black {{ $color }}">{{ number_format($value) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
                <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Workflow periode</p>
                            <h3 class="mt-1 text-lg font-black text-gray-900">Pengaturan penilaian dan tanggal rapor</h3>
                        </div>
                        <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ ($setting?->workflow_status ?? 'setup') === 'assessment' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ($setting?->workflow_status ?? 'setup') === 'assessment' ? 'Penilaian dibuka' : 'Persiapan' }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('erapor.configuration.period.update') }}" class="mt-6 grid gap-4 md:grid-cols-2" @if(!$period) aria-disabled="true" @endif>
                        @csrf
                        @method('PATCH')
                        <label class="block md:col-span-2">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Status workflow</span>
                            <select name="workflow_status" class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400" @disabled(!$period)>
                                <option value="setup" @selected(old('workflow_status', $setting?->workflow_status ?? 'setup') === 'setup')>Persiapan</option>
                                <option value="assessment" @selected(old('workflow_status', $setting?->workflow_status) === 'assessment')>Buka penilaian</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Mulai input nilai</span>
                            <input type="datetime-local" name="score_entry_starts_at"
                                value="{{ old('score_entry_starts_at', $setting?->score_entry_starts_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400" @disabled(!$period)>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Batas input nilai</span>
                            <input type="datetime-local" name="score_entry_ends_at"
                                value="{{ old('score_entry_ends_at', $setting?->score_entry_ends_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400" @disabled(!$period)>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Tanggal rapor</span>
                            <input type="date" name="report_date"
                                value="{{ old('report_date', $setting?->report_date?->format('Y-m-d')) }}"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400" @disabled(!$period)>
                        </label>
                        <div class="flex items-end">
                            <button class="w-full rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 disabled:opacity-50" @disabled(!$period)>
                                Simpan pengaturan
                            </button>
                        </div>
                    </form>
                </article>

                <aside class="rounded-2xl border {{ $readiness['can_open_assessment'] ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-6">
                    <h3 class="font-black {{ $readiness['can_open_assessment'] ? 'text-emerald-900' : 'text-amber-900' }}">
                        {{ $readiness['can_open_assessment'] ? 'Siap membuka penilaian' : 'Syarat pembukaan penilaian' }}
                    </h3>
                    @if ($blockers)
                        <ul class="mt-4 space-y-2">
                            @foreach ($blockers as $blocker)
                                <li class="flex gap-2 text-sm font-medium text-amber-800">
                                    <span class="mt-1 h-1.5 w-1.5 flex-none rounded-full bg-amber-500"></span>
                                    <span>{{ $blocker }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sm font-medium leading-relaxed text-emerald-800">
                            Seluruh rombel memiliki kurikulum, penugasan sudah dipetakan, dan KKM/KKTP telah lengkap.
                        </p>
                    @endif
                </aside>
            </section>

            <article class="overflow-visible rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="space-y-4 border-b border-gray-100 p-5">
                    <div>
                        <h3 class="font-black text-gray-900">Kurikulum per rombel</h3>
                        <p class="mt-1 text-sm text-gray-500">Pilih kurikulum referensi yang digunakan oleh masing-masing rombel.</p>
                    </div>
                    <form method="GET" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[180px_260px_200px_auto_auto] xl:items-end">
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Tingkat</span>
                            <select name="tingkat" class="w-full rounded-xl border-gray-200 text-sm">
                                <option value="">Semua tingkat</option>
                                @foreach (['X', 'XI', 'XII'] as $level)
                                    <option value="{{ $level }}" @selected($selectedLevel === $level)>Kelas {{ $level }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Kelas</span>
                            <select name="kelas_id" class="w-full rounded-xl border-gray-200 text-sm">
                                <option value="">Semua kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>{{ $class->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Status</span>
                            <select name="status" class="w-full rounded-xl border-gray-200 text-sm">
                                <option value="">Semua status</option>
                                <option value="configured" @selected($selectedStatus === 'configured')>Sudah dikonfigurasi</option>
                                <option value="unconfigured" @selected($selectedStatus === 'unconfigured')>Belum dikonfigurasi</option>
                            </select>
                        </label>
                        <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-bold text-white">Terapkan</button>
                        <a href="{{ route('erapor.configuration.index') }}"
                            class="rounded-xl border border-gray-200 px-5 py-2.5 text-center text-sm font-bold text-gray-600">Reset</a>
                    </form>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($rombels as $rombel)
                        <div class="grid gap-4 p-5 lg:grid-cols-[1fr_2fr] lg:items-center">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-black text-gray-900">{{ $rombel->kelas->nama_kelas }}</p>
                                    @if ($rombel->eraporCurriculum)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Dikonfigurasi</span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Belum dikonfigurasi</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $rombel->siswa_count }} siswa · {{ $rombel->jadwal_pelajaran_count }} slot jadwal
                                    @if ($rombel->waliKelas) · {{ $rombel->waliKelas->name }} @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('erapor.configuration.rombel-curricula.store') }}"
                                    class="flex min-w-0 flex-1 gap-2"
                                    x-data="eraporCurriculumCombobox({
                                        endpoint: @js(route('erapor.configuration.curriculum-options')),
                                        selectedId: @js($rombel->eraporCurriculum?->erapor_ref_curriculum_id),
                                        selectedLabel: @js($rombel->eraporCurriculum?->referenceCurriculum
                                            ? $rombel->eraporCurriculum->referenceCurriculum->name.' ['.$rombel->eraporCurriculum->referenceCurriculum->external_id.']'
                                            : ''),
                                    })">
                                    @csrf
                                    <input type="hidden" name="rombel_id" value="{{ $rombel->id }}">
                                    <input type="hidden" name="erapor_ref_curriculum_id" :value="selectedId">
                                    <div class="relative min-w-0 flex-1" @click.outside="open = false" @keydown.escape.window="open = false">
                                        <button type="button" @click="toggle()"
                                            class="flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2 text-left text-sm focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400">
                                            <span class="truncate" :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'"
                                                x-text="selectedLabel || 'Pilih atau cari kurikulum…'"></span>
                                            <svg class="h-4 w-4 flex-none text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <div x-show="open" x-cloak x-transition
                                            class="absolute z-40 mt-2 w-full min-w-[380px] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
                                            <div class="border-b border-gray-100 p-3">
                                                <input type="search" x-ref="searchInput" x-model="query" @input.debounce.300ms="search()"
                                                    placeholder="Cari nama, ID, atau jurusan kurikulum…"
                                                    class="w-full rounded-lg border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                            </div>
                                            <div class="max-h-64 overflow-y-auto p-1.5">
                                                <p x-show="loading" class="px-3 py-5 text-center text-xs text-gray-400">Memuat kurikulum…</p>
                                                <p x-show="!loading && options.length === 0" class="px-3 py-5 text-center text-xs text-gray-400">Kurikulum tidak ditemukan.</p>
                                                <template x-for="option in options" :key="option.id">
                                                    <button type="button" @click="choose(option)"
                                                        class="block w-full rounded-lg px-3 py-2.5 text-left hover:bg-red-50">
                                                        <span class="block text-sm font-bold text-gray-800" x-text="option.label"></span>
                                                        <span class="mt-0.5 block text-[11px] text-gray-400" x-text="option.meta"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <button :disabled="!selectedId"
                                        class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">
                                        Simpan
                                    </button>
                                </form>
                                @if ($rombel->eraporCurriculum)
                                    <form method="POST" action="{{ route('erapor.configuration.rombel-curricula.destroy', $rombel->eraporCurriculum) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-bold text-gray-500 hover:border-red-200 hover:text-red-600" title="Lepas kurikulum">×</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="p-10 text-center text-sm text-gray-500">Rombel periode aktif tidak ditemukan.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
