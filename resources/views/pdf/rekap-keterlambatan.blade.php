<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Keterlambatan Siswa</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 18mm 22mm 18mm 22mm;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e293b;
            line-height: 1.35;
        }

        .page-container {
            width: 100%;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border-bottom: 2.5px solid #991b1b;
            padding-bottom: 8px;
        }

        .kop-logo {
            width: 52px;
            vertical-align: middle;
            text-align: left;
        }

        .kop-logo-box {
            width: 44px;
            height: 44px;
            background-color: #991b1b;
            color: #ffffff;
            font-size: 18pt;
            font-weight: 800;
            text-align: center;
            line-height: 44px;
            border-radius: 0;
        }

        .kop-text {
            vertical-align: middle;
            text-align: center;
            padding: 0 10px;
        }

        .kop-institution {
            font-size: 8.5pt;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #64748b;
            text-transform: uppercase;
        }

        .kop-school {
            font-size: 15pt;
            font-weight: 800;
            color: #991b1b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1px 0;
        }

        .kop-address {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.3;
        }

        /* Title Area */
        .doc-title-container {
            text-align: center;
            margin: 10px 0 14px 0;
        }

        .doc-title {
            font-size: 13pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-subtitle {
            font-size: 8pt;
            color: #64748b;
            margin-top: 3px;
        }

        /* KPI Mini Cards Bar - Kotak Biasa Tanpa Border Radius & Tanpa Border Sisi Kiri Kanan */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background-color: #f8fafc;
        }

        .kpi-card {
            background-color: #f8fafc;
            border-top: 1.5px solid #94a3b8;
            border-bottom: 1.5px solid #94a3b8;
            border-left: none !important;
            border-right: none !important;
            border-radius: 0 !important;
            padding: 8px 12px;
            text-align: center;
            vertical-align: middle;
        }

        .kpi-value {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Modern Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table th {
            background-color: #991b1b;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            border: 1px solid #7f1d1d;
            text-align: center;
            vertical-align: middle;
        }

        .data-table td {
            font-size: 8pt;
            padding: 6px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .row-even {
            background-color: #ffffff;
        }

        .row-odd {
            background-color: #f8fafc;
        }

        /* Badges - Kotak Biasa Tanpa Border Radius */
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 0 !important;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            white-space: nowrap;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 0.5px solid #86efac;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #b45309;
            border: 0.5px solid #fde68a;
        }

        .badge-purple {
            background-color: #f3e8ff;
            color: #7e22ce;
            border: 0.5px solid #d8b4fe;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 0.5px solid #fca5a5;
        }

        .badge-neutral {
            background-color: #f1f5f9;
            color: #475569;
            border: 0.5px solid #cbd5e1;
        }

        .class-badge {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 2px 6px;
            border-radius: 0 !important;
            font-size: 7pt;
            font-weight: 700;
        }

        .student-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 8.5pt;
        }

        .student-nis {
            font-size: 7pt;
            color: #64748b;
            font-family: Consolas, 'Courier New', monospace;
            margin-top: 1px;
        }

        .officer-name {
            font-size: 7.5pt;
            color: #334155;
            font-weight: 600;
        }

        .officer-sub {
            font-size: 6.5pt;
            color: #64748b;
        }

        /* Signatures Section */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            page-break-inside: avoid;
        }

        .signature-table td {
            vertical-align: top;
            text-align: center;
            width: 33.33%;
            padding: 0 15px;
            font-size: 8pt;
        }

        .signature-space {
            height: 52px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .signature-nip {
            font-size: 7pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Page Footer */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            font-size: 7pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    @php
        $totalRecords = $data->count();
        $uniqueStudents = $data->pluck('master_siswa_id')->filter()->unique()->count();
        
        $verifiedCount = $data->filter(function($row) {
            $st = strtoupper($row->status ?? '');
            return str_contains($st, 'SELESAI') || str_contains($st, 'VERIFIKASI');
        })->count();

        $pendingCount = $data->filter(function($row) {
            $st = strtoupper($row->status ?? '');
            return str_contains($st, 'MENUNGGU');
        })->count();

        $bkCount = $data->filter(function($row) {
            $st = strtoupper($row->status ?? '');
            return str_contains($st, 'BK') || str_contains($st, 'PENDAMPINGAN');
        })->count();

        $startDateFormatted = !empty($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y') : null;
        $endDateFormatted = !empty($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y') : null;
        $periodeStr = ($startDateFormatted && $endDateFormatted) 
            ? "Periode: {$startDateFormatted} s/d {$endDateFormatted}"
            : "Periode: Seluruh Riwayat Keterlambatan";
    @endphp

    <div class="page-container">
        <!-- Kop Surat Resmi -->
        <table class="kop-table">
            <tr>
                <td class="kop-logo">
                    <div class="kop-logo-box">T</div>
                </td>
                <td class="kop-text">
                    <div class="kop-institution">YAYASAN PENDIDIKAN TELKOM</div>
                    <div class="kop-school">SMK TELKOM LAMPUNG</div>
                    <div class="kop-address">Jl. Raya Gadingrejo No. 88, Pringsewu, Lampung • Website: smktelkom-lpg.sch.id • Email: info@smktelkom-lpg.sch.id</div>
                </td>
                <td style="width: 52px;"></td>
            </tr>
        </table>

        <!-- Judul Dokumen -->
        <div class="doc-title-container">
            <h1 class="doc-title">Laporan Rekapitulasi Keterlambatan Siswa</h1>
            <p class="doc-subtitle">{{ $periodeStr }}  •  Dicetak pada: {{ now()->translatedFormat('l, d F Y - H:i') }} WIB</p>
        </div>

        <!-- KPI Mini Cards - Kotak Biasa Tanpa Border Radius & Tanpa Border Sisi Kiri Kanan -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-card" style="width: 25%;">
                    <div class="kpi-value">{{ $totalRecords }}</div>
                    <div class="kpi-label">Total Catatan Keterlambatan</div>
                </td>
                <td class="kpi-card" style="width: 25%;">
                    <div class="kpi-value">{{ $uniqueStudents }}</div>
                    <div class="kpi-label">Jumlah Siswa Terlambat</div>
                </td>
                <td class="kpi-card" style="width: 25%;">
                    <div class="kpi-value">{{ $verifiedCount }}</div>
                    <div class="kpi-label">Telah Diverifikasi / Selesai</div>
                </td>
                <td class="kpi-card" style="width: 25%;">
                    <div class="kpi-value">{{ $pendingCount }} / {{ $bkCount }}</div>
                    <div class="kpi-label">Menunggu Piket / Proses BK</div>
                </td>
            </tr>
        </table>

        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3.5%;">No</th>
                    <th style="width: 11%;">Waktu Datang</th>
                    <th style="width: 19%;">Nama Siswa & NIS</th>
                    <th style="width: 9%;">Kelas</th>
                    <th style="width: 22.5%;">Alasan Keterlambatan</th>
                    <th style="width: 13%;">Status Pembinaan</th>
                    <th style="width: 11%;">Petugas Security</th>
                    <th style="width: 11%;">Guru Piket</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $late)
                    @php
                        $isOdd = ($index % 2 !== 0);
                        $statusRaw = strtoupper(trim($late->status ?? ''));
                        $badgeClass = match(true) {
                            str_contains($statusRaw, 'SELESAI') || str_contains($statusRaw, 'VERIFIKASI') => 'badge-success',
                            str_contains($statusRaw, 'MENUNGGU') => 'badge-warning',
                            str_contains($statusRaw, 'BK') || str_contains($statusRaw, 'PENDAMPINGAN') => 'badge-purple',
                            str_contains($statusRaw, 'TOLAK') => 'badge-danger',
                            default => 'badge-neutral',
                        };
                    @endphp
                    <tr class="{{ $isOdd ? 'row-odd' : 'row-even' }}">
                        <td style="text-align: center; color: #64748b; font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">
                            <strong style="color: #0f172a;">{{ $late->waktu_dicatat_security ? $late->waktu_dicatat_security->format('d/m/Y') : '-' }}</strong><br>
                            <span style="color: #64748b; font-size: 7.5pt;">Pukul {{ $late->waktu_dicatat_security ? $late->waktu_dicatat_security->format('H:i') : '-' }} WIB</span>
                        </td>
                        <td>
                            <div class="student-name">{{ $late->siswa?->nama_lengkap ?? 'Siswa Tidak Ditemukan' }}</div>
                            <div class="student-nis">NIS: {{ $late->siswa?->nis ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="class-badge">{{ $late->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span style="color: #334155; font-style: italic;">"{{ $late->alasan_siswa ?? '-' }}"</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge {{ $badgeClass }}">
                                {{ str_replace('_', ' ', strtoupper($late->status ?? '-')) }}
                            </span>
                        </td>
                        <td>
                            <div class="officer-name">{{ $late->security?->name ?? 'Security' }}</div>
                            <div class="officer-sub">Pencatat Gerbang</div>
                        </td>
                        <td>
                            <div class="officer-name">{{ $late->guruPiket?->name ?? '-' }}</div>
                            <div class="officer-sub">{{ $late->waktu_verifikasi_piket ? $late->waktu_verifikasi_piket->format('H:i') . ' WIB' : 'Belum verif' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8; font-style: italic;">
                            Tidak ada data keterlambatan siswa pada periode atau filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Tanda Tangan Resmi -->
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Wakil Kepala Sekolah Bid. Kesiswaan</strong>
                    <div class="signature-space"></div>
                    <div class="signature-name">Waka Kesiswaan, S.Pd.</div>
                    <div class="signature-nip">NIP. ........................................</div>
                </td>
                <td>
                    Mengetahui / Memeriksa,<br>
                    <strong>Koordinator Guru BK</strong>
                    <div class="signature-space"></div>
                    <div class="signature-name">Guru Bimbingan Konseling, S.Psi.</div>
                    <div class="signature-nip">NIP. ........................................</div>
                </td>
                <td>
                    Pringsewu, {{ now()->translatedFormat('d F Y') }}<br>
                    <strong>Koordinator Guru Piket Harian</strong>
                    <div class="signature-space"></div>
                    <div class="signature-name">Guru Piket Harian, S.Pd.</div>
                    <div class="signature-nip">NIP. ........................................</div>
                </td>
            </tr>
        </table>

        <!-- Footer Stempel Sistem -->
        <table class="footer-table">
            <tr>
                <td style="text-align: left;">
                    Dokumen resmi Rekapitulasi Kesiswaan SMK Telkom Lampung
                </td>
                <td style="text-align: right;">
                    Halaman dicetak otomatis dari SISFO Stella Kesiswaan
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
