<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Nilai Ujian Semester</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #991b1b; padding-bottom: 10px; margin-bottom: 14px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .subtitle { margin-top: 4px; color: #4b5563; }
        .meta { width: 100%; margin-bottom: 12px; }
        .meta td { padding: 3px 0; }
        .stats { width: 100%; margin: 10px 0 14px; border-collapse: collapse; }
        .stats td { border: 1px solid #e5e7eb; padding: 8px; text-align: center; }
        .stats .label { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .stats .value { font-size: 15px; font-weight: bold; margin-top: 3px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th { background: #dc2626; color: white; padding: 7px 5px; border: 1px solid #ffffff; font-size: 9px; text-transform: uppercase; }
        table.data td { padding: 6px 5px; border: 1px solid #e5e7eb; }
        table.data tr:nth-child(even) td { background: #fef2f2; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 18px; font-size: 9px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Hasil Ujian Semester</div>
        <div class="subtitle">SMK Telkom Lampung</div>
    </div>

    <table class="meta">
        <tr>
            <td width="18%">Nama Ujian</td>
            <td width="2%">:</td>
            <td width="40%">{{ $ujian->nama_ujian }}</td>
            <td width="18%">Tahun/Semester</td>
            <td width="2%">:</td>
            <td>{{ $ujian->tahunPelajaran?->tahun }} - {{ $ujian->semester }}</td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $ujianMapel->nama_mapel }}</td>
            <td>Jumlah Soal</td>
            <td>:</td>
            <td>{{ $ujianMapel->jumlah_soal }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $kelas ?: 'Semua kelas' }}</td>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table class="stats">
        <tr>
            <td><div class="label">Peserta</div><div class="value">{{ $stats['total'] }}</div></td>
            <td><div class="label">Nilai Terbesar</div><div class="value">{{ $stats['max'] !== null ? number_format($stats['max'], 2, ',', '.') : '-' }}</div></td>
            <td><div class="label">Nilai Terkecil</div><div class="value">{{ $stats['min'] !== null ? number_format($stats['min'], 2, ',', '.') : '-' }}</div></td>
            <td><div class="label">Rata-rata</div><div class="value">{{ $stats['avg'] !== null ? number_format($stats['avg'], 2, ',', '.') : '-' }}</div></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="14%">NIS</th>
                <th>Nama Lengkap</th>
                <th width="14%">Kelas</th>
                <th width="12%">Benar</th>
                <th width="12%">Nilai</th>
                <th width="16%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">{{ $item->kode_peserta }}</td>
                    <td>{{ $item->nama_lengkap }}</td>
                    <td class="center">{{ $item->kelas }}</td>
                    <td class="center">{{ $item->jumlah_benar }}/{{ $item->jumlah_soal }}</td>
                    <td class="right"><strong>{{ $item->nilai_akhir !== null ? number_format((float) $item->nilai_akhir, 2, ',', '.') : '-' }}</strong></td>
                    <td class="center">{{ $item->master_siswa_id ? 'Cocok' : 'NIS belum cocok' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Tidak ada data nilai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak dari Sistem Informasi Kesiswaan</div>
</body>
</html>
