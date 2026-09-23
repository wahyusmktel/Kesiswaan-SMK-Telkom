<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Riwayat Bimbingan Laporan Prakerin - {{ $siswa?->nama_lengkap }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1.5cm 2cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1f2937;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #b91c1c;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header-title {
            font-size: 14pt;
            font-weight: 800;
            color: #b91c1c;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11pt;
            font-weight: 700;
            color: #111827;
            margin: 2px 0 0 0;
        }
        .header-address {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 3px;
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
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
            font-size: 9pt;
        }
        .info-label {
            width: 24%;
            color: #4b5563;
            font-weight: 600;
        }
        .info-colon {
            width: 2%;
            text-align: center;
        }
        .info-value {
            width: 74%;
            color: #111827;
            font-weight: 700;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .history-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 8.5pt;
            text-align: left;
            color: #111827;
            font-weight: 700;
        }
        .history-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-disetujui {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .badge-revisi {
            background-color: #fef2f2;
            color: #991b1b;
        }
        .badge-diajukan {
            background-color: #eff6ff;
            color: #1e40af;
        }
        .signature-section {
            margin-top: 20px;
            width: 100%;
        }
        .sig-box {
            float: right;
            width: 45%;
            text-align: center;
        }
        .sig-date {
            font-size: 8.5pt;
            margin-bottom: 4px;
            color: #374151;
        }
        .sig-role {
            font-size: 9pt;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .sig-qr {
            margin: 4px auto;
            text-align: center;
        }
        .sig-name {
            font-size: 9pt;
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
        .final-acc-banner {
            border: 2px solid #059669;
            background-color: #f0fdf4;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 10px;
            margin-bottom: 12px;
        }
        .final-acc-title {
            color: #065f46;
            font-weight: 800;
            font-size: 10pt;
            text-transform: uppercase;
        }
        .watermark {
            font-size: 7.5pt;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            padding-top: 6px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    {{-- Header Kop --}}
    <table class="header-table">
        <tr>
            <td style="width: 100%; text-align: center;">
                <h1 class="header-title">SMK TELKOM LAMPUNG</h1>
                <h2 class="header-subtitle">KARTU KENDALI BIMBINGAN LAPORAN PRAKERIN</h2>
                <p class="header-address">Jl. Purnawirawan No. 01, Pasir Gintung, Kec. Tj. Karang Barat, Kota Bandar Lampung</p>
            </td>
        </tr>
    </table>

    <div class="doc-title-container">
        <h3 class="doc-title">LEMBAR RIWAYAT & KEMAJUAN BIMBINGAN PRAKERIN</h3>
    </div>

    {{-- Data Siswa & PKL --}}
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Siswa</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa?->nama_lengkap ?? '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">NIS / Kelas</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $siswa?->nis ?? '-' }} / {{ $kelas }}</td>
        </tr>
        <tr>
            <td class="info-label">Tempat PKL (Industri)</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $penempatan->industri?->nama_industri ?? '-' }} ({{ $penempatan->industri?->kota ?? 'Lampung' }})</td>
        </tr>
        <tr>
            <td class="info-label">Judul Laporan PKL</td>
            <td class="info-colon">:</td>
            <td class="info-value" style="color:#b91c1c;">"{{ $laporan->judul_laporan ?? 'Belum Ditentukan' }}"</td>
        </tr>
        <tr>
            <td class="info-label">Guru Pembimbing</td>
            <td class="info-colon">:</td>
            <td class="info-value">{{ $guru?->nama_lengkap ?? $guruUser?->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- Status ACC Final jika sudah ACC --}}
    @if($laporan->status_laporan === 'selesai_acc')
        <div class="final-acc-banner">
            <div class="final-acc-title">✓ LAPORAN PRAKERIN TELAH DI-ACC (SELESAI)</div>
            <p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #047857;">
                Laporan telah disetujui secara tuntas oleh Guru Pembimbing pada <strong>{{ $laporan->acc_at?->isoFormat('D MMMM YYYY') }}</strong> dan dinyatakan memenuhi syarat pengesahan.
                @if($laporan->catatan_acc)
                    <br><em>"{{ $laporan->catatan_acc }}"</em>
                @endif
            </p>
        </div>
    @endif

    {{-- Tabel Riwayat Bimbingan Bab per Bab --}}
    <p style="font-weight: 700; margin-bottom: 4px; font-size: 9pt;">Riwayat Pemeriksaan Per Bab / Tahapan:</p>
    <table class="history-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Bab / Materi Bimbingan</th>
                <th style="width: 14%; text-align: center;">Tgl Pengajuan</th>
                <th style="width: 14%; text-align: center;">Tgl Review</th>
                <th style="width: 14%; text-align: center;">Status</th>
                <th style="width: 28%;">Catatan & Arahan Pembimbing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tahaps as $index => $tahap)
                <tr>
                    <td style="text-align: center; font-weight: 700;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $tahap->judul_tahap }}</strong>
                        @if($tahap->anotasis->count() > 0)
                            <div style="font-size: 7.5pt; color: #b91c1c; margin-top: 2px;">
                                ({{ $tahap->anotasis->count() }} Poin Coretan Anotasi)
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        {{ $tahap->diajukan_at?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td style="text-align: center;">
                        {{ $tahap->reviewed_at?->format('d/m/Y') ?? '-' }}
                    </td>
                    <td style="text-align: center;">
                        @if($tahap->status === 'disetujui')
                            <span class="badge-status badge-disetujui">Disetujui</span>
                        @elseif($tahap->status === 'perlu_revisi')
                            <span class="badge-status badge-revisi">Perlu Revisi</span>
                        @elseif($tahap->status === 'diajukan')
                            <span class="badge-status badge-diajukan">Ditinjau</span>
                        @else
                            <span class="badge-status" style="background:#f3f4f6; color:#6b7280;">{{ ucfirst(str_replace('_', ' ', $tahap->status)) }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $tahap->catatan_pembimbing ?? ($tahap->status === 'disetujui' ? 'Bab telah sesuai & disetujui.' : '-') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #9ca3af; padding: 15px;">Belum ada riwayat bimbingan bab.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pengesahan Digital Pembimbing --}}
    <div class="signature-section">
        <div class="sig-box">
            <p class="sig-date">Bandar Lampung, {{ $laporan->acc_at ? $laporan->acc_at->isoFormat('D MMMM YYYY') : now()->isoFormat('D MMMM YYYY') }}</p>
            <p class="sig-role">Guru Pembimbing Internal,</p>

            <div class="sig-qr">
                <img src="{{ $qrCodeBase64 }}" width="85" height="85" alt="QR Digital Signature">
            </div>

            <p class="sig-name">{{ $guru?->nama_lengkap ?? $guruUser?->name ?? 'Guru Pembimbing' }}</p>
            <p class="sig-nip">
                {{ $guru?->kode_guru ? 'Kode Guru: ' . $guru->kode_guru : ($guru?->nik ? 'NIK: ' . $guru->nik : 'Tanda Tangan Digital Terverifikasi') }}
            </p>
        </div>
        <div class="clear"></div>
    </div>

    {{-- Watermark --}}
    <div class="watermark">
        Dokumen ini sah dan diterbitkan secara digital melalui Sistem Informasi SMK Telkom Lampung.<br>
        Dapat dijadikan lampiran portofolio resmi laporan Praktik Kerja Lapangan (Prakerin).
    </div>
</body>
</html>
