@props(['plan'])

<div class="border rounded-3 p-4 {{ \Carbon\Carbon::parse($plan->teach_date)->isToday() ? 'border-primary bg-primary bg-opacity-10' : '' }}">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <h6 class="mb-1 fw-bold">{{ $plan->topic }}</h6>
            <div class="text-muted small">
                <i class="fas fa-clock me-1"></i> {{ $plan->duration_minutes }} menit
                @if($plan->class)
                <span class="ms-2"><i class="fas fa-chalkboard me-1"></i> {{ $plan->class->nama_kelas }}</span>
                @endif
                @if($plan->subject)
                <span class="ms-2"><i class="fas fa-book me-1"></i> {{ $plan->subject->nama_mapel }}</span>
                @endif
            </div>
        </div>
        <div>
            {!! $plan->statusBadge() !!}
        </div>
    </div>
    
    <div class="mt-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="text-muted">Progres Persiapan</small>
            <small class="fw-bold">{{ $plan->completionPercent() }}%</small>
        </div>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar {{ $plan->completionPercent() == 100 ? 'bg-success' : 'bg-primary' }}" 
                 role="progressbar" 
                 style="width: {{ $plan->completionPercent() }}%" 
                 aria-valuenow="{{ $plan->completionPercent() }}" 
                 aria-valuemin="0" 
                 aria-valuemax="100"></div>
        </div>
    </div>

    <div class="mt-3 pt-3 border-top d-flex justify-content-end gap-2">
        <a href="{{ route('guru-kelas.lesson-plan.show', $plan->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
        @if($plan->status != 'done')
        <a href="{{ route('guru-kelas.lesson-plan.edit', $plan->id) }}" class="btn btn-sm btn-primary">Edit RPP</a>
        @endif
    </div>
</div>
