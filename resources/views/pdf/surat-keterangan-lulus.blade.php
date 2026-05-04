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
            font-size: 10pt;
            color: #111;
            background: #fff;
            line-height: 1.4;
        }



        /* ====== WRAPPER ====== */
        .page-wrap {
            padding: 0 18mm 8mm 18mm;
        }

        /* ====== KOP SURAT (gambar penuh lebar, tanpa margin) ====== */
        .kop-wrapper {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .kop-image {
            width: 100%;
            display: block;
            margin: 0;
            padding: 0;
        }

        /* ====== JUDUL ====== */
        .judul-wrap {
            text-align: center;
            margin: 3pt 0 1pt;
        }
        .judul-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2pt;
            text-decoration: underline;
        }
        .judul-nomor {
            font-size: 9.5pt;
            margin-top: 2pt;
        }

        /* ====== PEMBUKA ====== */
        .pembuka {
            font-size: 10pt;
            text-align: justify;
            margin: 4pt 0 3pt;
        }

        /* ====== DATA SISWA ====== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2pt 0 2pt 20pt;
        }
        .data-table td {
            font-size: 10pt;
            padding: 1pt 0;
            vertical-align: top;
        }
        .col-label { width: 145pt; }
        .col-sep   { width: 12pt; text-align: center; }
        .col-value { font-weight: bold; }

        /* ====== DASAR HUKUM ====== */
        .dasar-wrap {
            font-size: 9.5pt;
            text-align: justify;
            margin: 4pt 0 2pt;
        }
        .dasar-list {
            margin: 2pt 0 0 18pt;
        }
        .dasar-list li {
            margin-bottom: 1pt;
        }

        /* ====== BOX STATUS ====== */
        .status-wrap {
            text-align: center;
            margin: 6pt 0 4pt;
        }
        .status-box {
            display: inline-block;
            border: 1.5pt solid #111;
            padding: 4pt 25pt;
        }
        .status-lulus {
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 3pt;
        }
        .status-tidak-lulus {
            font-size: 12pt;
            font-weight: normal;
            text-decoration: line-through;
            color: #888;
            letter-spacing: 3pt;
        }
        .status-separator {
            font-size: 11pt;
            font-weight: bold;
            padding: 0 6pt;
        }

        /* ====== PENUTUP ====== */
        .penutup {
            font-size: 10pt;
            text-align: justify;
            margin: 3pt 0;
        }

        /* ====== CATATAN ====== */
        .catatan {
            font-size: 9.5pt;
            text-align: justify;
            margin: 2pt 0;
        }

        /* ====== AREA TTD ====== */
        .ttd-wrap {
            margin-top: 8pt;
        }
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-table td {
            vertical-align: top;
            font-size: 10pt;
        }
        .ttd-left  { width: 55%; }
        .ttd-right { width: 45%; text-align: left; }
        .ttd-kota-tanggal { font-size: 10pt; margin-bottom: 0; }
        .ttd-jabatan      { font-size: 10pt; margin-bottom: 0; }
        .ttd-image-wrap   {
            height: 55pt;
            position: relative;
            text-align: left;
            margin-top: -20pt;
            margin-bottom: -20pt;
            z-index: 10;
        }
        .ttd-image {
            height: 90pt;
            max-width: 180pt;
        }
        .ttd-nama {
            font-size: 11pt;
            font-weight: bold;
            border-bottom: 1pt solid #000000;
            padding-top: 1pt;
            display: inline-block;
            min-width: 150pt;
        }
        .ttd-nip {
            font-size: 9.5pt;
            margin-top: 0;
        }
    </style>
</head>
<body>

    {{-- ===== KOP SURAT (full width, no margin) ===== --}}
    <div class="kop-wrapper">
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
    </div>

    <div class="page-wrap">

        {{-- ===== JUDUL ===== --}}
        <div class="judul-wrap">
            <div class="judul-title">Pengumuman Kelulusan</div>
            <div class="judul-nomor">Nomor: {{ $nomorSurat ?? '-' }}</div>
        </div>

        {{-- ===== PEMBUKA ===== --}}
        <p class="pembuka">
            Yang bertanda tangan dibawah ini Kepala SMK Telkom Lampung, Kabupaten Pringsewu Provinsi Lampung menerangkan bahwa :
        </p>

        {{-- ===== DATA SISWA ===== --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($siswa->nama_lengkap) }}</td>
            </tr>
            <tr>
                <td class="col-label">Kelas / Konsentrasi Keahlian</td>
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
                <td class="col-label">Tempat, Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="col-label">NIS / NISN</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $siswa->nis }}{{ ($siswa->dapodik && $siswa->dapodik->nisn) ? ' / ' . $siswa->dapodik->nisn : '' }}
                </td>
            </tr>
        </table>

        {{-- ===== DASAR HUKUM ===== --}}
        <div class="dasar-wrap">
            <p>Berdasarkan:</p>
            <ol class="dasar-list">
                <li>Undang-Undang Nomor 20 Tahun 2003 tentang Sistem Pendidikan Nasional (Lembaran Negara Republik Indonesia Tahun 2003 Nomor 78, Tambahan Lembaran Negara Republik Indonesia Nomor 4301);</li>
                <li>Peraturan Menteri Pendidikan, Kebudayaan, Riset dan Teknologi Republik Indonesia Nomor 21 Tahun 2022 tentang Standar Penilaian Pendidikan Pada Pendidikan Anak Usia Dini, Jenjang Pendidikan Dasar, Dan Jenjang Pendidikan Menengah;</li>
                <li>Pedoman Pelaksanaan Ujian Sekolah dan UKK SMK Telkom Lampung Tahun Pelajaran 2025/2026;</li>
                <li>Hasil Keputusan Rapat Pleno Kelulusan SMK Telkom Lampung yang diadakan pada hari Kamis, 30 April 2026 tentang Penetapan Kelulusan.</li>
            </ol>
        </div>

        <p class="pembuka" style="margin-top:3pt;">
            Dengan ini siswa tersebut di atas dinyatakan :
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

        @if($kelulusan->catatan)
        <p class="catatan"><em>Catatan: {{ $kelulusan->catatan }}</em></p>
        @endif

        <p class="penutup" style="margin-top:3pt;">
            Demikian surat keterangan ini dibuat, untuk dapat di pergunakan sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <div class="ttd-wrap">
            <table class="ttd-table">
                <tr>
                    <td class="ttd-left">
                        @if(isset($qrBase64))
                            <div style="margin-top: 15pt;">
                                <img src="{{ $qrBase64 }}" style="width: 65pt; height: 65pt;" alt="QR Code Verifikasi">
                                <div style="font-size: 8pt; margin-top: 4pt; color: #444; line-height: 1.2;">
                                    Dokumen ini resmi dan diverifikasi<br>oleh sistem SMK Telkom Lampung
                                </div>
                            </div>
                        @endif
                    </td>
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
                        <br>
                        <br>
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
