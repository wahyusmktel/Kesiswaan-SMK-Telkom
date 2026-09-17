<section class="border-t border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50 p-5" data-friday-evaluation-resume>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-emerald-600">Bahan Presentasi Evaluasi Jumat</p>
            <h4 class="mt-1 text-lg font-black text-gray-950">Resume Rencana & Komitmen</h4>
            <p class="mt-1 text-xs text-gray-600">Perbandingan rencana Senin dengan progres terkini {{ $selectedUnit->name }}.</p>
        </div>
        <div class="flex flex-wrap items-start justify-end gap-2">
            <div class="max-w-xs text-right">
                <button
                    type="button"
                    @if($report->status !== 'draft' && $presentationAiReady)
                        @click="generatePresentation('{{ route('okr.weekly.presentation', $report) }}', '{{ csrf_token() }}')"
                    @endif
                    @disabled($report->status === 'draft' || ! $presentationAiReady)
                    class="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-xs font-black text-white shadow-sm hover:from-violet-500 hover:to-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <svg x-show="presentationLoading" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                    <svg x-show="!presentationLoading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    <span x-text="presentationLoading ? 'Stella sedang menyusun slide...' : 'Hasilkan Slide Presentasi'"></span>
                </button>
                @if($report->status === 'draft')
                    <p class="mt-1 text-[10px] font-semibold text-amber-700">Kirim Evaluasi Jumat terlebih dahulu.</p>
                @elseif(! $presentationAiReady)
                    <p class="mt-1 text-[10px] font-semibold text-amber-700">Stella AI belum aktif atau belum dikonfigurasi.</p>
                @else
                    <p class="mt-1 text-[10px] text-gray-500">Menghasilkan PowerPoint yang dapat diedit.</p>
                @endif
                <p x-show="presentationError" x-cloak x-text="presentationError" class="mt-2 text-[10px] font-bold text-red-600"></p>
            </div>
            <div class="rounded-md border border-emerald-200 bg-white px-4 py-2 text-center"><p class="text-[9px] font-black uppercase text-gray-400">Capaian</p><p class="mt-1 text-xl font-black text-emerald-700">{{ $reportProgress }}%</p></div>
            <div class="rounded-md border border-emerald-200 bg-white px-4 py-2 text-center"><p class="text-[9px] font-black uppercase text-gray-400">Tercapai</p><p class="mt-1 text-xl font-black text-gray-900">{{ $reportCompleted }}/{{ $report->items->count() }}</p></div>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-lg border border-emerald-100 bg-white shadow-sm">
        <div class="grid grid-cols-[42px_minmax(0,1.5fr)_minmax(0,1fr)_90px] gap-3 bg-emerald-900 px-4 py-3 text-[9px] font-black uppercase tracking-wide text-emerald-50">
            <span>No.</span><span>Rencana & Target</span><span>Pembaruan Terakhir</span><span class="text-right">Progres</span>
        </div>
        @foreach($report->items as $item)
            @php($latestProgress = $item->progressUpdates->first())
            <div class="grid grid-cols-[42px_minmax(0,1.5fr)_minmax(0,1fr)_90px] gap-3 border-t border-gray-100 px-4 py-4 text-xs first:border-t-0">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 font-black text-emerald-800">{{ $item->priority_order }}</span>
                <div><p class="font-black leading-5 text-gray-950">{{ $item->commitment }}</p><p class="mt-1 text-[10px] leading-4 text-gray-500">Target: {{ $item->measurable_target }}</p></div>
                <div><p class="font-semibold leading-5 text-gray-700">{{ $latestProgress?->note ?? ($item->actual_result ?: 'Belum ada pembaruan progres.') }}</p>@if($latestProgress?->blockers || $item->blockers)<p class="mt-1 text-[10px] font-bold text-red-600">Kendala: {{ $latestProgress?->blockers ?? $item->blockers }}</p>@endif</div>
                <div class="text-right"><p class="text-xl font-black {{ $item->final_status === 'blocked' ? 'text-red-600' : 'text-emerald-700' }}">{{ (float) $item->completion_percent }}%</p><span class="text-[9px] font-black uppercase text-gray-500">{{ $itemStatusLabels[$item->final_status] }}</span></div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 grid gap-3 lg:grid-cols-2">
        <div class="rounded-md border border-blue-100 bg-blue-50 p-4"><p class="text-[9px] font-black uppercase tracking-wide text-blue-600">Fokus yang disepakati Senin</p><p class="mt-2 text-sm font-bold leading-6 text-blue-950">{{ $report->weekly_focus }}</p></div>
        <div class="rounded-md border {{ $reportBlocked > 0 ? 'border-red-100 bg-red-50' : 'border-gray-200 bg-gray-50' }} p-4"><p class="text-[9px] font-black uppercase tracking-wide {{ $reportBlocked > 0 ? 'text-red-600' : 'text-gray-500' }}">Catatan untuk forum Jumat</p><p class="mt-2 text-sm leading-6 text-gray-800">{{ $reportBlocked > 0 ? $reportBlocked.' komitmen membutuhkan pembahasan kendala dan keputusan tindak lanjut.' : 'Tidak ada komitmen yang berstatus terhambat. Konfirmasikan hasil akhir dan tindak lanjut pekan depan.' }}</p></div>
    </div>
</section>
