<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Izin Meninggalkan Tugas - {{ $izin->guru->nama_lengkap ?? 'Guru' }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.4;
            font-size: 9.5pt;
            margin: 0;
            padding: 0;
        }
        
        /* ════════ Kop Surat ════════ */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .kop-logo-cell {
            width: 70px;
            vertical-align: middle;
            text-align: left;
            padding-right: 12px;
        }
        .kop-logo {
            max-width: 68px;
            max-height: 68px;
            display: block;
        }
        .kop-text-cell {
            vertical-align: middle;
            text-align: center;
            padding-right: 70px; /* Counter-balance logo width so text is visually centered */
        }
        .kop-org {
            font-size: 8.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin: 0 0 2px 0;
        }
        .school-name {
            font-size: 15pt;
            font-weight: 800;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            color: #b91c1c; /* Telkom Red Accent */
            letter-spacing: 0.8px;
        }
        .school-info {
            font-size: 7.8pt;
            margin: 0;
            color: #475569;
            line-height: 1.35;
        }
        .school-contacts {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .kop-divider {
            border-top: 2.5px solid #0f172a;
            border-bottom: 0.8px solid #0f172a;
            height: 2px;
            margin-top: 8px;
            margin-bottom: 16px;
        }

        /* ════════ Judul & Nomor ════════ */
        .header-section {
            text-align: center;
            margin-bottom: 14px;
        }
        .title {
            font-size: 12pt;
            font-weight: 800;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .doc-number {
            font-size: 8.8pt;
            color: #475569;
            margin-top: 3px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* ════════ Pre-amble & Data Panel ════════ */
        .intro-text {
            font-size: 9pt;
            color: #334155;
            margin: 0 0 8px 0;
        }
        .info-card {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #b91c1c;
            background-color: #f8fafc;
            padding: 9px 14px;
            margin-bottom: 14px;
            border-radius: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 3.5px 0;
            vertical-align: top;
            font-size: 9pt;
        }
        .data-table .label {
            width: 170px;
            font-weight: 700;
            color: #475569;
        }
        .data-table .separator {
            width: 18px;
            text-align: center;
            color: #64748b;
            font-weight: bold;
        }
        .data-table .value {
            color: #0f172a;
            font-weight: 500;
        }

        /* ════════ Schedule Table ════════ */
        .section-title {
            font-weight: 700;
            margin-bottom: 6px;
            font-size: 8.8pt;
            text-transform: uppercase;
            color: #1e293b;
            border-left: 3px solid #b91c1c;
            padding-left: 8px;
            letter-spacing: 0.4px;
        }
        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }
        .jadwal-table th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            letter-spacing: 0.3px;
        }
        .jadwal-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            color: #1e293b;
        }
        .jadwal-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* ════════ Closing Statement ════════ */
        .closing-text {
            font-size: 9pt;
            color: #334155;
            margin: 0 0 16px 0;
            line-height: 1.45;
        }

        /* ════════ Footer & Signatures ════════ */
        .footer-section {
            margin-top: 10px;
        }
        .date-place {
            text-align: right;
            margin-bottom: 14px;
            font-size: 9pt;
            color: #1e293b;
            font-weight: 500;
            padding-right: 15px;
        }
        .signature-grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .sig-box {
            text-align: center;
            vertical-align: top;
            padding: 0 6px;
        }
        .sig-role {
            font-size: 8.8pt;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #1e293b;
            letter-spacing: 0.3px;
        }
        .ttd-digital-wrap {
            margin: 0 auto 6px auto;
            text-align: center;
        }
        .ttd-digital-qr {
            width: 50px;
            height: 50px;
            padding: 2px;
            border: 1px solid #c7d2fe;
            background: #ffffff;
            display: inline-block;
        }
        .ttd-digital-badge {
            font-size: 5.5pt;
            color: #4338ca;
            margin-top: 2px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }
        .sig-space {
            height: 58px;
        }
        .sig-name-wrap {
            white-space: nowrap !important;
            overflow: visible;
            line-height: 1.25;
            margin-top: 2px;
        }
        .sig-name {
            font-weight: 700;
            text-decoration: underline;
            font-size: 9pt;
            color: #0f172a;
            white-space: nowrap !important;
            display: inline-block;
        }
        .sig-nip {
            font-size: 8pt;
            color: #475569;
            margin-top: 3px;
            white-space: nowrap !important;
            font-weight: 500;
        }
        .sig-date {
            font-size: 7.2pt;
            color: #94a3b8;
            margin-top: 2px;
            white-space: nowrap !important;
        }

        /* ════════ Bottom Verification Note ════════ */
        .bottom-note {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 1px dashed #cbd5e1;
            font-size: 7.2pt;
            color: #64748b;
            text-align: center;
            line-height: 1.35;
        }
    </style>
</head>
<body>
    {{-- Kop Surat Resmi --}}
    <table class="kop-table">
        <tr>
            @if(!empty($logoBase64))
            <td class="kop-logo-cell">
                <img src="{{ $logoBase64 }}" class="kop-logo" alt="Logo Sekolah">
            </td>
            @endif
            <td class="kop-text-cell" @if(empty($logoBase64)) style="padding-right: 0;" @endif>
                <div class="kop-org">Yayasan Pendidikan Telkom</div>
                <h1 class="school-name">{{ $settings->school_name ?? 'SMK TELKOM LAMPUNG' }}</h1>
                <p class="school-info">
                    {{ $settings->address ?? 'Jl. Raya Gadingrejo No. 01, Gadingrejo, Kec. Gadingrejo, Kab. Pringsewu, Lampung 35372' }}
                </p>
                <div class="school-contacts">
                    Email: {{ $settings->email ?? 'info@smktelkom-lpg.sch.id' }} &nbsp;|&nbsp;
                    Telp: {{ $settings->phone ?? '-' }} &nbsp;|&nbsp;
                    Website: www.smktelkom-lpg.sch.id
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    {{-- Title Section --}}
    <div class="header-section">
        <h2 class="title">SURAT IZIN MENINGGALKAN TUGAS</h2>
        <div class="doc-number">Nomor: {{ $izin->id }}/IZIN-GURU/{{ $izin->created_at ? $izin->created_at->format('m/Y') : date('m/Y') }}</div>
    </div>

    {{-- Info Pegawai --}}
    <div class="content-section">
        <p class="intro-text">Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        
        <div class="info-card">
            <table class="data-table">
                <tr>
                    <td class="label">Nama Pegawai</td>
                    <td class="separator">:</td>
                    <td class="value" style="font-weight: 700; font-size: 9.5pt; color: #0f172a;">{{ $izin->guru->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label">NIP / NUPTK</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $izin->guru->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori / Keperluan</td>
                    <td class="separator">:</td>
                    <td class="value">
                        <strong>{{ $izin->categoryLabel() }}</strong> &mdash; {{ $izin->jenis_izin }}
                        @if($izin->deskripsi)
                            <span style="font-style: italic; color: #475569;">"{{ $izin->deskripsi }}"</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Waktu Pelaksanaan</td>
                    <td class="separator">:</td>
                    <td class="value" style="color: #0f172a; font-weight: 600;">
                        @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                            {{ $izin->tanggal_mulai->translatedFormat('l, d F Y') }}
                            <span style="color: #475569; font-weight: normal;">(Pukul {{ $izin->tanggal_mulai->format('H:i') }} &ndash; {{ $izin->tanggal_selesai->format('H:i') }} WIB)</span>
                        @else
                            {{ $izin->tanggal_mulai->translatedFormat('d F Y, H:i') }} &ndash; {{ $izin->tanggal_selesai->translatedFormat('d F Y, H:i') }} WIB
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Detail Jadwal Pelajaran yang Terdampak --}}
        <div class="section-title">Detail Jam Pelajaran yang Ditinggalkan</div>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th style="width: 12%; text-align: center;">Jam Ke</th>
                    <th style="width: 22%; text-align: center;">Waktu</th>
                    <th style="width: 26%;">Kelas</th>
                    <th>Mata Pelajaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($izin->jadwals as $jadwal)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $jadwal->jam_ke }}</td>
                    <td style="text-align: center; color: #475569;">{{ substr($jadwal->jam_mulai, 0, 5) }} &ndash; {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</td>
                    <td style="font-weight: 600;">{{ $jadwal->rombel->kelas->nama_kelas ?? '-' }}</td>
                    <td>{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #94a3b8; padding: 10px;">
                        Tidak ada jam pelajaran reguler tatap muka yang terdata pada rentang waktu perizinan ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <p class="closing-text">
            Demikian surat izin ini dibuat untuk dapat dipergunakan sebagaimana mestinya dan pihak yang berkepentingan dapat memakluminya.
        </p>
    </div>

    {{-- Footer & Tanda Tangan --}}
    @php
        $columns = [];
        if (in_array($izin->kategori_penyetujuan, ['sekolah', 'luar'], true)) {
            $columns[] = [
                'role' => 'Guru Piket',
                'user' => $izin->piket,
                'date' => $izin->piket_at,
                'qr'   => $qrPiketBase64 ?? null,
            ];
        }
        if ($izin->kategori_penyetujuan === 'luar') {
            $columns[] = [
                'role' => 'Waka Kurikulum',
                'user' => $izin->kurikulum,
                'date' => $izin->kurikulum_at,
                'qr'   => $qrKurikulumBase64 ?? null,
            ];
        }
        if ($izin->kategori_penyetujuan !== 'sekolah' || $izin->guru?->is_tpa) {
            $columns[] = [
                'role' => 'KAUR SDM',
                'user' => $izin->sdm,
                'date' => $izin->sdm_at,
                'qr'   => $qrSdmBase64 ?? null,
            ];
        }
        if ($izin->status_kepala_sekolah !== 'tidak_diperlukan') {
            $columns[] = [
                'role' => 'Kepala Sekolah',
                'user' => $izin->kepalaSekolah,
                'date' => $izin->kepala_sekolah_at,
                'qr'   => $qrKepalaSekolahBase64 ?? null,
            ];
        }
        $colCount = count($columns);
        $colWidth = $colCount > 0 ? floor(100 / $colCount) : 100;
    @endphp

    <div class="footer-section">
        <div class="date-place">
            Pringsewu, {{ ($izin->sdm_at ?? $izin->kepala_sekolah_at ?? now())->translatedFormat('d F Y') }}
        </div>
        
        <table class="signature-grid">
            <tr>
                @foreach($columns as $col)
                <td class="sig-box" style="width: {{ $colWidth }}%;">
                    <div class="sig-role">{{ $col['role'] }}</div>
                    
                    @if(!empty($col['qr']))
                        <div class="ttd-digital-wrap">
                            <img src="{{ $col['qr'] }}" class="ttd-digital-qr" alt="QR TTD">
                            <div class="ttd-digital-badge">&#9679; TTD DIGITAL SAH</div>
                        </div>
                    @else
                        <div class="sig-space"></div>
                    @endif

                    <div class="sig-name-wrap">
                        <span class="sig-name">{{ $col['user']?->name ?? '....................................' }}</span>
                    </div>

                    <div class="sig-nip">
                        NIP. {{ $col['user']?->nip ?? '-' }}
                    </div>

                    @if(!empty($col['date']))
                        <div class="sig-date">Tgl: {{ $col['date']->format('d/m/Y H:i') }}</div>
                    @endif
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    {{-- Catatan Keabsahan Digital --}}
    <div class="bottom-note">
        Dokumen resmi ini diterbitkan secara elektronik melalui Sistem Informasi SMK Telkom Lampung.
        Keabsahan tanda tangan digital dapat diverifikasi dengan memindai kode QR yang tertera pada dokumen.
    </div>
</body>
</html>
