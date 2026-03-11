<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Laporan Bulanan Absensi</h2>
    </x-slot>

    @push('styles')
    <style>
        .aurora-bg { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); position:relative; overflow:hidden; }
        .aurora-bg::before { content:''; position:absolute; width:500px;height:500px; background:radial-gradient(circle,rgba(100,40,180,0.3) 0%,transparent 70%); top:-100px;left:-80px; animation:a1 9s ease-in-out infinite alternate; pointer-events:none; }
        @keyframes a1 { 0%{transform:translate(0,0);}100%{transform:translate(60px,40px);} }
        .glass-card { background:rgba(255,255,255,0.07); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.12); border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .select-input { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.2); border-radius:12px; color:white; padding:0.6rem 1rem; -webkit-appearance:none; }
        .select-input option { background:#1e1b4b; color:white; }
        .chart-canvas { max-height: 300px; }
    </style>
    @endpush

    <div class="aurora-bg min-h-screen -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6 relative z-10">

            {{-- Header + Filter --}}
            <div class="glass-card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-700 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-white font-black text-xl">Laporan Bulanan Absensi</h1>
                        <p class="text-white/50 text-sm">Total absensi bulan ini: <span class="text-white font-bold">{{ $totalBulanIni }}</span> entri</p>
                    </div>
                </div>
                <form method="GET" class="flex items-center gap-2 flex-wrap">
                    <select name="bulan" class="select-input" onchange="this.form.submit()">
                        @foreach($bulanList as $bl)
                            <option value="{{ $bl['value'] }}" {{ $bulan == $bl['value'] ? 'selected' : '' }}>{{ $bl['label'] }}</option>
                        @endforeach
                    </select>
                    <select name="tahun" class="select-input" onchange="this.form.submit()">
                        @foreach(range(date('Y'), date('Y')-3) as $yr)
                            <option value="{{ $yr }}" {{ $tahun == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('sdm.absensi.export', ['bulan'=>$bulan,'tahun'=>$tahun]) }}"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </a>
                </form>
            </div>

            {{-- Chart --}}
            <div class="glass-card p-6">
                <h3 class="text-white font-bold text-base mb-5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Grafik Absensi Per Hari
                </h3>
                <canvas id="monthlyChart" class="chart-canvas"></canvas>
            </div>

            {{-- Table per day --}}
            <div class="glass-card p-6">
                <h3 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Data Harian
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-3 px-2">Tanggal</th>
                                <th class="text-center text-white/40 text-[9px] font-black uppercase tracking-widest pb-3 px-2">Total Hadir</th>
                                <th class="text-center text-white/40 text-[9px] font-black uppercase tracking-widest pb-3 px-2">Tepat Waktu</th>
                                <th class="text-center text-white/40 text-[9px] font-black uppercase tracking-widest pb-3 px-2">Terlambat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $dateKey = \Carbon\Carbon::createFromDate($tahun, $bulan, $d)->format('Y-m-d');
                                    $dayData = $perHari->get($dateKey);
                                    $dayLabel = \Carbon\Carbon::createFromDate($tahun, $bulan, $d)->translatedFormat('l, d M Y');
                                @endphp
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                    <td class="py-2.5 px-2 text-white/80 text-xs">{{ $dayLabel }}</td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="text-white font-bold">{{ $dayData ? $dayData->hadir : 0 }}</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300">{{ $dayData ? $dayData->tepat_waktu : 0 }}</span>
                                    </td>
                                    <td class="py-2.5 px-2 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300">{{ $dayData ? $dayData->terlambat : 0 }}</span>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Tepat Waktu',
                            data: @json($chartTepatWaktu),
                            backgroundColor: 'rgba(16,185,129,0.7)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'Terlambat',
                            data: @json($chartTerlambat),
                            backgroundColor: 'rgba(245,158,11,0.7)',
                            borderColor: '#f59e0b',
                            borderWidth: 1,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { labels: { color: 'rgba(255,255,255,0.7)', font: { weight: 'bold', size: 11 } } },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { stacked: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.5)', font:{size:10} } },
                        y: { stacked: true, beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.5)', font:{size:10}, stepSize: 1 } }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
