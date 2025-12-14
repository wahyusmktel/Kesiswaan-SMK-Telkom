<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pendataan Siswa Terlambat</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden sticky top-24">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                1. Cari Siswa
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Masukkan Nama atau NIS siswa yang terlambat.</p>
                        </div>

                        <div class="p-6">
                            <form action="{{ route('security.pendataan-terlambat.index') }}" method="GET">
                                <div class="space-y-4">
                                    <div>
                                        <label for="search" class="sr-only">Cari Siswa</label>
                                        <div class="relative">
                                            <input type="text" id="search" name="search"
                                                value="{{ request('search') }}"
                                                class="block w-full pl-4 pr-10 py-3 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Ketik Nama / NIS..." required autofocus>
                                            <div
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-lg transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                                        Cari Data
                                    </button>
                                    @if (request('search'))
                                        <a href="{{ route('security.pendataan-terlambat.index') }}"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                            Reset
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden min-h-[400px]">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                2. Input Keterlambatan
                            </h3>
                        </div>

                        <div class="p-6">
                            @if (isset($hasilPencarian))

                                @if ($hasilPencarian->count() == 1)
                                    @php $siswa = $hasilPencarian->first(); @endphp

                                    <div class="flex flex-col md:flex-row gap-6">
                                        <div class="w-full md:w-1/3">
                                            <div
                                                class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-center text-white shadow-md">
                                                <div
                                                    class="h-20 w-20 mx-auto rounded-full bg-white/20 backdrop-blur-md border-4 border-white/30 flex items-center justify-center text-3xl font-bold mb-4 shadow-sm">
                                                    {{ substr($siswa->nama_lengkap, 0, 1) }}
                                                </div>
                                                <h4 class="text-lg font-bold leading-tight">{{ $siswa->nama_lengkap }}
                                                </h4>
                                                <p class="text-blue-100 text-sm font-mono mt-1">{{ $siswa->nis }}</p>
                                                <div
                                                    class="mt-4 inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-bold">
                                                    {{ $siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-full md:w-2/3">
                                            <form action="{{ route('security.pendataan-terlambat.store') }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="master_siswa_id"
                                                    value="{{ $siswa->id }}">

                                                <div class="space-y-4">
                                                    <div>
                                                        <label for="alasan_siswa"
                                                            class="block text-sm font-bold text-gray-700 mb-2">Alasan
                                                            Terlambat (Menurut Siswa)</label>
                                                        <textarea name="alasan_siswa" id="alasan_siswa" rows="4"
                                                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50 focus:bg-white transition-colors"
                                                            placeholder="Contoh: Ban bocor, macet, bangun kesiangan..." required autofocus>{{ old('alasan_siswa') }}</textarea>
                                                    </div>

                                                    <div class="flex items-center justify-between pt-2">
                                                        <span class="text-xs text-gray-500 italic">*Data akan disimpan &
                                                            notifikasi dikirim ke Guru Piket.</span>
                                                        <button type="submit"
                                                            class="inline-flex justify-center items-center px-6 py-3 bg-green-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none shadow-lg hover:shadow-green-500/30 transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                                                            Simpan Data
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @elseif ($hasilPencarian->count() > 1)
                                    <div class="text-center py-4">
                                        <div class="inline-flex p-3 bg-yellow-100 rounded-full text-yellow-600 mb-3">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-800">Ditemukan
                                            {{ $hasilPencarian->count() }} siswa serupa</h4>
                                        <p class="text-sm text-gray-500 mb-6">Silakan pilih siswa yang dimaksud atau
                                            cari menggunakan NIS agar lebih spesifik.</p>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left">
                                            @foreach ($hasilPencarian as $s)
                                                <a href="{{ route('security.pendataan-terlambat.index', ['search' => $s->nis]) }}"
                                                    class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all group">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold group-hover:bg-white group-hover:text-indigo-600">
                                                        {{ substr($s->nama_lengkap, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-gray-800 group-hover:text-indigo-700">
                                                            {{ $s->nama_lengkap }}</p>
                                                        <p class="text-xs text-gray-500">{{ $s->nis }} •
                                                            {{ $s->rombels->first()?->kelas->nama_kelas ?? '-' }}</p>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <div class="bg-red-50 p-4 rounded-full mb-4">
                                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">Siswa Tidak Ditemukan</h3>
                                        <p class="text-gray-500 max-w-sm mt-1">Pastikan nama atau NIS yang dimasukkan
                                            benar. Coba gunakan kata kunci lain.</p>
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-center opacity-60">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/searching-data-2978384-2476761.png"
                                        alt="Search Illustration" class="w-48 h-auto mb-6 grayscale">
                                    <h3 class="text-xl font-bold text-gray-400">Menunggu Pencarian...</h3>
                                    <p class="text-gray-400">Gunakan kolom di sebelah kiri untuk mencari siswa.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
