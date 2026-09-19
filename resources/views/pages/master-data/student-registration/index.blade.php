<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Registrasi Siswa Baru</h2>
                <p class="text-sm text-gray-500">Verifikasi pendaftaran cepat dan cocokkan dengan data resmi Dapodik.</p>
            </div>
            
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>@endif
            @if ($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4"><p class="text-xs font-bold uppercase text-amber-700">Menunggu Verifikasi</p><p class="mt-1 text-2xl font-black text-amber-900">{{ $counts['pending'] ?? 0 }}</p></div>
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4"><p class="text-xs font-bold uppercase text-blue-700">Menunggu Pemetaan</p><p class="mt-1 text-2xl font-black text-blue-900">{{ $counts['approved'] ?? 0 }}</p></div>
                <div class="rounded-lg border border-gray-200 bg-white p-4"><p class="text-xs font-bold uppercase text-gray-500">Dapodik Belum Terhubung</p><p class="mt-1 text-2xl font-black text-gray-900">{{ $unmappedDapodikCount }}</p></div>
                            </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 mt-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Aksi Cepat</h3>
                            <p class="text-sm text-gray-500">Kelola registrasi siswa baru dengan mudah</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('master-data.student-registration.school-origins') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Kelola Sekolah Asal</a>
                            <a href="{{ route('student-registration.create') }}" target="_blank" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Buka Form Publik</a>
                            <button @click="$dispatch('open-student-registration')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-500">Tambah Langsung</button>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>• <strong>Kelola Sekolah Asal</strong>: Kelola dan normalisasikan data sekolah asal pendaftar</p>
                        <p class="mt-1">• <strong>Buka Form Publik</strong>: Link formulir pendaftaran publik untuk siswa baru</p>
                        <p class="mt-1">• <strong>Tambah Langsung</strong>: Tambahkan data siswa baru secara manual di sistem</p>
                    </div>
                </div>

                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                Setelah import Dapodik, lakukan pemetaan siswa sementara pada halaman ini <strong>sebelum</strong> menekan Sinkronisasi ke Master Siswa. Sistem akan menahan data yang memiliki calon pasangan agar tidak dibuat menjadi siswa duplikat.
            </div>

            <section
                x-data="bulkStudentApproval({{ Illuminate\Support\Js::from($registrations->map(fn ($registration) => ['id' => $registration->id, 'name' => $registration->nama_lengkap])->values()) }}, {{ count($integrityStatements) }})"
                class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-5 pt-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                        <nav class="flex gap-1 overflow-x-auto">
                            @foreach (['pending' => 'Menunggu', 'approved' => 'Siap Dipetakan', 'mapped' => 'Sudah Dipetakan', 'rejected' => 'Ditolak'] as $key => $label)
                                <a href="{{ route('master-data.student-registration.index', ['status' => $key]) }}" class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-bold {{ $status === $key ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-800' }}">{{ $label }} <span class="ml-1 rounded-full bg-gray-200 px-2 py-0.5 text-xs text-gray-700">{{ $counts[$key] ?? 0 }}</span></a>
                            @endforeach
                        </nav>
                        <div class="mb-3 flex flex-col gap-2 sm:flex-row">
                            @if ($status === 'pending')
                                <button type="button" @click="openPact()" :disabled="selected.length === 0"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Setujui Massal (<span x-text="selected.length">0</span>)</span>
                                </button>
                            @endif
                            @if ($status === 'approved')
                                <button type="button" @click="$dispatch('open-bulk-dapodik-mapping', { ids: selected })"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                    </svg>
                                    <span>Cocokkan Dapodik Massal<template x-if="selected.length > 0"> (<span x-text="selected.length"></span>)</template></span>
                                </button>
                            @endif
                            <form method="GET" class="flex gap-2">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, NISN, nomor registrasi" class="w-72 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                <button class="rounded-lg bg-gray-900 px-4 text-sm font-bold text-white">Cari</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50 text-xs uppercase text-gray-600">
                            <tr>
                                @if (in_array($status, ['pending', 'approved']))
                                    <th class="w-12 px-4 py-4 text-center">
                                        <input type="checkbox" :checked="allSelected" @change="toggleAll($event.target.checked)"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            aria-label="Pilih semua data pada halaman ini">
                                    </th>
                                @endif
                                <th class="px-6 py-4">Registrasi</th>
                                <th class="px-6 py-4">Identitas</th>
                                <th class="px-6 py-4">Kontak</th>
                                <th class="px-6 py-4">Sumber / Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($registrations as $item)
                                <tr class="align-top hover:bg-gray-50">
                                    @if (in_array($status, ['pending', 'approved']))
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" value="{{ $item->id }}" x-model="selected"
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                aria-label="Pilih {{ $item->nama_lengkap }}">
                                        </td>
                                    @endif
                                    <td class="px-6 py-4"><div class="font-mono text-xs font-bold text-gray-900">{{ $item->registration_number }}</div><div class="mt-1 text-xs text-gray-500">{{ $item->created_at->translatedFormat('d M Y H:i') }}</div></td>
                                    <td class="px-6 py-4"><div class="font-bold text-gray-900">{{ $item->nama_lengkap }}</div><div class="mt-1 text-xs text-gray-500">NISN {{ $item->nisn ?: '-' }} · {{ $item->tempat_lahir ?: '-' }}, {{ $item->tanggal_lahir->format('d-m-Y') }}</div><div class="text-xs text-gray-500">{{ $item->sekolah_asal ?: 'Sekolah asal belum diisi' }}</div></td>
                                    <td class="px-6 py-4"><div class="text-gray-700">{{ $item->nomor_hp }}</div><div class="text-xs text-gray-500">{{ $item->email ?: '-' }}</div></td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-bold text-gray-700">{{ $item->source === 'public' ? 'Form Publik' : 'Input Petugas' }}</span>@if($item->masterSiswa)<div class="mt-2 text-xs text-gray-500">NIS sementara: <span class="font-mono">{{ $item->masterSiswa->nis }}</span></div>@endif</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                      <button type="button" @click="$dispatch('open-biodata-detail', {{ Illuminate\Support\Js::from(['id' => $item->id, 'name' => $item->nama_lengkap]) }})" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">Detail</button>
                                            @if ($item->status === 'pending')
                                                <form method="POST" action="{{ route('master-data.student-registration.approve', $item) }}" class="approve-form">@csrf @method('PATCH')<button type="button" onclick="confirmApprove(this)" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 hover:bg-green-100">Setujui</button></form>
                                                <form method="POST" action="{{ route('master-data.student-registration.reject', $item) }}" class="reject-form">@csrf @method('PATCH')<input type="hidden" name="notes"><button type="button" onclick="confirmReject(this)" class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">Tolak</button></form>
                                            @elseif ($item->status === 'approved')
                                                <button type="button" @click="$dispatch('open-dapodik-mapping', {{ Illuminate\Support\Js::from(['id' => $item->id, 'name' => $item->nama_lengkap, 'nisn' => $item->nisn, 'birth' => $item->tanggal_lahir->format('d-m-Y'), 'url' => route('master-data.student-registration.map', $item)]) }})" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-500">Cocokkan Dapodik</button>
                                            @elseif ($item->status === 'mapped')
                                                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700"><span class="h-2 w-2 rounded-full bg-green-500"></span>{{ $item->dapodikSiswa?->nipd }}</span>
                                            @else
                                                <span class="text-xs text-red-600" title="{{ $item->notes }}">{{ Str::limit($item->notes, 40) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ in_array($status, ['pending', 'approved']) ? 6 : 5 }}" class="px-6 py-14 text-center text-gray-500">Tidak ada data pada status ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-3">{{ $registrations->withQueryString()->links() }}</div>

                <div x-show="pactOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape.window="pactOpen = false">
                    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="pactOpen = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="relative w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-xl" @click.stop>
                            <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Pakta Integritas Persetujuan Massal</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Periksa dan setujui setiap pernyataan untuk <strong x-text="selected.length"></strong> calon siswa.
                                    </p>
                                </div>
                                <button type="button" @click="pactOpen = false" class="rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('master-data.student-registration.bulk-approve') }}">
                                @csrf
                                <template x-for="registrationId in selected" :key="registrationId">
                                    <input type="hidden" name="registration_ids[]" :value="registrationId">
                                </template>

                                <div class="max-h-[65vh] overflow-y-auto p-6">
                                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-900">
                                        Persetujuan akan membuat data siswa sementara. Data tersebut tetap harus dicocokkan dengan data resmi Dapodik ketika tersedia.
                                    </div>

                                    <div class="mt-5 space-y-3">
                                        @foreach ($integrityStatements as $key => $statement)
                                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-4 transition hover:border-green-300 hover:bg-green-50/40">
                                                <input type="checkbox" name="statements[{{ $key }}]" value="1"
                                                    @change="toggleStatement('{{ $key }}', $event.target.checked)" required
                                                    class="mt-0.5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                                <span class="text-sm leading-6 text-gray-700">{{ $statement }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="mt-5 rounded-lg bg-gray-50 p-4">
                                        <p class="text-xs font-bold uppercase text-gray-500">Data terpilih</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <template x-for="student in selectedStudents" :key="student.id">
                                                <span class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-semibold text-gray-700" x-text="student.name"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col-reverse gap-2 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                                    <button type="button" @click="pactOpen = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100">Batal</button>
                                    <button type="submit" :disabled="!allStatementsChecked || selected.length === 0"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white hover:bg-green-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5h10.5A2.25 2.25 0 0 0 19.5 17.25V6.75A2.25 2.25 0 0 0 17.25 4.5H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6.75 19.5Z" />
                                        </svg>
                                        Setujui dan Unduh Pakta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div x-data="{ open: false }" @open-student-registration.window="open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4"><div class="relative w-full max-w-3xl rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b px-6 py-4"><div><h3 class="font-bold">Tambah Siswa Baru Sementara</h3><p class="text-sm text-gray-500">Data langsung disetujui dan muncul pada Data Siswa.</p></div><button @click="open = false" class="text-2xl text-gray-400">&times;</button></div>
            <form method="POST" action="{{ route('master-data.student-registration.store') }}">@csrf
                <div class="grid max-h-[65vh] gap-4 overflow-y-auto p-6 md:grid-cols-2">
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Nama lengkap *</span><input name="nama_lengkap" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">NISN</span><input name="nisn" inputmode="numeric" maxlength="10" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">NIK</span><input name="nik" inputmode="numeric" maxlength="16" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tempat lahir</span><input name="tempat_lahir" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Tanggal lahir *</span><input type="date" name="tanggal_lahir" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Jenis kelamin *</span><select name="jenis_kelamin" required class="w-full rounded-lg border-gray-300"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nomor HP *</span><input type="number" name="nomor_hp" inputmode="numeric" required class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Email</span><input type="email" name="email" class="w-full rounded-lg border-gray-300"></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Alamat *</span><textarea name="alamat" required rows="2" class="w-full rounded-lg border-gray-300"></textarea></label>
                    <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Sekolah asal</span><input name="sekolah_asal" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">Nama orang tua/wali</span><input name="nama_orang_tua" class="w-full rounded-lg border-gray-300"></label>
                    <label><span class="mb-1 block text-sm font-semibold">HP orang tua/wali</span><input type="number" name="nomor_hp_orang_tua" inputmode="numeric" class="w-full rounded-lg border-gray-300"></label>
                </div>
                <div class="flex justify-end gap-2 border-t bg-gray-50 px-6 py-4"><button type="button" @click="open = false" class="rounded-lg border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Simpan Siswa Sementara</button></div>
            </form>
        </div></div>
    </div>

    <div x-data="dapodikMapping()" @open-dapodik-mapping.window="openModal($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden" @click.stop>
                <div class="border-b border-gray-200 bg-gray-50/70 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Cocokkan dengan Data Dapodik</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                Calon Siswa: <strong class="text-gray-800" x-text="student.name"></strong> · 
                                NISN: <span class="font-mono text-gray-700" x-text="student.nisn || '-'"></span> · 
                                Lahir: <span class="text-gray-700" x-text="student.birth"></span>
                            </p>
                        </div>
                        <button type="button" @click="open = false" class="rounded-full p-1.5 text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form :action="student.url" method="POST">
                    @csrf
                    <div class="p-6">
                        {{-- Search Input and Quick Actions --}}
                        <div class="space-y-2">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="search" x-model="query" @keydown.enter.prevent="search()" class="w-full rounded-lg border-gray-300 pl-9 pr-3 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Cari berdasarkan nama, NISN, atau NIPD...">
                                    <svg class="h-4 w-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </div>
                                <button type="button" @click="search()" class="rounded-lg bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-500 transition-colors">Cari</button>
                            </div>

                            {{-- Quick Chips: Cari Nama & Cari NISN --}}
                            <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                                <span class="text-gray-400 font-medium">Cari cepat:</span>
                                <template x-if="student.name">
                                    <button type="button" @click="searchBy(student.name)"
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-medium transition-all"
                                        :class="query.trim().toLowerCase() === (student.name || '').trim().toLowerCase() ? 'bg-blue-600 text-white font-bold shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200'">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        <span>Nama: <strong x-text="student.name"></strong></span>
                                    </button>
                                </template>
                                <template x-if="student.nisn">
                                    <button type="button" @click="searchBy(student.nisn)"
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 font-medium transition-all"
                                        :class="query.trim() === (student.nisn || '').trim() ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200'">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" /></svg>
                                        <span>NISN: <span class="font-mono font-bold" x-text="student.nisn"></span></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Search Results List --}}
                        <div class="mt-4 max-h-72 space-y-2 overflow-y-auto pr-1">
                            <template x-if="loading">
                                <div class="py-8 text-center text-sm text-gray-500">
                                    <div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-blue-600 border-t-transparent mb-2"></div>
                                    <p>Mencari data Dapodik...</p>
                                </div>
                            </template>

                            <template x-if="!loading && results.length === 0">
                                <div class="rounded-xl border border-dashed border-gray-200 py-8 px-4 text-center">
                                    <p class="text-sm font-semibold text-gray-600">Data Dapodik tidak ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Tidak ada catatan yang cocok dengan "<span class="font-semibold" x-text="query"></span>".</p>
                                    <template x-if="query !== student.name && student.name">
                                        <button type="button" @click="searchBy(student.name)" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:underline">
                                            <span>Coba cari menggunakan nama calon siswa ("<span x-text="student.name"></span>")</span>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-for="record in results" :key="record.id">
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 transition-all hover:bg-blue-50/60"
                                    :class="selected == record.id ? 'border-blue-500 bg-blue-50 shadow-sm ring-1 ring-blue-500' : 'border-gray-200 bg-white'">
                                    <input type="radio" name="dapodik_siswa_id" :value="record.id" x-model="selected" required class="mt-1 text-blue-600 focus:ring-blue-500">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-bold text-sm text-gray-900 truncate" x-text="record.nama || '-'"></span>
                                            <template x-if="record.rombel_saat_ini">
                                                <span class="rounded bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700 border border-indigo-100 flex-shrink-0" x-text="record.rombel_saat_ini"></span>
                                            </template>
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500">
                                            <span>NIPD: <strong class="text-gray-700" x-text="record.nipd || '-'"></strong></span>
                                            <span>•</span>
                                            <span>NISN: <strong class="text-gray-700 font-mono" x-text="record.nisn || '-'"></strong></span>
                                            <span>•</span>
                                            <span x-text="`${record.tempat_lahir || '-'}, ${formatDate(record.tanggal_lahir)}`"></span>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50/70 px-6 py-4">
                        <button type="button" @click="open = false" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
                        <button type="submit" :disabled="!selected" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors">Konfirmasi Pemetaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL PENCOCOKAN DAPODIK MASSAL (PREVIEW & PETAKAN) --}}
    <div x-data="bulkDapodikMapping()" @open-bulk-dapodik-mapping.window="openModal($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="if(!submitting) open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[90vh]" @click.stop>
                
                {{-- Header Modal --}}
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 rounded-t-2xl bg-white">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Pencocokan Massal Dapodik</h3>
                            <p class="text-xs text-gray-500">Pratinjau deteksi otomatis calon siswa dengan data Dapodik sebelum dipetakan.</p>
                        </div>
                    </div>
                    <button type="button" @click="open = false" :disabled="submitting" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 disabled:opacity-50">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Loading State --}}
                <template x-if="loading">
                    <div class="flex flex-col items-center justify-center py-20 px-6">
                        <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600 mb-4"></div>
                        <h4 class="text-base font-bold text-gray-800">Sedang Mencocokkan Data...</h4>
                        <p class="text-sm text-gray-500 mt-1 text-center max-w-md">Sistem sedang membandingkan NISN, nama lengkap, dan tanggal lahir calon siswa dengan data Dapodik yang belum dipetakan.</p>
                    </div>
                </template>

                {{-- Content Body --}}
                <template x-if="!loading">
                    <div class="flex flex-col flex-1 overflow-hidden">
                        
                        {{-- Stats Bar & Tabs --}}
                        <div class="border-b border-gray-100 bg-gray-50/70 px-6 py-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                
                                {{-- Tabs --}}
                                <div class="flex gap-2">
                                    <button type="button" @click="activeTab = 'matched'"
                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all"
                                        :class="activeTab === 'matched' ? 'bg-white text-blue-700 shadow-sm border border-blue-200' : 'text-gray-600 hover:text-gray-900'">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span>Cocok Otomatis</span>
                                        <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[10px] text-blue-800" x-text="previewData.total_matched || 0"></span>
                                    </button>
                                    <button type="button" @click="activeTab = 'unmatched'"
                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5 text-xs font-bold transition-all"
                                        :class="activeTab === 'unmatched' ? 'bg-white text-amber-700 shadow-sm border border-amber-200' : 'text-gray-600 hover:text-gray-900'">
                                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                        <span>Belum Ditemukan</span>
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] text-amber-800" x-text="previewData.total_unmatched || 0"></span>
                                    </button>
                                </div>

                                {{-- Search & Stats Summary --}}
                                <div class="flex items-center gap-3">
                                    <div class="relative w-64">
                                        <input type="search" x-model="searchQuery" placeholder="Filter nama / NISN / NIPD..."
                                            class="w-full rounded-lg border-gray-300 py-1.5 pl-8 pr-3 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        <svg class="h-4 w-4 text-gray-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </div>
                                    <span class="hidden md:inline-block text-xs text-gray-400">|</span>
                                    <span class="hidden md:inline-block text-xs text-gray-500">Dapodik siap: <strong class="text-gray-700" x-text="previewData.available_dapodik_count || 0"></strong></span>
                                </div>
                            </div>
                        </div>

                        {{-- Table Area (Scrollable) --}}
                        <div class="flex-1 overflow-y-auto p-6 max-h-[55vh]">
                            
                            {{-- TAB 1: Matched List --}}
                            <div x-show="activeTab === 'matched'">
                                <template x-if="filteredMatched.length === 0">
                                    <div class="py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                            </svg>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-sm">Tidak ada calon siswa yang cocok otomatis</h4>
                                        <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Pastikan data Dapodik terbaru telah diimpor pada menu Manajemen Dapodik.</p>
                                    </div>
                                </template>

                                <template x-if="filteredMatched.length > 0">
                                    <div>
                                        <div class="mb-3 flex items-center justify-between text-xs text-gray-500 px-1">
                                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" :checked="allMatchedSelected" @change="toggleSelectAllMatched($event.target.checked)"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="font-bold text-gray-700">Pilih Semua (<span x-text="filteredMatched.length"></span>)</span>
                                            </label>
                                            <span>Klik baris untuk memilih / membatalkan pasangan yang akan dipetakan.</span>
                                        </div>

                                        <div class="space-y-3">
                                            <template x-for="item in filteredMatched" :key="item.registration_id">
                                                <div @click="toggleItem(item)"
                                                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-xl border p-4 transition-all cursor-pointer select-none"
                                                    :class="selectedMappings[item.registration_id] ? 'border-blue-400 bg-blue-50/40 shadow-sm' : 'border-gray-200 bg-white opacity-70 hover:opacity-100 hover:border-gray-300'">
                                                    
                                                    {{-- Calon Siswa (Kiri) --}}
                                                    <div class="flex items-start gap-3 flex-1 min-w-0">
                                                        <input type="checkbox" :checked="!!selectedMappings[item.registration_id]" @click.stop="toggleItem(item)"
                                                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-bold text-sm text-gray-900 truncate" x-text="item.student_name"></span>
                                                                <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-mono text-gray-600" x-text="item.registration_number"></span>
                                                            </div>
                                                            <div class="mt-1 text-xs text-gray-500 flex flex-wrap gap-x-3 gap-y-0.5">
                                                                <span>NISN: <strong class="font-mono text-gray-700" x-text="item.student_nisn"></strong></span>
                                                                <span>Lahir: <span x-text="item.student_birth"></span></span>
                                                                <span>NIS Sem: <span class="font-mono" x-text="item.temp_nis"></span></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Match Indicator (Tengah) --}}
                                                    <div class="flex flex-col items-center justify-center px-2 flex-shrink-0">
                                                        <div class="flex items-center gap-1.5 text-xs font-bold"
                                                            :class="item.match_type === 'nisn' ? 'text-emerald-700' : 'text-blue-700'">
                                                            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                                            </svg>
                                                            <span class="rounded-full px-2 py-0.5 text-[10px] uppercase tracking-wider font-extrabold"
                                                                :class="item.match_type === 'nisn' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'"
                                                                x-text="item.match_label"></span>
                                                        </div>
                                                    </div>

                                                    {{-- Data Dapodik (Kanan) --}}
                                                    <div class="rounded-lg bg-white/80 border border-gray-100 p-3 flex-1 min-w-0 shadow-xs">
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="font-bold text-sm text-blue-900 truncate" x-text="item.dapodik_name"></span>
                                                            <span class="inline-flex items-center gap-1 rounded bg-blue-50 px-2 py-0.5 text-xs font-mono font-black text-blue-700">
                                                                NIPD: <span x-text="item.dapodik_nipd"></span>
                                                            </span>
                                                        </div>
                                                        <div class="mt-1 text-xs text-gray-500 flex flex-wrap gap-x-3 gap-y-0.5">
                                                            <span>NISN: <span class="font-mono" x-text="item.dapodik_nisn"></span></span>
                                                            <span>Lahir: <span x-text="item.dapodik_birth"></span></span>
                                                            <span>Rombel: <span class="text-gray-700 font-semibold" x-text="item.dapodik_rombel"></span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- TAB 2: Unmatched List --}}
                            <div x-show="activeTab === 'unmatched'">
                                <template x-if="filteredUnmatched.length === 0">
                                    <div class="py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-sm">Semua Calon Siswa Berhasil Dicocokkan!</h4>
                                        <p class="text-xs text-gray-500 mt-1">Tidak ada calon siswa yang belum memiliki pasangan Dapodik.</p>
                                    </div>
                                </template>

                                <template x-if="filteredUnmatched.length > 0">
                                    <div class="space-y-2">
                                        <div class="rounded-lg bg-amber-50 p-3 border border-amber-200 text-xs text-amber-800 mb-3">
                                            Calon siswa di bawah ini tidak ditemukan padanannya di data Dapodik berdasarkan NISN maupun nama & tanggal lahir. Anda dapat melakukan pencarian manual jika terdapat perbedaan ejaan nama atau nomor NISN.
                                        </div>
                                        <template x-for="item in filteredUnmatched" :key="item.registration_id">
                                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-gray-50">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="font-bold text-sm text-gray-900" x-text="item.student_name"></span>
                                                        <span class="text-xs text-gray-400" x-text="`(${item.registration_number})`"></span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-0.5 flex gap-4">
                                                        <span>NISN: <span class="font-mono text-gray-700" x-text="item.student_nisn"></span></span>
                                                        <span>Lahir: <span x-text="item.student_birth"></span></span>
                                                        <span>NIS Sem: <span class="font-mono" x-text="item.temp_nis"></span></span>
                                                    </div>
                                                </div>
                                                <button type="button" @click="open = false; $dispatch('open-dapodik-mapping', { id: item.registration_id, name: item.student_name, nisn: item.student_nisn !== '-' ? item.student_nisn : '', birth: item.student_birth, url: `/master-data/registrasi-siswa-baru/${item.registration_id}/map` })"
                                                    class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100 flex-shrink-0">
                                                    Cari Manual
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                        </div>

                        {{-- Footer Modal --}}
                        <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl">
                            <div>
                                <span class="text-xs text-gray-600">Terpilih untuk dipetakan: <strong class="text-blue-700 font-extrabold text-sm" x-text="selectedCount"></strong> dari <span x-text="previewData.total_matched || 0"></span> data cocok</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="open = false" :disabled="submitting"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-100 disabled:opacity-50">
                                    Batal
                                </button>
                                <button type="button" @click="submitBulkMapping()" :disabled="submitting || selectedCount === 0"
                                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-all disabled:cursor-not-allowed disabled:bg-gray-300">
                                    <template x-if="submitting">
                                        <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                    </template>
                                    <svg x-show="!submitting" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Petakan (<span x-text="selectedCount"></span>) Siswa</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>
    </div>

    {{-- MODAL DETAIL BIODATA SISWA BARU --}}
    <div x-data="biodataDetailModal()" @open-biodata-detail.window="openModal($event.detail)" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl">
                {{-- Header Modal --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-8 py-6 rounded-t-2xl">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900" x-text="student.name"></h3>
                        <p class="mt-1 text-sm text-gray-500">Detail Biodata Calon Siswa</p>
                    </div>
                    <button @click="open = false" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Content Area --}}
                <div class="max-h-[70vh] overflow-y-auto px-8 py-6">
                    <template x-if="loading">
                        <div class="flex items-center justify-center py-12">
                            <div class="flex flex-col items-center">
                                <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
                                <p class="mt-4 text-sm font-medium text-gray-600">Memuat data biodata...</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loading && student.data">
                        <div class="space-y-8">
                            {{-- Section 1: Identitas Dasar --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-blue-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    Identitas Dasar
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Lengkap</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nama_lengkap || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">NISN</label>
                                        <p class="mt-1 font-mono text-lg font-medium text-gray-900" x-text="student.data.nisn || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tempat, Tanggal Lahir</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900">
                                            <span x-text="student.data.tempat_lahir || '-'"></span>, 
                                            <span x-text="student.data.tanggal_lahir ? new Date(student.data.tanggal_lahir).toLocaleDateString('id-ID') : '-'"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis Kelamin</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900">
                                            <span x-text="student.data.jenis_kelamin === 'L' ? 'Laki-laki' : (student.data.jenis_kelamin === 'P' ? 'Perempuan' : '-')"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">NIK</label>
                                        <p class="mt-1 font-mono text-lg font-medium text-gray-900" x-text="student.data.nik || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor Registrasi</label>
                                        <p class="mt-1 font-mono text-lg font-medium text-gray-900" x-text="student.data.registration_number || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Kontak & Alamat --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-green-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Kontak & Alamat
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nomor HP</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nomor_hp || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Email</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.email || '-'"></p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Alamat Lengkap</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.alamat || 'Alamat belum diisi'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Sekolah & Keluarga --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-amber-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sekolah & Keluarga
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sekolah Asal</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.sekolah_asal || 'Sekolah asal belum diisi'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Orang Tua/Wali</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nama_orang_tua || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">HP Orang Tua/Wali</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.nomor_hp_orang_tua || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Pendaftaran</label>
                                        <p class="mt-1 text-lg font-medium text-gray-900" x-text="student.data.created_at ? new Date(student.data.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-'"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4: Status & Informasi Tambahan --}}
                            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-purple-50/50 to-white p-6">
                                <h4 class="mb-4 text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Status & Informasi
                                </h4>
                                <div class="flex flex-wrap gap-4">
                                    <div class="rounded-lg bg-gray-100 px-4 py-3">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                        <p class="mt-1">
                                            <span x-show="student.data.status === 'pending'" class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-amber-500"></span>
                                                Menunggu Verifikasi
                                            </span>
                                            <span x-show="student.data.status === 'approved'" class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-blue-500"></span>
                                                Disetujui
                                            </span>
                                            <span x-show="student.data.status === 'rejected'" class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-red-500"></span>
                                                Ditolak
                                            </span>
                                            <span x-show="student.data.status === 'mapped'" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                <span class="mr-1.5 h-2 w-2 rounded-full bg-green-500"></span>
                                                Sudah Dipetakan
                                            </span>
                                        </p>
                                    </div>
                                    <div class="rounded-lg bg-gray-100 px-4 py-3">
                                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sumber Data</label>
                                        <p class="mt-1">
                                            <span x-show="student.data.source === 'public'" class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                Form Publik
                                            </span>
                                            <span x-show="student.data.source === 'manual'" class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                Input Petugas
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!loading && !student.data">
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-4 text-lg font-medium text-gray-900">Data tidak ditemukan</p>
                            <p class="mt-1 text-sm text-gray-500">Biodata siswa tidak dapat dimuat</p>
                        </div>
                    </template>
                </div>

                {{-- Footer dengan Tombol Aksi --}}
                <div class="sticky bottom-0 z-10 border-t border-gray-200 bg-white px-8 py-6 rounded-b-2xl">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-500">
                            <p>ID Registrasi: <span class="font-mono font-medium" x-text="student.id || '-'"></span></p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button @click="open = false" class="rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50">
                                Tutup
                            </button>
                            <template x-if="student.data && student.data.status === 'pending'">
                                <div class="flex gap-3">
                                    <form :action="student.data.reject_url" method="POST" class="reject-form">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="notes" :value="rejectReason">
                                        <button type="button" @click="showRejectDialog()" class="rounded-lg border border-red-300 bg-white px-6 py-3 text-sm font-bold text-red-700 hover:bg-red-50">
                                            Tolak
                                        </button>
                                    </form>
                                    <form :action="student.data.approve_url" method="POST" class="approve-form">
                                        @csrf @method('PATCH')
                                        <button type="button" @click="confirmApprove()" class="rounded-lg bg-green-600 px-6 py-3 text-sm font-bold text-white hover:bg-green-700">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function bulkStudentApproval(students, statementCount) {
            return {
                students,
                selected: [],
                checkedStatements: [],
                pactOpen: false,
                get allSelected() {
                    return this.students.length > 0 && this.selected.length === this.students.length;
                },
                get selectedStudents() {
                    return this.students.filter(student => this.selected.includes(String(student.id)));
                },
                get allStatementsChecked() {
                    return this.checkedStatements.length === statementCount;
                },
                toggleAll(checked) {
                    this.selected = checked ? this.students.map(student => String(student.id)) : [];
                },
                toggleStatement(key, checked) {
                    if (checked && !this.checkedStatements.includes(key)) {
                        this.checkedStatements.push(key);
                    }
                    if (!checked) {
                        this.checkedStatements = this.checkedStatements.filter(statement => statement !== key);
                    }
                },
                openPact() {
                    if (this.selected.length === 0) {
                        return;
                    }
                    this.checkedStatements = [];
                    this.pactOpen = true;
                    this.$nextTick(() => {
                        this.$root.querySelectorAll('input[name^="statements["]').forEach(input => {
                            input.checked = false;
                        });
                    });
                },
            };
        }

        // Fungsi untuk modal detail biodata
        function biodataDetailModal() {
            return {
                open: false,
                student: { id: null, name: '', data: null },
                loading: false,
                rejectReason: '',
                openModal(data) {
                    this.student = {
                        id: data.id,
                        name: data.name,
                        data: null
                    };
                    this.open = true;
                    this.loading = true;
                    this.loadBiodata(data.id);
                },
                async loadBiodata(id) {
                    try {
                        const url = `/master-data/registrasi-siswa-baru/${id}/biodata`;
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.student.data = data;
                            // Tambahkan URL untuk action
                            if (this.student.data) {
                                this.student.data.approve_url = `/master-data/registrasi-siswa-baru/${id}/approve`;
                                this.student.data.reject_url = `/master-data/registrasi-siswa-baru/${id}/reject`;
                            }
                        }
                    } catch (error) {
                        console.error('Error loading biodata:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                confirmApprove() {
                    Swal.fire({
                        title: 'Setujui pendaftaran?',
                        text: 'Data siswa sementara akan dibuat menjadi data master.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, setujui',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10b981',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submitApprove();
                        }
                    });
                },
                submitApprove() {
                    const form = document.querySelector('.approve-form');
                    if (form) {
                        form.submit();
                    } else {
                        // Fallback jika form tidak ditemukan
                        window.location.href = this.student.data.approve_url;
                    }
                },
                showRejectDialog() {
                    Swal.fire({
                        title: 'Alasan Penolakan',
                        input: 'textarea',
                        inputLabel: 'Tuliskan alasan penolakan atau data yang perlu diperbaiki',
                        inputPlaceholder: 'Contoh: Data NISN tidak valid, alamat tidak lengkap, dll.',
                        inputAttributes: {
                            'aria-label': 'Alasan penolakan'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Konfirmasi Penolakan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#ef4444',
                        preConfirm: (value) => {
                            if (!value || value.trim() === '') {
                                Swal.showValidationMessage('Alasan penolakan wajib diisi');
                                return false;
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.rejectReason = result.value;
                            this.submitReject();
                        }
                    });
                },
                submitReject() {
                    const form = document.querySelector('.reject-form');
                    if (form) {
                        const notesInput = form.querySelector('[name="notes"]');
                        if (notesInput) {
                            notesInput.value = this.rejectReason;
                        }
                        form.submit();
                    } else {
                        // Fallback jika form tidak ditemukan
                        fetch(this.student.data.reject_url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ notes: this.rejectReason })
                        }).then(response => {
                            if (response.ok) {
                                location.reload();
                            }
                        });
                    }
                }
            };
        }
        function confirmApprove(button) { Swal.fire({title:'Setujui pendaftaran?',text:'Data siswa sementara akan dibuat.',icon:'question',showCancelButton:true,confirmButtonText:'Ya, setujui'}).then(r => { if(r.isConfirmed) button.closest('form').submit(); }); }
        function confirmReject(button) { Swal.fire({title:'Alasan penolakan',input:'textarea',inputPlaceholder:'Tuliskan data yang perlu diperbaiki',showCancelButton:true,confirmButtonColor:'#dc2626',confirmButtonText:'Tolak pendaftaran',preConfirm:value => { if(!value) Swal.showValidationMessage('Alasan wajib diisi'); return value; }}).then(r => { if(r.isConfirmed){ const form=button.closest('form'); form.querySelector('[name=notes]').value=r.value; form.submit(); }}); }
        function dapodikMapping() {
            return {
                open: false,
                student: {},
                query: '',
                results: [],
                selected: null,
                loading: false,
                searchBy(val) {
                    if (!val) return;
                    this.query = val;
                    this.selected = null;
                    this.search();
                },
                formatDate(val) {
                    if (!val) return '-';
                    const clean = String(val).split('T')[0];
                    const parts = clean.split('-');
                    if (parts.length === 3) {
                        return `${parts[2]}-${parts[1]}-${parts[0]}`;
                    }
                    return clean;
                },
                async openModal(data) {
                    this.student = data || {};
                    this.selected = null;
                    this.results = [];
                    this.open = true;

                    if (this.student.nisn) {
                        this.query = this.student.nisn;
                        await this.search();
                        // If no matches found by NISN, automatically fallback to searching by student name
                        if (this.results.length === 0 && this.student.name) {
                            this.query = this.student.name;
                            await this.search();
                        }
                    } else if (this.student.name) {
                        this.query = this.student.name;
                        await this.search();
                    } else {
                        this.query = '';
                    }
                },
                async search() {
                    const q = (this.query || '').trim();
                    this.loading = true;
                    try {
                        const url = @js(route('master-data.student-registration.dapodik.search'));
                        const response = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            this.results = await response.json();
                        } else {
                            this.results = [];
                        }
                    } catch (err) {
                        console.error(err);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }

        function bulkDapodikMapping() {
            return {
                open: false,
                loading: false,
                submitting: false,
                activeTab: 'matched',
                searchQuery: '',
                targetIds: [],
                previewData: {
                    total_candidates: 0,
                    total_matched: 0,
                    total_unmatched: 0,
                    available_dapodik_count: 0,
                    matched: [],
                    unmatched: []
                },
                selectedMappings: {},

                async openModal(detail) {
                    this.targetIds = detail && detail.ids ? detail.ids : [];
                    this.open = true;
                    this.activeTab = 'matched';
                    this.searchQuery = '';
                    await this.fetchPreview();
                },

                async fetchPreview() {
                    this.loading = true;
                    try {
                        let url = @js(route('master-data.student-registration.dapodik.bulk-match-preview'));
                        if (this.targetIds.length > 0) {
                            url += `?ids=${encodeURIComponent(this.targetIds.join(','))}`;
                        }
                        const response = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!response.ok) throw new Error('Gagal memuat preview pemetaan');
                        const data = await response.json();
                        this.previewData = data;

                        this.selectedMappings = {};
                        (data.matched || []).forEach(item => {
                            this.selectedMappings[item.registration_id] = item.dapodik_id;
                        });
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Data',
                            text: err.message || 'Terjadi kesalahan saat mencocokkan data Dapodik.'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                get matchedCount() {
                    return this.previewData.total_matched || 0;
                },

                get selectedCount() {
                    return Object.keys(this.selectedMappings).filter(k => !!this.selectedMappings[k]).length;
                },

                get allMatchedSelected() {
                    return this.filteredMatched.length > 0 &&
                        this.filteredMatched.every(item => !!this.selectedMappings[item.registration_id]);
                },

                toggleSelectAllMatched(checked) {
                    this.filteredMatched.forEach(item => {
                        if (checked) {
                            this.selectedMappings[item.registration_id] = item.dapodik_id;
                        } else {
                            delete this.selectedMappings[item.registration_id];
                        }
                    });
                },

                toggleItem(item) {
                    if (this.selectedMappings[item.registration_id]) {
                        delete this.selectedMappings[item.registration_id];
                    } else {
                        this.selectedMappings[item.registration_id] = item.dapodik_id;
                    }
                },

                get filteredMatched() {
                    if (!this.searchQuery) return this.previewData.matched || [];
                    const q = this.searchQuery.toLowerCase();
                    return (this.previewData.matched || []).filter(item =>
                        (item.student_name && item.student_name.toLowerCase().includes(q)) ||
                        (item.dapodik_name && item.dapodik_name.toLowerCase().includes(q)) ||
                        (item.dapodik_nipd && item.dapodik_nipd.toLowerCase().includes(q)) ||
                        (item.student_nisn && item.student_nisn.toLowerCase().includes(q))
                    );
                },

                get filteredUnmatched() {
                    if (!this.searchQuery) return this.previewData.unmatched || [];
                    const q = this.searchQuery.toLowerCase();
                    return (this.previewData.unmatched || []).filter(item =>
                        (item.student_name && item.student_name.toLowerCase().includes(q)) ||
                        (item.student_nisn && item.student_nisn.toLowerCase().includes(q))
                    );
                },

                async submitBulkMapping() {
                    const pairs = [];
                    for (const [regId, dapId] of Object.entries(this.selectedMappings)) {
                        if (dapId) {
                            pairs.push({
                                registration_id: parseInt(regId),
                                dapodik_siswa_id: parseInt(dapId)
                            });
                        }
                    }

                    if (pairs.length === 0) {
                        Swal.fire('Pilih Data', 'Pilih minimal satu pasangan siswa yang akan dipetakan.', 'warning');
                        return;
                    }

                    const result = await Swal.fire({
                        title: `Petakan ${pairs.length} Siswa?`,
                        text: 'Data siswa sementara akan diperbarui dengan data Dapodik resmi (NIPD, identitas terverifikasi).',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: `Ya, Petakan Sekarang (${pairs.length})`,
                        cancelButtonText: 'Batal'
                    });

                    if (!result.isConfirmed) return;

                    this.submitting = true;
                    try {
                        const response = await fetch(@js(route('master-data.student-registration.dapodik.bulk-map')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ mappings: pairs })
                        });

                        const resData = await response.json();

                        if (response.ok && resData.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Pemetaan Berhasil!',
                                text: resData.message || `${pairs.length} siswa berhasil dipetakan ke Dapodik.`,
                                confirmButtonColor: '#10b981'
                            });
                            window.location.reload();
                        } else {
                            throw new Error(resData.message || 'Gagal memproses pemetaan massal.');
                        }
                    } catch (err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Pemetaan',
                            text: err.message || 'Terjadi kesalahan sistem saat memetakan data.'
                        });
                    } finally {
                        this.submitting = false;
                    }
                }
            };
        }

        @if (session('pact_download_url'))
            document.addEventListener('DOMContentLoaded', () => {
                const downloadFrame = document.createElement('iframe');
                downloadFrame.className = 'hidden';
                downloadFrame.src = @js(session('pact_download_url'));
                downloadFrame.title = 'Unduh pakta integritas';
                document.body.appendChild(downloadFrame);
                window.setTimeout(() => downloadFrame.remove(), 30000);
            });
        @endif
    </script>
    

@endpush
</x-app-layout>



