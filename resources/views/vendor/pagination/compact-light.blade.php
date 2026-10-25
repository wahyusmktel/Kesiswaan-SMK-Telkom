@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
        {{-- Summary Info --}}
        <div class="text-xs text-gray-600 order-2 sm:order-1 text-center sm:text-left">
            Menampilkan
            <span class="font-bold text-gray-900">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-bold text-gray-900">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-bold text-gray-900">{{ $paginator->total() }}</span>
            data
        </div>

        {{-- Navigation & Jump Controls --}}
        <div class="flex flex-wrap items-center justify-center gap-1.5 order-1 sm:order-2">
            {{-- Previous Page Button --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-200 rounded-md cursor-not-allowed select-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md shadow-sm transition-colors focus:ring-1 focus:ring-red-500">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Prev
                </a>
            @endif

            {{-- Compact Page Numbers (Max 4 Digits Window) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                if ($last <= 4) {
                    $pageRange = range(1, $last);
                } else {
                    $start = max(1, min($current - 1, $last - 3));
                    $end = min($last, $start + 3);
                    if ($end - $start < 3) {
                        $start = max(1, $end - 3);
                    }
                    $pageRange = range($start, $end);
                }
            @endphp

            @foreach ($pageRange as $page)
                @if ($page == $current)
                    <span aria-current="page" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-bold text-red-700 bg-red-50 border border-red-300 rounded-md shadow-sm select-none">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md shadow-sm transition-colors focus:ring-1 focus:ring-red-500">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Page Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-md shadow-sm transition-colors focus:ring-1 focus:ring-red-500">
                    Next
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-200 rounded-md cursor-not-allowed select-none">
                    Next
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif

            {{-- Jump to Page Input Box --}}
            <div class="flex items-center gap-1.5 ml-1 pl-2 border-l border-gray-200">
                <span class="text-xs text-gray-500 whitespace-nowrap">Ke Hal:</span>
                <input type="number" min="1" max="{{ $last }}"
                    value="{{ $current }}"
                    onkeydown="if(event.key==='Enter'){event.preventDefault(); let val=parseInt(this.value); if(!val||val<1)val=1; if(val>{{ $last }})val={{ $last }}; let u=new URL(window.location.href); u.searchParams.set('page', val); window.location.href=u.toString();}"
                    class="w-12 h-8 px-1 text-center text-xs font-bold text-gray-800 bg-white border border-gray-300 rounded-md focus:ring-1 focus:ring-red-500 focus:border-red-500 shadow-sm"
                    title="Ketik nomor halaman lalu tekan Enter untuk lompat">
                <span class="text-xs text-gray-400 whitespace-nowrap">/ {{ $last }}</span>
            </div>
        </div>
    </nav>
@endif
