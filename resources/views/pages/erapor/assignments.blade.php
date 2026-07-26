<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Penugasan e-Rapor</h2>
            <p class="mt-1 text-xs font-medium text-gray-500">{{ $period ? $period->tahun.' · Semester '.$period->semester : 'Tahun pelajaran aktif belum ditetapkan' }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Navigasi e-Rapor">
                <a href="{{ route('erapor.index') }}"
                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Ringkasan
                </a>
                <a href="{{ route('erapor.references.index') }}"
                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Referensi
                </a>
                <form method="POST" action="{{ route('erapor.assignments.sync') }}">
                    @csrf
                    <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(!$period)>
                        Sinkronkan dari jadwal
                    </button>
                </form>
            </nav>

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if (session('error') || $errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-800">{{ session('error') ?: $errors->first() }}</div>
            @endif

            <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['Penugasan aktif', $stats['active'], 'text-gray-900'],
                    ['Referensi terpetakan', $stats['mapped'], 'text-emerald-600'],
                    ['Belum dipetakan', $stats['unmapped'], 'text-amber-600'],
                    ['Tidak aktif', $stats['inactive'], 'text-gray-400'],
                ] as [$label, $value, $color])
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-black {{ $color }}">{{ number_format($value) }}</p>
                    </article>
                @endforeach
            </section>

            <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="space-y-4 border-b border-gray-100 p-5">
                    <div>
                        <h3 class="font-black text-gray-900">Daftar penugasan permanen</h3>
                        <p class="mt-1 text-sm text-gray-500">Satu rombel, mapel, dan guru menjadi satu penugasan meski jadwal memiliki beberapa slot.</p>
                    </div>
                    <form method="GET" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[1fr_240px_280px_auto_auto] xl:items-end">
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Pencarian</span>
                            <input name="search" value="{{ request('search') }}" placeholder="Guru, kelas, atau mapel"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Kelas</span>
                            <select name="kelas_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                <option value="">Semua kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @selected($selectedClassId === $class->id)>{{ $class->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-xs font-bold text-gray-500">Guru</span>
                            <select name="guru_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                <option value="">Semua guru</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" @selected($selectedTeacherId === $teacher->id)>{{ $teacher->nama_lengkap }}</option>
                                @endforeach
                            </select>
                        </label>
                        @if (request()->boolean('inactive'))<input type="hidden" name="inactive" value="1">@endif
                        <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-gray-800">Terapkan</button>
                        <a href="{{ route('erapor.assignments.index', request()->boolean('inactive') ? ['inactive' => 1] : []) }}"
                            class="rounded-xl border border-gray-200 px-5 py-2.5 text-center text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-black uppercase tracking-wider text-gray-500">
                            <tr><th class="px-5 py-3">Kelas / Mapel</th><th class="px-5 py-3">Guru</th><th class="px-5 py-3">Referensi</th><th class="px-5 py-3">Konfigurasi rapor</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($assignments as $assignment)
                                <tr class="align-top">
                                    <td class="px-5 py-4">
                                        <p class="font-black text-gray-900">{{ $assignment->rombel?->kelas?->nama_kelas ?? 'Rombel #'.$assignment->rombel_id }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $assignment->subject?->nama_mapel }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-bold text-gray-800">{{ $assignment->teacher?->nama_lengkap }}</p>
                                        <p class="mt-1 text-xs text-gray-400">{{ data_get($assignment->sync_metadata, 'schedule_slot_count', 0) }} slot jadwal</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($assignment->subjectMapping?->referenceSubject)
                                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $assignment->subjectMapping->referenceSubject->name }}</span>
                                        @else
                                            <a href="{{ route('erapor.references.index', ['search' => $assignment->subject?->nama_mapel]) }}" class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Petakan mapel</a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('erapor.assignments.update', $assignment) }}" class="grid min-w-[390px] grid-cols-[1fr_90px_90px_auto] gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input name="subject_group" value="{{ $assignment->subject_group }}" placeholder="Kelompok" class="rounded-lg border-gray-200 text-xs">
                                            <input name="sort_order" type="number" min="0" max="999" value="{{ $assignment->sort_order }}" placeholder="Urutan" class="rounded-lg border-gray-200 text-xs">
                                            <input name="passing_grade" type="number" min="0" max="100" step="0.01" value="{{ $assignment->passing_grade }}" placeholder="KKM" class="rounded-lg border-gray-200 text-xs">
                                            <button class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-bold text-gray-700">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">Belum ada penugasan. Jalankan sinkronisasi dari jadwal aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 p-5">
                    <div class="flex gap-2 text-xs font-bold">
                        <a href="{{ route('erapor.assignments.index', request()->except(['page', 'inactive'])) }}" class="{{ !request()->boolean('inactive') ? 'text-red-600' : 'text-gray-400' }}">Aktif</a>
                        <span class="text-gray-200">/</span>
                        <a href="{{ route('erapor.assignments.index', array_merge(request()->except(['page', 'inactive']), ['inactive' => 1])) }}" class="{{ request()->boolean('inactive') ? 'text-red-600' : 'text-gray-400' }}">Tidak aktif</a>
                    </div>
                    @if ($assignments->hasPages()){{ $assignments->links() }}@endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
