<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta['judul'] }} - {{ $meta['tahun_ajaran'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 11pt;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }
            .page-break {
                page-break-before: always;
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
            font-family: 'Arial', sans-serif;
            color: #111827;
        }
        table.table-bordered {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-bordered th, table.table-bordered td {
            border: 1.5px solid #000000;
            padding: 4px 6px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 print:py-0">

    {{-- SCREEN TOOLBAR --}}
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white border border-slate-300 rounded-lg p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('kaur-spmb.jadwal-piket.index', ['tahun_ajaran' => $tahunAjaran]) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-md border border-slate-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Halaman Jadwal
                </a>
                <span class="text-xs text-slate-500 font-medium">Tahun Ajaran: <strong class="text-slate-800">{{ $tahunAjaran }}</strong></span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Dokumen (Print / PDF)
                </button>
            </div>
        </div>
    </div>

    {{-- DOCUMENT SHEET (A4) --}}
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-10 border border-slate-300 print:border-none print:p-0 print:shadow-none shadow-md">
        
        {{-- DOCUMENT HEADER --}}
        <div class="text-center mb-6">
            <h1 class="text-base sm:text-lg font-bold tracking-wide uppercase leading-tight">
                {{ $meta['judul'] }}
            </h1>
            <h2 class="text-base sm:text-lg font-bold tracking-wide uppercase leading-tight mt-1">
                TAHUN AJARAN {{ $meta['tahun_ajaran'] }}
            </h2>
        </div>

        {{-- MAIN MATRIX TABLE --}}
        <table class="table-bordered text-[10.5pt] leading-tight">
            <thead>
                <tr class="bg-gray-100 print:bg-transparent font-bold text-center">
                    <th class="w-10 text-center py-2">NO</th>
                    <th class="text-center py-2">NAMA</th>
                    <th class="w-44 text-center py-2">HARI/ TANGGAL</th>
                    <th class="w-32 text-center py-2">WAKTU</th>
                    <th class="w-32 text-center py-2">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $globalIndex = 1;
                @endphp

                @forelse($kelompoks as $kelompok)
                    @php
                        $petugasCount = $kelompok->petugas->count();
                        $tanggals = $kelompok->tanggals;
                        $tanggalCount = $tanggals->count();
                        $rowCount = max($petugasCount, $tanggalCount + 1, 1);
                    @endphp

                    @for($i = 0; $i < $rowCount; $i++)
                        <tr>
                            {{-- NO --}}
                            <td class="text-center align-middle font-medium py-1">
                                @if($i < $petugasCount)
                                    {{ $globalIndex++ }}
                                @endif
                            </td>

                            {{-- NAMA PETUGAS --}}
                            <td class="align-middle px-2 py-1">
                                @if($i < $petugasCount)
                                    <span class="font-normal">{{ $kelompok->petugas[$i]->nama_petugas }}</span>
                                @endif
                            </td>

                            {{-- HARI / TANGGAL --}}
                            <td class="text-center align-middle font-medium px-1 py-1">
                                @if($i === 0)
                                    <div class="font-bold uppercase tracking-wider text-[10pt] py-0.5">SABTU</div>
                                @elseif(($i - 1) < $tanggalCount)
                                    @php
                                        $tgl = $tanggals[$i - 1];
                                    @endphp
                                    <div class="text-[10pt] py-0.5 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('j M Y') }}
                                    </div>
                                @endif
                            </td>

                            {{-- WAKTU (Rowspan for whole group) --}}
                            @if($i === 0)
                                <td class="text-center align-middle font-bold text-[10.5pt]" rowspan="{{ $rowCount }}">
                                    {{ $kelompok->waktu ?: '07.30 - 12.00' }}
                                </td>
                                <td class="text-center align-middle text-[10pt]" rowspan="{{ $rowCount }}">
                                    {{ $kelompok->keterangan }}
                                </td>
                            @endif
                        </tr>
                    @endfor
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-slate-500">
                            Belum ada kelompok piket untuk tahun ajaran {{ $tahunAjaran }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- NOTED --}}
        <div class="mt-4 text-[10.5pt]">
            <span class="font-bold">Noted :</span>
            <span class="font-bold">{{ $meta['catatan'] }}</span>
        </div>

        {{-- SIGNATURE BLOCK --}}
        <div class="mt-8 flex justify-end">
            <div class="w-72 text-center text-[10.5pt] leading-snug">
                <div>{{ $meta['tempat_ttd'] }}, {{ $meta['tanggal_ttd'] }}</div>
                <div class="font-medium mt-0.5">{{ $meta['jabatan_ttd'] }}</div>
                
                {{-- Space for Signature & Stamp --}}
                <div class="h-24 my-1 flex items-center justify-center">
                    {{-- Clean space for real physical signature & stamp --}}
                </div>

                <div class="font-bold underline tracking-wide">{{ $meta['nama_kepsek'] }}</div>
                <div>NIP. {{ $meta['nip_kepsek'] }}</div>
            </div>
        </div>

    </div>

</body>
</html>
