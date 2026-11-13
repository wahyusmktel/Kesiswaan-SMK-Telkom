<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Rekapitulasi Pilketos - {{ $periode?->tahun_ajaran }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                font-size: 11pt !important;
            }
            @page {
                size: A4;
                margin: 2cm 2cm 2cm 2cm;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-6">

    <!-- Action Bar -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
        <div>
            <h1 class="font-bold text-base text-gray-800">Berita Acara Rekapitulasi Hasil Pilketos</h1>
            <p class="text-xs text-gray-500">Dokumen resmi penetapan hasil pemungutan suara OSIS SMK Telkom Lampung</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Dokumen Resmi (Ctrl + P)</span>
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Paper Document -->
    <div class="max-w-4xl mx-auto bg-white p-10 sm:p-14 shadow-lg rounded-3xl border border-gray-200 space-y-6 text-sm">

        <!-- Kop Surat -->
        <div class="border-b-4 border-double border-gray-900 pb-4 text-center space-y-1 relative">
            <div class="text-xs font-bold uppercase tracking-widest text-gray-600">YAYASAN PENDIDIKAN TELKOM</div>
            <div class="text-xl sm:text-2xl font-black uppercase text-gray-900 tracking-tight">SMK TELKOM LAMPUNG</div>
            <div class="text-sm font-bold uppercase tracking-wider text-rose-600">KOMISI PEMILIHAN KETUA OSIS (KPU OSIS)</div>
            <div class="text-[11px] text-gray-500">
                Jl. Raya Gadingrejo, Kabupaten Pringsewu, Lampung | Website: smktelkom-lpg.sch.id
            </div>
        </div>

        <!-- Judul Berita Acara -->
        <div class="text-center pt-2 space-y-1">
            <h2 class="font-black text-base uppercase tracking-wider underline">BERITA ACARA REKAPITULASI HASIL PENGHITUNGAN SUARA</h2>
            <p class="text-xs font-mono text-gray-600">Nomor: {{ $periode?->id ? sprintf('024/KPU-OSIS/SMK-TL/%d', $periode->id) : '024/KPU-OSIS/SMK-TL/2026' }}</p>
        </div>

        <!-- Paragraf Pembuka -->
        <div class="text-justify leading-relaxed text-xs sm:text-sm">
            <p>
                Pada hari ini, <strong>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</strong>, telah dilaksanakan rekapitulasi akhir hasil pemungutan suara Pemilihan Umum Ketua dan Wakil Ketua Organisasi Siswa Intra Sekolah (OSIS) SMK Telkom Lampung untuk Masa Bakti <strong>{{ $periode?->tahun_ajaran ?? '2026/2027' }}</strong> dengan metode <em>Electronic Voting</em> (E-Voting) berbasis verifikasi fisik 1D Barcode Scanner.
            </p>
        </div>

        <!-- Data Statistik Pemilih -->
        <div class="space-y-2">
            <h3 class="font-bold text-xs uppercase tracking-wider text-gray-800">I. DATA PEMILIH DAN PARTISIPASI</h3>
            <table class="w-full text-xs border border-gray-300">
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 w-8 font-mono">1.</td>
                        <td class="px-4 py-2">Jumlah Pemilih Terdaftar (DPT)</td>
                        <td class="px-4 py-2 font-bold font-mono text-right">{{ number_format($totalDpt) }} Orang</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 font-mono">2.</td>
                        <td class="px-4 py-2">Jumlah Surat Suara Masuk (Suara Sah)</td>
                        <td class="px-4 py-2 font-bold font-mono text-right text-rose-600">{{ number_format($totalVotes) }} Suara</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="px-4 py-2 font-mono">3.</td>
                        <td class="px-4 py-2">Jumlah Pemilih yang Tidak Menggunakan Hak Suara</td>
                        <td class="px-4 py-2 font-bold font-mono text-right">{{ number_format($totalDpt - $totalVotes) }} Orang</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-mono">4.</td>
                        <td class="px-4 py-2">Tingkat Partisipasi Pemilih</td>
                        <td class="px-4 py-2 font-bold font-mono text-right text-indigo-600">{{ $turnout }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Hasil Perolehan Suara Kandidat -->
        <div class="space-y-2">
            <h3 class="font-bold text-xs uppercase tracking-wider text-gray-800">II. HASIL PEROLEHAN SUARA PASANGAN CALON</h3>
            <table class="w-full text-xs border border-gray-300 text-left">
                <thead class="bg-gray-100 border-b border-gray-300 font-bold uppercase">
                    <tr>
                        <th class="px-3 py-2 text-center w-12">No.</th>
                        <th class="px-3 py-2 text-center w-20">No. Urut</th>
                        <th class="px-3 py-2">Nama Pasangan Calon</th>
                        <th class="px-3 py-2 text-right">Perolehan Suara</th>
                        <th class="px-3 py-2 text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kandidats as $idx => $k)
                        <tr class="{{ $winner && $winner->id === $k->id && $winner->votes_count > 0 ? 'bg-rose-50 font-bold' : '' }}">
                            <td class="px-3 py-2.5 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="px-3 py-2.5 text-center font-bold font-mono text-rose-600">0{{ $k->nomor_urut }}</td>
                            <td class="px-3 py-2.5">
                                <div class="font-bold">{{ $k->nama_ketua }}</div>
                                @if($k->nama_wakil)
                                    <div class="text-[11px] text-gray-600">&amp; {{ $k->nama_wakil }}</div>
                                @endif
                                <div class="text-[10px] text-gray-400 font-mono">{{ $k->kelas_ketua }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold">{{ number_format($k->votes_count) }}</td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold">{{ $totalVotes > 0 ? round(($k->votes_count / $totalVotes) * 100, 1) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-400">Tidak ada data pasangan calon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Penetapan Hasil -->
        @if($winner && $winner->votes_count > 0)
            <div class="p-4 rounded-2xl border-2 border-rose-600 bg-rose-50/40 space-y-2">
                <h3 class="font-black text-xs uppercase tracking-wider text-rose-700">III. PENETAPAN PASANGAN CALON TERPILIH</h3>
                <p class="text-xs leading-relaxed">
                    Berdasarkan perolehan suara terbanyak, Komisi Pemilihan OSIS SMK Telkom Lampung secara resmi menetapkan Pasangan Calon Nomor Urut <strong>0{{ $winner->nomor_urut }}</strong>:
                </p>
                <div class="text-sm font-black text-gray-900 pl-4 border-l-4 border-rose-600">
                    <div>Ketua OSIS Terpilih: {{ $winner->nama_ketua }} ({{ $winner->kelas_ketua }})</div>
                    @if($winner->nama_wakil)
                        <div class="text-xs text-gray-700 mt-0.5">Wakil Ketua OSIS Terpilih: {{ $winner->nama_wakil }} ({{ $winner->kelas_wakil }})</div>
                    @endif
                    <div class="text-xs font-normal text-gray-600 mt-1">
                        Dengan perolehan <strong>{{ number_format($winner->votes_count) }} suara</strong> ({{ $totalVotes > 0 ? round(($winner->votes_count / $totalVotes) * 100, 1) : 0 }}%) sebagai Ketua &amp; Wakil Ketua OSIS Periode {{ $periode?->tahun_ajaran }}.
                    </div>
                </div>
            </div>
        @endif

        <div class="text-xs leading-relaxed pt-2">
            Demikian Berita Acara ini dibuat dengan sebenarnya dan penuh rasa tanggung jawab untuk dipergunakan sebagaimana mestinya.
        </div>

        <!-- Lembar Tanda Tangan -->
        <div class="pt-8 grid grid-cols-3 gap-6 text-center text-xs">
            <div class="space-y-16">
                <div>
                    <div>Mengetahui,</div>
                    <div class="font-bold">Ketua KPU OSIS</div>
                </div>
                <div>
                    <div class="font-bold underline">Panitia Pemilihan</div>
                    <div class="text-[10px] text-gray-500">NIS. -</div>
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <div>Menyetujui,</div>
                    <div class="font-bold">Pembina OSIS</div>
                </div>
                <div>
                    <div class="font-bold underline">Pembina Kesiswaan</div>
                    <div class="text-[10px] text-gray-500">NIP/NIK. -</div>
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <div>Mengesahkan,</div>
                    <div class="font-bold">Kepala SMK Telkom Lampung</div>
                </div>
                <div>
                    <div class="font-bold underline">Kepala Sekolah</div>
                    <div class="text-[10px] text-gray-500">NIP/NIK. -</div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
