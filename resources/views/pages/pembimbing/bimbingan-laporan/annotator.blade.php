<x-app-layout>
    <div class="mb-6 bg-white p-4 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.detail', $laporan) }}"
                class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Kembali ke Lembar Bimbingan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Studio Koreksi &amp; Coretan: {{ $tahap->nama_tahap }}</h2>
                <p class="text-xs text-gray-500">Siswa: {{ $laporan->penempatan?->siswa?->nama_lengkap }} • {{ $laporan->judul }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            {{-- Tombol Buka Asisten Stella AI --}}
            <button type="button" onclick="openStellaAiModal()"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-rose-600 hover:from-indigo-700 hover:via-purple-700 hover:to-rose-700 shadow-md shadow-indigo-100 transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 text-amber-200" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>Stella AI Koreksi</span>
            </button>

            @if ((isset($beritaAcaras) && $beritaAcaras->isNotEmpty()) || $tahap->nomor_berita_acara)
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl {{ $tahap->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }} transition">
                    <svg class="w-4 h-4 {{ $tahap->status === 'disetujui' ? 'text-emerald-600' : 'text-rose-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                    {{ $tahap->status === 'disetujui' ? 'Berita Acara ACC (PDF)' : 'Berita Acara Terakhir (PDF)' }}
                </a>
            @endif
            <a href="{{ $tahap->file_url }}" target="_blank" download
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh File Asli
            </a>
        </div>
    </div>

    {{-- Switcher Riwayat Versi Dokumen Bimbingan --}}
    @if(isset($beritaAcaras) && $beritaAcaras->isNotEmpty())
        <div class="mb-5 bg-white p-3.5 rounded-2xl border border-gray-200 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-xs flex-shrink-0 border border-indigo-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h3 class="text-xs font-bold text-gray-900">Riwayat Versi &amp; Arsip Dokumen Bimbingan</h3>
                    <p class="text-[11px] text-gray-500">Pilih berkas bimbingan aktif atau arsip revisi terdahulu lengkap dengan coretannya</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5 flex-wrap">
                {{-- Pilihan Dokumen Saat Ini / Aktif --}}
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap) }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ ! $isArsipMode ? 'bg-rose-600 text-white shadow-sm ring-2 ring-rose-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                    <span>Dokumen Saat Ini (Terbaru)</span>
                    @if($tahap->status === 'disetujui')
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-emerald-500 text-white">ACC</span>
                    @elseif($tahap->status === 'diajukan')
                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-400 text-gray-900">Perlu Koreksi</span>
                    @endif
                </a>

                {{-- Pilihan Arsip Berita Acara Terdahulu --}}
                @foreach($beritaAcaras as $ba)
                    @php $isCurrentBa = $isArsipMode && ($selectedBeritaAcara?->id === $ba->id); @endphp
                    <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', [$tahap, 'berita_acara_id' => $ba->id]) }}"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 {{ $isCurrentBa ? 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-300 font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span>
                            {{ $ba->jenis === 'disetujui' ? 'Arsip ACC' : 'Arsip Revisi #' . ($ba->revisi_ke ?? 1) }}
                            <span class="opacity-75 font-normal text-[10px]">({{ $ba->diterbitkan_at?->format('d/m/y') }})</span>
                        </span>
                        <span class="px-1 py-0.2 rounded text-[9px] font-bold {{ $isCurrentBa ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">
                            {{ $ba->anotasis()->count() }} Coretan
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Banner Mode Tinjauan Arsip --}}
    @if($isArsipMode && $selectedBeritaAcara)
        <div class="mb-5 p-4 rounded-2xl border-2 border-amber-300 bg-amber-50 text-amber-900 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold flex-shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-200 text-amber-900">Mode Tinjauan Arsip</span>
                        <h4 class="font-extrabold text-sm text-gray-900">{{ $selectedBeritaAcara->judul_dokumen }}</h4>
                    </div>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Anda sedang melihat dokumen PDF dan riwayat <strong>{{ $anotasis->count() }} coretan revisi</strong> pada saat Berita Acara ini diterbitkan ({{ $selectedBeritaAcara->diterbitkan_at?->translatedFormat('d F Y H:i') }} WIB). Dokumen ini terkunci sebagai arsip bimbingan otentik.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara.item', $selectedBeritaAcara) }}" target="_blank"
                    class="px-3 py-2 rounded-xl text-xs font-bold bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh BA (PDF)</span>
                </a>
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap) }}"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-amber-700 hover:bg-amber-800 transition shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Dokumen Terbaru</span>
                </a>
            </div>
        </div>
    @endif

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Studio Workspace: Toolbar & Canvas --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Interactive Annotation Toolbar / Readonly Header (Sticky saat PDF di-scroll) --}}
                @if(!($canAnnotate ?? true))
                    <div class="sticky top-2 z-30 {{ $isArsipMode ? 'bg-amber-50/95 border-amber-300' : 'bg-emerald-50/95 border-emerald-300' }} backdrop-blur-md border rounded-2xl p-3 shadow-md flex flex-wrap items-center justify-between gap-3 transition">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl {{ $isArsipMode ? 'bg-amber-600' : 'bg-emerald-600' }} text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                @if($isArsipMode)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </span>
                            <div>
                                <h4 class="text-xs font-bold {{ $isArsipMode ? 'text-amber-950' : 'text-emerald-950' }}">
                                    {{ $isArsipMode ? ($selectedBeritaAcara?->judul_dokumen ?? 'Mode Tinjauan Arsip') : 'Tahap Telah Disahkan (ACC)' }}
                                </h4>
                                <p class="text-[11px] {{ $isArsipMode ? 'text-amber-800' : 'text-emerald-700' }}">
                                    {{ $isArsipMode ? 'Menampilkan riwayat coretan dan berkas saat berita acara diterbitkan. Dokumen terkunci.' : 'Mode tinjauan arsip dokumen. Bab ini terkunci dan tidak dapat direvisi lagi.' }}
                                </p>
                            </div>
                        </div>
                                <p class="text-[11px] text-emerald-700">Mode tinjauan arsip dokumen. Bab ini terkunci dan tidak dapat direvisi lagi.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- Page Navigation & Quick Jump --}}
                            <div class="flex items-center gap-1.5 bg-white border border-emerald-200 rounded-xl px-2 py-1 shadow-xs">
                                <button type="button" id="prev-page-btn"
                                    class="p-1.5 rounded-lg hover:bg-emerald-50 text-gray-600 hover:text-emerald-900 transition disabled:opacity-40 disabled:cursor-not-allowed" title="Halaman Sebelumnya">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <div class="flex items-center gap-1 text-xs font-medium text-gray-600">
                                    <span>Hal.</span>
                                    <input type="number" id="page-input" min="1" max="1" value="1"
                                        class="w-12 text-center text-xs font-bold bg-gray-50 border border-gray-300 rounded-lg py-1 px-1 text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner"
                                        title="Ketik nomor halaman lalu tekan Enter untuk berpindah cepat">
                                    <span>/ <span id="total-pages-num" class="font-bold text-gray-800">-</span></span>
                                </div>
                                <button type="button" id="next-page-btn"
                                    class="p-1.5 rounded-lg hover:bg-emerald-50 text-gray-600 hover:text-emerald-900 transition disabled:opacity-40 disabled:cursor-not-allowed" title="Halaman Selanjutnya">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>

                            {{-- Zoom Controls --}}
                            <div class="flex items-center gap-1 bg-white border border-emerald-200 rounded-xl px-2 py-1 shadow-xs">
                                <button type="button" id="zoom-out-btn" class="p-1.5 rounded-lg hover:bg-emerald-50 text-gray-600 hover:text-emerald-900 transition" title="Perkecil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <span id="zoom-level-text" class="text-xs font-bold text-gray-700 min-w-[45px] text-center">100%</span>
                                <button type="button" id="zoom-in-btn" class="p-1.5 rounded-lg hover:bg-emerald-50 text-gray-600 hover:text-emerald-900 transition" title="Perbesar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="sticky top-2 z-30 bg-white/95 backdrop-blur-md rounded-2xl border border-gray-200/90 p-3 shadow-md flex flex-wrap items-center justify-between gap-3 transition">
                        {{-- Tool Selection --}}
                        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                            <button type="button" class="tool-btn px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 active-tool bg-white text-gray-900 shadow-sm"
                                data-tool="box" title="Kotak Penanda">
                                <span class="w-3.5 h-3.5 border-2 border-current rounded-sm"></span>
                                <span class="hidden sm:inline">Kotak</span>
                            </button>
                            <button type="button" class="tool-btn px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900"
                                data-tool="circle" title="Lingkaran Penanda">
                                <span class="w-3.5 h-3.5 border-2 border-current rounded-full"></span>
                                <span class="hidden sm:inline">Lingkaran</span>
                            </button>
                            <button type="button" class="tool-btn px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900"
                                data-tool="drawing" title="Coretan Bebas">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span class="hidden sm:inline">Coretan</span>
                            </button>
                            <button type="button" class="tool-btn px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 text-gray-600 hover:text-gray-900"
                                data-tool="pin" title="Pin Titik">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="hidden sm:inline">Pin</span>
                            </button>
                        </div>

                        {{-- Color Palette --}}
                        <div class="flex items-center gap-1.5">
                            <button type="button" class="color-btn w-6 h-6 rounded-full border-2 border-white shadow-sm ring-2 ring-red-500 bg-red-500 transition" data-color="#ef4444" title="Merah"></button>
                            <button type="button" class="color-btn w-6 h-6 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-300 bg-blue-500 transition" data-color="#3b82f6" title="Biru"></button>
                            <button type="button" class="color-btn w-6 h-6 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-300 bg-amber-500 transition" data-color="#f59e0b" title="Kuning"></button>
                            <button type="button" class="color-btn w-6 h-6 rounded-full border-2 border-white shadow-sm ring-1 ring-gray-300 bg-emerald-500 transition" data-color="#10b981" title="Hijau"></button>
                        </div>

                        {{-- Page Navigation & Quick Jump --}}
                        <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200 rounded-xl px-2 py-1">
                            <button type="button" id="prev-page-btn"
                                class="p-1.5 rounded-lg hover:bg-white text-gray-600 hover:text-gray-900 transition disabled:opacity-40 disabled:cursor-not-allowed" title="Halaman Sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="flex items-center gap-1 text-xs font-medium text-gray-600">
                                <span>Hal.</span>
                                <input type="number" id="page-input" min="1" max="1" value="1"
                                    class="w-12 text-center text-xs font-bold bg-white border border-gray-300 rounded-lg py-1 px-1 text-gray-800 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 shadow-inner"
                                    title="Ketik nomor halaman lalu tekan Enter untuk berpindah cepat">
                                <span>/ <span id="total-pages-num" class="font-bold text-gray-800">-</span></span>
                            </div>
                            <button type="button" id="next-page-btn"
                                class="p-1.5 rounded-lg hover:bg-white text-gray-600 hover:text-gray-900 transition disabled:opacity-40 disabled:cursor-not-allowed" title="Halaman Selanjutnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        {{-- Zoom Controls --}}
                        <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-xl px-2 py-1">
                            <button type="button" id="zoom-out-btn" class="p-1.5 rounded-lg hover:bg-white text-gray-600 hover:text-gray-900 transition" title="Perkecil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span id="zoom-level-text" class="text-xs font-bold text-gray-700 min-w-[45px] text-center">100%</span>
                            <button type="button" id="zoom-in-btn" class="p-1.5 rounded-lg hover:bg-white text-gray-600 hover:text-gray-900 transition" title="Perbesar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Drawing Instruction Banner --}}
                    <div class="p-3 bg-rose-50/80 border border-rose-200 rounded-xl text-xs text-rose-900 flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><strong>Petunjuk:</strong> Klik dan seret (drag) mouse di atas dokumen PDF untuk menandai area koreksi (kotak/lingkaran/coretan). Jendela catatan revisi akan muncul otomatis saat Anda melepaskan mouse.</span>
                    </div>
                @endif

                {{-- PDF & Drawing Canvas Container --}}
                <div id="pdf-viewer-container" class="bg-gray-200/70 rounded-2xl p-4 md:p-8 min-h-[600px] overflow-auto flex flex-col items-center gap-8 shadow-inner border border-gray-200 select-none">
                    <div id="pdf-loading-indicator" class="flex flex-col items-center justify-center p-12 text-gray-500">
                        <svg class="w-10 h-10 animate-spin text-rose-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-xs font-bold">Memuat Dokumen PDF Studio...</span>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar: Annotations List & Final Review Form (Scrollable Independen) --}}
            <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-2 lg:max-h-[calc(100vh-1rem)] lg:overflow-y-auto pr-1 pb-6 custom-scrollbar">
                {{-- Form Keputusan / Kartu Status Arsip / Disetujui (ACC) --}}
                @if($isArsipMode && $selectedBeritaAcara)
                    <div class="bg-white rounded-2xl border-2 border-amber-300 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <div class="p-2 bg-amber-100 text-amber-800 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-sm">{{ $selectedBeritaAcara->judul_dokumen }}</h4>
                                <p class="text-[11px] text-amber-700 font-semibold">Arsip resmi bimbingan terdahulu</p>
                            </div>
                        </div>

                        <div class="space-y-2.5 text-xs bg-amber-50/70 rounded-xl p-3.5 border border-amber-200/70">
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 font-medium">Nomor Berita Acara:</span>
                                <span class="font-mono font-bold text-gray-900">{{ $selectedBeritaAcara->nomor_berita_acara }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 font-medium">Diterbitkan Pada:</span>
                                <span class="font-semibold text-gray-800">{{ $selectedBeritaAcara->diterbitkan_at?->translatedFormat('d F Y H:i') }} WIB</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px]">
                                <span class="text-gray-500 font-medium">Total Coretan Revisi:</span>
                                <span class="font-bold text-rose-700">{{ $anotasis->count() }} Coretan</span>
                            </div>
                            @if($selectedBeritaAcara->catatan_pembimbing)
                                <div class="pt-2 border-t border-amber-200">
                                    <span class="text-[10px] font-bold text-amber-900 uppercase tracking-wider block">Arahan Pembimbing:</span>
                                    <p class="text-gray-700 italic mt-0.5 leading-relaxed">{{ $selectedBeritaAcara->catatan_pembimbing }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-2 pt-1">
                            <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara.item', $selectedBeritaAcara) }}" target="_blank"
                                class="w-full py-2.5 px-4 rounded-xl text-white font-bold text-xs shadow-md bg-amber-600 hover:bg-amber-700 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh PDF Berita Acara Arsip Ini</span>
                            </a>
                            <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap) }}"
                                class="w-full py-2 px-4 rounded-xl text-gray-700 font-bold text-xs border border-gray-300 hover:bg-gray-100 transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Kembali ke Dokumen Aktif</span>
                            </a>
                        </div>
                    </div>
                @elseif($tahap->status === 'disetujui')
                    <div class="bg-white rounded-2xl border-2 border-emerald-200 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                            <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-sm">Tahap Ini Telah Disetujui (ACC)</h4>
                                <p class="text-[11px] text-emerald-600 font-semibold">Status terkunci permanen. Bab ini tidak dapat direvisi lagi.</p>
                            </div>
                        </div>

                        <div class="space-y-2.5 text-xs bg-emerald-50/60 rounded-xl p-3.5 border border-emerald-100">
                            @if($tahap->nomor_berita_acara || ($tahap->latestBeritaAcara && $tahap->latestBeritaAcara->nomor_berita_acara))
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-gray-500 font-medium">Nomor Berita Acara:</span>
                                    <span class="font-mono font-bold text-gray-900">{{ $tahap->latestBeritaAcara?->nomor_berita_acara ?? $tahap->nomor_berita_acara }}</span>
                                </div>
                            @endif
                            @if($tahap->reviewed_at)
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-gray-500 font-medium">Disahkan Pada:</span>
                                    <span class="font-semibold text-gray-800">{{ $tahap->reviewed_at->translatedFormat('d F Y H:i') }} WIB</span>
                                </div>
                            @endif
                            @if($tahap->catatan_pembimbing)
                                <div class="pt-2 border-t border-emerald-200/60">
                                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Catatan Pengesahan:</span>
                                    <p class="text-gray-700 italic mt-0.5 leading-relaxed">{{ $tahap->catatan_pembimbing }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="pt-1">
                            <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                                class="w-full py-2.5 px-4 rounded-xl text-white font-bold text-xs shadow-md bg-emerald-600 hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh Berita Acara Pengesahan (PDF)</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                            <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Keputusan &amp; Berita Acara</h4>
                                <p class="text-[11px] text-gray-500">Terbitkan Berita Acara resmi (Revisi atau ACC)</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.selesaikan-review', $tahap) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Tindakan Keputusan <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="border-2 rounded-xl p-3 flex flex-col gap-1 cursor-pointer transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="status" value="revisi" id="radio-status-revisi"
                                                {{ ($tahap->status === 'perlu_revisi' || $tahap->status === 'revisi' || $tahap->status !== 'disetujui') ? 'checked' : '' }} required
                                                class="text-rose-600 focus:ring-rose-500" onchange="updateDecisionButtonState()">
                                            <span class="text-xs font-bold text-gray-800">Perlu Revisi</span>
                                        </div>
                                        <span class="text-[10px] text-gray-500 pl-5">Terbitkan Berita Acara Revisi</span>
                                    </label>
                                    <label class="border-2 rounded-xl p-3 flex flex-col gap-1 cursor-pointer transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="status" value="disetujui" id="radio-status-disetujui"
                                                {{ $tahap->status === 'disetujui' ? 'checked' : '' }} required
                                                class="text-emerald-600 focus:ring-emerald-500" onchange="updateDecisionButtonState()">
                                            <span class="text-xs font-bold text-gray-800">Setujui (ACC)</span>
                                        </div>
                                        <span class="text-[10px] text-gray-500 pl-5">Terbitkan Berita Acara ACC</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    Catatan &amp; Arahan Pembimbing (Opsional)
                                </label>
                                <textarea name="catatan_pembimbing" rows="3" maxlength="1000"
                                    placeholder="Tuliskan rangkuman poin arahan revisi atau apresiasi pengesahan..."
                                    class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">{{ old('catatan_pembimbing', $tahap->catatan_pembimbing) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                    PIN Tanda Tangan Digital <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" name="pin" required maxlength="10" placeholder="6 digit PIN TTD Digital"
                                    class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                                <p class="text-[10px] text-gray-400 mt-1">Dibutuhkan untuk membubuhkan QR tanda tangan digital pada Berita Acara resmi.</p>
                            </div>

                            <button type="submit" id="btn-submit-review"
                                class="w-full py-2.5 px-4 rounded-xl text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                <span id="btn-submit-text">Terbitkan Berita Acara Revisi</span>
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Riwayat Berita Acara yang Pernah Diterbitkan --}}
                @if(isset($beritaAcaras) && $beritaAcaras->isNotEmpty())
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Riwayat Berita Acara ({{ $beritaAcaras->count() }})
                            </h4>
                        </div>
                        <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                            @foreach($beritaAcaras as $ba)
                                <div class="p-2.5 rounded-xl border border-gray-200 bg-gray-50/70 hover:bg-white transition flex items-center justify-between gap-2 text-xs">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($ba->jenis === 'disetujui')
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                                    ACC / DISETUJUI
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                                    REVISI {{ $ba->revisi_ke ? '#' . $ba->revisi_ke : '' }}
                                                </span>
                                            @endif
                                            <span class="text-[10px] text-gray-400">
                                                {{ $ba->diterbitkan_at?->format('d/m/Y H:i') ?? $ba->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] font-semibold text-gray-700 truncate mt-0.5">
                                            {{ $ba->nomor_berita_acara }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', [$tahap, 'berita_acara_id' => $ba->id]) }}"
                                            class="p-1.5 rounded-lg bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-700 transition shadow-xs"
                                            title="Buka Dokumen & Coretan Revisi Ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara.item', $ba) }}" target="_blank"
                                            class="p-1.5 rounded-lg bg-white border border-gray-300 hover:border-emerald-500 hover:text-emerald-700 text-gray-600 transition shadow-sm"
                                            title="Unduh PDF Berita Acara ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Daftar Catatan & Navigasi Coretan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h4 class="font-bold text-gray-900 text-sm">Daftar Coretan & Revisi</h4>
                        <span id="annotation-count-badge" class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                            {{ $anotasis->count() }} Coretan
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-500">
                        Klik salah satu coretan di bawah untuk langsung menuju koordinat coretan pada dokumen.
                    </p>

                    <div id="annotations-list-container" class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1">
                        @forelse ($anotasis as $index => $anotasi)
                            <div class="annotation-nav-item group p-3 rounded-xl border border-gray-200 hover:border-rose-400 hover:bg-rose-50/50 cursor-pointer transition shadow-sm relative"
                                id="list-item-{{ $anotasi->id }}"
                                onclick="scrollToAnnotation({{ $anotasi->id }}, {{ $anotasi->halaman }})">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 group-hover:bg-rose-200 group-hover:text-rose-800">
                                        #{{ $index + 1 }} • Hal. {{ $anotasi->halaman }}
                                    </span>
                                    @if($tahap->status !== 'disetujui')
                                        <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.hapus-anotasi', $anotasi) }}"
                                            onsubmit="return confirm('Hapus coretan/catatan ini?')" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-rose-600 p-0.5" title="Hapus Coretan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-800 font-medium leading-snug">
                                    {{ $anotasi->catatan ?: '(Coretan penanda tanpa catatan teks)' }}
                                </p>
                            </div>
                        @empty
                            <div id="no-annotations-notice" class="py-8 text-center text-gray-400 text-xs">
                                Belum ada coretan. Tarik mouse di atas PDF untuk membuat penanda koreksi baru.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Popover: Input Catatan Coretan Baru --}}
    <div id="note-modal" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-gray-100">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-rose-50 text-rose-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </div>
                    <h4 class="font-bold text-gray-900 text-sm">Catatan Koreksi &amp; Arahan Revisi</h4>
                </div>
                <button type="button" onclick="cancelPendingAnnotation()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Uraian Koreksi untuk Area Ini <span class="text-rose-500">*</span>
                </label>
                <textarea id="pending-note-input" rows="5" maxlength="1500"
                    placeholder="Tuliskan arahan perbaikan secara jelas...&#10;Contoh:&#10;1. Perbaiki penulisan rumus pada bab ini&#10;2. Lengkapi referensi sumber tabel"
                    class="w-full text-xs font-normal leading-relaxed rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition placeholder:text-gray-400"></textarea>
                <div class="flex items-center justify-between text-[11px] text-gray-400 px-0.5">
                    <span>💡 Gunakan Enter untuk membuat baris baru / poin nomor</span>
                    <span id="note-char-count">Maks. 1500 karakter</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                <button type="button" onclick="cancelPendingAnnotation()"
                    class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    Batal
                </button>
                <button type="button" onclick="savePendingAnnotation()"
                    class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-200 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Catatan Koreksi</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Popover: Asisten Koreksi Stella AI --}}
    <div id="stella-ai-modal" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-indigo-100 overflow-hidden flex flex-col max-h-[90vh]">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-rose-600 p-5 text-white flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-amber-200 shadow-inner">
                        <svg class="w-6 h-6 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-base tracking-tight">Stella AI • Asisten Koreksi Laporan</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-white/25 text-white">AI Assistant</span>
                        </div>
                        <p class="text-xs text-indigo-100 mt-0.5">Analisis cerdas struktur bab, tata bahasa EYD, dan rekomendasi putusan berita acara</p>
                    </div>
                </div>
                <button type="button" onclick="closeStellaAiModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 overflow-y-auto space-y-5 custom-scrollbar flex-1">
                {{-- Form State --}}
                <div id="ai-form-state" class="space-y-5">
                    <div class="p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-900 leading-relaxed">
                        <strong class="font-bold">Cara Kerja:</strong> Stella AI membaca teks dokumen laporan siswa pada bab ini secara otomatis, menelaah kesesuaian materi, mendeteksi kesalahan EYD/ejaan, dan menyusun draf arahan pembimbing yang siap Anda gunakan.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Pilih Lingkup Analisis Koreksi:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 hover:bg-gray-50 border-gray-200">
                                <input type="radio" name="ai-scope" value="halaman" checked class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="font-bold text-xs text-gray-900 block">Halaman Aktif (<span id="ai-active-page-label">Hal. 1</span>)</span>
                                    <span class="text-[11px] text-gray-500 mt-0.5 block">Koreksi cepat teks pada halaman yang sedang Anda buka di layar.</span>
                                </div>
                            </label>
                            <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex items-start gap-3 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 hover:bg-gray-50 border-gray-200">
                                <input type="radio" name="ai-scope" value="seluruh" class="mt-1 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <span class="font-bold text-xs text-gray-900 block">Seluruh Bab Dokumen</span>
                                    <span class="text-[11px] text-gray-500 mt-0.5 block">Analisis menyeluruh isi bab dari halaman awal sampai akhir bab ini.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" onclick="closeStellaAiModal()" class="px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Tutup
                        </button>
                        <button type="button" onclick="runStellaAiCorrection()"
                            class="px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 rounded-xl shadow-md shadow-indigo-200 transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>Mulai Analisis Dokumen</span>
                        </button>
                    </div>
                </div>

                {{-- Loading State --}}
                <div id="ai-loading-state" class="hidden py-12 flex flex-col items-center justify-center text-center space-y-4">
                    <div class="relative">
                        <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-500 to-pink-500 animate-spin flex items-center justify-center p-1">
                            <div class="w-full h-full bg-white rounded-2xl"></div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Stella AI Sedang Memeriksa Dokumen...</h4>
                        <p class="text-xs text-gray-500 mt-1 max-w-sm">Mengekstrak teks bab, menganalisis struktur tulisan, ejaan EYD, dan menyusun evaluasi bimbingan.</p>
                    </div>
                </div>

                {{-- Error State --}}
                <div id="ai-error-state" class="hidden p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h4 class="font-bold text-xs">Pemeriksaan AI Belum Dapat Diproses</h4>
                            <p id="ai-error-message" class="text-xs mt-1 text-rose-700"></p>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" onclick="resetStellaAiModal()" class="px-4 py-1.5 text-xs font-bold text-rose-700 bg-white border border-rose-300 rounded-xl hover:bg-rose-100/50 transition">
                            Coba Kembali
                        </button>
                    </div>
                </div>

                {{-- Result State --}}
                <div id="ai-result-state" class="hidden space-y-4">
                    {{-- Summary & Recommendation Cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Skor Kelayakan</span>
                                <span id="ai-res-score" class="text-2xl font-black text-gray-900">-</span>
                                <span class="text-[10px] text-gray-500 block mt-0.5">Skor evaluasi materi bab</span>
                            </div>
                            <div id="ai-res-score-badge" class="px-3 py-1.5 rounded-xl font-bold text-xs bg-emerald-100 text-emerald-800">
                                Sangat Baik
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-gray-200 flex flex-col justify-between gap-2" id="ai-res-decision-card">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Rekomendasi Putusan</span>
                                <h4 id="ai-res-decision-text" class="text-sm font-black text-gray-900 mt-0.5">-</h4>
                            </div>
                            <button type="button" id="btn-apply-decision" onclick="applyAiDecision()"
                                class="w-full py-1.5 px-3 rounded-lg text-xs font-bold text-white transition flex items-center justify-center gap-1.5 shadow-xs bg-emerald-600 hover:bg-emerald-700">
                                <span>Terapkan ke Form Keputusan</span>
                            </button>
                        </div>
                    </div>

                    {{-- Ringkasan Evaluasi --}}
                    <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-1.5">
                        <span class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider block">Ringkasan Telaah Stella AI:</span>
                        <p id="ai-res-summary" class="text-xs text-gray-700 leading-relaxed"></p>
                    </div>

                    {{-- Catatan Arahan Siap Pakai --}}
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-700 uppercase tracking-wider">Draf Catatan Arahan Pembimbing:</span>
                            <button type="button" onclick="copyAiNoteToForm()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-indigo-700 bg-indigo-100/70 hover:bg-indigo-200 rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Salin ke Form Catatan</span>
                            </button>
                        </div>
                        <p id="ai-res-note" class="text-xs text-gray-700 bg-white p-3 rounded-xl border border-gray-200 italic leading-relaxed"></p>
                    </div>

                    {{-- Poin-poin Koreksi Spesifik --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-gray-700 uppercase tracking-wider">Poin Temuan &amp; Arahan Revisi:</span>
                            <span id="ai-findings-count" class="text-[11px] text-gray-400 font-semibold">0 Temuan</span>
                        </div>
                        <div id="ai-findings-container" class="space-y-2 max-h-[220px] overflow-y-auto pr-1"></div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <button type="button" onclick="resetStellaAiModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            &larr; Analisis Bagian Lain
                        </button>
                        <button type="button" onclick="closeStellaAiModal()" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md transition">
                            Selesai &amp; Kembali ke Dokumen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF.js & Drawing Studio Engine --}}
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.6);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .page-wrapper {
            position: relative;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: visible !important;
        }
        .canvas-drawing-overlay {
            position: absolute;
            top: 0;
            left: 0;
            cursor: crosshair;
            z-index: 10;
        }
        .annotation-layer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 25;
            overflow: visible !important;
        }
        .annotation-marker {
            position: absolute;
            pointer-events: auto;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .annotation-marker:hover {
            transform: scale(1.02);
            z-index: 45;
        }
        .annotation-tooltip {
            position: absolute;
            bottom: calc(100% + 10px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            padding: 10px 14px;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
            z-index: 70;
            width: max-content;
            min-width: 220px;
            max-width: 360px;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.2);
            white-space: normal;
            backdrop-filter: blur(8px);
        }
        .annotation-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #0f172a transparent transparent transparent;
        }
        .annotation-tooltip.tooltip-bottom {
            bottom: auto;
            top: calc(100% + 10px);
        }
        .annotation-tooltip.tooltip-bottom::after {
            top: auto;
            bottom: 100%;
            border-color: transparent transparent #0f172a transparent;
        }
        .annotation-tooltip.tooltip-align-left {
            left: 0 !important;
            transform: none !important;
        }
        .annotation-tooltip.tooltip-align-left::after {
            left: 20px !important;
            transform: none !important;
        }
        .annotation-tooltip.tooltip-align-right {
            left: auto !important;
            right: 0 !important;
            transform: none !important;
        }
        .annotation-tooltip.tooltip-align-right::after {
            left: auto !important;
            right: 20px !important;
            transform: none !important;
        }
        .annotation-tooltip-body {
            max-height: 220px;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding-right: 4px;
            font-size: 11px;
            line-height: 1.55;
            color: #f1f5f9;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .annotation-tooltip-body::-webkit-scrollbar {
            width: 4px;
        }
        .annotation-tooltip-body::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        .annotation-marker:hover .annotation-tooltip,
        .annotation-marker.active-tooltip .annotation-tooltip {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto;
        }
        .pdf-annotation-highlight {
            animation: pulse-glow 1.5s infinite alternate;
            z-index: 50 !important;
        }
        @keyframes pulse-glow {
            from {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.8);
                filter: drop-shadow(0 0 4px rgba(239, 68, 68, 0.9));
            }
            to {
                box-shadow: 0 0 0 12px rgba(239, 68, 68, 0);
                filter: drop-shadow(0 0 14px rgba(239, 68, 68, 1));
            }
        }
    </style>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

        const pdfUrl = @js($activePdfUrl ?? $tahap->file_url);
        const tahapId = @js($tahap->id);
        const storeAnnotationUrl = "{{ route('pembimbing-prakerin.bimbingan-laporan.simpan-anotasi', $tahap) }}";
        const csrfToken = "{{ csrf_token() }}";
        const isReadOnly = @js(!($canAnnotate ?? true));
        let existingAnnotations = @js($anotasis);

        let pdfDoc = null;
        let scale = 1.15;
        let totalPages = 0;
        let currentTool = 'box'; // 'box', 'circle', 'drawing', 'pin'
        let currentColor = '#ef4444';

        let isDrawing = false;
        let startX = 0;
        let startY = 0;
        let currentActivePage = 1;
        let currentOverlayCanvas = null;
        let currentOverlayCtx = null;
        let freehandPoints = [];

        let pendingAnnotationData = null;

        // Tool selection listeners
        document.querySelectorAll('.tool-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tool-btn').forEach(b => {
                    b.classList.remove('active-tool', 'bg-white', 'text-gray-900', 'shadow-sm');
                    b.classList.add('text-gray-600');
                });
                btn.classList.add('active-tool', 'bg-white', 'text-gray-900', 'shadow-sm');
                btn.classList.remove('text-gray-600');
                currentTool = btn.dataset.tool;
            });
        });

        // Color selection listeners
        document.querySelectorAll('.color-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.color-btn').forEach(b => {
                    b.classList.remove('ring-2', 'ring-red-500');
                    b.classList.add('ring-1', 'ring-gray-300');
                });
                btn.classList.add('ring-2', 'ring-red-500');
                btn.classList.remove('ring-1', 'ring-gray-300');
                currentColor = btn.dataset.color;
            });
        });

        let currentPage = 1;
        let pageObserver = null;

        function updateNavigationState() {
            const inputEl = document.getElementById('page-input');
            if (inputEl && document.activeElement !== inputEl) {
                inputEl.value = currentPage;
            }
            const prevBtn = document.getElementById('prev-page-btn');
            const nextBtn = document.getElementById('next-page-btn');
            if (prevBtn) prevBtn.disabled = (currentPage <= 1);
            if (nextBtn) nextBtn.disabled = (currentPage >= totalPages);
        }

        function goToPage(target) {
            let p = parseInt(target, 10);
            if (isNaN(p) || p < 1) p = 1;
            if (totalPages && p > totalPages) p = totalPages;
            currentPage = p;

            updateNavigationState();

            const targetEl = document.getElementById(`page-wrapper-${currentPage}`);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function setupPageObserver() {
            if (pageObserver) {
                pageObserver.disconnect();
            }

            const options = {
                root: null,
                threshold: [0.1, 0.4, 0.7]
            };

            pageObserver = new IntersectionObserver((entries) => {
                let bestEntry = null;
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (!bestEntry || entry.intersectionRatio > bestEntry.intersectionRatio) {
                            bestEntry = entry;
                        }
                    }
                });

                if (bestEntry && bestEntry.target) {
                    const id = bestEntry.target.id;
                    const pageNum = parseInt(id.replace('page-wrapper-', ''), 10);
                    if (!isNaN(pageNum) && pageNum !== currentPage) {
                        currentPage = pageNum;
                        updateNavigationState();
                    }
                }
            }, options);

            for (let i = 1; i <= totalPages; i++) {
                const wrapper = document.getElementById(`page-wrapper-${i}`);
                if (wrapper) pageObserver.observe(wrapper);
            }
        }

        async function initStudioViewer() {
            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;
                document.getElementById('pdf-loading-indicator')?.remove();

                const totalPagesEl = document.getElementById('total-pages-num');
                if (totalPagesEl) totalPagesEl.textContent = totalPages;

                const pageInputEl = document.getElementById('page-input');
                if (pageInputEl) {
                    pageInputEl.max = totalPages;
                    pageInputEl.value = currentPage;
                }

                updateNavigationState();

                const container = document.getElementById('pdf-viewer-container');
                container.innerHTML = '';

                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    await renderStudioPage(pageNum, container);
                }

                setupPageObserver();
            } catch (err) {
                console.error("Gagal memuat PDF studio:", err);
            }
        }

        async function renderStudioPage(pageNum, container) {
            const page = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: scale });

            const pageWrapper = document.createElement('div');
            pageWrapper.className = 'page-wrapper';
            pageWrapper.id = `page-wrapper-${pageNum}`;
            pageWrapper.style.width = `${viewport.width}px`;
            pageWrapper.style.height = `${viewport.height}px`;

            // PDF Render Canvas
            const pdfCanvas = document.createElement('canvas');
            pdfCanvas.width = viewport.width;
            pdfCanvas.height = viewport.height;
            const pdfCtx = pdfCanvas.getContext('2d');
            await page.render({ canvasContext: pdfCtx, viewport: viewport }).promise;
            pageWrapper.appendChild(pdfCanvas);

            // Saved Annotation Layer
            const annotationLayer = document.createElement('div');
            annotationLayer.className = 'annotation-layer';
            annotationLayer.id = `annotation-layer-${pageNum}`;
            pageWrapper.appendChild(annotationLayer);

            // Drawing Interaction Overlay Canvas
            const drawCanvas = document.createElement('canvas');
            drawCanvas.className = 'canvas-drawing-overlay';
            drawCanvas.id = `draw-canvas-${pageNum}`;
            drawCanvas.width = viewport.width;
            drawCanvas.height = viewport.height;
            pageWrapper.appendChild(drawCanvas);

            setupDrawingEvents(drawCanvas, pageNum);

            container.appendChild(pageWrapper);

            renderPageAnnotations(pageNum, annotationLayer);
        }

        function setupDrawingEvents(canvas, pageNum) {
            if (isReadOnly) {
                canvas.style.pointerEvents = 'none';
                return;
            }

            const ctx = canvas.getContext('2d');

            canvas.addEventListener('mousedown', (e) => {
                isDrawing = true;
                currentActivePage = pageNum;
                currentOverlayCanvas = canvas;
                currentOverlayCtx = ctx;

                const rect = canvas.getBoundingClientRect();
                startX = e.clientX - rect.left;
                startY = e.clientY - rect.top;

                if (currentTool === 'drawing') {
                    freehandPoints = [{ x: startX / scale, y: startY / scale }];
                    ctx.beginPath();
                    ctx.moveTo(startX, startY);
                }
            });

            canvas.addEventListener('mousemove', (e) => {
                if (!isDrawing || currentActivePage !== pageNum) return;

                const rect = canvas.getBoundingClientRect();
                const currentX = e.clientX - rect.left;
                const currentY = e.clientY - rect.top;

                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.strokeStyle = currentColor;
                ctx.lineWidth = 2.5;

                if (currentTool === 'box') {
                    const width = currentX - startX;
                    const height = currentY - startY;
                    ctx.fillStyle = `${currentColor}25`;
                    ctx.fillRect(startX, startY, width, height);
                    ctx.strokeRect(startX, startY, width, height);
                } else if (currentTool === 'circle') {
                    const radiusX = Math.abs((currentX - startX) / 2);
                    const radiusY = Math.abs((currentY - startY) / 2);
                    const centerX = Math.min(startX, currentX) + radiusX;
                    const centerY = Math.min(startY, currentY) + radiusY;
                    ctx.beginPath();
                    ctx.ellipse(centerX, centerY, radiusX, radiusY, 0, 0, 2 * Math.PI);
                    ctx.fillStyle = `${currentColor}25`;
                    ctx.fill();
                    ctx.stroke();
                } else if (currentTool === 'drawing') {
                    freehandPoints.push({ x: currentX / scale, y: currentY / scale });
                    ctx.beginPath();
                    ctx.moveTo(freehandPoints[0].x * scale, freehandPoints[0].y * scale);
                    for (let i = 1; i < freehandPoints.length; i++) {
                        ctx.lineTo(freehandPoints[i].x * scale, freehandPoints[i].y * scale);
                    }
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', (e) => {
                if (!isDrawing || currentActivePage !== pageNum) return;
                isDrawing = false;

                const rect = canvas.getBoundingClientRect();
                const endX = e.clientX - rect.left;
                const endY = e.clientY - rect.top;

                let coordX = Math.min(startX, endX) / scale;
                let coordY = Math.min(startY, endY) / scale;
                let width = Math.abs(endX - startX) / scale;
                let height = Math.abs(endY - startY) / scale;

                if (currentTool === 'pin') {
                    coordX = startX / scale;
                    coordY = startY / scale;
                    width = 24 / scale;
                    height = 24 / scale;
                }

                // Require minimal dragging size for box/circle
                if ((currentTool === 'box' || currentTool === 'circle') && (width < 10 || height < 10)) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    return;
                }

                pendingAnnotationData = {
                    halaman: pageNum,
                    tipe: currentTool,
                    koordinat_x: Math.round(coordX),
                    koordinat_y: Math.round(coordY),
                    lebar: Math.round(width),
                    tinggi: Math.round(height),
                    warna: currentColor,
                    drawing_data: currentTool === 'drawing' ? freehandPoints : null
                };

                // Show modal note input
                document.getElementById('pending-note-input').value = '';
                document.getElementById('note-modal').style.display = 'flex';
                setTimeout(() => document.getElementById('pending-note-input').focus(), 100);
            });
        }

        function cancelPendingAnnotation() {
            document.getElementById('note-modal').style.display = 'none';
            if (currentOverlayCanvas && currentOverlayCtx) {
                currentOverlayCtx.clearRect(0, 0, currentOverlayCanvas.width, currentOverlayCanvas.height);
            }
            pendingAnnotationData = null;
        }

        async function savePendingAnnotation() {
            if (!pendingAnnotationData) return;

            const catatan = document.getElementById('pending-note-input').value.trim();
            pendingAnnotationData.catatan = catatan;

            try {
                const response = await fetch(storeAnnotationUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(pendingAnnotationData)
                });

                const res = await response.json();
                if (res.success && res.anotasi) {
                    existingAnnotations.push(res.anotasi);

                    // Re-render layer on this page
                    const layer = document.getElementById(`annotation-layer-${res.anotasi.halaman}`);
                    if (layer) {
                        renderPageAnnotations(res.anotasi.halaman, layer);
                    }

                    // Add to right sidebar list
                    addAnnotationToList(res.anotasi);

                    // Clear overlay canvas
                    if (currentOverlayCanvas && currentOverlayCtx) {
                        currentOverlayCtx.clearRect(0, 0, currentOverlayCanvas.width, currentOverlayCanvas.height);
                    }
                    document.getElementById('note-modal').style.display = 'none';
                }
            } catch (err) {
                console.error("Gagal menyimpan coretan:", err);
                alert("Terjadi kesalahan saat menyimpan catatan coretan.");
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatAnnotationText(text) {
            if (!text) return '';
            const escaped = escapeHtml(text);
            return escaped.replace(/\n/g, '<br>');
        }

        function renderPageAnnotations(pageNum, layer) {
            layer.innerHTML = '';
            const pageAnnotations = existingAnnotations.filter(a => Number(a.halaman) === Number(pageNum));

            pageAnnotations.forEach(a => {
                const el = document.createElement('div');
                el.className = 'annotation-marker';
                el.id = `annotation-marker-${a.id}`;

                // Fallback resilient untuk koordinat dan tipe
                const posX = Number(a.koordinat_x !== undefined ? a.koordinat_x : (a.posisi_x ?? 0));
                const posY = Number(a.koordinat_y !== undefined ? a.koordinat_y : (a.posisi_y ?? 0));
                const widthVal = Number(a.lebar ?? 80);
                const heightVal = Number(a.tinggi ?? 40);
                const color = a.warna || '#ef4444';

                const rawType = String(a.tipe || a.tipe_anotasi || 'box').toLowerCase();
                const type = (rawType === 'sorot_kotak' || rawType === 'box') ? 'box'
                           : ((rawType === 'sorot_lingkaran' || rawType === 'circle') ? 'circle'
                           : ((rawType === 'coretan_bebas' || rawType === 'drawing') ? 'drawing'
                           : ((rawType === 'pin_catatan' || rawType === 'pin') ? 'pin' : 'box')));

                const left = posX * scale;
                const top = posY * scale;
                const width = widthVal * scale;
                const height = heightVal * scale;

                const globalIndex = existingAnnotations.findIndex(x => x.id === a.id);
                const badgeNum = globalIndex !== -1 ? (globalIndex + 1) : '';

                if (type === 'drawing') {
                    el.style.left = '0px';
                    el.style.top = '0px';
                    el.style.width = '100%';
                    el.style.height = '100%';
                    el.style.pointerEvents = 'none';

                    try {
                        const paths = Array.isArray(a.drawing_data) ? a.drawing_data : JSON.parse(a.drawing_data || '[]');
                        if (paths && paths.length > 0) {
                            const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                            svg.style.width = "100%";
                            svg.style.height = "100%";
                            svg.style.overflow = "visible";
                            svg.style.position = "absolute";
                            svg.style.top = "0";
                            svg.style.left = "0";
                            svg.style.pointerEvents = "none";

                            const polyline = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
                            const pointsString = paths.map(p => `${p.x * scale},${p.y * scale}`).join(" ");
                            polyline.setAttribute("points", pointsString);
                            polyline.setAttribute("stroke", color);
                            polyline.setAttribute("stroke-width", "3.5");
                            polyline.setAttribute("fill", "none");
                            polyline.setAttribute("stroke-linecap", "round");
                            polyline.setAttribute("stroke-linejoin", "round");
                            polyline.style.pointerEvents = "stroke";
                            polyline.style.cursor = "pointer";
                            svg.appendChild(polyline);
                            el.appendChild(svg);
                        }
                    } catch(e) {
                        console.error("Error render SVG drawing:", e);
                    }
                } else {
                    el.style.left = `${left}px`;
                    el.style.top = `${top}px`;

                    if (type === 'box') {
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.border = `2.5px solid ${color}`;
                        el.style.backgroundColor = `${color}25`;
                        el.style.borderRadius = '6px';
                        el.style.boxShadow = `0 0 0 1px rgba(255,255,255,0.7), 0 2px 8px ${color}33`;
                    } else if (type === 'circle') {
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.border = `2.5px solid ${color}`;
                        el.style.backgroundColor = `${color}25`;
                        el.style.borderRadius = '9999px';
                        el.style.boxShadow = `0 0 0 1px rgba(255,255,255,0.7), 0 2px 8px ${color}33`;
                    } else if (type === 'pin') {
                        el.style.width = '28px';
                        el.style.height = '28px';
                        el.innerHTML = `
                            <div style="background:${color};width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 3px 8px rgba(0,0,0,0.35);font-size:12px;font-weight:900;border:2px solid white;">
                                !
                            </div>
                        `;
                    }

                    // Tambahkan badge nomor penanda di sudut kotak
                    if (badgeNum) {
                        const badge = document.createElement('span');
                        badge.className = 'annotation-badge';
                        badge.style.cssText = `position:absolute;top:-10px;left:-10px;background:${color};color:white;font-size:10px;font-weight:900;padding:1px 6px;border-radius:9999px;border:2px solid white;box-shadow:0 2px 4px rgba(0,0,0,0.25);pointer-events:none;z-index:2;line-height:1.2;`;
                        badge.textContent = `#${badgeNum}`;
                        el.appendChild(badge);
                    }
                }

                // Popup Tooltip Catatan Revisi dengan Smart Positioning & Formatting Teks
                if (a.catatan) {
                    const tooltip = document.createElement('div');

                    // Smart positioning vertikal: jika marker dekat bagian atas (< 170px), buka ke bawah agar tidak terpotong
                    let posClass = (top < 170) ? ' tooltip-bottom' : '';

                    // Smart positioning horizontal:
                    const layerWidth = layer.offsetWidth || 800;
                    if (left < 140) {
                        posClass += ' tooltip-align-left';
                    } else if (left + width > layerWidth - 140) {
                        posClass += ' tooltip-align-right';
                    }

                    tooltip.className = 'annotation-tooltip' + posClass;
                    tooltip.innerHTML = `
                        <div style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#f87171;margin-bottom:6px;padding-bottom:4px;border-bottom:1px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:space-between;">
                            <span>Catatan Koreksi #${badgeNum}</span>
                            <span style="color:#94a3b8;font-size:9px;">Hal. ${a.halaman}</span>
                        </div>
                        <div class="annotation-tooltip-body">
                            ${formatAnnotationText(a.catatan)}
                        </div>
                    `;
                    el.appendChild(tooltip);
                }

                // Klik interaktif pada coretan di PDF
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                    scrollToAnnotation(a.id, pageNum);

                    const listItem = document.getElementById(`list-item-${a.id}`);
                    if (listItem) {
                        listItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        listItem.classList.add('ring-2', 'ring-rose-500', 'bg-rose-100');
                        setTimeout(() => listItem.classList.remove('ring-2', 'ring-rose-500', 'bg-rose-100'), 2500);
                    }
                });

                layer.appendChild(el);
            });
        }

        function addAnnotationToList(anotasi) {
            document.getElementById('no-annotations-notice')?.remove();

            const container = document.getElementById('annotations-list-container');
            const countBadge = document.getElementById('annotation-count-badge');
            countBadge.textContent = `${existingAnnotations.length} Coretan`;

            const item = document.createElement('div');
            item.className = 'annotation-nav-item group p-3 rounded-xl border border-gray-200 hover:border-rose-400 hover:bg-rose-50/50 cursor-pointer transition shadow-sm relative';
            item.id = `list-item-${anotasi.id}`;
            item.onclick = () => scrollToAnnotation(anotasi.id, anotasi.halaman);

            const deleteUrl = `{{ url('pembimbing-prakerin/bimbingan-laporan/anotasi') }}/${anotasi.id}`;

            const deleteButtonHtml = isReadOnly ? '' : `
                <form method="POST" action="${deleteUrl}" onsubmit="return confirm('Hapus coretan/catatan ini?')" onclick="event.stopPropagation();">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-gray-400 hover:text-rose-600 p-0.5" title="Hapus Coretan">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            `;

            item.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 group-hover:bg-rose-200 group-hover:text-rose-800">
                        #${existingAnnotations.length} • Hal. ${anotasi.halaman}
                    </span>
                    ${deleteButtonHtml}
                </div>
                <div class="text-xs text-gray-800 font-medium leading-relaxed whitespace-pre-line">
                    ${escapeHtml(anotasi.catatan) || '(Coretan penanda tanpa catatan teks)'}
                </div>
            `;

            container.appendChild(item);
        }

        function scrollToAnnotation(id, pageNum) {
            const marker = document.getElementById(`annotation-marker-${id}`);
            const pageWrapper = document.getElementById(`page-wrapper-${pageNum}`);

            if (marker) {
                marker.scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.querySelectorAll('.annotation-marker').forEach(m => {
                    m.classList.remove('pdf-annotation-highlight', 'active-tooltip');
                });
                marker.classList.add('pdf-annotation-highlight', 'active-tooltip');
                setTimeout(() => marker.classList.remove('pdf-annotation-highlight'), 4000);
            } else if (pageWrapper) {
                pageWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            currentPage = pageNum;
            updateNavigationState();
        }

        // Tutup tooltip aktif jika klik di luar marker
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.annotation-marker')) {
                document.querySelectorAll('.annotation-marker.active-tooltip').forEach(m => {
                    m.classList.remove('active-tooltip');
                });
            }
        });

        // Zoom Controls
        document.getElementById('zoom-in-btn').addEventListener('click', () => {
            if (scale >= 2.0) return;
            scale += 0.2;
            document.getElementById('zoom-level-text').textContent = `${Math.round(scale * 100 / 1.15)}%`;
            initStudioViewer();
        });

        document.getElementById('zoom-out-btn').addEventListener('click', () => {
            if (scale <= 0.6) return;
            scale -= 0.2;
            document.getElementById('zoom-level-text').textContent = `${Math.round(scale * 100 / 1.15)}%`;
            initStudioViewer();
        });

        // Page Navigation Event Listeners
        const prevPageBtn = document.getElementById('prev-page-btn');
        if (prevPageBtn) {
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    goToPage(currentPage - 1);
                }
            });
        }

        const nextPageBtn = document.getElementById('next-page-btn');
        if (nextPageBtn) {
            nextPageBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    goToPage(currentPage + 1);
                }
            });
        }

        const pageInput = document.getElementById('page-input');
        if (pageInput) {
            pageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    goToPage(pageInput.value);
                    pageInput.blur();
                }
            });

            pageInput.addEventListener('change', () => {
                goToPage(pageInput.value);
            });
        }

        // ==========================================
        // FITUR OTOMATIS KOREKSI DENGAN STELLA AI
        // ==========================================
        let lastAiResultData = null;

        function openStellaAiModal() {
            const modal = document.getElementById('stella-ai-modal');
            const pageLabel = document.getElementById('ai-active-page-label');
            if (pageLabel) {
                pageLabel.textContent = `Hal. ${currentPage}`;
            }
            if (modal) {
                modal.style.display = 'flex';
                resetStellaAiModal();
            }
        }

        function closeStellaAiModal() {
            const modal = document.getElementById('stella-ai-modal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function resetStellaAiModal() {
            document.getElementById('ai-form-state')?.classList.remove('hidden');
            document.getElementById('ai-loading-state')?.classList.add('hidden');
            document.getElementById('ai-error-state')?.classList.add('hidden');
            document.getElementById('ai-result-state')?.classList.add('hidden');
        }

        // Ekstraksi teks dari halaman tertentu menggunakan pdf.js
        async function extractPageText(pageNum) {
            if (!pdfDoc) return '';
            try {
                const page = await pdfDoc.getPage(pageNum);
                const textContent = await page.getTextContent();
                return textContent.items.map(item => item.str).join(' ');
            } catch (e) {
                console.error(`Gagal ekstrak teks hal ${pageNum}:`, e);
                return '';
            }
        }

        // Ekstraksi teks seluruh dokumen bab
        async function extractAllDocumentText() {
            if (!pdfDoc) return '';
            let fullText = '';
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const pageText = await extractPageText(i);
                if (pageText.trim()) {
                    fullText += `\n--- [Halaman ${i}] ---\n` + pageText;
                }
            }
            return fullText;
        }

        // Jalankan Analisis Koreksi Dokumen dengan Stella AI
        async function runStellaAiCorrection() {
            const scope = document.querySelector('input[name="ai-scope"]:checked')?.value || 'halaman';
            const aiLoadingEl = document.getElementById('ai-loading-state');
            const aiResultEl = document.getElementById('ai-result-state');
            const aiFormEl = document.getElementById('ai-form-state');
            const aiErrorEl = document.getElementById('ai-error-state');

            aiFormEl.classList.add('hidden');
            aiResultEl.classList.add('hidden');
            aiErrorEl.classList.add('hidden');
            aiLoadingEl.classList.remove('hidden');

            try {
                let extractedText = '';
                if (scope === 'halaman') {
                    extractedText = await extractPageText(currentPage);
                } else {
                    extractedText = await extractAllDocumentText();
                }

                if (!extractedText || extractedText.trim().length < 15) {
                    throw new Error('Teks pada dokumen PDF tidak terdeteksi atau terlalu sedikit. Pastikan file PDF bukan gambar hasil scan murni tanpa lapisan teks (OCR).');
                }

                const response = await fetch("{{ route('pembimbing-prakerin.bimbingan-laporan.ai-koreksi', $tahap) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        lingkup: scope,
                        halaman: currentPage,
                        teks_dokumen: extractedText
                    })
                });

                const res = await response.json();
                if (!response.ok || !res.success) {
                    throw new Error(res.message || 'Terjadi kendala saat memproses koreksi dengan Stella AI.');
                }

                lastAiResultData = res.data;
                renderAiResults(res.data, scope, currentPage);
            } catch (err) {
                aiLoadingEl.classList.add('hidden');
                aiErrorEl.classList.remove('hidden');
                const errMsgEl = document.getElementById('ai-error-message');
                if (errMsgEl) errMsgEl.textContent = err.message;
            }
        }

        // Tampilkan Hasil Evaluasi Stella AI
        function renderAiResults(data, scope, pageNum) {
            document.getElementById('ai-loading-state')?.classList.add('hidden');
            const resultEl = document.getElementById('ai-result-state');
            if (!resultEl) return;
            resultEl.classList.remove('hidden');

            // 1. Skor Kelayakan
            const score = parseInt(data.skor_kelayakan, 10) || 75;
            const scoreEl = document.getElementById('ai-res-score');
            const badgeEl = document.getElementById('ai-res-score-badge');
            if (scoreEl) scoreEl.textContent = `${score}/100`;

            if (badgeEl) {
                if (score >= 85) {
                    badgeEl.className = 'px-3 py-1.5 rounded-xl font-bold text-xs bg-emerald-100 text-emerald-800';
                    badgeEl.textContent = 'Sangat Layak';
                } else if (score >= 70) {
                    badgeEl.className = 'px-3 py-1.5 rounded-xl font-bold text-xs bg-amber-100 text-amber-800';
                    badgeEl.textContent = 'Cukup / Perlu Revisi Minor';
                } else {
                    badgeEl.className = 'px-3 py-1.5 rounded-xl font-bold text-xs bg-rose-100 text-rose-800';
                    badgeEl.textContent = 'Perlu Banyak Perbaikan';
                }
            }

            // 2. Rekomendasi Keputusan
            const decCard = document.getElementById('ai-res-decision-card');
            const decText = document.getElementById('ai-res-decision-text');
            const isAcc = (data.rekomendasi_status === 'disetujui' || data.rekomendasi_status === 'acc');

            if (decCard && decText) {
                if (isAcc) {
                    decCard.className = 'p-4 rounded-2xl border-2 border-emerald-300 bg-emerald-50/70 flex flex-col justify-between gap-2';
                    decText.textContent = 'Direkomendasikan: Setujui (ACC)';
                    decText.className = 'text-sm font-black text-emerald-900 mt-0.5';
                } else {
                    decCard.className = 'p-4 rounded-2xl border-2 border-rose-300 bg-rose-50/70 flex flex-col justify-between gap-2';
                    decText.textContent = 'Direkomendasikan: Perlu Revisi';
                    decText.className = 'text-sm font-black text-rose-900 mt-0.5';
                }
            }

            // 3. Ringkasan & Catatan Arahan
            const summaryEl = document.getElementById('ai-res-summary');
            if (summaryEl) summaryEl.textContent = data.ringkasan_evaluasi || '-';

            const noteEl = document.getElementById('ai-res-note');
            if (noteEl) noteEl.textContent = data.catatan_arahan || '-';

            // 4. Poin Temuan
            const findingsContainer = document.getElementById('ai-findings-container');
            const findingsCount = document.getElementById('ai-findings-count');
            const findings = Array.isArray(data.poin_koreksi) ? data.poin_koreksi : [];

            if (findingsCount) findingsCount.textContent = `${findings.length} Temuan`;

            if (findingsContainer) {
                findingsContainer.innerHTML = '';
                if (findings.length === 0) {
                    findingsContainer.innerHTML = '<div class="p-3 text-center text-xs text-gray-400">Tidak ada kesalahan signifikan yang ditemukan pada teks ini.</div>';
                } else {
                    findings.forEach((item, idx) => {
                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'p-3 rounded-xl border border-gray-200 bg-white hover:border-indigo-300 transition text-xs space-y-1.5 shadow-2xs';
                        itemDiv.innerHTML = `
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-800">
                                    \${item.aspek || 'Koreksi'}
                                </span>
                                <button type="button" onclick="createAnnotationFromAi(\${idx})"
                                    class="text-[11px] font-bold text-rose-600 hover:text-rose-800 hover:underline flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Jadikan Catatan Anotasi</span>
                                </button>
                            </div>
                            <p class="text-gray-800 font-semibold leading-snug"><strong>Temuan:</strong> \${item.temuan || '-'}</p>
                            <p class="text-gray-600 leading-snug"><strong>Saran:</strong> \${item.saran || '-'}</p>
                        `;
                        findingsContainer.appendChild(itemDiv);
                    });
                }
            }
        }

        // Terapkan Rekomendasi Keputusan ke Radio Button Form
        function applyAiDecision() {
            if (!lastAiResultData) return;
            const isAcc = (lastAiResultData.rekomendasi_status === 'disetujui' || lastAiResultData.rekomendasi_status === 'acc');
            const radioAcc = document.getElementById('radio-status-disetujui');
            const radioRev = document.getElementById('radio-status-revisi');

            if (isAcc && radioAcc) {
                radioAcc.checked = true;
                updateDecisionButtonState();
            } else if (radioRev) {
                radioRev.checked = true;
                updateDecisionButtonState();
            }

            closeStellaAiModal();
            // Scroll ke form keputusan
            document.querySelector('form[action*="selesaikan-review"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Salin Draf Catatan Arahan AI ke Textarea Form
        function copyAiNoteToForm() {
            if (!lastAiResultData || !lastAiResultData.catatan_arahan) return;
            const textarea = document.querySelector('textarea[name="catatan_pembimbing"]');
            if (textarea) {
                const currentVal = textarea.value.trim();
                textarea.value = currentVal ? `\${currentVal}\n\n\${lastAiResultData.catatan_arahan}` : lastAiResultData.catatan_arahan;
                textarea.classList.add('ring-2', 'ring-indigo-500');
                setTimeout(() => textarea.classList.remove('ring-2', 'ring-indigo-500'), 2500);
            }
            closeStellaAiModal();
            document.querySelector('textarea[name="catatan_pembimbing"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Jadikan Temuan AI sebagai Catatan Anotasi Cepat pada Halaman Aktif
        async function createAnnotationFromAi(findingIndex) {
            if (!lastAiResultData || !Array.isArray(lastAiResultData.poin_koreksi)) return;
            const item = lastAiResultData.poin_koreksi[findingIndex];
            if (!item) return;

            const textNote = `[Stella AI: \${item.aspek || 'Koreksi'}]\n\${item.temuan}\nSaran: \${item.saran}`;

            try {
                const payload = {
                    halaman: currentPage,
                    posisi_x: 20,
                    posisi_y: 20 + (findingIndex * 8),
                    lebar: 80,
                    tinggi: 30,
                    tipe_anotasi: 'pin_catatan',
                    warna: '#ef4444',
                    catatan: textNote,
                    drawing_data: null
                };

                const res = await fetch(storeAnnotationUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success && data.anotasi) {
                    existingAnnotations.push(data.anotasi);
                    const layer = document.getElementById(`annotation-layer-\${currentPage}`);
                    if (layer) {
                        renderAnnotationMarker(layer, data.anotasi, currentPage);
                    }
                    addAnnotationToList(data.anotasi);
                    closeStellaAiModal();
                    scrollToAnnotation(data.anotasi.id, currentPage);
                } else {
                    alert(data.message || 'Gagal menyimpan anotasi dari AI.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan anotasi.');
            }
        }

        function updateDecisionButtonState() {
            const radioAcc = document.getElementById('radio-status-disetujui');
            const submitBtn = document.getElementById('btn-submit-review');
            const submitText = document.getElementById('btn-submit-text');

            if (!submitBtn || !submitText) return;

            if (radioAcc && radioAcc.checked) {
                submitBtn.classList.remove('bg-rose-600', 'hover:bg-rose-700');
                submitBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                submitText.textContent = 'Sahkan & Terbitkan Berita Acara (ACC)';
            } else {
                submitBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                submitBtn.classList.add('bg-rose-600', 'hover:bg-rose-700');
                submitText.textContent = 'Terbitkan Berita Acara Revisi';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initStudioViewer();
            updateDecisionButtonState();
        });
    </script>
</x-app-layout>
