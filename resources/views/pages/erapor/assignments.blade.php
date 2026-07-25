<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-gray-900">Penugasan e-Rapor</h2>
                <p class="mt-1 text-xs font-medium text-gray-500">{{ $period ? $period->tahun.' · Semester '.$period->semester : 'Tahun pelajaran aktif belum ditetapkan' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('erapor.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600">Ringkasan</a>
                <a href="{{ route('erapor.references.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600">Referensi</a>
                <form method="POST" action="{{ route('erapor.assignments.sync') }}">
                    @csrf
                    <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white disabled:opacity-50" @disabled(!$period)>Sinkronkan dari jadwal</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
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
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-black text-gray-900">Daftar penugasan permanen</h3>
                        <p class="mt-1 text-sm text-gray-500">Satu rombel, mapel, dan guru menjadi satu penugasan meski jadwal memiliki beberapa slot.</p>
                    </div>
                    <form method="GET" class="flex gap-2">
                        <input name="search" value="{{ request('search') }}" placeholder="Guru, kelas, atau mapel" class="rounded-xl border-gray-200 text-sm">
                        @if (request()->boolean('inactive'))<input type="hidden" name="inactive" value="1">@endif
                        <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white">Cari</button>
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
                        <a href="{{ route('erapor.assignments.index', ['search' => request('search')]) }}" class="{{ !request()->boolean('inactive') ? 'text-red-600' : 'text-gray-400' }}">Aktif</a>
                        <span class="text-gray-200">/</span>
                        <a href="{{ route('erapor.assignments.index', ['inactive' => 1, 'search' => request('search')]) }}" class="{{ request()->boolean('inactive') ? 'text-red-600' : 'text-gray-400' }}">Tidak aktif</a>
                    </div>
                    @if ($assignments->hasPages()){{ $assignments->links() }}@endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
