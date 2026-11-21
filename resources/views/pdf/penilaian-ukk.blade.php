<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Penilaian UKK — {{ $siswa->nama_lengkap }}</title>
    @php
        \Carbon\Carbon::setLocale('id');
        $checkIcon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
        $checkIconGreen = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
    @endphp
    <style>
        @page {
            size: A4 portrait;
            margin-top: 14mm;
            margin-bottom: 18mm;
            margin-left: 10mm;
            margin-right: 10mm;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #1e293b;
            font-size: 8.5pt;
            margin: 0;
            padding: 0;
            letter-spacing: -.05px;
        }

        /* ── FIXED FOOTER ON EVERY PAGE ── */
        .page-footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
            line-height: 1.3;
        }

        /* ── KOP ── */
        .kop {
            margin-top: -14mm;
            margin-left: -10mm;
            margin-right: -10mm;
            margin-bottom: 10px;
        }
        .kop img {
            width: 100%;
            height: auto;
            max-height: 125px;
            display: block;
        }
        .kop-text {
            background: #1e293b;
            padding: 13px 24px 11px;
            color: #fff;
            text-align: center;
        }
        .kop-text h1 { margin: 0 0 3px; font-size: 13pt; font-weight: 900; }
        .kop-text p  { margin: 0; font-size: 8pt; color: #cbd5e1; }
        .kop-divider { height: 3px; background: #94a3b8; margin: 0; }

        /* ── CONTENT ── */
        .content { padding: 0; }

        /* ── TITLE ── */
        .title-block {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #1e293b;
        }
        .title-block h2 {
            font-size: 11.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin: 0 0 2px;
            color: #0f172a;
        }
        .title-block p {
            font-size: 9pt;
            font-weight: 600;
            margin: 0;
            color: #475569;
        }

        /* ── INFO TABLE ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 8.5pt; }
        .info-table td { padding: 2px 0; vertical-align: top; }
        .info-table .lbl { width: 132px; font-weight: 700; color: #475569; }
        .info-table .sep { width: 12px; text-align: center; color: #94a3b8; }
        .info-divider { height: 1px; background: #e2e8f0; margin: 7px 0; }

        /* ── SECTION HEAD ── */
        .section-head {
            font-size: 9pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #0f172a;
            padding: 4px 8px 4px 10px;
            border-left: 3px solid #334155;
            background: #f1f5f9;
            margin: 14px 0 8px;
            page-break-after: avoid;
        }

        /* ── INSTRUMEN BLOCK ── */
        .instrumen-block {
            border: 1px solid #cbd5e1;
            margin-bottom: 12px;
            page-break-inside: auto;
        }
        .instrumen-head {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-weight: 900;
            font-size: 9pt;
            color: #0f172a;
            page-break-after: avoid;
        }
        .instrumen-head small { font-size: 7.5pt; font-weight: 500; color: #64748b; }
        .instrumen-inner { padding: 8px 10px; }

        /* ── ASPECT HEAD ── */
        .aspect-head {
            font-size: 8.5pt;
            font-weight: 800;
            padding: 4px 8px;
            border-left: 3px solid;
            margin: 10px 0 6px;
            page-break-after: avoid;
        }
        .aspect-head.p { border-color: #3b82f6; color: #1d4ed8; background: #f0f7ff; }
        .aspect-head.k { border-color: #22c55e; color: #166534; background: #f0fdf4; }

        /* ── TABLES COMMON PAGINATION ── */
        table { border-collapse: collapse; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr { page-break-inside: avoid; }

        /* ── PENGETAHUAN TABLE ── */
        .soal-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 4px; }
        .soal-table th {
            background: #f0f7ff;
            color: #1e3a5f;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-weight: 800;
            text-align: center;
            font-size: 7.5pt;
        }
        .soal-table th.left { text-align: left; }
        .soal-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .soal-table td.center { text-align: center; }
        .soal-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .soal-table .foot-row td {
            background: #e0f0ff;
            font-weight: 800;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
        .chk-img { width: 11px; height: 11px; vertical-align: middle; display: inline-block; }

        /* ── KETERAMPILAN TABLE ── */
        .ket-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-bottom: 4px; }
        .ket-table th {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            font-weight: 800;
            text-align: center;
            font-size: 7.5pt;
            line-height: 1.25;
        }
        .ket-table th.ya-sub {
            background: #dcfce7;
            color: #14532d;
            border-color: #86efac;
        }
        .ket-table th.score-num {
            background: #ecfdf5;
            color: #15803d;
            border-color: #86efac;
            font-size: 8pt;
            font-weight: 900;
        }
        .ket-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: middle;
        }
        .ket-table td.center { text-align: center; }
        .ket-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .kat-row td {
            background: #f1f5f9 !important;
            font-weight: 800;
            font-size: 8pt;
            padding: 5px 6px;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }
        .rerata-row td {
            background: #f0fdf4 !important;
            font-style: italic;
            font-size: 7pt;
            color: #166534;
            border: 1px solid #cbd5e1;
        }
        .ket-foot-row td {
            background: #dcfce7 !important;
            font-weight: 900;
            color: #166534;
            border: 1px solid #86efac;
        }

        /* ── SIKAP TABLE ── */
        .sikap-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 4px; page-break-inside: avoid; }
        .sikap-table th {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            font-weight: 800;
            text-align: center;
            font-size: 8pt;
        }
        .sikap-table th.left { text-align: left; }
        .sikap-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .sikap-table td.center { text-align: center; }
        .sikap-table tbody tr:nth-child(even) td { background: #fefdf8; }
        .sikap-table .rata-row td {
            background: #fef3c7 !important;
            font-weight: 800;
            color: #78350f;
            border: 1px solid #fde68a;
        }
        .sikap-box {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            border-radius: 4px;
            background: #f8fafc;
            font-size: 8pt;
            margin-top: 4px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        /* ── REKAP TABLE ── */
        .rekap-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; page-break-inside: avoid; }
        .rekap-table th {
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            font-weight: 800;
            text-align: center;
            font-size: 8pt;
        }
        .rekap-table th.left { text-align: left; }
        .rekap-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: center;
        }
        .rekap-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .rekap-table .final-row td {
            background: #fef9c3;
            font-weight: 900;
            color: #713f12;
            border: 1px solid #fde047;
        }

        /* ── SIGNATURE ── */
        .sig-section { margin-top: 18px; page-break-inside: avoid; }
        .sig-grid { width: 100%; border-collapse: collapse; }
        .sig-cell { width: 50%; vertical-align: top; }
        .sig-box { display: inline-block; text-align: left; min-width: 210px; }
        .sig-label { font-size: 8.5pt; font-weight: 700; color: #475569; margin-bottom: 6px; line-height: 1.35; }
        .sig-blank { height: 50px; }
        .sig-img-wrap { height: 52px; display: block; margin-bottom: 2px; }
        .sig-img-wrap img { max-height: 50px; max-width: 140px; }
        .sig-name { font-size: 9pt; font-weight: 900; color: #0f172a; margin-top: 3px; }
        .sig-role { font-size: 8pt; color: #475569; margin-top: 2px; }
        .qr-wrap { margin-top: 3px; margin-bottom: 3px; }
        .qr-wrap img { width: 62px; height: 62px; }
        .doc-token { font-size: 6pt; color: #94a3b8; font-family: monospace; margin-top: 2px; }
        .verified-badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 7pt;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 3px;
            margin-top: 3px;
            border: 1px solid #86efac;
        }
    </style>
</head>
<body>

{{-- ════════ FIXED FOOTER PADA SETIAP HALAMAN ════════ --}}
<div class="page-footer">
    Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB
    @if($settings) &middot; {{ $settings->school_name }} @endif
</div>

{{-- ════════ KOP ════════ --}}
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

{{-- ════════ CONTENT ════════ --}}
<div class="content">

    {{-- Title --}}
    <div class="title-block">
        <h2>Lembar Penilaian Ujian Kompetensi Keahlian (UKK)</h2>
        <p>
            {{ $ujian->nama_ujian }}
            @if($ujian->tahunPelajaran)
                &mdash; Tahun Pelajaran {{ $ujian->tahunPelajaran->tahun }}
            @endif
        </p>
    </div>

    {{-- Info --}}
    <table class="info-table">
        <tr>
            <td class="lbl">Jurusan / Kompetensi</td>
            <td class="sep">:</td>
            <td><strong>{{ $ujian->jurusan }}</strong></td>
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
    </table>

    <div class="info-divider"></div>

    <table class="info-table" style="margin-bottom:4px;">
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

    {{-- ════════ RINCIAN PENILAIAN ════════ --}}
    <div class="section-head">Rincian Penilaian Per Instrumen</div>

    @foreach($instrumenScores as $item)
    @php
        $ins = $item['instrumen'];
        $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X'];
        $totalSoal = $item['total_soal'];
        $poinSoal  = $totalSoal > 0 ? round(100 / $totalSoal) : 0;
    @endphp

    <div class="instrumen-block">
        <div class="instrumen-head">
            {{ $loop->iteration }}. {{ $ins->nama_instrumen }}
            <small>&nbsp;&mdash; Pengetahuan {{ $ins->bobot_pengetahuan }}% &middot; Keterampilan {{ 100 - $ins->bobot_pengetahuan }}%</small>
        </div>
        <div class="instrumen-inner">

            {{-- 1. Aspek Pengetahuan --}}
            @if($ins->soalPengetahuan->isNotEmpty())
            @php
                $hasBilingualSoal = $ins->soalPengetahuan->contains(fn($s) => $s->jenis_soal === 'bilingual');
                $hasEsaiSoal      = $ins->soalPengetahuan->contains(fn($s) => $s->jenis_soal !== 'bilingual');
            @endphp
            <div class="aspect-head p">
                1. Penilaian Aspek Pengetahuan
                @if($hasBilingualSoal && $hasEsaiSoal)
                    (Esai &amp; Wawancara Bilingual)
                @elseif($hasBilingualSoal || $ujian->is_bilingual)
                    (Wawancara Bilingual / English)
                @else
                    (Esai)
                @endif
            </div>
            <table class="soal-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:26px;">No</th>
                        <th rowspan="2" class="left">Soal / Pertanyaan</th>
                        <th colspan="2">Jawaban</th>
                        <th rowspan="2" style="width:34px;">Skor</th>
                    </tr>
                    <tr>
                        <th style="width:40px;">Benar</th>
                        <th style="width:40px;">Salah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->soalPengetahuan as $soal)
                    @php
                        $vp      = isset($nilaiP[$soal->id]) ? (int)$nilaiP[$soal->id] : null;
                        $isBenar = $vp === 1;
                        $isSalah = $vp === 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $soal->pertanyaan }}
                            @if($hasBilingualSoal && $hasEsaiSoal)
                                <span style="font-size:7pt; color:#6b7280; font-style:italic;">
                                    ({{ $soal->jenis_soal === 'bilingual' ? 'Wawancara Bilingual' : 'Esai Siswa' }})
                                </span>
                            @endif
                        </td>
                        <td class="center">@if($isBenar)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td class="center">@if($isSalah)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td class="center">@if($vp !== null){{ $isBenar ? $poinSoal : 0 }}@endif</td>
                    </tr>
                    @endforeach
                    <tr class="foot-row">
                        <td colspan="3" style="text-align:right;padding-right:8px;">
                            Jawaban Benar: <strong>{{ $item['benar'] }} / {{ $totalSoal }}</strong>
                        </td>
                        <td colspan="2" class="center"><strong>{{ $item['skor_p'] }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- 2. Aspek Keterampilan --}}
            @if($ins->kategoriKeterampilan->isNotEmpty())
            <div class="aspect-head k" style="margin-top:11px;">2. Penilaian Aspek Keterampilan</div>
            <table class="ket-table">
                <thead>
                    <tr>
                        <th rowspan="4" style="width:27px;">No</th>
                        <th rowspan="4" style="text-align:left;padding-left:7px;">Komponen / Sub Komponen</th>
                        <th colspan="4">Kompeten</th>
                        <th rowspan="4" style="width:52px;">Catatan</th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width:38px;">Belum</th>
                        <th colspan="3" class="ya-sub">Ya</th>
                    </tr>
                    <tr>
                        <th style="width:38px;" class="ya-sub">Cukup</th>
                        <th style="width:38px;" class="ya-sub">Baik</th>
                        <th style="width:46px;" class="ya-sub">Sangat<br>Baik</th>
                    </tr>
                    <tr>
                        <th class="score-num">0</th>
                        <th class="score-num">1</th>
                        <th class="score-num">2</th>
                        <th class="score-num">3</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->kategoriKeterampilan as $ki => $kat)
                    <tr class="kat-row">
                        <td class="center"><strong>{{ $romans[$ki] ?? ($ki + 1) }}</strong></td>
                        <td colspan="5">
                            <strong>{{ $kat->nama_kategori }}</strong>
                            <span style="font-weight:500;font-size:7pt;color:#64748b;">(Bobot: {{ $kat->bobot }}%)</span>
                        </td>
                        <td></td>
                    </tr>
                    @foreach($kat->indikator as $ii => $ind)
                    @php $vk = isset($nilaiK[$ind->id]) ? (int)$nilaiK[$ind->id] : null; @endphp
                    <tr>
                        <td class="center" style="font-size:7pt;color:#64748b;">{{ $ki + 1 }}.{{ $ii + 1 }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td class="center">@if($vk === 0)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td class="center">@if($vk === 1)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td class="center">@if($vk === 2)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td class="center">@if($vk === 3)<img src="{{ $checkIcon }}" class="chk-img" alt="✓">@endif</td>
                        <td></td>
                    </tr>
                    @endforeach
                    @php
                        $ids      = $kat->indikator->pluck('id');
                        $avg      = $ids->count() ? $ids->avg(fn($id) => isset($nilaiK[$id]) ? (int)$nilaiK[$id] : 0) : 0;
                        $rRounded = (int)round($avg);
                    @endphp
                    <tr class="rerata-row">
                        <td></td>
                        <td>Rerata komponen {{ $kat->nama_kategori }} (Pembulatan)</td>
                        <td class="center">@if($rRounded === 0){{ $rRounded }}@endif</td>
                        <td class="center">@if($rRounded === 1){{ $rRounded }}@endif</td>
                        <td class="center">@if($rRounded === 2){{ $rRounded }}@endif</td>
                        <td class="center">@if($rRounded === 3){{ $rRounded }}@endif</td>
                        <td></td>
                    </tr>
                    @endforeach
                    <tr class="ket-foot-row">
                        <td colspan="2" style="text-align:right;padding-right:8px;font-weight:900;">Skor Keterampilan</td>
                        <td colspan="4" class="center" style="font-size:10.5pt;font-weight:900;">{{ $item['skor_k'] }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @endif

        </div>
    </div>
    @endforeach

    {{-- ════════ ASPEK SIKAP (SIKAP KERJA) ════════ --}}
    <div class="section-head" style="margin-top:10px;">Form Penilaian Aspek Sikap (Sikap Kerja)</div>
    <table class="sikap-table">
        <thead>
            <tr>
                <th rowspan="2" style="width:48%;" class="left">Indikator Pencapaian Kompetensi</th>
                <th colspan="4">Tingkat Pencapaian Kompetensi</th>
            </tr>
            <tr>
                <th style="width:13%;">Kurang</th>
                <th style="width:13%;">Cukup</th>
                <th style="width:13%;">Baik</th>
                <th style="width:13%;">Sangat Baik</th>
            </tr>
            <tr style="background:#f8fafc;font-size:7pt;color:#64748b;">
                <th class="left" style="padding:2px 6px;">2</th>
                <th style="padding:2px;">3</th>
                <th style="padding:2px;">4</th>
                <th style="padding:2px;">5</th>
                <th style="padding:2px;">6</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sikapItems = [
                    'kerapian'       => 'Kerapian',
                    'ketelitian'     => 'Ketelitian',
                    'kedisiplinan'   => 'Kedisiplinan',
                    'tanggung_jawab' => 'Tanggung Jawab',
                    'inisiatif'      => 'Inisiatif',
                ];
                $counts = ['kurang' => 0, 'cukup' => 0, 'baik' => 0, 'sangat_baik' => 0];
            @endphp
            @foreach($sikapItems as $field => $label)
            @php
                $val = strtolower($nilaiSikap?->$field ?? '');
                if (isset($counts[$val])) {
                    $counts[$val]++;
                }
            @endphp
            <tr>
                <td style="text-align:left;font-weight:600;">{{ $label }}</td>
                <td class="center">
                    @if($val === 'kurang')
                        <img src="{{ $checkIconGreen }}" class="chk-img" alt="✓">
                    @endif
                </td>
                <td class="center">
                    @if($val === 'cukup')
                        <img src="{{ $checkIconGreen }}" class="chk-img" alt="✓">
                    @endif
                </td>
                <td class="center">
                    @if($val === 'baik')
                        <img src="{{ $checkIconGreen }}" class="chk-img" alt="✓">
                    @endif
                </td>
                <td class="center">
                    @if($val === 'sangat_baik')
                        <img src="{{ $checkIconGreen }}" class="chk-img" alt="✓">
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="rata-row">
                <td style="text-align:left;font-weight:800;">Rata-rata pencapaian kompetensi</td>
                <td class="center">{{ $counts['kurang'] > 0 ? $counts['kurang'] : '' }}</td>
                <td class="center">{{ $counts['cukup'] > 0 ? $counts['cukup'] : '' }}</td>
                <td class="center">{{ $counts['baik'] > 0 ? $counts['baik'] : '' }}</td>
                <td class="center">{{ $counts['sangat_baik'] > 0 ? $counts['sangat_baik'] : '' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="sikap-box">
        <div style="margin-bottom:3px;">
            <strong>Kesimpulan :</strong>
            @php
                $kes = $nilaiSikap?->kesimpulan ?? '';
            @endphp
            <span style="{{ $kes === 'Kurang Baik' ? 'font-weight:900;text-decoration:underline;' : 'color:#64748b;' }}">Kurang Baik</span> /
            <span style="{{ $kes === 'Cukup Baik' ? 'font-weight:900;text-decoration:underline;' : 'color:#64748b;' }}">Cukup Baik</span> /
            <span style="{{ $kes === 'Baik' ? 'font-weight:900;text-decoration:underline;' : 'color:#64748b;' }}">Baik</span> /
            <span style="{{ $kes === 'Sangat Baik' ? 'font-weight:900;text-decoration:underline;' : 'color:#64748b;' }}">Sangat Baik</span>*
            @if($kes)
                &nbsp;&mdash; <strong>Hasil: {{ $kes }}</strong>
            @endif
        </div>
        <div>
            <strong>Catatan :</strong>
            @if(!empty($nilaiSikap?->catatan))
                <span style="font-style:italic;">{{ $nilaiSikap->catatan }}</span>
            @else
                <span style="color:#94a3b8;font-style:italic;">.................................................................................................................................................................</span>
            @endif
        </div>
    </div>

    {{-- ════════ REKAPITULASI ════════ --}}
    <div class="section-head">Rekapitulasi Nilai Akhir</div>
    <table class="rekap-table">
        <thead>
            <tr>
                <th class="left" style="width:36%;">Instrumen Penilaian</th>
                <th>Bobot P</th>
                <th>Skor Pengetahuan</th>
                <th>Bobot K</th>
                <th>Skor Keterampilan</th>
                <th>Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($instrumenScores as $item)
            @php $ins = $item['instrumen']; $bp = $ins->bobot_pengetahuan; $bk = 100 - $bp; @endphp
            <tr>
                <td style="text-align:left;font-weight:700;">{{ $ins->nama_instrumen }}</td>
                <td>{{ $bp }}%</td>
                <td>{{ $item['skor_p'] }}</td>
                <td>{{ $bk }}%</td>
                <td>{{ $item['skor_k'] }}</td>
                <td style="font-weight:900;">{{ $item['nilai_akhir'] }}</td>
            </tr>
            @endforeach
            <tr class="final-row">
                <td colspan="5" style="text-align:right;font-weight:900;font-size:9.5pt;">NILAI AKHIR UKK</td>
                <td style="font-size:13pt;font-weight:900;">{{ $nilaiAkhirFinal }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ════════ TANDA TANGAN ════════ --}}
    <div class="sig-section">
        <table class="sig-grid">
            <tr>
                <td class="sig-cell" style="text-align:left;padding-left:0;">
                    <div class="sig-box">
                        <div class="sig-label">Mengetahui,<br>Kepala Program Studi</div>
                        <div class="sig-blank"></div>
                        <div class="sig-name">( ............................................ )</div>
                        <div class="sig-role">NIP. ............................................</div>
                    </div>
                </td>
                <td class="sig-cell" style="text-align:right;padding-right:0;">
                    <div class="sig-box">
                        <div class="sig-label">
                            Pringsewu, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>Penguji UKK,
                        </div>
                        @if($ttdBase64)
                            <div class="sig-img-wrap">
                                <img src="{{ $ttdBase64 }}" alt="Tanda Tangan">
                            </div>
                        @else
                            <div class="sig-blank"></div>
                        @endif
                        @if($digDoc && $qrBase64)
                            <div class="qr-wrap">
                                <img src="{{ $qrBase64 }}" alt="QR Verifikasi">
                            </div>
                        @endif
                        <div class="sig-name">( {{ $user->name }} )</div>
                        <div class="sig-role">NIP. {{ $user->nip ?? '............................................' }}</div>
                        @if($digDoc)
                            <div style="margin-top:3px;">
                                <span class="verified-badge"><img src="{{ $checkIconGreen }}" style="width:9px;height:9px;vertical-align:middle;display:inline-block;" alt="✓"> Terverifikasi Digital</span>
                            </div>
                            <div class="doc-token">Token: {{ $digDoc->token }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
