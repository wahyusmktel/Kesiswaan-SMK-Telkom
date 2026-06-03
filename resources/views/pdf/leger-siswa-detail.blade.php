<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Nilai Siswa</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #991b1b; padding-bottom: 10px; margin-bottom: 14px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .muted { color: #6b7280; }
        .meta, .stats, .data { width: 100%; border-collapse: collapse; }
        .meta td { padding: 3px 0; }
        .stats { margin: 12px 0 14px; }
        .stats td { border: 1px solid #e5e7eb; padding: 8px; text-align: center; }
        .stats .label { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .stats .value { font-size: 15px; font-weight: bold; }
        .data th { background: #dc2626; color: white; padding: 7px 5px; border: 1px solid #fff; font-size: 9px; text-transform: uppercase; }
        .data td { padding: 6px 5px; border: 1px solid #e5e7eb; }
        .data tr:nth-child(even) td { background: #fef2f2; }
        .center { text-align: center; }
        .right { text-align: right; }
        .box { margin-top: 14px; border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Detail Nilai Siswa</div>
        <div class="muted">{{ $ujian->nama_ujian }} | {{ $ujian->tahunPelajaran?->tahun }} - {{ $ujian->semester }}</div>
    </div>

    <table class="meta">
        <tr><td width="18%">Nama</td><td width="2%">:</td><td>{{ $siswa->nama_lengkap }}</td></tr>
        <tr><td>NIS</td><td>:</td><td>{{ $siswa->nis }}</td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $detail['kelas'] }}</td></tr>
    </table>

    <table class="stats">
        <tr>
            <td><div class="label">Rata-rata</div><div class="value">{{ $detail['average'] !== null ? number_format($detail['average'], 2, ',', '.') : '-' }}</div></td>
            <td><div class="label">Tertinggi</div><div class="value">{{ $detail['highest'] !== null ? number_format($detail['highest'], 2, ',', '.') : '-' }}</div></td>
            <td><div class="label">Terendah</div><div class="value">{{ $detail['lowest'] !== null ? number_format($detail['lowest'], 2, ',', '.') : '-' }}</div></td>
            <td><div class="label">Kelengkapan</div><div class="value">{{ $detail['complete_count'] }}/{{ $detail['subject_count'] }}</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th width="15%">Benar</th>
                <th width="15%">Jumlah Soal</th>
                <th width="15%">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail['subjects'] as $subject)
                <tr>
                    <td>{{ $subject['mapel'] }}</td>
                    <td class="center">{{ $subject['jumlah_benar'] ?? '-' }}</td>
                    <td class="center">{{ $subject['jumlah_soal'] ?? '-' }}</td>
                    <td class="right"><strong>{{ $subject['nilai'] !== null ? number_format($subject['nilai'], 2, ',', '.') : '-' }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="box">
        <strong>Analisa:</strong><br>
        {{ $detail['recommendation'] }}
    </div>
</body>
</html>
