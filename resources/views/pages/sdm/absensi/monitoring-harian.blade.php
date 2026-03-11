<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Harian Absensi</h2>
    </x-slot>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .aurora-bg { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); }
        .aurora-bg::before { content:''; position:absolute; width:500px; height:500px; background:radial-gradient(circle,rgba(120,40,200,0.3) 0%,transparent 70%); top:-100px; left:-100px; animation:aurora1 8s ease-in-out infinite alternate; pointer-events:none; }
        .aurora-bg::after { content:''; position:absolute; width:400px; height:400px; background:radial-gradient(circle,rgba(0,180,160,0.25) 0%,transparent 70%); bottom:-100px; right:-80px; animation:aurora2 10s ease-in-out infinite alternate; pointer-events:none; }
        @keyframes aurora1 { 0% {transform:translate(0,0);} 100% {transform:translate(60px,40px);} }
        @keyframes aurora2 { 0% {transform:translate(0,0);} 100% {transform:translate(-40px,-30px);} }
        .glass-card { background:rgba(255,255,255,0.07); backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.12); border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,0.3); }
        .stat-card { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:16px; }
        #monitoring-map { height: 420px; border-radius:16px; z-index:1; }
        .status-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:999px; font-size:10px; font-weight:900; }
        .badge-tepat { background:rgba(16,185,129,0.2); color:#34d399; border:1px solid rgba(16,185,129,0.3); }
        .badge-terlambat { background:rgba(245,158,11,0.2); color:#fbbf24; border:1px solid rgba(245,158,11,0.3); }
        .badge-belum { background:rgba(239,68,68,0.2); color:#f87171; border:1px solid rgba(239,68,68,0.3); }
        .date-input { background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.2); border-radius:12px; color:white; padding:0.6rem 1rem; }
        .date-input:focus { outline:none; border-color:rgba(139,92,246,0.6); }
    </style>
    @endpush

    <div class="aurora-bg min-h-screen -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto space-y-6 relative z-10">

            {{-- Header --}}
            <div class="glass-card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div>
                        <h1 class="text-white font-black text-xl">Monitoring Harian</h1>
                        <p class="text-white/50 text-sm">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal->format('Y-m-d') }}" class="date-input" onchange="this.form.submit()">
                </form>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                    $stats = [
                        ['label'=>'Total Hadir','value'=>$totalHadir,'color'=>'from-teal-500 to-cyan-600','icon'=>'M5 13l4 4L19 7'],
                        ['label'=>'Tepat Waktu','value'=>$totalTepat,'color'=>'from-emerald-500 to-green-600','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label'=>'Terlambat','value'=>$totalTerlambat,'color'=>'from-amber-500 to-orange-500','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['label'=>'Belum Absen','value'=>$totalBelumAbsen,'color'=>'from-red-500 to-rose-600','icon'=>'M6 18L18 6M6 6l12 12'],
                    ];
                @endphp
                @foreach($stats as $st)
                    <div class="glass-card p-5 text-center">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $st['color'] }} flex items-center justify-center mx-auto mb-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $st['icon'] }}"/></svg>
                        </div>
                        <p class="text-white text-2xl font-black">{{ $st['value'] }}</p>
                        <p class="text-white/40 text-[10px] font-bold uppercase tracking-wider mt-1">{{ $st['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                {{-- Attendance Table --}}
                <div class="glass-card p-6">
                    <h3 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Data Absensi Pegawai
                    </h3>
                    <div class="overflow-y-auto max-h-[420px]">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 bg-gray-900/80 backdrop-blur">
                                <tr>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest py-2 px-2">Pegawai</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest py-2 px-2">Masuk</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest py-2 px-2">Pulang</th>
                                    <th class="text-left text-white/40 text-[9px] font-black uppercase tracking-widest py-2 px-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensiList as $abs)
                                    <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="py-2.5 px-2">
                                            <div class="flex items-center gap-2">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($abs->user->name) }}&background=6d28d9&color=fff&size=32" class="w-7 h-7 rounded-lg" alt="">
                                                <span class="text-white text-xs font-medium truncate max-w-[100px]">{{ $abs->user->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-2 text-white/70 text-xs">{{ $abs->waktu_checkin ? $abs->waktu_checkin->format('H:i') : '-' }}</td>
                                        <td class="py-2.5 px-2 text-white/70 text-xs">{{ $abs->waktu_checkout ? $abs->waktu_checkout->format('H:i') : '-' }}</td>
                                        <td class="py-2.5 px-2">
                                            <span class="status-badge {{ $abs->status === 'tepat_waktu' ? 'badge-tepat' : ($abs->status === 'terlambat' ? 'badge-terlambat' : 'badge-belum') }}">
                                                {{ $abs->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="py-8 text-center text-white/30 italic text-sm">Belum ada data absensi untuk tanggal ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Map with attendance points --}}
                <div class="glass-card p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-white font-bold text-base flex items-center gap-2">
                            <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            Sebaran Titik Absensi
                        </h3>
                    </div>
                    <div id="monitoring-map" class="w-full border border-white/10"></div>
                    {{-- Legend --}}
                    <div class="flex flex-wrap gap-3 text-[11px] text-white/60">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Tepat Waktu</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> Terlambat</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block border-2 border-white"></span> Lokasi Sekolah</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full border border-emerald-500 bg-emerald-500/20 inline-block"></span> Zona Absensi</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('monitoring-map').setView([{{ $setting->latitude_sekolah }}, {{ $setting->longitude_sekolah }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution:'© OpenStreetMap', maxZoom:19}).addTo(map);

            // School marker
            L.marker([{{ $setting->latitude_sekolah }}, {{ $setting->longitude_sekolah }}], {
                icon: L.divIcon({className:'', html:`<div style="width:16px;height:16px;background:#ef4444;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.5)"></div>`, iconAnchor:[8,8]})
            }).addTo(map).bindPopup('<b>📍 SMK Telkom Lampung</b>');

            // Radius zone
            L.circle([{{ $setting->latitude_sekolah }}, {{ $setting->longitude_sekolah }}], {
                color:'#10b981', fillColor:'#10b981', fillOpacity:0.12, weight:2, radius:{{ $setting->radius_meter }}
            }).addTo(map);

            // Attendance points
            const points = @json($mapPoints);
            const colors = {tepat_waktu:'#10b981', terlambat:'#f59e0b', tidak_hadir:'#ef4444'};
            points.forEach(p => {
                const color = colors[p.status] || '#6b7280';
                const marker = L.marker([p.lat, p.lng], {
                    icon: L.divIcon({className:'', html:`<div style="width:14px;height:14px;background:${color};border-radius:50%;border:2px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4);opacity:0.9"></div>`, iconAnchor:[7,7]})
                }).addTo(map);
                marker.bindPopup(`<b>${p.name}</b><br>Checkin: ${p.waktu}<br>Status: ${p.status.replace('_',' ')}`);
            });
        });
    </script>
    @endpush
</x-app-layout>
