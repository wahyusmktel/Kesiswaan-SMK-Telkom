<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Laporan Aset</title>
    <style>
        @page { margin: 13mm 11mm 15mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 8px; }
        .header { border-bottom: 3px solid #b91c1c; padding-bottom: 8px; }
        .header-table, .summary-table, .report-table, .signature-table { width: 100%; border-collapse: collapse; }
        .logo-cell { width: 75px; vertical-align: middle; }
        .logo { max-width: 64px; max-height: 50px; }
        h1 { margin: 0 0 3px; color: #991b1b; font-size: 17px; letter-spacing: .2px; }
        .school { margin: 0; font-size: 10px; font-weight: bold; }
        .meta { margin: 3px 0 0; color: #64748b; font-size: 7.5px; }
        .summary-table { margin: 10px 0; table-layout: fixed; }
        .summary-table td { padding-right: 6px; }
        .summary-card { border: 1px solid #e2e8f0; border-left: 4px solid #dc2626; background: #f8fafc; padding: 7px 8px; }
        .summary-label { color: #64748b; font-size: 6.8px; font-weight: bold; text-transform: uppercase; }
        .summary-value { margin-top: 2px; color: #0f172a; font-size: 14px; font-weight: bold; }
        .section-title { margin: 9px 0 5px; color: #991b1b; font-size: 10px; font-weight: bold; }
        .report-table { table-layout: fixed; }
        .report-table thead { display: table-header-group; }
        .report-table th { border: 1px solid #991b1b; background: #b91c1c; color: #fff; padding: 5px 3px; font-size: 6.7px; text-align: center; vertical-align: middle; }
        .report-table td { border-bottom: 1px solid #e2e8f0; padding: 4px 3px; vertical-align: top; word-wrap: break-word; }
        .report-table tr:nth-child(even) td { background: #f8fafc; }
        .center { text-align: center; }
        .ticket { color: #475569; font-family: DejaVu Sans Mono, monospace; font-size: 6.7px; font-weight: bold; }
        .asset { color: #0f172a; font-weight: bold; }
        .muted { color: #64748b; font-size: 6.7px; }
        .badge { display: inline-block; border-radius: 6px; padding: 2px 4px; font-size: 6px; font-weight: bold; text-transform: uppercase; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #075985; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-neutral { background: #e2e8f0; color: #475569; }
        .signature-wrap { page-break-inside: avoid; margin-top: 13px; }
        .signature-table td { border: 0; vertical-align: top; }
        .verification { width: 58%; padding: 9px; border: 1px solid #e2e8f0 !important; background: #f8fafc; color: #475569; }
        .signature { width: 42%; padding-left: 22px; text-align: center; }
        .signature img { width: 76px; height: 76px; margin: 5px 0 2px; }
        .signature-name { font-size: 9px; font-weight: bold; text-decoration: underline; }
        .token { margin-top: 4px; color: #64748b; font-family: DejaVu Sans Mono, monospace; font-size: 5.8px; word-break: break-all; }
        .footer { position: fixed; right: 0; bottom: -9mm; left: 0; border-top: 1px solid #e2e8f0; padding-top: 3px; color: #94a3b8; font-size: 6px; text-align: center; }
    </style>
</head>
<body>
    <div class="footer">SISFO SMK Telkom Lampung - Rekap Laporan Aset - Dokumen bertanda tangan digital</div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">@if($brandLogo)<img src="{{ $brandLogo }}" class="logo" alt="Logo">@endif</td>
                <td>
                    <p class="school">SMK TELKOM LAMPUNG</p>
                    <h1>REKAP LAPORAN ASET DAN SARANA PRASARANA</h1>
                    <p class="meta">Periode/filter: {{ implode(' | ', $filterLabels) }}</p>
                    <p class="meta">Dibuat dan ditandatangani: {{ $signedAt->locale('id')->translatedFormat('d F Y, H:i') }} WIB</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary-table">
        <tr>
            @foreach([
                ['Total Laporan', $summary['total']],
                ['Laporan Baru', $summary['new']],
                ['Dalam Proses', $summary['in_progress']],
                ['Prioritas Tinggi/Darurat', $summary['urgent']],
                ['Selesai', $summary['completed']],
            ] as [$label, $value])
                <td><div class="summary-card"><div class="summary-label">{{ $label }}</div><div class="summary-value">{{ number_format($value) }}</div></div></td>
            @endforeach
        </tr>
    </table>

    <div class="section-title">Daftar Laporan</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 3%">No</th>
                <th style="width: 10%">Tiket / Waktu</th>
                <th style="width: 12%">Lokasi</th>
                <th style="width: 13%">Aset / Kategori</th>
                <th style="width: 11%">Pelapor</th>
                <th style="width: 7%">Urgensi</th>
                <th style="width: 9%">Status</th>
                <th style="width: 18%">Deskripsi</th>
                <th style="width: 17%">Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
                @php
                    $urgencyClass = in_array($report->urgency, ['darurat', 'tinggi']) ? 'badge-danger' : ($report->urgency === 'normal' ? 'badge-info' : 'badge-neutral');
                    $statusClass = match($report->status) {'selesai' => 'badge-success', 'diproses' => 'badge-warning', 'diverifikasi' => 'badge-info', 'ditolak' => 'badge-neutral', default => 'badge-danger'};
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><span class="ticket">{{ $report->ticket_number }}</span><br><span class="muted">{{ $report->created_at->format('d/m/Y H:i') }}</span></td>
                    <td><span class="asset">{{ $report->location?->name ?? '-' }}</span><br><span class="muted">{{ $report->location?->building?->name ?? '-' }}{{ $report->location?->floor ? ' - '.$report->location->floor : '' }}</span></td>
                    <td><span class="asset">{{ $report->asset_name }}</span><br><span class="muted">{{ \App\Models\AssetReport::CATEGORIES[$report->category] ?? ucfirst($report->category) }}</span></td>
                    <td>{{ $report->reporter_name }}<br><span class="muted">{{ str_replace('_', '/', ucfirst($report->reporter_type)) }}{{ $report->contact ? ' - '.$report->contact : '' }}</span></td>
                    <td class="center"><span class="badge {{ $urgencyClass }}">{{ $report->urgency }}</span></td>
                    <td class="center"><span class="badge {{ $statusClass }}">{{ \App\Models\AssetReport::STATUSES[$report->status] ?? $report->status }}</span></td>
                    <td>{{ $report->description }}</td>
                    <td>{{ $report->admin_notes ?: 'Belum ada catatan tindak lanjut.' }}@if($report->handler)<br><span class="muted">Petugas: {{ $report->handler->name }}</span>@endif</td>
                </tr>
            @empty
                <tr><td colspan="9" class="center" style="padding: 15px; color: #64748b;">Tidak ada laporan pada filter yang dipilih.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-wrap">
        <table class="signature-table">
            <tr>
                <td class="verification">
                    <strong>Verifikasi Dokumen Digital</strong><br>
                    Pindai QR di sebelah kanan untuk memastikan identitas penandatangan dan keaslian rekaman tanda tangan digital melalui SISFO.<br>
                    <div class="token">Token: {{ $document->token }}<br>{{ $verificationUrl }}</div>
                </td>
                <td class="signature">
                    Bandar Lampung, {{ $signedAt->locale('id')->translatedFormat('d F Y') }}<br>
                    <strong>KAUR Sarana dan Prasarana</strong><br>
                    <img src="{{ $signatureQr }}" alt="QR Tanda Tangan Digital"><br>
                    <span class="signature-name">{{ $document->signer_name }}</span><br>
                    @if($document->signer_nip)<span class="muted">NIP. {{ $document->signer_nip }}</span>@endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
