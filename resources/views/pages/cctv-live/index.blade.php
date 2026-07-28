<x-app-layout>
    <div class="mx-auto max-w-[1600px] space-y-5" data-cctv-live-root>
        <section class="rounded-lg bg-slate-950 p-6 text-white shadow-xl lg:flex lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-red-300">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-red-500"></span>
                    Pemantauan internal
                </div>
                <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">CCTV Live</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">Pilih kamera yang telah diberikan kepada akun Anda. Tayangan hanya untuk kepentingan operasional sekolah dan setiap akses tercatat.</p>
            </div>
            <div class="mt-4 rounded-md border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-xs leading-5 text-amber-100 lg:mt-0 lg:max-w-sm">
                Jangan merekam, menyebarkan, atau memperlihatkan tayangan kepada pihak yang tidak berkepentingan.
            </div>
        </section>

        <div class="grid gap-5 xl:grid-cols-[300px_minmax(0,1fr)]">
            <aside class="rounded-lg border border-slate-200 bg-white">
                <div class="border-b border-slate-200 p-4">
                    <h2 class="font-extrabold text-slate-900">Kamera Tersedia</h2>
                    <p class="mt-1 text-xs text-slate-500">{{ $cameras->count() }} kamera dapat diakses</p>
                </div>
                <div class="max-h-[650px] divide-y divide-slate-100 overflow-y-auto">
                    @forelse($cameras as $camera)
                            <button type="button" data-camera-button="{{ $camera->id }}" class="group flex w-full items-center gap-3 p-4 text-left transition hover:bg-slate-50">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-slate-100 text-slate-500 group-[.is-active]:bg-red-600 group-[.is-active]:text-white">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.5-2.5v9L15 14m-9 4h7a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold text-slate-800">{{ $camera->name }}</span>
                                    <span class="block truncate text-xs text-slate-500">{{ $camera->location ?: 'Lokasi tidak dicantumkan' }}</span>
                                </span>
                            </button>
                    @empty
                        <div class="p-5 text-center text-xs leading-5 text-slate-500">Belum ada kamera aktif. Tambahkan kamera melalui halaman Manajemen CCTV.</div>
                    @endforelse
                </div>
            </aside>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                    <div>
                        <h2 class="font-extrabold text-slate-900" data-player-title>Pilih kamera</h2>
                        <p class="text-xs text-slate-500" data-player-location>Menyiapkan tayangan...</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span data-live-status class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Siap</span>
                        <button type="button" data-reload-camera title="Muat ulang tayangan" class="rounded-md border border-slate-200 p-2 text-slate-600 hover:bg-slate-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M20 20v-5h-5M5.6 15A7 7 0 0018 17.4M18.4 9A7 7 0 006 6.6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="relative aspect-video min-h-[260px] bg-black">
                    <video data-cctv-video class="h-full w-full bg-black object-contain" controls playsinline muted></video>
                    <div data-player-overlay class="absolute inset-0 flex items-center justify-center bg-slate-950 text-center text-white">
                        <div>
                            @if($cameras->isNotEmpty())
                                <span class="mx-auto block h-8 w-8 animate-spin rounded-full border-2 border-white/20 border-t-red-500"></span>
                                <p class="mt-4 text-sm font-bold" data-overlay-message>Menghubungkan ke kamera...</p>
                                <p class="mt-1 text-xs text-slate-400">Stream pertama dapat memerlukan beberapa detik.</p>
                            @else
                                <svg class="mx-auto h-10 w-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.5-2.5v9L15 14m-9 4h7a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <p class="mt-4 text-sm font-bold" data-overlay-message>Belum ada kamera aktif</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 border-t border-slate-200 bg-slate-50 p-5 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-500">Keterangan</p>
                        <p class="mt-1 text-sm text-slate-700" data-player-description>-</p>
                    </div>
                    <div class="md:text-right">
                        <p class="text-xs font-bold uppercase text-slate-500">Keamanan sesi</p>
                        <p class="mt-1 text-sm text-slate-700">Token tayangan diperbarui otomatis dan tidak menyimpan URL RTSP.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script type="application/json" id="cctv-live-data">@json($liveData)</script>

    @push('scripts')
        @vite('resources/js/cctv-live.js')
    @endpush
</x-app-layout>
