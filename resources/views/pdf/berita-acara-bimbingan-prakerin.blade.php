<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Bimbingan Laporan Prakerin - {{ $siswa?->nama_lengkap }}</title>
    @php
        if (!isset($kopBase64)) {
            $appSettingKop = \App\Models\AppSetting::first();
            $kopBase64 = $appSettingKop ? $appSettingKop->getKopSuratUkkBase64() : null;
        }
        $guruNip = $guruNip ?? ($guru?->dapodikGuru?->nip ?? $guruUser?->masterGuru?->dapodikGuru?->nip);
    @endphp
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        /* Kop Surat Banner Edge-to-Edge dari Super Admin */
        .kop-banner-wrap {
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 0;
        }
        .kop-banner-img {
            width: 100%;
            height: auto;
            max-height: 125px;
            display: block;
            margin: 0;
            padding: 0;
        }
        .kop-fallback-wrap {
            padding: 12mm 20mm 0 20mm;
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
        .doc-container {
            padding: 8px 20mm 20mm 20mm;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 16px;
        }
        .doc-title {
            font-size: 12pt;
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
            margin-top: 12px;
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
            padding: 3.5px 6px;
            vertical-align: top;
            font-size: 9pt;
        }
        .info-label {
            width: 25%;
            color: #4b5563;
            font-weight: 600;
        }
        .info-colon {
            width: 2%;
            text-align: center;
        }
        .info-value {
            width: 73%;
            color: #111827;
            font-weight: 700;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
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
        .status-revisi {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .anotasi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 12px;
        }
        .anotasi-table th {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            font-size: 8.5pt;
            text-align: left;
            color: #374151;
        }
        .anotasi-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 18px;
            width: 100%;
        }
        .sig-box {
            float: right;
            width: 45%;
            text-align: center;
        }
        .sig-date {
            font-size: 9pt;
            margin-bottom: 4px;
            color: #374151;
        }
        .sig-role {
            font-size: 9pt;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .sig-qr {
            margin: 4px auto;
            text-align: center;
        }
        .sig-name {
            font-size: 9.5pt;
            font-weight: 800;
            text-decoration: underline;
            color: #111827;
        }
        .sig-nip {
            font-size: 8.5pt;
            color: #4b5563;
            margin-top: 2px;
        }
        .clear {
            clear: both;
        }
        .footer-watermark {
            position: fixed;
            bottom: 8mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            font-size: 7.5pt;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            padding-top: 4px;
            line-height: 1.35;
        }
    </style>
</head>
<body>
    {{-- Header Kop Surat Edge-to-Edge (Menggunakan Kop Surat UKK dari Pengaturan Super Admin) --}}
    @if(!empty($kopBase64))
        <div class="kop-banner-wrap">
            <img src="{{ $kopBase64 }}" alt="Kop Surat Resmi" class="kop-banner-img">
        </div>
    @else
        <div class="kop-fallback-wrap">
            <table class="header-table">
                <tr>
                    <td style="width: 100%; text-align: center;">
                        <h1 class="header-title">SMK TELKOM LAMPUNG</h1>
                        <h2 class="header-subtitle">HUBUNGAN INDUSTRI (HUBIN) & PRAKERIN</h2>
                        <p class="header-address">Jl. Purnawirawan No. 01, Pasir Gintung, Kec. Tj. Karang Barat, Kota Bandar Lampung, Lampung</p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="doc-container">
        {{-- Judul Dokumen --}}
        <div class="doc-title-container">
            <h3 class="doc-title">
                @if($isAcc)
                    BERITA ACARA PENGESAHAN (ACC) BIMBINGAN LAPORAN PRAKERIN
                @else
                    BERITA ACARA REVISI BIMBINGAN LAPORAN PRAKERIN
                @endif
            </h3>
            <p class="doc-number">Nomor: {{ $nomorBeritaAcara }}</p>
        </div>

        <p style="margin-bottom: 12px; font-size: 9.5pt;">
            Pada hari ini, <strong>{{ $tanggalReview ? $tanggalReview->isoFormat('dddd, D MMMM YYYY') : now()->isoFormat('dddd, D MMMM YYYY') }}</strong>, telah dilaksanakan sesi bimbingan dan pemeriksaan dokumen laporan Praktik Kerja Lapangan (Prakerin) untuk peserta didik:
        </p>

        {{-- Data Peserta Didik --}}
        <div class="section-label">I. Identitas Peserta Didik & Industri</div>
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
                <td class="info-label">Kelas / Rombel</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $kelas }} (Rombel: {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }})</td>
            </tr>
            <tr>
                <td class="info-label">Industri Mitra PKL</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $penempatan->industri?->nama_industri ?? '-' }} ({{ $penempatan->industri?->kota ?? 'Lampung' }})</td>
            </tr>
            <tr>
                <td class="info-label">Judul Laporan PKL</td>
                <td class="info-colon">:</td>
                <td class="info-value" style="color: #b91c1c;">"{{ $laporan->judul_laporan ?? 'Belum ada judul' }}"</td>
            </tr>
        </table>

        {{-- Detail Bimbingan --}}
        <div class="section-label">II. Materi & Hasil Pemeriksaan Bimbingan</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Materi / Bab Bimbingan</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $tahap->judul_tahap }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Pengajuan Siswa</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $tahap->diajukan_at?->isoFormat('D MMMM YYYY, HH:mm') ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Pemeriksaan Pembimbing</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $tanggalReview ? $tanggalReview->isoFormat('D MMMM YYYY, HH:mm') : now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</td>
            </tr>
            <tr>
                <td class="info-label">Status Keputusan</td>
                <td class="info-colon">:</td>
                <td>
                    @if($isAcc)
                        <span class="status-badge status-disetujui">DISETUJUI / DISAHKAN (ACC BAB)</span>
                    @else
                        <span class="status-badge status-revisi">PERLU REVISI {{ $revisiKe ? '(Revisi ke-' . $revisiKe . ')' : '' }} - {{ $anotasis->count() }} Poin Catatan</span>
                    @endif
                </td>
            </tr>
            @if($catatanPembimbing)
                <tr>
                    <td class="info-label">Catatan Umum Pembimbing</td>
                    <td class="info-colon">:</td>
                    <td class="info-value" style="font-weight: 500; font-style: italic;">
                        "{{ $catatanPembimbing }}"
                    </td>
                </tr>
            @endif
        </table>

        {{-- Rekap Catatan / Anotasi Lembar Dokumen --}}
        @if($anotasis->isNotEmpty())
            <div class="section-label">III. Poin-Poin Coretan / Revisi pada Dokumen (Total: {{ $anotasis->count() }} Poin)</div>
            <table class="anotasi-table">
                <thead>
                    <tr>
                        <th style="width: 8%; text-align: center;">No</th>
                        <th style="width: 14%; text-align: center;">Halaman</th>
                        <th style="width: 20%;">Jenis Coretan</th>
                        <th style="width: 58%;">Catatan & Arahan Revisi Pembimbing</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anotasis as $idx => $anotasi)
                        <tr>
                            <td style="text-align: center; font-weight: 700;">{{ $idx + 1 }}</td>
                            <td style="text-align: center;">Hal. {{ $anotasi->halaman }}</td>
                            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $anotasi->tipe_anotasi) }}</td>
                            <td>{{ $anotasi->catatan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Tanda Tangan Digital Guru Pembimbing --}}
        <div class="signature-section">
            <div class="sig-box">
                <p class="sig-date">Pringsewu, {{ $tanggalReview ? $tanggalReview->isoFormat('D MMMM YYYY') : now()->isoFormat('D MMMM YYYY') }}</p>
                <p class="sig-role">Guru Pembimbing Internal,</p>

                <div class="sig-qr">
                    <img src="{{ $qrCodeBase64 }}" width="80" height="80" alt="QR Digital Signature">
                </div>

                <p class="sig-name">{{ $guru?->nama_lengkap ?? $guruUser?->name ?? 'Guru Pembimbing' }}</p>
                <p class="sig-nip">
                    NIP: {{ ($guruNip && trim($guruNip) !== '') ? $guruNip : '-' }}
                </p>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    {{-- Watermark / Validitas di Paling Bawah Dokumen --}}
    <div class="footer-watermark">
        Dokumen ini diterbitkan secara otomatis dan sah melalui Sistem Informasi Akademik & Kesiswaan SMK Telkom Lampung.<br>
        Keaslian dokumen dapat diverifikasi dengan memindai QR Code di atas.
    </div>
</body>
</html>
