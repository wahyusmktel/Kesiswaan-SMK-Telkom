<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pelajar - {{ $siswa->nama_lengkap }} ({{ $siswa->nis }})</title>
    @vite(['resources/css/app.css'])
    <style>
        @page {
            size: 86mm 54mm;
            margin: 0;
        }
        @media print {
            html, body {
                width: 86mm;
                height: 54mm;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card-wrapper {
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-4">

    <div class="no-print mb-4 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Sekarang
        </button>
        <a href="{{ route('operator.kartu-pelajar.download-jpg', $siswa->id) }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download JPG
        </a>
        <button onclick="window.close()" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm hover:bg-gray-50 transition text-sm">
            Tutup
        </button>
    </div>

    <div class="print-card-wrapper shadow-2xl rounded-xl">
        @include('pages.operator.kartu-pelajar.card-template', [
            'siswa' => $siswa,
            'barcodeBase64' => $barcodeBase64,
            'kepsekData' => $kepsekData,
            'namaKelas' => $siswa->rombels->first()?->kelas?->nama_kelas ?? '-',
        ])
    </div>

</body>
</html>
