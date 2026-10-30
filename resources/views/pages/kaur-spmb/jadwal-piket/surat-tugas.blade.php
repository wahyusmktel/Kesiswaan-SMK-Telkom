<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Tugas Piket SPMB - {{ $hariTanggalStr }}</title>
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
            background-color: #cbd5e1; /* Background kontras agar lembar kertas A4 terlihat jelas */
            margin: 0;
            padding: 0;
        }

        /* Lembar Kertas A4 Nyata (Layar & Cetak) */
        .page-container {
            width: 210mm;
            min-height: 297mm;
            height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        @media screen and (max-width: 840px) {
            .page-container {
                width: 100%;
                height: auto;
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
                height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                overflow: hidden !important;
            }
            .page-container {
                width: 210mm !important;
                height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                overflow: hidden !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
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
            .surat-body {
                padding-left: 18mm !important;
                padding-right: 18mm !important;
                padding-top: 3mm !important;
                padding-bottom: 8mm !important;
            }
            table.table-tugas {
                page-break-inside: avoid !important;
            }
            table.table-tugas th, table.table-tugas td {
                padding: 2px 5px !important;
                font-size: 8.5pt !important;
                line-height: 1.2 !important;
            }
            .ttd-container {
                page-break-inside: avoid !important;
            }
        }

        /* Bordered table styling */
        table.table-tugas {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-tugas th, table.table-tugas td {
            border: 1.2px solid #000000;
            padding: 2.5px 5.5px;
            font-size: 8.5pt;
            line-height: 1.2;
            vertical-align: middle;
        }
        table.table-tugas th {
            background-color: #e2e8f0;
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
                <a href="{{ route('kaur-spmb.jadwal-piket.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Jadwal Piket
                </a>
                <span class="text-xs text-slate-500 font-medium">
                    Tugas: <strong class="text-slate-800">{{ $hariTanggalStr }}</strong>
                </span>
            </div>

            <div class="flex items-center gap-2">
                @if($digitalDoc)
                    <a href="{{ route('verifikasi.dokumen', $digitalDoc->token) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-md transition-colors"
                        title="Verifikasi Keaslian Dokumen Digital">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Tervalidasi Digital (TTE)
                    </a>
                @endif

                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Surat Tugas (Print / PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- DOCUMENT SHEET A4 NYATA (210mm x 297mm) --}}
    <div class="page-container">

        {{-- 1. KOP SURAT (FULL EDGE-TO-EDGE TANPA MARGIN) --}}
        <div class="kop-banner w-full flex-shrink-0">
            @if(!empty($kopBase64))
                <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block" style="width: 100%; display: block; margin: 0; padding: 0;">
            @else
                {{-- Fallback HTML / CSS Kop Surat Telkom Schools SMK Telkom Lampung --}}
                <div class="relative w-full pt-3 pb-2 px-8 border-b-2 border-red-600 bg-white">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Logo Telkom Schools --}}
                        <div class="flex-shrink-0 flex items-center gap-2">
                            <div class="w-12 h-12 bg-red-600 rounded flex flex-col items-center justify-center text-white font-bold tracking-tight text-center shadow-sm">
                                <span class="text-[11px] uppercase leading-none font-extrabold">Telkom</span>
                                <span class="text-[8.5px] uppercase leading-none font-semibold mt-0.5">Schools</span>
                            </div>
                        </div>

                        {{-- Teks Lembaga & Sekolah --}}
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

                        {{-- Red wave accent placeholder --}}
                        <div class="w-12 flex-shrink-0 flex justify-end">
                            <div class="w-10 h-9 border-t-4 border-r-4 border-red-600 rounded-tr-lg opacity-80"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 2. SURAT BODY (PADDING RESMI DOKUMEN A4) --}}
        <div class="surat-body flex-1 flex flex-col px-8 sm:px-12 pt-3 pb-8 print:px-0">

            {{-- JUDUL & NOMOR SURAT --}}
            <div class="text-center mb-2.5">
                <h1 class="text-[11.5pt] font-bold tracking-wide uppercase underline decoration-1 underline-offset-2">
                    SURAT PERINTAH TUGAS
                </h1>
                <p class="text-[9.5pt] text-black mt-0.5">
                    Nomor: {{ $nomorSurat }}
                </p>
            </div>

            {{-- PEMBUKA --}}
            <div class="text-[9.5pt] leading-tight mb-2.5">
                <p>Yang bertanda tangan di bawah ini Kepala SMK Telkom Lampung:</p>
                <table class="mt-0.5 text-[9.5pt] leading-tight" style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0; width: 95px;">Nama</td>
                        <td style="border: none; padding: 1.5px 6px; width: 10px;">:</td>
                        <td style="border: none; padding: 1.5px 0;" class="font-medium">{{ $namaKepsek }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0;">NIP</td>
                        <td style="border: none; padding: 1.5px 6px;">:</td>
                        <td style="border: none; padding: 1.5px 0;">{{ $nipKepsek }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0;">Jabatan</td>
                        <td style="border: none; padding: 1.5px 6px;">:</td>
                        <td style="border: none; padding: 1.5px 0;">{{ $jabatanKepsek }}</td>
                    </tr>
                </table>
            </div>

            {{-- NARASI PENUGASAN --}}
            <div class="text-[9.5pt] leading-tight mb-2.5">
                <p>
                    Dengan ini menugaskan kepada nama-nama di bawah ini untuk melaksanakan Tugas <strong>Piket SPMB (Seleksi Penerimaan Murid Baru)</strong> Tahun Pelajaran <strong>{{ $tahunAjaran }}</strong> SMK Telkom Lampung pada:
                </p>
                <table class="mt-0.5 text-[9.5pt] leading-tight" style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0; width: 95px;">Hari, Tanggal</td>
                        <td style="border: none; padding: 1.5px 6px; width: 10px;">:</td>
                        <td style="border: none; padding: 1.5px 0;" class="font-medium">{{ $hariTanggalStr }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0;">Tempat</td>
                        <td style="border: none; padding: 1.5px 6px;">:</td>
                        <td style="border: none; padding: 1.5px 0;">{{ $tempat }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0;">Waktu</td>
                        <td style="border: none; padding: 1.5px 6px;">:</td>
                        <td style="border: none; padding: 1.5px 0;">{{ $waktu }}</td>
                    </tr>
                </table>
            </div>

            {{-- TABEL PENGANTAR --}}
            <div class="text-[9.5pt] mb-1">
                <p>Adapun nama-nama anggota yang bertugas sebagai berikut:</p>
            </div>

            {{-- TABEL ANGGOTA & PENUGASAN --}}
            <table class="table-tugas mb-3">
                <thead>
                    <tr>
                        <th style="width: 5.5%;" class="text-center py-1">NO</th>
                        <th style="width: 32%;" class="text-center py-1">NAMA</th>
                        <th style="width: 14.5%;" class="text-center py-1">JABATAN</th>
                        <th style="width: 48%;" class="text-center py-1">TUGAS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petugasList as $p)
                        <tr>
                            <td class="text-center">{{ $p['no'] }}</td>
                            <td class="font-medium text-slate-900">{{ $p['nama'] }}</td>
                            <td class="text-center">{{ $p['jabatan'] }}</td>
                            <td class="text-left" style="white-space: pre-line;">{!! nl2br(e($p['tugas'])) !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-2 text-slate-500">
                                Belum ada petugas yang terdaftar di kelompok ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PENUTUP --}}
            <div class="text-[9.5pt] leading-tight mb-3">
                <p>
                    Demikian surat perintah tugas ini dibuat dengan sebenar-benarnya untuk dijalankan dengan penuh tanggung jawab dan dapat dipergunakan sebagaimana mestinya.
                </p>
            </div>

            {{-- BLOK TANDA TANGAN (SEBELAH KANAN) --}}
            <div class="ttd-container flex justify-end mt-auto pt-1">
                <div class="text-[9.5pt] text-left" style="width: 240px;">
                    <div>Pringsewu, {{ $tanggalSurat }}</div>
                    <div class="font-semibold">Kepala SMK Telkom Lampung</div>

                    @if(!empty($qrCodeBase64) && $digitalDoc)
                        {{-- TANDA TANGAN DIGITAL OTOMATIS (TTE) & QR CODE VALIDATION --}}
                        <div class="my-1.5 p-1 border border-slate-300 rounded bg-slate-50 flex items-center gap-2">
                            <div class="flex-shrink-0">
                                <img src="{{ $qrCodeBase64 }}" alt="QR Verifikasi Dokumen" class="w-12 h-12 block">
                            </div>
                            <div class="text-[7pt] leading-tight text-slate-700">
                                <div class="font-bold text-slate-900 uppercase">Ditandatangani Digital</div>
                                <div class="text-[6.5pt] text-slate-500 mt-0.5">SMK Telkom Lampung</div>
                                <div class="text-[6pt] text-slate-400 font-mono mt-0.5 break-all">ID: {{ Str::limit($digitalDoc->token, 14) }}</div>
                                <div class="text-[6pt] text-emerald-700 font-semibold mt-0.5">Dokumen Resmi Sah</div>
                            </div>
                        </div>
                    @else
                        {{-- AREA TTD BASAH CONVENTIONAL --}}
                        <div class="h-12"></div>
                    @endif

                    {{-- NAMA KEPALA SEKOLAH (HANYA KAPITAL AWAL SAJA) --}}
                    <div class="font-bold underline text-slate-900 mt-0.5">
                        {{ $namaKepsek }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
