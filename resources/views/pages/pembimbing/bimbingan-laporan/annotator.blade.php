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

        <div class="flex items-center gap-2">
            @if ($tahap->status === 'disetujui' || $tahap->nomor_berita_acara)
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                    Berita Acara (PDF)
                </a>
            @endif
            <a href="{{ $tahap->file_url }}" target="_blank" download
                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Unduh File Asli
            </a>
        </div>
    </div>

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Studio Workspace: Toolbar & Canvas --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Interactive Annotation Toolbar --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-3 shadow-sm sticky top-4 z-20 flex flex-wrap items-center justify-between gap-3">
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

                    {{-- Zoom & Page Controls --}}
                    <div class="flex items-center gap-2">
                        <button type="button" id="zoom-out-btn" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Perkecil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span id="zoom-level-text" class="text-xs font-bold text-gray-600 min-w-[45px] text-center">100%</span>
                        <button type="button" id="zoom-in-btn" class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Perbesar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Drawing Instruction Banner --}}
                <div class="p-3 bg-rose-50/80 border border-rose-200 rounded-xl text-xs text-rose-900 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><strong>Petunjuk:</strong> Klik dan seret (drag) mouse di atas dokumen PDF untuk menandai area koreksi (kotak/lingkaran/coretan). Jendela catatan revisi akan muncul otomatis saat Anda melepaskan mouse.</span>
                </div>

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

            {{-- Right Sidebar: Annotations List & Final Review Form --}}
            <div class="lg:col-span-4 space-y-6 sticky top-4">
                {{-- Form Selesaikan Review & Penerbitan Berita Acara Digital --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                        <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-sm">Selesaikan Bimbingan Bab</h4>
                    </div>

                    <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.selesaikan-review', $tahap) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Keputusan Status Bab <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="border-2 rounded-xl p-3 flex items-center gap-2 cursor-pointer transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50">
                                    <input type="radio" name="status" value="revisi" {{ $tahap->status === 'revisi' ? 'checked' : '' }} required
                                        class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs font-bold text-gray-800">Perlu Revisi</span>
                                </label>
                                <label class="border-2 rounded-xl p-3 flex items-center gap-2 cursor-pointer transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                                    <input type="radio" name="status" value="disetujui" {{ $tahap->status === 'disetujui' ? 'checked' : '' }} required
                                        class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-bold text-gray-800">Setujui (ACC)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Catatan Kesimpulan Pembimbing (Opsional)
                            </label>
                            <textarea name="catatan_pembimbing" rows="3" maxlength="1000"
                                placeholder="Tuliskan rangkuman arahan atau poin pokok yang harus diperbaiki siswa..."
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">{{ old('catatan_pembimbing', $tahap->catatan_pembimbing) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                PIN Tanda Tangan Digital <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" name="pin" required maxlength="10" placeholder="6 digit PIN TTD Digital"
                                class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                            <p class="text-[10px] text-gray-400 mt-1">Dibutuhkan untuk membubuhkan QR tanda tangan digital pada Berita Acara bimbingan resmi.</p>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 shadow-md transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Sahkan & Terbitkan Berita Acara
                        </button>
                    </form>
                </div>

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
                                    <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.hapus-anotasi', $anotasi) }}"
                                        onsubmit="return confirm('Hapus coretan/catatan ini?')" onclick="event.stopPropagation();">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-rose-600 p-0.5" title="Hapus Coretan">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
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
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">Tulis Catatan Revisi Coretan</h4>
                <button type="button" onclick="cancelPendingAnnotation()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <textarea id="pending-note-input" rows="3" maxlength="1000"
                    placeholder="Tuliskan arahan perbaikan untuk area yang baru saja Anda tandai..."
                    class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                <button type="button" onclick="cancelPendingAnnotation()"
                    class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    Batal
                </button>
                <button type="button" onclick="savePendingAnnotation()"
                    class="px-4 py-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition">
                    Simpan Coretan
                </button>
            </div>
        </div>
    </div>

    {{-- PDF.js & Drawing Studio Engine --}}
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
    <style>
        .page-wrapper {
            position: relative;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
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
            z-index: 15;
        }
        .annotation-marker {
            position: absolute;
            pointer-events: auto;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .annotation-marker:hover {
            transform: scale(1.03);
            z-index: 30;
        }
        .annotation-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-6px);
            background: #1e293b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 8px;
            white-space: normal;
            min-width: 140px;
            max-width: 240px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.2s ease;
            z-index: 40;
            text-align: center;
        }
        .annotation-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: #1e293b transparent transparent transparent;
        }
        .annotation-marker:hover .annotation-tooltip,
        .annotation-marker.active-tooltip .annotation-tooltip {
            opacity: 1;
            visibility: visible;
        }
        .pdf-annotation-highlight {
            animation: pulse-glow 1.5s infinite alternate;
        }
        @keyframes pulse-glow {
            from {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                filter: drop-shadow(0 0 4px rgba(239, 68, 68, 0.8));
            }
            to {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
                filter: drop-shadow(0 0 12px rgba(239, 68, 68, 1));
            }
        }
    </style>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

        const pdfUrl = @js($tahap->file_url);
        const tahapId = @js($tahap->id);
        const storeAnnotationUrl = "{{ route('pembimbing-prakerin.bimbingan-laporan.simpan-anotasi', $tahap) }}";
        const csrfToken = "{{ csrf_token() }}";
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

        async function initStudioViewer() {
            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;
                document.getElementById('pdf-loading-indicator')?.remove();

                const container = document.getElementById('pdf-viewer-container');
                container.innerHTML = '';

                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    await renderStudioPage(pageNum, container);
                }
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

        function renderPageAnnotations(pageNum, layer) {
            layer.innerHTML = '';
            const pageAnnotations = existingAnnotations.filter(a => Number(a.halaman) === Number(pageNum));

            pageAnnotations.forEach(a => {
                const el = document.createElement('div');
                el.className = 'annotation-marker';
                el.id = `annotation-marker-${a.id}`;

                const left = a.koordinat_x * scale;
                const top = a.koordinat_y * scale;
                const width = (a.lebar || 80) * scale;
                const height = (a.tinggi || 40) * scale;
                const color = a.warna || '#ef4444';

                el.style.left = `${left}px`;
                el.style.top = `${top}px`;

                if (a.tipe === 'box') {
                    el.style.width = `${width}px`;
                    el.style.height = `${height}px`;
                    el.style.border = `2.5px solid ${color}`;
                    el.style.backgroundColor = `${color}25`;
                    el.style.borderRadius = '6px';
                } else if (a.tipe === 'circle') {
                    el.style.width = `${width}px`;
                    el.style.height = `${height}px`;
                    el.style.border = `2.5px solid ${color}`;
                    el.style.backgroundColor = `${color}25`;
                    el.style.borderRadius = '9999px';
                } else if (a.tipe === 'pin') {
                    el.style.width = '24px';
                    el.style.height = '24px';
                    el.innerHTML = `
                        <div style="background:${color};width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 2px 6px rgba(0,0,0,0.3);font-size:11px;font-weight:bold;">
                            !
                        </div>
                    `;
                } else if (a.tipe === 'drawing' && a.drawing_data) {
                    try {
                        const paths = Array.isArray(a.drawing_data) ? a.drawing_data : JSON.parse(a.drawing_data);
                        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                        svg.setAttribute("width", width || 200);
                        svg.setAttribute("height", height || 100);
                        svg.style.overflow = "visible";

                        const polyline = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
                        const pointsString = paths.map(p => `${p.x * scale},${p.y * scale}`).join(" ");
                        polyline.setAttribute("points", pointsString);
                        polyline.setAttribute("stroke", color);
                        polyline.setAttribute("stroke-width", "3");
                        polyline.setAttribute("fill", "none");
                        polyline.setAttribute("stroke-linecap", "round");
                        svg.appendChild(polyline);
                        el.appendChild(svg);
                    } catch(e) {}
                }

                if (a.catatan) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'annotation-tooltip';
                    tooltip.textContent = a.catatan;
                    el.appendChild(tooltip);
                }

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

            item.innerHTML = `
                <div class="flex items-center justify-between mb-1">
                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 group-hover:bg-rose-200 group-hover:text-rose-800">
                        #${existingAnnotations.length} • Hal. ${anotasi.halaman}
                    </span>
                    <form method="POST" action="${deleteUrl}" onsubmit="return confirm('Hapus coretan/catatan ini?')" onclick="event.stopPropagation();">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-gray-400 hover:text-rose-600 p-0.5" title="Hapus Coretan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                <p class="text-xs text-gray-800 font-medium leading-snug">
                    ${anotasi.catatan || '(Coretan penanda tanpa catatan teks)'}
                </p>
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
        }

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

        document.addEventListener('DOMContentLoaded', () => {
            initStudioViewer();
        });
    </script>
</x-app-layout>
