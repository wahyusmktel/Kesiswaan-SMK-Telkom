<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pemilih Berbarcode - Pilketos {{ $periode?->tahun_ajaran }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-after: always;
            }
            .card-item {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-6">

    <!-- Top Toolbar (Hidden on Print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-bold text-lg text-gray-800">Cetak Kartu Pemilih Berbarcode 1D</h1>
            <p class="text-xs text-gray-500">Total: {{ $voters->count() }} Kartu Pemilih siap cetak untuk scanner fisik bilik suara</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Semua Kartu (Ctrl + P)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($voters as $v)
            <div class="card-item bg-white border-2 border-dashed border-gray-300 rounded-2xl p-5 flex flex-col justify-between shadow-sm relative overflow-hidden">
                <!-- Watermark / Accent -->
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-rose-50 rounded-full opacity-50 pointer-events-none"></div>

                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 text-white font-black text-xs flex items-center justify-center shadow-sm">
                                OSIS
                            </div>
                            <div>
                                <div class="text-[11px] font-black tracking-tight text-gray-900 uppercase">SMK TELKOM LAMPUNG</div>
                                <div class="text-[9px] text-rose-600 font-bold tracking-wider uppercase">KPU PEMILIHAN KETUA OSIS</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-[9px] font-mono font-bold text-gray-600 uppercase">
                                {{ $periode?->tahun_ajaran ?? '2026/2027' }}
                            </span>
                        </div>
                    </div>

                    <!-- Identitas Pemilih -->
                    <div class="space-y-1 mb-4">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Nama Pemilih:</div>
                        <div class="text-sm font-black text-gray-800 leading-tight line-clamp-1">{{ $v->nama_pemilih }}</div>
                        <div class="flex items-center gap-3 text-[11px] font-mono text-gray-600 mt-1">
                            <div>NIS: <strong class="text-gray-800">{{ $v->nis }}</strong></div>
                            <div>&bull;</div>
                            <div>Kelas: <strong class="text-rose-600">{{ $v->kelas }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- 1D Barcode Container -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center space-y-1">
                    <div class="flex justify-center items-center overflow-hidden py-1">
                        <div class="w-full max-w-[280px]">
                            {!! $v->barcode_svg !!}
                        </div>
                    </div>
                    <div class="font-mono text-xs font-black tracking-widest text-gray-800">
                        {{ $v->barcode_token }}
                    </div>
                    <div class="text-[8px] text-gray-400 uppercase tracking-wider">
                        Tunjukkan barcode ini ke panitia penjaga bilik suara
                    </div>
                </div>

                <!-- Footer Peraturan -->
                <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between text-[9px] text-gray-400">
                    <span class="italic">*Hanya berlaku 1x pemilihan (Anti Kecurangan)</span>
                    <span class="font-mono uppercase font-bold text-gray-500">DPT RESMI</span>
                </div>
            </div>
        @empty
            <div class="col-span-2 text-center py-12 bg-white rounded-3xl border border-gray-200 text-gray-400">
                Belum ada data pemilih yang sesuai kriteria filter.
            </div>
        @endforelse
    </div>

</body>
</html>
