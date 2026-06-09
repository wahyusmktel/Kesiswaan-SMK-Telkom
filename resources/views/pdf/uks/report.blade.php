<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; }
        .header { border-bottom: 3px solid #dc2626; padding-bottom: 12px; }
        .title { font-size: 22px; font-weight: 900; }
        .cards { width: 100%; border-collapse: separate; border-spacing: 8px; margin-top: 14px; }
        .cards td { width: 25%; background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px; border-radius: 8px; text-align: center; }
        .value { font-size: 22px; font-weight: 900; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 18px; }
        table.data th { background: #111827; color: #fff; padding: 8px; text-align: left; }
        table.data td { border-bottom: 1px solid #e5e7eb; padding: 7px; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan {{ $period === 'weekly' ? 'Mingguan' : 'Bulanan' }} UKS</div>
        <div>Periode {{ $dateFrom->translatedFormat('d F Y') }} - {{ $dateTo->translatedFormat('d F Y') }}</div>
    </div>
    <table class="cards">
        <tr>
            <td><div>Total</div><div class="value">{{ $summary['total'] }}</div></td>
            <td><div>Rujukan</div><div class="value">{{ $summary['referrals'] }}</div></td>
            <td><div>Pulang</div><div class="value">{{ $summary['home'] }}</div></td>
            <td><div>Istirahat</div><div class="value">{{ $summary['resting'] }}</div></td>
        </tr>
    </table>
    <h3>Analisa Penyakit / Diagnosis Terbanyak</h3>
    <ol>
        @forelse($summary['top_diagnoses'] as $diagnosis => $count)
            <li>{{ $diagnosis }} - {{ $count }} kasus</li>
        @empty
            <li>Belum ada diagnosis.</li>
        @endforelse
    </ol>
    <table class="data">
        <thead><tr><th>Tanggal</th><th>Siswa</th><th>Keluhan</th><th>Diagnosis</th><th>Tindak Lanjut</th></tr></thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->visited_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $record->student?->nama_lengkap }}<br><small>{{ $record->student?->rombels->first()?->kelas?->nama_kelas ?? '-' }}</small></td>
                    <td>{{ $record->complaint }}</td>
                    <td>{{ $record->diagnosis ?: '-' }}</td>
                    <td>{{ $record->disposition_label }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
