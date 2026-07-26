<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Referensi & Pemetaan e-Rapor</h2>
            <p class="mt-1 text-xs font-medium text-gray-500">Referensi resmi berversi, data induk tetap berasal dari SISFO.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center gap-2" aria-label="Navigasi e-Rapor">
                <a href="{{ route('erapor.index') }}"
                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Ringkasan
                </a>
                <a href="{{ route('erapor.assignments.index') }}"
                    class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">
                    Penugasan
                </a>
                <a href="{{ route('erapor.configuration.index') }}"
                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                    Konfigurasi
                </a>
            </nav>

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-800">{{ $errors->first() }}</div>
            @endif

            <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
                @foreach ([
                    ['Kurikulum referensi', $stats['curricula']],
                    ['Mapel referensi', $stats['reference_subjects']],
                    ['Referensi aktif', $stats['active_reference_subjects']],
                    ['Mapel SISFO', $stats['local_subjects']],
                    ['Sudah dipetakan', $stats['mapped_subjects']],
                ] as [$label, $value])
                    <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                        <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($value) }}</p>
                    </article>
                @endforeach
            </section>

            <section class="space-y-6">
                <div class="grid gap-6 lg:grid-cols-2">
                    <article class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                        <h3 class="font-black text-blue-950">Sumber referensi terkontrol</h3>
                        <p class="mt-2 text-sm leading-relaxed text-blue-800">
                            JSON e-Rapor hanya mengisi referensi kurikulum. Guru, siswa, kelas, rombel, dan jadwal tetap memakai ID master SISFO.
                            Setiap versi import dicatat bersama checksum berkas.
                        </p>
                        <div class="mt-4 rounded-xl bg-blue-950 px-4 py-3 font-mono text-xs text-blue-50">php artisan erapor:import-references "PATH_E_RAPOR"</div>
                    </article>

                    <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-4"><h3 class="font-black text-gray-900">Manifest import terbaru</h3></div>
                        <div class="divide-y divide-gray-100">
                            @forelse ($imports as $import)
                                <div class="px-5 py-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-black text-gray-900">{{ str_replace('_', ' ', $import->dataset) }}</p>
                                            <p class="mt-1 text-xs text-gray-500">v{{ $import->source_version }} · {{ number_format($import->records_imported) }} rekaman · {{ $import->files_count }} berkas</p>
                                        </div>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $import->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($import->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $import->status }}</span>
                                    </div>
                                    <p class="mt-2 truncate font-mono text-[10px] text-gray-400" title="{{ $import->checksum }}">{{ $import->checksum }}</p>
                                </div>
                            @empty
                                <p class="px-5 py-8 text-center text-sm text-gray-500">Belum ada referensi yang diimpor.</p>
                            @endforelse
                        </div>
                    </article>
                </div>

                <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-5">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h3 class="font-black text-gray-900">Pemetaan mata pelajaran SISFO</h3>
                                <p class="mt-1 text-sm text-gray-500">Pilih padanan referensi untuk setiap mapel dalam jadwal.</p>
                            </div>
                            <form method="GET" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[1fr_160px_220px_190px_auto_auto] xl:items-end">
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-bold text-gray-500">Cari mapel SISFO</span>
                                    <input name="search" value="{{ request('search') }}" placeholder="Nama atau kode mapel"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                </label>
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-bold text-gray-500">Tingkat</span>
                                    <select name="tingkat" class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                        <option value="">Semua tingkat</option>
                                        @foreach (['X', 'XI', 'XII'] as $level)
                                            <option value="{{ $level }}" @selected($selectedLevel === $level)>Kelas {{ $level }}</option>
                                        @endforeach
                                    </select>
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
                                    <span class="mb-1.5 block text-xs font-bold text-gray-500">Status pemetaan</span>
                                    <select name="status_pemetaan" class="w-full rounded-xl border-gray-200 text-sm focus:border-red-400 focus:ring-red-400">
                                        <option value="">Semua status</option>
                                        <option value="mapped" @selected($selectedMappingStatus === 'mapped')>Terpetakan</option>
                                        <option value="unmapped" @selected($selectedMappingStatus === 'unmapped')>Belum terpetakan</option>
                                    </select>
                                </label>
                                <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-gray-800">Terapkan</button>
                                <a href="{{ route('erapor.references.index') }}"
                                    class="rounded-xl border border-gray-200 px-5 py-2.5 text-center text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                            </form>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($subjects as $subject)
                            <div class="p-5">
                                <div class="grid gap-4 lg:grid-cols-[1fr_1.7fr] lg:items-center">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-black text-gray-900">{{ $subject->nama_mapel }}</p>
                                            @if ($subject->kelas)
                                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $subject->kelas->nama_kelas }}</span>
                                            @else
                                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-gray-500">Tanpa kelas</span>
                                            @endif
                                            @if ($subject->eraporMapping)
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Terpetakan</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">{{ $subject->kode_mapel ?: 'Tanpa kode' }} · {{ $subject->jadwal_pelajaran_count }} slot jadwal</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('erapor.mappings.store') }}"
                                            class="flex min-w-0 flex-1 gap-2"
                                            x-data="eraporReferenceCombobox({
                                                endpoint: @js(route('erapor.references.subject-options')),
                                                selectedId: @js($subject->eraporMapping?->erapor_ref_subject_id),
                                                selectedLabel: @js($subject->eraporMapping?->referenceSubject
                                                    ? $subject->eraporMapping->referenceSubject->name.' ['.$subject->eraporMapping->referenceSubject->external_id.']'
                                                    : ''),
                                            })">
                                            @csrf
                                            <input type="hidden" name="mata_pelajaran_id" value="{{ $subject->id }}">
                                            <input type="hidden" name="apply_same_name" value="1">
                                            <div
                                                class="relative min-w-0 flex-1"
                                                @click.outside="open = false"
                                                @keydown.escape.window="open = false">
                                                <input type="hidden" name="erapor_ref_subject_id" :value="selectedId">
                                                <button type="button" @click="toggle()"
                                                    class="flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2 text-left text-sm focus:border-red-400 focus:outline-none focus:ring-1 focus:ring-red-400">
                                                    <span class="min-w-0 truncate" :class="selectedLabel ? 'text-gray-800' : 'text-gray-400'"
                                                        x-text="selectedLabel || 'Pilih atau cari referensi e-Rapor…'"></span>
                                                    <svg class="h-4 w-4 flex-none text-gray-400 transition" :class="{ 'rotate-180': open }"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-cloak x-transition
                                                    class="absolute z-40 mt-2 w-full min-w-[340px] overflow-hidden rounded-xl border border-gray-200 bg-white shadow-xl">
                                                    <div class="border-b border-gray-100 p-3">
                                                        <div class="relative">
                                                            <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                            </svg>
                                                            <input type="search" x-ref="searchInput" x-model="query"
                                                                @input.debounce.300ms="search()"
                                                                placeholder="Cari nama atau ID referensi…"
                                                                class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-red-400 focus:ring-red-400">
                                                        </div>
                                                    </div>
                                                    <div class="max-h-64 overflow-y-auto p-1.5">
                                                        <p x-show="loading" class="px-3 py-5 text-center text-xs font-medium text-gray-400">Memuat referensi…</p>
                                                        <p x-show="!loading && options.length === 0" class="px-3 py-5 text-center text-xs font-medium text-gray-400">Referensi tidak ditemukan.</p>
                                                        <template x-for="option in options" :key="option.id">
                                                            <button type="button" @click="choose(option)"
                                                                class="flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-left text-sm hover:bg-red-50 hover:text-red-700"
                                                                :class="{ 'bg-red-50 font-bold text-red-700': selectedId === String(option.id) }">
                                                                <span class="min-w-0" x-text="option.label"></span>
                                                                <svg x-show="selectedId === String(option.id)" class="h-4 w-4 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <button :disabled="!selectedId"
                                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Simpan</button>
                                        </form>
                                        @if ($subject->eraporMapping)
                                            <form method="POST" action="{{ route('erapor.mappings.destroy', $subject->eraporMapping) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-bold text-gray-500" title="Lepas pemetaan">×</button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-medium text-gray-400 lg:col-start-2">Berlaku otomatis untuk semua mapel SISFO dengan nama yang sama.</p>
                                </div>
                            </div>
                        @empty
                            <p class="p-8 text-center text-sm text-gray-500">Mata pelajaran SISFO tidak ditemukan.</p>
                        @endforelse
                    </div>
                    @if ($subjects->hasPages())
                        <div class="border-t border-gray-100 p-5">{{ $subjects->links() }}</div>
                    @endif
                </article>
            </section>
        </div>
    </div>
</x-app-layout>
