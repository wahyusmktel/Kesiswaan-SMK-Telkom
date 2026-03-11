<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengaturan Absensi</h2>
    </x-slot>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .aurora-bg { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); }
        .glass-card { background: rgba(255,255,255,0.07); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .neu-input {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            box-shadow: inset 3px 3px 8px rgba(0,0,0,0.3), inset -2px -2px 6px rgba(255,255,255,0.05);
            color: white;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.2s;
        }
        .neu-input:focus { outline: none; border-color: rgba(139,92,246,0.6); box-shadow: inset 2px 2px 6px rgba(0,0,0,0.3), 0 0 0 3px rgba(139,92,246,0.2); }
        .neu-input::placeholder { color: rgba(255,255,255,0.3); }
        .neu-btn-save {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            box-shadow: 6px 6px 16px rgba(124,58,237,0.4), -3px -3px 8px rgba(255,255,255,0.1);
            border: none; border-radius: 14px;
            transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        .neu-btn-save:hover { box-shadow: 3px 3px 10px rgba(124,58,237,0.5); transform: translateY(2px); }
        .label-text { color: rgba(255,255,255,0.6); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; display: block; }
        #settings-map { height: 380px; border-radius: 16px; }
    </style>
    @endpush

    <div class="aurora-bg min-h-screen -m-4 sm:-m-6 lg:-m-8 p-4 sm:p-6 lg:p-8">
        <div class="max-w-5xl mx-auto space-y-6 relative z-10">

            {{-- Header --}}
            <div class="glass-card p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h1 class="text-white font-black text-xl">Pengaturan Absensi Pegawai</h1>
                    <p class="text-white/50 text-sm">Atur jam batas check-in, jam pulang, koordinat sekolah, dan radius zona absensi.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="glass-card p-4 border-l-4 border-emerald-400 bg-emerald-500/10">
                    <p class="text-emerald-300 font-medium">✓ {{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('sdm.absensi-settings.update') }}" method="POST" x-data="settingsApp()">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Left: Form --}}
                    <div class="glass-card p-6 space-y-5">
                        <h3 class="text-white font-bold text-base flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pengaturan Waktu
                        </h3>

                        <div>
                            <label class="label-text">Batas Jam Check-In (Lewat = Terlambat)</label>
                            <input type="time" name="jam_masuk_batas" value="{{ substr($setting->jam_masuk_batas, 0, 5) }}" class="neu-input" required>
                            @error('jam_masuk_batas')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="label-text">Jam Pulang / Batas Check-Out</label>
                            <input type="time" name="jam_keluar_batas" value="{{ substr($setting->jam_keluar_batas, 0, 5) }}" class="neu-input" required>
                            @error('jam_keluar_batas')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="pt-2 border-t border-white/10">
                            <h3 class="text-white font-bold text-base mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                Koordinat & Radius Zona Absensi
                            </h3>
                            <p class="text-white/40 text-xs mb-4">Klik pada peta untuk memilih koordinat sekolah secara interaktif.</p>
                        </div>

                        <div>
                            <label class="label-text">Latitude Sekolah</label>
                            <input type="number" step="any" name="latitude_sekolah" id="lat-input" x-model="lat" class="neu-input" required>
                        </div>

                        <div>
                            <label class="label-text">Longitude Sekolah</label>
                            <input type="number" step="any" name="longitude_sekolah" id="lng-input" x-model="lng" class="neu-input" required>
                        </div>

                        <div>
                            <label class="label-text">Radius Zona Absensi (meter)<span class="text-white/40 ml-1 normal-case" x-text="' — ' + radius + ' m'"></span></label>
                            <input type="range" name="radius_meter" x-model="radius" min="10" max="2000" step="10"
                                class="w-full accent-violet-500 cursor-pointer" @input="updateMapRadius()">
                            <div class="flex justify-between text-white/30 text-[10px] mt-1"><span>10m</span><span>2000m</span></div>
                        </div>

                        <button type="submit" class="neu-btn-save w-full py-4 text-white font-black text-base flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Pengaturan
                        </button>
                    </div>

                    {{-- Right: Map --}}
                    <div class="glass-card p-6 space-y-4">
                        <h3 class="text-white font-bold text-base flex items-center gap-2">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            Peta Zona Absensi — Klik untuk atur lokasi
                        </h3>
                        <div id="settings-map" class="w-full border border-white/10"></div>
                        <div class="flex gap-4 text-[11px] text-white/50">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Titik Sekolah</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500/30 border border-emerald-500 inline-block"></span> Zona Radius</span>
                        </div>
                        <div class="bg-white/5 rounded-xl p-3 border border-white/10 text-xs text-white/50">
                            💡 Geser marker merah atau klik pada peta untuk mengubah lokasi sekolah. Pastikan simpan pengaturan setelah selesai.
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        function settingsApp() {
            return {
                lat: {{ $setting->latitude_sekolah }},
                lng: {{ $setting->longitude_sekolah }},
                radius: {{ $setting->radius_meter }},
                map: null, marker: null, circle: null,

                init() {
                    this.$nextTick(() => {
                        this.map = L.map('settings-map').setView([this.lat, this.lng], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap', maxZoom: 19
                        }).addTo(this.map);

                        this.marker = L.marker([this.lat, this.lng], {draggable: true,
                            icon: L.divIcon({className:'', html:'<div style="width:18px;height:18px;background:#ef4444;border-radius:50%;border:3px solid white;box-shadow:0 2px 10px rgba(0,0,0,0.5);cursor:grab"></div>', iconAnchor:[9,9]})
                        }).addTo(this.map);

                        this.circle = L.circle([this.lat, this.lng], {
                            color: '#10b981', fillColor: '#10b981', fillOpacity: 0.12, weight: 2, radius: this.radius
                        }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const p = e.target.getLatLng();
                            this.lat = p.lat.toFixed(7);
                            this.lng = p.lng.toFixed(7);
                            this.circle.setLatLng(p);
                        });

                        this.map.on('click', (e) => {
                            this.lat = e.latlng.lat.toFixed(7);
                            this.lng = e.latlng.lng.toFixed(7);
                            this.marker.setLatLng(e.latlng);
                            this.circle.setLatLng(e.latlng);
                        });
                    });
                },

                updateMapRadius() {
                    if (this.circle) this.circle.setRadius(parseInt(this.radius));
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
