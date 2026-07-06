<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>
    <style>
        @page {
            margin: {{ $config->is_borderless ? '0mm' : (($config->margin_top ?? 15) . 'mm ' . ($config->margin_right ?? 15) . 'mm ' . ($config->margin_bottom ?? 15) . 'mm ' . ($config->margin_left ?? 15) . 'mm') }};
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Times New Roman", Times, serif;
            color: #111;
            font-size: 10.4pt;
            line-height: 1.22;
        }

        .page {
            position: relative;
            min-height: 100%;
            page-break-after: always;
            overflow: hidden;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .borderless-inner {
            padding: {{ $config->is_borderless ? '8mm 13mm 8mm 13mm' : '0' }};
        }

        .letterhead {
            width: 100%;
            margin-bottom: 5mm;
        }

        .borderless-letterhead {
            margin: {{ $config->is_borderless ? '0 0 5mm 0' : '0 0 5mm 0' }};
        }

        .letterhead img {
            width: 100%;
            display: block;
        }

        .fallback-letterhead {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 7px;
            margin-bottom: 5mm;
        }

        .fallback-letterhead .school {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .watermark {
            position: fixed;
            top: 39%;
            left: 50%;
            width: 72mm;
            transform: translate(-50%, -50%);
            opacity: .10;
            z-index: -1;
        }

        .title {
            text-align: center;
            margin-top: 1mm;
            margin-bottom: 4mm;
        }

        .title h1 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
            letter-spacing: .5px;
        }

        .title p {
            margin: 2px 0 0;
            font-size: 10.5pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .identity td {
            padding: 1.1mm 0;
            vertical-align: top;
        }

        .identity .label {
            width: 35mm;
        }

        .identity .colon {
            width: 4mm;
        }

        .grade-table {
            margin-top: 4mm;
            font-size: 9.4pt;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid #111;
            padding: 1.25mm 1.5mm;
            vertical-align: middle;
        }

        .grade-table th {
            text-align: center;
            font-weight: bold;
        }

        .grade-table .no {
            width: 8mm;
            text-align: center;
        }

        .grade-table .score {
            width: 23mm;
            text-align: center;
        }

        .grade-table .group-row td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .summary-table {
            width: 48%;
            margin-top: 2mm;
            margin-left: auto;
            font-size: 9.4pt;
        }

        .summary-table td {
            border: 1px solid #111;
            padding: 1.2mm 1.5mm;
        }

        .sign-row {
            width: 100%;
            margin-top: 7mm;
            font-size: 10.2pt;
        }

        .sign-left,
        .sign-right {
            width: 50%;
            vertical-align: top;
        }

        .signature-space {
            height: 23mm;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
@php
    $groups = \App\Models\TranscriptSubject::groups();
@endphp

@foreach($students as $student)
    @php
        $gradeMap = $student->transcriptGrades->keyBy('transcript_subject_id');
        $validScores = $subjects->map(fn($subject) => $gradeMap->get($subject->id)?->score)->filter(fn($score) => $score !== null);
        $totalScore = $validScores->sum(fn($score) => (float) $score);
        $averageScore = $validScores->count() ? $totalScore / $validScores->count() : null;
        $kelas = $student->rombels->first()?->kelas?->nama_kelas ?? '-';
    @endphp
    <div class="page">
        @if($watermarkDataUri)
            <img src="{{ $watermarkDataUri }}" class="watermark" alt="">
        @endif

        @if($config->is_borderless && $letterheadDataUri)
            <div class="letterhead borderless-letterhead"><img src="{{ $letterheadDataUri }}" alt="Kop Transkrip"></div>
        @endif

        <div class="borderless-inner">
            @if(! $config->is_borderless)
                @if($letterheadDataUri)
                    <div class="letterhead"><img src="{{ $letterheadDataUri }}" alt="Kop Transkrip"></div>
                @else
                    <div class="fallback-letterhead">
                        <div class="school">{{ $config->school_name ?? 'SMK Telkom Lampung' }}</div>
                        <div>NPSN: {{ $config->npsn ?? '-' }}</div>
                    </div>
                @endif
            @elseif(! $letterheadDataUri)
                <div class="fallback-letterhead">
                    <div class="school">{{ $config->school_name ?? 'SMK Telkom Lampung' }}</div>
                    <div>NPSN: {{ $config->npsn ?? '-' }}</div>
                </div>
            @endif

            <div class="title">
                <h1>TRANSKRIP NILAI</h1>
                <p>Nomor: {{ $config->numberPreview() }}</p>
            </div>

            <table class="identity">
                <tr><td colspan="3">Yang bertanda tangan di bawah ini menerangkan bahwa:</td></tr>
                <tr><td class="label">Nama Peserta Didik</td><td class="colon">:</td><td class="bold">{{ strtoupper($student->nama_lengkap) }}</td></tr>
                <tr><td class="label">NIS/NISN</td><td class="colon">:</td><td>{{ $student->nis ?? '-' }} / {{ $student->dapodik?->nisn ?? '-' }}</td></tr>
                <tr><td class="label">Kelas</td><td class="colon">:</td><td>{{ $kelas }}</td></tr>
                <tr><td class="label">Nomor Ijazah</td><td class="colon">:</td><td>{{ $student->transcriptDiplomaNumber?->diploma_number ?? '-' }}</td></tr>
                <tr><td class="label">Satuan Pendidikan</td><td class="colon">:</td><td>{{ $config->school_name ?? 'SMK Telkom Lampung' }}</td></tr>
                <tr><td class="label">Tanggal Kelulusan</td><td class="colon">:</td><td>{{ $config->graduation_date?->translatedFormat('d F Y') ?? '-' }}</td></tr>
            </table>

            <table class="grade-table">
                <thead>
                    <tr>
                        <th class="no">No</th>
                        <th>Mata Pelajaran</th>
                        <th class="score">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @php($counter = 1)
                    @foreach($subjects->groupBy('group') as $groupKey => $groupSubjects)
                        <tr class="group-row"><td colspan="3">{{ $groups[$groupKey] ?? $groupKey }}</td></tr>
                        @foreach($groupSubjects as $subject)
                            @php($score = $gradeMap->get($subject->id)?->score)
                            <tr>
                                <td class="no">{{ $counter++ }}</td>
                                <td>{{ $subject->name }}</td>
                                <td class="score">{{ $score !== null ? number_format((float) $score, 2, '.', '') : '-' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <table class="summary-table">
                <tr><td class="bold">Jumlah</td><td style="text-align: center;">{{ $validScores->count() ? number_format($totalScore, 2, '.', '') : '-' }}</td></tr>
                <tr><td class="bold">Rata-rata</td><td style="text-align: center;">{{ $averageScore !== null ? number_format($averageScore, 2, '.', '') : '-' }}</td></tr>
            </table>

            <table class="sign-row">
                <tr>
                    <td class="sign-left"></td>
                    <td class="sign-right">
                        {{ $config->signature_city ?? 'Bandar Lampung' }}, {{ $config->signature_date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}<br>
                        Kepala Sekolah,
                        <div class="signature-space"></div>
                        <span class="bold"><u>{{ $config->principal_name ?? '-' }}</u></span><br>
                        NIP. {{ $config->principal_nip ?? '-' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endforeach
</body>
</html>
