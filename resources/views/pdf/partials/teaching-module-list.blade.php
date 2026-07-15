@php
    $filteredItems = collect($items ?? [])
        ->filter(fn ($item) => is_string($item) && trim($item) !== '')
        ->values();
@endphp

@if($filteredItems->isEmpty())
    <span class="empty-value">-</span>
@elseif($filteredItems->count() === 1)
    <div class="text-block">{!! nl2br(e($filteredItems->first())) !!}</div>
@else
    <div class="content-list">
        @foreach($filteredItems as $item)
            <div class="content-list-item"><span class="content-list-marker"></span>{!! nl2br(e($item)) !!}</div>
        @endforeach
    </div>
@endif
