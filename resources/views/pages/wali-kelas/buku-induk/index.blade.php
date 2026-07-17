<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Buku Induk Siswa</h2>
                <p class="mt-1 text-sm text-gray-500">Kelola arsip induk siswa pada kelas binaan aktif.</p>
            </div>
            @if($selectedRombel)
                <a href="{{ route('wali-kelas.buku-induk.print-class', $selectedRombel) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-5h12v9H6v-9z"/></svg>
                    Cetak Satu Kelas
                </a>
            @endif
        </div>
    </x-slot>

    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="mb-5 border-b border-gray-200 pb-5">
            <form method="GET" class="grid gap-3 md:grid-cols-[minmax(220px,320px)_minmax(260px,1fr)_auto]">
                <label>
                    <span class="mb-1 block text-xs font-bold uppercase text-gray-500">Kelas Binaan</span>
                    <select name="rombel_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        @forelse($rombels as $rombel)
                            <option value="{{ $rombel->id }}" @selected($selectedRombel?->id === $rombel->id)>
                                {{ $rombel->kelas?->nama_kelas }} - {{ $rombel->tahunPelajaran?->tahun }} ({{ $rombel->siswa_count }} siswa)
                            </option>
                        @empty
                            <option>Belum ada kelas binaan aktif</option>
                        @endforelse
                    </select>
                </label>
                <label>
                    <span class="mb-1 block text-xs font-bold uppercase text-gray-500">Cari Siswa</span>
                    <div class="relative">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input name="search" value="{{ request('search') }}" placeholder="Nama, NIS, atau NISN"
                            class="w-full rounded-lg border-gray-300 pl-10 text-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                </label>
                <button class="mt-auto inline-flex h-[42px] items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-bold text-gray-700 hover:bg-gray-50">Terapkan</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-bold uppercase text-gray-500">
                            <th class="px-5 py-3">Siswa</th>
                            <th class="px-5 py-3">Identitas</th>
                            <th class="px-5 py-3">Status Buku Induk</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $student)
                            @php
                                $identityReady = filled($student->dapodik?->nisn) && filled($student->dapodik?->nik) && filled($student->dapodik?->no_kk);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-900">{{ $student->nama_lengkap }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">NIS {{ $student->nis }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <p class="text-gray-700">NISN {{ $student->dapodik?->nisn ?: '-' }}</p>
                                    <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $identityReady ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $identityReady ? 'Data dasar siap' : 'Perlu dilengkapi' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($student->masterBook?->completed_at)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Lengkap
                                        </span>
                                    @elseif($student->masterBook)
                                        <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">Dalam proses</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">Belum dimulai</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('wali-kelas.buku-induk.edit', $student) }}" title="Kelola Buku Induk"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-gray-600 hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 13H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <a href="{{ route('wali-kelas.buku-induk.print-student', $student) }}" target="_blank" title="Cetak PDF"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-900 text-white hover:bg-red-600">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-12-5h12v9H6v-9z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-16 text-center text-sm text-gray-500">Belum ada siswa pada kelas binaan aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="border-t border-gray-200 px-5 py-4">{{ $students->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
