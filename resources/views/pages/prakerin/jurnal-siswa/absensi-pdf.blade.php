<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi PKL</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #e5e7eb; padding: 10px; margin: 12px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 7px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; }
    </style>
</head>
<body>
    <h1>Rekap Absensi PKL</h1>
    <div class="muted">Dicetak pada {{ now()->format('d M Y H:i') }}</div>

    <div class="box">
        <table>
            <tr>
                <td width="22%">Nama Siswa</td>
                <td>{{ $penempatan->siswa?->nama_lengkap ?? '-' }}</td>
                <td width="18%">NIS</td>
                <td>{{ $penempatan->siswa?->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Industri</td>
                <td>{{ $penempatan->industri?->nama_industri ?? '-' }}</td>
                <td>Rombel</td>
                <td>{{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td colspan="3">
                    {{ $effectiveSchedule['tanggal_mulai'] ? \Carbon\Carbon::parse($effectiveSchedule['tanggal_mulai'])->format('d M Y') : '-' }}
                    -
                    {{ $effectiveSchedule['tanggal_selesai'] ? \Carbon\Carbon::parse($effectiveSchedule['tanggal_selesai'])->format('d M Y') : '-' }}
                    ({{ $effectiveSchedule['period_source'] }})
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis as $absensi)
                <tr>
                    <td>{{ $absensi->tanggal?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $absensi->check_in_at ?? '-' }}</td>
                    <td>{{ $absensi->check_out_at ?? '-' }}</td>
                    <td>{{ $absensi->status ?? '-' }}</td>
                    <td>{{ $absensi->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">Belum ada riwayat absensi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
