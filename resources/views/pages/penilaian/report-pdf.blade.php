<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color:#111827; font-size: 12px; }
        h1 { margin:0 0 4px; font-size: 20px; }
        .muted { color:#6b7280; }
        table { width:100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border:1px solid #e5e7eb; padding:8px; text-align:left; }
        th { background:#f3f4f6; }
        .summary { display: table; width: 100%; margin-top: 18px; }
        .box { display: table-cell; border:1px solid #e5e7eb; padding:10px; }
    </style>
</head>
<body>
    <h1>Laporan Penilaian</h1>
    <div class="muted">{{ $period->title }} | {{ $period->tahunPelajaran?->tahun }} | Semester {{ $period->semester }}</div>

    <div class="summary">
        @foreach($summary as $label => $row)
            <div class="box">
                <strong>{{ $label }}</strong><br>
                Rata-rata: {{ $row['average'] }}<br>
                Respons: {{ $row['responses'] }}
            </div>
        @endforeach
    </div>

    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Nama</th>
                <th>Skor</th>
                <th>Jumlah Respons</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranking as $row)
                <tr>
                    <td>#{{ $loop->iteration }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['score'] }}</td>
                    <td>{{ $row['responses'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
