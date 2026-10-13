<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak RAB - {{ $rab->judul_pengajuan }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 18mm 15mm 18mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 20px;
            background-color: #f3f4f6;
        }

        .paper {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 15mm 18mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        /* Toolbar */
        .no-print-toolbar {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1e293b;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background-color: #dc2626;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #b91c1c;
        }

        .btn-secondary {
            background-color: #334155;
            color: #ffffff;
        }
        .btn-secondary:hover {
            background-color: #475569;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .paper {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: none;
            }
            .no-print-toolbar {
                display: none !important;
            }
        }

        /* Top-right decorative ribbon */
        .header-ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 145px;
            height: auto;
            z-index: 10;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .kop-logo-cell {
            width: 18%;
            vertical-align: middle;
            text-align: center;
            padding-right: 8px;
        }

        .kop-logo-img {
            max-width: 85px;
            max-height: 85px;
            display: inline-block;
        }

        .kop-text-cell {
            width: 82%;
            text-align: center;
            vertical-align: middle;
        }

        .kop-yayasan {
            font-size: 11.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            color: #111111;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 21pt;
            font-weight: 800;
            color: #000000;
            margin: 1px 0 2px 0;
            letter-spacing: -0.2px;
        }

        .kop-keahlian-title {
            font-size: 8pt;
            font-style: italic;
            color: #222222;
            margin: 0;
        }

        .kop-keahlian-list {
            font-size: 7.2pt;
            color: #222222;
            margin: 1px 0;
        }

        .kop-alamat {
            font-size: 7.2pt;
            color: #222222;
            margin: 1px 0;
        }

        .kop-npsn {
            font-size: 7.2pt;
            font-weight: bold;
            color: #222222;
            margin: 1px 0;
        }

        .kop-kontak {
            font-size: 7.2pt;
            color: #222222;
            margin: 1px 0;
        }

        /* Red Divider Line */
        .red-divider {
            width: 100%;
            height: 2.2pt;
            background-color: #b91c1c;
            margin-top: 4px;
            margin-bottom: 14px;
        }

        /* Document Title */
        .doc-title-container {
            text-align: center;
            margin-bottom: 14px;
        }

        .doc-title-main {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
        }

        .doc-title-subject {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .doc-title-year {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        /* Metadata Info */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.8pt;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 140px;
            font-weight: bold;
            color: #000;
        }

        .meta-colon {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        .meta-value {
            color: #111;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }

        .items-table th,
        .items-table td {
            border: 0.75pt solid #000000;
            padding: 4.5px 5px;
            vertical-align: middle;
        }

        .items-table th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ffffff;
            font-size: 8.2pt;
        }

        .items-table td.col-no {
            text-align: center;
            width: 5%;
        }

        .items-table td.col-uraian {
            text-align: left;
            width: 32%;
        }

        .items-table td.col-spesifikasi {
            text-align: center;
            width: 22%;
        }

        .items-table td.col-satuan {
            text-align: center;
            width: 9%;
        }

        .items-table td.col-vol {
            text-align: center;
            width: 6%;
        }

        .items-table td.col-harga {
            text-align: right;
            width: 13%;
            white-space: nowrap;
        }

        .items-table td.col-jumlah {
            text-align: right;
            width: 13%;
            white-space: nowrap;
        }

        .items-table .row-total td {
            font-weight: bold;
            font-size: 8.5pt;
        }

        .items-table .total-label {
            text-align: right;
            padding-right: 12px;
            text-transform: uppercase;
        }

        /* Approval Table */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.2pt;
            margin-bottom: 8px;
        }

        .approval-table th,
        .approval-table td {
            border: 0.75pt solid #000000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .approval-table th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ffffff;
        }

        .approval-table .col-role {
            width: 24%;
            text-align: left;
        }

        .approval-table .col-name {
            width: 32%;
            text-align: center;
        }

        .approval-table .col-jabatan {
            width: 18%;
            text-align: center;
        }

        .approval-table .col-tanggal {
            width: 12%;
            text-align: center;
            white-space: nowrap;
        }

        .approval-table .col-ttd {
            width: 14%;
            text-align: center;
            height: 44px;
        }

        .approval-table .catatan-anggaran-cell {
            padding: 5px 8px;
            font-size: 8.2pt;
            vertical-align: top;
            height: 38px;
        }

        /* Catatan Penutup Box */
        .catatan-box {
            width: 100%;
            border: 0.75pt dotted #333333;
            padding: 5px 8px;
            font-size: 8.2pt;
            line-height: 1.35;
            min-height: 35px;
        }

        .catatan-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: block;
        }
    </style>
</head>
<body>

    <div class="no-print-toolbar">
        <div style="font-size: 14px; font-weight: bold;">
            Dokumen RAB: {{ $rab->judul_pengajuan }} ({{ $rab->tahun_anggaran }})
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ Cetak / Print Dokumen
            </button>
            <a href="{{ route('rab.pdf', $rab) }}" class="btn btn-secondary">
                📥 Unduh File PDF
            </a>
            <a href="{{ route('rab.show', $rab) }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="paper">
        {{-- Top-Right Decorative Ribbon --}}
        @if(!empty($ribbonDataUri))
            <img src="{{ $ribbonDataUri }}" alt="Header Ribbon" class="header-ribbon">
        @endif

        {{-- Kop Surat --}}
        <table class="kop-table">
            <tr>
                <td class="kop-logo-cell">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo SMK Telkom" class="kop-logo-img">
                    @else
                        <div style="font-size: 20pt; font-weight: bold; color: #b91c1c;">TS</div>
                        <div style="font-size: 8pt; font-weight: bold; color: #b91c1c;">Telkom Schools</div>
                    @endif
                </td>
                <td class="kop-text-cell">
                    <div class="kop-yayasan">YAYASAN PENDIDIKAN TELKOM</div>
                    <div class="kop-sekolah">SMK Telkom Lampung</div>
                    <div class="kop-keahlian-title">Kompetensi Keahlian :</div>
                    <div class="kop-keahlian-list">(1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi</div>
                    <div class="kop-alamat">Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372 - Telp/Faks : 0721 5621 547</div>
                    <div class="kop-npsn">NPSN : 69944770</div>
                    <div class="kop-kontak">w: www.smktelkom-lpg.sch.id e: smktelkom9914@gmail.com / telkomschool@smktelkom-lpg.sch.id</div>
                </td>
            </tr>
        </table>

        {{-- Red Divider Line --}}
        <div class="red-divider"></div>

        {{-- Document Title --}}
        <div class="doc-title-container">
            <div class="doc-title-main">RENCANA ANGGARAN BIAYA (RAB)</div>
            <div class="doc-title-subject">{{ strtoupper($rab->judul_pengajuan) }}</div>
            <div class="doc-title-year">TAHUN ANGGARAN {{ $rab->tahun_anggaran }}</div>
        </div>

        {{-- Metadata Anggaran --}}
        <table class="meta-table">
            <tr>
                <td class="meta-label">NOMOR AKUN</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $rab->nomor_akun }}</td>
            </tr>
            <tr>
                <td class="meta-label">NAMA AKUN</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $rab->nama_akun }}</td>
            </tr>
            <tr>
                <td class="meta-label">DRK</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $rab->drk }}</td>
            </tr>
            <tr>
                <td class="meta-label">KEBUTUHAN WAKTU</td>
                <td class="meta-colon">:</td>
                <td class="meta-value">{{ $rab->kebutuhan_waktu }}</td>
            </tr>
        </table>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 32%;">URAIAN KEGIATAN</th>
                    <th style="width: 22%;">SPESIFIKASI</th>
                    <th style="width: 9%;">SATUAN</th>
                    <th style="width: 6%;">VOL</th>
                    <th style="width: 13%;">HARGA</th>
                    <th style="width: 13%;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rab->items as $index => $item)
                    <tr>
                        <td class="col-no">{{ $index + 1 }}</td>
                        <td class="col-uraian">{{ $item->uraian_kegiatan }}</td>
                        <td class="col-spesifikasi">{{ $item->spesifikasi ?: '-' }}</td>
                        <td class="col-satuan">{{ $item->satuan }}</td>
                        <td class="col-vol">{{ $item->volume == intval($item->volume) ? intval($item->volume) : number_format($item->volume, 1) }}</td>
                        <td class="col-harga">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="col-jumlah">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 10px;">Belum ada item rincian anggaran.</td>
                    </tr>
                @endforelse
                <tr class="row-total">
                    <td colspan="6" class="total-label">TOTAL ANGGARAN</td>
                    <td class="col-jumlah">Rp {{ number_format($rab->total_anggaran, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Approval Table --}}
        <table class="approval-table">
            <thead>
                <tr>
                    <th class="col-role"></th>
                    <th class="col-name">NAMA / NIK</th>
                    <th class="col-jabatan">JABATAN</th>
                    <th class="col-tanggal">TANGGAL</th>
                    <th class="col-ttd">TANDA TANGAN</th>
                </tr>
            </thead>
            <tbody>
                {{-- Dibuat oleh --}}
                <tr>
                    <td class="col-role">Dibuat oleh</td>
                    <td class="col-name">{{ $rab->pembuat_nama }}{{ $rab->pembuat_nik ? ' / ' . $rab->pembuat_nik : '' }}</td>
                    <td class="col-jabatan">{{ $rab->pembuat_jabatan ?: '-' }}</td>
                    <td class="col-tanggal">{{ $rab->tanggal_dibuat ? $rab->tanggal_dibuat->format('d-M-y') : '-' }}</td>
                    <td class="col-ttd"></td>
                </tr>

                {{-- Catatan Anggaran --}}
                <tr>
                    <td colspan="5" class="catatan-anggaran-cell">
                        <strong>CATATAN ANGGARAN:</strong><br>
                        <span>{{ $rab->catatan_anggaran ?: '-' }}</span>
                    </td>
                </tr>

                {{-- Diperiksa & Disetujui oleh --}}
                <tr>
                    <td class="col-role">Diperiksa & Disetujui oleh</td>
                    <td class="col-name">{{ $rab->pemeriksa_nama }}{{ $rab->pemeriksa_nik ? ' / ' . $rab->pemeriksa_nik : '' }}</td>
                    <td class="col-jabatan">{{ $rab->pemeriksa_jabatan ?: 'Kaur Keuangan' }}</td>
                    <td class="col-tanggal">{{ $rab->tanggal_diperiksa ? $rab->tanggal_diperiksa->format('d-M-y') : '-' }}</td>
                    <td class="col-ttd"></td>
                </tr>

                {{-- Diperiksa & Disetujui Realisasi --}}
                <tr>
                    <td class="col-role">Diperiksa & Disetujui Realisasi</td>
                    <td class="col-name">{{ $rab->penyetuju_nama }}{{ $rab->penyetuju_nik ? ' / ' . $rab->penyetuju_nik : '' }}</td>
                    <td class="col-jabatan">{{ $rab->penyetuju_jabatan ?: 'Kepala Sekolah' }}</td>
                    <td class="col-tanggal">{{ $rab->tanggal_disetujui ? $rab->tanggal_disetujui->format('d-M-y') : '-' }}</td>
                    <td class="col-ttd"></td>
                </tr>
            </tbody>
        </table>

        {{-- Catatan Penutup --}}
        <div class="catatan-box">
            <span class="catatan-title">CATATAN:</span>
            <span>{{ $rab->catatan ?: '-' }}</span>
        </div>
    </div>

</body>
</html>
