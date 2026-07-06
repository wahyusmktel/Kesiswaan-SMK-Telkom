<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Transkrip Nilai</title>
    <style>
        @page {
            margin: {{ $config->is_borderless ? '0mm' : ($config->margin_top ?? 15) . 'mm ' . ($config->margin_right ?? 15) . 'mm ' . ($config->margin_bottom ?? 15) . 'mm ' . ($config->margin_left ?? 15) . 'mm' }};
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Calibri, "DejaVu Sans", Arial, sans-serif;
            color: #111;
            font-size: 9.3pt;
            line-height: 1.08;
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
            padding: {{ $config->is_borderless ? '6mm 11mm 6mm 11mm' : '0' }};
        }

        .letterhead {
            width: 100%;
            margin-bottom: 1.4mm;
        }

        .letterhead img {
            width: 100%;
            display: block;
        }

        .fallback-letterhead {
            text-align: center;
            border-bottom: 2px solid #111;
            padding-bottom: 7px;
            margin-bottom: 2mm;
        }

        .fallback-letterhead .school {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            width: 140mm;
            transform: translate(-50%, -50%);
            opacity: .70;
            z-index: -1;
        }

        .title {
            text-align: center;
            margin: 0 0 2.2mm;
        }

        .title h1 {
            margin-top: -12px;
            font-size: 12pt;
            letter-spacing: .5px;
        }

        .title p {
            margin-top: -10px;
            font-size: 9.4pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .identity td {
            padding: .55mm 0;
            vertical-align: top;
        }

        .identity .label {
            width: 57mm;
        }

        .identity .colon {
            width: 4mm;
        }

        .grade-table {
            margin-top: 2.4mm;
            font-size: 8.55pt;
        }

        .grade-table th,
        .grade-table td {
            border: 1px solid #111;
            padding: .82mm 1.25mm;
            vertical-align: middle;
        }

        .grade-table th {
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
            padding-top: 4.0mm;
            padding-bottom: 4.0mm;
        }

        .grade-table .no {
            width: 8mm;
            text-align: center;
        }

        .grade-table .score {
            width: 24mm;
            text-align: center;
        }

        .grade-table .group-row td {
            font-weight: bold;
            background: #f2f2f2;
            padding-left: 2.8mm;
        }

        .grade-table .local-title {
            font-weight: bold;
        }

        .grade-table .average-row td {
            font-weight: bold;
            text-align: center;
        }

        .sign-row {
            width: 100%;
            margin-top: 4.5mm;
            font-size: 9.1pt;
        }

        .sign-left,
        .sign-right {
            width: 50%;
            vertical-align: top;
        }

        .sign-left {
            text-align: left;
            vertical-align: bottom;
            padding-left: 0;
            padding-right: 0;
            padding-bottom: 1mm;
        }

        .sign-right {
            text-align: right;
        }

        .principal-signature {
            display: inline-block;
            width: 58mm;
            text-align: left;
        }

        .signature-space {
            height: 19mm;
        }

        .digital-signature-qr {
            display: inline-block;
            width: 24mm;
            text-align: center;
            font-size: 6.4pt;
            line-height: 1.08;
        }

        .digital-signature-qr img {
            display: block;
            width: 22mm;
            height: 22mm;
            margin: 0 auto .8mm;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @php
        $groups = \App\Models\TranscriptSubject::groups();
        $groupLetters = ['umum' => 'A', 'kejuruan' => 'B'];
    @endphp

    @foreach ($students as $student)
        @php
            $gradeMap = $student->transcriptGrades->keyBy('transcript_subject_id');
            $validScores = $subjects
                ->map(fn($subject) => $gradeMap->get($subject->id)?->score)
                ->filter(fn($score) => $score !== null);
            $averageScore = $validScores->count()
                ? $validScores->sum(fn($score) => (float) $score) / $validScores->count()
                : null;
            $kelasModel = $student->rombels->first()?->kelas;
            $konsentrasi = $kelasModel?->jurusan ?? 'Teknik Komputer dan Jaringan';
            $program = str($konsentrasi)
                ->lower()
                ->contains(['komputer', 'jaringan', 'tkj'])
                ? 'Teknik Jaringan Komputer dan Telekomunikasi'
                : $konsentrasi;
            $tempatLahir = $student->dapodik?->tempat_lahir ?? ($student->tempat_lahir ?? '-');
            $tanggalLahir = $student->dapodik?->tanggal_lahir ?? $student->tanggal_lahir;
            $tanggalLahirText = $tanggalLahir
                ? \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('d F Y')
                : '-';
            $tanggalKelulusanText = $config->graduation_date?->locale('id')->translatedFormat('d F Y') ?? '-';
            $tanggalTtdText =
                $config->signature_date?->locale('id')->translatedFormat('d F Y') ??
                now()->locale('id')->translatedFormat('d F Y');
            $subjectsByGroup = $subjects->groupBy('group');
            $mainGroups = ['umum', 'kejuruan'];
        @endphp
        <div class="page">
            @if ($watermarkDataUri)
                <img src="{{ $watermarkDataUri }}" class="watermark" alt="">
            @endif

            @if ($config->is_borderless && $letterheadDataUri)
                <div class="letterhead"><img src="{{ $letterheadDataUri }}" alt="Kop Transkrip"></div>
            @endif

            <div class="borderless-inner">
                @if (!$config->is_borderless)
                    @if ($letterheadDataUri)
                        <div class="letterhead"><img src="{{ $letterheadDataUri }}" alt="Kop Transkrip"></div>
                    @else
                        <div class="fallback-letterhead">
                            <div class="school">{{ $config->school_name ?? 'SMK Telkom Lampung' }}</div>
                            <div>NPSN: {{ $config->npsn ?? '-' }}</div>
                        </div>
                    @endif
                @elseif(!$letterheadDataUri)
                    <div class="fallback-letterhead">
                        <div class="school">{{ $config->school_name ?? 'SMK Telkom Lampung' }}</div>
                        <div>NPSN: {{ $config->npsn ?? '-' }}</div>
                    </div>
                @endif

                <div class="title">
                    <h1>TRANSKRIP NILAI</h1>
                    <p>Nomor : {{ $transcriptNumbers[$student->id] ?? '-' }}</p>
                </div>

                <table class="identity">
                    <tr>
                        <td class="label">Satuan Pendidikan</td>
                        <td class="colon">:</td>
                        <td>{{ $config->school_name ?? 'SMK Telkom Lampung' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Pokok Sekolah Nasional</td>
                        <td class="colon">:</td>
                        <td>{{ $config->npsn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="colon">:</td>
                        <td>{{ \Illuminate\Support\Str::of($student->nama_lengkap)->lower()->title() }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tempat dan Tanggal Lahir</td>
                        <td class="colon">:</td>
                        <td>{{ \Illuminate\Support\Str::of($tempatLahir . ', ' . $tanggalLahirText)->lower()->title() }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Induk Siswa Nasional</td>
                        <td class="colon">:</td>
                        <td>{{ $student->dapodik?->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Ijazah</td>
                        <td class="colon">:</td>
                        <td>{{ $student->transcriptDiplomaNumber?->diploma_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Kelulusan</td>
                        <td class="colon">:</td>
                        <td>{{ \Illuminate\Support\Str::of($tanggalKelulusanText)->lower()->title() }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Keahlian</td>
                        <td class="colon">:</td>
                        <td>{{ $program }}</td>
                    </tr>
                    <tr>
                        <td class="label">Konsentrasi Keahlian</td>
                        <td class="colon">:</td>
                        <td>{{ $konsentrasi }}</td>
                    </tr>
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
                        @foreach ($mainGroups as $groupKey)
                            @php
                                $groupSubjects = $subjectsByGroup->get($groupKey, collect());
                                $localSubjects =
                                    $groupKey === 'umum' ? $subjectsByGroup->get('muatan_lokal', collect()) : collect();
                                $counter = 1;
                                $localLetters = range('a', 'z');
                            @endphp
                            @if ($groupSubjects->isNotEmpty() || $localSubjects->isNotEmpty())
                                <tr class="group-row">
                                    <td colspan="3">{{ $groupLetters[$groupKey] ?? '' }}.
                                        {{ $groups[$groupKey] ?? $groupKey }}</td>
                                </tr>
                                @foreach ($groupSubjects as $subject)
                                    @php($score = $gradeMap->get($subject->id)?->score)
                                    <tr>
                                        <td class="no">{{ $counter++ }}</td>
                                        <td>{{ $subject->name }}</td>
                                        <td class="score">
                                            {{ $score !== null ? number_format((float) $score, 2, '.', '') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($localSubjects->isNotEmpty())
                                    <tr>
                                        <td class="no">{{ $counter++ }}</td>
                                        <td class="local-title">Muatan Lokal</td>
                                        <td class="score"></td>
                                    </tr>
                                    @foreach ($localSubjects as $localIndex => $subject)
                                        @php($score = $gradeMap->get($subject->id)?->score)
                                        <tr>
                                            <td class="no"></td>
                                            <td>{{ $localLetters[$localIndex] ?? '-' }}. {{ $subject->name }}</td>
                                            <td class="score">
                                                {{ $score !== null ? number_format((float) $score, 2, '.', '') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                        <tr class="average-row">
                            <td colspan="2">Rata-rata</td>
                            <td>{{ $averageScore !== null ? number_format($averageScore, 2, '.', '') : '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="sign-row">
                    <tr>
                        <td class="sign-left">
                            @if (!empty($transcriptQrCodes[$student->id]))
                                <div class="digital-signature-qr">
                                    <img src="{{ $transcriptQrCodes[$student->id] }}" alt="QR Verifikasi Transkrip">
                                    <span>Scan untuk verifikasi<br>Keabsahan dokumen</span>
                                </div>
                            @endif
                        </td>
                        <td class="sign-right">
                            <div class="principal-signature">
                                {{ $config->signature_city ?? 'Bandar Lampung' }}, {{ $tanggalTtdText }}<br>
                                Kepala Sekolah,
                                <div class="signature-space"></div>
                                <span class="bold"><u>{{ $config->principal_name ?? '-' }}</u></span><br>
                                NIP. {{ $config->principal_nip ?? '-' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html>
