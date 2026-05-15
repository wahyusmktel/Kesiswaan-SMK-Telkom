<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Penilaian UKK — {{ $siswa->nama_lengkap }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #111827;
            font-size: 9.5pt;
            margin: 0;
            padding: 0;
        }

        /* ── KOP ── */
        .kop { width: 100%; margin: 0; padding: 0; }
        .kop img {
            width: 100%;
            height: 90px;
            display: block;
        }
        .kop-text {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
            padding: 14px 24px 12px;
            color: #fff;
            text-align: center;
        }
        .kop-text h1 { margin: 0 0 3px; font-size: 14pt; font-weight: 900; letter-spacing: .5px; }
        .kop-text p  { margin: 0; font-size: 8pt; opacity: .88; }
        .kop-divider { height: 3px; background: #1e3a5f; margin: 0; }

        /* ── CONTENT ── */
        .content { padding: 16px 24px 14px; }

        /* ── TITLE BLOCK ── */
        .title-block {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .title-block h2 {
            font-size: 12pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin: 0 0 2px;
        }
        .title-block p {
            font-size: 9pt;
            font-weight: 600;
            margin: 0;
            color: #374151;
        }

        /* ── INFO TABLE ── */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 9pt; }
        .info-table td { padding: 2.5px 0; vertical-align: top; }
        .info-table .lbl { width: 135px; font-weight: 700; color: #374151; }
        .info-table .sep { width: 14px; text-align: center; color: #6b7280; }
        .info-divider { height: 1px; background: #e5e7eb; margin: 8px 0; }

        /* ── SECTION HEAD ── */
        .section-head {
            font-size: 9.5pt;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
            background: #1e3a5f;
            color: #fff;
            padding: 5px 10px;
            margin: 14px 0 8px;
            border-radius: 4px;
        }

        /* ── INSTRUMEN BLOCK ── */
        .instrumen-block {
            border: 1px solid #d1d5db;
            border-radius: 5px;
            margin-bottom: 14px;
            overflow: hidden;
        }
        .instrumen-head {
            background: #f3f4f6;
            border-bottom: 1px solid #d1d5db;
            padding: 6px 10px;
            font-weight: 900;
            font-size: 9.5pt;
            color: #111827;
        }
        .instrumen-head small {
            font-size: 8pt;
            font-weight: 600;
            color: #6b7280;
        }
        .instrumen-inner { padding: 10px; }

        /* ── ASPECT HEAD ── */
        .aspect-head {
            font-size: 9pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: 4px 8px;
            border-left: 3px solid;
            margin: 10px 0 6px;
        }
        .aspect-head.p { border-color: #2563eb; color: #1d4ed8; background: #eff6ff; }
        .aspect-head.k { border-color: #059669; color: #065f46; background: #f0fdf4; }

        /* ── PENGETAHUAN TABLE ── */
        .soal-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .soal-table th {
            background: #1d4ed8;
            color: #fff;
            border: 1px solid #1d4ed8;
            padding: 4px 6px;
            font-weight: 800;
            text-align: center;
            line-height: 1.3;
        }
        .soal-table th.left { text-align: left; }
        .soal-table td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .soal-table td.center { text-align: center; }
        .soal-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .soal-table .foot-row td {
            background: #dbeafe;
            font-weight: 800;
            color: #1e40af;
        }

        /* ── KETERAMPILAN TABLE ── */
        .ket-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .ket-table th {
            background: #065f46;
            color: #fff;
            border: 1px solid #065f46;
            padding: 4px 5px;
            font-weight: 800;
            text-align: center;
            line-height: 1.25;
        }
        .ket-table th.ya-head { background: #047857; border-color: #047857; }
        .ket-table th.score-row {
            background: #d1fae5;
            color: #065f46;
            font-size: 8pt;
            font-weight: 900;
            border-color: #6ee7b7;
        }
        .ket-table td {
            border: 1px solid #d1d5db;
            padding: 4px 5px;
            vertical-align: middle;
        }
        .ket-table td.center { text-align: center; }
        .ket-table tbody tr:nth-child(even) td { background: #f8fafb; }
        .kat-row td {
            background: #e5e7eb !important;
            font-weight: 800;
            font-size: 8.5pt;
            padding: 5px 6px;
        }
        .rerata-row td {
            background: #ecfdf5 !important;
            font-weight: 700;
            font-style: italic;
            font-size: 7.5pt;
            color: #065f46;
        }
        .ket-foot-row td {
            background: #d1fae5 !important;
            font-weight: 900;
            color: #065f46;
        }
        .check-mark {
            font-size: 11pt;
            font-weight: 900;
            color: #111827;
        }

        /* ── REKAP TABLE ── */
        .rekap-table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-top: 2px; }
        .rekap-table th {
            background: #1e3a5f;
            color: #fff;
            border: 1px solid #1e3a5f;
            padding: 5px 8px;
            font-weight: 800;
            text-align: center;
        }
        .rekap-table th.left { text-align: left; }
        .rekap-table td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            text-align: center;
        }
        .rekap-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .rekap-table .final-row td {
            background: #fef3c7;
            font-weight: 900;
            font-size: 11pt;
            color: #92400e;
        }
        .rekap-table .sub-row td { background: #f1f5f9; font-style: italic; font-size: 8.5pt; }

        /* ── SIGNATURE ── */
        .sig-section { margin-top: 20px; page-break-inside: avoid; }
        .sig-grid { width: 100%; }
        .sig-cell { width: 50%; vertical-align: top; padding: 0 8px; }
        .sig-label { font-size: 8.5pt; font-weight: 700; color: #374151; margin-bottom: 6px; }
        .sig-blank { height: 56px; }
        .sig-img-wrap { height: 62px; display: flex; align-items: center; margin-bottom: 2px; }
        .sig-img-wrap img { max-height: 58px; max-width: 155px; }
        .sig-line { border-bottom: 1.5px solid #111827; margin: 4px 0 3px; width: 170px; }
        .sig-name { font-size: 9pt; font-weight: 900; }
        .sig-role { font-size: 8pt; color: #6b7280; }
        .qr-wrap { margin-top: 5px; }
        .qr-wrap img { width: 68px; height: 68px; }
        .qr-label { font-size: 7pt; color: #6b7280; margin-top: 2px; }
        .doc-token { font-size: 6pt; color: #9ca3af; font-family: monospace; margin-top: 1px; }
        .verified-badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            font-size: 7.5pt;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 3px;
            margin-top: 3px;
            border: 1px solid #6ee7b7;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
            font-size: 7pt;
            color: #9ca3af;
            text-align: center;
        }
    </style>
</head>
<body>

{{-- ════════════ KOP ════════════ --}}
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

{{-- ════════════ CONTENT ════════════ --}}
<div class="content">

    {{-- ── Title ── --}}
    <div class="title-block">
        <h2>Lembar Penilaian Ujian Kompetensi Keahlian (UKK)</h2>
        <p>
            {{ $ujian->nama_ujian }}
            @if($ujian->tahunPelajaran)
                &mdash; Tahun Pelajaran {{ $ujian->tahunPelajaran->tahun }}
            @endif
        </p>
    </div>

    {{-- ── Info Table ── --}}
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

    {{-- ════════════ RINCIAN PER INSTRUMEN ════════════ --}}
    <div class="section-head">Rincian Penilaian Per Instrumen</div>

    @foreach($instrumenScores as $item)
    @php
        $ins = $item['instrumen'];
        $romanNumerals = ['I','II','III','IV','V','VI','VII','VIII','IX','X'];
    @endphp

    <div class="instrumen-block">
        <div class="instrumen-head">
            {{ $loop->iteration }}. {{ $ins->nama_instrumen }}
            <small>&mdash; Bobot Pengetahuan {{ $ins->bobot_pengetahuan }}% &middot; Keterampilan {{ 100 - $ins->bobot_pengetahuan }}%</small>
        </div>
        <div class="instrumen-inner">

            {{-- ── 1. Penilaian Aspek Pengetahuan ── --}}
            @if($ins->soalPengetahuan->isNotEmpty())
            <div class="aspect-head p">1. Penilaian Aspek Pengetahuan</div>
            @php $totalSoal = $item['total_soal']; @endphp
            <table class="soal-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:26px;">No</th>
                        <th rowspan="2" class="left">Soal / Pertanyaan</th>
                        <th colspan="2">Jawaban</th>
                        <th rowspan="2" style="width:36px;">Skor</th>
                    </tr>
                    <tr>
                        <th style="width:42px;">Benar</th>
                        <th style="width:42px;">Salah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->soalPengetahuan as $soal)
                    @php
                        $vp      = $nilaiP[$soal->id] ?? null;
                        $isBenar = $vp !== null && (int)$vp === 1;
                        $isSalah = $vp !== null && (int)$vp === 0;
                        $poinSoal = $totalSoal > 0 ? round(100 / $totalSoal) : 0;
                    @endphp
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td>{{ $soal->pertanyaan }}</td>
                        <td class="center"><span class="check-mark">{{ $isBenar ? '✓' : '' }}</span></td>
                        <td class="center"><span class="check-mark">{{ $isSalah ? '✓' : '' }}</span></td>
                        <td class="center">{{ $vp !== null ? ($isBenar ? $poinSoal : 0) : '' }}</td>
                    </tr>
                    @endforeach
                    <tr class="foot-row">
                        <td colspan="3" style="text-align:right;">Jawaban Benar: <strong>{{ $item['benar'] }} / {{ $totalSoal }}</strong></td>
                        <td class="center" colspan="2"><strong>{{ $item['skor_p'] }}</strong></td>
                    </tr>
                </tbody>
            </table>
            @endif

            {{-- ── 2. Penilaian Aspek Keterampilan ── --}}
            @if($ins->kategoriKeterampilan->isNotEmpty())
            <div class="aspect-head k" style="margin-top:12px;">2. Penilaian Aspek Keterampilan</div>
            <table class="ket-table">
                <thead>
                    <tr>
                        <th rowspan="4" style="width:28px;">No</th>
                        <th rowspan="4" class="left" style="text-align:left; padding-left:7px;">Komponen / Sub Komponen</th>
                        <th colspan="4">Kompeten</th>
                        <th rowspan="4" style="width:55px;">Catatan</th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="width:38px;" class="ya-head">Belum</th>
                        <th colspan="3" class="ya-head">Ya</th>
                    </tr>
                    <tr>
                        <th style="width:38px;" class="ya-head">Cukup</th>
                        <th style="width:38px;" class="ya-head">Baik</th>
                        <th style="width:48px;" class="ya-head">Sangat<br>Baik</th>
                    </tr>
                    <tr>
                        <th class="score-row">0</th>
                        <th class="score-row">1</th>
                        <th class="score-row">2</th>
                        <th class="score-row">3</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ins->kategoriKeterampilan as $katIdx => $kat)
                    {{-- Category header row --}}
                    <tr class="kat-row">
                        <td class="center"><strong>{{ $romanNumerals[$katIdx] ?? ($katIdx + 1) }}</strong></td>
                        <td colspan="5">
                            <strong>{{ $kat->nama_kategori }}</strong>
                            <span style="font-weight:600;font-size:7.5pt;color:#6b7280;">(Bobot: {{ $kat->bobot }}%)</span>
                        </td>
                        <td></td>
                    </tr>
                    {{-- Indikator rows --}}
                    @foreach($kat->indikator as $indIdx => $ind)
                    @php $vk = isset($nilaiK[$ind->id]) ? (int)$nilaiK[$ind->id] : null; @endphp
                    <tr>
                        <td class="center" style="font-size:7.5pt;">{{ $katIdx + 1 }}.{{ $indIdx + 1 }}</td>
                        <td>{{ $ind->nama_indikator }}</td>
                        <td class="center"><span class="check-mark">{{ $vk === 0 ? '✓' : '' }}</span></td>
                        <td class="center"><span class="check-mark">{{ $vk === 1 ? '✓' : '' }}</span></td>
                        <td class="center"><span class="check-mark">{{ $vk === 2 ? '✓' : '' }}</span></td>
                        <td class="center"><span class="check-mark">{{ $vk === 3 ? '✓' : '' }}</span></td>
                        <td></td>
                    </tr>
                    @endforeach
                    {{-- Rerata row --}}
                    @php
                        $indIds   = $kat->indikator->pluck('id');
                        $vals     = $indIds->map(fn($id) => isset($nilaiK[$id]) ? (int)$nilaiK[$id] : 0);
                        $rerata   = $vals->count() ? $vals->avg() : 0;
                        $rRounded = (int)round($rerata);
                    @endphp
                    <tr class="rerata-row">
                        <td></td>
                        <td>Rerata komponen {{ $kat->nama_kategori }} (Pembulatan)</td>
                        <td class="center">{{ $rRounded === 0 ? $rRounded : '' }}</td>
                        <td class="center">{{ $rRounded === 1 ? $rRounded : '' }}</td>
                        <td class="center">{{ $rRounded === 2 ? $rRounded : '' }}</td>
                        <td class="center">{{ $rRounded === 3 ? $rRounded : '' }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                    {{-- Skor Keterampilan footer --}}
                    <tr class="ket-foot-row">
                        <td colspan="2" style="text-align:right;font-weight:900;">Skor Keterampilan</td>
                        <td colspan="4" class="center" style="font-size:10pt;font-weight:900;">{{ $item['skor_k'] }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @endif

        </div>{{-- /instrumen-inner --}}
    </div>{{-- /instrumen-block --}}
    @endforeach

    {{-- ════════════ REKAPITULASI NILAI AKHIR ════════════ --}}
    <div class="section-head">Rekapitulasi Nilai Akhir</div>
    <table class="rekap-table">
        <thead>
            <tr>
                <th class="left" style="width:34%;">Instrumen</th>
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
                <td colspan="5" style="text-align:right;">NILAI AKHIR UKK</td>
                <td style="font-size:13pt;font-weight:900;">{{ $nilaiAkhirFinal }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ════════════ TANDA TANGAN ════════════ --}}
    <div class="sig-section">
        <table class="sig-grid">
            <tr>
                <td class="sig-cell" style="text-align:left;padding-left:0;">
                    <div class="sig-label">Mengetahui,<br>Kepala Sekolah / Kaprodi</div>
                    <div class="sig-blank"></div>
                    <div class="sig-line"></div>
                    <div class="sig-name">_______________________</div>
                </td>
                <td class="sig-cell" style="text-align:right;padding-right:0;">
                    <div class="sig-label" style="text-align:right;">
                        {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }},<br>Penguji UKK,
                    </div>
                    @if($ttdBase64)
                        <div class="sig-img-wrap" style="justify-content:flex-end;">
                            <img src="{{ $ttdBase64 }}" alt="Tanda Tangan">
                        </div>
                    @else
                        <div class="sig-blank"></div>
                    @endif
                    @if($digDoc && $qrBase64)
                        <div class="qr-wrap" style="text-align:right;">
                            <img src="{{ $qrBase64 }}" alt="QR Verifikasi">
                        </div>
                    @endif
                    <div class="sig-line" style="margin-left:auto;margin-right:0;"></div>
                    <div class="sig-name" style="text-align:right;">{{ $user->name }}</div>
                    <div class="sig-role" style="text-align:right;">Penguji UKK</div>
                    @if($digDoc)
                        <div style="text-align:right;">
                            <span class="verified-badge">&#10003; Terverifikasi Digital</span>
                        </div>
                        <div class="doc-token" style="text-align:right;">Token: {{ $digDoc->token }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="page-footer">
        Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB
        @if($settings) &middot; {{ $settings->school_name }} @endif
    </div>

</div>
</body>
</html>
