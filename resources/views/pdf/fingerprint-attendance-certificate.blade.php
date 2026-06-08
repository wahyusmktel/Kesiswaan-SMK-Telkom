<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; background: #f8fafc; }
        .sheet { height: 680px; border: 8px solid #dc2626; padding: 26px; position: relative; background: #fff; }
        .inner { height: 612px; border: 2px solid #111827; padding: 28px; position: relative; }
        .brand { font-size: 12px; letter-spacing: 3px; font-weight: 800; color: #dc2626; text-transform: uppercase; }
        .title { margin-top: 34px; font-size: 46px; font-weight: 900; text-align: center; letter-spacing: 2px; }
        .subtitle { margin-top: 8px; font-size: 15px; text-align: center; color: #4b5563; }
        .name { margin: 42px auto 10px; padding-bottom: 10px; border-bottom: 3px solid #111827; width: 78%; text-align: center; font-size: 34px; font-weight: 900; color: #991b1b; }
        .text { width: 76%; margin: 18px auto; text-align: center; font-size: 15px; line-height: 1.7; color: #374151; }
        .stats { width: 82%; margin: 28px auto 0; border-collapse: collapse; }
        .stats td { padding: 12px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; }
        .stat-value { font-size: 24px; font-weight: 900; color: #111827; }
        .footer { position: absolute; left: 28px; right: 28px; bottom: 26px; display: table; width: calc(100% - 56px); }
        .footer-col { display: table-cell; vertical-align: bottom; width: 50%; font-size: 12px; color: #4b5563; }
        .sign { text-align: right; }
        .seal { position: absolute; right: 42px; top: 46px; width: 96px; height: 96px; border-radius: 50%; background: #dc2626; color: #fff; text-align: center; font-weight: 900; font-size: 22px; line-height: 96px; }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="inner">
            <div class="brand">SMK Telkom Lampung | Sistem Informasi Kesiswaan</div>
            <div class="seal">TOP {{ $employee['rank'] }}</div>
            <div class="title">SERTIFIKAT APRESIASI</div>
            <div class="subtitle">Diberikan sebagai penghargaan atas kedisiplinan kehadiran pegawai</div>
            <div class="name">{{ $employee['name'] }}</div>
            <div class="text">
                Atas capaian sebagai salah satu dari 10 pegawai terbaik dalam rekap kehadiran fingerprint periode
                <strong>{{ $dateFrom->translatedFormat('d F Y') }}</strong> sampai <strong>{{ $dateTo->translatedFormat('d F Y') }}</strong>.
                Semoga konsistensi ini menjadi teladan budaya kerja profesional dan disiplin.
            </div>
            <table class="stats">
                <tr>
                    <td><div class="stat-label">Ranking</div><div class="stat-value">#{{ $employee['rank'] }}</div></td>
                    <td><div class="stat-label">Skor</div><div class="stat-value">{{ $employee['score'] }}</div></td>
                    <td><div class="stat-label">Hadir</div><div class="stat-value">{{ $employee['attendance_rate'] }}%</div></td>
                    <td><div class="stat-label">Disiplin</div><div class="stat-value">{{ $employee['discipline_rate'] }}%</div></td>
                </tr>
            </table>
            <div class="footer">
                <div class="footer-col">
                    Dicetak otomatis pada {{ $generatedAt->translatedFormat('d F Y H:i') }}<br>
                    Berdasarkan data absensi fingerprint yang tersimpan di sistem.
                </div>
                <div class="footer-col sign">
                    Kaur SDM / Super Admin<br><br><br>
                    <strong>SMK Telkom Lampung</strong>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
