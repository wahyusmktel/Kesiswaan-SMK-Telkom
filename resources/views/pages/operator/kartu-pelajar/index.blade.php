<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Kartu Pelajar</h2>
                <p class="text-xs text-gray-500 mt-1">Cetak & ekspor kartu tanda pelajar standar CR80 (8.6 x 5.4 cm) dengan barcode 1D dan tanda tangan digital.</p>
            </div>
            <div class="flex items-center gap-2">
                @if (!empty($kepsekData['signature_path']))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        TTD Digital Kepsek Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200" title="Kepala Sekolah belum mengatur tanda tangan digital">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        TTD Digital Kepsek Belum Disetel
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="kartuPelajarManager()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filter & Action Bar Card --}}
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-5">
                <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
                    
                    {{-- Form Filter --}}
                    <form action="{{ route('operator.kartu-pelajar.index') }}" method="GET" class="flex flex-wrap items-center gap-3 flex-1">
                        {{-- Dropdown Kelas / Rombel --}}
                        <div class="w-full sm:w-56">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Pilih Kelas</label>
                            <select name="rombel_id" onchange="this.form.submit()"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($rombels as $r)
                                    <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                                        {{ $r->kelas->nama_kelas ?? 'Kelas ' . $r->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dropdown Status Foto --}}
                        <div class="w-full sm:w-44">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Foto</label>
                            <select name="status_foto" onchange="this.form.submit()"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                <option value="">Semua Status</option>
                                <option value="ada" {{ request('status_foto') == 'ada' ? 'selected' : '' }}>Sudah Ada Foto</option>
                                <option value="belum" {{ request('status_foto') == 'belum' ? 'selected' : '' }}>Belum Ada Foto</option>
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="w-full sm:w-64">
                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Siswa</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="w-full rounded-xl border-gray-300 pl-9 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                    placeholder="NIS atau Nama Siswa...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-end pt-5">
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-xl text-sm font-semibold hover:bg-gray-900 transition">
                                Cari
                            </button>
                            @if (request()->hasAny(['rombel_id', 'status_foto', 'search']))
                                <a href="{{ route('operator.kartu-pelajar.index') }}" class="ml-2 px-3 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0">
                        {{-- Upload Masal Foto --}}
                        <button @click="openUploadModal()" type="button"
                            class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-semibold text-xs uppercase tracking-wider shadow-sm transition gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Upload Masal Foto
                        </button>

                        {{-- Cetak Per Kelas --}}
                        @if (request('rombel_id'))
                            <a href="{{ route('operator.kartu-pelajar.cetak-kelas', ['rombel_id' => request('rombel_id')]) }}" target="_blank"
                                class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-xs uppercase tracking-wider shadow-sm transition gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Kelas Ini
                            </a>
                        @else
                            <button type="button" onclick="alert('Silakan pilih kelas terlebih dahulu untuk cetak kartu per kelas.')"
                                class="inline-flex items-center px-4 py-2.5 bg-gray-200 text-gray-400 rounded-xl font-semibold text-xs uppercase tracking-wider cursor-not-allowed gap-2"
                                title="Pilih kelas terlebih dahulu">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Per Kelas
                            </button>
                        @endif

                        {{-- Export Masal ZIP --}}
                        <a href="{{ route('operator.kartu-pelajar.export-zip', ['rombel_id' => request('rombel_id')]) }}"
                            class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-xl font-semibold text-xs uppercase tracking-wider shadow-sm transition gap-2"
                            title="Download file ZIP berisi kartu pelajar JPG format: nama kelas_nama siswa_nis.jpg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export ZIP (.jpg)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm">
                        Daftar Siswa {{ $selectedRombel ? '- Kelas ' . ($selectedRombel->kelas->nama_kelas ?? '') : '' }}
                    </h3>
                    <span class="text-xs text-gray-500">Menampilkan {{ $siswa->total() }} siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3.5 font-bold">Foto</th>
                                <th class="px-6 py-3.5 font-bold">NIS</th>
                                <th class="px-6 py-3.5 font-bold">Nama Lengkap & TTL</th>
                                <th class="px-6 py-3.5 font-bold">L/P</th>
                                <th class="px-6 py-3.5 font-bold">Kelas</th>
                                <th class="px-6 py-3.5 font-bold">Status Foto</th>
                                <th class="px-6 py-3.5 font-bold text-right">Aksi Kartu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($siswa as $item)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="w-10 h-12 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                            @if ($item->foto_url)
                                                <img src="{{ $item->foto_url }}" alt="{{ $item->nama_lengkap }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap font-mono font-bold text-gray-900">
                                        {{ $item->nis }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $item->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->tempat_lahir ? $item->tempat_lahir . ', ' : '' }}{{ $item->tanggal_lahir ? $item->tanggal_lahir->translatedFormat('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded text-xs font-bold {{ $item->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $item->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap font-medium text-gray-800">
                                        {{ $item->rombels->first()?->kelas?->nama_kelas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @if ($item->foto)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Ada Foto
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Tombol Preview Kartu --}}
                                            <button type="button" @click="loadPreview({{ $item->id }})"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition gap-1"
                                                title="Preview Kartu Pelajar">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Preview
                                            </button>

                                            {{-- Download JPG Satuan --}}
                                            <a href="{{ route('operator.kartu-pelajar.download-jpg', $item->id) }}"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold border border-indigo-200 transition gap-1"
                                                title="Download File JPG">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                JPG
                                            </a>

                                            {{-- Cetak Satuan --}}
                                            <a href="{{ route('operator.kartu-pelajar.cetak', $item->id) }}" target="_blank"
                                                class="inline-flex items-center px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-semibold border border-red-200 transition gap-1"
                                                title="Cetak Kartu">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                                Cetak
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <p class="text-base font-medium">Data siswa tidak ditemukan.</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter kelas atau kata kunci pencarian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $siswa->links() }}
                </div>
            </div>
        </div>

        {{-- Modal Upload Masal Foto --}}
        <div x-show="isUploadModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isUploadModalOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="isUploadModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isUploadModalOpen"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <div class="bg-emerald-600 px-5 py-4 flex justify-between items-center text-white">
                        <h3 class="text-base font-bold">Upload Masal Foto Siswa</h3>
                        <button @click="isUploadModalOpen = false" class="text-emerald-100 hover:text-white focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('operator.kartu-pelajar.upload-foto-masal') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 text-xs text-amber-800 space-y-1.5">
                                <p class="font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    Aturan Penamaan File Foto:
                                </p>
                                <p>Nama file foto harus menggunakan <strong>Nomor Induk Siswa (NIS)</strong> agar sistem dapat memetakan foto secara otomatis.</p>
                                <p class="font-mono bg-white p-1.5 rounded border border-amber-200 text-[11px]">Contoh: 12345.jpg, 553241100.png</p>
                            </div>

                            {{-- Pilihan Metode Upload --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pilih File ZIP atau Multiple Foto</label>
                                
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-xs text-gray-500 font-medium">Opsi A: Upload File .ZIP (Direkomendasikan jika banyak)</span>
                                        <input type="file" name="file_zip" accept=".zip"
                                            class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="h-px bg-gray-200 flex-1"></div>
                                        <span class="text-[11px] text-gray-400 uppercase font-semibold">atau</span>
                                        <div class="h-px bg-gray-200 flex-1"></div>
                                    </div>

                                    <div>
                                        <span class="text-xs text-gray-500 font-medium">Opsi B: Upload Langsung Banyak File Foto (.jpg / .png)</span>
                                        <input type="file" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp"
                                            class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-gray-100">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                                Upload & Proses
                            </button>
                            <button type="button" @click="isUploadModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Preview Kartu Pelajar --}}
        <div x-show="isPreviewModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isPreviewModalOpen" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closePreviewModal()"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isPreviewModalOpen"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100 p-6">
                    
                    <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-base">Preview Kartu Tanda Pelajar</h3>
                        <button @click="closePreviewModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div id="preview-modal-content">
                        <div class="py-12 flex flex-col items-center justify-center text-gray-400">
                            <svg class="animate-spin h-8 w-8 text-red-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-xs">Memuat kartu pelajar...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function kartuPelajarManager() {
                return {
                    isUploadModalOpen: false,
                    isPreviewModalOpen: false,

                    openUploadModal() {
                        this.isUploadModalOpen = true;
                    },

                    loadPreview(siswaId) {
                        this.isPreviewModalOpen = true;
                        const container = document.getElementById('preview-modal-content');
                        container.innerHTML = `
                            <div class="py-12 flex flex-col items-center justify-center text-gray-400">
                                <svg class="animate-spin h-8 w-8 text-red-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-xs">Memuat kartu pelajar...</p>
                            </div>
                        `;

                        fetch(`/operator/kartu-pelajar/preview/${siswaId}`)
                            .then(response => response.text())
                            .then(html => {
                                container.innerHTML = html;
                            })
                            .catch(err => {
                                container.innerHTML = `<div class="p-6 text-center text-red-600 text-sm">Gagal memuat kartu pelajar. Silakan coba lagi.</div>`;
                            });
                    },

                    closePreviewModal() {
                        this.isPreviewModalOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
