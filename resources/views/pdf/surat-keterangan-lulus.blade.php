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
            line-height: 1.6;
        }

        /* ====== BINGKAI ====== */
        .border-outer {
            position: fixed;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 2.5pt solid #1a1a1a;
        }
        .border-inner {
            position: fixed;
            top: 11mm;
            left: 11mm;
            right: 11mm;
            bottom: 11mm;
            border: 1pt solid #1a1a1a;
        }

        /* ====== WRAPPER ====== */
        .page-wrap {
            padding: 20mm 22mm 16mm 22mm;
        }

        /* ====== KOP SURAT (gambar penuh lebar) ====== */
        .kop-image {
            width: 100%;
            display: block;
        }
        .kop-divider {
            border: none;
            border-top: 3pt double #111;
            margin: 4pt 0 0;
        }

        /* ====== JUDUL ====== */
        .judul-wrap {
            text-align: center;
            margin: 14pt 0 4pt;
        }
        .judul-title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            text-decoration: underline;
        }
        .judul-nomor {
            font-size: 10pt;
            margin-top: 3pt;
        }

        /* ====== PEMBUKA ====== */
        .pembuka {
            font-size: 11pt;
            text-align: justify;
            margin: 12pt 0 6pt;
        }

        /* ====== DATA SISWA ====== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4pt 0 4pt 20pt;
        }
        .data-table td {
            font-size: 11pt;
            padding: 2pt 0;
            vertical-align: top;
        }
        .col-label { width: 145pt; }
        .col-sep   { width: 12pt; text-align: center; }
        .col-value { font-weight: bold; }

        /* ====== DASAR HUKUM ====== */
        .dasar-wrap {
            font-size: 10.5pt;
            text-align: justify;
            margin: 8pt 0 4pt;
        }
        .dasar-list {
            margin: 3pt 0 0 18pt;
        }
        .dasar-list li {
            margin-bottom: 2pt;
        }

        /* ====== BOX STATUS ====== */
        .status-wrap {
            text-align: center;
            margin: 10pt 0 8pt;
        }
        .status-box {
            display: inline-block;
            border: 1.5pt solid #111;
            padding: 5pt 30pt;
        }
        .status-lulus {
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 3pt;
        }
        .status-tidak-lulus {
            font-size: 13pt;
            font-weight: normal;
            text-decoration: line-through;
            color: #888;
            letter-spacing: 3pt;
        }
        .status-separator {
            font-size: 12pt;
            font-weight: bold;
            padding: 0 8pt;
        }

        /* ====== PENUTUP ====== */
        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin: 6pt 0;
        }

        /* ====== CATATAN ====== */
        .catatan {
            font-size: 10pt;
            text-align: justify;
            margin: 4pt 0;
        }

        /* ====== AREA TTD ====== */
        .ttd-wrap {
            margin-top: 18pt;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            vertical-align: top;
            font-size: 10.5pt;
        }
        .ttd-left  { width: 55%; }
        .ttd-right { width: 45%; text-align: center; }
        .ttd-kota-tanggal { font-size: 10.5pt; margin-bottom: 2pt; }
        .ttd-jabatan      { font-size: 10.5pt; margin-bottom: 4pt; }
        .ttd-image-wrap   {
            height: 70pt;
            position: relative;
            text-align: center;
        }
        .ttd-image {
            height: 70pt;
            max-width: 160pt;
        }
        .ttd-nama {
            font-size: 11pt;
            font-weight: bold;
            border-top: 1pt solid #333;
            padding-top: 2pt;
            display: inline-block;
            min-width: 150pt;
        }
        .ttd-nip {
            font-size: 9.5pt;
            margin-top: 2pt;
        }
    </style>
</head>
<body>

    <div class="border-outer"></div>
    <div class="border-inner"></div>

    <div class="page-wrap">

        {{-- ===== KOP SURAT ===== --}}
        @if($kopBase64)
            <img src="{{ $kopBase64 }}" class="kop-image" alt="Kop Surat">
        @else
            {{-- Fallback kop teks jika gambar belum diupload --}}
            <table width="100%" style="border-collapse:collapse; padding-bottom:6pt;">
                <tr>
                    <td style="text-align:center; padding:4pt 0;">
                        <div style="font-size:18pt; font-weight:bold; text-transform:uppercase; letter-spacing:1pt;">
                            {{ config('app.name', 'SMK TELKOM') }}
                        </div>
                        <div style="font-size:9pt; margin-top:2pt;">Sekolah Menengah Kejuruan</div>
                        <div style="font-size:8.5pt; color:#555; margin-top:2pt;">Alamat Sekolah</div>
                    </td>
                </tr>
            </table>
        @endif
        <hr class="kop-divider">

        {{-- ===== JUDUL ===== --}}
        <div class="judul-wrap">
            <div class="judul-title">Pengumuman Kelulusan</div>
            <div class="judul-nomor">Nomor: {{ $nomorSurat ?? '-' }}</div>
        </div>

        {{-- ===== PEMBUKA ===== --}}
        <p class="pembuka">
            Yang bertanda tangan di bawah ini, Kepala {{ config('app.name', 'SMK Telkom') }},
            menerangkan bahwa siswa yang namanya tersebut di bawah ini:
        </p>

        {{-- ===== DATA SISWA ===== --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($siswa->nama_lengkap) }}</td>
            </tr>
            <tr>
                <td class="col-label">NIS / NISN</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $siswa->nis }}{{ ($siswa->dapodik && $siswa->dapodik->nisn) ? ' / ' . $siswa->dapodik->nisn : '' }}
                </td>
            </tr>
            <tr>
                <td class="col-label">Tempat, Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="col-label">Program Keahlian / Kelas</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    @php
                        $namaKelas = $rombel?->kelas->nama_kelas ?? '-';
                        $jurusan   = $rombel?->kelas->jurusan ?? null;
                        $kelasLabel = $namaKelas;
                        if ($jurusan) {
                            preg_match('/^(XII[A-Z\s]*)/i', $namaKelas, $m);
                            $level = trim($m[1] ?? $namaKelas);
                            $kelasLabel = $level . ' / ' . $jurusan;
                        }
                    @endphp
                    {{ $kelasLabel }}
                </td>
            </tr>
            <tr>
                <td class="col-label">Tahun Pelajaran</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $tahunPelajaran->tahun }}</td>
            </tr>
        </table>

        {{-- ===== DASAR HUKUM ===== --}}
        <div class="dasar-wrap">
            <p>Berdasarkan:</p>
            <ol class="dasar-list">
                <li>Peraturan Pemerintah Nomor 57 Tahun 2021 tentang Standar Nasional Pendidikan;</li>
                <li>Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi tentang Penilaian Hasil Belajar;</li>
                <li>Hasil rapat dewan guru {{ config('app.name', 'SMK Telkom') }} tentang kelulusan Tahun Pelajaran {{ $tahunPelajaran->tahun }};</li>
                <li>Keputusan Kepala {{ config('app.name', 'SMK Telkom') }} tentang kelulusan peserta didik Tahun Pelajaran {{ $tahunPelajaran->tahun }}.</li>
            </ol>
        </div>

        <p class="pembuka" style="margin-top:8pt;">
            Menyatakan bahwa siswa tersebut di atas dinyatakan:
        </p>

        {{-- ===== BOX STATUS LULUS / TIDAK LULUS ===== --}}
        <div class="status-wrap">
            <div class="status-box">
                @if($kelulusan->status === 'lulus')
                    <span class="status-lulus">LULUS</span>
                    <span class="status-separator">/</span>
                    <span class="status-tidak-lulus">TIDAK LULUS</span>
                @else
                    <span class="status-tidak-lulus">LULUS</span>
                    <span class="status-separator">/</span>
                    <span class="status-lulus">TIDAK LULUS</span>
                @endif
            </div>
        </div>

        {{-- ===== PENUTUP ===== --}}
        <p class="penutup">
            dari {{ config('app.name', 'SMK Telkom') }} pada Tahun Pelajaran {{ $tahunPelajaran->tahun }}.
        </p>

        @if($kelulusan->catatan)
        <p class="catatan"><em>Catatan: {{ $kelulusan->catatan }}</em></p>
        @endif

        <p class="penutup" style="margin-top:6pt;">
            Demikian surat keterangan ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan
            sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <div class="ttd-wrap">
            <table class="ttd-table">
                <tr>
                    <td class="ttd-left"></td>
                    <td class="ttd-right">
                        @php
                            $kotaTanggal = ($pengumuman->kota_surat ?? '_______') . ', ';
                            $kotaTanggal .= $pengumuman->tanggal_surat
                                ? \Carbon\Carbon::parse($pengumuman->tanggal_surat)->translatedFormat('d F Y')
                                : \Carbon\Carbon::now()->translatedFormat('d F Y');
                        @endphp
                        <div class="ttd-kota-tanggal">{{ $kotaTanggal }}</div>
                        <div class="ttd-jabatan">Kepala Sekolah,</div>
                        <div class="ttd-image-wrap">
                            @if($ttdBase64)
                                <img src="{{ $ttdBase64 }}" class="ttd-image" alt="TTD">
                            @endif
                        </div>
                        <div>
                            <span class="ttd-nama">{{ $pengumuman->nama_kepala_sekolah ?? '______________________________' }}</span>
                        </div>
                        @if($pengumuman->nip_kepala_sekolah)
                        <div class="ttd-nip">NIP. {{ $pengumuman->nip_kepala_sekolah }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>{{-- end page-wrap --}}

</body>
</html>
