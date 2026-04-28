<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Lulus - {{ $siswa->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #1a1a1a;
            background: #fff;
            padding: 0;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 15mm 25mm;
            position: relative;
        }

        /* ============ BORDER GANDA ============ */
        .border-outer {
            position: absolute;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #1a3a5c;
        }
        .border-inner {
            position: absolute;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #1a3a5c;
        }

        /* ============ HEADER KOP SURAT ============ */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 4px double #1a3a5c;
            margin-bottom: 18px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
        }
        .kop-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .kop-logo-placeholder {
            width: 70px;
            height: 70px;
            border: 2px solid #1a3a5c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            font-weight: bold;
            color: #1a3a5c;
            text-align: center;
            line-height: 1.2;
        }
        .kop-text {
            flex: 1;
            text-align: center;
        }
        .kop-school { font-size: 20pt; font-weight: bold; text-transform: uppercase; color: #1a3a5c; letter-spacing: 1px; }
        .kop-tagline { font-size: 10pt; color: #444; margin-top: 2px; }
        .kop-address { font-size: 9pt; color: #555; margin-top: 3px; line-height: 1.4; }
        .kop-nss { font-size: 8.5pt; color: #666; margin-top: 2px; }

        /* ============ JUDUL SURAT ============ */
        .title-section {
            text-align: center;
            margin: 18px 0 20px;
        }
        .title-main {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a3a5c;
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        .title-nomor {
            font-size: 10pt;
            color: #555;
            margin-top: 6px;
        }

        /* ============ BODY SURAT ============ */
        .body-text {
            line-height: 1.8;
            text-align: justify;
            margin-bottom: 14px;
        }
        .body-text p { margin-bottom: 8px; }

        /* ============ TABEL DATA SISWA ============ */
        .data-siswa {
            margin: 14px 0 14px 20px;
            width: calc(100% - 20px);
        }
        .data-siswa tr td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11.5pt;
        }
        .data-siswa td:first-child { width: 160px; font-weight: normal; }
        .data-siswa td:nth-child(2) { width: 16px; text-align: center; }
        .data-siswa td:last-child { font-weight: bold; }

        /* ============ BOX KELULUSAN ============ */
        .kelulusan-box {
            border: 2px solid #1a5c2a;
            background: #f0fdf4;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 16px 0;
            text-align: center;
        }
        .kelulusan-status {
            font-size: 18pt;
            font-weight: bold;
            color: #1a5c2a;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .kelulusan-label {
            font-size: 10pt;
            color: #2d6a3a;
            margin-top: 4px;
        }

        /* ============ FOOTER / TTD ============ */
        .footer-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .footer-left {
            font-size: 10pt;
            color: #555;
            line-height: 1.6;
        }
        .footer-right {
            text-align: center;
            min-width: 200px;
        }
        .ttd-place-date {
            font-size: 11pt;
            margin-bottom: 4px;
        }
        .ttd-title {
            font-size: 11pt;
            margin-bottom: 60px;
        }
        .ttd-name {
            font-size: 12pt;
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 4px;
        }
        .ttd-nip {
            font-size: 9.5pt;
            color: #555;
            margin-top: 2px;
        }

        /* ============ WATERMARK / LATAR BELAKANG ============ */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72pt;
            font-weight: 900;
            color: rgba(26, 90, 44, 0.04);
            text-transform: uppercase;
            letter-spacing: 10px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        /* ============ CATATAN ============ */
        .catatan-box {
            border-left: 4px solid #1a3a5c;
            padding: 6px 12px;
            margin: 14px 0;
            background: #f0f4f8;
            font-size: 10pt;
            color: #444;
            border-radius: 0 4px 4px 0;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- Border dekoratif --}}
    <div class="border-outer"></div>
    <div class="border-inner"></div>

    {{-- Watermark --}}
    <div class="watermark">LULUS</div>

    {{-- KOP SURAT --}}
    <div class="kop">
        <div class="kop-logo">
            <div class="kop-logo-placeholder">SMK<br>TELKOM</div>
        </div>
        <div class="kop-text">
            <div class="kop-school">{{ config('app.name', 'SMK Telkom') }}</div>
            <div class="kop-tagline">Sekolah Menengah Kejuruan Teknologi & Komunikasi</div>
            <div class="kop-address">
                Alamat Sekolah, Kecamatan, Kabupaten/Kota, Provinsi
            </div>
            <div class="kop-nss">NSS/NPSN: ________________ | Website: smktelkom.sch.id | Email: info@smktelkom.sch.id</div>
        </div>
        <div class="kop-logo">
            <div class="kop-logo-placeholder" style="background:#fffbf0;border-color:#8b6914;color:#8b6914">GARUDA</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="title-section">
        <div class="title-main">Surat Keterangan Lulus</div>
        <div class="title-nomor">
            Nomor: {{ str_pad($siswa->id, 4, '0', STR_PAD_LEFT) }}/SKL/{{ config('app.name', 'SMK-TLK') }}/{{ $tahunPelajaran->tahun }}
        </div>
    </div>

    {{-- BODY --}}
    <div class="body-text">
        <p>Yang bertanda tangan di bawah ini, Kepala {{ config('app.name', 'SMK Telkom') }}, menerangkan bahwa siswa yang namanya tersebut di bawah ini:</p>
    </div>

    {{-- DATA SISWA --}}
    <table class="data-siswa">
        <tr>
            <td>Nama Lengkap</td>
            <td>:</td>
            <td>{{ strtoupper($siswa->nama_lengkap) }}</td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td>{{ $siswa->nis }}</td>
        </tr>
        @if($siswa->dapodik && $siswa->dapodik->nisn)
        <tr>
            <td>NISN</td>
            <td>:</td>
            <td>{{ $siswa->dapodik->nisn }}</td>
        </tr>
        @endif
        <tr>
            <td>Tempat, Tanggal Lahir</td>
            <td>:</td>
            <td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td>{{ $rombel?->kelas->nama_kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tahun Pelajaran</td>
            <td>:</td>
            <td>{{ $tahunPelajaran->tahun }}</td>
        </tr>
        @if($siswa->jenis_kelamin)
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        @endif
    </table>

    {{-- BOX STATUS LULUS --}}
    <div class="kelulusan-box">
        <div class="kelulusan-status">&#10003; LULUS</div>
        <div class="kelulusan-label">
            dari {{ config('app.name', 'SMK Telkom') }} pada Tahun Pelajaran {{ $tahunPelajaran->tahun }}
        </div>
    </div>

    <div class="body-text">
        <p>
            Surat keterangan lulus ini diterbitkan sebagai bukti kelulusan sementara sebelum diterbitkannya ijazah resmi.
            Surat ini berlaku untuk keperluan administrasi pendidikan dan pekerjaan.
        </p>
        @if($kelulusan->catatan)
        <div class="catatan-box">
            <strong>Catatan:</strong> {{ $kelulusan->catatan }}
        </div>
        @endif
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    {{-- TTD --}}
    <div class="footer-section">
        <div class="footer-left">
            <p>Diterbitkan pada:</p>
            <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        </div>
        <div class="footer-right">
            <div class="ttd-place-date">
                _________________, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            <div class="ttd-title">Kepala Sekolah,</div>
            <div class="ttd-name">____________________________</div>
            <div class="ttd-nip">NIP. ________________________</div>
        </div>
    </div>
</div>
</body>
</html>
