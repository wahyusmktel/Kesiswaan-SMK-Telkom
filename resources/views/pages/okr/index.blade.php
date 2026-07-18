<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-black text-gray-900">Manajemen OKR Sekolah</h2>
            <p class="mt-1 text-sm text-gray-500">Target sekolah, rencana unit, dan evaluasi capaian dalam satu ruang kerja.</p>
        </div>
    </x-slot>

    @php
        $keyResults = $period->objectives->flatMap->keyResults;
        $allVisiblePlans = $keyResults->flatMap(function ($keyResult) {
            return $keyResult->plans->flatMap(function ($annual) {
                return collect([$annual])->concat($annual->children)->concat($annual->children->flatMap->children);
            });
        });
        $parentPlans = $allVisiblePlans->whereIn('level', ['annual', 'monthly'])->values()->map(fn ($plan) => [
            'id' => $plan->id,
            'okr_key_result_id' => $plan->okr_key_result_id,
            'okr_unit_id' => $plan->okr_unit_id,
            'level' => $plan->level,
            'title' => $plan->title,
        ]);
    @endphp

    <div class="min-h-screen bg-gray-50 py-6" x-data="okrWorkspace()" x-init="initCharts()">
        <div class="mx-auto max-w-[1600px] space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="flex items-center gap-3 border-l-4 border-emerald-500 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    <svg class="h-5 w-5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="border-l-4 border-red-500 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-black">Data belum dapat disimpan.</p>
                    <p class="mt-1">{{ $errors->first() }}</p>
                </div>
            @endif

            <section class="border border-indigo-100 bg-indigo-50 px-5 py-4">
                <div class="flex gap-3">
                    <div class="flex h-9 w-9 flex-none items-center justify-center rounded-md bg-indigo-600 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-indigo-950">Alur kerja OKR setiap unit</h3>
                        <p class="mt-1 text-xs leading-5 text-indigo-800">Pilih key result sekolah, susun target tahunan, turunkan menjadi target bulanan dan mingguan, lalu catat evaluasinya secara rutin. Progres target anak otomatis membentuk progres target induk. Stella AI dapat membantu menyusun rumusan target yang terukur.</p>
                    </div>
                </div>
            </section>

            <section class="border-y border-gray-200 bg-white px-5 py-4">
                <form method="GET" class="grid gap-3 md:grid-cols-[minmax(220px,1fr)_minmax(220px,1fr)_auto_auto_auto] md:items-end">
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black uppercase text-gray-500">Periode / Arsip</span>
                        <select name="period_id" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($periods as $periodOption)
                                <option value="{{ $periodOption->id }}" @selected($periodOption->id === $period->id)>
                                    {{ $periodOption->title }} · {{ ucfirst($periodOption->status) }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black uppercase text-gray-500">Unit Kerja</span>
                        <select name="unit_id" class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" @selected($unit->id === $selectedUnit?->id)>
                                    {{ $unit->name }}{{ in_array($unit->id, $editableUnitIds, true) ? ' · dapat dikelola' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <button class="inline-flex h-[42px] items-center justify-center rounded-md bg-indigo-600 px-4 text-sm font-bold text-white hover:bg-indigo-500">Tampilkan</button>
                    <a href="{{ route('okr.report', ['period' => $period, 'unit_id' => $selectedUnit?->id]) }}"
                        class="inline-flex h-[42px] items-center justify-center gap-2 rounded-md bg-gray-900 px-4 text-sm font-bold text-white hover:bg-gray-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF Unit
                    </a>
                    <a href="{{ route('okr.report', $period) }}" class="inline-flex h-[42px] items-center justify-center rounded-md border border-gray-300 bg-white px-4 text-sm font-bold text-gray-700 hover:bg-gray-50">PDF Global</a>
                </form>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach([
                    ['label' => 'Progres Unit', 'value' => $stats['progress'].'%', 'tone' => 'text-indigo-700', 'sub' => $selectedUnit?->name],
                    ['label' => 'Total Rencana', 'value' => $stats['total'], 'tone' => 'text-gray-900', 'sub' => 'Tahunan, bulanan, mingguan'],
                    ['label' => 'Target Tercapai', 'value' => $stats['completed'], 'tone' => 'text-emerald-700', 'sub' => 'Selesai dan terverifikasi'],
                    ['label' => 'Perlu Perhatian', 'value' => $stats['at_risk'], 'tone' => 'text-amber-700', 'sub' => 'Berstatus berisiko'],
                ] as $card)
                    <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-[11px] font-black uppercase text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-black {{ $card['tone'] }}">{{ $card['value'] }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $card['sub'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="grid gap-5 xl:grid-cols-[1.35fr_.65fr]">
                <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h3 class="font-black text-gray-900">Perbandingan Capaian Unit</h3>
                        <p class="text-xs text-gray-500">Rata-rata progres target tahunan pada periode terpilih.</p>
                    </div>
                    <div class="h-72"><canvas x-ref="unitChart"></canvas></div>
                </div>
                <div class="rounded-md border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h3 class="font-black text-gray-900">Tren Evaluasi {{ $selectedUnit?->name }}</h3>
                        <p class="text-xs text-gray-500">Rata-rata progres yang dicatat enam bulan terakhir.</p>
                    </div>
                    <div class="h-72"><canvas x-ref="trendChart"></canvas></div>
                </div>
            </section>

            <section class="bg-white border-y border-gray-200">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div>
                        <h3 class="font-black text-gray-900">Matriks Objektif dan Key Result</h3>
                        <p class="mt-1 text-xs text-gray-500">{{ $period->title }} · Unit {{ $selectedUnit?->name }}</p>
                    </div>
                    @if($canManageAll)
                        <div class="flex gap-2">
                            <button type="button" @click="showObjectiveModal = true" class="rounded-md border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Tambah Objektif</button>
                            <button type="button" @click="showPeriodModal = true" class="rounded-md bg-gray-900 px-3 py-2 text-xs font-bold text-white hover:bg-gray-800">Kelola Periode</button>
                        </div>
                    @endif
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($period->objectives as $objective)
                        <article x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="flex w-full items-center gap-4 bg-gray-50 px-5 py-4 text-left hover:bg-gray-100">
                                <span class="flex h-9 min-w-12 items-center justify-center rounded bg-gray-900 px-2 text-xs font-black text-white">{{ $objective->code }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-black text-gray-900">{{ $objective->title }}</span>
                                    <span class="mt-0.5 block text-[11px] text-gray-500">{{ $objective->keyResults->count() }} key result</span>
                                </span>
                                <svg class="h-5 w-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" x-collapse>
                                @if($canManageAll)
                                    <div class="flex flex-wrap items-center justify-end gap-4 border-t border-gray-200 bg-white px-5 py-2">
                                        <details class="relative">
                                            <summary class="cursor-pointer list-none text-xs font-bold text-gray-500 hover:text-gray-800">Edit Objektif</summary>
                                            <div class="absolute right-0 z-20 mt-2 w-[min(90vw,480px)] border border-gray-200 bg-white p-4 shadow-xl">
                                                <form method="POST" action="{{ route('okr.objectives.update', $objective) }}" class="space-y-3">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="grid gap-3 sm:grid-cols-[90px_1fr]">
                                                        <input name="code" value="{{ $objective->code }}" required class="rounded-md border-gray-300 text-sm">
                                                        <input name="title" value="{{ $objective->title }}" required class="rounded-md border-gray-300 text-sm">
                                                    </div>
                                                    <button class="rounded-md bg-gray-900 px-3 py-2 text-xs font-bold text-white">Simpan Perubahan</button>
                                                </form>
                                                <form method="POST" action="{{ route('okr.objectives.destroy', $objective) }}" class="mt-3 border-t border-gray-100 pt-3" onsubmit="return confirm('Hapus objektif beserta seluruh key result dan rencana unitnya?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-xs font-bold text-red-600 hover:underline">Hapus objektif</button>
                                                </form>
                                            </div>
                                        </details>
                                        <button type="button" @click="openKeyResult({{ $objective->id }})" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Tambah Key Result
                                        </button>
                                    </div>
                                @endif
                                @foreach($objective->keyResults as $keyResult)
                                    <div class="border-t border-gray-200 first:border-t-0">
                                        <div class="grid gap-4 px-5 py-4 lg:grid-cols-[minmax(0,1fr)_180px_auto] lg:items-center">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded bg-red-50 px-2 py-1 text-[10px] font-black text-red-700">{{ $keyResult->code }}</span>
                                                    <h4 class="text-sm font-black text-gray-900">{{ $keyResult->title }}</h4>
                                                </div>
                                                <p class="mt-1.5 text-xs leading-5 text-gray-500">{{ $keyResult->description }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <span class="block font-semibold">Target sekolah</span>
                                                <strong class="mt-1 block text-sm text-gray-900">
                                                    {{ $keyResult->metric_type === 'currency' ? 'Rp'.number_format((float) $keyResult->target_value, 0, ',', '.') : number_format((float) $keyResult->target_value, 0, ',', '.').' '.$keyResult->metric_unit }}
                                                </strong>
                                            </div>
                                            @if($canEditSelected)
                                                <button type="button" @click="openPlan({{ $keyResult->id }})" class="inline-flex items-center justify-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-xs font-bold text-white hover:bg-indigo-500">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    Tambah Rencana
                                                </button>
                                            @endif
                                        </div>

                                        @if($canManageAll)
                                            <details class="border-t border-dashed border-gray-200 bg-gray-50/60 px-5 py-2">
                                                <summary class="cursor-pointer list-none text-[11px] font-bold text-gray-500 hover:text-gray-800">Edit struktur {{ $keyResult->code }}</summary>
                                                <div class="py-3">
                                                    <form method="POST" action="{{ route('okr.key-results.update', $keyResult) }}" class="grid gap-3 md:grid-cols-4">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input name="code" value="{{ $keyResult->code }}" required class="rounded-md border-gray-300 text-xs">
                                                        <input name="title" value="{{ $keyResult->title }}" required class="rounded-md border-gray-300 text-xs md:col-span-2">
                                                        <select name="metric_type" class="rounded-md border-gray-300 text-xs">
                                                            @foreach(['percentage' => 'Persentase', 'number' => 'Jumlah', 'currency' => 'Rupiah', 'boolean' => 'Tercapai/tidak'] as $value => $label)
                                                                <option value="{{ $value }}" @selected($keyResult->metric_type === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <textarea name="description" rows="2" required class="rounded-md border-gray-300 text-xs md:col-span-4">{{ $keyResult->description }}</textarea>
                                                        <input type="number" name="baseline_value" value="{{ $keyResult->baseline_value }}" min="0" step="0.01" required class="rounded-md border-gray-300 text-xs" title="Nilai awal">
                                                        <input type="number" name="target_value" value="{{ $keyResult->target_value }}" min="0" step="0.01" required class="rounded-md border-gray-300 text-xs" title="Nilai target">
                                                        <input name="metric_unit" value="{{ $keyResult->metric_unit }}" required class="rounded-md border-gray-300 text-xs" title="Satuan">
                                                        <input type="number" name="weight" value="{{ $keyResult->weight }}" min="0.01" step="0.01" required class="rounded-md border-gray-300 text-xs" title="Bobot">
                                                        <input type="date" name="due_date" value="{{ $keyResult->due_date?->format('Y-m-d') }}" class="rounded-md border-gray-300 text-xs">
                                                        <div class="flex items-center gap-4 md:col-span-3">
                                                            <button class="rounded-md bg-gray-900 px-3 py-2 text-xs font-bold text-white">Simpan Key Result</button>
                                                        </div>
                                                    </form>
                                                    <form method="POST" action="{{ route('okr.key-results.destroy', $keyResult) }}" class="mt-3" onsubmit="return confirm('Hapus key result beserta seluruh rencana unitnya?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="text-xs font-bold text-red-600 hover:underline">Hapus {{ $keyResult->code }}</button>
                                                    </form>
                                                </div>
                                            </details>
                                        @endif

                                        @forelse($keyResult->plans as $plan)
                                            @include('pages.okr._plan-row', ['plan' => $plan, 'depth' => 0, 'canEditSelected' => $canEditSelected])
                                        @empty
                                            <div class="border-t border-dashed border-gray-200 px-5 py-4 text-center text-xs text-gray-400">
                                                Unit ini belum menjabarkan rencana untuk {{ $keyResult->code }}.
                                            </div>
                                        @endforelse
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-16 text-center text-sm text-gray-500">Belum ada objektif pada periode ini.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-md border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h3 class="font-black text-gray-900">Aktivitas Evaluasi Terbaru</h3>
                    <p class="text-xs text-gray-500">Jejak pembaruan progres dari seluruh unit.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-500">
                            <tr><th class="px-5 py-3 text-left">Tanggal</th><th class="px-5 py-3 text-left">Unit</th><th class="px-5 py-3 text-left">Target</th><th class="px-5 py-3 text-left">Evaluasi</th><th class="px-5 py-3 text-right">Progres</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentUpdates as $update)
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-3 text-xs text-gray-500">{{ $update->recorded_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-700">{{ $update->plan->unit->name }}</td>
                                    <td class="px-5 py-3"><span class="font-semibold text-gray-900">{{ $update->plan->title }}</span><span class="block text-[10px] text-gray-400">{{ $update->plan->keyResult->code }}</span></td>
                                    <td class="max-w-md px-5 py-3 text-xs text-gray-500">
                                        {{ $update->note }}
                                        @if($update->evidence_path)
                                            <a href="{{ asset('storage/'.$update->evidence_path) }}" target="_blank" rel="noopener" class="mt-1 block font-bold text-indigo-600 hover:underline">Lihat bukti pendukung</a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right text-xs font-black text-gray-900">{{ number_format((float) $update->progress_after, 0) }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">Belum ada evaluasi yang dicatat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Modal Rencana -->
        <div x-cloak x-show="showPlanModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-950/60 p-4" @keydown.escape.window="showPlanModal = false">
            <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-md bg-white shadow-2xl" @click.outside="showPlanModal = false">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
                    <div><h3 class="font-black text-gray-900" x-text="plan.id ? 'Edit Rencana OKR' : 'Tambah Rencana OKR'"></h3><p class="text-xs text-gray-500">Turunkan key result menjadi langkah yang terukur.</p></div>
                    <button @click="showPlanModal = false" class="text-gray-400 hover:text-gray-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form method="POST" :action="planAction" class="space-y-5 p-6">
                    @csrf
                    <input type="hidden" name="_method" :value="plan.id ? 'PATCH' : 'POST'">
                    <input type="hidden" name="okr_key_result_id" x-model="plan.okr_key_result_id">
                    <input type="hidden" name="okr_unit_id" value="{{ $selectedUnit?->id }}">
                    <div class="grid gap-4 md:grid-cols-2">
                        <label><span class="mb-1.5 block text-xs font-bold text-gray-700">Tingkat target</span><select name="level" x-model="plan.level" @change="plan.parent_id = ''" class="w-full rounded-md border-gray-300 text-sm"><option value="annual">Tahunan</option><option value="monthly">Bulanan</option><option value="weekly">Mingguan</option></select></label>
                        <label x-show="plan.level !== 'annual'"><span class="mb-1.5 block text-xs font-bold text-gray-700">Target induk</span><select name="parent_id" x-model="plan.parent_id" class="w-full rounded-md border-gray-300 text-sm"><option value="">Pilih target induk</option><template x-for="parent in eligibleParents" :key="parent.id"><option :value="parent.id" x-text="parent.title"></option></template></select></label>
                    </div>

                    @if($aiReady)
                        <div class="border border-violet-200 bg-violet-50 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div><p class="text-xs font-black uppercase text-violet-700">Bantuan Stella AI</p><p class="mt-1 text-xs text-violet-600">Tuliskan konteks unit, lalu pilih rekomendasi yang paling sesuai.</p></div>
                                <button type="button" @click="generateAi()" :disabled="aiLoading" class="rounded-md bg-violet-600 px-3 py-2 text-xs font-bold text-white disabled:opacity-50" x-text="aiLoading ? 'Menyusun...' : 'Buat Rekomendasi'"></button>
                            </div>
                            <textarea x-model="aiContext" rows="2" class="mt-3 w-full rounded-md border-violet-200 text-xs" placeholder="Contoh: fokus bulan ini adalah persiapan audit dan kelengkapan eviden..."></textarea>
                            <p x-show="aiError" class="mt-2 text-xs font-semibold text-red-600" x-text="aiError"></p>
                            <div x-show="aiSuggestions.length" class="mt-3 divide-y divide-violet-100 border border-violet-100 bg-white">
                                <template x-for="(suggestion, index) in aiSuggestions" :key="index">
                                    <button type="button" @click="applySuggestion(suggestion)" class="block w-full px-3 py-3 text-left hover:bg-violet-50"><span class="block text-xs font-black text-gray-900" x-text="suggestion.title"></span><span class="mt-1 block text-[11px] leading-4 text-gray-500" x-text="suggestion.description"></span></button>
                                </template>
                            </div>
                        </div>
                    @endif

                    <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Nama target / aktivitas</span><input name="title" x-model="plan.title" required class="w-full rounded-md border-gray-300 text-sm"></label>
                    <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Rincian pelaksanaan</span><textarea name="description" x-model="plan.description" rows="3" class="w-full rounded-md border-gray-300 text-sm"></textarea></label>
                    <label class="block"><span class="mb-1.5 block text-xs font-bold text-gray-700">Indikator keberhasilan</span><textarea name="success_indicator" x-model="plan.success_indicator" rows="2" class="w-full rounded-md border-gray-300 text-sm"></textarea></label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label><span class="mb-1.5 block text-xs font-bold text-gray-700">Penanggung jawab</span><select name="owner_id" x-model="plan.owner_id" class="w-full rounded-md border-gray-300 text-sm"><option value="">Belum ditentukan</option>@foreach($owners as $owner)<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endforeach</select></label>
                        <label><span class="mb-1.5 block text-xs font-bold text-gray-700">Bobot</span><input type="number" name="weight" x-model="plan.weight" min="0.01" step="0.01" required class="w-full rounded-md border-gray-300 text-sm"></label>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold text-gray-700">Mulai</span><input type="date" name="starts_at" x-model="plan.starts_at" class="w-full rounded-md border-gray-300 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-gray-700">Tenggat</span><input type="date" name="ends_at" x-model="plan.ends_at" class="w-full rounded-md border-gray-300 text-sm"></label></div>
                    <div class="grid gap-4 md:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold text-gray-700">Nilai target</span><input type="number" name="target_value" x-model="plan.target_value" min="0" step="0.01" class="w-full rounded-md border-gray-300 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-gray-700">Satuan</span><input name="metric_unit" x-model="plan.metric_unit" maxlength="40" class="w-full rounded-md border-gray-300 text-sm" placeholder="%, dokumen, kegiatan..."></label></div>
                    <div class="flex justify-end gap-2 border-t border-gray-200 pt-4"><button type="button" @click="showPlanModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Batal</button><button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Simpan Rencana</button></div>
                </form>
            </div>
        </div>

        <!-- Modal Evaluasi -->
        <div x-cloak x-show="showProgressModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-950/60 p-4">
            <div class="w-full max-w-xl rounded-md bg-white shadow-2xl" @click.outside="showProgressModal = false">
                <div class="border-b border-gray-200 px-6 py-4"><h3 class="font-black text-gray-900">Catat Evaluasi Target</h3><p class="mt-1 text-xs text-gray-500" x-text="progress.title"></p></div>
                <form method="POST" :action="`{{ url('/okr-sekolah/plans') }}/${progress.id}/progress`" enctype="multipart/form-data" class="space-y-4 p-6">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold">Progres (%)</span><input type="number" name="progress_percent" x-model="progress.progress_percent" min="0" max="100" step="1" required class="w-full rounded-md border-gray-300"></label><label><span class="mb-1.5 block text-xs font-bold">Status</span><select name="status" x-model="progress.status" class="w-full rounded-md border-gray-300"><option value="not_started">Belum dimulai</option><option value="in_progress">Berjalan</option><option value="at_risk">Berisiko</option><option value="completed">Tercapai</option><option value="cancelled">Dibatalkan</option></select></label></div>
                    <div class="grid gap-4 sm:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold">Realisasi angka</span><input type="number" name="current_value" x-model="progress.current_value" min="0" step="0.01" class="w-full rounded-md border-gray-300"></label><label><span class="mb-1.5 block text-xs font-bold">Tanggal evaluasi</span><input type="date" name="recorded_at" value="{{ now()->format('Y-m-d') }}" required class="w-full rounded-md border-gray-300"></label></div>
                    <label class="block"><span class="mb-1.5 block text-xs font-bold">Catatan hasil, kendala, dan tindak lanjut</span><textarea name="note" rows="4" required class="w-full rounded-md border-gray-300" placeholder="Jelaskan capaian, kendala, bukti, dan langkah berikutnya..."></textarea></label>
                    <label class="block"><span class="mb-1.5 block text-xs font-bold">Bukti pendukung (opsional)</span><input type="file" name="evidence" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" class="block w-full rounded-md border border-gray-300 p-2 text-xs"><span class="mt-1 block text-[10px] text-gray-400">PDF, gambar, Word, atau Excel. Maksimal 10 MB.</span></label>
                    <div class="flex justify-end gap-2 border-t border-gray-200 pt-4"><button type="button" @click="showProgressModal = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-bold text-white">Simpan Evaluasi</button></div>
                </form>
            </div>
        </div>

        @if($canManageAll)
            <!-- Modal Objektif -->
            <div x-cloak x-show="showObjectiveModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-950/60 p-4">
                <div class="w-full max-w-xl rounded-md bg-white p-6 shadow-2xl" @click.outside="showObjectiveModal = false">
                    <h3 class="font-black text-gray-900">Tambah Objektif Sekolah</h3>
                    <form method="POST" action="{{ route('okr.objectives.store') }}" class="mt-5 space-y-4">@csrf<input type="hidden" name="okr_period_id" value="{{ $period->id }}"><label class="block"><span class="mb-1 block text-xs font-bold">Kode</span><input name="code" required placeholder="O6" class="w-full rounded-md border-gray-300"></label><label class="block"><span class="mb-1 block text-xs font-bold">Rumusan objektif</span><textarea name="title" rows="3" required class="w-full rounded-md border-gray-300"></textarea></label><div class="flex justify-end gap-2"><button type="button" @click="showObjectiveModal = false" class="rounded-md border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-bold text-white">Tambah</button></div></form>
                </div>
            </div>

            <!-- Modal Key Result -->
            <div x-cloak x-show="showKeyResultModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-950/60 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-md bg-white p-6 shadow-2xl" @click.outside="showKeyResultModal = false">
                    <h3 class="font-black text-gray-900">Tambah Key Result Sekolah</h3>
                    <form method="POST" action="{{ route('okr.key-results.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">@csrf
                        <input type="hidden" name="okr_objective_id" x-model="keyResultObjectiveId">
                        <label><span class="mb-1 block text-xs font-bold">Kode</span><input name="code" required placeholder="KR 1.4" class="w-full rounded-md border-gray-300"></label>
                        <label><span class="mb-1 block text-xs font-bold">Nama key result</span><input name="title" required class="w-full rounded-md border-gray-300"></label>
                        <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold">Rumusan target</span><textarea name="description" rows="3" required class="w-full rounded-md border-gray-300"></textarea></label>
                        <label><span class="mb-1 block text-xs font-bold">Jenis ukuran</span><select name="metric_type" class="w-full rounded-md border-gray-300"><option value="percentage">Persentase</option><option value="number">Jumlah</option><option value="currency">Rupiah</option><option value="boolean">Tercapai / tidak</option></select></label>
                        <label><span class="mb-1 block text-xs font-bold">Satuan</span><input name="metric_unit" value="%" required class="w-full rounded-md border-gray-300"></label>
                        <label><span class="mb-1 block text-xs font-bold">Nilai awal</span><input type="number" name="baseline_value" value="0" min="0" step="0.01" required class="w-full rounded-md border-gray-300"></label>
                        <label><span class="mb-1 block text-xs font-bold">Nilai target</span><input type="number" name="target_value" value="100" min="0" step="0.01" required class="w-full rounded-md border-gray-300"></label>
                        <label><span class="mb-1 block text-xs font-bold">Bobot</span><input type="number" name="weight" value="1" min="0.01" step="0.01" required class="w-full rounded-md border-gray-300"></label>
                        <label><span class="mb-1 block text-xs font-bold">Tenggat</span><input type="date" name="due_date" value="{{ $period->ends_at?->format('Y-m-d') }}" class="w-full rounded-md border-gray-300"></label>
                        <div class="flex justify-end gap-2 sm:col-span-2"><button type="button" @click="showKeyResultModal = false" class="rounded-md border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Tambah Key Result</button></div>
                    </form>
                </div>
            </div>

            <!-- Modal Periode -->
            <div x-cloak x-show="showPeriodModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-gray-950/60 p-4">
                <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-md bg-white p-6 shadow-2xl" @click.outside="showPeriodModal = false">
                    <h3 class="font-black text-gray-900">Buat Periode OKR Baru</h3>
                    <p class="mt-1 text-xs text-gray-500">Periode lama tetap tersimpan sebagai arsip laporan.</p>
                    <form method="POST" action="{{ route('okr.periods.store') }}" class="mt-5 grid gap-4 sm:grid-cols-2">@csrf
                        <label><span class="mb-1 block text-xs font-bold">Tahun pelajaran</span><select name="tahun_pelajaran_id" class="w-full rounded-md border-gray-300"><option value="">Tanpa relasi</option>@foreach($academicYears as $year)<option value="{{ $year->id }}">{{ $year->tahun }} · {{ $year->semester }}</option>@endforeach</select></label>
                        <label><span class="mb-1 block text-xs font-bold">Status</span><select name="status" class="w-full rounded-md border-gray-300"><option value="draft">Draft</option><option value="active">Aktif</option><option value="closed">Ditutup</option></select></label>
                        <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold">Nama periode</span><input name="title" required class="w-full rounded-md border-gray-300" placeholder="Program Kerja OKR 2027/2028"></label>
                        <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold">Visi / arah strategis</span><textarea name="vision" rows="3" class="w-full rounded-md border-gray-300"></textarea></label>
                        <label><span class="mb-1 block text-xs font-bold">Mulai</span><input type="date" name="starts_at" class="w-full rounded-md border-gray-300"></label><label><span class="mb-1 block text-xs font-bold">Selesai</span><input type="date" name="ends_at" class="w-full rounded-md border-gray-300"></label>
                        <div class="flex justify-end gap-2 sm:col-span-2"><button type="button" @click="showPeriodModal = false" class="rounded-md border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-bold text-white">Buat Periode</button></div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function okrWorkspace() {
            return {
                showPlanModal: false,
                showProgressModal: false,
                showObjectiveModal: false,
                showKeyResultModal: false,
                showPeriodModal: false,
                keyResultObjectiveId: null,
                aiLoading: false,
                aiContext: '',
                aiError: '',
                aiSuggestions: [],
                parents: @js($parentPlans),
                plan: {},
                progress: {},
                get planAction() {
                    return this.plan.id ? `{{ url('/okr-sekolah/plans') }}/${this.plan.id}` : '{{ route('okr.plans.store') }}';
                },
                get eligibleParents() {
                    const requiredLevel = this.plan.level === 'monthly' ? 'annual' : 'monthly';
                    return this.parents.filter(parent =>
                        parent.okr_key_result_id == this.plan.okr_key_result_id
                        && parent.okr_unit_id == {{ (int) $selectedUnit?->id }}
                        && parent.level === requiredLevel
                        && parent.id != this.plan.id
                    );
                },
                emptyPlan(keyResultId) {
                    return { id: null, okr_key_result_id: keyResultId, parent_id: '', owner_id: '', level: 'annual', title: '', description: '', starts_at: '{{ $period->starts_at?->format('Y-m-d') }}', ends_at: '{{ $period->ends_at?->format('Y-m-d') }}', target_value: 100, metric_unit: '%', weight: 1, success_indicator: '' };
                },
                openPlan(keyResultId, existing = null) {
                    this.plan = existing ? { ...existing, parent_id: existing.parent_id || '', owner_id: existing.owner_id || '' } : this.emptyPlan(keyResultId);
                    this.aiSuggestions = [];
                    this.aiError = '';
                    this.aiContext = '';
                    this.showPlanModal = true;
                },
                openProgress(plan) {
                    this.progress = { ...plan, current_value: plan.current_value || '' };
                    this.showProgressModal = true;
                },
                openKeyResult(objectiveId) {
                    this.keyResultObjectiveId = objectiveId;
                    this.showKeyResultModal = true;
                },
                async generateAi() {
                    this.aiLoading = true;
                    this.aiError = '';
                    this.aiSuggestions = [];
                    try {
                        const response = await fetch('{{ route('okr.ai.suggest') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({ okr_key_result_id: this.plan.okr_key_result_id, okr_unit_id: {{ (int) $selectedUnit?->id }}, level: this.plan.level, context: this.aiContext }),
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Rekomendasi belum dapat dibuat.');
                        this.aiSuggestions = data.suggestions || [];
                    } catch (error) {
                        this.aiError = error.message;
                    } finally {
                        this.aiLoading = false;
                    }
                },
                applySuggestion(suggestion) {
                    this.plan.title = suggestion.title;
                    this.plan.description = suggestion.description;
                    this.plan.success_indicator = suggestion.success_indicator;
                    this.plan.target_value = suggestion.target_value;
                    this.plan.metric_unit = suggestion.metric_unit;
                    this.aiSuggestions = [];
                },
                initCharts() {
                    this.$nextTick(() => {
                        if (typeof Chart === 'undefined') return;
                        new Chart(this.$refs.unitChart, {
                            type: 'bar',
                            data: { labels: @js($unitStats->pluck('name')), datasets: [{ data: @js($unitStats->pluck('progress')), backgroundColor: ['#4f46e5','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#14b8a6','#64748b','#111827'], borderRadius: 4, maxBarThickness: 42 }] },
                            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 900 }, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, ticks: { callback: value => value + '%' }, grid: { color: '#f3f4f6' } }, x: { grid: { display: false }, ticks: { maxRotation: 35, minRotation: 0, font: { size: 10 } } } } }
                        });
                        new Chart(this.$refs.trendChart, {
                            type: 'line',
                            data: { labels: @js($monthlyTrend->pluck('label')), datasets: [{ data: @js($monthlyTrend->pluck('progress')), borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,.1)', fill: true, tension: .38, pointRadius: 3, pointBackgroundColor: '#fff', pointBorderWidth: 2 }] },
                            options: { responsive: true, maintainAspectRatio: false, animation: { duration: 1000 }, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, ticks: { callback: value => value + '%' }, grid: { color: '#f3f4f6' } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
                        });
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
