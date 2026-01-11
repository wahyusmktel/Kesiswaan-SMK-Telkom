<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
                    {{ __('Analisa & Aktifitas Coaching') }}
                </h2>
                <p class="text-sm text-gray-500 font-medium mt-1">
                    Analisa penyebab keterlambatan dan efektivitas intervensi pembinaan.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Analytics Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Coached --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between overflow-hidden relative">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Coached / Breakdown</p>
                        <h3 class="text-4xl font-black text-gray-900">{{ $totalCoached }}</h3>
                        <div class="flex gap-3 mt-2">
                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">BK: {{ $stats['total_bk'] }}</span>
                            <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">Wali: {{ $stats['total_wali'] }}</span>
                        </div>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-5">
                        <svg class="w-32 h-32 text-gray-900" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                    </div>
                </div>

                {{-- Effectiveness Rate --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between overflow-hidden relative">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tingkat Efektivitas</p>
                        <h3 class="text-4xl font-black text-green-600">{{ $effectivenessRate }}%</h3>
                        <p class="text-xs text-green-600/70 mt-2 font-medium italic">Strategi pembinaan berhasil</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-5">
                        <svg class="w-32 h-32 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                    </div>
                </div>

                {{-- Active Issue --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between overflow-hidden relative text-amber-600">
                    <div class="relative z-10">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Menunggu Review</p>
                        <h3 class="text-4xl font-black">{{ $activities->whereIn('status', ['pendampingan_wali_kelas', 'pembinaan_bk'])->count() }}</h3>
                        <p class="text-xs text-amber-600/70 mt-2 font-medium italic">Coaching belum dilaksanakan</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-5">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M11 15h2v2h-2zm0-8h2v6h-2zm1-5C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                    </div>
                </div>
            </div>

            {{-- Routine & Causes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Routine Stats --}}
                <div class="bg-indigo-900 rounded-3xl p-8 shadow-sm border border-indigo-800 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h4 class="text-xs font-black text-indigo-200 uppercase tracking-widest mb-6">Analisa Rutinitas Pagi (BK)</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-[8px] font-black text-indigo-300 uppercase tracking-tighter mb-2">Jam Bangun</p>
                                <p class="text-2xl font-black">{{ $routines['avg_wake'] }}</p>
                            </div>
                            <div class="text-center border-x border-indigo-800">
                                <p class="text-[8px] font-black text-indigo-300 uppercase tracking-tighter mb-2">Jam Berangkat</p>
                                <p class="text-2xl font-black">{{ $routines['avg_depart'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[8px] font-black text-indigo-300 uppercase tracking-tighter mb-2">Durasi Jalan</p>
                                <p class="text-2xl font-black">{{ $routines['avg_travel'] }} <span class="text-[10px]">min</span></p>
                            </div>
                        </div>
                        <p class="text-[10px] text-indigo-300 mt-6 font-medium italic">* Data diambil dari sesi pendalaman Guru BK</p>
                    </div>
                    <div class="absolute -right-10 -bottom-10 opacity-10">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                    </div>
                </div>

                {{-- Common Causes --}}
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6">Analisa GROW (Wali Kelas)</h4>
                    <div class="space-y-4">
                        @forelse($recentGrow as $coaching)
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-black text-purple-600 uppercase tracking-widest">{{ $coaching->keterlambatan?->siswa?->nama_lengkap ?? 'Unknown' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold">{{ $coaching->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Rencana Aksi:</p>
                                    <p class="text-xs text-gray-700 italic font-medium leading-relaxed">"{{ Str::limit($coaching->rencana_aksi, 80) }}"</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm text-gray-400 italic">Belum ada data analisa tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Activity List --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h4 class="text-sm font-black text-gray-900 uppercase tracking-widest">Daftar Aktifitas Pembinaan</h4>
                        <p class="text-xs text-gray-500 font-medium mt-1">Log lengkap intervensi kedisiplinan siswa.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Siswa & Kelas</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Terakhir</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahap Mentoring</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($activities as $activity)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-900 leading-none mb-1">{{ $activity->siswa->nama_lengkap }}</span>
                                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">NIS: {{ $activity->siswa->nis }} • {{ $activity->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($activity->status == 'selesai')
                                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100">Selesai</span>
                                        @elseif($activity->status == 'pembinaan_bk')
                                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">Guru BK</span>
                                        @else
                                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-100 italic">Wali Kelas</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-sm font-medium text-gray-600">
                                        {{ $activity->waktu_pembinaan_bk ? 'Pembinaan Guru BK' : ($activity->waktu_pendampingan_wali_kelas ? 'Sesi Coaching GROW' : 'Sedang Diproses') }}
                                    </td>
                                    <td class="px-8 py-6 text-right space-x-2">
                                        <a href="{{ route('monitoring-keterlambatan.show', $activity->id) }}" class="inline-flex items-center justify-center p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @if($activity->status == 'selesai' || $activity->status == 'pembinaan_bk')
                                            @if($activity->coaching)
                                                <a href="{{ route('wali-kelas.keterlambatan.coaching-pdf', $activity->id) }}" target="_blank" class="inline-flex items-center justify-center p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Cetak GROW PDF">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                </a>
                                            @endif
                                            @if($activity->bkCoaching)
                                                <a href="{{ route('bk.keterlambatan.coaching-pdf', $activity->id) }}" target="_blank" class="inline-flex items-center justify-center p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Cetak Kontrak BK">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center">
                                        <p class="text-sm text-gray-400 italic">Belum ada aktifitas coaching yang dicatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50">
                    {{ $activities->links() }}
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('trendsChart').getContext('2d');
            const data = @json($trends);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(i => {
                        const [year, month] = i.month.split('-');
                        const date = new Date(year, month - 1);
                        return date.toLocaleString('default', { month: 'short' });
                    }),
                    datasets: [{
                        label: 'Jumlah Coaching',
                        data: data.map(i => i.total),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { display: false },
                            ticks: { font: { weight: 'bold' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold' } }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
