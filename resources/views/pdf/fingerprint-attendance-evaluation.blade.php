<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { background: #111827; color: #fff; padding: 22px; border-radius: 12px; }
        .eyebrow { font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: #fca5a5; font-weight: 800; }
        .title { margin-top: 8px; font-size: 26px; font-weight: 900; }
        .period { margin-top: 6px; color: #d1d5db; }
        .card { margin-top: 16px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; }
        .name { font-size: 22px; font-weight: 900; color: #991b1b; }
        .muted { color: #6b7280; }
        .grid { width: 100%; border-collapse: separate; border-spacing: 10px; margin-top: 12px; }
        .grid td { width: 25%; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; text-align: center; }
        .label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 800; }
        .value { margin-top: 5px; font-size: 22px; font-weight: 900; }
        .section-title { margin: 20px 0 8px; font-size: 15px; font-weight: 900; }
        .box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 14px; line-height: 1.6; }
        .bar { height: 12px; background: #e5e7eb; border-radius: 99px; overflow: hidden; margin-top: 8px; }
        .fill { height: 12px; background: #dc2626; }
        .footer { margin-top: 26px; color: #6b7280; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="eyebrow">Report Evaluasi Kehadiran</div>
        <div class="title">Analisa Absensi Fingerprint Pegawai</div>
        <div class="period">{{ $dateFrom->translatedFormat('d F Y') }} - {{ $dateTo->translatedFormat('d F Y') }}</div>
    </div>

    <div class="card">
        <div class="name">{{ $employee['name'] }}</div>
        <div class="muted">{{ $employee['employee_code'] ?: $employee['email'] }} | {{ $employee['employment_status'] }}</div>
        <table class="grid">
            <tr>
                <td><div class="label">Ranking</div><div class="value">#{{ $employee['rank'] }}</div></td>
                <td><div class="label">Skor</div><div class="value">{{ $employee['score'] }}</div></td>
                <td><div class="label">Hadir</div><div class="value">{{ $employee['attendance_rate'] }}%</div></td>
                <td><div class="label">Disiplin</div><div class="value">{{ $employee['discipline_rate'] }}%</div></td>
            </tr>
        </table>

        <div class="section-title">Ringkasan Data</div>
        <table class="grid">
            <tr>
                <td><div class="label">Hari Wajib</div><div class="value">{{ $employee['required_days'] }}</div></td>
                <td><div class="label">Hari Hadir</div><div class="value">{{ $employee['present_days'] }}</div></td>
                <td><div class="label">Terlambat</div><div class="value">{{ $employee['late_days'] }}</div></td>
                <td><div class="label">Tidak Hadir</div><div class="value">{{ $employee['absent_days'] }}</div></td>
            </tr>
        </table>

        <div class="section-title">Skor Kehadiran</div>
        <div class="bar"><div class="fill" style="width: {{ $employee['score'] }}%;"></div></div>

        <div class="section-title">{{ $employee['evaluation']['title'] }}</div>
        <div class="box">{{ $employee['evaluation']['message'] }}</div>

        <div class="section-title">Catatan Waktu</div>
        <div class="muted">
            Total keterlambatan: {{ \App\Support\AttendanceDuration::humanizeMinutes((int) $employee['total_late_minutes']) }}.
            Total pulang cepat: {{ \App\Support\AttendanceDuration::humanizeMinutes((int) $employee['total_early_minutes']) }}.
            Scan lengkap tercatat {{ $employee['complete_days'] }} hari.
        </div>
    </div>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh Sistem Informasi Kesiswaan pada {{ $generatedAt->translatedFormat('d F Y H:i') }}.
        Gunakan report ini sebagai bahan evaluasi dan pembinaan kedisiplinan pegawai.
    </div>
</body>
</html>
