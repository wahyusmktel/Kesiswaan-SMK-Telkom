<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Judul Laporan Prakerin - {{ $siswa?->nama_lengkap }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .header-title {
            font-size: 15pt;
            font-weight: 800;
            color: #b91c1c;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 12pt;
            font-weight: 700;
            color: #111827;
            margin: 2px 0 0 0;
        }
        .header-address {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 4px;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 18px;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            color: #111827;
            margin: 0;
        }
        .doc-number {
            font-size: 9pt;
            color: #4b5563;
            margin-top: 3px;
        }
        .section-label {
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #b91c1c;
            margin-top: 14px;
            margin-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 9.5pt;
        }
        .info-label {
            width: 28%;
            color: #4b5563;
            font-weight: 600;
        }
        .info-colon {
            width: 2%;
            text-align: center;
        }
        .info-value {
            width: 70%;
            color: #111827;
            font-weight: 700;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-disetujui {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-ditolak {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .box-catatan {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 6px;
            font-size: 9pt;
            color: #991b1b;
        }
        .box-catatan-acc {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 6px;
            font-size: 9pt;
            color: #065f46;
        }
        .signature-section {
            margin-top: 25px;
            width: 100%;
        }
        .sig-box {
            float: right;
            width: 45%;
            text-align: center;
        }
        .sig-date {
            font-size: 9pt;
            margin-bottom: 6px;
            color: #374151;
        }
        .sig-role {
            font-size: 9pt;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .sig-qr {
            margin: 6px auto;
            text-align: center;
        }
        .sig-name {
            font-size: 9.5pt;
            font-weight: 800;
            text-decoration: underline;
            color: #111827;
        }
        .sig-nip {
            font-size: 8pt;
            color: #6b7280;
        }
        .clear {
            clear: both;
        }
        .watermark {
            font-size: 7.5pt;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            padding-top: 6px;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Header Kop Sekolah --}}
    <table class="header-table">
        <tr>
            <td style="width: 100%; text-align: center;">
                <h1 class="header-title">SMK TELKOM LAMPUNG</h1>
                <h2 class="header-subtitle">HUBUNGAN INDUSTRI (HUBIN) & PRAKERIN</h2>
                <p class="header-address">Jl. Purnawirawan No. 01, Pasir Gintung, Kec. Tj. Karang Barat, Kota Bandar Lampung, Lampung</p>
            </td>
        </tr>
    </table>

    {{-- Judul Dokumen --}}
    <div class="doc-title-container">
        <h3 class="doc-title">
            @if($isAcc)
                BERITA ACARA PERSETUJUAN (ACC) JUDUL LAPORAN PRAKERIN
            @else
                BERITA ACARA REVIEW & REVISI JUDUL LAPORAN PRAKERIN
            @endif
        </h3>
        <p class="doc-number">Nomor: {{ $nomorBeritaAcara }}</p>
    </div>

    <p style="margin-bottom: 12px; font-size: 9.5pt;">
        Pada hari ini, <strong>{{ $tanggalReview ? $tanggalReview->isoFormat('dddd, D MMMM YYYY') : now()->isoFormat('dddd, D MMMM YYYY') }}</strong>, telah dilaksanakan sesi bimbingan, telaah, dan verifikasi kelayakan judul laporan Praktik Kerja Lapangan (Prakerin) bagi peserta didik sebagai berikut:
    </p>

    {{-- I. Data Peserta Didik & Industri --}}
    <div class="section-label">I. Identitas Peserta Didik & Industri Mitra</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Siswa</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa?->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Nomor Induk Siswa (NIS)</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa?->nis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Kelas / Rombel PKL</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $kelas }} / {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tempat PKL (Industri)</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $penempatan->industri?->nama_industri ?? '-' }} ({{ $penempatan->industri?->kota ?? 'Lampung' }})</td>
        </tr>
    </table>

    {{-- II. Rincian Pengajuan Judul --}}
    <div class="section-label">II. Rincian Pengajuan Judul Laporan</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Pengajuan Ke-</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $judul->pengajuan_ke }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu Pengajuan</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $judul->diajukan_at ? $judul->diajukan_at->isoFormat('dddd, D MMMM YYYY HH:mm') . ' WIB' : '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Judul yang Diajukan</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="color: #b91c1c; font-size: 10pt;">
                "{{ $judul->judul_laporan }}"
            </td>
        </tr>
        @if($judul->deskripsi_judul)
        <tr>
            <td class="info-label">Ringkasan Rencana</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="font-weight: normal; color: #374151;">
                {{ $judul->deskripsi_judul }}
            </td>
        </tr>
        @endif
    </table>

    {{-- III. Hasil Keputusan & Catatan Pembimbing --}}
    <div class="section-label">III. Hasil Keputusan Pembimbing Sekolah</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Keputusan Pembimbing</td>
            <td class="info-colon">:</td>
            <td class="info-value">
                @if($isAcc)
                    <span class="status-badge status-disetujui">DISETUJUI (ACC JUDUL)</span>
                @else
                    <span class="status-badge status-ditolak">DITOLAK (PERLU PERBAIKAN / REVISI)</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">Catatan & Arahan Pembimbing</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="font-weight: normal;">
                @if(!$isAcc)
                    <div class="box-catatan">
                        <strong>Alasan / Catatan Penolakan:</strong><br>
                        {{ $catatanPembimbing ?: 'Judul laporan kurang relevan dengan lingkup pekerjaan siswa di industri. Harap melakukan revisi judul.' }}
                    </div>
                @else
                    <div class="box-catatan-acc">
                        <strong>Catatan Pengesahan:</strong><br>
                        {{ $catatanPembimbing ?: 'Judul laporan telah memenuhi standar kompetensi dan disetujui untuk penulisan bab laporan.' }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Bagian Tanda Tangan Pembimbing --}}
    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-date">
                Bandar Lampung, {{ $tanggalReview ? $tanggalReview->isoFormat('D MMMM YYYY') : now()->isoFormat('D MMMM YYYY') }}
            </div>
            <div class="sig-role">Guru Pembimbing Sekolah</div>

            <div class="sig-qr">
                <img src="{{ $qrCodeBase64 }}" width="90" height="90" alt="QR Code Digital Signature">
            </div>

            <div class="sig-name">
                {{ $guru?->nama_lengkap ?? $guruUser?->name ?? 'Guru Pembimbing Sekolah' }}
            </div>
            <div class="sig-nip">
                NIP/NIK: {{ $guru?->nip ?? $guru?->nik ?? '-' }}
            </div>
            <div style="font-size: 7.5pt; color: #047857; margin-top: 2px; font-weight: 700;">
                ✓ DITANDATANGANI SECARA DIGITAL
            </div>
        </div>
        <div class="clear"></div>
    </div>

    {{-- Watermark / Validitas --}}
    <div class="watermark">
        Dokumen ini diterbitkan secara otomatis dan sah melalui Sistem Informasi Akademik & Kesiswaan SMK Telkom Lampung.<br>
        Keaslian dokumen dapat diverifikasi dengan memindai QR Code di atas.
    </div>
</body>
</html>
