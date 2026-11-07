<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu Riwayat Bimbingan Laporan Prakerin - {{ $siswa?->nama_lengkap }}</title>
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
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.35;
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
            padding: 12mm 18mm 0 18mm;
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
        .doc-container {
            padding: 8px 18mm 20mm 18mm;
        }
        .doc-title-container {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            color: #111827;
            margin: 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-table td {
            padding: 3px 6px;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .info-label {
            width: 22%;
            color: #4b5563;
            font-weight: 600;
        }
        .info-colon {
            width: 2%;
            text-align: center;
        }
        .info-value {
            width: 76%;
            color: #111827;
            font-weight: 700;
        }
        .bimbingan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
        }
        .bimbingan-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 5px 6px;
            font-size: 8pt;
            color: #374151;
            font-weight: 700;
            text-align: left;
        }
        .bimbingan-table td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            font-size: 8pt;
            vertical-align: top;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-disetujui {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-revisi {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .badge-diajukan {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .signature-section {
            margin-top: 15px;
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
            font-size: 8.5pt;
            font-weight: 700;
            color: #111827;
            margin-bottom: 5px;
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
            color: #4b5563;
            margin-top: 2px;
        }
        .clear {
            clear: both;
        }
        .final-acc-banner {
            border: 1.5px solid #059669;
            background-color: #f0fdf4;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .final-acc-title {
            color: #065f46;
            font-weight: 800;
            font-size: 9.5pt;
            text-transform: uppercase;
        }
        .footer-watermark {
            position: fixed;
            bottom: 8mm;
            left: 18mm;
            right: 18mm;
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
                        <h2 class="header-subtitle">KARTU KENDALI BIMBINGAN LAPORAN PRAKERIN</h2>
                        <p class="header-address">Jl. Purnawirawan No. 01, Pasir Gintung, Kec. Tj. Karang Barat, Kota Bandar Lampung</p>
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div class="doc-container">
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
                <td class="info-label">NIS / NISN</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $siswa?->nis ?? '-' }} / {{ $siswa?->nisn ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Kelas / Rombel</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $kelas }} (Rombel PKL: {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }})</td>
            </tr>
            <tr>
                <td class="info-label">Industri Mitra PKL</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $penempatan->industri?->nama_industri ?? '-' }} ({{ $penempatan->industri?->kota ?? 'Lampung' }})</td>
            </tr>
            <tr>
                <td class="info-label">Guru Pembimbing</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $guru?->nama_lengkap ?? $guruUser?->name ?? 'Guru Pembimbing Sekolah' }}</td>
            </tr>
            <tr>
                <td class="info-label">Judul Laporan Prakerin</td>
                <td class="info-colon">:</td>
                <td class="info-value" style="color: #b91c1c;">"{{ $laporan->judul_laporan ?? 'Belum ada judul laporan' }}"</td>
            </tr>
        </table>

        {{-- Banner Jika Laporan Selesai di-ACC --}}
        @if($laporan->status_laporan === 'selesai_acc')
            <div class="final-acc-banner">
                <div class="final-acc-title">&#10003; LAPORAN PRAKERIN TELAH DI-ACC FINAL</div>
                <div style="font-size: 8pt; color: #047857; margin-top: 2px;">
                    Laporan telah dinyatakan lengkap dan memenuhi seluruh standar bimbingan. Disahkan pada: <strong>{{ $laporan->acc_at ? $laporan->acc_at->isoFormat('D MMMM YYYY, HH:mm') : '-' }} WIB</strong>.
                </div>
            </div>
        @endif

        {{-- Tabel Log Kemajuan Bimbingan (Termasuk Riwayat Judul & Seluruh Bab) --}}
        <table class="bimbingan-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 27%;">Materi / Tahapan Bimbingan</th>
                    <th style="width: 14%; text-align: center;">Tgl Pengajuan</th>
                    <th style="width: 14%; text-align: center;">Tgl Review</th>
                    <th style="width: 13%; text-align: center;">Status</th>
                    <th style="width: 27%;">Catatan Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNo = 1; @endphp

                {{-- Riwayat Pengajuan & Review Judul --}}
                @if(isset($judulRiwayats) && $judulRiwayats->count() > 0)
                    @foreach($judulRiwayats as $jHistory)
                        <tr style="background-color: #fafaf9;">
                            <td style="text-align: center; font-weight: 700;">{{ $rowNo++ }}</td>
                            <td>
                                <strong>Pengajuan Judul (Ke-{{ $jHistory->pengajuan_ke }})</strong>
                                <div style="font-size: 7.5pt; color: #4b5563; margin-top: 1px; font-style: italic;">
                                    "{{ $jHistory->judul_laporan }}"
                                </div>
                            </td>
                            <td style="text-align: center;">
                                {{ $jHistory->diajukan_at ? $jHistory->diajukan_at->format('d/m/Y') : '-' }}
                            </td>
                            <td style="text-align: center;">
                                {{ $jHistory->reviewed_at ? $jHistory->reviewed_at->format('d/m/Y') : '-' }}
                            </td>
                            <td style="text-align: center;">
                                @if($jHistory->status === 'disetujui')
                                    <span class="badge-status badge-disetujui">Disetujui</span>
                                @elseif($jHistory->status === 'ditolak')
                                    <span class="badge-status badge-revisi">Ditolak</span>
                                @else
                                    <span class="badge-status badge-diajukan">Ditinjau</span>
                                @endif
                            </td>
                            <td>
                                {{ $jHistory->catatan_pembimbing ?: ($jHistory->status === 'disetujui' ? 'Judul disetujui untuk penulisan laporan.' : '-') }}
                            </td>
                        </tr>
                    @endforeach
                @elseif($laporan->judul_laporan)
                    <tr style="background-color: #fafaf9;">
                        <td style="text-align: center; font-weight: 700;">{{ $rowNo++ }}</td>
                        <td>
                            <strong>Pengajuan Judul Laporan</strong>
                            <div style="font-size: 7.5pt; color: #4b5563; margin-top: 1px; font-style: italic;">
                                "{{ $laporan->judul_laporan }}"
                            </div>
                        </td>
                        <td style="text-align: center;">
                            {{ $laporan->judul_diajukan_at?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            {{ $laporan->judul_reviewed_at?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            @if($laporan->status_judul === 'disetujui')
                                <span class="badge-status badge-disetujui">Disetujui</span>
                            @elseif($laporan->status_judul === 'ditolak')
                                <span class="badge-status badge-revisi">Ditolak</span>
                            @else
                                <span class="badge-status badge-diajukan">Ditinjau</span>
                            @endif
                        </td>
                        <td>
                            {{ $laporan->catatan_judul ?? ($laporan->status_judul === 'disetujui' ? 'Judul telah disetujui untuk penulisan laporan.' : '-') }}
                        </td>
                    </tr>
                @endif

                {{-- Entri Bab Laporan --}}
                @forelse($tahaps as $tahap)
                    <tr>
                        <td style="text-align: center; font-weight: 700;">{{ $rowNo++ }}</td>
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
                <p class="sig-date">Pringsewu, {{ $laporan->acc_at ? $laporan->acc_at->isoFormat('D MMMM YYYY') : now()->isoFormat('D MMMM YYYY') }}</p>
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
