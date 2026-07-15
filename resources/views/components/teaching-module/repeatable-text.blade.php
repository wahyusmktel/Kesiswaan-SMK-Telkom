@props([
    'title',
    'path',
    'description' => null,
    'addLabel' => 'Tambah poin',
    'itemLabel' => 'Poin',
    'placeholder' => 'Tuliskan isi bagian ini...',
    'rows' => 3,
])

<div class="space-y-3">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h4 class="text-sm font-bold text-gray-900">{{ $title }}</h4>
            @if($description)
                <p class="mt-1 text-xs leading-5 text-gray-500">{{ $description }}</p>
            @endif
        </div>
        <button type="button" @click="{{ $path }}.push('')"
            class="inline-flex h-9 shrink-0 items-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ $addLabel }}
        </button>
    </div>

    <div class="space-y-3">
        <template x-for="(item, index) in {{ $path }}" :key="index">
            <div class="grid grid-cols-[2rem_minmax(0,1fr)_2.5rem] items-start gap-2">
                <div class="mt-2 flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-black text-gray-600"
                    x-text="index + 1"></div>
                <div>
                    <label class="sr-only" x-text="'{{ $itemLabel }} ' + (index + 1)"></label>
                    <textarea x-model="{{ $path }}[index]" rows="{{ $rows }}"
                        placeholder="{{ $placeholder }}"
                        class="block w-full resize-y rounded-md border-gray-300 text-sm leading-6 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                </div>
                <button type="button" @click="removeItem({{ $path }}, index)"
                    :disabled="{{ $path }}.length === 1"
                    class="mt-1 inline-flex h-9 w-9 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:cursor-not-allowed disabled:opacity-30"
                    title="Hapus {{ strtolower($itemLabel) }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="sr-only">Hapus</span>
                </button>
            </div>
        </template>
    </div>
</div>
