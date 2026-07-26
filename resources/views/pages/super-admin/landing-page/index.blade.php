<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Kelola Landing Page</h2>
                <p class="text-sm text-gray-500">Atur hero slider dan info berjalan untuk tema Stella Vue.</p>
            </div>
            <a href="{{ route('welcome', ['preview' => 'stella-vue']) }}" target="_blank"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H19.5V12M10.5 13.5 19.5 4.5M19.5 15.75v2.25A1.5 1.5 0 0118 19.5H6A1.5 1.5 0 014.5 18V6A1.5 1.5 0 016 4.5h2.25" />
                </svg>
                Preview Landing
            </a>
        </div>
    </x-slot>

    <div class="w-full py-6">
        <div class="mx-auto w-full max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-disc space-y-1 pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <section class="border-b border-gray-200 pb-8" x-data="{ createOpen: false }">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-red-600">Konten Utama</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-950">Hero Slider</h3>
                        <p class="mt-1 text-sm text-gray-500">Gunakan gambar landscape minimal 1600 x 900 piksel agar tajam di layar besar.</p>
                    </div>
                    <button type="button" @click="createOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah Hero
                    </button>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @forelse ($slides as $slide)
                        <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                            <div class="relative aspect-[16/9] bg-gray-100">
                                <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                <div class="absolute left-3 top-3 flex gap-2">
                                    <span class="rounded-full bg-gray-950/80 px-2.5 py-1 text-[10px] font-bold text-white backdrop-blur">Urutan {{ $slide->sort_order }}</span>
                                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $slide->is_active ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $slide->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="text-xs font-bold uppercase text-red-600">{{ $slide->eyebrow ?: 'SISFO TS' }}</p>
                                <h4 class="mt-2 text-lg font-bold leading-6 text-gray-950">{{ $slide->title }}</h4>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500">{{ $slide->description ?: 'Tanpa deskripsi.' }}</p>
                                <div class="mt-5 flex items-center gap-2">
                                    <button type="button" @click="$dispatch('edit-landing-slide', {{ Illuminate\Support\Js::from([
                                        'id' => $slide->id,
                                        'eyebrow' => $slide->eyebrow,
                                        'title' => $slide->title,
                                        'description' => $slide->description,
                                        'cta_label' => $slide->cta_label,
                                        'cta_url' => $slide->cta_url,
                                        'sort_order' => $slide->sort_order,
                                        'is_active' => $slide->is_active,
                                        'image_url' => $slide->image_url,
                                        'url' => route('super-admin.landing-page.slides.update', $slide),
                                    ]) }})"
                                        class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-bold text-gray-700 hover:bg-gray-50">Edit</button>
                                    <form method="POST" action="{{ route('super-admin.landing-page.slides.destroy', $slide) }}" onsubmit="return confirm('Hapus hero slide ini?')">
                                        @csrf @method('DELETE')
                                        <button class="grid h-10 w-10 place-items-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50" title="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M19.228 5.79 18.16 19.673A2.25 2.25 0 0115.916 21H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="md:col-span-2 xl:col-span-3 rounded-lg border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                            <p class="font-bold text-gray-800">Belum ada hero khusus.</p>
                            <p class="mt-1 text-sm text-gray-500">Landing page akan menggunakan gambar bawaan sampai hero pertama ditambahkan.</p>
                        </div>
                    @endforelse
                </div>

                <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="fixed inset-0 bg-gray-950/60" @click="createOpen = false"></div>
                    <div class="flex min-h-full items-center justify-center p-4">
                        <div class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl">
                            <div class="border-b px-6 py-5"><h3 class="text-lg font-bold">Tambah Hero Slide</h3></div>
                            <form method="POST" action="{{ route('super-admin.landing-page.slides.store') }}" enctype="multipart/form-data">
                                @csrf
                                @include('pages.super-admin.landing-page.partials.slide-fields', ['slide' => null])
                                <div class="flex justify-end gap-2 border-t bg-gray-50 px-6 py-4">
                                    <button type="button" @click="createOpen = false" class="rounded-lg border px-4 py-2 text-sm font-bold">Batal</button>
                                    <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Simpan Hero</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <section class="pb-8">
                <div class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr]">
                    <div>
                        <p class="text-xs font-bold uppercase text-red-600">Running Information</p>
                        <h3 class="mt-1 text-2xl font-bold text-gray-950">Tambah Info Terkini</h3>
                        <p class="mt-1 text-sm leading-6 text-gray-500">Teks aktif akan bergerak di antara hero dan statistik. Tautan bersifat opsional.</p>
                        <form method="POST" action="{{ route('super-admin.landing-page.tickers.store') }}" class="mt-5 space-y-4">
                            @csrf
                            <label class="block"><span class="mb-1 block text-sm font-semibold">Isi Informasi *</span><textarea name="text" rows="4" required maxlength="500" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></textarea></label>
                            <label class="block"><span class="mb-1 block text-sm font-semibold">Tautan Opsional</span><input name="url" placeholder="https://... atau /halaman" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></label>
                            <div class="grid grid-cols-2 gap-4">
                                <label><span class="mb-1 block text-sm font-semibold">Urutan</span><input type="number" name="sort_order" value="0" min="0" max="999" class="w-full rounded-lg border-gray-300 text-sm"></label>
                                <label class="flex items-end gap-2 pb-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-red-600 focus:ring-red-500"><span class="text-sm font-semibold">Tampilkan</span></label>
                            </div>
                            <button class="inline-flex items-center gap-2 rounded-lg bg-gray-950 px-4 py-2 text-sm font-bold text-white hover:bg-gray-800">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Tambah Informasi
                            </button>
                        </form>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                        <div class="border-b bg-gray-50 px-5 py-4">
                            <h4 class="font-bold text-gray-900">Daftar Info Terkini</h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse ($tickers as $ticker)
                                <form method="POST" action="{{ route('super-admin.landing-page.tickers.update', $ticker) }}" class="grid gap-3 p-5 sm:grid-cols-[1fr_90px_auto] sm:items-end">
                                    @csrf @method('PUT')
                                    <div class="space-y-2">
                                        <textarea name="text" rows="2" required maxlength="500" class="w-full rounded-lg border-gray-300 text-sm">{{ $ticker->text }}</textarea>
                                        <input name="url" value="{{ $ticker->url }}" placeholder="Tautan opsional" class="w-full rounded-lg border-gray-300 text-xs">
                                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-gray-600"><input type="checkbox" name="is_active" value="1" {{ $ticker->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-red-600"> Aktif</label>
                                    </div>
                                    <label><span class="mb-1 block text-xs font-semibold text-gray-500">Urutan</span><input type="number" name="sort_order" value="{{ $ticker->sort_order }}" min="0" max="999" class="w-full rounded-lg border-gray-300 text-sm"></label>
                                    <div class="flex gap-2">
                                        <button class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Simpan</button>
                                        <button type="submit" form="delete-ticker-{{ $ticker->id }}" class="grid h-9 w-9 place-items-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50" title="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166M4.772 5.79 5.84 19.673A2.25 2.25 0 008.084 21h7.832a2.25 2.25 0 002.244-2.077L19.228 5.79M9.75 5.25v-.916A2.25 2.25 0 0112 2.084a2.25 2.25 0 012.25 2.25v.916" /></svg>
                                        </button>
                                    </div>
                                </form>
                                <form id="delete-ticker-{{ $ticker->id }}" method="POST" action="{{ route('super-admin.landing-page.tickers.destroy', $ticker) }}" onsubmit="return confirm('Hapus info ini?')" class="hidden">@csrf @method('DELETE')</form>
                            @empty
                                <p class="px-5 py-12 text-center text-sm text-gray-500">Belum ada info terkini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div x-data="{ open: false, slide: {} }" @edit-landing-slide.window="slide = $event.detail; open = true" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="fixed inset-0 bg-gray-950/60" @click="open = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-lg bg-white shadow-xl">
                <div class="border-b px-6 py-5"><h3 class="text-lg font-bold">Edit Hero Slide</h3></div>
                <form method="POST" :action="slide.url" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="grid max-h-[70vh] gap-4 overflow-y-auto p-6 md:grid-cols-2">
                        <label><span class="mb-1 block text-sm font-semibold">Label Kecil</span><input name="eyebrow" x-model="slide.eyebrow" class="w-full rounded-lg border-gray-300"></label>
                        <label><span class="mb-1 block text-sm font-semibold">Urutan *</span><input type="number" name="sort_order" x-model="slide.sort_order" required min="0" max="999" class="w-full rounded-lg border-gray-300"></label>
                        <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Judul *</span><input name="title" x-model="slide.title" required maxlength="160" class="w-full rounded-lg border-gray-300"></label>
                        <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Deskripsi</span><textarea name="description" x-model="slide.description" rows="3" maxlength="500" class="w-full rounded-lg border-gray-300"></textarea></label>
                        <label><span class="mb-1 block text-sm font-semibold">Label CTA</span><input name="cta_label" x-model="slide.cta_label" class="w-full rounded-lg border-gray-300"></label>
                        <label><span class="mb-1 block text-sm font-semibold">URL CTA</span><input name="cta_url" x-model="slide.cta_url" class="w-full rounded-lg border-gray-300"></label>
                        <label class="md:col-span-2"><span class="mb-1 block text-sm font-semibold">Ganti Gambar</span><input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="w-full rounded-lg border border-gray-300 p-2 text-sm"></label>
                        <label class="md:col-span-2 inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" :checked="slide.is_active" class="rounded border-gray-300 text-red-600"><span class="text-sm font-semibold">Tampilkan slide ini</span></label>
                    </div>
                    <div class="flex justify-end gap-2 border-t bg-gray-50 px-6 py-4"><button type="button" @click="open = false" class="rounded-lg border px-4 py-2 text-sm font-bold">Batal</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
