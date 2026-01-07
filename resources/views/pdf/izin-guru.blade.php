<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Izin Meninggalkan Tugas - {{ $izin->guru->nama_lengkap }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a202c;
            line-height: 1.5;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }
        
        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px solid #1a202c;
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #c53030; /* Crimson Red for Telkom vibe */
        }
        .school-info {
            font-size: 9pt;
            margin: 5px 0 0;
            color: #4a5568;
        }

        /* Judul & Nomor */
        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 14pt;
            font-weight: 800;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 10pt;
            color: #718096;
            margin-top: 5px;
        }

        /* Content Table */
        .content-section {
            margin-bottom: 30px;
        }
        .data-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .data-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .data-table .label {
            width: 160px;
            font-weight: bold;
            color: #4a5568;
        }
        .data-table .separator {
            width: 20px;
            text-align: center;
        }
        .data-table .value {
            font-weight: 600;
        }

        /* Schedule Table */
        .table-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 10pt;
            text-transform: uppercase;
            color: #2d3748;
            border-left: 4px solid #c53030;
            padding-left: 10px;
        }
        .jadwal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .jadwal-table th {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            color: #4a5568;
        }
        .jadwal-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
        }
        .jadwal-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Footer/Signatures */
        .footer-section {
            margin-top: 50px;
        }
        .date-place {
            text-align: right;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        .signature-grid {
            width: 100%;
            table-layout: fixed;
        }
        .sig-box {
            text-align: center;
            vertical-align: top;
        }
        .sig-role {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 60px;
            color: #4a5568;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
            display: block;
        }
        .sig-id {
            font-size: 8pt;
            color: #718096;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    {{-- Kop Surat --}}
    <div class="kop-surat">
        <h1 class="school-name">{{ $settings->school_name ?? 'SMK TELKOM LAMPUNG' }}</h1>
        <p class="school-info">
            {{ $settings->address ?? 'Jl. Raya Gading Rejo, Gading Rejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372' }}<br>
            Email: {{ $settings->email ?? 'info@smktelkom-lpg.sch.id' }} | Telp: {{ $settings->phone ?? '-' }}
        </p>
    </div>

    {{-- Title Section --}}
    <div class="header-section">
        <h2 class="title">SURAT IZIN MENINGGALKAN TUGAS</h2>
        <div class="doc-number">Nomor: {{ $izin->id }}/IZIN-GURU/{{ date('m/Y') }}</div>
    </div>

    {{-- Info Pegawai --}}
    <div class="content-section">
        <p style="margin-bottom: 15px;">Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
        <table class="data-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="separator">:</td>
                <td class="value">{{ $izin->guru->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">NIP / NUPTK</td>
                <td class="separator">:</td>
                <td class="value">{{ $izin->guru->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Meninggalkan Tugas</td>
                <td class="separator">:</td>
                <td class="value">
                    @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                        {{ $izin->tanggal_mulai->translatedFormat('d F Y') }} <span style="color: #718096">({{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }})</span>
                    @else
                        {{ $izin->tanggal_mulai->translatedFormat('d F Y, H:i') }} s/d {{ $izin->tanggal_selesai->translatedFormat('d F Y, H:i') }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Alasan / Keperluan</td>
                <td class="separator">:</td>
                <td class="value">{{ $izin->jenis_izin }} - <span style="font-style: italic; font-weight: normal; color: #4a5568;">"{{ $izin->deskripsi }}"</span></td>
            </tr>
        </table>

        {{-- Jadwal Dampak --}}
        <div class="table-title">Detail Jam Pelajaran yang Ditinggalkan</div>
        <table class="jadwal-table">
            <thead>
                <tr>
                    <th style="width: 10%; text-align: center;">Jam Ke</th>
                    <th style="width: 20%; text-align: center;">Waktu</th>
                    <th style="width: 25%;">Kelas</th>
                    <th>Mata Pelajaran</th>
                </tr>
            </thead>
            <tbody>
                @forelse($izin->jadwals as $jadwal)
                <tr>
                    <td style="text-align: center;">{{ $jadwal->jam_ke }}</td>
                    <td style="text-align: center; color: #718096;">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                    <td>{{ $jadwal->rombel->kelas->nama_kelas }}</td>
                    <td>{{ $jadwal->mataPelajaran->nama_mapel }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; font-style: italic; color: #a0aec0; padding: 20px;">
                        Tidak ada jam pelajaran rutin yang terdata dalam rentang waktu izin.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer-section">
        <div class="date-place">Pringsewu, {{ now()->translatedFormat('d F Y') }}</div>
        
        <table class="signature-grid">
            <tr>
                <td class="sig-box">
                    <div class="sig-role">Guru Piket</div>
                    <span class="sig-name">{{ $izin->piket->name ?? '..........................' }}</span>
                    @if($izin->piket)
                    <div class="sig-id">Tgl: {{ $izin->piket_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    @endif
                </td>
                <td class="sig-box">
                    <div class="sig-role">Waka Kurikulum</div>
                    <span class="sig-name">{{ $izin->kurikulum->name ?? '..........................' }}</span>
                    @if($izin->kurikulum)
                    <div class="sig-id">Tgl: {{ $izin->kurikulum_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    @endif
                </td>
                <td class="sig-box">
                    <div class="sig-role">KAUR SDM</div>
                    <span class="sig-name">{{ $izin->sdm->name ?? '..........................' }}</span>
                    @if($izin->sdm)
                    <div class="sig-id">Tgl: {{ $izin->sdm_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div style="position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8pt; color: #cbd5e0;">
        Dokumen ini diterbitkan secara elektronik melalui Aplikasi Izin SMK Telkom Lampung dan sah sebagai surat keterangan resmi.
    </div>
</body>
</html>
