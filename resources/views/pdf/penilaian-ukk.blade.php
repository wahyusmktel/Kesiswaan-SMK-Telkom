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
            color: #1e293b;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            letter-spacing: -.05px;
        }

        /* ── KOP ── */
        .kop { width: 100%; margin: 0; padding: 0; }
        .kop img { width: 100%; height: 90px; display: block; }
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
        .content { padding: 15px 24px 14px; }

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
        }

        /* ── INSTRUMEN BLOCK ── */
        .instrumen-block {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            margin-bottom: 13px;
            overflow: hidden;
        }
        .instrumen-head {
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-weight: 900;
            font-size: 9pt;
            color: #0f172a;
        }
        .instrumen-head small { font-size: 7.5pt; font-weight: 500; color: #64748b; }
        .instrumen-inner { padding: 10px; }

        /* ── ASPECT HEAD ── */
        .aspect-head {
            font-size: 8.5pt;
            font-weight: 800;
            padding: 4px 8px;
            border-left: 3px solid;
            margin: 10px 0 6px;
        }
        .aspect-head.p { border-color: #3b82f6; color: #1d4ed8; background: #f0f7ff; }
        .aspect-head.k { border-color: #22c55e; color: #166534; background: #f0fdf4; }

        /* ── PENGETAHUAN TABLE ── */
        .soal-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .soal-table th {
            background: #f0f7ff;
            color: #1e3a5f;
            border: 1px solid #bfdbfe;
            padding: 4px 6px;
            font-weight: 800;
            text-align: center;
            font-size: 7.5pt;
        }
        .soal-table th.left { text-align: left; }
        .soal-table td {
            border: 1px solid #e2e8f0;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .soal-table td.center { text-align: center; }
        .soal-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .soal-table .foot-row td {
            background: #e0f0ff;
            font-weight: 800;
            color: #1e40af;
            border-color: #bfdbfe;
        }
        .chk { font-size: 11pt; font-weight: 900; color: #0f172a; }

        /* ── KETERAMPILAN TABLE ── */
        .ket-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .ket-table th {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
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
            border-color: #bbf7d0;
            font-size: 8pt;
            font-weight: 900;
        }
        .ket-table td {
            border: 1px solid #e2e8f0;
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
            border-color: #cbd5e1;
        }
        .rerata-row td {
            background: #f0fdf4 !important;
            font-style: italic;
            font-size: 7pt;
            color: #166534;
            border-color: #d1fae5;
        }
        .ket-foot-row td {
            background: #dcfce7 !important;
            font-weight: 900;
            color: #166534;
            border-color: #86efac;
        }

        /* ── REKAP TABLE ── */
        .rekap-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
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
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            text-align: center;
        }
        .rekap-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .rekap-table .final-row td {
            background: #fef9c3;
            font-weight: 900;
            color: #713f12;
            border-color: #fde047;
        }

        /* ── SIGNATURE ── */
        .sig-section { margin-top: 20px; page-break-inside: avoid; }
        .sig-grid { width: 100%; }
        .sig-cell { width: 50%; vertical-align: top; padding: 0 8px; }
        .sig-label { font-size: 8.5pt; font-weight: 700; color: #475569; margin-bottom: 6px; }
        .sig-blank { height: 54px; }
        .sig-img-wrap { height: 60px; display: flex; align-items: center; margin-bottom: 2px; }
        .sig-img-wrap img { max-height: 56px; max-width: 150px; }
        .sig-line { border-bottom: 1px solid #1e293b; margin: 4px 0 3px; width: 170px; }
        .sig-name { font-size: 9pt; font-weight: 900; color: #0f172a; }
        .sig-role { font-size: 7.5pt; color: #64748b; }
        .qr-wrap { margin-top: 4px; }
        .qr-wrap img { width: 66px; height: 66px; }
        .doc-token { font-size: 6pt; color: #94a3b8; font-family: monospace; margin-top: 1px; }
        .verified-badge {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 7pt;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 3px;
            margin-top: 2px;
            border: 1px solid #86efac;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

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
            <div class="aspect-head p">1. Penilaian Aspek Pengetahuan</div>
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
                        <td>{{ $soal->pertanyaan }}</td>
                        <td class="center"><span class="chk">@if($isBenar)&#10003;@endif</span></td>
                        <td class="center"><span class="chk">@if($isSalah)&#10003;@endif</span></td>
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
                        <td class="center"><span class="chk">@if($vk === 0)&#10003;@endif</span></td>
                        <td class="center"><span class="chk">@if($vk === 1)&#10003;@endif</span></td>
                        <td class="center"><span class="chk">@if($vk === 2)&#10003;@endif</span></td>
                        <td class="center"><span class="chk">@if($vk === 3)&#10003;@endif</span></td>
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

    <div class="page-footer">
        Dicetak pada {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH:mm') }} WIB
        @if($settings) &middot; {{ $settings->school_name }} @endif
    </div>

</div>
</body>
</html>
