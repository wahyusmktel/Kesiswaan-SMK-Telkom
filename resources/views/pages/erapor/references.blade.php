<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-gray-900">Referensi & Pemetaan e-Rapor</h2>
                <p class="mt-1 text-xs font-medium text-gray-500">Referensi resmi berversi, data induk tetap berasal dari SISFO.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('erapor.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-bold text-gray-600">Ringkasan</a>
                <a href="{{ route('erapor.assignments.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white">Penugasan</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
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

            <section class="grid gap-6 xl:grid-cols-[1fr_1.8fr]">
                <div class="space-y-6">
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
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                            <div>
                                <h3 class="font-black text-gray-900">Pemetaan mata pelajaran SISFO</h3>
                                <p class="mt-1 text-sm text-gray-500">Pilih padanan referensi untuk setiap mapel dalam jadwal.</p>
                            </div>
                            <form method="GET" class="flex flex-col gap-2 sm:flex-row">
                                <input name="search" value="{{ request('search') }}" placeholder="Cari mapel SISFO" class="rounded-xl border-gray-200 text-sm">
                                <input name="reference_search" value="{{ $referenceSearch }}" placeholder="Saring referensi" class="rounded-xl border-gray-200 text-sm">
                                <button class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-bold text-white">Cari</button>
                            </form>
                        </div>
                        @if ($referenceSearch)
                            <p class="mt-3 text-xs font-medium text-gray-500">Pilihan referensi disaring dengan “{{ $referenceSearch }}”.</p>
                        @endif
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($subjects as $subject)
                            <div class="p-5">
                                <div class="grid gap-4 lg:grid-cols-[1fr_1.7fr] lg:items-center">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-black text-gray-900">{{ $subject->nama_mapel }}</p>
                                            @if ($subject->eraporMapping)
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Terpetakan</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">{{ $subject->kode_mapel ?: 'Tanpa kode' }} · {{ $subject->jadwal_pelajaran_count }} slot jadwal</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('erapor.mappings.store') }}" class="flex min-w-0 flex-1 gap-2">
                                            @csrf
                                            <input type="hidden" name="mata_pelajaran_id" value="{{ $subject->id }}">
                                            <input type="hidden" name="apply_same_name" value="1">
                                            <select name="erapor_ref_subject_id" required class="min-w-0 flex-1 rounded-xl border-gray-200 text-sm">
                                                <option value="">Pilih referensi e-Rapor…</option>
                                                @foreach ($referenceSubjects as $reference)
                                                    <option value="{{ $reference->id }}" @selected($subject->eraporMapping?->erapor_ref_subject_id === $reference->id)>
                                                        {{ $reference->name }} [{{ $reference->external_id }}]
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white">Simpan</button>
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
