<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPP - {{ $plan->topic }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #34495e;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #7f8c8d;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            text-align: left;
            font-size: 12px;
        }
        .info-table th {
            background-color: #ecf0f1;
            width: 25%;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #34495e;
            color: #fff;
            padding: 6px 10px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        .content-box {
            border: 1px solid #bdc3c7;
            padding: 10px;
            background-color: #fafafa;
            white-space: pre-wrap;
            border-radius: 3px;
        }
        ul {
            margin: 0;
            padding-left: 20px;
        }
        .activity-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .activity-grid th, .activity-grid td {
            padding: 8px;
            border: 1px solid #bdc3c7;
            text-align: left;
            vertical-align: top;
        }
        .activity-grid th {
            background-color: #ecf0f1;
            width: 30%;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rencana Pelaksanaan Pembelajaran (RPP)</h1>
        <p>Guru: {{ $plan->teacher->name ?? 'Nama Guru' }} | SMK Telkom</p>
    </div>

    <table class="info-table">
        <tr>
            <th>Topik / Judul Materi</th>
            <td colspan="3"><strong>{{ $plan->topic }}</strong></td>
        </tr>
        <tr>
            <th>Mata Pelajaran</th>
            <td>{{ $plan->subject->nama_mapel ?? '-' }}</td>
            <th>Kelas</th>
            <td>{{ $plan->class->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tanggal Mengajar</th>
            <td>{{ $plan->teach_date->translatedFormat('d F Y') }}</td>
            <th>Durasi Waktu</th>
            <td>{{ $plan->duration_minutes }} Menit</td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">1. Tujuan Pembelajaran</div>
        <div class="content-box">
{{ $plan->learning_objectives }}
        </div>
    </div>

    @if($plan->pre_assessment)
    <div class="section">
        <div class="section-title">2. Asesmen Awal</div>
        <div class="content-box">
{{ $plan->pre_assessment }}
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">3. Metode Pembelajaran</div>
        <div class="content-box">
            @if(is_array($plan->methods) && count($plan->methods) > 0)
                {{ implode(', ', $plan->methods) }}
            @else
                Tidak ditentukan.
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">4. Aktivitas Kelas</div>
        <table class="activity-grid">
            @if($plan->activities)
                @foreach(['pembuka' => 'Pembuka', 'eksplorasi' => 'Eksplorasi', 'elaborasi' => 'Elaborasi', 'konfirmasi' => 'Konfirmasi', 'penutup' => 'Penutup'] as $key => $label)
                    @if(!empty($plan->activities[$key]))
                    <tr>
                        <th>{{ $label }}</th>
                        <td>{{ $plan->activities[$key] }}</td>
                    </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="2">Tidak ada aktivitas kelas yang dijabarkan.</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="section">
        <div class="section-title">5. Media & Sumber Belajar</div>
        <div class="content-box">
            @if(!empty($plan->resources['needs']))
                <strong>Kebutuhan:</strong> {{ implode(', ', (array) $plan->resources['needs']) }}<br>
            @endif
            @if(!empty($plan->resources['link']))
                <strong>Tautan/Referensi:</strong> {{ $plan->resources['link'] }}
            @endif
        </div>
    </div>

    @if($plan->final_assessment)
    <div class="section">
        <div class="section-title">6. Asesmen Akhir</div>
        <div class="content-box">
{{ $plan->final_assessment }}
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="signature-box">
            <p>Dibuat pada {{ now()->translatedFormat('d F Y') }}</p>
            <div class="signature-line"></div>
            <p><strong>{{ $plan->teacher->name ?? 'Guru Pengajar' }}</strong></p>
        </div>

        @if(isset($qr))
            <div class="signature-box" style="float: right;">
                <p>Verifikasi QR Code:</p>
                <img src="data:image/png;base64,{{ $qr }}" alt="QR Code" style="width: 120px; height: 120px;" />
                <p><small>Scan untuk memverifikasi dokumen</small></p>
            </div>
        @endif
    </div>

</body>
</html>
