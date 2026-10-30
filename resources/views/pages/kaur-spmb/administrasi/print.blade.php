<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Piket SPMB - {{ $hariTanggalStr }}</title>
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

        /* Lembar Kertas A4 Nyata */
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
                padding-left: 18mm !important;
                padding-right: 18mm !important;
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
                padding: 2.5px 5.5px !important;
                font-size: 8.5pt !important;
                line-height: 1.2 !important;
            }
        }

        /* Bordered table styling */
        table.table-doc {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-doc th, table.table-doc td {
            border: 1.2px solid #000000;
            padding: 3px 6px;
            font-size: 8.5pt;
            line-height: 1.25;
            vertical-align: middle;
        }
        table.table-doc th {
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
                <a href="{{ route('kaur-spmb.administrasi.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Administrasi SPMB
                </a>
                <span class="text-xs text-slate-500 font-medium">
                    Dokumen: <strong class="text-slate-800">{{ $beritaAcara->nomor_surat }}</strong>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Dokumen (Print / PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR 1: BERITA ACARA PELAKSANAAN PIKET SPMB            --}}
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

        {{-- ISI BERITA ACARA --}}
        <div class="doc-body flex-1 flex flex-col px-8 sm:px-12 pt-3 pb-8 print:px-0">

            {{-- JUDUL & NOMOR --}}
            <div class="text-center mb-3">
                <h1 class="text-[12pt] font-bold tracking-wide uppercase underline decoration-1 underline-offset-2">
                    {{ strtoupper($beritaAcara->judul) }}
                </h1>
                <p class="text-[9.5pt] text-black mt-0.5 font-mono">
                    Nomor: {{ $beritaAcara->nomor_surat }}
                </p>
            </div>

            {{-- NARASI PEMBUKA --}}
            <div class="text-[9.5pt] leading-relaxed mb-3 text-justify">
                <p>
                    Pada hari ini, <strong>{{ $hariTanggalStr }}</strong>, bertempat di <strong>{{ $beritaAcara->tempat }}</strong>, telah dilaksanakan kegiatan Pelayanan dan Piket SPMB (Seleksi Penerimaan Murid Baru) Tahun Pelajaran <strong>{{ $tahunAjaran }}</strong> SMK Telkom Lampung dengan rincian pelaksanaan sebagai berikut:
                </p>
            </div>

            {{-- TABEL IDENTITAS & HASIL KEGIATAN --}}
            <table class="table-doc mb-3">
                <tbody>
                    <tr>
                        <td style="width: 28%; font-weight: bold; background-color: #f8fafc;">Hari, Tanggal Pelaksanaan</td>
                        <td style="width: 72%;">{{ $hariTanggalStr }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: #f8fafc;">Waktu Pelaksanaan</td>
                        <td>{{ $beritaAcara->waktu_mulai }} sd {{ $beritaAcara->waktu_selesai }} WIB</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: #f8fafc;">Tempat Pelaksanaan</td>
                        <td>{{ $beritaAcara->tempat }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: #f8fafc;">Kelompok Piket Bertugas</td>
                        <td class="font-semibold">{{ $kelompok?->nama_kelompok ?? 'Kelompok Piket SPMB' }} (Total: {{ $totalPetugas }} Petugas)</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: #f8fafc;">Tingkat Kehadiran Petugas</td>
                        <td>
                            <strong>{{ $totalHadir }}</strong> Hadir &nbsp;|&nbsp; 
                            <strong>{{ $totalIzinSakit }}</strong> Izin/Sakit &nbsp;|&nbsp; 
                            <strong>{{ $totalAlpa }}</strong> Belum Hadir/Alpa
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; background-color: #f8fafc;">Statistik Peserta Terlayani</td>
                        <td>
                            Calon Siswa Hadir: <strong>{{ $beritaAcara->jumlah_casis_hadir }} Orang</strong> &nbsp;|&nbsp; 
                            Tamu / Wali Hadir: <strong>{{ $beritaAcara->jumlah_tamu }} Orang</strong>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- URAIAN RINGKASAN KEGIATAN --}}
            <div class="text-[9.5pt] mb-3">
                <div class="font-bold text-slate-900 mb-1">A. Uraian & Ringkasan Jalannya Kegiatan:</div>
                <div class="p-3 bg-slate-50 border border-black/80 rounded text-justify leading-relaxed whitespace-pre-line text-[9pt]">
                    {!! nl2br(e($beritaAcara->ringkasan_kegiatan)) !!}
                </div>
            </div>

            {{-- CATATAN KENDALA (JIKA ADA) --}}
            @if(!empty($beritaAcara->catatan_kendala))
                <div class="text-[9.5pt] mb-3">
                    <div class="font-bold text-slate-900 mb-0.5">B. Catatan Kendala / Hambatan:</div>
                    <div class="p-2.5 bg-slate-50 border border-black/80 rounded text-justify leading-relaxed text-[9pt]">
                        {!! nl2br(e($beritaAcara->catatan_kendala)) !!}
                    </div>
                </div>
            @endif

            {{-- KESIMPULAN / EVALUASI --}}
            @if(!empty($beritaAcara->kesimpulan))
                <div class="text-[9.5pt] mb-3">
                    <div class="font-bold text-slate-900 mb-0.5">C. Kesimpulan & Tindak Lanjut:</div>
                    <div class="p-2.5 bg-slate-50 border border-black/80 rounded text-justify leading-relaxed text-[9pt]">
                        {!! nl2br(e($beritaAcara->kesimpulan)) !!}
                    </div>
                </div>
            @endif

            {{-- PENUTUP --}}
            <div class="text-[9.5pt] leading-relaxed mb-4 text-justify">
                <p>
                    Demikian Berita Acara Pelaksanaan Piket SPMB ini dibuat dengan sebenar-benarnya berdasarkan keadaan yang sesungguhnya di lapangan, dan disertai lampiran daftar kehadiran petugas piket terlampir.
                </p>
            </div>

            {{-- TANDA TANGAN PENUTUP KAUR SPMB --}}
            <div class="flex justify-end mt-auto pt-1">
                <div class="text-[9.5pt] text-left" style="width: 250px;">
                    <div>{{ $beritaAcara->tempat_ttd }}, {{ $beritaAcara->tanggal_ttd ?: $hariTanggalStr }}</div>
                    <div class="font-semibold">{{ $beritaAcara->jabatan_penandatangan }}</div>
                    
                    {{-- Ruang Tanda Tangan --}}
                    <div class="h-16"></div>

                    <div class="font-bold underline text-slate-900">
                        {{ $beritaAcara->nama_penandatangan }}
                    </div>
                    <div class="text-[9pt] text-slate-700">
                        {{ $beritaAcara->nip_penandatangan ? 'NIP. ' . $beritaAcara->nip_penandatangan : '' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR 2: LAMPIRAN REKAPITULASI KEHADIRAN PETUGAS PIKET  --}}
    {{-- ======================================================== --}}
    <div class="page-sheet">

        {{-- KOP SURAT (FULL EDGE-TO-EDGE) --}}
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
                        </div>
                        <div class="w-12 flex-shrink-0"></div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ISI LAMPIRAN --}}
        <div class="doc-body flex-1 flex flex-col px-8 sm:px-12 pt-3 pb-8 print:px-0">

            {{-- HEADER LAMPIRAN --}}
            <div class="text-center mb-3">
                <p class="text-[8.5pt] font-semibold text-slate-500 uppercase tracking-wider">
                    LAMPIRAN BERITA ACARA NOMOR: {{ $beritaAcara->nomor_surat }}
                </p>
                <h2 class="text-[11.5pt] font-bold tracking-wide uppercase underline decoration-1 underline-offset-2 mt-0.5">
                    DAFTAR HADIR PETUGAS PIKET SPMB
                </h2>
                <p class="text-[9.5pt] text-black mt-0.5">
                    Hari/Tanggal: <strong>{{ $hariTanggalStr }}</strong> &nbsp;|&nbsp; 
                    Pukul: <strong>{{ $beritaAcara->waktu_mulai }} - {{ $beritaAcara->waktu_selesai }} WIB</strong> &nbsp;|&nbsp; 
                    {{ $kelompok?->nama_kelompok ?? 'Kelompok Piket' }}
                </p>
            </div>

            {{-- RINGKASAN KEHADIRAN --}}
            <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-black/80 rounded mb-3 text-[9pt]">
                <div>
                    Total Anggota Terjadwal: <strong>{{ $totalPetugas }} Orang</strong>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-emerald-700 font-semibold">Hadir: {{ $totalHadir }}</span>
                    <span class="text-blue-700 font-semibold">Izin/Sakit: {{ $totalIzinSakit }}</span>
                    <span class="text-rose-700 font-semibold">Alpa: {{ $totalAlpa }}</span>
                </div>
            </div>

            {{-- TABEL ABSENSI PETUGAS --}}
            <table class="table-doc mb-4">
                <thead>
                    <tr>
                        <th style="width: 5%;" class="text-center">NO</th>
                        <th style="width: 28%;">NAMA PETUGAS</th>
                        <th style="width: 12%;" class="text-center">JABATAN</th>
                        <th style="width: 25%;">TUGAS PIKET</th>
                        <th style="width: 12%;" class="text-center">STATUS</th>
                        <th style="width: 18%;">WAKTU & KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($petugasAbsensiList as $p)
                        <tr>
                            <td class="text-center font-medium">{{ $p['no'] }}</td>
                            <td class="font-medium text-slate-900">{{ $p['nama'] }}</td>
                            <td class="text-center">{{ $p['jabatan'] }}</td>
                            <td class="text-left" style="white-space: pre-line;">{!! nl2br(e($p['tugas'])) !!}</td>
                            <td class="text-center">
                                @if(in_array($p['status'], ['Hadir', 'Terlambat']))
                                    <span class="font-bold text-emerald-800">{{ $p['status'] }}</span>
                                @elseif(in_array($p['status'], ['Izin', 'Sakit']))
                                    <span class="font-bold text-blue-800">{{ $p['status'] }}</span>
                                @else
                                    <span class="font-bold text-rose-800">{{ $p['status'] }}</span>
                                @endif
                            </td>
                            <td class="text-left text-[8pt]">
                                <div>{{ $p['waktu_absen'] }}</div>
                                @if($p['keterangan'] !== '-')
                                    <div class="text-slate-500 italic mt-0.5">{{ $p['keterangan'] }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-slate-500">
                                Belum ada data petugas pada kelompok ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- TANDA TANGAN PENGESAHAN LAMPIRAN --}}
            <div class="flex justify-end mt-auto pt-2">
                <div class="text-[9.5pt] text-left" style="width: 250px;">
                    <div>Pringsewu, {{ $beritaAcara->tanggal_ttd ?: $hariTanggalStr }}</div>
                    <div class="font-semibold">{{ $beritaAcara->jabatan_penandatangan }}</div>
                    
                    {{-- Ruang Tanda Tangan --}}
                    <div class="h-16"></div>

                    <div class="font-bold underline text-slate-900">
                        {{ $beritaAcara->nama_penandatangan }}
                    </div>
                    <div class="text-[9pt] text-slate-700">
                        {{ $beritaAcara->nip_penandatangan ? 'NIP. ' . $beritaAcara->nip_penandatangan : '' }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
