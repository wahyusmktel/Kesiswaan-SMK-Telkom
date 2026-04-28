<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Lulus</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Serif', 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #111;
            background: #fff;
            line-height: 1.5;
        }

        /* ====== BINGKAI HALAMAN ====== */
        .outer-border {
            position: fixed;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 3pt solid #1a3a5c;
        }
        .inner-border {
            position: fixed;
            top: 13.5mm;
            left: 13.5mm;
            right: 13.5mm;
            bottom: 13.5mm;
            border: 1pt solid #1a3a5c;
        }

        /* ====== KONTEN UTAMA ====== */
        .content-wrap {
            padding: 22mm 22mm 18mm 26mm;
        }

        /* ====== KOP SURAT ====== */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4pt double #1a3a5c;
            padding-bottom: 8pt;
            margin-bottom: 12pt;
        }
        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo-cell {
            width: 65pt;
            text-align: center;
        }
        .kop-logo-box {
            width: 58pt;
            height: 58pt;
            border: 2pt solid #1a3a5c;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            padding-top: 14pt;
            font-size: 8pt;
            font-weight: bold;
            color: #1a3a5c;
            line-height: 1.3;
        }
        .kop-garuda-box {
            width: 58pt;
            height: 58pt;
            border: 2pt solid #8b6914;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            padding-top: 14pt;
            font-size: 8pt;
            font-weight: bold;
            color: #8b6914;
            line-height: 1.3;
            background: #fffbf0;
        }
        .kop-text-cell {
            text-align: center;
            padding: 0 8pt;
        }
        .kop-school {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a3a5c;
            letter-spacing: 1pt;
        }
        .kop-tagline {
            font-size: 9.5pt;
            color: #444;
            margin-top: 2pt;
        }
        .kop-address {
            font-size: 8.5pt;
            color: #555;
            margin-top: 3pt;
        }
        .kop-nss {
            font-size: 8pt;
            color: #666;
            margin-top: 2pt;
        }

        /* ====== JUDUL SURAT ====== */
        .title-wrap {
            text-align: center;
            margin: 10pt 0 8pt;
        }
        .title-main {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            color: #1a3a5c;
            text-decoration: underline;
        }
        .title-nomor {
            font-size: 9.5pt;
            color: #555;
            margin-top: 4pt;
        }

        /* ====== PEMBUKA SURAT ====== */
        .pembuka {
            font-size: 11pt;
            text-align: justify;
            margin: 10pt 0 6pt;
        }

        /* ====== TABEL DATA SISWA ====== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6pt 0 6pt 14pt;
        }
        .data-table td {
            font-size: 11pt;
            padding: 2.5pt 0;
            vertical-align: top;
        }
        .data-table .col-label {
            width: 140pt;
            font-weight: normal;
            color: #222;
        }
        .data-table .col-sep {
            width: 14pt;
            text-align: center;
            font-weight: normal;
        }
        .data-table .col-value {
            font-weight: bold;
            color: #111;
        }

        /* ====== BOX STATUS LULUS ====== */
        .status-box {
            border: 2pt solid #1a5c2a;
            background-color: #f0fdf4;
            text-align: center;
            padding: 10pt 0;
            margin: 12pt 0;
        }
        .status-text {
            font-size: 17pt;
            font-weight: bold;
            color: #155724;
            letter-spacing: 4pt;
            text-transform: uppercase;
        }
        .status-sub {
            font-size: 9.5pt;
            color: #2d6a3a;
            margin-top: 3pt;
        }

        /* ====== CATATAN ====== */
        .catatan-box {
            border-left: 4pt solid #1a3a5c;
            padding: 5pt 10pt;
            margin: 8pt 0;
            background: #f0f4f8;
            font-size: 10pt;
            color: #333;
        }

        /* ====== TEKS PENUTUP ====== */
        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin: 6pt 0 4pt;
        }

        /* ====== AREA TTD ====== */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20pt;
        }
        .ttd-table td {
            vertical-align: top;
            font-size: 10.5pt;
        }
        .ttd-left {
            width: 50%;
            padding-top: 2pt;
            color: #555;
        }
        .ttd-right {
            width: 50%;
            text-align: center;
        }
        .ttd-kota-tanggal {
            font-size: 10.5pt;
            margin-bottom: 2pt;
        }
        .ttd-jabatan {
            font-size: 10.5pt;
            margin-bottom: 56pt;
        }
        .ttd-nama {
            font-size: 11pt;
            font-weight: bold;
            border-top: 1pt solid #333;
            padding-top: 3pt;
            display: inline-block;
            min-width: 160pt;
        }
        .ttd-nip {
            font-size: 9pt;
            color: #555;
            margin-top: 2pt;
        }
    </style>
</head>
<body>

    {{-- Bingkai ganda --}}
    <div class="outer-border"></div>
    <div class="inner-border"></div>

    <div class="content-wrap">

        {{-- ===== KOP SURAT ===== --}}
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    <div class="kop-logo-box">SMK<br>TELKOM</div>
                </td>
                <td class="kop-text-cell">
                    <div class="kop-school">{{ config('app.name', 'SMK Telkom') }}</div>
                    <div class="kop-tagline">Sekolah Menengah Kejuruan Teknologi &amp; Komunikasi</div>
                    <div class="kop-address">Alamat Sekolah, Kecamatan, Kabupaten/Kota, Provinsi</div>
                    <div class="kop-nss">NSS/NPSN: ________________ &nbsp;|&nbsp; Website: smktelkom.sch.id &nbsp;|&nbsp; Email: info@smktelkom.sch.id</div>
                </td>
                <td class="kop-logo-cell">
                    <div class="kop-garuda-box">LAMBANG<br>GARUDA</div>
                </td>
            </tr>
        </table>

        {{-- ===== JUDUL ===== --}}
        <div class="title-wrap">
            <div class="title-main">Surat Keterangan Lulus</div>
            <div class="title-nomor">
                Nomor: {{ str_pad($siswa->id, 4, '0', STR_PAD_LEFT) }}/SKL/{{ str_replace(' ', '-', strtoupper(config('app.name', 'SMK-TLK'))) }}/{{ $tahunPelajaran->tahun }}
            </div>
        </div>

        {{-- ===== PEMBUKA ===== --}}
        <p class="pembuka">
            Yang bertanda tangan di bawah ini, Kepala {{ config('app.name', 'SMK Telkom') }},
            menerangkan bahwa siswa yang namanya tersebut di bawah ini:
        </p>

        {{-- ===== DATA SISWA ===== --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama Lengkap</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($siswa->nama_lengkap) }}</td>
            </tr>
            <tr>
                <td class="col-label">NIS</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $siswa->nis }}</td>
            </tr>
            @if($siswa->dapodik && $siswa->dapodik->nisn)
            <tr>
                <td class="col-label">NISN</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $siswa->dapodik->nisn }}</td>
            </tr>
            @endif
            <tr>
                <td class="col-label">Tempat, Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            @if($siswa->jenis_kelamin)
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            @endif
            <tr>
                <td class="col-label">Kelas</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $rombel?->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Tahun Pelajaran</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $tahunPelajaran->tahun }}</td>
            </tr>
        </table>

        {{-- ===== STATUS LULUS ===== --}}
        <div class="status-box">
            <div class="status-text">&#10003;&nbsp; LULUS</div>
            <div class="status-sub">
                dari {{ config('app.name', 'SMK Telkom') }} pada Tahun Pelajaran {{ $tahunPelajaran->tahun }}
            </div>
        </div>

        {{-- ===== PENUTUP ===== --}}
        <p class="penutup">
            Surat keterangan lulus ini diterbitkan sebagai bukti kelulusan sementara sebelum
            diterbitkannya ijazah resmi, dan berlaku untuk keperluan administrasi pendidikan
            maupun pekerjaan.
        </p>

        @if($kelulusan->catatan)
        <div class="catatan-box">
            <strong>Catatan:</strong> {{ $kelulusan->catatan }}
        </div>
        @endif

        <p class="penutup">
            Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan
            sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td class="ttd-left">
                    Diterbitkan pada:<br>
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </td>
                <td class="ttd-right">
                    <div class="ttd-kota-tanggal">
                        _______________, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </div>
                    <div class="ttd-jabatan">Kepala Sekolah,</div>
                    <div class="ttd-nama">______________________________</div>
                    <div class="ttd-nip">NIP. __________________________</div>
                </td>
            </tr>
        </table>

    </div>{{-- end content-wrap --}}

</body>
</html>
