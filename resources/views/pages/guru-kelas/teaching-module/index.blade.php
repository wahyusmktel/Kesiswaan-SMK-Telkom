<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Perangkat Pembelajaran</h2>
            <p class="text-xs text-gray-500">Modul ajar milik Anda</p>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 border-b border-gray-200 pb-5 sm:flex-row sm:items-end">
                <div>
                    <h1 class="text-2xl font-black text-gray-950">Daftar Perangkat Pembelajaran</h1>
                    <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                        Kelola metadata, isi modul ajar, dan hasil PDF dari satu halaman kerja.
                    </p>
                </div>
                <a href="{{ route('guru-kelas.teaching-module.create') }}"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-red-600 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Perangkat
                </a>
            </div>

            <div class="grid grid-cols-1 border-y border-gray-200 bg-gray-50 sm:grid-cols-3 sm:divide-x sm:divide-gray-200">
                <div class="px-5 py-4">
                    <p class="text-xs font-bold uppercase text-gray-500">Total Modul</p>
                    <p class="mt-1 text-2xl font-black text-gray-950">{{ $stats['total'] }}</p>
                </div>
                <div class="border-t border-gray-200 px-5 py-4 sm:border-t-0">
                    <p class="text-xs font-bold uppercase text-amber-700">Masih Draft</p>
                    <p class="mt-1 text-2xl font-black text-gray-950">{{ $stats['draft'] }}</p>
                </div>
                <div class="border-t border-gray-200 px-5 py-4 sm:border-t-0">
                    <p class="text-xs font-bold uppercase text-emerald-700">Sudah Lengkap</p>
                    <p class="mt-1 text-2xl font-black text-gray-950">{{ $stats['complete'] }}</p>
                </div>
            </div>

            <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="search" name="search" value="{{ $search }}"
                        placeholder="Cari nama, kode, mata pelajaran, atau lingkup materi"
                        class="h-11 w-full rounded-md border-gray-300 pl-10 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                </div>
                <select name="status"
                    class="h-11 rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 md:w-48">
                    <option value="">Semua status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="complete" @selected(request('status') === 'complete')>Lengkap</option>
                </select>
                <button type="submit"
                    class="inline-flex h-11 items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                    Terapkan
                </button>
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('guru-kelas.teaching-module.index') }}"
                        class="inline-flex h-11 items-center justify-center px-2 text-sm font-bold text-red-700 hover:text-red-800">
                        Reset
                    </a>
                @endif
            </form>

            <div class="overflow-hidden border border-gray-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left">
                        <thead class="bg-gray-50">
                            <tr class="text-xs font-black uppercase text-gray-500">
                                <th class="px-5 py-3.5">Kode & Modul</th>
                                <th class="px-5 py-3.5">Mata Pelajaran</th>
                                <th class="px-5 py-3.5">Periode</th>
                                <th class="px-5 py-3.5">Konteks</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-sm">
                            @forelse($modules as $module)
                                <tr class="align-top transition hover:bg-gray-50/70">
                                    <td class="px-5 py-4">
                                        <div class="font-mono text-xs font-black text-red-700">{{ $module->kode_modul }}</div>
                                        <div class="mt-1 max-w-xs font-bold text-gray-950">{{ $module->nama_modul }}</div>
                                        <div class="mt-1 text-xs text-gray-500">Diperbarui {{ $module->updated_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-gray-800">{{ $module->mata_pelajaran }}</div>
                                        <div class="mt-1 text-xs text-gray-500">{{ $module->program_keahlian }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                        <div class="font-semibold">{{ $module->tahun_pelajaran }}</div>
                                        <div class="mt-1 text-xs text-gray-500">Semester {{ $module->semester }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-gray-700">
                                        <div>{{ $module->jenjang }} · Kelas {{ $module->kelas }} · Fase {{ $module->fase }}</div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $module->alokasi_waktu }} ·
                                            {{ $module->jumlah_murid === 'Disesuaikan' ? 'Jumlah murid disesuaikan' : $module->jumlah_murid.' murid' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($module->status === 'complete')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Lengkap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('guru-kelas.teaching-module.content.edit', $module) }}"
                                                class="inline-flex h-9 items-center justify-center rounded-md bg-red-600 px-3 text-xs font-bold text-white transition hover:bg-red-700">
                                                Isi Modul
                                            </a>
                                            <a href="{{ route('guru-kelas.teaching-module.pdf.preview', $module) }}" target="_blank"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                                                title="Pratinjau PDF">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 4H9m8 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0117 7.414V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="sr-only">Pratinjau PDF</span>
                                            </a>
                                            <a href="{{ route('guru-kelas.teaching-module.edit', $module) }}"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                                                title="Edit data utama">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="sr-only">Edit metadata</span>
                                            </a>
                                            <form method="POST" action="{{ route('guru-kelas.teaching-module.destroy', $module) }}"
                                                x-data @submit.prevent="Swal.fire({
                                                    title: 'Hapus perangkat pembelajaran?',
                                                    text: 'Isi modul dan arsip PDF dari data ini tidak dapat dipulihkan.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#dc2626',
                                                    confirmButtonText: 'Ya, hapus',
                                                    cancelButtonText: 'Batal'
                                                }).then(result => { if (result.isConfirmed) $el.submit() })">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-700"
                                                    title="Hapus">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span class="sr-only">Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0117 7.414V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="mt-4 text-sm font-bold text-gray-900">Belum ada perangkat pembelajaran</h3>
                                        <p class="mt-1 text-sm text-gray-500">Buat data utama terlebih dahulu, lalu isi modul ajarnya.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($modules->hasPages())
                    <div class="border-t border-gray-200 px-5 py-4">
                        {{ $modules->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
