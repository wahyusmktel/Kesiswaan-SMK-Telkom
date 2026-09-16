<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pekanan OKR {{ $report->unit->name }}</title>
    <style>
        @page { margin: 22px 30px 34px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #172033; font-family: DejaVu Sans, sans-serif; font-size: 9px; line-height: 1.45; }
        .header { background: #172554; color: #fff; padding: 18px 20px; border-radius: 7px; }
        .logo { width: 54px; height: 54px; object-fit: contain; float: left; margin-right: 14px; }
        .eyebrow { color: #a5f3fc; font-size: 7px; font-weight: bold; letter-spacing: 1.4px; text-transform: uppercase; }
        h1 { margin: 3px 0 2px; font-size: 18px; line-height: 1.2; }
        .header p { margin: 0; color: #dbeafe; font-size: 8px; }
        .clear { clear: both; }
        .meta { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .meta td { width: 25%; padding: 8px 10px; border: 1px solid #dbe3ef; background: #f8fafc; vertical-align: top; }
        .label { color: #64748b; font-size: 6.5px; font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .value { margin-top: 2px; color: #0f172a; font-size: 9px; font-weight: bold; }
        .section { margin-top: 13px; }
        .section-title { padding-left: 9px; border-left: 4px solid #2563eb; color: #172554; font-size: 12px; font-weight: bold; }
        .section-subtitle { margin: 2px 0 8px 13px; color: #64748b; font-size: 7.5px; }
        .callout { padding: 10px 12px; border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 5px; }
        .callout strong { display: block; margin-bottom: 3px; color: #1d4ed8; font-size: 7px; text-transform: uppercase; }
        .summary { width: 100%; margin-top: 8px; border-collapse: separate; border-spacing: 5px 0; }
        .summary td { text-align: center; padding: 8px 5px; border: 1px solid #dbe3ef; border-radius: 5px; }
        .summary b { display: block; margin-top: 2px; font-size: 16px; }
        .blue { color: #2563eb; } .green { color: #059669; } .red { color: #dc2626; } .amber { color: #d97706; }
        .item { margin-top: 9px; padding: 11px; border: 1px solid #dbe3ef; border-radius: 6px; page-break-inside: avoid; }
        .item-head { width: 100%; border-collapse: collapse; }
        .item-head td { vertical-align: top; }
        .number { display: inline-block; width: 22px; height: 22px; padding-top: 4px; border-radius: 11px; background: #2563eb; color: #fff; text-align: center; font-weight: bold; }
        .commitment { padding-left: 8px; font-size: 10px; font-weight: bold; }
        .percent { width: 58px; text-align: right; color: #0f766e; font-size: 15px; font-weight: bold; }
        .bar { height: 5px; margin: 7px 0 9px 30px; overflow: hidden; border-radius: 3px; background: #e2e8f0; }
        .bar > div { height: 5px; background: #0d9488; }
        .details { width: 100%; border-collapse: collapse; }
        .details td { width: 50%; padding: 7px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        .details p { margin: 2px 0 0; }
        .hierarchy { margin-top: 7px; padding: 7px 9px; background: #f8fafc; border-left: 3px solid #818cf8; }
        .hierarchy p { margin: 2px 0; }
        .update { margin-top: 7px; padding: 7px 9px; border: 1px solid #ccfbf1; background: #f0fdfa; }
        .update-time { color: #0f766e; font-size: 7px; font-weight: bold; }
        .update-note { margin: 3px 0 0; font-weight: bold; }
        .update-blocker { margin: 3px 0 0; color: #b91c1c; }
        .status { display: inline-block; margin-top: 3px; padding: 2px 6px; border-radius: 8px; background: #e0f2fe; color: #075985; font-size: 6.5px; font-weight: bold; text-transform: uppercase; }
        .evaluation { margin-top: 8px; padding: 9px; border: 1px solid #bbf7d0; background: #f0fdf4; }
        .review { margin-top: 10px; padding: 10px 12px; border: 1px solid #fde68a; background: #fffbeb; border-radius: 5px; page-break-inside: avoid; }
        .footer { position: fixed; bottom: -23px; left: 0; right: 0; color: #94a3b8; font-size: 7px; text-align: center; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    @php
        $statusLabels = ['draft' => 'Draft Senin', 'submitted' => 'Menunggu Tinjauan', 'reviewed' => 'Sudah Ditinjau'];
        $itemStatusLabels = ['not_started' => 'Belum Dimulai', 'on_progress' => 'Berjalan', 'completed' => 'Tercapai', 'blocked' => 'Terhambat'];
        $average = $report->items->isNotEmpty() ? round((float) $report->items->avg('completion_percent'), 1) : 0;
        $logo = public_path('images/teaching-module/smk-telkom-lampung.png');
    @endphp

    <div class="footer">Arsip Laporan Pekanan OKR · {{ $report->unit->name }} · Halaman <span class="page-number"></span></div>

    <div class="header">
        @if(is_file($logo))<img src="{{ $logo }}" class="logo" alt="Logo">@endif
        <div class="eyebrow">Sistem Informasi SMK Telkom Lampung</div>
        <h1>Laporan Pekanan OKR</h1>
        <p>Dokumen arsip rencana, progres, evaluasi, dan hasil tinjauan unit kerja</p>
        <div class="clear"></div>
    </div>

    <table class="meta">
        <tr>
            <td><div class="label">Unit Kerja</div><div class="value">{{ $report->unit->name }}</div></td>
            <td><div class="label">Periode Pekan</div><div class="value">{{ $report->week_start->translatedFormat('d M') }} – {{ $report->week_end->translatedFormat('d M Y') }}</div></td>
            <td><div class="label">Status Laporan</div><div class="value">{{ $statusLabels[$report->status] ?? $report->status }}</div></td>
            <td><div class="label">Dicetak</div><div class="value">{{ $generatedAt->translatedFormat('d M Y H:i') }}</div></td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">Rencana & Komitmen Senin</div>
        <div class="section-subtitle">Arah kerja dan dukungan yang disepakati untuk pekan berjalan.</div>
        <div class="callout"><strong>Fokus Pekan</strong>{{ $report->weekly_focus }}</div>
        <div class="callout" style="margin-top: 6px; border-color: #fde68a; background: #fffbeb;"><strong style="color:#b45309;">Dukungan yang Dibutuhkan</strong>{{ $report->support_needed ?: 'Tidak ada dukungan khusus yang diajukan.' }}</div>
    </div>

    <table class="summary">
        <tr>
            <td><span class="label">Rata-rata Progres</span><b class="blue">{{ $average }}%</b></td>
            <td><span class="label">Jumlah Komitmen</span><b>{{ $report->items->count() }}</b></td>
            <td><span class="label">Tercapai</span><b class="green">{{ $report->items->where('final_status', 'completed')->count() }}</b></td>
            <td><span class="label">Terhambat</span><b class="red">{{ $report->items->where('final_status', 'blocked')->count() }}</b></td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title" style="border-color:#0d9488;">Progres Pekan Berjalan & Resume Evaluasi Jumat</div>
        <div class="section-subtitle">Jejak pembaruan pekerjaan disandingkan dengan hasil evaluasi akhir.</div>

        @foreach($report->items as $item)
            @php
                $weeklyTarget = $item->plan;
                $monthlyTarget = $weeklyTarget?->parent?->level === 'monthly' ? $weeklyTarget->parent : null;
                $annualTarget = $monthlyTarget?->parent?->level === 'annual' ? $monthlyTarget->parent : null;
            @endphp
            <div class="item">
                <table class="item-head"><tr><td style="width:30px;"><span class="number">{{ $item->priority_order }}</span></td><td class="commitment">{{ $item->commitment }}<br><span class="status">{{ $itemStatusLabels[$item->final_status] ?? $item->final_status }}</span></td><td class="percent">{{ (float) $item->completion_percent }}%</td></tr></table>
                <div class="bar"><div style="width:{{ min(100, (float) $item->completion_percent) }}%;"></div></div>
                <table class="details"><tr><td><span class="label">Target Terukur</span><p>{{ $item->measurable_target }}</p></td><td><span class="label">Kolaborasi / Persetujuan</span><p>{{ collect([$item->cross_unit_dependencies, $item->approval_needs])->filter()->implode(' · ') ?: 'Tidak ada catatan khusus.' }}</p></td></tr></table>

                @if($weeklyTarget)
                    <div class="hierarchy">
                        <p><strong>OKR Mingguan:</strong> {{ $weeklyTarget->title }}</p>
                        @if($monthlyTarget)<p><strong>OKR Bulanan:</strong> {{ $monthlyTarget->title }}</p>@endif
                        @if($annualTarget)<p><strong>OKR Tahunan:</strong> {{ $annualTarget->title }}</p>@endif
                        @if($weeklyTarget->keyResult)<p><strong>{{ $weeklyTarget->keyResult->code }}:</strong> {{ $weeklyTarget->keyResult->title }}</p>@endif
                    </div>
                @endif

                @forelse($item->progressUpdates->sortBy('recorded_at') as $update)
                    <div class="update">
                        <div class="update-time">{{ $update->recorded_at->translatedFormat('d M Y H:i') }} · {{ $update->recorder?->name ?? 'Pengguna' }} · {{ (float) $update->progress_percent }}%</div>
                        <p class="update-note">{{ $update->note }}</p>
                        @if($update->blockers)<p class="update-blocker"><strong>Kendala:</strong> {{ $update->blockers }}</p>@endif
                        @if($update->evidence_path)<p style="margin:3px 0 0;color:#4338ca;"><strong>Bukti:</strong> {{ basename($update->evidence_path) }}</p>@endif
                    </div>
                @empty
                    <div class="update" style="border-color:#fde68a;background:#fffbeb;color:#92400e;">Belum ada pembaruan progres selama pekan berjalan.</div>
                @endforelse

                <div class="evaluation">
                    <span class="label" style="color:#047857;">Hasil Evaluasi Jumat</span>
                    <p style="margin:3px 0 0;"><strong>Capaian aktual:</strong> {{ $item->actual_result ?: 'Belum diisi.' }}</p>
                    @if($item->blockers)<p style="margin:3px 0 0;color:#b91c1c;"><strong>Kendala:</strong> {{ $item->blockers }}</p>@endif
                    @if($item->next_follow_up)<p style="margin:3px 0 0;color:#1d4ed8;"><strong>Tindak lanjut:</strong> {{ $item->next_follow_up }}</p>@endif
                    @if($item->evidence_path)<p style="margin:3px 0 0;"><strong>Bukti akhir:</strong> {{ basename($item->evidence_path) }}</p>@endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="review">
        <div class="label" style="color:#b45309;">Tinjauan Kepala Sekolah</div>
        <p style="margin:4px 0 0;font-size:9px;">{{ $report->review_notes ?: 'Belum ada catatan tinjauan Kepala Sekolah.' }}</p>
        @if($report->reviewed_at)<p style="margin:4px 0 0;color:#64748b;font-size:7px;">Ditinjau oleh {{ $report->reviewer?->name ?? 'Kepala Sekolah' }} pada {{ $report->reviewed_at->translatedFormat('d M Y H:i') }}</p>@endif
    </div>

    <p style="margin-top:12px;color:#94a3b8;font-size:7px;">Dokumen diunduh oleh {{ $generatedBy->name }}. Data pada dokumen ini mengikuti kondisi laporan saat arsip dibuat.</p>
</body>
</html>
