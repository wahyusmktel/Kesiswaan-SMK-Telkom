@php
    $levelLabels = ['annual' => 'Tahunan', 'monthly' => 'Bulanan', 'weekly' => 'Mingguan'];
    $levelColors = [
        'annual' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'monthly' => 'bg-sky-50 text-sky-700 border-sky-200',
        'weekly' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];
    $statusLabels = [
        'not_started' => 'Belum dimulai',
        'in_progress' => 'Berjalan',
        'at_risk' => 'Berisiko',
        'completed' => 'Tercapai',
        'cancelled' => 'Dibatalkan',
    ];
    $statusColors = [
        'not_started' => 'bg-gray-100 text-gray-600',
        'in_progress' => 'bg-blue-100 text-blue-700',
        'at_risk' => 'bg-amber-100 text-amber-800',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $planPayload = [
        'id' => $plan->id,
        'okr_key_result_id' => $plan->okr_key_result_id,
        'okr_unit_id' => $plan->okr_unit_id,
        'parent_id' => $plan->parent_id,
        'owner_id' => $plan->owner_id,
        'level' => $plan->level,
        'title' => $plan->title,
        'description' => $plan->description,
        'starts_at' => $plan->starts_at?->format('Y-m-d'),
        'ends_at' => $plan->ends_at?->format('Y-m-d'),
        'target_value' => $plan->target_value,
        'metric_unit' => $plan->metric_unit,
        'weight' => $plan->weight,
        'success_indicator' => $plan->success_indicator,
    ];
    $progressPayload = [
        'id' => $plan->id,
        'title' => $plan->title,
        'progress_percent' => (float) $plan->progress_percent,
        'current_value' => $plan->current_value,
        'status' => $plan->status,
        'latest_evaluation' => $plan->latest_evaluation,
    ];
@endphp

<div class="relative border-t border-gray-100 px-4 py-4 hover:bg-gray-50/70 transition-colors"
    style="padding-left: {{ 1 + ($depth * 1.5) }}rem">
    @if($depth > 0)
        <span class="absolute top-0 bottom-0 border-l border-dashed border-gray-300" style="left: {{ .75 + (($depth - 1) * 1.5) }}rem"></span>
    @endif
    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_180px_170px] lg:items-center">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded border px-2 py-0.5 text-[10px] font-black uppercase {{ $levelColors[$plan->level] }}">
                    {{ $levelLabels[$plan->level] }}
                </span>
                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold {{ $statusColors[$plan->status] }}">
                    {{ $statusLabels[$plan->status] }}
                </span>
                @if($plan->ends_at && $plan->status !== 'completed')
                    <span class="text-[10px] font-semibold {{ $plan->ends_at->isPast() ? 'text-red-600' : 'text-gray-400' }}">
                        Tenggat {{ $plan->ends_at->translatedFormat('d M Y') }}
                    </span>
                @endif
            </div>
            <h5 class="mt-1.5 text-sm font-black text-gray-900">{{ $plan->title }}</h5>
            @if($plan->description)
                <p class="mt-1 text-xs leading-5 text-gray-500">{{ $plan->description }}</p>
            @endif
            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-gray-500">
                <span>Penanggung jawab: <strong class="text-gray-700">{{ $plan->owner?->name ?? 'Belum ditentukan' }}</strong></span>
                @if($plan->target_value !== null)
                    <span>Target: <strong class="text-gray-700">{{ number_format((float) $plan->target_value, 0, ',', '.') }} {{ $plan->metric_unit }}</strong></span>
                @endif
            </div>
            @if($plan->latest_evaluation)
                <p class="mt-2 border-l-2 border-amber-300 pl-2 text-[11px] italic leading-4 text-gray-500">
                    Evaluasi terakhir: {{ $plan->latest_evaluation }}
                </p>
            @endif
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between text-[11px]">
                <span class="font-semibold text-gray-500">Progres</span>
                <span class="font-black text-gray-900">{{ number_format((float) $plan->progress_percent, 0) }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                <div class="h-full rounded-full transition-all duration-700 {{ $plan->status === 'at_risk' ? 'bg-amber-500' : ($plan->status === 'completed' ? 'bg-emerald-500' : 'bg-indigo-500') }}"
                    style="width: {{ min(100, (float) $plan->progress_percent) }}%"></div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-1.5">
            @if($canEditSelected)
                <button type="button" @click="openProgress(@js($progressPayload))"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:border-emerald-300 hover:text-emerald-600"
                    title="Catat evaluasi">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </button>
                <button type="button" @click="openPlan({{ $plan->okr_key_result_id }}, @js($planPayload))"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-600 hover:border-indigo-300 hover:text-indigo-600"
                    title="Edit target">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <form method="POST" action="{{ route('okr.plans.destroy', $plan) }}" onsubmit="return confirm('Hapus target ini beserta seluruh target turunannya?')">
                    @csrf
                    @method('DELETE')
                    <button class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 bg-white text-gray-500 hover:border-red-300 hover:text-red-600" title="Hapus target">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"/></svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@foreach($plan->children as $child)
    @include('pages.okr._plan-row', ['plan' => $child, 'depth' => $depth + 1, 'canEditSelected' => $canEditSelected])
@endforeach
