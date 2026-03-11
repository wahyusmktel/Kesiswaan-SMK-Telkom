<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Rekapitulasi Absensi Pegawai</h2>
    </x-slot>

    @push('styles')
    <style>
        .aurora-bg { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); position:relative; overflow:hidden; }
        .aurora-bg::before { content:''; position:absolute; width:500px;height:500px;background:radial-gradient(circle,rgba(120,40,200,0.3) 0%,transparent 70%);top:-100px;left:-80px;animation:a1 8s ease-in-out infinite alternate;pointer-events:none; }
        .aurora-bg::after { content:''; position:absolute; width:400px;height:400px;background:radial-gradient(circle,rgba(0,200,160,0.25) 0%,transparent 70%);bottom:-100px;right:-80px;animation:a2 10s ease-in-out infinite alternate;pointer-events:none; }
        @keyframes a1{0%{transform:translate(0,0)}100%{transform:translate(60px,50px)}}
        @keyframes a2{0%{transform:translate(0,0)}100%{transform:translate(-40px,-30px)}}
        .glass-card { background:rgba(255,255,255,0.07); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.12); border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .select-input { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.2); border-radius:12px; color:white; padding:0.6rem 1rem; }
        .select-input option { background:#1e1b4b; color:white; }
        .neu-stat { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:14px; padding:1rem; text-align:center; }
        .progress-bar { height:6px; border-radius:999px; background:rgba(255,255,255,0.1); overflow:hidden; }
        .progress-fill { height:100%; border-radius:999px; transition:width 0.6s ease; }
    </style>
    @endpush

    <div class="aurora-bg min-h-screen -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6 relative z-10">

            {{-- Header + Filter --}}
            <div class="glass-card p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-white font-black text-xl">Rekapitulasi Absensi</h1>
                        <p class="text-white/50 text-sm">Rekap per pegawai dengan grafik tren absensi bulanan</p>
                    </div>
                </div>
                <form method="GET" class="flex flex-wrap items-center gap-2">
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
                    <select name="user_id" class="select-input" onchange="this.form.submit()">
                        <option value="" {{ !$userId ? 'selected' : '' }}>— Semua Pegawai —</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Rekap Grid per pegawai --}}
            @if($rekap->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($rekap as $r)
                        @php
                            $persen = $r->total_hari > 0 ? round(($r->total_hadir / $r->total_hari) * 100) : 0;
                        @endphp
                        <div class="glass-card p-5 space-y-3 {{ $userId == $r->user_id ? 'ring-2 ring-violet-500/50' : '' }}">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($r->user->name ?? 'User') }}&background=6d28d9&color=fff&size=40"
                                    class="w-10 h-10 rounded-xl" alt="">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold text-sm truncate">{{ $r->user->name ?? 'Pegawai' }}</p>
                                    <p class="text-white/40 text-[10px] font-bold uppercase tracking-wider">{{ $r->total_hari }} hari tercatat</p>
                                </div>
                                <span class="text-lg font-black {{ $persen >= 80 ? 'text-emerald-400' : ($persen >= 60 ? 'text-amber-400' : 'text-red-400') }}">{{ $persen }}%</span>
                            </div>

                            <div class="progress-bar">
                                <div class="progress-fill {{ $persen >= 80 ? 'bg-gradient-to-r from-emerald-500 to-teal-400' : ($persen >= 60 ? 'bg-gradient-to-r from-amber-500 to-orange-400' : 'bg-gradient-to-r from-red-500 to-rose-400') }}"
                                    style="width: {{ $persen }}%"></div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="neu-stat">
                                    <p class="text-emerald-400 text-lg font-black">{{ $r->total_tepat }}</p>
                                    <p class="text-white/40 text-[9px] uppercase tracking-wider">Tepat</p>
                                </div>
                                <div class="neu-stat">
                                    <p class="text-amber-400 text-lg font-black">{{ $r->total_terlambat }}</p>
                                    <p class="text-white/40 text-[9px] uppercase tracking-wider">Terlambat</p>
                                </div>
                                <div class="neu-stat">
                                    <p class="text-cyan-400 text-lg font-black">{{ $r->total_checkout }}</p>
                                    <p class="text-white/40 text-[9px] uppercase tracking-wider">Checkout</p>
                                </div>
                            </div>

                            <a href="?bulan={{ $bulan }}&tahun={{ $tahun }}&user_id={{ $r->user_id }}"
                                class="block w-full text-center py-2 rounded-xl text-[11px] font-black text-violet-300 border border-violet-500/30 hover:bg-violet-500/20 transition-colors">
                                Lihat Detail + Grafik →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="glass-card p-10 text-center">
                    <svg class="w-14 h-14 text-white/20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-white/50 text-lg">Belum ada data absensi untuk periode ini.</p>
                </div>
            @endif

            {{-- Detail Chart for Selected User --}}
            @if($userId && $detailUser && $detailAbsensi->count() > 0)
                <div class="glass-card p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($detailUser->name) }}&background=7c3aed&color=fff&size=48" class="w-12 h-12 rounded-xl" alt="">
                        <div>
                            <h3 class="text-white font-black text-lg">{{ $detailUser->name }}</h3>
                            <p class="text-white/50 text-sm">Grafik Tren Absensi — {{ $bulanList->firstWhere('value', $bulan)['label'] ?? '' }} {{ $tahun }}</p>
                        </div>
                    </div>

                    <canvas id="rekap-chart" style="max-height:280px"></canvas>

                    {{-- Detail table --}}
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Tanggal</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Check-In</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Check-Out</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Durasi</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Status</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest pb-2 px-2">Radius</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailAbsensi as $da)
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="py-2 px-2 text-white/80 text-xs">{{ $da->tanggal->translatedFormat('D, d M') }}</td>
                                        <td class="py-2 px-2 text-white/70 text-xs">{{ $da->waktu_checkin ? $da->waktu_checkin->format('H:i') : '-' }}</td>
                                        <td class="py-2 px-2 text-white/70 text-xs">{{ $da->waktu_checkout ? $da->waktu_checkout->format('H:i') : '-' }}</td>
                                        <td class="py-2 px-2 text-white/70 text-xs">{{ $da->durasi_kerja ?? '-' }}</td>
                                        <td class="py-2 px-2">
                                            @if($da->status === 'tepat_waktu')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300">Tepat Waktu</span>
                                            @elseif($da->status === 'terlambat')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300">Terlambat</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-500/20 text-red-300">Tidak Hadir</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-2 text-[10px] {{ $da->dalam_radius_checkin ? 'text-emerald-400' : 'text-red-400' }}">
                                            {{ $da->waktu_checkin ? ($da->dalam_radius_checkin ? '✓ Dalam' : '✗ Luar') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const ctx = document.getElementById('rekap-chart').getContext('2d');
                        const labels = @json($chartData['labels'] ?? []);
                        const values = @json($chartData['status'] ?? []);

                        const colors = values.map(v => v === 1 ? 'rgba(16,185,129,0.8)' : v === 0.5 ? 'rgba(245,158,11,0.8)' : 'rgba(239,68,68,0.8)');
                        const borderColors = values.map(v => v === 1 ? '#10b981' : v === 0.5 ? '#f59e0b' : '#ef4444');

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Status Absensi',
                                    data: values,
                                    backgroundColor: colors,
                                    borderColor: borderColors,
                                    borderWidth: 1,
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: (ctx) => {
                                                const v = ctx.raw;
                                                return v === 1 ? 'Tepat Waktu' : v === 0.5 ? 'Terlambat' : 'Tidak Hadir';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: { grid:{ color:'rgba(255,255,255,0.05)' }, ticks:{ color:'rgba(255,255,255,0.5)', font:{size:10} } },
                                    y: { min:0, max:1.2, grid:{ color:'rgba(255,255,255,0.05)' }, ticks:{ color:'rgba(255,255,255,0.5)', callback: (v) => v === 1 ? 'Tepat' : v === 0.5 ? 'Terlambat' : 'Alpa', font:{size:10} } }
                                }
                            }
                        });
                    });
                </script>
                @endpush
            @endif

        </div>
    </div>
</x-app-layout>
