<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Data Dapodik Guru</h2>
        <p class="text-sm text-gray-500 mt-0.5">Integrasi data Dapodik dengan data pegawai berdasarkan NIK</p>
    </x-slot>

    <div
        x-data="{ showImport: false }"
        class="relative">

        {{-- ── IMPORT MODAL ── --}}
        <div x-show="showImport" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
            <div x-show="showImport"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

            <div x-show="showImport"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="h-1.5 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div class="p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900">Import Data Dapodik</h3>
                                <p class="text-xs text-gray-400">Sinkronisasi data guru dari aplikasi Dapodik</p>
                            </div>
                        </div>
                        <button @click="showImport=false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Petunjuk --}}
                    <div class="space-y-3 mb-5">
                        <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                            <p class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-2">Cara mendapatkan file</p>
                            <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside leading-relaxed">
                                <li>Buka aplikasi <strong>Dapodik</strong> di browser</li>
                                <li>Masuk ke menu <strong>GTK → Daftar GTK</strong></li>
                                <li>Klik tombol <strong>"Unduh"</strong> / <strong>"Export Excel"</strong></li>
                                <li>Upload file hasil unduhan di bawah ini</li>
                            </ol>
                        </div>

                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex gap-3">
                            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="text-xs text-amber-700 leading-relaxed">
                                <strong class="font-bold">Gunakan file asli dari Dapodik!</strong> Jangan ubah format, urutan kolom, atau hapus baris header. Pencocokan data dilakukan berdasarkan <strong>NIK</strong> — pastikan data NIK pegawai sudah diisi di halaman Manajemen Pegawai.
                            </div>
                        </div>
                    </div>

                    {{-- Upload form --}}
                    <form method="POST" action="{{ route('dapodik-guru.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div x-data="{ fileName: '' }">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all group">
                                <div x-show="!fileName" class="flex flex-col items-center gap-2 pointer-events-none">
                                    <svg class="w-9 h-9 text-gray-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-sm text-gray-400">Klik untuk pilih file <strong>.xlsx</strong> dari Dapodik</span>
                                    <span class="text-xs text-gray-300">Maks. 10 MB</span>
                                </div>
                                <div x-show="fileName" class="flex items-center gap-2 pointer-events-none">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-sm font-semibold text-blue-700" x-text="fileName"></span>
                                </div>
                                <input type="file" name="file_import" accept=".xlsx,.xls" required class="hidden"
                                    @change="fileName = $event.target.files[0]?.name ?? ''">
                            </label>
                        </div>

                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" @click="showImport=false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-500 transition-all shadow-md">
                                Mulai Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- PAGE CONTENT --}}
        <div class="py-6">
            <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

                {{-- Import errors --}}
                @if(session('dapodik_import_errors'))
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-bold text-red-700 mb-2">Beberapa baris gagal diproses:</p>
                                <ul class="space-y-1">
                                    @foreach(session('dapodik_import_errors') as $err)
                                        <li class="text-xs text-red-600">• {{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalDapodik }}</div>
                            <div class="text-xs font-semibold text-gray-400">Total Data Dapodik</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalLinked }}</div>
                            <div class="text-xs font-semibold text-gray-400">Terhubung ke Pegawai</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-gray-900">{{ $totalUnlinked }}</div>
                            <div class="text-xs font-semibold text-gray-400">Belum Terhubung</div>
                        </div>
                    </div>
                </div>

                {{-- Search & Filter --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <form method="GET" action="{{ route('dapodik-guru.index') }}" class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, NUPTK, NIP..."
                                class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <select name="jenis_ptk" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                            <option value="">Semua Jenis PTK</option>
                            @foreach($jenisPtkList as $ptk)
                                <option value="{{ $ptk }}" {{ request('jenis_ptk') === $ptk ? 'selected' : '' }}>{{ $ptk }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 min-w-[160px]">
                            <option value="">Semua Status</option>
                            <option value="linked" {{ request('status') === 'linked' ? 'selected' : '' }}>Terhubung</option>
                            <option value="unlinked" {{ request('status') === 'unlinked' ? 'selected' : '' }}>Belum Terhubung</option>
                        </select>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-500 transition-colors shrink-0">Filter</button>
                        @if(request('search') || request('status') || request('jenis_ptk'))
                            <a href="{{ route('dapodik-guru.index') }}" class="px-4 py-2.5 border border-gray-200 text-gray-500 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors text-center shrink-0">Reset</a>
                        @endif
                    </form>
                </div>

                {{-- Table card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900">Daftar Data Dapodik Guru</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Menampilkan {{ $dapodikGurus->count() }} dari {{ $dapodikGurus->total() }} data</p>
                        </div>
                        <button @click="showImport = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Import Dapodik
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider w-8">#</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Guru</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">NIK / NUPTK</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis PTK</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status Kepegawaian</th>
                                    <th class="text-left px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Link Akun</th>
                                    <th class="text-right px-6 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse ($dapodikGurus as $item)
                                    <tr class="hover:bg-gray-50/70 transition-colors group">
                                        <td class="px-6 py-4 text-gray-400 font-mono text-xs">{{ $dapodikGurus->firstItem() + $loop->index }}</td>

                                        {{-- Nama --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($item->masterGuru?->user?->avatar)
                                                    <img src="{{ $item->masterGuru->user->avatar }}" class="w-9 h-9 rounded-full object-cover border border-gray-200" alt="">
                                                @else
                                                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0 text-blue-600 font-black text-sm border border-blue-200">
                                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-gray-900 truncate">{{ $item->nama }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : '') }}
                                                        @if($item->agama) · {{ $item->agama }} @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- NIK / NUPTK --}}
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                @if($item->nik)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] font-bold text-slate-400 uppercase">NIK</span>
                                                        <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">{{ $item->nik }}</span>
                                                    </div>
                                                @endif
                                                @if($item->nuptk)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] font-bold text-indigo-400 uppercase">NUPTK</span>
                                                        <span class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded-md">{{ $item->nuptk }}</span>
                                                    </div>
                                                @endif
                                                @if(!$item->nik && !$item->nuptk)
                                                    <span class="text-xs text-gray-300">—</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Jenis PTK --}}
                                        <td class="px-6 py-4">
                                            @if($item->jenis_ptk)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $item->jenis_ptk }}</span>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>

                                        {{-- Status Kepegawaian --}}
                                        <td class="px-6 py-4">
                                            @if($item->status_kepegawaian)
                                                @php
                                                    $sc = match(true) {
                                                        str_contains($item->status_kepegawaian, 'PNS')    => 'bg-blue-50 text-blue-700 border-blue-100',
                                                        str_contains($item->status_kepegawaian, 'Honor')  => 'bg-amber-50 text-amber-700 border-amber-100',
                                                        str_contains($item->status_kepegawaian, 'PPPK')   => 'bg-teal-50 text-teal-700 border-teal-100',
                                                        default => 'bg-gray-50 text-gray-600 border-gray-100',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $sc }}">{{ $item->status_kepegawaian }}</span>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>

                                        {{-- Link status --}}
                                        <td class="px-6 py-4">
                                            @if($item->masterGuru)
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                                                    <span class="text-xs text-green-700 font-semibold truncate max-w-[120px]">{{ $item->masterGuru->user?->name ?? $item->masterGuru->nama_lengkap }}</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-gray-300 shrink-0"></span>
                                                    <span class="text-xs text-gray-400">Tidak terhubung</span>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a href="{{ route('dapodik-guru.show', $item) }}"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600 hover:bg-blue-50 transition-colors" title="Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('dapodik-guru.edit', $item) }}"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 hover:bg-amber-50 transition-colors" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center border border-gray-100">
                                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">Belum ada data Dapodik</p>
                                                    <p class="text-sm text-gray-400 mt-0.5">Klik "Import Dapodik" untuk mengunggah data dari aplikasi Dapodik</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($dapodikGurus->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $dapodikGurus->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
