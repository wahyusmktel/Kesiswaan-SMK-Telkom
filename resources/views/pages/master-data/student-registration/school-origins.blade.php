<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Pengelolaan Sekolah Asal</h2>
                <p class="text-sm text-gray-500 font-medium">Kelompokkan, bersihkan, dan normalisasikan penulisan sekolah asal pendaftar agar akurat.</p>
            </div>
            <div>
                <a href="{{ route('master-data.student-registration.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Registrasi
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="w-full space-y-5 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 shadow-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-900 shadow-sm">
                <div class="flex gap-2">
                    <svg class="h-5 w-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <strong class="font-bold">Algoritma Normalisasi & Kemiripan Otomatis</strong>
                        <p class="mt-1 font-normal">Sistem secara otomatis mendeteksi kemiripan nama sekolah asal menggunakan algoritma <em>Levenshtein Distance</em> dan pengenalan pola singkatan (seperti mendeteksi <code class="bg-blue-100 px-1 py-0.5 rounded font-mono">SMP Negeri</code>, <code class="bg-blue-100 px-1 py-0.5 rounded font-mono">SMPN</code>, <code class="bg-blue-100 px-1 py-0.5 rounded font-mono">SMPN1</code>, dan typo serupa). Anda dapat menyeragamkan nama-nama variasi tersebut menjadi satu nama resmi yang bersih di bawah ini.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <div>
                    <h3 class="font-bold text-gray-900">Pencarian & Filter</h3>
                    <p class="text-xs text-gray-500">Cari berdasarkan nama sekolah asal atau variasi penulisannya</p>
                </div>
                <form method="GET" class="flex gap-2">
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama sekolah asal..." class="w-72 rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <button class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-bold text-white hover:bg-gray-800">Cari</button>
                    @if($search)
                        <a href="{{ route('master-data.student-registration.school-origins') }}" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center">Reset</a>
                    @endif
                </form>
            </div>

            <div class="grid gap-6 md:grid-cols-1 lg:grid-cols-2">
                @forelse ($groups as $index => $group)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between">
                                <div class="space-y-1">
                                    <h4 class="text-base font-bold text-gray-900">{{ $group['canonical_name'] }}</h4>
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700 border border-red-100">
                                        {{ $group['total_registrations'] }} Pendaftar
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 font-mono">Grup #{{ $index + 1 }}</span>
                            </div>

                            <div class="mt-4 space-y-2">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Variasi Penulisan di Database:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($group['variations'] as $variation)
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600 border border-gray-200">
                                            "{{ $variation }}"
                                            <span class="ml-1.5 text-[10px] font-bold text-gray-400 bg-gray-200/60 px-1 rounded">
                                                {{ count(array_filter($group['registrations'], function($r) use ($variation) { return trim($r->sekolah_asal) === $variation; })) }}
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4">
                                <details class="group border-t border-gray-100 pt-3">
                                    <summary class="text-xs font-bold text-red-600 hover:text-red-700 cursor-pointer select-none flex items-center gap-1">
                                        <svg class="h-3 w-3 transform group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        Daftar Calon Siswa ({{ $group['total_registrations'] }})
                                    </summary>
                                    <ul class="mt-2 divide-y divide-gray-100 text-xs text-gray-600 max-h-40 overflow-y-auto pr-1">
                                        @foreach ($group['registrations'] as $student)
                                            <li class="py-2 flex justify-between items-center gap-4">
                                                <div class="min-w-0">
                                                    <span class="font-bold text-gray-900 block truncate">{{ $student->nama_lengkap }}</span>
                                                    <span class="text-gray-400">NISN: {{ $student->nisn ?: '-' }} &middot; Reg: {{ $student->registration_number }}</span>
                                                </div>
                                                <div class="shrink-0 text-right">
                                                    <span class="text-gray-500 italic block font-mono text-[10px]">"{{ $student->sekolah_asal }}"</span>
                                                    <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold {{ $student->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($student->status === 'approved' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                                        {{ strtoupper($student->status) }}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('master-data.student-registration.update-school-origins') }}" class="mt-5 border-t border-gray-100 pt-4">
                            @csrf
                            @foreach ($group['variations'] as $variation)
                                <input type="hidden" name="original_names[]" value="{{ $variation }}">
                            @endforeach
                            <label class="block text-xs font-bold text-gray-700">Seragamkan Penulisan Nama Sekolah:</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" name="standardized_name" value="{{ $group['canonical_name'] }}" required class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 py-1.5">
                                <button type="submit" class="shrink-0 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-500 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menyeragamkan penulisan sekolah asal untuk seluruh pendaftar di grup ini?')">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-lg border border-gray-200 p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-bold text-gray-900">Tidak Ada Data Sekolah Asal</h3>
                        <p class="mt-1 text-xs text-gray-500">@if($search) Tidak ditemukan sekolah asal yang cocok dengan pencarian Anda. @else Saat ini belum ada data pendaftar dengan sekolah asal yang terisi. @endif</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
