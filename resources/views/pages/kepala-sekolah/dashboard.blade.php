<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold leading-tight text-gray-800">Dashboard Eksekutif Kepala Sekolah</h2>
                <p class="text-xs font-medium text-gray-500">Monitoring lintas unit dalam mode baca-saja</p>
            </div>
            <span class="text-xs text-gray-400">Diperbarui {{ $generatedAt->translatedFormat('d M Y, H:i') }}</span>
        </div>
    </x-slot>

    @php
        $toneClasses = [
            'blue' => ['icon' => 'bg-blue-50 text-blue-600', 'dot' => 'bg-blue-500'],
            'indigo' => ['icon' => 'bg-indigo-50 text-indigo-600', 'dot' => 'bg-indigo-500'],
            'violet' => ['icon' => 'bg-violet-50 text-violet-600', 'dot' => 'bg-violet-500'],
            'amber' => ['icon' => 'bg-amber-50 text-amber-600', 'dot' => 'bg-amber-500'],
            'rose' => ['icon' => 'bg-rose-50 text-rose-600', 'dot' => 'bg-rose-500'],
            'emerald' => ['icon' => 'bg-emerald-50 text-emerald-600', 'dot' => 'bg-emerald-500'],
            'cyan' => ['icon' => 'bg-cyan-50 text-cyan-600', 'dot' => 'bg-cyan-500'],
            'orange' => ['icon' => 'bg-orange-50 text-orange-600', 'dot' => 'bg-orange-500'],
        ];
    @endphp

    <div class="w-full py-6">
        <div class="w-full space-y-8 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-red-950 p-7 text-white shadow-xl sm:p-9">
                <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-red-500/20 blur-3xl"></div>
                <div class="relative grid gap-8 lg:grid-cols-[1.4fr_1fr] lg:items-end">
                    <div>
                        <div class="mb-3 flex items-center gap-2">
                            <span class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-red-100">Executive Overview</span>
                            <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                        </div>
                        <h3 class="text-3xl font-black sm:text-4xl">Selamat datang, {{ auth()->user()->name }}</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300">
                            Pantau capaian program dan indikator operasional seluruh unit sekolah dari satu tempat. Dashboard ini tidak menyediakan aksi perubahan data.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('okr.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-xs font-black text-slate-900 transition hover:bg-red-50">Lihat Detail OKR</a>
                            <a href="{{ route('tanda-tangan.index') }}" class="rounded-xl border border-white/20 bg-white/5 px-4 py-2.5 text-xs font-black text-white transition hover:bg-white/10">Tanda Tangan Digital</a>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 divide-x divide-white/10 rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
                        <div class="px-3 text-center">
                            <strong class="block text-2xl font-black">{{ number_format($overallProgress, 1) }}%</strong>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Progres OKR</span>
                        </div>
                        <div class="px-3 text-center">
                            <strong class="block text-2xl font-black text-emerald-400">{{ $completedCount }}</strong>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tuntas</span>
                        </div>
                        <div class="px-3 text-center">
                            <strong class="block text-2xl font-black text-amber-400">{{ $atRiskCount }}</strong>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Perlu Atensi</span>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="mb-4 flex items-end justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Indikator Operasional</h3>
                        <p class="text-sm text-gray-500">Ringkasan kondisi terkini dari setiap layanan sekolah.</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($highlights as $highlight)
                        @php($tone = $toneClasses[$highlight['tone']])
                        <article class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-[0.16em] text-gray-400">{{ $highlight['unit'] }}</p>
                                    <p class="mt-2 text-3xl font-black text-gray-900">{{ number_format($highlight['metric']) }}</p>
                                    <p class="mt-1 text-xs font-bold text-gray-600">{{ $highlight['label'] }}</p>
                                </div>
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $tone['icon'] }}">
                                    <span class="h-3 w-3 rounded-full {{ $tone['dot'] }}"></span>
                                </div>
                            </div>
                            <p class="mt-4 border-t border-gray-100 pt-3 text-xs text-gray-500">{{ $highlight['detail'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.35fr_0.85fr]">
                <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 p-6">
                        <div>
                            <h3 class="font-black text-gray-900">Perkembangan Seluruh Unit</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ $activePeriod?->title ?? 'Belum ada periode OKR aktif' }}</p>
                        </div>
                        <a href="{{ route('okr.index') }}" class="text-xs font-black text-red-600 hover:text-red-700">Buka laporan lengkap →</a>
                    </div>
                    <div class="grid gap-4 p-6 md:grid-cols-2">
                        @forelse ($unitProgress as $unit)
                            <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $unit['name'] }}</p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $unit['code'] }} · {{ $unit['total'] }} target utama</p>
                                    </div>
                                    <span class="text-lg font-black {{ $unit['at_risk'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ number_format($unit['progress'], 1) }}%</span>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div class="h-full rounded-full {{ $unit['at_risk'] > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ min(100, max(0, $unit['progress'])) }}%"></div>
                                </div>
                                <div class="mt-3 flex items-center justify-between text-[11px] text-gray-500">
                                    <span>{{ $unit['completed'] }} selesai · {{ $unit['at_risk'] }} berisiko</span>
                                    <span>{{ $unit['last_update'] ? $unit['last_update']->diffForHumans() : 'Belum diperbarui' }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="md:col-span-2 rounded-2xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500">Belum ada unit aktif untuk ditampilkan.</div>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 p-6">
                        <h3 class="font-black text-gray-900">Pembaruan Terbaru</h3>
                        <p class="mt-1 text-xs text-gray-500">Aktivitas evaluasi OKR lintas unit.</p>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($recentUpdates as $update)
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-bold text-gray-900">{{ $update->plan?->keyResult?->title ?? 'Target tidak tersedia' }}</p>
                                        <p class="mt-1 text-xs text-gray-500">{{ $update->plan?->unit?->name ?? 'Unit tidak tersedia' }} · {{ $update->user?->name ?? 'Sistem' }}</p>
                                    </div>
                                    <span class="shrink-0 rounded-lg bg-slate-100 px-2 py-1 text-xs font-black text-slate-700">{{ number_format((float) $update->progress_after, 1) }}%</span>
                                </div>
                                <p class="mt-2 line-clamp-2 text-xs leading-relaxed text-gray-500">{{ $update->note }}</p>
                                <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">{{ $update->recorded_at?->translatedFormat('d M Y') }}</p>
                            </div>
                        @empty
                            <div class="p-10 text-center text-sm text-gray-500">Belum ada pembaruan progres pada periode aktif.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                <strong>Catatan akses:</strong> halaman ini bersifat monitoring. Pengelolaan pengguna, role, permission, konfigurasi aplikasi, database, dan sistem tetap khusus Super Admin.
            </div>
        </div>
    </div>
</x-app-layout>
