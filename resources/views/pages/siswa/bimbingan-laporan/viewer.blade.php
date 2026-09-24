<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('siswa.bimbingan-laporan.index') }}"
                    class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Review Dokumen: {{ $tahap->nama_tahap }}</h2>
                    <p class="text-xs text-gray-500">Coretan, catatan revisi, dan navigasi langsung ke area yang dikoreksi oleh pembimbing</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($tahap->status === 'disetujui' || $tahap->nomor_berita_acara)
                    <a href="{{ route('siswa.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                        Berita Acara (PDF)
                    </a>
                @endif
                <a href="{{ $tahap->file_url }}" target="_blank" download
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Dokumen Asli
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Main PDF Viewer Panel --}}
            <div class="lg:col-span-8 space-y-4">
                {{-- Viewer Controls --}}
                <div class="bg-white rounded-2xl border border-gray-200 p-3 shadow-sm flex flex-wrap items-center justify-between gap-3">
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

                {{-- PDF Pages Container --}}
                <div id="pdf-viewer-container" class="bg-gray-200/70 rounded-2xl p-4 md:p-8 min-h-[600px] overflow-auto flex flex-col items-center gap-8 shadow-inner border border-gray-200">
                    <div id="pdf-loading-indicator" class="flex flex-col items-center justify-center p-12 text-gray-500">
                        <svg class="w-10 h-10 animate-spin text-rose-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-xs font-bold">Memuat Dokumen PDF...</span>
                    </div>
                </div>
            </div>

            {{-- Right Sidebar: Summary & Navigation to Annotations --}}
            <div class="lg:col-span-4 space-y-6 sticky top-4">
                {{-- Status & Info Tahap --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Tahap</span>
                        @if ($tahap->status === 'disetujui')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Disetujui
                            </span>
                        @elseif ($tahap->status === 'revisi')
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Perlu Revisi
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Menunggu Review</span>
                        @endif
                    </div>

                    <div>
                        <h4 class="font-extrabold text-gray-900 text-base">{{ $tahap->nama_tahap }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Laporan: {{ $laporan->judul }}</p>
                    </div>

                    @if ($tahap->catatan_pembimbing)
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
                            <span class="font-bold text-amber-800 block mb-1">Catatan Kesimpulan Pembimbing:</span>
                            {{ $tahap->catatan_pembimbing }}
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 space-y-1">
                        <div>Pembimbing: <span class="font-semibold text-gray-700">{{ $tahap->reviewer?->name ?? 'Guru Pembimbing' }}</span></div>
                        <div>Waktu Review: <span class="font-semibold text-gray-700">{{ $tahap->direview_at ? \Carbon\Carbon::parse($tahap->direview_at)->translatedFormat('d F Y, H:i') : '-' }}</span></div>
                    </div>
                </div>

                {{-- Navigasi Catatan & Coretan Revisi --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm">Daftar Catatan Revisi</h4>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                            {{ $anotasis->count() }} Coretan
                        </span>
                    </div>

                    <p class="text-[11px] text-gray-500">
                        Klik catatan di bawah ini untuk langsung melompat dan menyorot area coretan pembimbing pada dokumen.
                    </p>

                    <div class="space-y-2.5 max-h-[460px] overflow-y-auto pr-1">
                        @forelse ($anotasis as $index => $anotasi)
                            <div class="annotation-nav-item group p-3 rounded-xl border border-gray-200 hover:border-rose-400 hover:bg-rose-50/50 cursor-pointer transition shadow-sm"
                                data-id="{{ $anotasi->id }}"
                                data-page="{{ $anotasi->halaman }}"
                                data-x="{{ $anotasi->koordinat_x }}"
                                data-y="{{ $anotasi->koordinat_y }}"
                                onclick="scrollToAnnotation({{ $anotasi->id }}, {{ $anotasi->halaman }})">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 group-hover:bg-rose-200 group-hover:text-rose-800">
                                        #{{ $index + 1 }} • Hal. {{ $anotasi->halaman }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-gray-400 capitalize">
                                        {{ $anotasi->tipe === 'box' ? 'Kotak' : ($anotasi->tipe === 'circle' ? 'Lingkaran' : ($anotasi->tipe === 'pin' ? 'Pin' : 'Coretan')) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-800 font-medium leading-snug">
                                    {{ $anotasi->catatan ?: '(Tanda koreksi tanpa catatan teks)' }}
                                </p>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs">
                                Belum ada coretan atau catatan revisi pada dokumen ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF.js Core Script --}}
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
    <style>
        .page-wrapper {
            position: relative;
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: visible !important;
        }
        .annotation-layer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 25;
        }
        .pdf-annotation-highlight {
            animation: pulse-glow 1.5s infinite alternate;
            z-index: 40 !important;
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
        /* Custom Tooltip Styling */
        .annotation-marker {
            position: absolute;
            cursor: pointer;
            pointer-events: auto;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .annotation-marker:hover {
            transform: scale(1.02);
            z-index: 35;
        }
        .annotation-tooltip {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
            z-index: 60;
            min-width: 160px;
            max-width: 300px;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.2);
            white-space: normal;
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
            top: calc(100% + 8px);
        }
        .annotation-tooltip.tooltip-bottom::after {
            top: auto;
            bottom: 100%;
            border-color: transparent transparent #0f172a transparent;
        }
        .annotation-marker:hover .annotation-tooltip,
        .annotation-marker.active-tooltip .annotation-tooltip {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto;
        }
    </style>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

        const pdfUrl = @js($tahap->file_url);
        const annotations = @js($anotasis);

        let pdfDoc = null;
        let scale = 1.15;
        let totalPages = 0;
        let currentPage = 1;
        let pageObserver = null;
        let renderedPages = {};

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

        async function initPdfViewer() {
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

                // Render each page
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    await renderPage(pageNum, container);
                }

                setupPageObserver();
            } catch (err) {
                console.error("Gagal memuat PDF:", err);
                const container = document.getElementById('pdf-viewer-container');
                container.innerHTML = `
                    <div class="p-8 text-center text-rose-600 bg-rose-50 rounded-2xl border border-rose-200">
                        <p class="font-bold">Gagal membuka dokumen PDF.</p>
                        <p class="text-xs mt-1 text-gray-600">Pastikan file PDF valid atau unduh langsung melalui tombol di atas.</p>
                    </div>
                `;
            }
        }

        async function renderPage(pageNum, container) {
            const page = await pdfDoc.getPage(pageNum);
            const viewport = page.getViewport({ scale: scale });

            const pageWrapper = document.createElement('div');
            pageWrapper.className = 'page-wrapper';
            pageWrapper.id = `page-wrapper-${pageNum}`;
            pageWrapper.style.width = `${viewport.width}px`;
            pageWrapper.style.height = `${viewport.height}px`;

            const canvas = document.createElement('canvas');
            canvas.className = 'pdf-canvas';
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            const ctx = canvas.getContext('2d');

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            await page.render(renderContext).promise;
            pageWrapper.appendChild(canvas);

            // Layer Anotasi
            const annotationLayer = document.createElement('div');
            annotationLayer.className = 'annotation-layer';
            annotationLayer.id = `annotation-layer-${pageNum}`;
            pageWrapper.appendChild(annotationLayer);

            container.appendChild(pageWrapper);
            renderedPages[pageNum] = { viewport, scale };

            // Render anotasi pada halaman ini
            renderAnnotationsForPage(pageNum, annotationLayer, viewport);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderAnnotationsForPage(pageNum, layer, viewport) {
            layer.innerHTML = '';
            const pageAnnotations = annotations.filter(a => Number(a.halaman) === Number(pageNum));

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

                const globalIndex = annotations.findIndex(x => x.id === a.id);
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

                // Popup Tooltip Catatan Revisi
                if (a.catatan) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'annotation-tooltip' + (top < 70 ? ' tooltip-bottom' : '');
                    tooltip.innerHTML = `
                        <div style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;color:#f87171;margin-bottom:3px;display:flex;align-items:center;justify-content:space-between;">
                            <span>Catatan Koreksi #${badgeNum}</span>
                            <span style="color:#94a3b8;font-size:9px;">Hal. ${a.halaman}</span>
                        </div>
                        <div style="font-size:11px;font-weight:600;color:#ffffff;line-height:1.4;">
                            ${escapeHtml(a.catatan)}
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

        function scrollToAnnotation(id, pageNum) {
            const marker = document.getElementById(`annotation-marker-${id}`);
            const pageWrapper = document.getElementById(`page-wrapper-${pageNum}`);

            if (marker) {
                marker.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Remove previous highlights and active tooltips
                document.querySelectorAll('.annotation-marker').forEach(m => {
                    m.classList.remove('pdf-annotation-highlight', 'active-tooltip');
                });

                // Add highlight & open tooltip
                marker.classList.add('pdf-annotation-highlight', 'active-tooltip');

                setTimeout(() => {
                    marker.classList.remove('pdf-annotation-highlight');
                }, 4000);
            } else if (pageWrapper) {
                pageWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            currentPage = pageNum;
            updateNavigationState();
        }

        // Close tooltips when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.annotation-marker') && !e.target.closest('.annotation-nav-item')) {
                document.querySelectorAll('.annotation-marker.active-tooltip').forEach(m => {
                    m.classList.remove('active-tooltip');
                });
            }
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

        // Zoom controls
        document.getElementById('zoom-in-btn').addEventListener('click', () => {
            if (scale >= 2.0) return;
            scale += 0.2;
            document.getElementById('zoom-level-text').textContent = `${Math.round(scale * 100 / 1.15)}%`;
            initPdfViewer();
        });

        document.getElementById('zoom-out-btn').addEventListener('click', () => {
            if (scale <= 0.6) return;
            scale -= 0.2;
            document.getElementById('zoom-level-text').textContent = `${Math.round(scale * 100 / 1.15)}%`;
            initPdfViewer();
        });

        document.addEventListener('DOMContentLoaded', () => {
            initPdfViewer();
        });
    </script>
</x-app-layout>
