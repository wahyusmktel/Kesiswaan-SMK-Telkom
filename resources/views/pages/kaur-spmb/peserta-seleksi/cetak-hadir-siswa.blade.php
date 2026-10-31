<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir Calon Siswa - {{ $hariTanggalStr }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Standar Ukuran Kertas A4: 210mm x 297mm */
        @page {
            size: A4 portrait;
            margin: 0; /* Zero margin agar Kop Surat menempel penuh edge-to-edge */
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            color: #111827;
            background-color: #cbd5e1;
            margin: 0;
            padding: 0;
        }

        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        @media screen and (max-width: 840px) {
            .page-sheet {
                width: 100%;
                min-height: auto;
            }
            .screen-toolbar {
                width: 100% !important;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-sheet {
                width: 210mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                page-break-after: always;
            }
            .page-sheet:last-child {
                page-break-after: auto;
            }
            .kop-banner {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .kop-banner img {
                width: 100% !important;
                display: block !important;
            }
            .doc-body {
                padding-left: 14mm !important;
                padding-right: 14mm !important;
                padding-top: 3mm !important;
                padding-bottom: 8mm !important;
            }
            table.table-doc {
                page-break-inside: auto;
            }
            table.table-doc tr {
                page-break-inside: avoid;
            }
            table.table-doc th, table.table-doc td {
                padding: 4px 5px !important;
                font-size: 8pt !important;
                line-height: 1.2 !important;
            }
        }

        /* Bordered table styling */
        table.table-doc {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-doc th, table.table-doc td {
            border: 1px solid #000000;
            padding: 4.5px 6px;
            font-size: 8.5pt;
            line-height: 1.25;
            vertical-align: middle;
        }
        table.table-doc th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body class="min-h-screen py-6 print:py-0">

    {{-- SCREEN TOOLBAR (HIDDEN ON PRINT) --}}
    <div class="no-print screen-toolbar mx-auto mb-4 bg-white border border-slate-300 rounded-lg p-3.5 shadow-sm" style="width: 210mm; max-width: 95%;">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('kaur-spmb.administrasi.peserta-seleksi.index', ['tanggal_id' => $tanggal->id]) }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Seleksi
                </a>
                <span class="text-xs text-slate-500 font-medium">
                    Jadwal: <strong class="text-slate-800">{{ $hariTanggalStr }}</strong> ({{ $pesertas->count() }} Calon Siswa)
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Presensi Siswa (Print / PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR A4: DAFTAR HADIR PESERTA CALON SISWA SELEKSI     --}}
    {{-- ======================================================== --}}
    <div class="page-sheet">

        {{-- KOP SURAT (FULL EDGE-TO-EDGE TANPA MARGIN) --}}
        <div class="kop-banner w-full flex-shrink-0">
            @if(!empty($kopBase64))
                <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block" style="width: 100%; display: block; margin: 0; padding: 0;">
            @else
                <div class="relative w-full pt-3 pb-2 px-8 border-b-2 border-red-600 bg-white">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-shrink-0 flex items-center gap-2">
                            <div class="w-12 h-12 bg-red-600 rounded flex flex-col items-center justify-center text-white font-bold tracking-tight text-center shadow-sm">
                                <span class="text-[11px] uppercase leading-none font-extrabold">Telkom</span>
                                <span class="text-[8.5px] uppercase leading-none font-semibold mt-0.5">Schools</span>
                            </div>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="text-[9pt] font-semibold tracking-wider text-slate-800 uppercase">YAYASAN PENDIDIKAN TELKOM</div>
                            <div class="text-[14pt] font-extrabold text-slate-900 tracking-wide uppercase leading-tight">SMK Telkom Lampung</div>
                            <div class="text-[7.5pt] text-slate-700 font-semibold mt-0.5">
                                Kompetensi Keahlian : (1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi
                            </div>
                            <div class="text-[7pt] text-slate-600 mt-0.5">
                                Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372 - Telp/Faks : 0721 5621 547 • NPSN : 69944770
                            </div>
                            <div class="text-[7pt] text-red-600 font-medium">
                                w: www.smktelkom-lpg.sch.id &nbsp; e: smktelkom9914@gmail.com / telkomschool@smktelkom-lpg.sch.id
                            </div>
                        </div>
                        <div class="w-12 flex-shrink-0 flex justify-end">
                            <div class="w-10 h-9 border-t-4 border-r-4 border-red-600 rounded-tr-lg opacity-80"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ISI DOKUMEN --}}
        <div class="doc-body flex-1 flex flex-col px-8 sm:px-10 pt-3 pb-8 print:px-0">

            {{-- JUDUL --}}
            <div class="text-center mb-3">
                <h1 class="text-[12pt] font-bold tracking-wide uppercase underline decoration-1 underline-offset-2">
                    DAFTAR HADIR PESERTA CALON SISWA SELEKSI
                </h1>
                <p class="text-[9.5pt] font-semibold text-slate-800 mt-0.5">
                    SELEKSI PENERIMAAN MURID BARU (SPMB) TAHUN PELAJARAN {{ $tahunAjaran }}
                </p>
            </div>

            {{-- INFORMASI PELAKSANAAN --}}
            <table class="w-full text-[9pt] mb-3" style="border: none;">
                <tr>
                    <td style="width: 15%; padding: 2px 0; font-weight: bold;">Hari, Tanggal</td>
                    <td style="width: 2%; padding: 2px 0;">:</td>
                    <td style="width: 48%; padding: 2px 0;">{{ $hariTanggalStr }}</td>
                    <td style="width: 10%; padding: 2px 0; font-weight: bold;">Waktu</td>
                    <td style="width: 2%; padding: 2px 0;">:</td>
                    <td style="width: 23%; padding: 2px 0;">07.30 - 12.00 WIB</td>
                </tr>
                <tr>
                    <td style="padding: 2px 0; font-weight: bold;">Tempat</td>
                    <td style="padding: 2px 0;">:</td>
                    <td style="padding: 2px 0;">SMK Telkom Lampung</td>
                    <td style="padding: 2px 0; font-weight: bold;">Petugas</td>
                    <td style="padding: 2px 0;">:</td>
                    <td style="padding: 2px 0;">{{ $tanggal->kelompok?->nama_kelompok ?? 'Kelompok Piket SPMB' }}</td>
                </tr>
            </table>

            {{-- TABEL PRESENSI SISWA --}}
            <table class="table-doc mb-4">
                <thead>
                    <tr>
                        <th style="width: 5%;">NO</th>
                        <th style="width: 16%;">NO. DAFTAR</th>
                        <th style="width: 33%;">NAMA CALON SISWA</th>
                        <th style="width: 6%;">L/P</th>
                        <th style="width: 18%;">NO. WHATSAPP SISWA</th>
                        <th style="width: 15%;">TANDA TANGAN</th>
                        <th style="width: 7%;">KET</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesertas as $index => $item)
                        <tr>
                            <td class="text-center font-bold">{{ $index + 1 }}</td>
                            <td class="text-center font-mono font-semibold">{{ $item->nomor_pendaftaran }}</td>
                            <td class="font-semibold">{{ $item->nama_lengkap }}</td>
                            <td class="text-center">{{ $item->jenis_kelamin }}</td>
                            <td class="text-center font-mono text-[8pt]">{{ $item->no_wa_siswa ?: '-' }}</td>
                            <td style="height: 28px; vertical-align: bottom;">
                                @if(($index + 1) % 2 != 0)
                                    <span style="font-size: 7.5pt; color: #475569;">{{ $index + 1 }}. ............</span>
                                @else
                                    <div style="text-align: right;">
                                        <span style="font-size: 7.5pt; color: #475569;">{{ $index + 1 }}. ............</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center text-[7.5pt]">
                                @if($item->status_kehadiran_siswa === 'Hadir')
                                    <span class="font-bold text-emerald-800">Hadir</span>
                                @elseif($item->status_kehadiran_siswa === 'Tidak Hadir')
                                    <span class="text-red-700">A</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-500 italic">
                                Belum ada calon siswa yang terdaftar pada tanggal piket ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- KETERANGAN RINGKAS & TANDA TANGAN --}}
            <div class="mt-auto pt-2">
                <div class="flex justify-between items-end">
                    <div class="text-[8pt] text-slate-600">
                        <p class="font-semibold text-slate-800">Rekap Presensi Peserta:</p>
                        <p>Total Terjadwal: <strong>{{ $pesertas->count() }}</strong> Siswa | Hadir: <strong>{{ $pesertas->where('status_kehadiran_siswa', 'Hadir')->count() }}</strong> Siswa</p>
                        <p class="italic mt-1 text-[7.5pt]">* Lembar ini digunakan untuk verifikasi kehadiran fisik saat sesi seleksi &amp; ujian SPMB.</p>
                    </div>

                    <div class="text-center" style="width: 220px;">
                        <p class="text-[9pt]">Pringsewu, {{ \Carbon\Carbon::parse($tanggal->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</p>
                        <p class="text-[9pt] font-semibold mt-0.5">Kaur SPMB SMK Telkom Lampung</p>
                        <div style="height: 55px;"></div>
                        <p class="text-[9pt] font-bold underline decoration-1">{{ $namaKaur }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
