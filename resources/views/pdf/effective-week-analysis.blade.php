<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Analisis Pekan Efektif</title>
    <style>
        @page { size: A4 portrait; margin: 12mm 15mm 11mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: Arial, Helvetica, sans-serif; font-size: 8pt; line-height: 1.12; }
        h1 { margin: 0 0 3mm; text-align: center; font-size: 11.5pt; }
        table { width: 100%; border-collapse: collapse; }
        .identity { margin-bottom: 3mm; }
        .identity td { border: .45pt solid #aaa; padding: .65mm 1.6mm; }
        .identity .label { width: 33%; }
        .identity .colon { width: 4%; text-align: center; font-weight: bold; }
        .section { margin-top: 2.5mm; page-break-inside: avoid; }
        .section-title { margin: 0 0 1mm; font-size: 8pt; font-weight: bold; }
        .analysis th, .analysis td { border: .65pt solid #111; padding: .55mm 1mm; vertical-align: middle; }
        .analysis th { background: #c7c7c7; font-weight: bold; text-align: center; }
        .analysis .center { text-align: center; }
        .analysis .total td { background: #f2f2f2; font-weight: bold; }
        .summary-title { margin-top: .6mm; font-weight: bold; }
        .summary { margin-top: .25mm; width: 54%; }
        .summary td { padding: .28mm 0; border: 0; }
        .summary .number { width: 5mm; }
        .summary .summary-label { width: 34mm; }
        .signatures { margin-top: 4mm; table-layout: fixed; }
        .signatures td { width: 50%; border: 0; padding: 0 2mm; vertical-align: top; }
        .signature-space { height: 20mm; }
        .name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    <h1>ANALISIS PEKAN EFEKTIF</h1>

    <table class="identity">
        @foreach ([
            'Nama Sekolah' => $analysis['school_name'],
            'Mata Pelajaran' => $analysis['subject'],
            'Fase' => $analysis['phase'],
            'Kelas' => $analysis['class'],
            'Nama Guru' => $analysis['teacher_name'],
            'Tahun Pelajaran' => $analysis['academic_year']->tahun,
            'Jadwal Mengajar' => $analysis['schedule_label'],
        ] as $label => $value)
            <tr>
                <td class="label">{{ $label }}</td>
                <td class="colon">:</td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    @foreach ($analysis['semesters'] as $semesterIndex => $semester)
        <div class="section">
            <div class="section-title">{{ chr(65 + $semesterIndex) }}. &nbsp;&nbsp; SEMESTER {{ strtoupper($semester['name']) }}</div>
            <table class="analysis">
                <colgroup>
                    <col style="width:7%">
                    <col style="width:15%">
                    <col style="width:12%">
                    <col style="width:15%">
                    <col style="width:13%">
                    <col style="width:38%">
                </colgroup>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Bulan</th>
                        <th>Banyak<br>Pekan</th>
                        <th>Pekan Tidak<br>Efektif</th>
                        <th>Pekan<br>Efektif</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($semester['months'] as $row)
                        <tr>
                            <td class="center">{{ $row['number'] }}</td>
                            <td class="center">{{ $row['month']->locale('id')->translatedFormat('M Y') }}</td>
                            <td class="center">{{ $row['total_weeks'] }}</td>
                            <td class="center">{{ $row['ineffective_weeks'] }}</td>
                            <td class="center">{{ $row['effective_weeks'] }}</td>
                            <td>{{ $row['notes'] ?: '-' }}</td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td></td>
                        <td class="center">Jumlah</td>
                        <td class="center">{{ $semester['total_weeks'] }}</td>
                        <td class="center">{{ $semester['ineffective_weeks'] }}</td>
                        <td class="center">{{ $semester['effective_weeks'] }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="summary-title">Perhitungan Jam Tatap Muka</div>
            <table class="summary">
                @foreach ([
                    'Pekan Efektif' => $semester['effective_weeks'],
                    'Pekan P5' => $semester['p5_weeks'] ?: '-',
                    'Pekan Cadangan' => $semester['reserve_weeks'] ?: '-',
                    'Pekan Tatap Muka' => $semester['contact_weeks'],
                    'Jam Per Pekan' => $semester['jp_per_week'].' JP',
                    'Jumlah Jam Tatap Muka' => $semester['total_jp'].' JP',
                ] as $label => $value)
                    <tr>
                        <td class="number">{{ $loop->iteration }}.</td>
                        <td class="summary-label">{{ $label }}</td>
                        <td>: {{ $value }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach

    <table class="signatures">
        <tr>
            <td>
                Validator,<br>
                Wakil Kepala Sekolah Bidang Kurikulum
                <div class="signature-space"></div>
                <span class="name">{{ $analysis['validator_name'] }}</span><br>
                NIP. {{ $analysis['validator_nip'] }}
            </td>
            <td>
                {{ $analysis['signature_city'] }}, {{ $analysis['signature_date']->locale('id')->translatedFormat('d F Y') }}<br>
                Guru Mata Pelajaran
                <div class="signature-space"></div>
                <span class="name">{{ $analysis['teacher_name'] }}</span><br>
                NIP. {{ $analysis['teacher_nip'] }}
            </td>
        </tr>
    </table>
</body>
</html>
