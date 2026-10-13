<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Realisasi - {{ $rab->judul_pengajuan }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            background: #f1f5f9;
        }

        .print-control-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .print-control-bar button, .print-control-bar a {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .print-control-bar .btn-secondary {
            background: #475569;
        }

        .print-control-bar button:hover {
            background: #dc2626;
        }

        .print-control-bar .btn-secondary:hover {
            background: #334155;
        }

        .page-sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 60px auto 30px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            position: relative;
        }

        @media print {
            body {
                background: #ffffff;
            }
            .print-control-bar {
                display: none !important;
            }
            .page-sheet {
                width: 100%;
                margin: 0;
                box-shadow: none;
                min-height: auto;
            }
        }

        /* ── KOP SURAT UKK (NO MARGIN) ── */
        .kop-banner {
            width: 100%;
            margin: 0;
            padding: 0;
            line-height: 0;
        }

        .kop-banner img {
            width: 100%;
            display: block;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .kop-fallback-wrap {
            padding: 10mm 12mm 0 12mm;
            position: relative;
        }

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
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            color: #111111;
            margin: 0;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 20pt;
            font-weight: 800;
            color: #000000;
            margin: 1px 0 2px 0;
            letter-spacing: -0.2px;
        }

        .kop-keahlian-title {
            font-size: 7.8pt;
            font-style: italic;
            color: #222222;
            margin: 0;
        }

        .kop-keahlian-list {
            font-size: 7pt;
            color: #222222;
            margin: 1px 0;
        }

        .kop-alamat {
            font-size: 7pt;
            color: #222222;
            margin: 1px 0;
        }

        .red-divider {
            width: 100%;
            height: 2.2pt;
            background-color: #b91c1c;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        /* ── DOKUMEN KONTEN (MARGIN SEMPIT) ── */
        .doc-content {
            padding: 5mm 12mm 10mm 12mm;
        }

        .doc-title-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .doc-title-main {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
        }

        .doc-title-subject {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .doc-title-year {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 165px;
            font-weight: bold;
            color: #111111;
        }

        .meta-separator {
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        .meta-value {
            color: #111111;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border: 1px solid #000000;
        }

        .items-table th {
            border: 1px solid #000000;
            padding: 5px 4px;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            text-transform: uppercase;
            background-color: #ffffff;
            vertical-align: middle;
        }

        .items-table td {
            border: 1px solid #000000;
            padding: 5px 6px;
            font-size: 8pt;
            vertical-align: top;
            color: #000000;
        }

        .col-no {
            width: 32px;
            text-align: center;
        }

        .col-tgl {
            width: 75px;
            text-align: center;
            white-space: nowrap;
        }

        .col-uraian {
            text-align: left;
        }

        .col-nominal {
            width: 95px;
            text-align: right;
            white-space: nowrap;
        }

        .col-ket {
            width: 100px;
            text-align: center;
        }

        .items-table tr.total-row td {
            font-weight: bold;
            font-size: 8pt;
            background-color: #ffffff;
            padding: 5px 6px;
        }

        .items-table tr.total-row td.total-label {
            text-align: center;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
            border: 1px solid #000000;
        }

        .approval-table th {
            border: 1px solid #000000;
            padding: 5px 4px;
            font-weight: bold;
            text-align: center;
            font-size: 7.8pt;
            text-transform: uppercase;
            background-color: #ffffff;
        }

        .approval-table td {
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 8pt;
            vertical-align: middle;
        }

        .col-appr-role {
            width: 180px;
            font-weight: normal;
        }

        .col-appr-person {
            width: 200px;
            text-align: center;
        }

        .col-appr-jabatan {
            width: 130px;
            text-align: center;
        }

        .col-appr-date {
            width: 90px;
            text-align: center;
            white-space: nowrap;
        }

        .col-appr-sign {
            text-align: center;
            min-height: 40px;
        }
    </style>
</head>
<body>

    <div class="print-control-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('rab.realisasi.show', $rab) }}" class="btn-secondary">
                ← Kembali
            </a>
            <span style="font-weight: 600; font-size: 14px;">Preview Cetak Laporan Realisasi Anggaran</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('rab.realisasi.pdf', $rab) }}" class="btn-secondary">
                Unduh PDF
            </a>
            <button onclick="window.print()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Cetak Dokumen
            </button>
        </div>
    </div>

    <div class="page-sheet">

        {{-- KOP SURAT UKK (NO MARGIN) --}}
        @if(!empty($kopBase64))
            <div class="kop-banner">
                <img src="{{ $kopBase64 }}" alt="Kop Surat">
            </div>
        @else
            <div class="kop-fallback-wrap">
                <table class="kop-table">
                    <tr>
                        <td class="kop-logo-cell">
                            <img src="{{ asset('images/teaching-module/smk-telkom-lampung.png') }}" class="kop-logo-img" alt="Logo">
                        </td>
                        <td class="kop-text-cell">
                            <div class="kop-yayasan">YAYASAN PENDIDIKAN TELKOM</div>
                            <div class="kop-sekolah">SMK Telkom Lampung</div>
                            <div class="kop-keahlian-title">Kompetensi Keahlian :</div>
                            <div class="kop-keahlian-list">(1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi</div>
                            <div class="kop-alamat">Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372 - Telp/Faks : 0721 5621 547</div>
                        </td>
                    </tr>
                </table>
                <div class="red-divider"></div>
            </div>
        @endif

        {{-- DOKUMEN KONTEN --}}
        <div class="doc-content">

            {{-- Judul Dokumen Realisasi --}}
            <div class="doc-title-container">
                <div class="doc-title-main">LAPORAN REALISASI PENGGUNAAN ANGGARAN</div>
                <div class="doc-title-subject">{{ $rab->judul_pengajuan }}</div>
                <div class="doc-title-year">TAHUN ANGGARAN {{ $rab->tahun_anggaran }}</div>
            </div>

            {{-- Metadata Akun & Anggaran --}}
            <table class="meta-table">
                <tr>
                    <td class="meta-label">NOMOR AKUN</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $rab->nomor_akun }}</td>
                </tr>
                <tr>
                    <td class="meta-label">NAMA AKUN</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $rab->nama_akun }}</td>
                </tr>
                <tr>
                    <td class="meta-label">DRK</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $rab->drk }}</td>
                </tr>
                <tr>
                    <td class="meta-label">KEBUTUHAN WAKTU</td>
                    <td class="meta-separator">:</td>
                    <td class="meta-value">{{ $rab->kebutuhan_waktu }}</td>
                </tr>
            </table>

            {{-- Tabel Realisasi Transaksi --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-tgl">TGL</th>
                        <th class="col-uraian">URAIAN</th>
                        <th class="col-nominal">PENERIMAAN</th>
                        <th class="col-nominal">PENGELUARAN</th>
                        <th class="col-nominal">SALDO</th>
                        <th class="col-ket">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($realisasi->items as $index => $item)
                        <tr>
                            <td class="col-no">{{ $index + 1 }}</td>
                            <td class="col-tgl">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d-M-y') }}</td>
                            <td class="col-uraian">{!! nl2br(e($item->uraian)) !!}</td>
                            <td class="col-nominal">
                                {{ $item->penerimaan > 0 ? 'Rp ' . number_format($item->penerimaan, 0, ',', '.') : '' }}
                            </td>
                            <td class="col-nominal">
                                {{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '' }}
                            </td>
                            <td class="col-nominal">
                                Rp {{ number_format($item->saldo, 0, ',', '.') }}
                            </td>
                            <td class="col-ket">
                                {{ !empty(trim((string) $item->keterangan)) ? $item->keterangan : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 10px;">Belum ada catatan realisasi transaksi.</td>
                        </tr>
                    @endforelse

                    {{-- Baris Total / Jumlah --}}
                    <tr class="total-row">
                        <td colspan="3" class="total-label">JUMLAH</td>
                        <td class="col-nominal">
                            Rp {{ number_format($realisasi->total_penerimaan, 0, ',', '.') }}
                        </td>
                        <td class="col-nominal">
                            Rp {{ number_format($realisasi->total_pengeluaran, 0, ',', '.') }}
                        </td>
                        <td class="col-nominal">
                            Rp {{ number_format($realisasi->saldo_akhir, 0, ',', '.') }}
                        </td>
                        <td class="col-ket"></td>
                    </tr>
                </tbody>
            </table>

            {{-- Tabel Matriks Pengesahan (3 Pihak) --}}
            <table class="approval-table">
                <thead>
                    <tr>
                        <th class="col-appr-role"></th>
                        <th class="col-appr-person">NAMA / NIK</th>
                        <th class="col-appr-jabatan">JABATAN</th>
                        <th class="col-appr-date">TANGGAL</th>
                        <th class="col-appr-sign">TANDA TANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-appr-role">Dibuat oleh</td>
                        <td class="col-appr-person">{{ $realisasi->pembuat_nama }} / {{ $realisasi->pembuat_nik }}</td>
                        <td class="col-appr-jabatan">{{ $realisasi->pembuat_jabatan }}</td>
                        <td class="col-appr-date">
                            {{ $realisasi->tanggal_dibuat ? \Carbon\Carbon::parse($realisasi->tanggal_dibuat)->translatedFormat('d-M-y') : '-' }}
                        </td>
                        <td class="col-appr-sign"></td>
                    </tr>
                    <tr>
                        <td class="col-appr-role">Diperiksa & Disetujui oleh</td>
                        <td class="col-appr-person">{{ $realisasi->pemeriksa_nama }} / {{ $realisasi->pemeriksa_nik }}</td>
                        <td class="col-appr-jabatan">{{ $realisasi->pemeriksa_jabatan }}</td>
                        <td class="col-appr-date">
                            {{ $realisasi->tanggal_diperiksa ? \Carbon\Carbon::parse($realisasi->tanggal_diperiksa)->translatedFormat('d-M-y') : '-' }}
                        </td>
                        <td class="col-appr-sign"></td>
                    </tr>
                    <tr>
                        <td class="col-appr-role">Diperiksa & Disetujui Realisasi</td>
                        <td class="col-appr-person">{{ $realisasi->penyetuju_nama }} / {{ $realisasi->penyetuju_nik }}</td>
                        <td class="col-appr-jabatan">{{ $realisasi->penyetuju_jabatan }}</td>
                        <td class="col-appr-date">
                            {{ $realisasi->tanggal_disetujui ? \Carbon\Carbon::parse($realisasi->tanggal_disetujui)->translatedFormat('d-M-y') : '-' }}
                        </td>
                        <td class="col-appr-sign"></td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>

</body>
</html>
