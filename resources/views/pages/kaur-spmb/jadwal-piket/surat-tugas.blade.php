<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Tugas Piket SPMB - {{ $hariTanggalStr }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 0; /* Zero margin for full edge-to-edge Kop Surat */
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .kop-banner {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .surat-body {
                padding-left: 20mm !important;
                padding-right: 20mm !important;
                padding-top: 4mm !important;
                padding-bottom: 15mm !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        body {
            font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            color: #111827;
            background-color: #f1f5f9;
        }

        /* Bordered table styling */
        table.table-tugas {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-tugas th, table.table-tugas td {
            border: 1.5px solid #000000;
            padding: 4px 6px;
            font-size: 9.5pt;
            line-height: 1.25;
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
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white border border-slate-300 rounded-lg p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('kaur-spmb.jadwal-piket.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
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
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-md transition-colors"
                        title="Verifikasi Keaslian Dokumen Digital">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Tervalidasi Digital (TTE)
                    </a>
                @endif

                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Surat Tugas (Print / PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- DOCUMENT SHEET (A4) --}}
    <div class="page-container max-w-4xl mx-auto bg-white border border-slate-300 print:border-none shadow-md overflow-hidden">

        {{-- 1. KOP SURAT (FULL EDGE-TO-EDGE, TANPA MARGIN SAAT DICETAK) --}}
        <div class="kop-banner w-full">
            @if(!empty($kopBase64))
                <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block" style="width: 100%; display: block; margin: 0; padding: 0;">
            @else
                {{-- Fallback HTML / CSS Kop Surat Telkom Schools SMK Telkom Lampung --}}
                <div class="relative w-full pt-4 pb-2 px-8 border-b-2 border-red-600 bg-white">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Logo Telkom Schools --}}
                        <div class="flex-shrink-0 flex items-center gap-2">
                            <div class="w-14 h-14 bg-red-600 rounded flex flex-col items-center justify-center text-white font-bold tracking-tight text-center shadow-sm">
                                <span class="text-xs uppercase leading-none font-extrabold">Telkom</span>
                                <span class="text-[9px] uppercase leading-none font-semibold mt-0.5">Schools</span>
                            </div>
                        </div>

                        {{-- Teks Lembaga & Sekolah --}}
                        <div class="flex-1 text-center">
                            <div class="text-[10pt] font-semibold tracking-wider text-slate-800 uppercase">YAYASAN PENDIDIKAN TELKOM</div>
                            <div class="text-[15pt] font-extrabold text-slate-900 tracking-wide uppercase leading-tight">SMK Telkom Lampung</div>
                            <div class="text-[8pt] text-slate-700 font-semibold mt-0.5">
                                Kompetensi Keahlian : (1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi
                            </div>
                            <div class="text-[7.5pt] text-slate-600 mt-0.5">
                                Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372 - Telp/Faks : 0721 5621 547 • NPSN : 69944770
                            </div>
                            <div class="text-[7.5pt] text-red-600 mt-0.5 font-medium">
                                w: www.smktelkom-lpg.sch.id &nbsp; e: smktelkom9914@gmail.com / telkomschool@smktelkom-lpg.sch.id
                            </div>
                        </div>

                        {{-- Red wave accent placeholder --}}
                        <div class="w-16 flex-shrink-0 flex justify-end">
                            <div class="w-12 h-10 border-t-4 border-r-4 border-red-600 rounded-tr-xl opacity-80"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 2. SURAT BODY --}}
        <div class="surat-body px-8 sm:px-12 pt-4 pb-12 print:px-0">

            {{-- JUDUL & NOMOR SURAT --}}
            <div class="text-center mb-5">
                <h1 class="text-[12pt] font-bold tracking-wide uppercase underline decoration-1 underline-offset-2">
                    SURAT PERINTAH TUGAS
                </h1>
                <p class="text-[10pt] text-black mt-0.5">
                    Nomor: {{ $nomorSurat }}
                </p>
            </div>

            {{-- PEMBUKA --}}
            <div class="text-[10pt] leading-relaxed mb-3">
                <p>Yang bertanda tangan di bawah ini Kepala SMK Telkom Lampung:</p>
                <table class="mt-1 text-[10pt] leading-tight" style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0; width: 100px;">Nama</td>
                        <td style="border: none; padding: 1.5px 6px; width: 12px;">:</td>
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
            <div class="text-[10pt] leading-relaxed mb-3">
                <p>
                    Dengan ini menugaskan kepada nama-nama di bawah ini untuk melaksanakan Tugas <strong>Piket SPMB (Seleksi Penerimaan Murid Baru)</strong> Tahun Pelajaran <strong>{{ $tahunAjaran }}</strong> SMK Telkom Lampung pada:
                </p>
                <table class="mt-1 text-[10pt] leading-tight" style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none; padding: 1.5px 0; width: 100px;">Hari, Tanggal</td>
                        <td style="border: none; padding: 1.5px 6px; width: 12px;">:</td>
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
            <div class="text-[10pt] mb-1.5">
                <p>Adapun nama-nama anggota yang bertugas sebagai berikut:</p>
            </div>

            {{-- TABEL ANGGOTA & PENUGASAN --}}
            <table class="table-tugas mb-4">
                <thead>
                    <tr>
                        <th style="width: 5.5%;" class="text-center py-1.5">NO</th>
                        <th style="width: 32%;" class="text-center py-1.5">NAMA</th>
                        <th style="width: 14.5%;" class="text-center py-1.5">JABATAN</th>
                        <th style="width: 48%;" class="text-center py-1.5">TUGAS</th>
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
                            <td colspan="4" class="text-center py-3 text-slate-500">
                                Belum ada petugas yang terdaftar di kelompok ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PENUTUP --}}
            <div class="text-[10pt] leading-relaxed mb-6">
                <p>
                    Demikian surat perintah tugas ini dibuat dengan sebenar-benarnya untuk dijalankan dengan penuh tanggung jawab dan dapat dipergunakan sebagaimana mestinya.
                </p>
            </div>

            {{-- BLOK TANDA TANGAN (SEBELAH KANAN) --}}
            <div class="flex justify-end pt-1">
                <div class="text-[10pt] text-left" style="width: 250px;">
                    <div>Pringsewu, {{ $tanggalSurat }}</div>
                    <div class="font-semibold">{{ $jabatanKepsek }} SMK Telkom Lampung</div>

                    @if(!empty($qrCodeBase64) && $digitalDoc)
                        {{-- TANDA TANGAN DIGITAL OTOMATIS (TTE) & QR CODE VALIDATION --}}
                        <div class="my-2 p-1.5 border border-slate-300 rounded bg-slate-50 flex items-center gap-2">
                            <div class="flex-shrink-0">
                                <img src="{{ $qrCodeBase64 }}" alt="QR Verifikasi Dokumen" class="w-16 h-16 block">
                            </div>
                            <div class="text-[7.5pt] leading-tight text-slate-700">
                                <div class="font-bold text-slate-900 uppercase">Ditandatangani Digital</div>
                                <div class="text-[7pt] text-slate-500 mt-0.5">SMK Telkom Lampung</div>
                                <div class="text-[6.5pt] text-slate-400 font-mono mt-0.5 break-all">ID: {{ Str::limit($digitalDoc->token, 16) }}</div>
                                <div class="text-[6.5pt] text-emerald-700 font-semibold mt-0.5">Dokumen Resmi Sah</div>
                            </div>
                        </div>
                    @else
                        {{-- AREA TTD BASAH CONVENTIONAL --}}
                        <div class="h-16"></div>
                    @endif

                    <div class="font-bold underline uppercase text-slate-900">
                        {{ $namaKepsek }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
