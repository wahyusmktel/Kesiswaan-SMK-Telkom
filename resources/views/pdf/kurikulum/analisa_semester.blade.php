<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report Kehadiran Guru</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
        }
        .header {
            background-color: #1e40af;
            color: white;
            padding: 40px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .container {
            padding: 30px 40px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .kpi-grid {
            width: 100%;
            margin-bottom: 30px;
        }
        .kpi-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: black;
            color: #1e40af;
            display: block;
        }
        .kpi-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: bold;
        }
        .chart-section {
            margin-bottom: 30px;
        }
        .bar-container {
            width: 100%;
            background-color: #f3f4f6;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .bar-fill {
            height: 100%;
            background-color: #3b82f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 11px;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 12px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 20px 40px;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .trend-item {
            margin-bottom: 15px;
        }
        .trend-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>Laporan Audit Akademik</p>
        <h1>Semester Monitoring & Analysis</h1>
        <p>Tahun Pelajaran: {{ $tahunAktif->tahun_ajaran }} | Semester: {{ $tahunAktif->semester }}</p>
    </div>

    <div class="container">
        <div class="section-title">Ringkasan Eksekutif</div>
        
        <table class="kpi-grid">
            <tr>
                <td class="kpi-card">
                    <span class="kpi-value">{{ $kehadiranPersen }}%</span>
                    <span class="kpi-label">Persentase Kehadiran</span>
                </td>
                <td class="kpi-card">
                    <span class="kpi-value">{{ $totalRecord }}</span>
                    <span class="kpi-label">Total Tatap Muka</span>
                </td>
                <td class="kpi-card" style="border-right: none;">
                    <span class="kpi-value text-green-600">{{ $totalHadir }}</span>
                    <span class="kpi-label">Hadir Tepat Waktu</span>
                </td>
            </tr>
        </table>

        <div class="chart-section">
            <div class="section-title">Analisa Tren Bulanan</div>
            @foreach($trends as $month => $data)
                <div class="trend-item">
                    <span class="trend-label">{{ $month }} - Efektivitas: {{ round(($data['total'] > 0 ? ($data['hadir'] / $data['total']) * 100 : 0), 1) }}%</span>
                    <div class="bar-container">
                        <div class="bar-fill" style="width: {{ $data['total'] > 0 ? ($data['hadir'] / $data['total']) * 100 : 0 }}%"></div>
                    </div>
                    <div style="font-size: 10px; color: #6b7280; margin-top: 3px;">
                        Hadir: {{ $data['hadir'] }} | Terlambat: {{ $data['terlambat'] }} | Izin: {{ $data['izin'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="page-break-before: always;"></div>

        <div class="section-title">Rekapitulasi Kehadiran Guru</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th>Nama Guru</th>
                    <th style="text-align: center">Hadir</th>
                    <th style="text-align: center">Terlambat</th>
                    <th style="text-align: center">Izin/Alpa</th>
                    <th style="text-align: right">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guruStats->take(15) as $guru)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $guru['nama'] }}</strong></td>
                    <td style="text-align: center">{{ $guru['hadir'] }}</td>
                    <td style="text-align: center">{{ $guru['terlambat'] }}</td>
                    <td style="text-align: center">{{ $guru['total'] - ($guru['hadir'] + $guru['terlambat']) }}</td>
                    <td style="text-align: right; font-weight: bold; color: {{ $guru['persentase'] >= 90 ? '#059669' : ($guru['persentase'] >= 75 ? '#d97706' : '#dc2626') }}">
                        {{ $guru['persentase'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($guruStats->count() > 15)
            <p style="font-size: 10px; font-style: italic; color: #6b7280;">* Menampilkan 15 guru teratas. Lihat file Excel untuk data lengkap.</p>
        @endif
    </div>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh Sistem Manajemen Sekolah pada {{ date('d/m/Y H:i') }}.
    </div>
</body>
</html>
