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

    <div class="min-h-screen bg-gray-50 py-6" x-data="weeklyOkrDashboard()" x-init="initCharts()">
        <div class="mx-auto max-w-[1600px] space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
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
                                <form method="POST" action="{{ route('okr.weekly.copy-previous') }}">@csrf<input type="hidden" name="okr_period_id" value="{{ $period->id }}"><input type="hidden" name="okr_unit_id" value="{{ $selectedUnit->id }}"><input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"><button class="rounded-md bg-blue-700 px-4 py-2.5 text-xs font-black text-white hover:bg-blue-600">Salin yang Belum Selesai</button></form>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('okr.weekly.planning') }}" class="space-y-5 p-5">
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
                            <button class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-500">Simpan Rencana Senin</button>
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
                        <form method="POST" action="{{ route('okr.weekly.review', $report) }}" class="border-t border-gray-200 bg-amber-50 p-5">@csrf<label class="block"><span class="mb-1.5 block text-xs font-black text-amber-900">Catatan Kepala Sekolah</span><textarea name="review_notes" rows="3" class="w-full rounded-md border-amber-200 text-sm" placeholder="Apresiasi, arahan, atau tindak lanjut yang perlu dilakukan..."></textarea></label><div class="mt-3 flex justify-end"><button class="rounded-md bg-gray-900 px-5 py-2.5 text-sm font-bold text-white">Tandai Sudah Ditinjau</button></div></form>
                    @elseif($report->status === 'reviewed')
                        <div class="border-t border-emerald-200 bg-emerald-50 px-5 py-4"><p class="text-xs font-black text-emerald-900">Ditinjau oleh {{ $report->reviewer?->name ?? 'Kepala Sekolah' }} · {{ $report->reviewed_at?->translatedFormat('d M Y H:i') }}</p>@if($report->review_notes)<p class="mt-2 text-sm text-emerald-800">{{ $report->review_notes }}</p>@endif</div>
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
    @endpush
</x-app-layout>
