<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Penilaian UKK — {{ $siswa->nama_lengkap }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a202c;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        /* ── KOP ── */
        .kop {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .kop img {
            width: 100%;
            display: block;
        }
        .kop-text {
            background: linear-gradient(135deg, #c05621 0%, #dd6b20 50%, #ed8936 100%);
            padding: 18px 24px 14px;
            color: #fff;
            text-align: center;
        }
        .kop-text h1 {
            margin: 0 0 4px;
            font-size: 16pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            margin: 0;
            font-size: 8.5pt;
            opacity: .9;
        }
        .kop-divider {
            height: 4px;
            background: #744210;
            margin: 0;
        }

        /* ── CONTENT ── */
        .content {
            padding: 18px 24px 16px;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: 900;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 2px;
        }
        .doc-title p {
            font-size: 9pt;
            color: #718096;
            margin: 0;
        }

        /* Info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9.5pt;
        }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table .lbl { width: 130px; font-weight: bold; color: #4a5568; }
        .info-table .sep { width: 14px; text-align: center; }

        /* Section heading */
        .section-head {
            font-size: 9pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #744210;
            border-left: 3px solid #dd6b20;
            padding-left: 7px;
            margin: 14px 0 7px;
        }

        /* Instrumen block */
        .instrumen-block {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .instrumen-head {
            background: #f5f3ff;
            padding: 6px 10px;
            font-weight: 900;
            font-size: 9.5pt;
            border-bottom: 1px solid #e2e8f0;
        }
        .instrumen-inner {
            padding: 8px 10px;
        }

        /* Sub-head (Pengetahuan / Keterampilan) */
        .sub-head {
            font-size: 8.5pt;
            font-weight: 800;
            color: #2b6cb0;
            margin: 6px 0 4px;
        }
        .sub-head.green { color: #276749; }

        /* Soal table */
        .soal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .soal-table th {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            padding: 4px 6px;
            font-weight: 800;
            text-align: left;
        }
        .soal-table th.center { text-align: center; }
        .soal-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            vertical-align: top;
        }
        .soal-table td.center { text-align: center; }
        .badge-benar {
            display: inline-block;
            background: #c6f6d5;
            color: #276749;
            font-weight: 900;
            padding: 1px 5px;
            border-radius: 3px;
        }
        .badge-salah {
            display: inline-block;
            background: #fed7d7;
            color: #c53030;
            font-weight: 900;
            padding: 1px 5px;
            border-radius: 3px;
        }
        .badge-nil {
            display: inline-block;
            background: #edf2f7;
            color: #718096;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
        }

        /* Indikator table */
        .ind-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .ind-table th {
            background: #f0fff4;
            border: 1px solid #c6f6d5;
            padding: 4px 6px;
            font-weight: 800;
            text-align: left;
        }
        .ind-table th.center { text-align: center; }
        .ind-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            vertical-align: top;
        }
        .ind-table td.center { text-align: center; }
        .ind-kat {
            background: #f7fafc;
            font-weight: 700;
            font-size: 8pt;
            color: #4a5568;
            padding: 3px 6px;
            border: 1px solid #e2e8f0;
        }

        /* Score summary */
        .score-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-top: 12px;
        }
        .score-table th {
            background: #744210;
            color: #fff;
            padding: 5px 8px;
            font-weight: 800;
            text-align: center;
            font-size: 8.5pt;
        }
        .score-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            text-align: center;
        }
        .score-table tr:nth-child(even) td { background: #fffaf0; }
        .score-table .final-row td {
            background: #fef3c7;
            font-weight: 900;
            font-size: 10pt;
        }

        /* Signature section */
        .sig-section {
            margin-top: 22px;
            page-break-inside: avoid;
        }
        .sig-grid {
            width: 100%;
        }
        .sig-cell {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 12px;
        }
        .sig-label {
            font-size: 9pt;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 6px;
        }
        .sig-img-wrap {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2px;
        }
        .sig-img-wrap img {
            max-height: 60px;
            max-width: 160px;
        }
        .sig-line {
            border-bottom: 1.5px solid #1a202c;
            margin: 4px auto 3px;
            width: 180px;
        }
        .sig-name {
            font-size: 9pt;
            font-weight: 900;
        }
        .sig-role {
            font-size: 8pt;
            color: #718096;
        }
        .sig-blank {
            height: 58px;
        }
        .qr-wrap {
            text-align: center;
            margin-top: 6px;
        }
        .qr-wrap img {
            width: 72px;
            height: 72px;
        }
        .qr-label {
            font-size: 7pt;
            color: #718096;
            margin-top: 2px;
        }
        .doc-token {
            font-size: 6.5pt;
            color: #a0aec0;
            font-family: monospace;
            margin-top: 1px;
        }
        .verified-badge {
            display: inline-block;
            background: #c6f6d5;
            color: #276749;
            font-size: 7.5pt;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 3px;
            margin-top: 3px;
        }

        /* Footer */
        .page-footer {
            margin-top: 14px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 7.5pt;
            color: #a0aec0;
            text-align: center;
        }
    </style>
</head>
<body>

{{-- ═══════ KOP ═══════ --}}
<div class="kop">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" alt="Kop Sekolah">
    @else
        <div class="kop-text">
            <h1>{{ $settings->school_name ?? 'SMK TELKOM' }}</h1>
            <p>{{ $settings->address ?? '' }}</p>
            <p>
                @if($settings->phone ?? null) Telp: {{ $settings->phone }} @endif
                @if($settings->email ?? null) &nbsp;|&nbsp; {{ $settings->email }} @endif
            </p>
        </div>
        <div class="kop-divider"></div>
    @endif
</div>

{{-- ═══════ CONTENT ═══════ --}}
<div class="content">

    {{-- Title --}}
    <div class="doc-title">
        <h2>Lembar Penilaian UKK</h2>
        <p>Ujian Kompetensi Keahlian (UKK)</p>
    </div>

    {{-- Exam Info --}}
    <table class="info-table">
        <tr>
            <td class="lbl">Nama Ujian</td>
            <td class="sep">:</td>
            <td><strong>{{ $ujian->nama_ujian }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Jurusan</td>
            <td class="sep">:</td>
            <td>{{ $ujian->jurusan }}</td>
        </tr>
        @if($ujian->nama_project)
        <tr>
            <td class="lbl">Nama Project</td>
            <td class="sep">:</td>
            <td>{{ $ujian->nama_project }}</td>
        </tr>
        @endif
        @if($ujian->tanggal_pelaksanaan)
        <tr>
            <td class="lbl">Tanggal Pelaksanaan</td>
            <td class="sep">:</td>
            <td>{{ \Carbon\Carbon::parse($ujian->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}</td>
        </tr>
        @endif
        @if($ujian->tahunPelajaran)
        <tr>
            <td class="lbl">Tahun Pelajaran</td>
            <td class="sep">:</td>
            <td>{{ $ujian->tahunPelajaran->tahun }}</td>
        </tr>
        @endif
        <tr><td colspan="3" style="padding-top:4px;"></td></tr>
        <tr>
            <td class="lbl">Nama Siswa</td>
            <td class="sep">:</td>
            <td><strong>{{ $siswa->nama_lengkap }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">NIS</td>
            <td class="sep">:</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        <tr>
            <td class="lbl">Penguji</td>
            <td class="sep">:</td>
            <td>{{ $user->name }}</td>
        </tr>
    </table>

    {{-- Instruments --}}
    <div class="section-head">Rincian Penilaian Per Instrumen</div>

    @foreach($instrumenScores as $item)
    @php $ins = $item['instrumen']; @endphp
    <div class="instrumen-block">
        <div class="instrumen-head">
            {{ $loop->iteration }}. {{ $ins->nama_instrumen }}
            &nbsp;&mdash;&nbsp;
            <span style="font-size:8.5pt;font-weight:600;color:#553c9a;">
                Pengetahuan {{ $ins->bobot_pengetahuan }}% &middot; Keterampilan {{ 100 - $ins->bobot_pengetahuan }}%
            </span>
        </div>
        <div class="instrumen-inner">

            {{-- Pengetahuan --}}
            @if($ins->soalPengetahuan->isNotEmpty())
            <div class="sub-head">Penilaian Pengetahuan</div>
            <table class="soal-table">
                <thead>
                    <tr>
                        <th style="width:24px;" class="center">No</th>
                        <th>Soal / Pertanyaan</th>
                        <th style="width:60px;" class="center">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->soalPengetahuan as $soal)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $soal->pertanyaan }}</td>
                        <td class="center">
                            @if(isset($nilaiP[$soal->id]))
                                @if($nilaiP[$soal->id] == 1)
                                    <span class="badge-benar">✓ Benar</span>
                                @else
                                    <span class="badge-salah">✗ Salah</span>
                                @endif
                            @else
                                <span class="badge-nil">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align:right;font-weight:800;background:#ebf8ff;">
                            Jawaban Benar: {{ $item['benar'] }} / {{ $item['total_soal'] }}
                        </td>
                        <td class="center" style="background:#ebf8ff;font-weight:900;color:#2b6cb0;">
                            {{ $item['skor_p'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- Keterampilan --}}
            @if($ins->kategoriKeterampilan->isNotEmpty())
            <div class="sub-head green" style="margin-top:8px;">Penilaian Keterampilan</div>
            <table class="ind-table">
                <thead>
                    <tr>
                        <th style="width:24px;" class="center">No</th>
                        <th>Indikator</th>
                        <th style="width:80px;" class="center">Nilai (0–3)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->kategoriKeterampilan as $kat)
                    <tr>
                        <td colspan="3" class="ind-kat">
                            {{ $loop->iteration }}. {{ $kat->nama_kategori }}
                            <span style="font-weight:600;color:#718096;">(bobot {{ $kat->bobot }}%)</span>
                        </td>
                    </tr>
                    @foreach($kat->indikator as $ind)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td class="center">
                            @php $vk = $nilaiK[$ind->id] ?? null; @endphp
                            @if($vk !== null)
                                @php
                                    $labels = [0=>'Belum', 1=>'Cukup', 2=>'Baik', 3=>'S.Baik'];
                                    $colors = [0=>'#718096', 1=>'#d69e2e', 2=>'#3182ce', 3=>'#276749'];
                                @endphp
                                <span style="font-weight:900;color:{{ $colors[$vk] ?? '#1a202c' }};">
                                    {{ $vk }} &mdash; {{ $labels[$vk] ?? '' }}
                                </span>
                            @else
                                <span class="badge-nil">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align:right;font-weight:800;background:#f0fff4;">
                            Skor Keterampilan
                        </td>
                        <td class="center" style="background:#f0fff4;font-weight:900;color:#276749;">
                            {{ $item['skor_k'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- Instrumen score summary --}}
            <table style="width:100%;margin-top:6px;font-size:9pt;">
                <tr>
                    <td style="text-align:right;padding-right:10px;font-weight:700;color:#4a5568;">Nilai Akhir Instrumen:</td>
                    <td style="width:80px;text-align:center;background:#fef3c7;font-weight:900;font-size:10pt;border:1px solid #f6e05e;border-radius:4px;padding:3px 0;">
                        {{ $item['nilai_akhir'] }}
                    </td>
                </tr>
            </table>

        </div>
    </div>
    @endforeach

    {{-- Overall score summary --}}
    <div class="section-head">Rekapitulasi Nilai Akhir</div>
    <table class="score-table">
        <thead>
            <tr>
                <th style="text-align:left;">Instrumen</th>
                <th>Skor Pengetahuan</th>
                <th>Skor Keterampilan</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instrumenScores as $item)
            <tr>
                <td style="text-align:left;font-weight:700;">{{ $item['instrumen']->nama_instrumen }}</td>
                <td>{{ $item['skor_p'] }}</td>
                <td>{{ $item['skor_k'] }}</td>
                <td style="font-weight:900;">{{ $item['nilai_akhir'] }}</td>
            </tr>
            @endforeach
            <tr class="final-row">
                <td colspan="3" style="text-align:right;font-weight:900;">NILAI AKHIR UKK</td>
                <td style="font-size:12pt;">{{ $nilaiAkhirFinal }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Signature --}}
    <div class="sig-section">
        <table class="sig-grid">
            <tr>
                <td class="sig-cell" style="text-align:left;padding-left:0;">
                    <div class="sig-label">Mengetahui,</div>
                    <div class="sig-blank"></div>
                    <div class="sig-line" style="margin-left:0;width:160px;"></div>
                    <div class="sig-name">Kepala Sekolah / Kaprodi</div>
                </td>
                <td class="sig-cell" style="text-align:right;padding-right:0;">
                    <div class="sig-label">
                        {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }},<br>Penguji UKK,
                    </div>
                    @if($ttdBase64)
                        <div class="sig-img-wrap" style="justify-content:flex-end;">
                            <img src="{{ $ttdBase64 }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="sig-blank"></div>
                    @endif
                    @if($digDoc)
                        <div class="qr-wrap" style="text-align:right;margin-right:0;">
                            @if($qrBase64)
                                <img src="{{ $qrBase64 }}" alt="QR Verifikasi">
                            @endif
                        </div>
                    @endif
                    <div class="sig-line" style="margin-right:0;margin-left:auto;width:180px;"></div>
                    <div class="sig-name">{{ $user->name }}</div>
                    <div class="sig-role">Penguji UKK</div>
                    @if($digDoc)
                        <div class="verified-badge">✓ Terverifikasi Digital</div>
                        <div class="doc-token">Token: {{ $digDoc->token }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Page footer --}}
    <div class="page-footer">
        Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB
        @if($settings) &middot; {{ $settings->school_name }} @endif
    </div>

</div>
</body>
</html>
