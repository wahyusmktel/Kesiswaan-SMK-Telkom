<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Absensi Saya</h2>
    </x-slot>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* ===== AURORA UI BACKGROUND ===== */
        .aurora-bg {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            position: relative;
            overflow: hidden;
        }
        .aurora-bg::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(120,40,200,0.35) 0%, transparent 70%);
            top: -200px; left: -100px;
            animation: aurora1 8s ease-in-out infinite alternate;
        }
        .aurora-bg::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0,200,180,0.3) 0%, transparent 70%);
            bottom: -150px; right: -100px;
            animation: aurora2 10s ease-in-out infinite alternate;
        }
        @keyframes aurora1 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(80px,60px) scale(1.2); } }
        @keyframes aurora2 { 0% { transform: translate(0,0) scale(1); } 100% { transform: translate(-60px,-40px) scale(1.15); } }

        /* ===== GLASSMORPHISM CARD ===== */
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .glass-card-light {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(230,230,240,0.8);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(80,60,120,0.08);
        }

        /* ===== NEUMORPHISM BUTTON ===== */
        .neu-btn-checkin {
            background: linear-gradient(145deg, #22d3ee, #06b6d4);
            box-shadow: 6px 6px 16px rgba(6,182,212,0.4), -4px -4px 10px rgba(255,255,255,0.15);
            border: none;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .neu-btn-checkin:hover { box-shadow: 2px 2px 8px rgba(6,182,212,0.5), -2px -2px 6px rgba(255,255,255,0.1); transform: translateY(2px); }
        .neu-btn-checkin:active { box-shadow: inset 3px 3px 8px rgba(0,0,0,0.2); transform: translateY(3px); }

        .neu-btn-checkout {
            background: linear-gradient(145deg, #f97316, #ea580c);
            box-shadow: 6px 6px 16px rgba(234,88,12,0.4), -4px -4px 10px rgba(255,255,255,0.1);
            border: none;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .neu-btn-checkout:hover { box-shadow: 2px 2px 8px rgba(234,88,12,0.5); transform: translateY(2px); }

        .neu-btn-disabled {
            background: linear-gradient(145deg, #374151, #4b5563);
            box-shadow: inset 3px 3px 8px rgba(0,0,0,0.4), inset -3px -3px 6px rgba(255,255,255,0.05);
            border: none;
            border-radius: 16px;
            cursor: not-allowed;
        }

        /* ===== STATUS BADGES ===== */
        .status-tepat { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .status-terlambat { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .status-tidak-hadir { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }

        /* ===== MAP CONTAINER ===== */
        #absensi-map { height: 300px; border-radius: 16px; z-index: 1; }

        /* ===== CLOCK ===== */
        .live-clock {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #e0e7ff, #a5b4fc, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -2px;
        }
        .pulse-dot { animation: pulse-dot 1.5s infinite; }
        @keyframes pulse-dot { 0%,100% { opacity:1; } 50% { opacity:0; } }

        /* ===== HISTORY TABLE ===== */
        .history-row:hover { background: rgba(255,255,255,0.05); }
    </style>
    @endpush

    <div class="aurora-bg min-h-screen -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6 relative z-10">

            {{-- Top Live Clock & Date --}}
            <div class="glass-card p-8 text-center" x-data="clockWidget()">
                <p class="text-white/50 text-xs font-bold uppercase tracking-widest mb-2" x-text="dateStr"></p>
                <div class="live-clock" x-text="timeStr"></div>
                <p class="text-white/40 text-sm mt-2">{{ Auth::user()->name }}</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ===== Check-In / Check-Out Panel ===== --}}
                <div class="glass-card p-6 space-y-5" x-data="absensiApp()">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h3 class="text-white font-bold text-lg">Absensi Hari Ini</h3>
                    </div>

                    {{-- Status card --}}
                    @if($absensiHariIni && $absensiHariIni->waktu_checkin)
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white/10 rounded-2xl p-4 text-center">
                                <p class="text-white/50 text-[10px] font-bold uppercase tracking-widest">Check-In</p>
                                <p class="text-white text-2xl font-black mt-1">{{ $absensiHariIni->waktu_checkin->format('H:i') }}</p>
                                <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-black {{ $absensiHariIni->status === 'tepat_waktu' ? 'bg-emerald-500/30 text-emerald-300' : 'bg-amber-500/30 text-amber-300' }}">
                                    {{ $absensiHariIni->status_label }}
                                </span>
                            </div>
                            <div class="bg-white/10 rounded-2xl p-4 text-center">
                                <p class="text-white/50 text-[10px] font-bold uppercase tracking-widest">Check-Out</p>
                                <p class="text-white text-2xl font-black mt-1">
                                    {{ $absensiHariIni->waktu_checkout ? $absensiHariIni->waktu_checkout->format('H:i') : '--:--' }}
                                </p>
                                <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[10px] font-black {{ $absensiHariIni->waktu_checkout ? 'bg-emerald-500/30 text-emerald-300' : 'bg-gray-500/30 text-gray-400' }}">
                                    {{ $absensiHariIni->waktu_checkout ? 'Selesai' : 'Belum' }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="bg-white/5 rounded-2xl p-4 text-center border border-white/10">
                            <svg class="w-10 h-10 text-white/20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-white/50 text-sm">Belum absen hari ini</p>
                            <p class="text-white/30 text-xs mt-1">Batas check-in: {{ substr($setting->jam_masuk_batas, 0, 5) }}</p>
                        </div>
                    @endif

                    {{-- Location Status --}}
                    <div class="bg-white/5 rounded-xl p-3 flex items-center gap-3 border border-white/10">
                        <div class="w-2 h-2 rounded-full flex-shrink-0" :class="locationReady ? 'bg-emerald-400 animate-pulse' : 'bg-red-400'"></div>
                        <p class="text-white/70 text-xs" x-text="locationStatus"></p>
                    </div>

                    {{-- Radius Info --}}
                    <div x-show="distanceToSchool !== null" class="bg-white/5 rounded-xl p-3 border border-white/10" x-cloak>
                        <div class="flex items-center justify-between">
                            <span class="text-white/60 text-xs">Jarak ke Sekolah</span>
                            <span class="text-white font-bold text-sm" x-text="distanceToSchool + ' meter'"></span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                :class="inRadius ? 'bg-gradient-to-r from-emerald-400 to-teal-400' : 'bg-gradient-to-r from-red-400 to-rose-500'"
                                :style="'width: ' + Math.min(100, ({{ $setting->radius_meter }} / Math.max(distanceRaw, 1)) * 100) + '%'"></div>
                        </div>
                        <p class="text-[10px] mt-1" :class="inRadius ? 'text-emerald-400' : 'text-red-400'" x-text="inRadius ? '✓ Dalam radius zona absensi ({{ $setting->radius_meter }}m)' : '⚠ Di luar radius zona absensi ({{ $setting->radius_meter }}m)'"></p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="grid grid-cols-1 gap-3">
                        @if(!$absensiHariIni || !$absensiHariIni->waktu_checkin)
                            <button @click="doCheckin()" :disabled="!locationReady || loading"
                                class="neu-btn-checkin w-full py-4 text-white font-black text-base flex items-center justify-center gap-3 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                <span x-text="loading ? 'Memproses...' : 'CHECK-IN Sekarang'"></span>
                            </button>
                        @elseif(!$absensiHariIni->waktu_checkout)
                            <div class="neu-btn-disabled w-full py-3 text-white/30 font-black text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Check-in: {{ $absensiHariIni->waktu_checkin->format('H:i') }}
                            </div>
                            <button @click="doCheckout()" :disabled="!locationReady || loading"
                                class="neu-btn-checkout w-full py-4 text-white font-black text-base flex items-center justify-center gap-3 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span x-text="loading ? 'Memproses...' : 'CHECK-OUT Sekarang'"></span>
                            </button>
                        @else
                            <div class="neu-btn-disabled w-full py-3 text-emerald-300/70 font-black text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Absensi lengkap hari ini ✓
                            </div>
                        @endif
                    </div>

                    {{-- Notification Toast --}}
                    <div x-show="notification" x-cloak
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        class="mt-2 p-3 rounded-xl text-sm font-medium"
                        :class="notifSuccess ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30'"
                        x-text="notification">
                    </div>

                    {{-- Settings Info --}}
                    <div class="flex items-center justify-between pt-2 border-t border-white/10 text-[11px] text-white/40">
                        <span>Batas masuk: <span class="text-white/60 font-bold">{{ substr($setting->jam_masuk_batas, 0, 5) }}</span></span>
                        <span>Jam pulang: <span class="text-white/60 font-bold">{{ substr($setting->jam_keluar_batas, 0, 5) }}</span></span>
                        <span>Radius: <span class="text-white/60 font-bold">{{ $setting->radius_meter }}m</span></span>
                    </div>
                </div>

                {{-- ===== Mini Map Panel ===== --}}
                <div class="glass-card p-6 space-y-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">Lokasi Absensi</h3>
                            <p class="text-white/40 text-xs">OpenStreetMap — Lingkaran = zona absensi</p>
                        </div>
                    </div>
                    <div id="absensi-map" class="w-full border border-white/10"></div>
                    <div class="flex gap-4 text-[11px] text-white/50">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Posisi Anda</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Sekolah</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500/30 border border-emerald-500 inline-block"></span> Zona Absensi</span>
                    </div>
                </div>
            </div>

            {{-- ===== Riwayat Absensi ===== --}}
            <div class="glass-card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-white font-bold text-lg">Riwayat 30 Hari Terakhir</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Tanggal</th>
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Check-In</th>
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Check-Out</th>
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Durasi</th>
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Status</th>
                                <th class="text-left text-white/40 text-[10px] font-black uppercase tracking-widest pb-3 px-2">Radius</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $row)
                                <tr class="history-row border-b border-white/5 transition-colors">
                                    <td class="py-3 px-2 text-white font-medium">{{ $row->tanggal->translatedFormat('D, d M Y') }}</td>
                                    <td class="py-3 px-2 text-white/70">{{ $row->waktu_checkin ? $row->waktu_checkin->format('H:i') : '-' }}</td>
                                    <td class="py-3 px-2 text-white/70">{{ $row->waktu_checkout ? $row->waktu_checkout->format('H:i') : '-' }}</td>
                                    <td class="py-3 px-2 text-white/70">{{ $row->durasi_kerja ?? '-' }}</td>
                                    <td class="py-3 px-2">
                                        @if($row->status === 'tepat_waktu')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Tepat Waktu</span>
                                        @elseif($row->status === 'terlambat')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">Terlambat</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-500/20 text-red-300 border border-red-500/30">Tidak Hadir</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">
                                        @if($row->waktu_checkin)
                                            <span class="text-[10px] {{ $row->dalam_radius_checkin ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ $row->dalam_radius_checkin ? '✓ Dalam' : '✗ Luar' }}
                                            </span>
                                        @else
                                            <span class="text-white/30 text-[10px]">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-white/30 italic">Belum ada riwayat absensi</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ===== CLOCK WIDGET =====
        function clockWidget() {
            return {
                timeStr: '', dateStr: '',
                init() {
                    const tick = () => {
                        const now = new Date();
                        this.timeStr = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
                        this.dateStr = now.toLocaleDateString('id-ID', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
                    };
                    tick();
                    setInterval(tick, 1000);
                }
            };
        }

        // ===== ABSENSI APP =====
        function absensiApp() {
            return {
                lat: null, lng: null,
                locationReady: false,
                locationStatus: 'Mendeteksi lokasi Anda...',
                distanceToSchool: null,
                distanceRaw: 0,
                inRadius: false,
                loading: false,
                notification: '',
                notifSuccess: true,
                schoolLat: {{ $setting->latitude_sekolah }},
                schoolLng: {{ $setting->longitude_sekolah }},
                radiusMeter: {{ $setting->radius_meter }},
                map: null, userMarker: null, schoolMarker: null, radiusCircle: null,

                init() {
                    this.$nextTick(() => this.initMap());
                    this.getLocation();
                },

                initMap() {
                    this.map = L.map('absensi-map').setView([this.schoolLat, this.schoolLng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors', maxZoom: 19
                    }).addTo(this.map);
                    this.schoolMarker = L.marker([this.schoolLat, this.schoolLng], {
                        icon: L.divIcon({ className: '', html: '<div style="width:16px;height:16px;background:#ef4444;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4)"></div>', iconAnchor:[8,8] })
                    }).addTo(this.map).bindPopup('<b>📍 SMK Telkom Lampung</b>');
                    this.radiusCircle = L.circle([this.schoolLat, this.schoolLng], {
                        color: '#10b981', fillColor: '#10b981', fillOpacity: 0.1,
                        weight: 2, radius: this.radiusMeter
                    }).addTo(this.map);
                },

                getLocation() {
                    if (!navigator.geolocation) {
                        this.locationStatus = '✗ Browser tidak mendukung Geolocation';
                        return;
                    }
                    navigator.geolocation.watchPosition(
                        (pos) => {
                            this.lat = pos.coords.latitude;
                            this.lng = pos.coords.longitude;
                            this.locationReady = true;
                            this.locationStatus = `✓ Lokasi terdeteksi (akurasi ±${Math.round(pos.coords.accuracy)}m)`;
                            this.updateDistance();
                            this.updateUserMarker();
                        },
                        (err) => {
                            this.locationStatus = '✗ Akses lokasi ditolak. Aktifkan GPS browser Anda.';
                        },
                        { enableHighAccuracy: true, maximumAge: 10000 }
                    );
                },

                haversine(lat1, lng1, lat2, lng2) {
                    const R = 6371000;
                    const dLat = (lat2-lat1)*Math.PI/180;
                    const dLng = (lng2-lng1)*Math.PI/180;
                    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                },

                updateDistance() {
                    if (!this.lat) return;
                    this.distanceRaw = this.haversine(this.lat, this.lng, this.schoolLat, this.schoolLng);
                    this.distanceToSchool = Math.round(this.distanceRaw);
                    this.inRadius = this.distanceRaw <= this.radiusMeter;
                },

                updateUserMarker() {
                    if (!this.map || !this.lat) return;
                    const icon = L.divIcon({ className: '', html: '<div style="width:16px;height:16px;background:#3b82f6;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.4)"></div>', iconAnchor:[8,8] });
                    if (this.userMarker) this.userMarker.setLatLng([this.lat, this.lng]);
                    else { this.userMarker = L.marker([this.lat, this.lng], {icon}).addTo(this.map).bindPopup('📍 Posisi Anda'); }
                },

                async doCheckin() {
                    if (!this.lat) return;
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('absensi-saya.checkin') }}', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                            body: JSON.stringify({latitude: this.lat, longitude: this.lng})
                        });
                        const data = await res.json();
                        this.notifSuccess = data.success;
                        this.notification = data.message;
                        if (data.success) setTimeout(() => location.reload(), 2000);
                    } catch(e) { this.notification = 'Terjadi kesalahan koneksi'; this.notifSuccess = false; }
                    this.loading = false;
                    setTimeout(() => this.notification = '', 5000);
                },

                async doCheckout() {
                    if (!this.lat) return;
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('absensi-saya.checkout') }}', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                            body: JSON.stringify({latitude: this.lat, longitude: this.lng})
                        });
                        const data = await res.json();
                        this.notifSuccess = data.success;
                        this.notification = data.message;
                        if (data.success) setTimeout(() => location.reload(), 2000);
                    } catch(e) { this.notification = 'Terjadi kesalahan koneksi'; this.notifSuccess = false; }
                    this.loading = false;
                    setTimeout(() => this.notification = '', 5000);
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
