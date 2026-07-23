<x-app-layout>
<div x-data="whatsappGatewayApp()" x-init="initData()" class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">

    <!-- Toast Notification -->
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
        :class="{
            'bg-emerald-600 text-white': toast.type === 'success',
            'bg-rose-600 text-white': toast.type === 'error',
            'bg-amber-500 text-white': toast.type === 'warning',
            'bg-blue-600 text-white': toast.type === 'info'
        }"
        class="fixed bottom-5 right-5 z-50 px-5 py-3.5 rounded-2xl shadow-2xl flex items-center space-x-3 text-sm font-semibold max-w-md border border-white/20"
        style="display: none;">
        <template x-if="toast.type === 'success'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </template>
        <template x-if="toast.type === 'error'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </template>
        <span x-text="toast.message"></span>
    </div>

    <!-- Header Section -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 sm:p-8 text-white shadow-2xl border border-emerald-500/20">
        <!-- Background Glow Elements -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-bold tracking-wider uppercase backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>WhatsApp Engine 2.0 SPA</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight flex items-center gap-3">
                    <span>WhatsApp Gateway</span>
                    <span class="text-xs bg-emerald-500 text-slate-950 font-black px-2.5 py-1 rounded-lg">SUPERADMIN</span>
                </h1>
                <p class="text-emerald-100/80 text-sm max-w-2xl">
                    Kelola koneksi nomor WhatsApp, konfigurasi trigger bot notifikasi otomatis untuk presensi & perizinan siswa, serta pantau log pengiriman pesan secara real-time.
                </p>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <button @click="openAddDeviceModal()"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/30 hover:shadow-emerald-400/40 transition-all flex items-center space-x-2 transform active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Perangkat</span>
                </button>
                <button @click="refreshDevicesData()" :disabled="loading"
                    class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm backdrop-blur-md border border-white/10 transition-all flex items-center space-x-2 transform active:scale-95">
                    <svg :class="{'animate-spin': loading}" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1: Total Device -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Perangkat</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1" x-text="stats.total_devices">0</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-3 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Terdaftar dalam sistem</span>
            </p>
        </div>

        <!-- Stat 2: Connected Device -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Terhubung</p>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1 flex items-center gap-2">
                        <span x-text="stats.connected_devices">0</span>
                        <span class="text-sm font-normal text-slate-500">/ <span x-text="stats.total_devices">0</span></span>
                    </h3>
                </div>
                <div class="relative w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <template x-if="stats.connected_devices > 0">
                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full animate-ping"></span>
                    </template>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-emerald-600 font-medium mt-3 flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span x-text="stats.connected_devices > 0 ? 'Sesi WhatsApp aktif & siap kirim' : 'Tidak ada sesi aktif'"></span>
            </p>
        </div>

        <!-- Stat 3: Sent Today -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pesan Hari Ini</p>
                    <h3 class="text-2xl font-black text-blue-600 mt-1" x-text="stats.total_sent_today">0</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-3">Log keluar hari ini</p>
        </div>

        <!-- Stat 4: Success Rate -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Keberhasilan</p>
                    <h3 class="text-2xl font-black text-indigo-600 mt-1" x-text="stats.success_rate + '%'">100%</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-3">Rasio pengiriman berhasil</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-sm flex flex-wrap gap-2">
        <button @click="activeTab = 'devices'"
            :class="activeTab === 'devices' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Perangkat & Sesi</span>
            <span :class="activeTab === 'devices' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'" class="px-2 py-0.5 rounded-full text-xs font-bold" x-text="devices.length">0</span>
        </button>

        <button @click="activeTab = 'templates'"
            :class="activeTab === 'templates' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Bot Notifikasi & Template</span>
        </button>

        <button @click="activeTab = 'test'"
            :class="activeTab === 'test' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <span>Uji Coba Kirim Pesan</span>
        </button>

        <button @click="activeTab = 'logs'; fetchLogs()"
            :class="activeTab === 'logs' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 hover:bg-slate-100'"
            class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Log & Riwayat</span>
        </button>
    </div>

    <!-- TAB 1: PERANGKAT & SESI -->
    <div x-show="activeTab === 'devices'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="device in devices" :key="device.id">
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative overflow-hidden group">
                    <!-- Default Gateway Indicator -->
                    <template x-if="device.is_default">
                        <div class="absolute top-0 right-0 bg-emerald-500 text-slate-950 font-black text-[10px] uppercase px-3 py-1 rounded-bl-xl shadow-sm">
                            Gateway Utama
                        </div>
                    </template>

                    <div>
                        <!-- Header Device Card -->
                        <div class="flex items-center space-x-4 mb-4">
                            <div :class="{
                                    'bg-emerald-100 text-emerald-600 border-emerald-200': device.status === 'connected',
                                    'bg-amber-100 text-amber-600 border-amber-200': device.status === 'connecting' || device.status === 'qr_ready',
                                    'bg-slate-100 text-slate-500 border-slate-200': device.status === 'disconnected'
                                }"
                                class="w-14 h-14 rounded-2xl border flex items-center justify-center flex-shrink-0 transition-colors">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-lg font-extrabold text-slate-800 truncate" x-text="device.name"></h4>
                                <p class="text-xs font-mono text-slate-500" x-text="device.phone_number || 'Belum Terhubung'"></p>
                                <span class="inline-block mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-400" x-text="'Provider: ' + device.provider"></span>
                            </div>
                        </div>

                        <!-- Status Pill & Session Info -->
                        <div class="bg-slate-50 rounded-2xl p-4 space-y-2 border border-slate-100 mb-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Status Koneksi:</span>
                                <span :class="{
                                        'bg-emerald-500/10 text-emerald-600 border-emerald-200': device.status === 'connected',
                                        'bg-amber-500/10 text-amber-600 border-amber-200': device.status === 'qr_ready' || device.status === 'connecting',
                                        'bg-rose-500/10 text-rose-600 border-rose-200': device.status === 'disconnected'
                                    }"
                                    class="px-2.5 py-1 rounded-full font-bold border text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                    <span :class="{
                                            'bg-emerald-500 animate-pulse': device.status === 'connected',
                                            'bg-amber-500 animate-ping': device.status === 'qr_ready' || device.status === 'connecting',
                                            'bg-rose-500': device.status === 'disconnected'
                                        }" class="w-2 h-2 rounded-full"></span>
                                    <span x-text="device.status"></span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">ID Sesi:</span>
                                <span class="font-mono text-slate-700 font-semibold" x-text="device.session_id"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Koneksi Terakhir:</span>
                                <span class="text-slate-600" x-text="device.last_connected_at ? formatDateTime(device.last_connected_at) : 'Belum pernah'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                        <template x-if="device.status === 'disconnected' || device.status === 'qr_ready'">
                            <button @click="openQrModal(device)"
                                class="flex-1 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-colors flex items-center justify-center space-x-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                <span>Hubungkan QR</span>
                            </button>
                        </template>

                        <template x-if="device.status === 'connected'">
                            <button @click="disconnectDevice(device)"
                                class="flex-1 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors flex items-center justify-center space-x-1.5 border border-rose-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Putuskan</span>
                            </button>
                        </template>

                        <button @click="openEditDeviceModal(device)"
                            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Edit Perangkat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>

                        <button @click="deleteDevice(device)"
                            class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors" title="Hapus Perangkat">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- TAB 2: BOT NOTIFIKASI & TEMPLATE -->
    <div x-show="activeTab === 'templates'" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Template List & Editor -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Template Bot Notifikasi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Atur format pesan otomatis yang dikirim ke siswa / orang tua siswa.</p>
                    </div>
                    <button @click="saveTemplates()" :disabled="loading"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-md transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

                <div class="space-y-6">
                    <template x-for="(tpl, index) in templateList" :key="tpl.event_key">
                        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                    <div>
                                        <h4 class="font-extrabold text-slate-800 text-sm" x-text="tpl.title"></h4>
                                        <span class="text-[11px] font-mono text-slate-400" x-text="'Event: ' + tpl.event_key"></span>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="tpl.is_enabled" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    <span class="ml-2 text-xs font-bold" :class="tpl.is_enabled ? 'text-emerald-700' : 'text-slate-400'" x-text="tpl.is_enabled ? 'Aktif' : 'Nonaktif'"></span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Format Pesan WhatsApp:</label>
                                <textarea x-model="tpl.template_text" rows="5"
                                    @focus="selectedTemplateKey = tpl.event_key"
                                    class="w-full rounded-xl border-slate-300 text-xs font-sans focus:ring-emerald-500 focus:border-emerald-500 shadow-sm leading-relaxed"
                                    placeholder="Tulis format pesan bot WhatsApp..."></textarea>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Variable Dinamis Tersedia:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="v in tpl.variables" :key="v">
                                        <button @click="insertVariable(tpl, v)"
                                            class="px-2 py-0.5 rounded-md bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-[11px] font-mono font-semibold transition-colors">
                                            <span x-text="'{' + v + '}'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Realtime WhatsApp Mobile Preview Box -->
        <div class="lg:col-span-5">
            <div class="sticky top-6 bg-slate-900 rounded-3xl p-5 border border-slate-800 shadow-2xl text-white space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Live WhatsApp Chat Simulator</span>
                    </div>
                    <span class="text-[10px] bg-emerald-500/20 text-emerald-300 font-mono px-2 py-0.5 rounded-full">Preview UI</span>
                </div>

                <!-- Smartphone Screen Mockup -->
                <div class="bg-[#0b141a] rounded-2xl p-4 min-h-[420px] flex flex-col justify-between border border-slate-800 shadow-inner relative overflow-hidden">
                    <!-- WhatsApp Header Mockup -->
                    <div class="bg-[#202c33] -mx-4 -mt-4 p-3 flex items-center space-x-3 border-b border-slate-800 text-slate-200">
                        <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white text-sm">
                            WA
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-slate-100">Bot Kesiswaan SMK Telkom</h5>
                            <p class="text-[10px] text-emerald-400 font-medium">Official Notification Gateway</p>
                        </div>
                    </div>

                    <!-- Chat Bubble Body -->
                    <div class="py-4 space-y-3 flex-1 flex flex-col justify-end">
                        <div class="bg-[#005c4b] text-slate-100 p-3.5 rounded-2xl rounded-tr-none text-xs font-sans max-w-[90%] ml-auto shadow-md leading-relaxed whitespace-pre-line relative">
                            <span x-text="previewText"></span>
                            <div class="flex items-center justify-end space-x-1 mt-2 text-[9px] text-emerald-200">
                                <span x-text="currentTime()"></span>
                                <svg class="w-3.5 h-3.5 text-sky-300" fill="currentColor" viewBox="0 0 24 24"><path d="M.427 12.657L5.59 17.82l1.414-1.414L1.84 11.243zM10.121 16.406l1.414 1.414 7.071-7.071-1.414-1.414zM16.485 7.921l1.414 1.414 4.243-4.243-1.414-1.414z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Input Footer Mockup -->
                    <div class="bg-[#202c33] -mx-4 -mb-4 p-2.5 flex items-center space-x-2 border-t border-slate-800 text-slate-400 text-xs">
                        <div class="flex-1 bg-[#2a3942] rounded-full px-3 py-1.5 text-slate-400 text-[11px]">
                            Ketik pesan...
                        </div>
                        <div class="w-7 h-7 rounded-full bg-emerald-600 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: UJI COBA KIRIM PESAN -->
    <div x-show="activeTab === 'test'" class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Uji Coba Pengiriman Pesan WhatsApp</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">Kirim pesan WhatsApp langsung ke nomor tujuan untuk memastikan perangkat & gateway berfungsi secara baik.</p>
            </div>

            <form @submit.prevent="sendTestMessage()" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Perangkat WhatsApp Pengirim:</label>
                    <select x-model="testForm.whatsapp_device_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <template x-for="d in devices" :key="d.id">
                            <option :value="d.id" x-text="d.name + ' (' + (d.phone_number || 'Sesi ' + d.session_id) + ')'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nomor WhatsApp Tujuan:</label>
                    <input type="text" x-model="testForm.recipient" placeholder="Contoh: 081234567890 atau 6281234567890" required
                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                    <span class="text-[11px] text-slate-400 mt-1 block">Format otomatis disesuaikan dengan kode negara +62 Indonesia.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Isi Pesan Test:</label>
                    <textarea x-model="testForm.message" rows="4" required
                        class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 shadow-sm"
                        placeholder="Tulis pesan pengujian di sini..."></textarea>
                </div>

                <div class="pt-3">
                    <button type="submit" :disabled="sendingTest"
                        class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center space-x-2">
                        <template x-if="sendingTest">
                            <svg class="animate-spin w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </template>
                        <template x-if="!sendingTest">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </template>
                        <span x-text="sendingTest ? 'Mengirim Pesan...' : 'Kirim Pesan Uji Coba Now'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TAB 4: LOG & RIWAYAT PESAN -->
    <div x-show="activeTab === 'logs'" class="space-y-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Log & Riwayat Pengiriman Pesan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar rekaman seluruh pesan WhatsApp yang dikirimkan oleh sistem.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <input type="text" x-model="logFilter.search" @input.debounce.400ms="fetchLogs()" placeholder="Cari nomor / isi pesan..."
                        class="rounded-xl border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2 w-48 sm:w-64">

                    <select x-model="logFilter.status" @change="fetchLogs()" class="rounded-xl border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 py-2">
                        <option value="">Semua Status</option>
                        <option value="sent">Sent</option>
                        <option value="delivered">Delivered</option>
                        <option value="failed">Failed</option>
                        <option value="pending">Pending</option>
                    </select>

                    <button @click="clearLogs()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors">
                        Clear Log
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="p-3.5">Waktu</th>
                            <th class="p-3.5">Perangkat</th>
                            <th class="p-3.5">Penerima</th>
                            <th class="p-3.5">Kategori / Tipe</th>
                            <th class="p-3.5">Isi Pesan</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <template x-for="log in logData.data" :key="log.id">
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-3.5 font-mono text-slate-500 whitespace-nowrap" x-text="formatDateTime(log.created_at)"></td>
                                <td class="p-3.5 font-semibold text-slate-700" x-text="log.device ? log.device.name : 'Default Gateway'"></td>
                                <td class="p-3.5 font-mono font-bold text-emerald-700" x-text="log.recipient"></td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px] uppercase" x-text="log.type"></span>
                                </td>
                                <td class="p-3.5 max-w-xs truncate text-slate-600" x-text="log.message"></td>
                                <td class="p-3.5">
                                    <span :class="{
                                            'bg-emerald-100 text-emerald-700': log.status === 'sent' || log.status === 'delivered',
                                            'bg-rose-100 text-rose-700': log.status === 'failed',
                                            'bg-amber-100 text-amber-700': log.status === 'pending'
                                        }" class="px-2.5 py-1 rounded-full font-bold text-[10px] uppercase" x-text="log.status"></span>
                                </td>
                                <td class="p-3.5 text-right space-x-1">
                                    <button @click="resendLog(log)" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-[11px] font-bold border border-emerald-200">
                                        Kirim Ulang
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="!logData.data || logData.data.length === 0">
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 font-medium">
                                    Belum ada log pengiriman pesan WhatsApp yang ditemukan.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL: TAMBAH / EDIT PERANGKAT -->
    <div x-show="deviceModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="deviceModalOpen = false"></div>
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-bold text-slate-800" x-text="isEditDevice ? 'Edit Perangkat WhatsApp' : 'Tambah Perangkat WhatsApp Baru'"></h3>
                    <button @click="deviceModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="submitDeviceForm()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nama Perangkat / Sesi:</label>
                        <input type="text" x-model="deviceForm.name" required placeholder="Misal: Bot Kesiswaan Utama"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Nomor WhatsApp (Opsional):</label>
                        <input type="text" x-model="deviceForm.phone_number" placeholder="+62 812-xxxx-xxxx"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Provider Engine:</label>
                        <select x-model="deviceForm.provider" class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="fonnte">Fonnte Gateway</option>
                            <option value="wablas">Wablas API Gateway</option>
                            <option value="node_baileys">Node.js Baileys Local Engine</option>
                            <option value="custom_http">Custom HTTP Webhook Gateway</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Server URL (Opsional):</label>
                        <input type="url" x-model="deviceForm.server_url" placeholder="Node lokal: http://127.0.0.1:3001"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="mt-1 text-[11px] text-slate-400">Untuk Node.js Baileys di server yang sama, gunakan http://127.0.0.1:3001.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">API Key / Token:</label>
                        <input type="password" x-model="deviceForm.api_key" placeholder="Masukkan API Key / Token Provider"
                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <p class="mt-1 text-[11px] text-slate-400">Untuk Node.js lokal, biarkan kosong agar memakai WHATSAPP_GATEWAY_API_KEY dari server.</p>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_default" x-model="deviceForm.is_default" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <label for="is_default" class="text-xs font-bold text-slate-700">Jadikan Gateway Utama (Default)</label>
                    </div>

                    <div class="pt-4 flex justify-end space-x-2">
                        <button type="button" @click="deviceModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500">Simpan Perangkat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: QR CODE SCANNER -->
    <div x-show="qrModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" @click="closeQrModal()"></div>
            <div class="inline-block bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl z-10 space-y-6 relative border border-slate-100">
                <button @click="closeQrModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="text-center space-y-2">
                    <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-full">QR CODE PAIRING</span>
                    <h3 class="text-xl font-black text-slate-800">Hubungkan WhatsApp</h3>
                    <p class="text-xs text-slate-500">Buka aplikasi WhatsApp pada HP Anda > Perangkat Tertaut > Tautkan Perangkat lalu scan QR Code di bawah ini.</p>
                </div>

                <!-- QR pairing dari engine lokal -->
                <div class="bg-slate-50 rounded-2xl p-6 border-2 border-dashed border-emerald-300 flex flex-col items-center justify-center space-y-4">
                    <div class="bg-white p-4 rounded-xl shadow-md border border-slate-200 relative">
                        <template x-if="activeDeviceForQr && activeDeviceForQr.qr_code_data">
                            <img :src="activeDeviceForQr.qr_code_data" alt="QR Code penautan WhatsApp" class="w-48 h-48 rounded-lg">
                        </template>
                        <template x-if="!activeDeviceForQr || !activeDeviceForQr.qr_code_data">
                            <div class="w-48 h-48 rounded-lg bg-slate-100 flex items-center justify-center text-center p-5 text-xs font-semibold text-slate-500">
                                QR sedang disiapkan oleh WhatsApp Engine.
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-emerald-500/5 rounded-lg pointer-events-none"></div>
                    </div>

                    <div class="flex items-center space-x-2 text-xs font-mono text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Menunggu Scan WhatsApp...</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <button @click="checkActiveDeviceConnection()"
                        class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Periksa Status Koneksi</span>
                    </button>
                    <button @click="closeQrModal()" class="w-full py-2 text-xs text-slate-400 hover:text-slate-600">Tutup</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function whatsappGatewayApp() {
        return {
            activeTab: 'devices',
            loading: false,
            sendingTest: false,
            devices: @json($devices),
            stats: @json($stats),
            templateList: @json($templates),
            selectedTemplateKey: 'absensi_alpha',

            // Modals
            deviceModalOpen: false,
            isEditDevice: false,
            qrModalOpen: false,
            activeDeviceForQr: null,

            // Forms
            deviceForm: {
                id: null,
                name: '',
                phone_number: '',
                provider: 'node_baileys',
                server_url: 'http://127.0.0.1:3001',
                api_key: '',
                is_default: false,
            },

            testForm: {
                whatsapp_device_id: '',
                recipient: '',
                message: 'Halo! Ini adalah pesan uji coba dari WhatsApp Gateway SMK Telkom Lampung.',
            },
            qrPollTimer: null,

            logFilter: {
                search: '',
                status: '',
            },

            logData: {
                data: [],
            },

            toast: {
                show: false,
                message: '',
                type: 'success',
            },

            initData() {
                if (this.devices.length > 0) {
                    this.testForm.whatsapp_device_id = this.devices[0].id;
                }
                this.fetchLogs();
            },

            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 3500);
            },

            get previewText() {
                const found = this.templateList.find(t => t.event_key === this.selectedTemplateKey);
                if (!found || !found.template_text) return "Halo! Pesan notifikasi akan muncul di sini...";

                return found.template_text
                    .replace('{nama_siswa}', 'Ahmad Fadhil')
                    .replace('{kelas}', 'XI RPL 1')
                    .replace('{tanggal}', '23 Juli 2026')
                    .replace('{jam_tap}', '07:15 WIB')
                    .replace('{durasi_keterlambatan}', '15')
                    .replace('{alasan}', 'Sakit Demam')
                    .replace('{tanggal_panggilan}', '24 Juli 2026')
                    .replace('{jam_panggilan}', '09:00 WIB')
                    .replace('{perihal_panggilan}', 'Konsultasi Absensi');
            },

            currentTime() {
                const now = new Date();
                return now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            },

            formatDateTime(dateTimeStr) {
                if (!dateTimeStr) return '-';
                const d = new Date(dateTimeStr);
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' +
                       d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            },

            insertVariable(templateObj, varName) {
                templateObj.template_text += ` {${varName}}`;
            },

            async refreshDevicesData() {
                this.loading = true;
                try {
                    const res = await fetch("{{ route('super-admin.whatsapp-gateway.devices-data') }}");
                    const data = await res.json();
                    if (data.success) {
                        this.devices = data.devices;
                        this.stats = data.stats;
                        this.showToast('Data perangkat WhatsApp diperbarui.');
                    }
                } catch (e) {
                    this.showToast('Gagal menyinkronkan data perangkat.', 'error');
                } finally {
                    this.loading = false;
                }
            },

            openAddDeviceModal() {
                this.isEditDevice = false;
                this.deviceForm = {
                    id: null,
                    name: '',
                    phone_number: '',
                    provider: 'node_baileys',
                    server_url: 'http://127.0.0.1:3001',
                    api_key: '',
                    is_default: this.devices.length === 0,
                };
                this.deviceModalOpen = true;
            },

            openEditDeviceModal(device) {
                this.isEditDevice = true;
                this.deviceForm = {
                    id: device.id,
                    name: device.name,
                    phone_number: device.phone_number,
                    provider: device.provider,
                    server_url: device.server_url,
                    api_key: device.api_key,
                    is_default: device.is_default,
                };
                this.deviceModalOpen = true;
            },

            async submitDeviceForm() {
                const url = this.isEditDevice
                    ? `/super-admin/whatsapp-gateway/device/${this.deviceForm.id}`
                    : "{{ route('super-admin.whatsapp-gateway.device.store') }}";

                const method = this.isEditDevice ? 'PUT' : 'POST';

                try {
                    const res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.deviceForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.deviceModalOpen = false;
                        this.refreshDevicesData();
                    } else {
                        this.showToast(data.message || 'Gagal menyimpan perangkat.', 'error');
                    }
                } catch (e) {
                    this.showToast('Terjadi kesalahan koneksi server.', 'error');
                }
            },

            async deleteDevice(device) {
                if (!confirm(`Apakah Anda yakin ingin menghapus perangkat "${device.name}"?`)) return;

                try {
                    const res = await fetch(`/super-admin/whatsapp-gateway/device/${device.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.refreshDevicesData();
                    }
                } catch (e) {
                    this.showToast('Gagal menghapus perangkat.', 'error');
                }
            },

            async openQrModal(device) {
                if (this.qrPollTimer) {
                    clearInterval(this.qrPollTimer);
                    this.qrPollTimer = null;
                }
                this.activeDeviceForQr = device;
                this.qrModalOpen = true;
                try {
                    const res = await fetch(`/super-admin/whatsapp-gateway/device/${device.id}/qr`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.activeDeviceForQr = data.device;
                        await this.refreshQrDevice();
                        this.qrPollTimer = setInterval(() => this.refreshQrDevice(), 2000);
                    } else {
                        this.showToast(data.message || 'Gagal memulai WhatsApp engine.', 'error');
                    }
                } catch (e) {
                    this.showToast('WhatsApp engine tidak dapat dihubungi.', 'error');
                }
            },

            async checkActiveDeviceConnection() {
                if (!this.activeDeviceForQr) return;
                try {
                    const res = await fetch(`/super-admin/whatsapp-gateway/device/${this.activeDeviceForQr.id}/connect`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.closeQrModal();
                        this.refreshDevicesData();
                    } else {
                        this.showToast(data.message, 'error');
                    }
                } catch (e) {
                    this.showToast('Gagal memeriksa status sesi.', 'error');
                }
            },

            async refreshQrDevice() {
                if (!this.activeDeviceForQr || !this.qrModalOpen) return;
                try {
                    const res = await fetch("{{ route('super-admin.whatsapp-gateway.devices-data') }}");
                    const data = await res.json();
                    const current = data.devices?.find(device => device.id === this.activeDeviceForQr.id);
                    if (!current) return;
                    this.activeDeviceForQr = current;
                    this.devices = data.devices;
                    this.stats = data.stats;
                    if (current.status === 'connected') {
                        this.showToast('Perangkat WhatsApp berhasil terhubung.');
                        this.closeQrModal();
                    }
                } catch (e) {}
            },

            closeQrModal() {
                this.qrModalOpen = false;
                if (this.qrPollTimer) {
                    clearInterval(this.qrPollTimer);
                    this.qrPollTimer = null;
                }
            },

            async disconnectDevice(device) {
                if (!confirm(`Putuskan koneksi WhatsApp "${device.name}"?`)) return;
                try {
                    const res = await fetch(`/super-admin/whatsapp-gateway/device/${device.id}/disconnect`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.refreshDevicesData();
                    }
                } catch (e) {
                    this.showToast('Gagal memutuskan koneksi.', 'error');
                }
            },

            async saveTemplates() {
                this.loading = true;
                const payload = {};
                this.templateList.forEach(t => {
                    payload[t.event_key] = {
                        title: t.title,
                        is_enabled: t.is_enabled,
                        template_text: t.template_text
                    };
                });

                try {
                    const res = await fetch("{{ route('super-admin.whatsapp-gateway.templates.save') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ templates: payload })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                    }
                } catch (e) {
                    this.showToast('Gagal menyimpan template.', 'error');
                } finally {
                    this.loading = false;
                }
            },

            async sendTestMessage() {
                this.sendingTest = true;
                try {
                    const res = await fetch("{{ route('super-admin.whatsapp-gateway.send-test') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(this.testForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.fetchLogs();
                        this.refreshDevicesData();
                    } else {
                        this.showToast(data.message || 'Gagal mengirim pesan uji coba.', 'error');
                    }
                } catch (e) {
                    this.showToast('Koneksi bermasalah saat mengirim pesan.', 'error');
                } finally {
                    this.sendingTest = false;
                }
            },

            async fetchLogs() {
                const params = new URLSearchParams({
                    search: this.logFilter.search,
                    status: this.logFilter.status,
                });
                try {
                    const res = await fetch(`{{ route('super-admin.whatsapp-gateway.logs') }}?${params.toString()}`);
                    const data = await res.json();
                    if (data.success) {
                        this.logData = data.logs;
                    }
                } catch (e) {}
            },

            async resendLog(log) {
                try {
                    const res = await fetch(`/super-admin/whatsapp-gateway/logs/${log.id}/resend`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.fetchLogs();
                    }
                } catch (e) {
                    this.showToast('Gagal mengirim ulang pesan.', 'error');
                }
            },

            async clearLogs() {
                if (!confirm('Apakah Anda yakin ingin menghapus seluruh riwayat log WhatsApp?')) return;
                try {
                    const res = await fetch("{{ route('super-admin.whatsapp-gateway.logs.clear') }}", {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showToast(data.message);
                        this.fetchLogs();
                    }
                } catch (e) {
                    this.showToast('Gagal membersihkan log.', 'error');
                }
            }
        };
    }
</script>
@endpush
</x-app-layout>
