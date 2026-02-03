<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Super Admin</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome & Quick Stats --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Welcome Card --}}
                <div
                    class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 text-white shadow-xl">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Super Admin</span>
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        </div>
                        <h3 class="text-3xl font-black mb-2">Halo, {{ Auth::user()->name }}!</h3>
                        <p class="text-slate-400 font-medium">Sistem Monitoring & Manajemen Pengguna</p>

                        <div class="mt-8 flex flex-wrap gap-8 border-t border-white/10 pt-6">
                            <div>
                                <span class="text-3xl font-black block">{{ $stats['total_users'] }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total
                                    Users</span>
                            </div>
                            <div class="border-l border-white/10 pl-8">
                                <span
                                    class="text-3xl font-black block text-emerald-400">{{ $activeUsers->count() }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Aktif
                                    Sekarang</span>
                            </div>
                            <div class="border-l border-white/10 pl-8">
                                <span class="text-3xl font-black block">{{ $stats['active_sessions'] }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total
                                    Sessions</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Stats Cards --}}
                <div
                    class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
                    <div>
                        <div
                            class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Siswa</h4>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_siswa']) }}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <span
                            class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded-lg">{{ $stats['total_rombel'] }}
                            Rombel</span>
                    </div>
                </div>

                <div
                    class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
                    <div>
                        <div
                            class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Total Guru</h4>
                        <p class="text-2xl font-black text-gray-900">{{ number_format($stats['total_guru']) }}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <span
                            class="text-xs font-bold px-2 py-1 bg-emerald-100 text-emerald-700 rounded-lg">{{ $stats['total_kelas'] }}
                            Kelas</span>
                    </div>
                </div>
            </div>

            {{-- Happiness Meter Section --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-black text-gray-900 text-lg">Analisa Kesejahteraan Warga</h4>
                    <span class="text-xs font-bold px-3 py-1 bg-pink-100 text-pink-700 rounded-full">Live Mood
                        Tracking</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Happiness Meter Summary Card --}}
                    <div
                        class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm group hover:border-pink-200 transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-pink-50 text-pink-600 rounded-2xl flex items-center justify-center">
                                <span class="text-2xl animate-bounce">💖</span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Mood Hari
                                Ini</span>
                        </div>
                        <div>
                            <h4 class="text-gray-900 font-black text-2xl">{{ $happinessSummary['total'] }} <span
                                    class="text-sm font-medium text-slate-500">Responden</span></h4>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-pink-500"
                                        style="width: {{ ($happinessSummary['average'] / 5) * 100 }}%"></div>
                                </div>
                                <span
                                    class="text-xs font-bold text-pink-600">{{ $happinessSummary['average'] }}/5</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dominant Mood Card --}}
                    <div
                        class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:border-amber-200 transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                                <span class="text-2xl">
                                    @php
                                        $emojis = [
                                            'sangat_bahagia' => '😄',
                                            'bahagia' => '🙂',
                                            'netral' => '😐',
                                            'sedih' => '😢',
                                            'sangat_sedih' => '😭',
                                        ];
                                        $labels = [
                                            'sangat_bahagia' => 'Sangat Bahagia',
                                            'bahagia' => 'Bahagia',
                                            'netral' => 'Netral',
                                            'sedih' => 'Sedih',
                                            'sangat_sedih' => 'Sangat Sedih',
                                        ];
                                    @endphp
                                    {{ $emojis[$happinessSummary['dominant']] ?? '❓' }}
                                </span>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Dominasi
                                Mood</span>
                        </div>
                        <div>
                            <h4 class="text-gray-900 font-black text-xl uppercase tracking-tighter">
                                {{ $labels[$happinessSummary['dominant']] ?? $happinessSummary['dominant'] }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-1">Status emosional mayoritas warga hari ini</p>
                        </div>
                    </div>

                    {{-- Happiness Action Card --}}
                    <div
                        class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden group transition-all hover:scale-[1.02]">
                        <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <h4 class="font-bold text-lg mb-1">Analisa Kebahagiaan</h4>
                            <p class="text-xs text-rose-100 mb-4">Pantau kesejahteraan mental <br>warga SMK Telkom</p>
                            <a href="#"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs font-bold backdrop-blur-sm transition-all">
                                Lihat Detail Laporan
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Users & Login Trend --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Active Users Now --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Pengguna Aktif</h4>
                            <p class="text-gray-500 text-xs">Online dalam 5 menit terakhir</p>
                        </div>
                        <span
                            class="text-xs font-bold px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full flex items-center gap-1">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            {{ $activeUsers->count() }} Online
                        </span>
                    </div>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($activeUsers as $user)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" class="w-10 h-10 rounded-full object-cover" alt="">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-black text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->last_activity_formatted }}</p>
                                </div>
                                <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 italic">Tidak ada pengguna aktif.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Login Trend Chart --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Tren Aktivitas Login</h4>
                            <p class="text-gray-500 text-xs">7 hari terakhir</p>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="loginTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Users by Role & Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Users by Role Chart --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Distribusi Pengguna</h4>
                            <p class="text-gray-500 text-xs">Berdasarkan role/peran</p>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="usersByRoleChart"></canvas>
                    </div>
                </div>

                {{-- Recent Login Activity --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Aktivitas Login Terbaru</h4>
                            <p class="text-gray-500 text-xs">Log session pengguna</p>
                        </div>
                    </div>
                    <div class="space-y-3 max-h-72 overflow-y-auto">
                        @forelse($recentLogins->take(8) as $login)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all">
                                @if($login->avatar)
                                    <img src="{{ $login->avatar }}" class="w-10 h-10 rounded-full object-cover" alt="">
                                @else
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-black text-sm">
                                        {{ substr($login->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 truncate">{{ $login->name }}</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span>{{ $login->browser }}</span>
                                        <span>•</span>
                                        <span>{{ $login->ip_address }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-400">{{ $login->last_activity_formatted }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 italic">Tidak ada data login.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- User Activity Feed --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="font-black text-gray-900 text-lg">Aktivitas Terbaru</h4>
                        <p class="text-gray-500 text-xs">Aktivitas pengguna di sistem</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 bg-blue-100 text-blue-700 rounded-full">Live
                        Feed</span>
                </div>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    @forelse($userActivityFeed as $activity)
                        <div
                            class="flex items-start gap-4 p-4 rounded-2xl bg-gradient-to-r from-gray-50 to-white border border-gray-50 hover:border-gray-100 transition-all">
                            @php
                                $typeIcons = [
                                    'keterlambatan' => ['icon' => '🕐', 'color' => 'bg-amber-100 text-amber-700'],
                                    'perizinan' => ['icon' => '📝', 'color' => 'bg-blue-100 text-blue-600'],
                                    'backup' => ['icon' => '💾', 'color' => 'bg-emerald-100 text-emerald-600'],
                                    'restore' => ['icon' => '🔄', 'color' => 'bg-purple-100 text-purple-600'],
                                    'izin_keluar' => ['icon' => '🚪', 'color' => 'bg-cyan-100 text-cyan-600'],
                                ];
                                $icon = $typeIcons[$activity->type] ?? ['icon' => '📌', 'color' => 'bg-gray-100 text-gray-600'];
                            @endphp
                            <div
                                class="w-10 h-10 rounded-xl {{ $icon['color'] }} flex items-center justify-center text-lg shrink-0">
                                {{ $icon['icon'] }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900">
                                    <span class="font-bold">{{ $activity->user_name }}</span>
                                    <span class="text-gray-600">{{ $activity->description }}</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ $activity->created_at_formatted }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 italic">Belum ada aktivitas terbaru.</div>
                    @endforelse
                </div>
            </div>

            {{-- Database Activities --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="font-black text-gray-900 text-lg">Aktivitas Database</h4>
                        <p class="text-gray-500 text-xs">Log backup & restore database</p>
                    </div>
                    <a href="{{ route('kesiswaan.database.index') }}"
                        class="text-xs font-bold text-red-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr
                                class="text-left text-xs font-bold uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                <th class="pb-4">Tipe</th>
                                <th class="pb-4">File</th>
                                <th class="pb-4">Status</th>
                                <th class="pb-4">Pengguna</th>
                                <th class="pb-4">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentActivities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3">
                                        @php
                                            $typeColors = [
                                                'backup' => 'bg-emerald-100 text-emerald-700',
                                                'restore' => 'bg-blue-100 text-blue-700',
                                            ];
                                            $color = $typeColors[$activity->type] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span
                                            class="text-xs font-bold px-2 py-1 rounded-lg {{ $color }} uppercase">{{ $activity->type }}</span>
                                    </td>
                                    <td class="py-3 text-sm text-gray-900 font-medium">{{ $activity->filename ?? '-' }}
                                    </td>
                                    <td class="py-3">
                                        @if($activity->status === 'success')
                                            <span
                                                class="text-xs font-bold px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700">Success</span>
                                        @else
                                            <span
                                                class="text-xs font-bold px-2 py-1 rounded-lg bg-red-100 text-red-700">Failed</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-sm text-gray-500">{{ $activity->user->name ?? 'System' }}</td>
                                    <td class="py-3 text-sm text-gray-400">{{ $activity->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-400 italic">Tidak ada aktivitas
                                        database.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
                Chart.defaults.color = '#94a3b8';

                // Login Trend Chart
                const trendData = @json($loginTrend);
                new Chart(document.getElementById('loginTrendChart'), {
                    type: 'line',
                    data: {
                        labels: trendData.map(d => d.date),
                        datasets: [{
                            label: 'Login Activity',
                            data: trendData.map(d => d.count),
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3b82f6',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Users by Role Chart
                const roleData = @json($usersByRole);
                const roleColors = ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#8b5cf6', '#ec4899', '#14b8a6'];
                new Chart(document.getElementById('usersByRoleChart'), {
                    type: 'doughnut',
                    data: {
                        labels: roleData.map(r => r.role),
                        datasets: [{
                            data: roleData.map(r => r.total),
                            backgroundColor: roleColors.slice(0, roleData.length),
                            borderWidth: 0,
                            hoverOffset: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'right', labels: { usePointStyle: true, padding: 15, font: { size: 11 } } }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>