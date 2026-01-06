<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report - Semester Analysis</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #1e293b;
            background-color: #ffffff;
            line-height: 1.5;
        }
        /* -- Header Section -- */
        .hero {
            background-color: #0f172a;
            color: #f8fafc;
            padding: 60px 50px;
            position: relative;
        }
        .hero-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #6366f1;
            margin-bottom: 10px;
            display: block;
        }
        .hero h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1;
        }
        .hero-meta {
            margin-top: 25px;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            display: table;
            width: 100%;
        }
        .hero-meta-item {
            display: table-cell;
        }

        /* -- Container -- */
        .content {
            padding: 40px 50px;
        }

        /* -- Section Titles -- */
        .section-header {
            margin-bottom: 30px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
        }
        .section-header h2 {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            color: #0f172a;
        }

        /* -- KPI Grid -- */
        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 0;
            margin-left: -15px;
            margin-bottom: 40px;
        }
        .card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            padding: 20px;
            border-radius: 16px;
            text-align: left;
        }
        .card-val {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
            display: block;
            margin-bottom: 2px;
        }
        .card-label {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* -- Trends -- */
        .trend-row {
            margin-bottom: 25px;
        }
        .trend-info {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .trend-title {
            display: table-cell;
            font-size: 12px;
            font-weight: 900;
            color: #334155;
        }
        .trend-perc {
            display: table-cell;
            text-align: right;
            font-size: 12px;
            font-weight: 800;
            color: #4f46e5;
        }
        .progress-bg {
            background: #f1f5f9;
            height: 12px;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #4f46e5;
            border-radius: 10px;
        }
        .trend-subs {
            margin-top: 6px;
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* -- Table -- */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }
        .modern-table th {
            text-align: left;
            padding: 15px;
            font-size: 10px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #f1f5f9;
        }
        .modern-table td {
            padding: 15px;
            font-size: 11px;
            font-weight: 500;
            color: #1e293b;
            border-bottom: 1px solid #f8fafc;
        }
        .modern-table tr:nth-child(even) {
            background-color: #fbfcfd;
        }
        .guru-name {
            font-weight: 800;
            color: #0f172a;
        }

        /* -- Badges -- */
        .dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .pill-green { background: #f0fdf4; color: #166534; }
        .pill-blue { background: #eff6ff; color: #1e40af; }
        .pill-amber { background: #fffbeb; color: #92400e; }
        .pill-rose { background: #fff1f2; color: #9f1239; }

        /* -- Footer -- */
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 40px 50px;
            font-size: 10px;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="hero">
        <span class="hero-label">Academic Audit Record</span>
        <h1>SEMESTER MONITORING<br>& ANALYSIS</h1>
        
        <div class="hero-meta">
            <div class="hero-meta-item">
                <span style="color: #6366f1;">PERIOD</span><br>
                {{ $tahunAktif->tahun }}
            </div>
            <div class="hero-meta-item">
                <span style="color: #6366f1;">SEMESTER</span><br>
                {{ $tahunAktif->semester }}
            </div>
            <div class="hero-meta-item">
                <span style="color: #6366f1;">GENERATED AT</span><br>
                {{ date('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <div class="content">
        <div class="section-header">
            <h2>Executive Summary</h2>
        </div>

        <table class="grid">
            <tr>
                <td style="width: 25%">
                    <div class="card">
                        <span class="card-val">{{ $kehadiranPersen }}%</span>
                        <span class="card-label">Efficiency</span>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="card">
                        <span class="card-val">{{ $totalHadir }}</span>
                        <span class="card-label">On-Time</span>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="card">
                        <span class="card-val">{{ $totalTerlambat }}</span>
                        <span class="card-label">Lateness</span>
                    </div>
                </td>
                <td style="width: 25%">
                    <div class="card">
                        <span class="card-val" style="color: #e11d48;">{{ $totalAlpa }}</span>
                        <span class="card-label">Absenteeism</span>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-header">
            <h2>Monthly Trends Analysis</h2>
        </div>

        @foreach($trends as $month => $data)
        <div class="trend-row">
            <div class="trend-info">
                <span class="trend-title">{{ $month }}</span>
                <span class="trend-perc">{{ round(($data['total'] > 0 ? ($data['hadir'] / $data['total']) * 100 : 0), 1) }}% Performance</span>
            </div>
            <div class="progress-bg">
                <div class="progress-fill" style="width: {{ $data['total'] > 0 ? ($data['hadir'] / $data['total']) * 100 : 0 }}%"></div>
            </div>
            <div class="trend-subs">
                Hadir: {{ $data['hadir'] }} &nbsp;&bull;&nbsp; 
                Terlambat: {{ $data['terlambat'] }} &nbsp;&bull;&nbsp; 
                Izin: {{ $data['izin'] }} &nbsp;&bull;&nbsp; 
                Alpa: {{ $data['alpa'] }}
            </div>
        </div>
        @endforeach

        <div style="page-break-before: always;"></div>

        <div class="content">
            <div class="section-header" style="margin-top: 20px;">
                <h2>Teacher Attendance Recap (Top 25)</h2>
            </div>

            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 30px">#</th>
                        <th>Teacher Name</th>
                        <th style="text-align: center">HD</th>
                        <th style="text-align: center">TL</th>
                        <th style="text-align: center">IZ</th>
                        <th style="text-align: center">AL</th>
                        <th style="text-align: right">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guruStats->take(25) as $guru)
                    <tr>
                        <td style="color: #94a3b8; font-weight: 700;">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="guru-name">{{ $guru['nama'] }}</td>
                        <td style="text-align: center"><span style="color: #10b981;">{{ $guru['hadir'] }}</span></td>
                        <td style="text-align: center"><span style="color: #f59e0b;">{{ $guru['terlambat'] }}</span></td>
                        <td style="text-align: center"><span style="color: #3b82f6;">{{ $guru['izin'] }}</span></td>
                        <td style="text-align: center"><span style="color: #f43f5e;">{{ $guru['alpa'] }}</span></td>
                        <td style="text-align: right; font-weight: 800;">
                            <span class="status-pill {{ $guru['persentase'] >= 90 ? 'pill-green' : ($guru['persentase'] >= 75 ? 'pill-amber' : 'pill-rose') }}">
                                {{ $guru['persentase'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top: 25px; font-size: 9px; color: #94a3b8; font-style: italic;">
                * This report provides a prioritized summary of faculty attendance performance. Detailed raw data and historical records are available in the accompanying digital audit file (spreadsheet format).
            </p>
        </div>
    </div>

    <div class="footer">
        Confidential Document &bull; SMK Telkom School System &bull; {{ date('Y') }}
    </div>
</body>
</html>
