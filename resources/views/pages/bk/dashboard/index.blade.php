<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-layout-dashboard">
                    <rect width="7" height="9" x="3" y="3" rx="1" />
                    <rect width="7" height="5" x="14" y="3" rx="1" />
                    <rect width="7" height="9" x="14" y="12" rx="1" />
                    <rect width="7" height="5" x="3" y="16" rx="1" />
                </svg>
            </div>
            <span>{{ __('Dashboard Guru BK') }}</span>
        </h2>
    </x-slot>

    <div class="py-8 space-y-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Welcome Banner -->
            <div
                class="relative overflow-hidden bg-gradient-to-br from-red-600 to-red-800 rounded-3xl p-8 text-white shadow-xl">
                <div class="relative z-10">
                    <h1 class="text-3xl font-extrabold mb-2">Selamat Datang Kembali, {{ Auth::user()->name }}! 👋</h1>
                    <p class="text-white/80 text-lg font-medium max-w-2xl">
                        Kelola bimbingan dan pembinaan siswa dengan lebih mudah hari ini. Berikut adalah ringkasan
                        aktivitas terbaru Anda.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <div
                            class="bg-white/20 backdrop-blur-md rounded-2xl px-4 py-2 flex items-center gap-2 border border-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-calendar text-white/90">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                            <span
                                class="text-sm font-bold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
                        </div>
                        <div
                            class="bg-white/20 backdrop-blur-md rounded-2xl px-4 py-2 flex items-center gap-2 border border-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-clock text-white/90">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span class="text-sm font-bold">{{ \Carbon\Carbon::now()->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Abstract Background Elements -->
                <div
                    class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-64 h-64 bg-red-400/20 rounded-full blur-3xl">
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Siswa -->
                <div
                    class="bg-white p-6 rounded-3xl shadow-soft border border-gray-100 flex items-center gap-5 group hover:border-red-500/30 transition-all duration-300">
                    <div
                        class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-users">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-widest font-black mb-1">Total Siswa</p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_siswa'] }}</p>
                    </div>
                </div>

                <!-- Pending Konsultasi -->
                <div
                    class="bg-white p-6 rounded-3xl shadow-soft border border-gray-100 flex items-center gap-5 group hover:border-red-500/30 transition-all duration-300">
                    <div
                        class="w-14 h-14 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center group-hover:bg-yellow-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-message-square-more">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            <path d="M8 9h.01" />
                            <path d="M12 9h.01" />
                            <path d="M16 9h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-widest font-black mb-1 text-nowrap">
                            Pending Konsultasi</p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ $stats['pending_konsultasi'] }}</p>
                    </div>
                </div>

                <!-- Total Pembinaan -->
                <div
                    class="bg-white p-6 rounded-3xl shadow-soft border border-gray-100 flex items-center gap-5 group hover:border-red-500/30 transition-all duration-300">
                    <div
                        class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-shield-check">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-widest font-black mb-1">Total Pembinaan
                        </p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_pembinaan'] }}</p>
                    </div>
                </div>

                <!-- Izin Hari Ini -->
                <div
                    class="bg-white p-6 rounded-3xl shadow-soft border border-gray-100 flex items-center gap-5 group hover:border-red-500/30 transition-all duration-300">
                    <div
                        class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-clock-rewind">
                            <path d="M22 12A10 10 0 1 1 12 2a10 10 0 0 1 9.1 6" />
                            <path d="M12 6v6l4 2" />
                            <path d="M16 8h5V3" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-400 uppercase tracking-widest font-black mb-1">Izin Hari Ini</p>
                        <p class="text-2xl font-black text-gray-900 leading-none">{{ $stats['izin_hari_ini'] }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Konsultasi Section -->
                <div class="space-y-8">
                    <!-- Konsultasi -->
                    <div
                        class="bg-white rounded-[2rem] shadow-soft p-8 border border-gray-50 overflow-hidden relative group transition-all duration-500 hover:shadow-xl">
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-calendar-lines">
                                        <path d="M8 2v4" />
                                        <path d="M16 2v4" />
                                        <rect width="18" height="18" x="3" y="4" rx="2" />
                                        <path d="M3 10h18" />
                                        <path d="M8 14h8" />
                                        <path d="M8 18h5" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Permintaan Konsultasi</h3>
                            </div>
                            <a href="{{ route('bk.konsultasi.index') }}"
                                class="text-xs font-black text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-all uppercase tracking-wider">Lihat
                                Semua</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-2 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Siswa</th>
                                        <th
                                            class="px-4 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Perihal</th>
                                        <th
                                            class="px-2 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @forelse($recent_konsultasi as $rk)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-2 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 uppercase">
                                                        {{ substr($rk->siswa->nama_lengkap ?? 'S', 0, 1) }}
                                                    </div>
                                                    <span
                                                        class="text-sm font-bold text-gray-900 line-clamp-1">{{ $rk->siswa->nama_lengkap }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500 font-medium line-clamp-1">
                                                {{ $rk->perihal }}
                                            </td>
                                            <td class="px-2 py-4 text-center">
                                                @if($rk->status == 'pending')
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-yellow-50 text-yellow-600 border border-yellow-100">Pending</span>
                                                @elseif($rk->status == 'disetujui' || $rk->status == 'selesai')
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-100">{{ $rk->status }}</span>
                                                @else
                                                    <span
                                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-100">{{ $rk->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-12 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div
                                                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                            class="lucide lucude-clipboard-x text-gray-300">
                                                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                                            <path
                                                                d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                                            <path d="m15 11-6 6" />
                                                            <path d="m9 11 6 6" />
                                                        </svg>
                                                    </div>
                                                    <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">
                                                        Belum ada permintaan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Grafik Keterlambatan -->
                    <div
                        class="bg-white rounded-[2rem] shadow-soft p-8 border border-gray-50 overflow-hidden relative group transition-all duration-500 hover:shadow-xl">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-area-chart">
                                    <path d="M3 3v18h18" />
                                    <path d="M7 12v5" />
                                    <path d="M11 9v8" />
                                    <path d="M15 13v4" />
                                    <path d="M19 7v10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900">Analisa Keterlambatan</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">30 Hari Terakhir
                                    (Senin - Jumat)</p>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="latenessChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Chat & Today Section -->
                <div class="space-y-8">
                    <!-- Percakapan Chat -->
                    <div
                        class="bg-white rounded-[2rem] shadow-soft p-8 border border-gray-50 overflow-hidden relative group transition-all duration-500 hover:shadow-xl">
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-messages-square">
                                        <path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z" />
                                        <path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Percakapan Terbaru</h3>
                            </div>
                            <a href="{{ route('bk.chat.index') }}"
                                class="text-xs font-black text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-all uppercase tracking-wider">Lihat
                                Semua</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($chat_rooms as $room)
                                <a href="{{ route('bk.chat.index', ['room_id' => $room->id]) }}"
                                    class="flex items-center gap-5 p-4 rounded-2xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100 group/chat">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-white font-black text-base relative shadow-lg shadow-red-200 group-hover/chat:scale-105 transition-transform duration-300">
                                        {{ substr($room->siswa->name ?? 'S', 0, 1) }}
                                        @if($room->unread_count > 0)
                                            <div
                                                class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-red-600 border-4 border-white rounded-full flex items-center justify-center text-[10px] font-black">
                                                {{ $room->unread_count }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline mb-1">
                                            <h4
                                                class="text-sm font-black text-gray-900 truncate group-hover/chat:text-red-600 transition-colors">
                                                {{ $room->siswa->name }}
                                            </h4>
                                            <span
                                                class="text-[10px] text-gray-400 font-black uppercase tracking-tighter bg-gray-100 px-2 py-0.5 rounded-lg">
                                                {{ $room->last_message_at ? \Carbon\Carbon::parse($room->last_message_at)->diffForHumans() : '' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate font-medium flex items-center gap-1">
                                            @if($room->messages->first()?->sender_id == Auth::id())
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-check-check text-indigo-500">
                                                    <path d="M18 6 7 17l-5-5" />
                                                    <path d="m22 10-7.5 7.5L13 16" />
                                                </svg>
                                            @endif
                                            {{ $room->messages->first()?->message ?? ($room->messages->first()?->type != 'text' ? '[Lampiran]' : 'Belum ada pesan') }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 text-gray-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-message-circle-off">
                                                <path
                                                    d="M20.5 14.99a1 1 0 0 1-1.07 1.25l-2.01-.2a9 9 0 0 1-6.7-1.71L7 17l.64-3.13a9 9 0 0 1-1.29-8.48" />
                                                <path d="m2 2 20 20" />
                                                <path d="M11.53 5.92a9 9 0 0 1 9.4 6.78Z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-400 font-bold uppercase tracking-widest">Belum ada
                                            percakapan</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Keterlambatan Hari Ini -->
                    <div
                        class="bg-white rounded-[2rem] shadow-soft p-8 border border-gray-50 overflow-hidden relative group transition-all duration-500 hover:shadow-xl">
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-user-minus">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <line x1="22" x2="16" y1="11" y2="11" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-black text-gray-900">Keterlambatan Hari Ini</h3>
                            </div>
                            <span
                                class="text-[10px] font-black bg-orange-50 text-orange-600 px-3 py-1 rounded-lg uppercase tracking-widest">{{ count($today_lateness) }}
                                Siswa</span>
                        </div>
                        <div class="space-y-4">
                            @forelse($today_lateness as $tl)
                                <div
                                    class="flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 transition-all border border-gray-50 group/late">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm uppercase">
                                        {{ substr($tl->siswa->nama_lengkap ?? 'S', 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-black text-gray-900 truncate">{{ $tl->siswa->nama_lengkap }}
                                        </h4>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">
                                            {{ $tl->siswa->rombels->first()?->nama_rombel ?? 'Tanpa Kelas' }} •
                                            {{ \Carbon\Carbon::parse($tl->waktu_dicatat_security)->format('H:i') }} WIB
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="text-[10px] font-black text-red-600 bg-red-50 px-2 py-1 rounded-lg uppercase">Terlambat</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center">
                                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Tidak ada data hari
                                        ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('latenessChart').getContext('2d');
                const latenessChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($lateness_labels) !!},
                        datasets: [{
                            label: 'Siswa Terlambat',
                            data: {!! json_encode($lateness_data) !!},
                            fill: true,
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(239, 68, 68)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1f2937',
                                padding: 12,
                                titleFont: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                cornerRadius: 8,
                                displayColors: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 10,
                                        weight: 'bold'
                                    },
                                    color: '#9ca3af'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10,
                                        weight: 'bold'
                                    },
                                    color: '#9ca3af'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
    @push('styles')
        <style>
            .shadow-soft {
                box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.01) !important;
            }

            @keyframes subtle-float {
                0% {
                    transform: translateY(0px) rotate(0deg);
                }

                50% {
                    transform: translateY(-10px) rotate(1deg);
                }

                100% {
                    transform: translateY(0px) rotate(0deg);
                }
            }
        </style>
    @endpush
</x-app-layout>