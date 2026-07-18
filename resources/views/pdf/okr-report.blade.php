<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px 28px 30px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 9px; }
        h1 { margin: 0; font-size: 18px; }
        .muted { color: #6b7280; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 10px; }
        .header-table, .summary, .plans { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .right { text-align: right; }
        .vision { margin-top: 10px; padding: 8px 10px; background: #f3f4f6; border-left: 3px solid #4f46e5; line-height: 1.45; }
        .summary { margin: 12px 0; table-layout: fixed; }
        .summary td { border: 1px solid #d1d5db; padding: 9px; }
        .summary .value { display: block; margin-top: 4px; font-size: 16px; font-weight: bold; }
        .plans { table-layout: fixed; }
        .plans th { background: #1f2937; color: white; padding: 7px 6px; border: 1px solid #374151; text-align: left; }
        .plans td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; line-height: 1.35; }
        .plans tr { page-break-inside: avoid; }
        .code { font-weight: bold; color: #b91c1c; }
        .level { font-weight: bold; text-transform: uppercase; font-size: 7px; }
        .progress-track { width: 100%; height: 5px; background: #e5e7eb; margin-top: 4px; }
        .progress-value { height: 5px; background: #4f46e5; }
        .status-completed { color: #047857; font-weight: bold; }
        .status-at_risk { color: #b45309; font-weight: bold; }
        .footer { position: fixed; bottom: -16px; left: 0; right: 0; text-align: center; color: #9ca3af; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $statusLabels = [
            'not_started' => 'Belum dimulai',
            'in_progress' => 'Berjalan',
            'at_risk' => 'Berisiko',
            'completed' => 'Tercapai',
            'cancelled' => 'Dibatalkan',
        ];
        $levelLabels = ['annual' => 'Tahunan', 'monthly' => 'Bulanan', 'weekly' => 'Mingguan'];
    @endphp

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>Laporan Capaian OKR Sekolah</h1>
                    <div class="muted">{{ $schoolName }} · {{ $period->title }}</div>
                </td>
                <td class="right">
                    <strong>{{ $unit?->name ?? 'Laporan Global Seluruh Unit' }}</strong><br>
                    Dicetak {{ now()->translatedFormat('d F Y H:i') }}<br>
                    oleh {{ $generatedBy->name }}
                </td>
            </tr>
        </table>
        @if($period->vision)
            <div class="vision"><strong>Visi:</strong> {{ $period->vision }}</div>
        @endif
    </div>

    <table class="summary">
        <tr>
            <td><span class="muted">Progres rata-rata</span><span class="value">{{ $summary['progress'] }}%</span></td>
            <td><span class="muted">Total rencana</span><span class="value">{{ $summary['total'] }}</span></td>
            <td><span class="muted">Target tercapai</span><span class="value">{{ $summary['completed'] }}</span></td>
            <td><span class="muted">Perlu perhatian</span><span class="value">{{ $summary['at_risk'] }}</span></td>
        </tr>
    </table>

    <table class="plans">
        <thead>
            <tr>
                <th style="width: 9%">Unit</th>
                <th style="width: 9%">Objektif / KR</th>
                <th style="width: 7%">Tingkat</th>
                <th style="width: 25%">Rencana dan indikator</th>
                <th style="width: 10%">PIC / Tenggat</th>
                <th style="width: 10%">Target</th>
                <th style="width: 10%">Progres</th>
                <th style="width: 20%">Evaluasi terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td><strong>{{ $plan->unit->name }}</strong></td>
                    <td><span class="code">{{ $plan->keyResult->code }}</span><br>{{ $plan->keyResult->title }}<br><span class="muted">{{ $plan->keyResult->objective->code }}</span></td>
                    <td><span class="level">{{ $levelLabels[$plan->level] }}</span></td>
                    <td>
                        <strong>{{ $plan->title }}</strong>
                        @if($plan->description)<br><span class="muted">{{ $plan->description }}</span>@endif
                        @if($plan->success_indicator)<br><br><strong>Indikator:</strong> {{ $plan->success_indicator }}@endif
                    </td>
                    <td>{{ $plan->owner?->name ?? '-' }}<br><span class="muted">{{ $plan->ends_at?->translatedFormat('d M Y') ?? '-' }}</span></td>
                    <td>{{ $plan->target_value !== null ? number_format((float) $plan->target_value, 0, ',', '.').' '.$plan->metric_unit : '-' }}</td>
                    <td class="status-{{ $plan->status }}">
                        {{ number_format((float) $plan->progress_percent, 0) }}%<br>
                        <span style="font-size:7px">{{ $statusLabels[$plan->status] }}</span>
                        <div class="progress-track"><div class="progress-value" style="width:{{ min(100, (float) $plan->progress_percent) }}%"></div></div>
                    </td>
                    <td>
                        {{ $plan->latest_evaluation ?? 'Belum ada evaluasi.' }}
                        @if($plan->updates->first())
                            <br><span class="muted">{{ $plan->updates->first()->recorded_at->translatedFormat('d M Y') }} · {{ $plan->updates->first()->user?->name }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; padding:20px">Belum ada rencana OKR pada ruang lingkup laporan ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dokumen dihasilkan oleh Sistem Informasi {{ $schoolName }} · Arsip OKR {{ $period->academicYear?->tahun }}</div>
</body>
</html>
