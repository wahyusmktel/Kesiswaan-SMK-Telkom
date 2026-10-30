<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Laporan Pekanan OKR</h2>
            <p class="mt-1 text-sm text-gray-500">Komitmen Senin, evaluasi Jumat, dan monitoring progres seluruh unit.</p>
        </div>
    </x-slot>

    @php
        $statusLabels = ['not_reported' => 'Belum melapor', 'draft' => 'Draft Senin', 'submitted' => 'Menunggu tinjauan', 'reviewed' => 'Sudah ditinjau'];
        $statusColors = ['not_reported' => 'bg-gray-100 text-gray-600', 'draft' => 'bg-blue-100 text-blue-700', 'submitted' => 'bg-amber-100 text-amber-800', 'reviewed' => 'bg-emerald-100 text-emerald-700'];
        $itemStatusLabels = ['not_started' => 'Belum dimulai', 'on_progress' => 'Berjalan', 'completed' => 'Tercapai', 'blocked' => 'Terhambat'];
        $savedItemsCount = $report?->items?->count() ?? 0;
        $oldItemsCount = is_array(old('items')) ? count(old('items')) : 0;
        $initialCount = max(3, $savedItemsCount, $oldItemsCount);
        $planningItems = collect(range(0, $initialCount - 1))->map(fn ($index) => $report?->items?->values()->get($index));
        $previousWeek = $weekStart->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->addWeek()->format('Y-m-d');
        $reportProgress = $report?->items->isNotEmpty() ? round((float) $report->items->avg('completion_percent'), 1) : 0;
        $reportCompleted = $report?->items->where('final_status', 'completed')->count() ?? 0;
        $reportBlocked = $report?->items->where('final_status', 'blocked')->count() ?? 0;
    @endphp

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <style>
            .ql-toolbar.ql-snow {
                border-color: #fcd34d !important;
                background-color: #fef3c7 !important;
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
                padding: 6px 10px !important;
            }
            .ql-container.ql-snow {
                border-color: #fcd34d !important;
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                background-color: #ffffff;
                font-family: inherit;
                font-size: 0.875rem;
            }
            .ql-editor {
                min-height: 120px;
                max-height: 320px;
                overflow-y: auto;
                line-height: 1.6;
                padding: 12px 14px;
            }
            .ql-editor.ql-blank::before {
                color: #9ca3af;
                font-style: normal;
                font-size: 0.875rem;
            }
            .okr-review-content {
                line-height: 1.7;
            }
            .okr-review-content p {
                margin-bottom: 0.65rem;
            }
            .okr-review-content p:last-child {
                margin-bottom: 0;
            }
            .okr-review-content ol {
                list-style-type: decimal !important;
                padding-left: 1.35rem !important;
                margin-top: 0.4rem !important;
                margin-bottom: 0.65rem !important;
            }
            .okr-review-content ul {
                list-style-type: disc !important;
                padding-left: 1.35rem !important;
                margin-top: 0.4rem !important;
                margin-bottom: 0.65rem !important;
            }
            .okr-review-content li {
                margin-bottom: 0.35rem !important;
            }
            .okr-review-content li:last-child {
                margin-bottom: 0 !important;
            }
            .okr-review-content h1,
            .okr-review-content h2,
            .okr-review-content h3 {
                font-weight: 800;
                margin-top: 0.75rem;
                margin-bottom: 0.35rem;
                color: #064e3b;
            }
            .okr-review-content h1 { font-size: 1.15rem; }
            .okr-review-content h2 { font-size: 1.05rem; }
            .okr-review-content h3 { font-size: 0.95rem; }
            .okr-review-content strong,
            .okr-review-content b {
                font-weight: 700;
                color: #064e3b;
            }
            .okr-review-content blockquote {
                border-left: 3px solid #10b981;
                padding-left: 0.75rem;
                margin: 0.5rem 0;
                color: #047857;
                font-style: italic;
            }
            [x-cloak] {
                display: none !important;
            }

            /* Monitoring Modal Interaktif (Lega, Draggable, Maximizable) */
            .monitoring-modal-backdrop {
                position: fixed; inset: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(4px);
                z-index: 99999;
                display: flex; align-items: center; justify-content: center;
                padding: 1rem;
            }
            .monitoring-modal-box {
                background: #fff;
                border-radius: 0.5rem;
                border: 1px solid #cbd5e1;
                box-shadow: 0 25px 60px rgba(0,0,0,.3);
                width: 100%;
                max-width: 1250px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                max-height: 88vh;
            }
            .monitoring-modal-box.maximized {
                position: fixed !important;
                inset: 0 !important;
                max-width: 100vw !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                z-index: 99999 !important;
            }
            .monitoring-modal-box.positioned {
                position: fixed;
            }
            .monitoring-modal-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 0.875rem 1.25rem;
                flex-shrink: 0;
                border-bottom: 1px solid #c7d2fe;
                background: linear-gradient(to right, #eef2ff, #e0e7ff);
                user-select: none;
            }
            .monitoring-modal-body {
                padding: 1rem 1.25rem;
                overflow-y: auto;
                flex: 1;
            }
            .monitoring-modal-footer {
                padding: 0.875rem 1.25rem;
                border-top: 1px solid #e5e7eb;
                display: flex; align-items: center; justify-content: space-between;
                flex-shrink: 0;
                background: #f8fafc;
            }

            /* Unlinked Commitments Modal Interaktif */
            .unlinked-modal-box {
                background: #fff;
                border-radius: 0.5rem;
                border: 1px solid #fcd34d;
                box-shadow: 0 25px 60px rgba(0,0,0,.3);
                width: 100%;
                max-width: 1250px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                max-height: 88vh;
            }
            .unlinked-modal-box.maximized {
                position: fixed !important;
                inset: 0 !important;
                max-width: 100vw !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                z-index: 99999 !important;
            }
            .unlinked-modal-box.positioned {
                position: fixed;
            }
            .unlinked-modal-header {
                display: flex; align-items: center; justify-content: space-between;
                padding: 0.875rem 1.25rem;
                flex-shrink: 0;
                border-bottom: 1px solid #fcd34d;
                background: linear-gradient(to right, #fef3c7, #fde68a);
                user-select: none;
            }
            .unlinked-modal-body {
                padding: 0;
                overflow-y: auto;
                flex: 1;
            }
            .unlinked-modal-footer {
                padding: 0.875rem 1.25rem;
                border-top: 1px solid #e5e7eb;
                display: flex; align-items: center; justify-content: space-between;
                flex-shrink: 0;
                background: #fffbeb;
            }
        </style>
    @endpush

    <div class="min-h-screen bg-gray-50 py-6" x-data="weeklyOkrDashboard()" x-init="initCharts()">
        <div class="mx-auto max-w-[1600px] space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800"><p class="font-black">Data belum dapat disimpan.</p><p class="mt-1">{{ $errors->first() }}</p></div>
            @endif

            <section class="border border-indigo-100 bg-indigo-50 px-5 py-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-black text-indigo-950">Satu siklus laporan untuk satu pekan</p>
                        <p class="mt-1 max-w-3xl text-xs leading-5 text-indigo-800">Kepala unit menyimpan komitmen target pada Senin, kemudian mengisi capaian aktual, kendala, dan tindak lanjut pada Jumat. Kepala Sekolah meninjau hasil seluruh unit dari halaman ini.</p>
                    </div>
                    <a href="{{ route('okr.index', ['period_id' => $period->id, 'unit_id' => $selectedUnit->id]) }}" class="rounded-md border border-indigo-200 bg-white px-4 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Kembali ke Matriks OKR</a>
                </div>
            </section>

            @if($unlinkedCommitments->isNotEmpty())
                @php
                    $unlinkedUnits = $unlinkedCommitments->map(fn($item) => $item->report->unit)->unique('id')->values();
                @endphp
                <div 
                    x-data="{
                        unlinkedModal: {
                            open: false,
                            maximized: false,
                            dragging: false,
                            pos: null,
                            dragOffset: { x: 0, y: 0 }
                        },
                        unlinkedSearch: '',
                        unlinkedUnitFilter: 'all',

                        openUnlinkedModal() {
                            this.unlinkedModal.open = true;
                            this.unlinkedModal.maximized = false;
                            this.unlinkedModal.pos = null;
                            this.unlinkedSearch = '';
                            this.unlinkedUnitFilter = 'all';
                        },
                        closeUnlinkedModal() {
                            this.unlinkedModal.open = false;
                            this.unlinkedModal.maximized = false;
                            this.unlinkedModal.pos = null;
                        },
                        toggleUnlinkedMaximize() {
                            this.unlinkedModal.maximized = !this.unlinkedModal.maximized;
                            if (this.unlinkedModal.maximized) {
                                this.unlinkedModal.pos = null;
                            }
                        },
                        startUnlinkedDrag(e) {
                            if (this.unlinkedModal.maximized) return;
                            this.unlinkedModal.dragging = true;
                            const box = this.$refs.unlinkedModalBox;
                            if (!box) return;
                            const rect = box.getBoundingClientRect();
                            this.unlinkedModal.pos = { x: rect.left, y: rect.top };
                            this.unlinkedModal.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
                        },
                        onUnlinkedDrag(e) {
                            if (!this.unlinkedModal.dragging) return;
                            const maxX = window.innerWidth - 120;
                            const maxY = window.innerHeight - 60;
                            this.unlinkedModal.pos.x = Math.max(0, Math.min(e.clientX - this.unlinkedModal.dragOffset.x, maxX));
                            this.unlinkedModal.pos.y = Math.max(0, Math.min(e.clientY - this.unlinkedModal.dragOffset.y, maxY));
                        },
                        stopUnlinkedDrag() {
                            this.unlinkedModal.dragging = false;
                        },
                        matchesUnlinkedFilter(unitId, text) {
                            if (this.unlinkedUnitFilter !== 'all' && this.unlinkedUnitFilter != unitId) return false;
                            if (this.unlinkedSearch && this.unlinkedSearch.trim() !== '') {
                                const q = this.unlinkedSearch.toLowerCase().trim();
                                if (!text.includes(q)) return false;
                            }
                            return true;
                        }
                    }"
                    @mousemove.window="onUnlinkedDrag($event)"
                    @mouseup.window="stopUnlinkedDrag()"
                    class="rounded-lg border-2 border-amber-300 bg-gradient-to-r from-amber-50 via-amber-50/80 to-amber-100/50 p-4 sm:p-5 shadow-sm space-y-3"
                >
                    {{-- Header Ringkas Card di Halaman Utama --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white shadow-md shadow-amber-500/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-black text-amber-950">
                                        Komitmen Belum Dikaitkan dengan Target OKR
                                    </h3>
                                    <span class="rounded-full bg-amber-200 px-2.5 py-0.5 text-[10px] font-black uppercase text-amber-900 tracking-wide border border-amber-300">
                                        Urgensi Penautan
                                    </span>
                                    <span class="rounded bg-amber-200/80 text-amber-900 px-2 py-0.5 text-[10px] font-bold">
                                        Periode: {{ $period->title }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-amber-900 leading-relaxed max-w-3xl">
                                    Terdapat <strong>{{ $unlinkedCommitments->count() }} komitmen</strong> yang belum terhubung ke Target OKR Mingguan. Klik tombol untuk membuka modal penautan dan menghubungkan komitmen ke target agar rekomendasi progres berjalan otomatis.
                                </p>
                            </div>
                        </div>

                        {{-- Tombol Buka Modal Penautan --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button 
                                type="button" 
                                @click="openUnlinkedModal()"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 hover:bg-amber-700 px-4 py-2.5 text-xs font-black text-white shadow-md shadow-amber-600/25 hover:shadow-lg transition cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <span>Tautkan Target OKR (Buka Modal)</span>
                                <span class="bg-amber-800/90 text-white px-2 py-0.5 rounded-md text-[10px] font-mono font-bold">{{ $unlinkedCommitments->count() }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════════════
                         MODAL INTERAKTIF: PENAUTAN KOMITMEN KE TARGET OKR
                         (Lega, Draggable via Header, Maximizable, Hanya Ditutup via Button)
                    ══════════════════════════════════════════════════════════════ --}}
                    <template x-teleport="body">
                        <div 
                            x-show="unlinkedModal.open" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="monitoring-modal-backdrop"
                            style="display: none;"
                        >
                            <div 
                                x-ref="unlinkedModalBox"
                                class="unlinked-modal-box"
                                :class="{
                                    'maximized': unlinkedModal.maximized,
                                    'positioned': !unlinkedModal.maximized && unlinkedModal.pos !== null
                                }"
                                :style="!unlinkedModal.maximized && unlinkedModal.pos !== null
                                    ? 'left:' + unlinkedModal.pos.x + 'px; top:' + unlinkedModal.pos.y + 'px; width:1250px; max-width:95vw;'
                                    : ''"
                                @click.stop
                            >
                                {{-- 1. Header Modal (Drag Handle) --}}
                                <div class="unlinked-modal-header"
                                    :class="!unlinkedModal.maximized ? 'cursor-grab active:cursor-grabbing' : 'cursor-default'"
                                    @mousedown="startUnlinkedDrag($event)"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-amber-600 flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-sm font-black text-amber-950">Penautan Komitmen ke Target OKR Mingguan</h3>
                                                <span class="rounded bg-amber-200 text-amber-900 px-2 py-0.5 text-[10px] font-bold">
                                                    Periode {{ $period->title }}
                                                </span>
                                                <span class="rounded-full bg-amber-300 text-amber-950 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide border border-amber-400">
                                                    {{ $unlinkedCommitments->count() }} Komitmen Perlu Ditautkan
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-amber-800 font-medium">Tahan dan geser header untuk memindahkan modal. Pilih target OKR mingguan yang sesuai pada dropdown masing-masing baris.</p>
                                        </div>
                                    </div>

                                    {{-- Control Buttons (Maximize & Close) --}}
                                    <div class="flex items-center gap-1.5 flex-shrink-0" @mousedown.stop>
                                        {{-- Maximize Button --}}
                                        <button 
                                            type="button"
                                            @click="toggleUnlinkedMaximize()"
                                            class="w-8 h-8 rounded-md flex items-center justify-center text-amber-800 hover:bg-amber-200 transition-colors cursor-pointer border border-transparent hover:border-amber-300"
                                            :title="unlinkedModal.maximized ? 'Kembalikan ukuran' : 'Perbesar layar penuh'"
                                        >
                                            <svg x-show="!unlinkedModal.maximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                            </svg>
                                            <svg x-show="unlinkedModal.maximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                                            </svg>
                                        </button>

                                        {{-- Close Button (X) --}}
                                        <button 
                                            type="button"
                                            @click="closeUnlinkedModal()"
                                            class="w-8 h-8 rounded-md flex items-center justify-center text-amber-800 hover:bg-red-50 hover:text-red-600 transition-colors cursor-pointer border border-transparent hover:border-red-200"
                                            title="Tutup (Esc)"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- 2. Sub-Header: Search & Filter Toolbar --}}
                                <div class="px-5 py-3 border-b border-amber-200 bg-amber-50/70 flex flex-wrap items-center justify-between gap-3">
                                    <div class="relative flex-1 min-w-[220px] max-w-md">
                                        <input 
                                            type="text" 
                                            x-model="unlinkedSearch"
                                            placeholder="Cari komitmen, unit kerja, atau target..." 
                                            class="w-full text-xs rounded-md border-amber-300 pl-8 pr-3 py-1.5 focus:border-amber-500 focus:ring-amber-500 bg-white"
                                        >
                                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>

                                    @if($unlinkedUnits->count() > 1)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-amber-900 whitespace-nowrap">Filter Unit:</span>
                                            <select x-model="unlinkedUnitFilter" class="rounded-md border-amber-300 text-xs py-1 px-2.5 bg-white font-semibold text-gray-700 focus:border-amber-500 focus:ring-amber-500">
                                                <option value="all">Semua Unit ({{ $unlinkedCommitments->count() }})</option>
                                                @foreach($unlinkedUnits as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- Form & Body Table --}}
                                <form method="POST" action="{{ route('okr.weekly.link-all-commitments') }}" class="flex flex-col flex-1 overflow-hidden m-0">
                                    @csrf
                                    <input type="hidden" name="period_id" value="{{ $period->id }}">
                                    <input type="hidden" name="unit_id" value="{{ $selectedUnit->id }}">
                                    <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">

                                    <div class="unlinked-modal-body">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead class="sticky top-0 bg-amber-100/95 backdrop-blur-xs font-black text-amber-950 uppercase tracking-wider text-[11px] border-b border-amber-200 shadow-xs z-10">
                                                <tr>
                                                    <th class="px-4 py-3 w-40">Unit Kerja</th>
                                                    <th class="px-4 py-3 w-44">Pekan Laporan</th>
                                                    <th class="px-4 py-3">Komitmen &amp; Target Terukur</th>
                                                    <th class="px-4 py-3 w-80">Pilih Target OKR Mingguan</th>
                                                    <th class="px-4 py-3 w-28 text-center">Navigasi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-amber-100">
                                                @foreach($unlinkedCommitments as $unlinkedItem)
                                                    @php
                                                        $itemReport = $unlinkedItem->report;
                                                        $unitPlans = $weeklyPlansByUnit->get($itemReport->okr_unit_id, collect());
                                                        $unlinkedSearchText = strtolower($itemReport->unit->name . ' ' . $unlinkedItem->commitment . ' ' . ($unlinkedItem->measurable_target ?? ''));
                                                    @endphp
                                                    <tr 
                                                        x-show="matchesUnlinkedFilter('{{ $itemReport->okr_unit_id }}', {{ json_encode($unlinkedSearchText) }})"
                                                        class="hover:bg-amber-50/60 transition"
                                                    >
                                                        <td class="px-4 py-3 align-top font-bold text-gray-900">
                                                            <span class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 border border-indigo-200 px-2 py-1 text-[11px] font-black text-indigo-700">
                                                                {{ $itemReport->unit->name }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 align-top">
                                                            <p class="font-bold text-gray-800">{{ $itemReport->week_start->translatedFormat('d M') }} – {{ $itemReport->week_end->translatedFormat('d M Y') }}</p>
                                                            <span class="mt-1 inline-block rounded px-1.5 py-0.5 text-[10px] font-bold {{ $statusColors[$itemReport->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                                {{ $statusLabels[$itemReport->status] ?? $itemReport->status }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 align-top">
                                                            <p class="font-black text-indigo-900">Komitmen {{ $unlinkedItem->priority_order }}:</p>
                                                            <p class="font-bold text-gray-900 mt-0.5">{{ $unlinkedItem->commitment }}</p>
                                                            <p class="text-xs text-gray-500 mt-1">Target: {{ $unlinkedItem->measurable_target }}</p>
                                                        </td>
                                                        <td class="px-4 py-3 align-top">
                                                            @if($unitPlans->isNotEmpty())
                                                                <select 
                                                                    name="items[{{ $unlinkedItem->id }}][okr_plan_id]" 
                                                                    class="w-full rounded-md border-amber-300 bg-white text-xs text-gray-800 focus:border-amber-500 focus:ring-amber-500 py-1.5 font-medium"
                                                                >
                                                                    <option value="">-- Pilih Target OKR Mingguan --</option>
                                                                    @foreach($unitPlans as $plan)
                                                                        <option value="{{ $plan->id }}">
                                                                            [{{ $plan->keyResult?->code ?? 'KR' }}] {{ $plan->title }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <span class="text-xs text-red-600 font-bold italic">
                                                                    Unit ini belum memiliki target mingguan di Matriks OKR.
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 align-top text-center">
                                                            <a 
                                                                href="{{ route('okr.weekly.index', ['period_id' => $itemReport->okr_period_id, 'unit_id' => $itemReport->okr_unit_id, 'week_start' => $itemReport->week_start->format('Y-m-d')]) }}"
                                                                class="inline-flex items-center gap-1 rounded bg-white border border-gray-300 px-2 py-1 text-[11px] font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 shadow-xs transition"
                                                                title="Buka laporan pekan ini"
                                                            >
                                                                <span>Buka</span>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- 4. Footer Modal --}}
                                    <div class="unlinked-modal-footer">
                                        <div class="text-[11px] text-amber-900 leading-snug">
                                            💡 Anda dapat menautkan satu atau beberapa komitmen sekaligus. Setelah seluruh komitmen tertaut, panel urgensi ini akan hilang otomatis.
                                        </div>

                                        <div class="flex items-center gap-2.5">
                                            <button 
                                                type="button" 
                                                @click="closeUnlinkedModal()" 
                                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 cursor-pointer"
                                            >
                                                Batal
                                            </button>
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center gap-2 rounded-md bg-amber-600 hover:bg-amber-700 px-5 py-2 text-xs font-black text-white shadow-sm transition cursor-pointer"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                <span>Simpan Penautan Target OKR</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            @endif

            @if($incompleteCommitments->isNotEmpty())
                @php
                    $incompleteUnits = $incompleteCommitments->map(fn($item) => $item->report->unit)->unique('id')->values();
                @endphp
                <div 
                    x-data="{
                        statusFilter: 'all',
                        unitFilter: 'all',
                        searchQuery: '',
                        detailModal: {
                            open: false,
                            maximized: false,
                            dragging: false,
                            pos: null,
                            dragOffset: { x: 0, y: 0 }
                        },
                        progressModalOpen: false,
                        modalItemId: null,
                        modalCommitment: '',
                        modalOrder: 1,
                        modalUnit: '',
                        modalProgressPercent: 0,
                        modalStatus: 'on_progress',
                        modalBlockers: '',
                        modalActionUrl: '',

                        openDetailModal(filter = 'all') {
                            this.statusFilter = filter;
                            this.detailModal.open = true;
                            this.detailModal.maximized = false;
                            this.detailModal.pos = null;
                        },
                        closeDetailModal() {
                            this.detailModal.open = false;
                            this.detailModal.maximized = false;
                            this.detailModal.pos = null;
                        },
                        toggleDetailMaximize() {
                            this.detailModal.maximized = !this.detailModal.maximized;
                            if (this.detailModal.maximized) {
                                this.detailModal.pos = null;
                            }
                        },
                        startDetailDrag(e) {
                            if (this.detailModal.maximized) return;
                            this.detailModal.dragging = true;
                            const box = this.$refs.detailModalBox;
                            if (!box) return;
                            const rect = box.getBoundingClientRect();
                            this.detailModal.pos = { x: rect.left, y: rect.top };
                            this.detailModal.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
                        },
                        onDetailDrag(e) {
                            if (!this.detailModal.dragging) return;
                            const maxX = window.innerWidth - 120;
                            const maxY = window.innerHeight - 60;
                            this.detailModal.pos.x = Math.max(0, Math.min(e.clientX - this.detailModal.dragOffset.x, maxX));
                            this.detailModal.pos.y = Math.max(0, Math.min(e.clientY - this.detailModal.dragOffset.y, maxY));
                        },
                        stopDetailDrag() {
                            this.detailModal.dragging = false;
                        },
                        matchesFilter(unitId, isZero, isBlocked, isInProgress, text) {
                            if (this.statusFilter === 'zero' && !isZero) return false;
                            if (this.statusFilter === 'blocked' && !isBlocked) return false;
                            if (this.statusFilter === 'in_progress' && !isInProgress) return false;
                            if (this.unitFilter !== 'all' && this.unitFilter != unitId) return false;
                            if (this.searchQuery && this.searchQuery.trim() !== '') {
                                const q = this.searchQuery.toLowerCase().trim();
                                if (!text.includes(q)) return false;
                            }
                            return true;
                        },
                        openProgressModal(id, commitment, order, unit, progress, status, blockers, actionUrl) {
                            this.modalItemId = id;
                            this.modalCommitment = commitment;
                            this.modalOrder = order;
                            this.modalUnit = unit;
                            this.modalProgressPercent = progress;
                            this.modalStatus = status === 'not_started' ? 'on_progress' : status;
                            this.modalBlockers = blockers || '';
                            this.modalActionUrl = actionUrl;
                            this.progressModalOpen = true;
                        },
                        closeProgressModal() {
                            this.progressModalOpen = false;
                        },
                        onStatusChange() {
                            if (this.modalStatus === 'completed') {
                                this.modalProgressPercent = 100;
                            }
                        },
                        onProgressChange() {
                            if (parseFloat(this.modalProgressPercent) >= 100) {
                                this.modalStatus = 'completed';
                            }
                        }
                    }"
                    @mousemove.window="onDetailDrag($event)"
                    @mouseup.window="stopDetailDrag()"
                    class="rounded-lg border-2 border-indigo-200 bg-gradient-to-r from-indigo-50/70 via-white to-slate-50 p-4 sm:p-5 shadow-sm space-y-4"
                >
                    {{-- Header Kotak Monitoring --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-md shadow-indigo-600/20">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-black text-indigo-950">
                                        Monitoring Komitmen Belum Selesai dan Mandek
                                    </h3>
                                    <span class="rounded-full bg-rose-100 text-rose-700 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide border border-rose-200">
                                        {{ $incompleteStats['total'] }} Perlu Tindak Lanjut
                                    </span>
                                    <span class="rounded bg-indigo-100 text-indigo-800 px-2 py-0.5 text-[10px] font-bold">
                                        Periode: {{ $period->title }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-600 leading-relaxed max-w-3xl">
                                    Ringkasan seluruh komitmen pekanan lintas unit yang belum mencapai 100%, 0%, atau terhambat. Klik tombol untuk membuka modal interaktif selengkapnya dan melakukan update progres.
                                </p>
                            </div>
                        </div>

                        {{-- Tombol Buka Modal Interaktif --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button 
                                type="button" 
                                @click="openDetailModal('all')"
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2.5 text-xs font-black text-white shadow-md shadow-indigo-600/25 hover:shadow-lg transition cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                <span>Lihat Data Selengkapnya &amp; Aksi</span>
                                <span class="bg-indigo-800/90 text-white px-2 py-0.5 rounded-md text-[10px] font-mono font-bold">{{ $incompleteStats['total'] }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- 4 Stat Cards Ringkas & Interaktif --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-indigo-100/80">
                        <div 
                            @click="openDetailModal('all')"
                            class="p-3 bg-white rounded-lg border border-indigo-200 hover:border-indigo-400 hover:shadow-sm transition cursor-pointer group"
                            title="Klik untuk membuka data komitmen"
                        >
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider block">Total Komitmen</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-indigo-950">{{ $incompleteStats['total'] }}</span>
                                <span class="text-[10px] font-bold text-indigo-600 group-hover:translate-x-0.5 transition-transform">Buka Modal &rarr;</span>
                            </div>
                        </div>

                        <div 
                            @click="openDetailModal('zero')"
                            class="p-3 bg-white rounded-lg border border-amber-200 hover:border-amber-400 hover:shadow-sm transition cursor-pointer group"
                            title="Klik untuk memfilter komitmen 0%"
                        >
                            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Belum Ada Progres (0%)</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-amber-700">{{ $incompleteStats['zero'] }}</span>
                                <span class="text-[10px] font-bold text-amber-600 group-hover:translate-x-0.5 transition-transform">Filter &rarr;</span>
                            </div>
                        </div>

                        <div 
                            @click="openDetailModal('blocked')"
                            class="p-3 bg-white rounded-lg border border-rose-200 hover:border-rose-400 hover:shadow-sm transition cursor-pointer group"
                            title="Klik untuk memfilter komitmen terhambat"
                        >
                            <span class="text-[10px] font-bold text-rose-800 uppercase tracking-wider block">Terhambat</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-rose-700">{{ $incompleteStats['blocked'] }}</span>
                                <span class="text-[10px] font-bold text-rose-600 group-hover:translate-x-0.5 transition-transform">Filter &rarr;</span>
                            </div>
                        </div>

                        <div 
                            @click="openDetailModal('in_progress')"
                            class="p-3 bg-white rounded-lg border border-sky-200 hover:border-sky-400 hover:shadow-sm transition cursor-pointer group"
                            title="Klik untuk memfilter komitmen sedang berjalan"
                        >
                            <span class="text-[10px] font-bold text-sky-800 uppercase tracking-wider block">Sedang Berjalan</span>
                            <div class="mt-1 flex items-baseline justify-between">
                                <span class="text-xl font-black text-sky-700">{{ $incompleteStats['in_progress'] }}</span>
                                <span class="text-[10px] font-bold text-sky-600 group-hover:translate-x-0.5 transition-transform">Filter &rarr;</span>
                            </div>
                        </div>
                    </div>

                    {{-- Highlight banner jika ada komitmen terhambat --}}
                    @if($incompleteStats['blocked'] > 0)
                        <div class="flex items-center justify-between gap-3 p-3 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-900">
                            <div class="flex items-center gap-2">
                                <span class="flex h-2 w-2 rounded-full bg-rose-600 flex-shrink-0"></span>
                                <span>Perhatian: Terdapat <strong>{{ $incompleteStats['blocked'] }} komitmen</strong> yang berstatus terhambat kendala. Segera lakukan koordinasi dan evaluasi.</span>
                            </div>
                            <button 
                                type="button" 
                                @click="openDetailModal('blocked')"
                                class="inline-flex items-center gap-1 font-bold text-rose-700 hover:text-rose-900 underline flex-shrink-0 cursor-pointer text-xs"
                            >
                                Tinjau Terhambat &rarr;
                            </button>
                        </div>
                    @endif

                    {{-- ══════════════════════════════════════════════════════════════
                         MODAL INTERAKTIF: DETAIL MONITORING KOMITMEN & AKSI LENGKAP
                         (Lega, Draggable via Header, Maximizable, Hanya Ditutup via Button)
                    ══════════════════════════════════════════════════════════════ --}}
                    <template x-teleport="body">
                        <div 
                            x-show="detailModal.open" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="monitoring-modal-backdrop"
                            style="display: none;"
                        >
                            <div 
                                x-ref="detailModalBox"
                                class="monitoring-modal-box"
                                :class="{
                                    'maximized': detailModal.maximized,
                                    'positioned': !detailModal.maximized && detailModal.pos !== null
                                }"
                                :style="!detailModal.maximized && detailModal.pos !== null
                                    ? 'left:' + detailModal.pos.x + 'px; top:' + detailModal.pos.y + 'px; width:1250px; max-width:95vw;'
                                    : ''"
                                @click.stop
                            >
                                {{-- 1. Header Modal (Drag Handle) --}}
                                <div class="monitoring-modal-header"
                                    :class="!detailModal.maximized ? 'cursor-grab active:cursor-grabbing' : 'cursor-default'"
                                    @mousedown="startDetailDrag($event)"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white flex-shrink-0 shadow-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-sm font-black text-indigo-950">Monitoring Komitmen Belum Selesai &amp; Mandek</h3>
                                                <span class="rounded bg-indigo-100 text-indigo-800 px-2 py-0.5 text-[10px] font-bold">
                                                    Periode {{ $period->title }}
                                                </span>
                                                <span class="rounded-full bg-rose-100 text-rose-700 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wide border border-rose-200">
                                                    {{ $incompleteStats['total'] }} Perlu Tindak Lanjut
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-500 font-medium">Tahan dan geser header untuk memindahkan posisi modal. Gunakan tombol perbesar untuk tampilan penuh.</p>
                                        </div>
                                    </div>

                                    {{-- Control Buttons (Maximize & Close) --}}
                                    <div class="flex items-center gap-1.5 flex-shrink-0" @mousedown.stop>
                                        {{-- Maximize Button --}}
                                        <button 
                                            type="button"
                                            @click="toggleDetailMaximize()"
                                            class="w-8 h-8 rounded-md flex items-center justify-center text-gray-500 hover:bg-white/80 hover:text-gray-800 transition-colors cursor-pointer border border-transparent hover:border-gray-200"
                                            :title="detailModal.maximized ? 'Kembalikan ukuran' : 'Perbesar layar penuh'"
                                        >
                                            <svg x-show="!detailModal.maximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                            </svg>
                                            <svg x-show="detailModal.maximized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                                            </svg>
                                        </button>

                                        {{-- Close Button (X) --}}
                                        <button 
                                            type="button"
                                            @click="closeDetailModal()"
                                            class="w-8 h-8 rounded-md flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors cursor-pointer border border-transparent hover:border-red-200"
                                            title="Tutup (Esc)"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- 2. Sub-Header: Search & Filter Toolbar --}}
                                <div class="px-5 py-3 border-b border-gray-200 bg-slate-50 flex flex-wrap items-center justify-between gap-3">
                                    {{-- Search Box --}}
                                    <div class="relative flex-1 min-w-[220px] max-w-md">
                                        <input 
                                            type="text" 
                                            x-model="searchQuery"
                                            placeholder="Cari komitmen, unit kerja, target, atau kendala..." 
                                            class="w-full text-xs rounded-md border-gray-300 pl-8 pr-3 py-1.5 focus:border-indigo-500 focus:ring-indigo-500 bg-white"
                                        >
                                        <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>

                                    {{-- Status Tabs --}}
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <button 
                                            type="button" 
                                            @click="statusFilter = 'all'" 
                                            :class="statusFilter === 'all' ? 'bg-indigo-600 text-white font-black shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100 font-bold'"
                                            class="rounded-md px-2.5 py-1 text-xs transition cursor-pointer"
                                        >
                                            Semua ({{ $incompleteStats['total'] }})
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="statusFilter = 'zero'" 
                                            :class="statusFilter === 'zero' ? 'bg-amber-600 text-white font-black shadow-xs' : 'bg-white text-amber-900 border border-amber-200 hover:bg-amber-50 font-bold'"
                                            class="rounded-md px-2.5 py-1 text-xs transition cursor-pointer"
                                        >
                                            0% / Belum Ada Progres ({{ $incompleteStats['zero'] }})
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="statusFilter = 'blocked'" 
                                            :class="statusFilter === 'blocked' ? 'bg-rose-600 text-white font-black shadow-xs' : 'bg-white text-rose-900 border border-rose-200 hover:bg-rose-50 font-bold'"
                                            class="rounded-md px-2.5 py-1 text-xs transition cursor-pointer"
                                        >
                                            Terhambat ({{ $incompleteStats['blocked'] }})
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="statusFilter = 'in_progress'" 
                                            :class="statusFilter === 'in_progress' ? 'bg-sky-600 text-white font-black shadow-xs' : 'bg-white text-sky-900 border border-sky-200 hover:bg-sky-50 font-bold'"
                                            class="rounded-md px-2.5 py-1 text-xs transition cursor-pointer"
                                        >
                                            Sedang Berjalan ({{ $incompleteStats['in_progress'] }})
                                        </button>
                                    </div>

                                    {{-- Unit Filter Selector --}}
                                    @if($incompleteUnits->count() > 1)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xs font-bold text-gray-500 whitespace-nowrap">Unit:</span>
                                            <select x-model="unitFilter" class="rounded-md border-gray-300 text-xs py-1 px-2.5 bg-white font-semibold text-gray-700 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="all">Semua Unit ({{ $incompleteStats['total'] }})</option>
                                                @foreach($incompleteUnits as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- 3. Body: Interactive Scrollable Table --}}
                                <div class="monitoring-modal-body p-0">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead class="sticky top-0 bg-indigo-50/95 backdrop-blur-xs font-black text-indigo-950 uppercase tracking-wider text-[11px] border-b border-indigo-200 shadow-xs z-10">
                                            <tr>
                                                <th class="px-4 py-3 w-36">Unit Kerja</th>
                                                <th class="px-4 py-3 w-44">Pekan Laporan</th>
                                                <th class="px-4 py-3">Komitmen &amp; Target Terukur</th>
                                                <th class="px-4 py-3 w-52">Target OKR Terkait</th>
                                                <th class="px-4 py-3 w-48">Capaian &amp; Status</th>
                                                <th class="px-4 py-3 w-52">Kendala / Update Terkini</th>
                                                <th class="px-4 py-3 w-36 text-center">Aksi Cepat</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($incompleteCommitments as $incItem)
                                                @php
                                                    $incReport = $incItem->report;
                                                    $incLatestProgress = $incItem->progressUpdates->first();
                                                    $isZero = (float) $incItem->completion_percent == 0;
                                                    $isBlocked = $incItem->final_status === 'blocked';
                                                    $isInProgress = (float) $incItem->completion_percent > 0 && (float) $incItem->completion_percent < 100 && ! $isBlocked;
                                                    $canEditDraft = in_array($incReport->okr_unit_id, $editableUnitIds, true) && $incReport->status === 'draft';
                                                    $searchIndex = strtolower($incReport->unit->name . ' ' . $incItem->commitment . ' ' . ($incItem->measurable_target ?? '') . ' ' . ($incItem->blockers ?? '') . ' ' . ($incLatestProgress?->note ?? ''));
                                                @endphp
                                                <tr 
                                                    x-show="matchesFilter('{{ $incReport->okr_unit_id }}', {{ $isZero ? 'true' : 'false' }}, {{ $isBlocked ? 'true' : 'false' }}, {{ $isInProgress ? 'true' : 'false' }}, {{ json_encode($searchIndex) }})"
                                                    class="hover:bg-indigo-50/30 transition {{ $isBlocked ? 'bg-rose-50/20' : ($isZero ? 'bg-amber-50/10' : '') }}"
                                                >
                                                    <td class="px-4 py-3 align-top font-bold text-gray-900">
                                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 border border-indigo-200 px-2 py-1 text-[11px] font-black text-indigo-700">
                                                            {{ $incReport->unit->name }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        <p class="font-bold text-gray-800">{{ $incReport->week_start->translatedFormat('d M') }} – {{ $incReport->week_end->translatedFormat('d M Y') }}</p>
                                                        <span class="mt-1 inline-block rounded px-1.5 py-0.5 text-[10px] font-bold {{ $statusColors[$incReport->status] ?? 'bg-gray-100 text-gray-700' }}">
                                                            {{ $statusLabels[$incReport->status] ?? $incReport->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        <p class="font-black text-indigo-900">Komitmen {{ $incItem->priority_order }}:</p>
                                                        <p class="font-bold text-gray-900 mt-0.5">{{ $incItem->commitment }}</p>
                                                        <p class="text-xs text-gray-500 mt-1"><span class="font-semibold text-gray-600">Target:</span> {{ $incItem->measurable_target }}</p>
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        @if($incItem->plan)
                                                            <p class="font-bold text-gray-800">[{{ $incItem->plan->keyResult?->code ?? 'KR' }}] {{ $incItem->plan->title }}</p>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 rounded bg-amber-100 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-800">
                                                                Belum Tertaut Target
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-center justify-between text-xs">
                                                                <span class="font-black text-gray-900 text-sm">{{ (float) $incItem->completion_percent }}%</span>
                                                                <span class="rounded px-1.5 py-0.5 text-[9px] font-black {{ $incItem->final_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($incItem->final_status === 'blocked' ? 'bg-rose-100 text-rose-700' : ($isZero ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-700')) }}">
                                                                    {{ $itemStatusLabels[$incItem->final_status] ?? $incItem->final_status }}
                                                                </span>
                                                            </div>
                                                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 border border-gray-200">
                                                                <div class="h-full rounded-full {{ $incItem->final_status === 'blocked' ? 'bg-rose-500' : ($isZero ? 'bg-amber-400' : 'bg-gradient-to-r from-cyan-500 to-indigo-600') }}" style="width: {{ min(100, max(3, (float) $incItem->completion_percent)) }}%"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 align-top">
                                                        @if($incItem->blockers || $incLatestProgress?->blockers)
                                                            <div class="rounded bg-rose-50 border border-rose-200 p-2 text-[11px] font-semibold text-rose-800 leading-snug">
                                                                <span class="font-black uppercase text-[9px] block text-rose-700">Kendala:</span>
                                                                {{ $incItem->blockers ?: $incLatestProgress->blockers }}
                                                            </div>
                                                        @elseif($incLatestProgress?->note)
                                                            <p class="text-[11px] text-gray-700 leading-snug line-clamp-2">{{ $incLatestProgress->note }}</p>
                                                            <p class="text-[9px] text-gray-400 mt-1">{{ $incLatestProgress->recorder?->name ?? 'Pengguna' }} · {{ $incLatestProgress->recorded_at->translatedFormat('d M H:i') }}</p>
                                                        @elseif($incItem->actual_result)
                                                            <p class="text-[11px] text-gray-700 leading-snug line-clamp-2">{{ $incItem->actual_result }}</p>
                                                        @else
                                                            <span class="text-[11px] text-gray-400 italic">Belum ada pembaruan</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 align-top text-center">
                                                        <div class="flex flex-col items-center gap-1.5">
                                                            @if($canEditDraft)
                                                                <button 
                                                                    type="button"
                                                                    @click="openProgressModal(
                                                                        {{ $incItem->id }},
                                                                        @js($incItem->commitment),
                                                                        {{ $incItem->priority_order }},
                                                                        @js($incReport->unit->name),
                                                                        {{ (float) $incItem->completion_percent }},
                                                                        '{{ $incItem->final_status }}',
                                                                        @js($incItem->blockers ?? ''),
                                                                        '{{ route('okr.weekly.progress', $incReport) }}'
                                                                    )"
                                                                    class="w-full inline-flex items-center justify-center gap-1 rounded bg-indigo-600 px-2.5 py-1.5 text-[11px] font-black text-white hover:bg-indigo-700 shadow-xs transition cursor-pointer"
                                                                    title="Perbarui progres komitmen ini"
                                                                >
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                    <span>Update Progres</span>
                                                                </button>
                                                            @endif
                                                            <a 
                                                                href="{{ route('okr.weekly.index', ['period_id' => $incReport->okr_period_id, 'unit_id' => $incReport->okr_unit_id, 'week_start' => $incReport->week_start->format('Y-m-d')]) }}"
                                                                class="w-full inline-flex items-center justify-center gap-1 rounded bg-white border border-gray-300 px-2 py-1 text-[11px] font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 shadow-xs transition"
                                                                title="Buka halaman laporan pekan ini"
                                                            >
                                                                <span>Buka Laporan</span>
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- 4. Footer Modal --}}
                                <div class="monitoring-modal-footer">
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Gunakan tombol <strong>Update Progres</strong> untuk memperbarui langsung atau <strong>Buka Laporan</strong> untuk membuka pekan terkait.</span>
                                    </div>

                                    <button 
                                        type="button" 
                                        @click="closeDetailModal()"
                                        class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-md text-xs font-bold transition shadow-xs cursor-pointer"
                                    >
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- ══════════════════════════════════════════════════════════════
                         SUB-MODAL: PERBARUI PROGRES CEPAT (Z-Index Tinggi di atas modal detail)
                    ══════════════════════════════════════════════════════════════ --}}
                    @if(!empty($editableUnitIds))
                        <template x-teleport="body">
                            <div 
                                x-show="progressModalOpen" 
                                x-cloak
                                class="fixed inset-0 z-[100001] overflow-y-auto"
                                style="display: none;"
                            >
                                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>
                                <div class="flex min-h-full items-center justify-center p-4">
                                    <div 
                                        class="relative w-full max-w-lg rounded-lg bg-white p-6 shadow-2xl transition-all border border-gray-200"
                                        @click.stop
                                    >
                                        <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="rounded bg-indigo-50 border border-indigo-200 px-2 py-0.5 text-[10px] font-black text-indigo-700" x-text="modalUnit"></span>
                                                    <span class="text-xs font-black text-gray-500" x-text="'Komitmen #' + modalOrder"></span>
                                                </div>
                                                <h3 class="mt-1.5 text-base font-black text-gray-900" x-text="modalCommitment"></h3>
                                            </div>
                                            <button type="button" @click="closeProgressModal()" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>

                                        <form method="POST" x-bind:action="modalActionUrl" enctype="multipart/form-data" class="mt-5 space-y-4">
                                            @csrf
                                            <input type="hidden" name="item_id" x-bind:value="modalItemId">

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="mb-1 block text-xs font-bold text-gray-700">Progres (%)</label>
                                                    <input 
                                                        type="number" 
                                                        name="progress_percent" 
                                                        min="0" 
                                                        max="100" 
                                                        step="1" 
                                                        x-model="modalProgressPercent" 
                                                        @input="onProgressChange()"
                                                        required 
                                                        class="w-full rounded-md border-gray-300 text-sm font-bold focus:border-indigo-500 focus:ring-indigo-500"
                                                    >
                                                </div>
                                                <div>
                                                    <label class="mb-1 block text-xs font-bold text-gray-700">Status Capaian</label>
                                                    <select 
                                                        name="status" 
                                                        x-model="modalStatus" 
                                                        @change="onStatusChange()"
                                                        class="w-full rounded-md border-gray-300 text-sm font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                                                    >
                                                        <option value="not_started">Belum dimulai</option>
                                                        <option value="on_progress">Berjalan</option>
                                                        <option value="completed">Tercapai (100%)</option>
                                                        <option value="blocked">Terhambat</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-gray-700">
                                                    Pembaruan Pekerjaan <span class="text-rose-500">*</span>
                                                </label>
                                                <textarea 
                                                    name="note" 
                                                    rows="3" 
                                                    required 
                                                    placeholder="Apa saja tindakan atau kemajuan yang sudah dicapai?" 
                                                    class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                ></textarea>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-gray-700">
                                                    Kendala / Hambatan <span class="text-gray-400 font-normal">(opsional)</span>
                                                </label>
                                                <textarea 
                                                    name="blockers" 
                                                    rows="2" 
                                                    x-model="modalBlockers"
                                                    placeholder="Tuliskan jika ada faktor penghambat atau kebutuhan koordinasi..." 
                                                    class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                ></textarea>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-gray-700">
                                                    Unggah Bukti Progres <span class="text-gray-400 font-normal">(opsional)</span>
                                                </label>
                                                <input 
                                                    type="file" 
                                                    name="evidence" 
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" 
                                                    class="w-full rounded-md border border-gray-300 bg-gray-50 p-2 text-xs text-gray-600 focus:outline-none"
                                                >
                                            </div>

                                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                                <button 
                                                    type="button" 
                                                    @click="closeProgressModal()" 
                                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 cursor-pointer"
                                                >
                                                    Batal
                                                </button>
                                                <button 
                                                    type="submit" 
                                                    class="rounded-md bg-indigo-600 px-5 py-2 text-xs font-black text-white hover:bg-indigo-700 shadow-sm cursor-pointer"
                                                >
                                                    Simpan Progres Komitmen
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    @endif
                </div>
            @endif

            <section class="border-y border-gray-200 bg-white px-5 py-4">
                <form method="GET" class="grid gap-3 lg:grid-cols-[minmax(220px,1fr)_minmax(200px,1fr)_170px_auto_auto_auto] lg:items-end">
                    <label><span class="mb-1.5 block text-[11px] font-black uppercase text-gray-500">Periode OKR</span><select name="period_id" class="w-full rounded-md border-gray-300 text-sm">@foreach($periods as $periodOption)<option value="{{ $periodOption->id }}" @selected($periodOption->id === $period->id)>{{ $periodOption->title }}</option>@endforeach</select></label>
                    <label><span class="mb-1.5 block text-[11px] font-black uppercase text-gray-500">Unit Kerja</span><select name="unit_id" class="w-full rounded-md border-gray-300 text-sm">@foreach($units as $unit)<option value="{{ $unit->id }}" @selected($unit->id === $selectedUnit->id)>{{ $unit->name }}{{ in_array($unit->id, $editableUnitIds, true) ? ' · dapat dikelola' : '' }}</option>@endforeach</select></label>
                    <label><span class="mb-1.5 block text-[11px] font-black uppercase text-gray-500">Pekan</span><input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 text-sm"></label>
                    <button class="h-[42px] rounded-md bg-indigo-600 px-4 text-sm font-bold text-white hover:bg-indigo-500">Tampilkan</button>
                    <a href="{{ route('okr.weekly.index', ['period_id' => $period->id, 'unit_id' => $selectedUnit->id, 'week_start' => $previousWeek]) }}" class="inline-flex h-[42px] items-center justify-center rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700">Pekan lalu</a>
                    <a href="{{ route('okr.weekly.index', ['period_id' => $period->id, 'unit_id' => $selectedUnit->id, 'week_start' => $nextWeek]) }}" class="inline-flex h-[42px] items-center justify-center rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700">Pekan berikutnya</a>
                </form>
                <p class="mt-3 text-xs font-semibold text-gray-500">Periode laporan: {{ $weekStart->translatedFormat('d M Y') }} – {{ $weekEnd->translatedFormat('d M Y') }}</p>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                @foreach([
                    ['label' => 'Unit Melapor', 'value' => $stats['reported_units'].'/'.$stats['expected_units'], 'tone' => 'text-indigo-700'],
                    ['label' => 'Rata-rata Capaian', 'value' => $stats['completion'].'%', 'tone' => 'text-gray-900'],
                    ['label' => 'Target Tercapai', 'value' => $stats['completed'], 'tone' => 'text-emerald-700'],
                    ['label' => 'Target Terhambat', 'value' => $stats['blocked'], 'tone' => 'text-red-700'],
                    ['label' => 'Belum Melapor', 'value' => max(0, $stats['expected_units'] - $stats['reported_units']), 'tone' => 'text-amber-700'],
                ] as $card)
                    <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-black uppercase text-gray-400">{{ $card['label'] }}</p><p class="mt-2 text-3xl font-black {{ $card['tone'] }}">{{ $card['value'] }}</p></div>
                @endforeach
            </section>

            <section class="grid gap-5 xl:grid-cols-2">
                <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm"><h3 class="font-black text-gray-900">Capaian Pekanan per Unit</h3><p class="mb-4 mt-1 text-xs text-gray-500">Rata-rata realisasi komitmen pada pekan terpilih.</p><div class="h-72"><canvas x-ref="unitChart"></canvas></div></div>
                <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm"><h3 class="font-black text-gray-900">Tren Capaian 8 Pekan</h3><p class="mb-4 mt-1 text-xs text-gray-500">Perubahan rata-rata capaian laporan mingguan.</p><div class="h-72"><canvas x-ref="trendChart"></canvas></div></div>
            </section>

            @if($canReview)
                <section class="rounded-md border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 px-5 py-4"><h3 class="font-black text-gray-900">Monitoring Pelaporan Seluruh Unit</h3><p class="mt-1 text-xs text-gray-500">Klik unit untuk membuka detail komitmen dan evaluasinya.</p></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-[10px] uppercase text-gray-500"><tr><th class="px-5 py-3 text-left">Unit</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Capaian</th><th class="px-5 py-3 text-right">Tercapai</th><th class="px-5 py-3 text-right">Terhambat</th><th class="px-5 py-3 text-left">Pelapor</th><th class="px-5 py-3"></th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($unitSummaries as $summary)
                                    <tr class="{{ $summary['id'] === $selectedUnit->id ? 'bg-indigo-50/50' : '' }}">
                                        <td class="px-5 py-3 font-bold text-gray-900">{{ $summary['name'] }}</td>
                                        <td class="px-5 py-3"><span class="rounded-full px-2 py-1 text-[10px] font-black {{ $statusColors[$summary['status']] }}">{{ $statusLabels[$summary['status']] }}</span></td>
                                        <td class="px-5 py-3 text-right font-black">{{ $summary['completion'] }}%</td><td class="px-5 py-3 text-right text-emerald-700">{{ $summary['completed'] }}</td><td class="px-5 py-3 text-right text-red-700">{{ $summary['blocked'] }}</td>
                                        <td class="px-5 py-3 text-xs text-gray-500">{{ $summary['submitter'] ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right"><a href="{{ route('okr.weekly.index', ['period_id' => $period->id, 'unit_id' => $summary['id'], 'week_start' => $weekStart->format('Y-m-d')]) }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-5 py-4">
                    <div><h3 class="font-black text-gray-900">Laporan {{ $selectedUnit->name }}</h3><p class="mt-1 text-xs text-gray-500">Pekan {{ $weekStart->translatedFormat('d M') }}–{{ $weekEnd->translatedFormat('d M Y') }}</p></div>
                    <div class="flex items-center gap-2">
                        @if($report && ($canEditSelected || $canReview))
                            <a href="{{ route('okr.weekly.pdf', $report) }}" class="inline-flex items-center gap-2 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-100">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0-3-3m3 3 3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Unduh PDF Arsip
                            </a>
                        @endif
                        <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $statusColors[$report?->status ?? 'not_reported'] }}">{{ $statusLabels[$report?->status ?? 'not_reported'] }}</span>
                    </div>
                </div>

                @if($canEditSelected && (!$report || $report->status === 'draft'))
                    @if(!$report && $carryForwardCount > 0)
                        <div class="border-b border-blue-200 bg-blue-50 px-5 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div><p class="text-sm font-black text-blue-950">Ada {{ $carryForwardCount }} komitmen pekan lalu yang belum selesai</p><p class="mt-1 text-xs text-blue-700">Salin tindak lanjutnya sebagai draft pekan ini, lalu sesuaikan target sebelum disimpan.</p></div>
                                <form method="POST" action="{{ route('okr.weekly.copy-previous') }}" x-data="{ copying: false }" @submit="if(copying) { $event.preventDefault(); return false; } copying = true;">
                                    @csrf
                                    <input type="hidden" name="okr_period_id" value="{{ $period->id }}">
                                    <input type="hidden" name="okr_unit_id" value="{{ $selectedUnit->id }}">
                                    <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                                    <button type="submit" :disabled="copying" class="inline-flex items-center gap-2 rounded-md bg-blue-700 px-4 py-2.5 text-xs font-black text-white hover:bg-blue-600 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <svg x-show="copying" class="h-3.5 w-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                        </svg>
                                        <span x-text="copying ? 'Menyalin...' : 'Salin yang Belum Selesai'">Salin yang Belum Selesai</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('okr.weekly.planning') }}" class="space-y-5 p-5" x-data="{ submitting: false }" @submit="if(submitting) { $event.preventDefault(); return false; } submitting = true;">
                        @csrf
                        <input type="hidden" name="okr_period_id" value="{{ $period->id }}"><input type="hidden" name="okr_unit_id" value="{{ $selectedUnit->id }}"><input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                        <div class="border-l-4 border-blue-500 bg-blue-50 px-4 py-3"><p class="text-sm font-black text-blue-900">Rencana Senin</p><p class="mt-1 text-xs text-blue-700">Tentukan fokus dan komitmen target yang harus selesai atau bergerak signifikan minggu ini.</p></div>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Fokus OKR pekan ini</span><textarea name="weekly_focus" rows="3" required class="w-full rounded-md border-gray-300 text-sm">{{ old('weekly_focus', $report?->weekly_focus) }}</textarea></label>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Dukungan yang dibutuhkan</span><textarea name="support_needed" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old('support_needed', $report?->support_needed) }}</textarea></label>

                        <div class="space-y-4" id="planning-items-container">
                            @foreach($planningItems as $index => $planningItem)
                                <div class="planning-item rounded-md border border-gray-200 bg-gray-50 p-4" data-item-index="{{ $index }}">
                                    <div class="mb-3 flex items-center justify-between">
                                        <p class="commitment-title text-xs font-black uppercase text-gray-500">Komitmen {{ $index + 1 }}{{ $index === 0 ? ' · wajib' : ' · opsional' }}</p>
                                        <button
                                            type="button"
                                            class="remove-commitment-btn inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition {{ $index === 0 ? 'hidden' : '' }}"
                                            title="Hapus komitmen ini"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                    <div class="grid gap-4 lg:grid-cols-2">
                                        <div class="lg:col-span-2">
                                            @include('pages.okr.weekly._plan-picker', [
                                                'fieldName' => "items[$index][okr_plan_id]",
                                                'selectedPlanId' => old("items.$index.okr_plan_id", $planningItem?->okr_plan_id),
                                            ])
                                        </div>
                                        <label><span class="mb-1 block text-xs font-bold">Komitmen target</span><textarea name="items[{{ $index }}][commitment]" rows="3" @required($index === 0) class="commitment-input w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.commitment", $planningItem?->commitment) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Target terukur / batas waktu</span><textarea name="items[{{ $index }}][measurable_target]" rows="3" @required($index === 0) class="target-input w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.measurable_target", $planningItem?->measurable_target) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Ketergantungan lintas unit</span><textarea name="items[{{ $index }}][cross_unit_dependencies]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.cross_unit_dependencies", $planningItem?->cross_unit_dependencies) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Anggaran/logistik yang membutuhkan persetujuan</span><textarea name="items[{{ $index }}][approval_needs]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.approval_needs", $planningItem?->approval_needs) }}</textarea></label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 pt-3">
                            <button
                                type="button"
                                id="btn-add-commitment"
                                class="inline-flex items-center gap-2 rounded-md border-2 border-dashed border-blue-300 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-700 hover:border-blue-500 hover:bg-blue-100 hover:text-blue-800 transition"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Komitmen</span>
                            </button>
                            <span class="text-xs text-gray-500">Minimal 1 komitmen wajib. Klik &quot;Tambah Komitmen&quot; jika unit memerlukan lebih dari 3 komitmen.</span>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" :disabled="submitting" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-500 disabled:opacity-60 disabled:cursor-not-allowed">
                                <svg x-show="submitting" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Menyimpan...' : 'Simpan Rencana Senin'">Simpan Rencana Senin</span>
                            </button>
                        </div>
                    </form>

                    <template id="commitment-item-template">
                        <div class="planning-item rounded-md border border-gray-200 bg-gray-50 p-4" data-item-index="__INDEX__">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="commitment-title text-xs font-black uppercase text-gray-500">Komitmen __ORDER__ · opsional</p>
                                <button
                                    type="button"
                                    class="remove-commitment-btn inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition"
                                    title="Hapus komitmen ini"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                            <div class="grid gap-4 lg:grid-cols-2">
                                <div class="lg:col-span-2">
                                    @include('pages.okr.weekly._plan-picker', [
                                        'fieldName' => 'items[__INDEX__][okr_plan_id]',
                                        'selectedPlanId' => null,
                                    ])
                                </div>
                                <label><span class="mb-1 block text-xs font-bold">Komitmen target</span><textarea name="items[__INDEX__][commitment]" rows="3" class="commitment-input w-full rounded-md border-gray-300 text-sm"></textarea></label>
                                <label><span class="mb-1 block text-xs font-bold">Target terukur / batas waktu</span><textarea name="items[__INDEX__][measurable_target]" rows="3" class="target-input w-full rounded-md border-gray-300 text-sm"></textarea></label>
                                <label><span class="mb-1 block text-xs font-bold">Ketergantungan lintas unit</span><textarea name="items[__INDEX__][cross_unit_dependencies]" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea></label>
                                <label><span class="mb-1 block text-xs font-bold">Anggaran/logistik yang membutuhkan persetujuan</span><textarea name="items[__INDEX__][approval_needs]" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea></label>
                            </div>
                        </div>
                    </template>
                @elseif(!$report)
                    <div class="px-5 py-12 text-center text-sm text-gray-500">Unit ini belum membuat rencana pekanan.</div>
                @endif

                @if($report)
                    <section class="border-t border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-5" data-weekly-plan-resume>
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-black uppercase tracking-[0.16em] text-blue-600">Bahan Presentasi Rapat Pekanan</p>
                                <h4 class="mt-1 text-lg font-black text-gray-950">Resume Rencana & Komitmen</h4>
                                <p class="mt-1 text-xs text-gray-600">Ringkasan rencana {{ $selectedUnit->name }} untuk pekan {{ $weekStart->translatedFormat('d M') }}–{{ $weekEnd->translatedFormat('d M Y') }}.</p>
                            </div>
                            <span class="rounded-full border border-blue-200 bg-white px-3 py-1.5 text-[10px] font-black uppercase text-blue-700">{{ $report->items->count() }} komitmen utama</span>
                        </div>

                        <div class="mt-4 grid gap-3 lg:grid-cols-3">
                            <div class="rounded-md border border-blue-100 bg-white p-4 lg:col-span-2">
                                <p class="text-[10px] font-black uppercase tracking-wide text-gray-400">Fokus pekan ini</p>
                                <p class="mt-2 text-sm font-bold leading-6 text-gray-900">{{ $report->weekly_focus }}</p>
                            </div>
                            <div class="rounded-md border border-amber-100 bg-amber-50 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wide text-amber-600">Dukungan dibutuhkan</p>
                                <p class="mt-2 text-sm leading-6 text-amber-950">{{ $report->support_needed ?: 'Tidak ada dukungan khusus yang diajukan.' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 xl:grid-cols-3">
                            @foreach($report->items->sortBy('priority_order') as $item)
                                @php
                                    $weeklyTarget = $item->plan;
                                    $monthlyTarget = $weeklyTarget?->level === 'weekly' && $weeklyTarget?->parent?->level === 'monthly' ? $weeklyTarget->parent : null;
                                    $annualTarget = $monthlyTarget?->parent?->level === 'annual' ? $monthlyTarget->parent : null;
                                @endphp
                                <article class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-600 text-xs font-black text-white">{{ $item->priority_order }}</span>
                                        @if($weeklyTarget)<span class="rounded-full bg-emerald-50 px-2 py-1 text-[9px] font-black uppercase text-emerald-700">OKR Mingguan</span>@endif
                                    </div>
                                    <p class="mt-3 text-sm font-black leading-5 text-gray-950">{{ $item->commitment }}</p>
                                    <div class="mt-3 rounded bg-gray-50 p-3"><p class="text-[9px] font-black uppercase text-gray-400">Target terukur</p><p class="mt-1 text-xs font-semibold leading-5 text-gray-700">{{ $item->measurable_target }}</p></div>
                                    @if($weeklyTarget)
                                        <div class="mt-3 space-y-2 border-l-2 border-indigo-200 pl-3 text-xs">
                                            <div><span class="font-black text-emerald-700">Mingguan</span><p class="mt-0.5 text-gray-700">{{ $weeklyTarget->title }}</p></div>
                                            @if($monthlyTarget)<div><span class="font-black text-blue-700">Bulanan</span><p class="mt-0.5 text-gray-700">{{ $monthlyTarget->title }}</p></div>@endif
                                            @if($annualTarget)<div><span class="font-black text-indigo-700">Tahunan</span><p class="mt-0.5 text-gray-700">{{ $annualTarget->title }}</p></div>@endif
                                        </div>
                                        @if($weeklyTarget->keyResult)
                                            <div class="mt-3 rounded bg-indigo-50 p-3 text-[10px] leading-4 text-indigo-950">
                                                <p><span class="font-black">Key Result:</span> {{ $weeklyTarget->keyResult->code }} · {{ $weeklyTarget->keyResult->title }}</p>
                                                @if($weeklyTarget->keyResult->objective)<p class="mt-1"><span class="font-black">Objektif:</span> {{ $weeklyTarget->keyResult->objective->code }} · {{ $weeklyTarget->keyResult->objective->title }}</p>@endif
                                            </div>
                                        @endif
                                    @else
                                        <p class="mt-3 text-[10px] font-semibold text-amber-700">Komitmen ini belum dikaitkan dengan target OKR mingguan.</p>
                                    @endif
                                    @if($item->cross_unit_dependencies)<p class="mt-3 text-[10px] text-gray-600"><span class="font-black">Kolaborasi:</span> {{ $item->cross_unit_dependencies }}</p>@endif
                                    @if($item->approval_needs)<p class="mt-1 text-[10px] text-gray-600"><span class="font-black">Persetujuan:</span> {{ $item->approval_needs }}</p>@endif
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section class="border-t border-cyan-200 bg-gradient-to-br from-cyan-50 via-white to-indigo-50 p-5 text-gray-900" data-weekly-progress-monitor>
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-cyan-700">Monitoring Langsung</p>
                                <h4 class="mt-1 text-lg font-black">Progres Pekan Berjalan</h4>
                                <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-600">Unit memperbarui progres setiap komitmen selama pekan berjalan. Kepala Sekolah dapat memantau capaian, kendala, bukti, dan waktu pembaruan terbaru dari sini.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-md border border-cyan-100 bg-white px-4 py-2 shadow-sm"><p class="text-[9px] font-black uppercase text-slate-400">Rata-rata</p><p class="mt-1 text-xl font-black text-cyan-700">{{ $reportProgress }}%</p></div>
                                <div class="rounded-md border border-emerald-100 bg-white px-4 py-2 shadow-sm"><p class="text-[9px] font-black uppercase text-slate-400">Selesai</p><p class="mt-1 text-xl font-black text-emerald-700">{{ $reportCompleted }}</p></div>
                                <div class="rounded-md border border-rose-100 bg-white px-4 py-2 shadow-sm"><p class="text-[9px] font-black uppercase text-slate-400">Terhambat</p><p class="mt-1 text-xl font-black text-rose-700">{{ $reportBlocked }}</p></div>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4 xl:grid-cols-3">
                            @foreach($report->items as $item)
                                @php($latestProgress = $item->progressUpdates->first())
                                <article class="overflow-hidden rounded-lg border border-cyan-100 bg-white text-gray-900 shadow-sm">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div><p class="text-[10px] font-black uppercase text-indigo-600">Komitmen {{ $item->priority_order }}</p><p class="mt-1 text-sm font-black leading-5">{{ $item->commitment }}</p></div>
                                            <span class="rounded-full px-2 py-1 text-[9px] font-black {{ $item->final_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($item->final_status === 'blocked' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">{{ $itemStatusLabels[$item->final_status] }}</span>
                                        </div>
                                        <div class="mt-4 flex items-end justify-between"><span class="text-[10px] font-bold text-gray-500">Capaian terkini</span><span class="text-2xl font-black text-gray-950">{{ (float) $item->completion_percent }}%</span></div>
                                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100"><div class="h-full rounded-full {{ $item->final_status === 'blocked' ? 'bg-red-500' : 'bg-gradient-to-r from-cyan-500 to-indigo-600' }}" style="width: {{ min(100, (float) $item->completion_percent) }}%"></div></div>
                                        @if($latestProgress)
                                            <div class="mt-4 rounded-md bg-gray-50 p-3 text-xs leading-5">
                                                <p class="font-semibold text-gray-800">{{ $latestProgress->note }}</p>
                                                @if($latestProgress->blockers)<p class="mt-2 font-bold text-red-700">Kendala: {{ $latestProgress->blockers }}</p>@endif
                                                <p class="mt-2 text-[10px] text-gray-500">{{ $latestProgress->recorder?->name ?? 'Pengguna' }} · {{ $latestProgress->recorded_at->translatedFormat('d M Y H:i') }}</p>
                                                @if($latestProgress->evidence_path)<a href="{{ asset('storage/'.$latestProgress->evidence_path) }}" target="_blank" class="mt-2 inline-block font-black text-indigo-600">Lihat bukti progres</a>@endif
                                            </div>
                                        @else
                                            <p class="mt-4 rounded-md bg-amber-50 p-3 text-xs font-semibold text-amber-800">Belum ada pembaruan progres pada pekan ini.</p>
                                        @endif
                                    </div>

                                    @if($canEditSelected && $report->status === 'draft')
                                        <form method="POST" action="{{ route('okr.weekly.progress', $report) }}" enctype="multipart/form-data" class="space-y-3 border-t border-gray-200 bg-gray-50 p-4">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <div class="grid grid-cols-2 gap-3">
                                                <label><span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Progres (%)</span><input type="number" name="progress_percent" min="0" max="100" step="1" value="{{ (float) $item->completion_percent }}" required class="w-full rounded-md border-gray-300 text-xs"></label>
                                                <label><span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Status</span><select name="status" class="w-full rounded-md border-gray-300 text-xs">@foreach($itemStatusLabels as $value => $label)<option value="{{ $value }}" @selected($item->final_status === $value)>{{ $label }}</option>@endforeach</select></label>
                                            </div>
                                            <label class="block"><span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Pembaruan pekerjaan</span><textarea name="note" rows="2" required placeholder="Apa yang sudah dikerjakan atau dicapai?" class="w-full rounded-md border-gray-300 text-xs"></textarea></label>
                                            <label class="block"><span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Kendala <span class="normal-case font-normal">(opsional)</span></span><textarea name="blockers" rows="2" class="w-full rounded-md border-gray-300 text-xs"></textarea></label>
                                            <label class="block"><span class="mb-1 block text-[10px] font-black uppercase text-gray-500">Bukti <span class="normal-case font-normal">(opsional)</span></span><input type="file" name="evidence" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="w-full rounded-md border border-gray-300 bg-white p-2 text-[10px]"></label>
                                            <button class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-xs font-black text-white hover:bg-indigo-500">Simpan Update Progres</button>
                                        </form>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>

                    @include('pages.okr.weekly._friday-resume', [
                        'report' => $report,
                        'selectedUnit' => $selectedUnit,
                        'weekStart' => $weekStart,
                        'weekEnd' => $weekEnd,
                        'itemStatusLabels' => $itemStatusLabels,
                        'reportProgress' => $reportProgress,
                        'reportCompleted' => $reportCompleted,
                        'reportBlocked' => $reportBlocked,
                    ])

                    @if($canEditSelected && $report->status !== 'reviewed')
                        <form method="POST" action="{{ route('okr.weekly.evaluation', $report) }}" enctype="multipart/form-data" class="space-y-5 border-t border-gray-200 p-5">
                            @csrf
                            <div class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3"><p class="text-sm font-black text-emerald-900">Evaluasi Jumat</p><p class="mt-1 text-xs text-emerald-700">Bandingkan hasil aktual terhadap komitmen Senin dan isi berdasarkan data atau bukti.</p></div>
                            @foreach($report->items as $item)
                                <div class="rounded-md border border-gray-200 p-4">
                                    <p class="text-xs font-black text-indigo-700">Komitmen {{ $item->priority_order }}</p><p class="mt-1 text-sm font-bold text-gray-900">{{ $item->commitment }}</p><p class="mt-1 text-xs text-gray-500">Target: {{ $item->measurable_target }}</p>
                                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                        <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Capaian aktual</span><textarea name="items[{{ $item->id }}][actual_result]" rows="3" required class="w-full rounded-md border-gray-300 text-sm">{{ old("items.{$item->id}.actual_result", $item->actual_result ?: $item->progressUpdates->first()?->note) }}</textarea><span class="mt-1 block text-[10px] text-gray-500">Otomatis mengambil pembaruan progres terakhir dan tetap dapat disesuaikan.</span></label>
                                        <label><span class="mb-1 block text-xs font-bold">Status akhir</span><select name="items[{{ $item->id }}][final_status]" class="w-full rounded-md border-gray-300 text-sm">@foreach($itemStatusLabels as $value => $label)<option value="{{ $value }}" @selected(old("items.{$item->id}.final_status", $item->final_status) === $value)>{{ $label }}</option>@endforeach</select></label>
                                        <label><span class="mb-1 block text-xs font-bold">Capaian (%)</span><input type="number" name="items[{{ $item->id }}][completion_percent]" value="{{ old("items.{$item->id}.completion_percent", (float) $item->completion_percent) }}" min="0" max="100" step="1" required class="w-full rounded-md border-gray-300 text-sm"></label>
                                        <label><span class="mb-1 block text-xs font-bold">Kendala</span><textarea name="items[{{ $item->id }}][blockers]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.{$item->id}.blockers", $item->blockers) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Tindak lanjut Senin depan</span><textarea name="items[{{ $item->id }}][next_follow_up]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.{$item->id}.next_follow_up", $item->next_follow_up) }}</textarea></label>
                                        <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Bukti pendukung</span><input type="file" name="items[{{ $item->id }}][evidence]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="w-full rounded-md border border-gray-300 bg-white p-2 text-xs">@if($item->evidence_path)<a href="{{ asset('storage/'.$item->evidence_path) }}" target="_blank" class="mt-1 block text-xs font-bold text-indigo-600">Lihat bukti sebelumnya</a>@endif</label>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex justify-end"><button class="rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-500">Kirim Evaluasi Jumat</button></div>
                        </form>
                    @else
                        <div class="space-y-4 border-t border-gray-200 p-5">
                            @foreach($report->items as $item)
                                <div class="rounded-md border border-gray-200 p-4"><div class="flex flex-wrap justify-between gap-2"><div><p class="text-xs font-black text-indigo-700">Komitmen {{ $item->priority_order }}</p><p class="mt-1 text-sm font-bold text-gray-900">{{ $item->commitment }}</p></div><div class="text-right"><span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-black text-gray-700">{{ $itemStatusLabels[$item->final_status] }}</span><p class="mt-2 text-xl font-black text-gray-900">{{ (float) $item->completion_percent }}%</p></div></div><p class="mt-3 text-xs text-gray-500">Capaian aktual</p><p class="mt-1 text-sm text-gray-800">{{ $item->actual_result ?? 'Belum dievaluasi' }}</p>@if($item->blockers)<p class="mt-3 text-xs font-bold text-red-600">Kendala: {{ $item->blockers }}</p>@endif @if($item->next_follow_up)<p class="mt-2 text-xs font-bold text-blue-700">Tindak lanjut: {{ $item->next_follow_up }}</p>@endif</div>
                            @endforeach
                        </div>
                    @endif

                    @if($canReview && $report->status === 'submitted')
                        <form method="POST" action="{{ route('okr.weekly.review', $report) }}" class="border-t border-amber-200 bg-amber-50/70 p-5 space-y-3">
                            @csrf
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                    <span class="block text-xs font-black text-amber-950 uppercase tracking-wider">Catatan & Arahan Kepala Sekolah</span>
                                    <span class="text-[11px] font-semibold text-amber-700">Tersedia formatting teks (bold, nomor 1, 2, 3, poin, heading)</span>
                                </div>
                                <div class="rounded-lg shadow-sm">
                                    <div id="review-notes-quill" class="min-h-[130px] text-sm text-gray-800"></div>
                                </div>
                                <input type="hidden" name="review_notes" id="review_notes_input" value="{{ old('review_notes', $report->review_notes) }}">
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                <p class="text-xs text-amber-800">Menandai laporan akan mengunci evaluasi pekan ini dan membuka pembaruan progres OKR bagi unit terkait.</p>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-gray-800 shadow-sm transition">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Tandai Sudah Ditinjau</span>
                                </button>
                            </div>
                        </form>
                    @elseif($report->status === 'reviewed')
                        <div class="border-t border-emerald-200 bg-emerald-50/80 px-5 py-4" x-data="{ editing: false }">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-xs font-black text-emerald-900">
                                    Ditinjau oleh {{ $report->reviewer?->name ?? 'Kepala Sekolah' }} · {{ $report->reviewed_at?->translatedFormat('d M Y H:i') }}
                                </p>
                                @if($canReview)
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('okr.weekly.unreview', $report) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan peninjauan laporan ini? Status laporan akan kembali menjadi menunggu tinjauan.')" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                x-show="!editing" 
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-rose-300 bg-white px-2.5 py-1 text-xs font-bold text-rose-700 shadow-sm hover:bg-rose-50 hover:border-rose-400 transition"
                                                title="Batalkan status peninjauan dan kembalikan ke menunggu tinjauan"
                                            >
                                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>Batalkan Peninjauan</span>
                                            </button>
                                        </form>

                                        <button 
                                            type="button" 
                                            x-show="!editing" 
                                            @click="editing = true; $nextTick(() => initQuillEditor('review-notes-quill-edit', 'review_notes_input_edit'))"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-white px-2.5 py-1 text-xs font-bold text-emerald-800 shadow-sm hover:bg-emerald-100 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit Catatan</span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div x-show="!editing" class="mt-2.5">
                                @if($report->review_notes)
                                    <div class="okr-review-content rounded-xl border border-emerald-200/90 bg-white/90 p-4 text-sm text-emerald-950 shadow-sm">
                                        {!! $report->formatted_review_notes !!}
                                    </div>
                                @else
                                    <p class="text-xs italic text-emerald-700">Tidak ada catatan tertulis dari Kepala Sekolah.</p>
                                @endif
                            </div>

                            @if($canReview)
                                <div x-show="editing" x-cloak class="mt-3 space-y-3">
                                    <form method="POST" action="{{ route('okr.weekly.review', $report) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="mb-1.5 block text-xs font-bold text-emerald-950">Sunting Catatan & Arahan Kepala Sekolah</label>
                                            <div class="rounded-lg shadow-sm">
                                                <div id="review-notes-quill-edit" class="min-h-[130px] text-sm text-gray-800"></div>
                                            </div>
                                            <input type="hidden" name="review_notes" id="review_notes_input_edit" value="{{ old('review_notes', $report->review_notes) }}">
                                        </div>
                                        <div class="flex items-center justify-end gap-2">
                                            <button 
                                                type="button" 
                                                @click="editing = false" 
                                                class="rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 transition"
                                            >
                                                Batal
                                            </button>
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-800 shadow-sm transition"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>Simpan Perubahan Catatan</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($report->status === 'reviewed' && $progressRecommendations->isNotEmpty())
                        @if($canEditSelected)
                            <form method="POST" action="{{ route('okr.weekly.apply-progress', $report) }}" enctype="multipart/form-data" class="space-y-5 border-t border-indigo-200 bg-indigo-50/60 p-5">
                                @csrf
                                <div class="border-l-4 border-indigo-600 pl-4"><p class="text-sm font-black text-indigo-950">Perbarui Progres OKR dari Laporan Ini</p><p class="mt-1 text-xs leading-5 text-indigo-700">Sistem menghitung rekomendasi berdasarkan capaian pekan dan panjang periode target. Periksa angkanya, tambahkan catatan atau bukti, kemudian konfirmasi.</p></div>
                                <label class="block max-w-xs"><span class="mb-1 block text-xs font-bold text-gray-700">Tanggal pencatatan progres</span><input type="date" name="recorded_at" value="{{ old('recorded_at', $report->week_end->format('Y-m-d')) }}" required class="w-full rounded-md border-gray-300 text-sm"></label>
                                @foreach($progressRecommendations as $recommendation)
                                    @php($plan = $recommendation['plan'])
                                    <div class="rounded-md border border-indigo-200 bg-white p-4">
                                        <p class="text-xs font-black uppercase text-indigo-600">{{ strtoupper($plan->level) }} · {{ $recommendation['item_count'] }} komitmen terkait</p>
                                        <p class="mt-1 text-sm font-black text-gray-900">{{ $plan->title }}</p>
                                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                            <div class="rounded bg-gray-50 p-3"><p class="text-[10px] font-black uppercase text-gray-400">Progres sekarang</p><p class="mt-1 text-xl font-black text-gray-900">{{ (float) $plan->progress_percent }}%</p></div>
                                            <div class="rounded bg-blue-50 p-3"><p class="text-[10px] font-black uppercase text-blue-500">Capaian pekan</p><p class="mt-1 text-xl font-black text-blue-800">{{ $recommendation['weekly_completion'] }}%</p></div>
                                            <div class="rounded bg-indigo-50 p-3"><p class="text-[10px] font-black uppercase text-indigo-500">Rekomendasi kenaikan</p><p class="mt-1 text-xl font-black text-indigo-800">+{{ $recommendation['increment'] }}%</p></div>
                                        </div>
                                        <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                            <label><span class="mb-1 block text-xs font-bold">Progres OKR setelah diperbarui (%)</span><input type="number" name="plans[{{ $plan->id }}][progress_percent]" value="{{ old("plans.{$plan->id}.progress_percent", $recommendation['suggested']) }}" min="0" max="100" step="0.1" required class="w-full rounded-md border-gray-300 text-sm"></label>
                                            <label><span class="mb-1 block text-xs font-bold">Status target</span><select name="plans[{{ $plan->id }}][status]" class="w-full rounded-md border-gray-300 text-sm"><option value="not_started" @selected(old("plans.{$plan->id}.status", $recommendation['status']) === 'not_started')>Belum dimulai</option><option value="in_progress" @selected(old("plans.{$plan->id}.status", $recommendation['status']) === 'in_progress')>Berjalan</option><option value="at_risk" @selected(old("plans.{$plan->id}.status", $recommendation['status']) === 'at_risk')>Berisiko</option><option value="completed" @selected(old("plans.{$plan->id}.status", $recommendation['status']) === 'completed')>Tercapai</option></select></label>
                                            <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Catatan evaluasi</span><textarea name="plans[{{ $plan->id }}][note]" rows="3" required class="w-full rounded-md border-gray-300 text-sm">{{ old("plans.{$plan->id}.note", $recommendation['note']) }}</textarea></label>
                                            <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Bukti tambahan <span class="font-normal text-gray-400">(opsional bila bukti sudah dilampirkan pada evaluasi Jumat)</span></span><input type="file" name="plans[{{ $plan->id }}][evidence]" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="w-full rounded-md border border-gray-300 bg-white p-2 text-xs"></label>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="flex justify-end"><button class="rounded-md bg-indigo-700 px-5 py-2.5 text-sm font-black text-white hover:bg-indigo-600">Konfirmasi dan Perbarui Progres</button></div>
                            </form>
                        @else
                            <div class="border-t border-indigo-200 bg-indigo-50 px-5 py-4 text-xs font-semibold text-indigo-800">Laporan sudah direview. Unit {{ $selectedUnit->name }} dapat mengonfirmasi rekomendasi pembaruan progres OKR dari laporan ini.</div>
                        @endif
                    @elseif($report->status === 'reviewed' && $linkedProgressCount > 0 && $appliedProgressCount >= $linkedProgressCount)
                        <div class="border-t border-emerald-200 bg-emerald-50 px-5 py-4 text-xs font-black text-emerald-800">Progres seluruh target OKR terkait sudah diperbarui dari laporan ini.</div>
                    @elseif($report->status === 'reviewed' && $linkedProgressCount === 0)
                        <div class="border-t border-amber-200 bg-amber-50 px-5 py-4 text-xs font-semibold text-amber-800">Laporan belum dapat memperbarui progres karena komitmennya belum dikaitkan dengan target OKR.</div>
                    @endif
                @endif
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function weeklyPlanPicker(options, initialSelected) {
                const normalizedOptions = Array.isArray(options) ? options : [];
                const initialPlan = normalizedOptions.find(plan => String(plan.id) === String(initialSelected));

                return {
                    options: normalizedOptions,
                    selected: initialPlan ? String(initialPlan.id) : '',
                    search: initialPlan?.label ?? '',
                    open: false,
                    get selectedPlan() {
                        return this.options.find(plan => String(plan.id) === String(this.selected)) ?? null;
                    },
                    get filteredPlans() {
                        const keyword = this.search.trim().toLocaleLowerCase('id-ID');
                        if (! keyword || this.selectedPlan?.label === this.search) {
                            return this.options.slice(0, 30);
                        }

                        return this.options
                            .filter(plan => `${plan.label} ${plan.search}`.toLocaleLowerCase('id-ID').includes(keyword))
                            .slice(0, 30);
                    },
                    get hierarchyLevels() {
                        return [
                            { key: 'weekly', label: '1 · Mingguan', data: this.selectedPlan?.weekly, classes: 'border-emerald-100 bg-emerald-50 text-emerald-700' },
                            { key: 'monthly', label: '2 · Bulanan', data: this.selectedPlan?.monthly, classes: 'border-blue-100 bg-blue-50 text-blue-700' },
                            { key: 'annual', label: '3 · Tahunan', data: this.selectedPlan?.annual, classes: 'border-indigo-100 bg-indigo-50 text-indigo-700' },
                        ];
                    },
                    choose(plan) {
                        this.selected = String(plan.id);
                        this.search = plan.label;
                        this.open = false;
                    },
                    clear() {
                        this.selected = '';
                        this.search = '';
                        this.open = false;
                    },
                };
            }

            function weeklyOkrDashboard() {
                return {
                    presentationLoading: false,
                    presentationError: '',
                    async generatePresentation(url, csrfToken) {
                        if (this.presentationLoading) return;
                        this.presentationLoading = true;
                        this.presentationError = '';

                        try {
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json, application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                },
                            });
                            if (! response.ok) {
                                const error = await response.json().catch(() => ({}));
                                throw new Error(error.message || 'Slide presentasi belum dapat dibuat.');
                            }

                            const blob = await response.blob();
                            const disposition = response.headers.get('Content-Disposition') || '';
                            const encodedName = disposition.match(/filename\*=UTF-8''([^;]+)/i)?.[1];
                            const simpleName = disposition.match(/filename="?([^";]+)"?/i)?.[1];
                            const filename = encodedName ? decodeURIComponent(encodedName) : (simpleName || 'presentasi-evaluasi-okr.pptx');
                            const downloadUrl = URL.createObjectURL(blob);
                            const anchor = document.createElement('a');
                            anchor.href = downloadUrl;
                            anchor.download = filename;
                            document.body.appendChild(anchor);
                            anchor.click();
                            anchor.remove();
                            URL.revokeObjectURL(downloadUrl);
                        } catch (error) {
                            this.presentationError = error.message || 'Terjadi kesalahan saat membuat presentasi.';
                        } finally {
                            this.presentationLoading = false;
                        }
                    },
                    initCharts() {
                        this.$nextTick(() => {
                            if (typeof Chart === 'undefined') return;
                            const baseOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, ticks: { color: '#64748b', callback: value => value + '%' }, grid: { color: '#e5e7eb' } }, x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { display: false } } } };
                            new Chart(this.$refs.unitChart, { type: 'bar', data: { labels: @js($unitSummaries->pluck('name')), datasets: [{ data: @js($unitSummaries->pluck('completion')), backgroundColor: '#4f46e5', borderRadius: 5, maxBarThickness: 38 }] }, options: baseOptions });
                            new Chart(this.$refs.trendChart, { type: 'line', data: { labels: @js($weeklyTrend->pluck('label')), datasets: [{ data: @js($weeklyTrend->pluck('completion')), borderColor: '#059669', backgroundColor: 'rgba(5,150,105,.1)', fill: true, tension: .35, pointRadius: 4, pointBackgroundColor: '#ffffff', pointBorderWidth: 2 }] }, options: baseOptions });
                        });
                    }
                }
            }

            function initPlanningCommitmentsManager() {
                const container = document.getElementById('planning-items-container');
                const addBtn = document.getElementById('btn-add-commitment');
                const template = document.getElementById('commitment-item-template');

                if (! container || ! addBtn || ! template) return;

                function updateItemIndices() {
                    const items = container.querySelectorAll('.planning-item');
                    items.forEach((item, index) => {
                        item.setAttribute('data-item-index', index);

                        const title = item.querySelector('.commitment-title');
                        if (title) {
                            title.textContent = `Komitmen ${index + 1}${index === 0 ? ' · wajib' : ' · opsional'}`;
                        }

                        const removeBtn = item.querySelector('.remove-commitment-btn');
                        if (removeBtn) {
                            if (index === 0) {
                                removeBtn.classList.add('hidden');
                            } else {
                                removeBtn.classList.remove('hidden');
                            }
                        }

                        const commitmentInput = item.querySelector('.commitment-input');
                        const targetInput = item.querySelector('.target-input');
                        if (commitmentInput) {
                            if (index === 0) {
                                commitmentInput.setAttribute('required', 'required');
                            } else {
                                commitmentInput.removeAttribute('required');
                            }
                        }
                        if (targetInput) {
                            if (index === 0) {
                                targetInput.setAttribute('required', 'required');
                            } else {
                                targetInput.removeAttribute('required');
                            }
                        }

                        const formElements = item.querySelectorAll('[name*="items["]');
                        formElements.forEach(el => {
                            el.name = el.name.replace(/items\[\d+\]/, `items[${index}]`);
                        });
                    });
                }

                addBtn.addEventListener('click', function () {
                    const currentItems = container.querySelectorAll('.planning-item');
                    const nextIndex = currentItems.length;
                    const nextOrder = nextIndex + 1;

                    let html = template.innerHTML
                        .replace(/__INDEX__/g, nextIndex)
                        .replace(/__ORDER__/g, nextOrder);

                    const temp = document.createElement('div');
                    temp.innerHTML = html.trim();
                    const newItem = temp.firstElementChild;

                    container.appendChild(newItem);

                    if (window.Alpine) {
                        window.Alpine.initTree(newItem);
                    }

                    updateItemIndices();

                    const newCommitmentInput = newItem.querySelector('.commitment-input');
                    if (newCommitmentInput) {
                        newCommitmentInput.focus();
                    }
                });

                container.addEventListener('click', function (e) {
                    const removeBtn = e.target.closest('.remove-commitment-btn');
                    if (! removeBtn) return;

                    const item = removeBtn.closest('.planning-item');
                    if (! item) return;

                    const allItems = container.querySelectorAll('.planning-item');
                    if (allItems.length <= 1) return;

                    item.remove();
                    updateItemIndices();
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPlanningCommitmentsManager);
            } else {
                initPlanningCommitmentsManager();
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            function initQuillEditor(containerId, inputId) {
                const editorEl = document.getElementById(containerId);
                const hiddenInput = document.getElementById(inputId);
                if (!editorEl || editorEl.__quill || typeof Quill === 'undefined') return;

                const quill = new Quill(editorEl, {
                    theme: 'snow',
                    placeholder: 'Apresiasi, arahan, atau tindak lanjut yang perlu dilakukan...',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'header': [2, 3, false] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['blockquote', 'clean']
                        ]
                    }
                });
                editorEl.__quill = quill;

                if (hiddenInput && hiddenInput.value) {
                    const val = hiddenInput.value;
                    if (val.includes('<') && val.includes('>')) {
                        quill.clipboard.dangerouslyPasteHTML(val);
                    } else {
                        quill.setText(val);
                    }
                }

                const form = editorEl.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        if (quill.getText().trim().length === 0) {
                            hiddenInput.value = '';
                        } else {
                            hiddenInput.value = quill.root.innerHTML;
                        }
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    initQuillEditor('review-notes-quill', 'review_notes_input');
                });
            } else {
                initQuillEditor('review-notes-quill', 'review_notes_input');
            }
        </script>
    @endpush
</x-app-layout>
