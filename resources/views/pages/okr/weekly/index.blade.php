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
        $planningItems = collect(range(0, 2))->map(fn ($index) => $report?->items->get($index));
        $previousWeek = $weekStart->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->addWeek()->format('Y-m-d');
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
                        <p class="mt-1 max-w-3xl text-xs leading-5 text-indigo-800">Kepala unit menyimpan maksimal tiga komitmen pada Senin, kemudian mengisi capaian aktual, kendala, dan tindak lanjut pada Jumat. Kepala Sekolah meninjau hasil seluruh unit dari halaman ini.</p>
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
                    <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $statusColors[$report?->status ?? 'not_reported'] }}">{{ $statusLabels[$report?->status ?? 'not_reported'] }}</span>
                </div>

                @if($canEditSelected && (!$report || $report->status === 'draft'))
                    <form method="POST" action="{{ route('okr.weekly.planning') }}" class="space-y-5 p-5">
                        @csrf
                        <input type="hidden" name="okr_period_id" value="{{ $period->id }}"><input type="hidden" name="okr_unit_id" value="{{ $selectedUnit->id }}"><input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                        <div class="border-l-4 border-blue-500 bg-blue-50 px-4 py-3"><p class="text-sm font-black text-blue-900">Rencana Senin</p><p class="mt-1 text-xs text-blue-700">Tentukan fokus dan maksimal tiga komitmen utama yang harus selesai atau bergerak signifikan minggu ini.</p></div>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Fokus OKR pekan ini</span><textarea name="weekly_focus" rows="3" required class="w-full rounded-md border-gray-300 text-sm">{{ old('weekly_focus', $report?->weekly_focus) }}</textarea></label>
                        <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Dukungan yang dibutuhkan</span><textarea name="support_needed" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old('support_needed', $report?->support_needed) }}</textarea></label>

                        <div class="space-y-4">
                            @foreach($planningItems as $index => $planningItem)
                                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                                    <p class="mb-3 text-xs font-black uppercase text-gray-500">Komitmen {{ $index + 1 }}{{ $index === 0 ? ' · wajib' : ' · opsional' }}</p>
                                    <div class="grid gap-4 lg:grid-cols-2">
                                        <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Terkait target OKR</span><select name="items[{{ $index }}][okr_plan_id]" class="w-full rounded-md border-gray-300 text-sm"><option value="">Belum dikaitkan</option>@foreach($availablePlans as $plan)<option value="{{ $plan->id }}" @selected((string) old("items.$index.okr_plan_id", $planningItem?->okr_plan_id) === (string) $plan->id)>{{ strtoupper($plan->level) }} · {{ $plan->keyResult->code }} · {{ $plan->title }}</option>@endforeach</select></label>
                                        <label><span class="mb-1 block text-xs font-bold">Komitmen target</span><textarea name="items[{{ $index }}][commitment]" rows="3" @required($index === 0) class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.commitment", $planningItem?->commitment) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Target terukur / batas waktu</span><textarea name="items[{{ $index }}][measurable_target]" rows="3" @required($index === 0) class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.measurable_target", $planningItem?->measurable_target) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Ketergantungan lintas unit</span><textarea name="items[{{ $index }}][cross_unit_dependencies]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.cross_unit_dependencies", $planningItem?->cross_unit_dependencies) }}</textarea></label>
                                        <label><span class="mb-1 block text-xs font-bold">Anggaran/logistik yang membutuhkan persetujuan</span><textarea name="items[{{ $index }}][approval_needs]" rows="2" class="w-full rounded-md border-gray-300 text-sm">{{ old("items.$index.approval_needs", $planningItem?->approval_needs) }}</textarea></label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-end"><button class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-500">Simpan Rencana Senin</button></div>
                    </form>
                @elseif($report)
                    <div class="space-y-4 p-5">
                        <div><p class="text-[10px] font-black uppercase text-gray-400">Fokus pekan</p><p class="mt-1 text-sm text-gray-800">{{ $report->weekly_focus }}</p></div>
                        @if($report->support_needed)<div><p class="text-[10px] font-black uppercase text-gray-400">Dukungan dibutuhkan</p><p class="mt-1 text-sm text-gray-700">{{ $report->support_needed }}</p></div>@endif
                    </div>
                @else
                    <div class="px-5 py-12 text-center text-sm text-gray-500">Unit ini belum membuat rencana pekanan.</div>
                @endif

                @if($report)
                    @if($canEditSelected && $report->status !== 'reviewed')
                        <form method="POST" action="{{ route('okr.weekly.evaluation', $report) }}" enctype="multipart/form-data" class="space-y-5 border-t border-gray-200 p-5">
                            @csrf
                            <div class="border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3"><p class="text-sm font-black text-emerald-900">Evaluasi Jumat</p><p class="mt-1 text-xs text-emerald-700">Bandingkan hasil aktual terhadap komitmen Senin dan isi berdasarkan data atau bukti.</p></div>
                            @foreach($report->items as $item)
                                <div class="rounded-md border border-gray-200 p-4">
                                    <p class="text-xs font-black text-indigo-700">Komitmen {{ $item->priority_order }}</p><p class="mt-1 text-sm font-bold text-gray-900">{{ $item->commitment }}</p><p class="mt-1 text-xs text-gray-500">Target: {{ $item->measurable_target }}</p>
                                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                                        <label class="lg:col-span-2"><span class="mb-1 block text-xs font-bold">Capaian aktual</span><textarea name="items[{{ $item->id }}][actual_result]" rows="3" required class="w-full rounded-md border-gray-300 text-sm">{{ old("items.{$item->id}.actual_result", $item->actual_result) }}</textarea></label>
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
                @endif
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function weeklyOkrDashboard() {
                return {
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
        </script>
    @endpush
</x-app-layout>
