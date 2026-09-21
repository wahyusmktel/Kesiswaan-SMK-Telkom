<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lembar Distribusi Akun Siswa - {{ $rombel->kelas->nama_kelas ?? 'Kelas' }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: a4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1f2937;
            line-height: 1.35;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 3px double #b91c1c;
        }

        .kop-logo {
            width: 70px;
            vertical-align: middle;
            text-align: left;
        }

        .kop-logo img {
            max-height: 60px;
            max-width: 65px;
        }

        .kop-logo-placeholder {
            width: 55px;
            height: 55px;
            background-color: #b91c1c;
            color: #ffffff;
            font-size: 22pt;
            font-weight: bold;
            text-align: center;
            line-height: 55px;
            border-radius: 8px;
        }

        .kop-text {
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }

        .kop-institution {
            font-size: 9pt;
            font-weight: 600;
            letter-spacing: 1px;
            color: #4b5563;
            text-transform: uppercase;
        }

        .kop-school {
            font-size: 14pt;
            font-weight: bold;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
        }

        .kop-address {
            font-size: 7.5pt;
            color: #4b5563;
            line-height: 1.25;
        }

        /* Title */
        .doc-title {
            text-align: center;
            margin: 10px 0 12px 0;
        }

        .doc-title h2 {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-title p {
            font-size: 8.5pt;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Info Card */
        .info-card {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #b91c1c;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-label {
            font-weight: 600;
            color: #475569;
            width: 110px;
        }

        .info-value {
            color: #0f172a;
            font-weight: 500;
        }

        /* Data Table */
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8pt;
        }

        .student-table thead {
            display: table-header-group;
        }

        .student-table tr {
            page-break-inside: avoid;
        }

        .student-table th {
            background-color: #b91c1c;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
            padding: 6px 4px;
            border: 1px solid #991b1b;
            text-align: center;
        }

        .student-table td {
            padding: 5px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .student-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .student-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .mono {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
            font-size: 7.5pt;
        }

        .badge-active {
            display: inline-block;
            background-color: #ecfdf5;
            color: #065f46;
            border: 0.5px solid #a7f3d0;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending {
            display: inline-block;
            background-color: #fffbeb;
            color: #92400e;
            border: 0.5px solid #fde68a;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-cell {
            height: 24px;
            text-align: left;
            padding-left: 6px !important;
            color: #94a3b8;
            font-size: 7pt;
        }

        /* Instructions & Signature Block */
        .bottom-block {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .instructions-box {
            background-color: #f1f5f9;
            border: 1px dashed #cbd5e1;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 7.5pt;
            color: #334155;
            line-height: 1.35;
        }

        .instructions-box strong {
            color: #b91c1c;
        }

        .instructions-box ol {
            margin-left: 14px;
            margin-top: 3px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-table td {
            vertical-align: top;
            padding: 0;
        }

        .signature-signer {
            text-align: center;
            width: 220px;
            float: right;
            font-size: 8pt;
        }

        .signature-space {
            height: 50px;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            font-size: 8.5pt;
        }

        .signer-title {
            color: #64748b;
            font-size: 7.5pt;
            margin-top: 2px;
        }

        .footer-note {
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Kop Sekolah -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @else
                    <div class="kop-logo-placeholder">T</div>
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-institution">YAYASAN PENDIDIKAN TELKOM</div>
                <div class="kop-school">{{ $schoolSetting?->school_name ?? 'SMK TELKOM LAMPUNG' }}</div>
                <div class="kop-address">
                    {{ $schoolSetting?->address ?? 'Jl. Purnawirawan No. 1 Gedong Meneng, Rajabasa, Bandar Lampung' }}
                    @if($schoolSetting?->phone) | Telp: {{ $schoolSetting->phone }} @endif
                    @if($schoolSetting?->email) | Email: {{ $schoolSetting->email }} @endif
                </div>
            </td>
            <td style="width: 70px;"></td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h2>LEMBAR DISTRIBUSI AKUN PENGGUNA SISFO</h2>
        <p>Daftar Akun Login Siswa untuk Portal Sistem Informasi Kesiswaan</p>
    </div>

    <!-- Informasi Kelas / Rombel -->
    <div class="info-card">
        <table class="info-table">
            <tr>
                <td class="info-label">Rombongan Belajar</td>
                <td style="width: 5px;">:</td>
                <td class="info-value" style="width: 40%;"><strong>{{ $rombel->kelas?->nama_kelas ?? '-' }}</strong></td>
                <td class="info-label">Tahun Pelajaran</td>
                <td style="width: 5px;">:</td>
                <td class="info-value">{{ $rombel->tahunPelajaran?->tahun ?? $rombel->tahun_ajaran }} ({{ ucfirst($rombel->tahunPelajaran?->semester ?? '-') }})</td>
            </tr>
            <tr>
                <td class="info-label">Wali Kelas</td>
                <td>:</td>
                <td class="info-value">{{ $rombel->waliKelas?->name ?? 'Belum Ditentukan' }}</td>
                <td class="info-label">Total Peserta Didik</td>
                <td>:</td>
                <td class="info-value">{{ $students->count() }} Siswa</td>
            </tr>
            <tr>
                <td class="info-label">URL Portal Sisfo</td>
                <td>:</td>
                <td class="info-value"><span class="mono">{{ url('/login') }}</span></td>
                <td class="info-label">Tanggal Cetak</td>
                <td>:</td>
                <td class="info-value">{{ now()->isoFormat('D MMMM Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Akun Siswa -->
    <table class="student-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 65px;">NIS</th>
                <th style="width: 75px;">NISN</th>
                <th style="width: 145px;" class="text-left">Nama Lengkap</th>
                <th style="width: 25px;">L/P</th>
                <th style="width: 140px;" class="text-left">Email / Username Login</th>
                <th style="width: 70px;">Kata Sandi</th>
                <th style="width: 50px;">Status</th>
                <th style="width: 80px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $siswa)
                @php
                    $userEmail = $siswa->user?->email ?? ($siswa->nis ? $siswa->nis . '@smktelkom-lpg.sch.id' : '-');
                    $isRegistered = (bool)$siswa->user_id;
                    $gender = strtoupper(substr($siswa->jenis_kelamin ?? ($siswa->dapodik?->jenis_kelamin ?? '-'), 0, 1));
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center mono">{{ $siswa->nis ?: '-' }}</td>
                    <td class="text-center mono">{{ $siswa->dapodik?->nisn ?: '-' }}</td>
                    <td class="text-left" style="font-weight: 500;">
                        {{ $siswa->nama_lengkap }}
                    </td>
                    <td class="text-center">{{ $gender ?: '-' }}</td>
                    <td class="text-left mono" style="font-size: 7pt; color: #1e293b;">
                        {{ $userEmail }}
                    </td>
                    <td class="text-center mono" style="color: #b91c1c;">
                        smktelkom
                    </td>
                    <td class="text-center">
                        @if($isRegistered)
                            <span class="badge-active">Aktif</span>
                        @else
                            <span class="badge-pending">Draft</span>
                        @endif
                    </td>
                    <td class="signature-cell">
                        {{ $index + 1 }}. ............
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #94a3b8; font-style: italic;">
                        Belum ada siswa yang terdaftar pada rombel ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Petunjuk dan Tanda Tangan -->
    <div class="bottom-block">
        <div class="instructions-box">
            <strong>Petunjuk Penting Distribusi & Penggunaan Akun Siswa:</strong>
            <ol>
                <li>Siswa dapat mengakses portal aplikasi melalui browser di tautan: <strong>{{ url('/login') }}</strong>.</li>
                <li>Masuk menggunakan <strong>Email / Username</strong> dan <strong>Kata Sandi Default: <span class="mono">smktelkom</span></strong> yang tertera pada lembar ini.</li>
                <li>Demi keamanan akun dan kerahasiaan data pribadi, siswa <strong>wajib segera memperbarui kata sandi</strong> setelah berhasil masuk pertama kali melalui menu Profil.</li>
                <li>Lembar ini dapat digunakan oleh Wali Kelas / Guru sebagai arsip dan bukti fisik tanda terima akun dari siswa.</li>
            </ol>
        </div>

        <table class="signature-table">
            <tr>
                <td style="width: 60%; vertical-align: bottom;">
                    <div style="font-size: 7.5pt; color: #64748b;">
                        <div>Ringkasan Rombel:</div>
                        <div>• Siswa Terdaftar: <strong>{{ $students->count() }}</strong> orang</div>
                        <div>• Akun Aktif: <strong>{{ $students->whereNotNull('user_id')->count() }}</strong> orang</div>
                        <div>• Akun Belum Dibuat: <strong>{{ $students->whereNull('user_id')->count() }}</strong> orang</div>
                    </div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <div class="signature-signer">
                        <div>Bandar Lampung, {{ now()->isoFormat('D MMMM Y') }}</div>
                        <div style="margin-top: 2px;">Wali Kelas {{ $rombel->kelas?->nama_kelas ?? '' }}</div>
                        <div class="signature-space"></div>
                        <div class="signer-name">{{ $rombel->waliKelas?->name ?? '....................................' }}</div>
                        <div class="signer-title">NIP. {{ $rombel->waliKelas?->nip ?? '................................' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer-note">
            Dokumen resmi Sistem Informasi Kesiswaan SMK Telkom Lampung &bull; Dicetak secara otomatis pada {{ now()->format('d/m/Y H:i') }} WIB
        </div>
    </div>

</body>
</html>
