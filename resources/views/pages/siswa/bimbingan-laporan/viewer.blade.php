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
                <div class="bg-white rounded-2xl border border-gray-100 p-3 shadow-sm flex items-center justify-between gap-4 sticky top-4 z-20">
                    <div class="flex items-center gap-2">
                        <button type="button" id="prev-page-btn"
                            class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Halaman Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-semibold text-gray-700">
                            Hal. <span id="current-page-num" class="font-bold">1</span> dari <span id="total-pages-num" class="font-bold">-</span>
                        </span>
                        <button type="button" id="next-page-btn"
                            class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Halaman Selanjutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="zoom-out-btn" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Perkecil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span id="zoom-level-text" class="text-xs font-bold text-gray-600 min-w-[50px] text-center">100%</span>
                        <button type="button" id="zoom-in-btn" class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Perbesar">
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
                        <div>Waktu Review: <span class="font-semibold text-gray-700">{{ $tahap->direview_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span></div>
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
            overflow: hidden;
        }
        .annotation-layer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: auto;
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
        /* Custom Tooltip Styling */
        .annotation-marker {
            position: absolute;
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
    </style>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";

        const pdfUrl = @js($tahap->file_url);
        const annotations = @js($anotasis);

        let pdfDoc = null;
        let scale = 1.15;
        let totalPages = 0;
        let renderedPages = {};

        async function initPdfViewer() {
            try {
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;
                document.getElementById('total-pages-num').textContent = totalPages;
                document.getElementById('pdf-loading-indicator')?.remove();

                const container = document.getElementById('pdf-viewer-container');
                container.innerHTML = '';

                // Render each page
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    await renderPage(pageNum, container);
                }
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

        function renderAnnotationsForPage(pageNum, layer, viewport) {
            const pageAnnotations = annotations.filter(a => Number(a.halaman) === Number(pageNum));

            pageAnnotations.forEach(a => {
                const el = document.createElement('div');
                el.className = 'annotation-marker';
                el.id = `annotation-marker-${a.id}`;

                // Calculate scaled coordinates (basis scale 1.0 vs current scale)
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
                    el.style.backgroundColor = `${color}20`; // translucent fill
                    el.style.borderRadius = '6px';
                } else if (a.tipe === 'circle') {
                    el.style.width = `${width}px`;
                    el.style.height = `${height}px`;
                    el.style.border = `2.5px solid ${color}`;
                    el.style.backgroundColor = `${color}20`;
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
                    // SVG path drawing
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
                    } catch(e) {
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.border = `2px dashed ${color}`;
                    }
                }

                // Add Tooltip
                if (a.catatan) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'annotation-tooltip';
                    tooltip.textContent = a.catatan;
                    el.appendChild(tooltip);
                }

                layer.appendChild(el);
            });
        }

        function scrollToAnnotation(id, pageNum) {
            const marker = document.getElementById(`annotation-marker-${id}`);
            const pageWrapper = document.getElementById(`page-wrapper-${pageNum}`);

            if (marker) {
                marker.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Remove previous highlights
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

            document.getElementById('current-page-num').textContent = pageNum;
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
