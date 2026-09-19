<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pelajar - Kelas {{ $rombel->kelas->nama_kelas ?? 'Rombel' }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
            .card-container {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6">

    {{-- Top Action Bar --}}
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-gray-900">Cetak Kartu Pelajar - Kelas {{ $rombel->kelas->nama_kelas ?? '-' }}</h1>
            <p class="text-xs text-gray-500">Total: {{ $siswaList->count() }} siswa | Siap cetak pada lembar A4 (Grid 2 kolom)</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Semua (A4)
            </button>
            <a href="{{ route('operator.kartu-pelajar.export-zip', ['rombel_id' => $rombel->id]) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export ZIP (.jpg)
            </a>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition text-sm">
                Tutup
            </button>
        </div>
    </div>

    {{-- Grid Kartu (Chunked per 8 kartu per halaman A4) --}}
    @php
        $chunks = $siswaList->chunk(8);
    @endphp

    <div class="max-w-[190mm] mx-auto space-y-8 print:space-y-0">
        @foreach ($chunks as $chunkIndex => $chunk)
            <div class="bg-white p-4 print:p-0 rounded-2xl shadow-sm print:shadow-none border print:border-none border-gray-200 {{ !$loop->last ? 'page-break' : '' }}">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4 justify-items-center">
                    @foreach ($chunk as $siswa)
                        <div class="card-container relative border border-dashed border-gray-300 print:border-gray-400 p-0.5 rounded-xl">
                            @include('pages.operator.kartu-pelajar.card-template', [
                                'siswa' => $siswa,
                                'barcodeBase64' => $barcodes[$siswa->id] ?? null,
                                'kepsekData' => $kepsekData,
                                'namaKelas' => $rombel->kelas->nama_kelas ?? '-',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
