@php
    $photoItems = $photos->map(fn ($photo) => [
        'id' => $photo->id,
        'album_id' => $photo->gallery_album_id,
        'album' => $photo->album?->name ?? 'Tanpa Album',
        'title' => $photo->title ?: ($photo->album?->name ?? 'Foto Galeri'),
        'caption' => $photo->caption,
        'url' => $photo->image_url,
        'uploader' => $photo->uploader?->name ?? 'Kontributor',
        'date' => optional($photo->taken_at ?? $photo->created_at)->format('d M Y'),
        'size' => $photo->size_for_humans,
        'likes_count' => $photo->likes_count,
        'comments_count' => $photo->comments_count,
        'liked_by_me' => $photo->likes->isNotEmpty(),
        'love_url' => route('gallery-photo.love', $photo),
        'comment_url' => route('gallery-photo.comments.store', $photo),
        'delete_url' => route('gallery-photo.destroy', $photo),
        'can_delete' => auth()->check() && (auth()->id() === $photo->user_id || auth()->user()->hasRole('Super Admin')),
        'comments' => $photo->comments->sortByDesc('created_at')->map(fn ($comment) => [
            'id' => $comment->id,
            'author' => $comment->author?->name ?? 'Pengguna',
            'body' => $comment->body,
            'date' => $comment->created_at->diffForHumans(),
            'delete_url' => route('gallery-photo.comments.destroy', $comment),
            'can_delete' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole('Super Admin')),
        ])->values(),
    ])->values();

    $albumItems = $albums->map(fn ($album) => [
        'id' => $album->id,
        'name' => $album->name,
        'description' => $album->description,
        'count' => $album->photos_count,
        'cover' => $album->coverPhoto?->image_url,
        'latest' => $photoItems->where('album_id', $album->id)->take(4)->pluck('url')->values(),
    ])->values();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Galeri Photo - SMK Telkom Lampung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; }
        h1, h2, h3, .font-outfit { font-family: 'Outfit', sans-serif; }
        .masonry { column-count: 1; column-gap: 1rem; }
        @media (min-width: 640px) { .masonry { column-count: 2; } }
        @media (min-width: 1024px) { .masonry { column-count: 3; } }
        .masonry-item { break-inside: avoid; margin-bottom: 1rem; }
    </style>
</head>
<body x-data="galleryPage(@js($albumItems), @js($photoItems), @js($canInteract))" x-init="init()" class="min-h-screen">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 shadow-sm p-1 overflow-hidden">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full rounded-lg bg-red-600"></div>
                    @endif
                </div>
                <div>
                    <p class="font-outfit text-sm font-black leading-none">Galeri Photo</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">SMK Telkom Lampung</p>
                </div>
            </a>

            <div class="flex items-center gap-2">
                @if($canUpload)
                    <button @click="uploadOpen = true" class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Foto
                    </button>
                @elseif(!auth()->check())
                    <a href="{{ route('login') }}" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-red-600 transition-colors">Masuk</a>
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-bold mb-1">Permintaan belum bisa diproses.</p>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">
            <div class="space-y-6">
                <div class="rounded-[28px] overflow-hidden bg-slate-950 text-white border border-slate-900">
                    <div class="grid md:grid-cols-[1.1fr_0.9fr] min-h-[300px]">
                        <div class="p-7 sm:p-10 flex flex-col justify-between gap-8">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.28em] text-red-300">Koleksi Dokumentasi</p>
                                <h1 class="font-outfit text-4xl sm:text-5xl font-black tracking-tight mt-3">Momen sekolah dalam satu ruang visual.</h1>
                                <p class="text-slate-300 mt-4 max-w-2xl">Jelajahi album kegiatan, dokumentasi kelas, agenda piket, prestasi, dan keseharian sekolah dengan tampilan grid modern.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 max-w-lg">
                                <div class="rounded-2xl bg-white/10 p-4 border border-white/10">
                                    <p class="text-2xl font-black">{{ $photos->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Foto</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4 border border-white/10">
                                    <p class="text-2xl font-black">{{ $albums->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Album</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4 border border-white/10">
                                    <p class="text-2xl font-black">{{ $photos->unique('user_id')->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Kontributor</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative bg-slate-900 min-h-[260px]">
                            <template x-if="featuredPhoto">
                                <img :src="featuredPhoto.url" :alt="featuredPhoto.title" class="absolute inset-0 w-full h-full object-cover">
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-5 left-5 right-5" x-show="featuredPhoto">
                                <p class="text-sm font-black" x-text="featuredPhoto?.title"></p>
                                <p class="text-xs text-slate-300" x-text="featuredPhoto?.album"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button @click="selectAlbum(null)" :class="selectedAlbum === null ? 'bg-slate-950 text-white' : 'bg-white text-slate-700 border-slate-200'" class="shrink-0 rounded-full border px-4 py-2 text-sm font-bold transition-colors">Semua Foto</button>
                    <template x-for="album in albums" :key="album.id">
                        <button @click="selectAlbum(album.id)" :class="selectedAlbum === album.id ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-700 border-slate-200'" class="shrink-0 rounded-full border px-4 py-2 text-sm font-bold transition-colors">
                            <span x-text="album.name"></span>
                            <span class="opacity-70" x-text="'(' + album.count + ')'"></span>
                        </button>
                    </template>
                </div>

                <section>
                    <div class="flex items-end justify-between gap-4 mb-4">
                        <div>
                            <h2 class="font-outfit text-2xl font-black" x-text="activeTitle"></h2>
                            <p class="text-sm text-slate-500" x-text="filteredPhotos.length + ' foto ditampilkan'"></p>
                        </div>
                    </div>

                    <div x-show="filteredPhotos.length" class="masonry">
                        <template x-for="photo in filteredPhotos" :key="photo.id">
                            <button @click="openPhoto(photo)" class="masonry-item group block w-full text-left overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                <div class="relative">
                                    <img :src="photo.url" :alt="photo.title" loading="lazy" class="w-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div class="absolute left-3 right-3 bottom-3 opacity-0 group-hover:opacity-100 transition-opacity text-white">
                                        <p class="text-sm font-black truncate" x-text="photo.title"></p>
                                        <div class="flex items-center justify-between gap-3 text-xs text-white/80">
                                            <span class="truncate" x-text="photo.album"></span>
                                            <span class="shrink-0 inline-flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" :class="photo.liked_by_me ? 'fill-red-500 text-red-500' : 'text-white'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                                    <span x-text="photo.likes_count"></span>
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                                    <span x-text="photo.comments_count"></span>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="!filteredPhotos.length" class="rounded-3xl border border-dashed border-slate-300 bg-white py-20 text-center">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2 1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="mt-4 font-outfit text-xl font-black">Belum ada foto</h3>
                        <p class="mt-1 text-sm text-slate-500">Foto yang diunggah akan tampil di sini.</p>
                    </div>
                </section>
            </div>

            <aside class="lg:sticky lg:top-24 space-y-4">
                <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-outfit font-black text-xl">Album</h2>
                        <span class="text-xs font-bold text-slate-400">{{ $albums->count() }} koleksi</span>
                    </div>
                    <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                        <template x-for="album in albums" :key="album.id">
                            <button @click="selectAlbum(album.id)" class="w-full rounded-2xl border p-2 text-left transition-all" :class="selectedAlbum === album.id ? 'border-red-200 bg-red-50' : 'border-slate-200 hover:bg-slate-50'">
                                <div class="grid grid-cols-[72px_1fr] gap-3 items-center">
                                    <div class="h-16 rounded-xl overflow-hidden bg-slate-100 grid grid-cols-2 gap-0.5">
                                        <template x-for="url in album.latest.length ? album.latest : [album.cover]" :key="url">
                                            <img x-show="url" :src="url" class="w-full h-full object-cover">
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-sm truncate" x-text="album.name"></p>
                                        <p class="text-xs text-slate-500 truncate" x-text="album.description || 'Dokumentasi sekolah'"></p>
                                        <p class="text-[11px] text-red-600 font-bold mt-1" x-text="album.count + ' foto'"></p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <div x-show="selectedPhoto" class="rounded-3xl bg-slate-950 text-white border border-slate-900 p-4 shadow-sm">
                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-black mb-3">Preview Lightroom</p>
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden bg-slate-900">
                        <template x-if="selectedPhoto">
                            <img :src="selectedPhoto.url" :alt="selectedPhoto.title" class="w-full h-full object-cover">
                        </template>
                    </div>
                    <div class="mt-4">
                        <h3 class="font-outfit text-xl font-black" x-text="selectedPhoto?.title"></h3>
                        <p class="text-sm text-slate-300 mt-1" x-text="selectedPhoto?.caption || selectedPhoto?.album"></p>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            <div class="rounded-xl bg-white/10 p-3"><span class="block text-slate-400">Uploader</span><b x-text="selectedPhoto?.uploader"></b></div>
                            <div class="rounded-xl bg-white/10 p-3"><span class="block text-slate-400">Tanggal</span><b x-text="selectedPhoto?.date"></b></div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <form x-show="canInteract" :action="selectedPhoto?.love_url" method="POST" class="flex-1">
                                @csrf
                                <button class="w-full rounded-xl px-3 py-2 text-xs font-black transition-colors" :class="selectedPhoto?.liked_by_me ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-white/10 text-white hover:bg-white/20'">
                                    <span x-text="selectedPhoto?.liked_by_me ? 'Loved' : 'Love'"></span>
                                    <span x-text="'(' + (selectedPhoto?.likes_count || 0) + ')'"></span>
                                </button>
                            </form>
                            <button @click="lightboxOpen = true" class="flex-1 rounded-xl bg-white/10 px-3 py-2 text-xs font-black text-white hover:bg-white/20">
                                Komentar <span x-text="'(' + (selectedPhoto?.comments_count || 0) + ')'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl p-4 sm:p-8" @keydown.escape.window="lightboxOpen = false">
        <button @click="lightboxOpen = false" class="absolute top-4 right-4 z-10 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="h-full grid lg:grid-cols-[1fr_420px] gap-6">
            <div class="min-h-0 flex items-center justify-center">
                <template x-if="selectedPhoto">
                    <img :src="selectedPhoto.url" :alt="selectedPhoto.title" class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl">
                </template>
            </div>
            <div class="rounded-3xl bg-white text-slate-950 p-5 self-center max-h-full overflow-y-auto">
                <p class="text-xs font-black uppercase tracking-widest text-red-600" x-text="selectedPhoto?.album"></p>
                <h2 class="font-outfit text-2xl font-black mt-2" x-text="selectedPhoto?.title"></h2>
                <p class="text-sm text-slate-600 mt-2" x-text="selectedPhoto?.caption || 'Tidak ada keterangan foto.'"></p>
                <div class="mt-5 space-y-2 text-sm">
                    <p><span class="text-slate-400">Uploader:</span> <b x-text="selectedPhoto?.uploader"></b></p>
                    <p><span class="text-slate-400">Tanggal:</span> <b x-text="selectedPhoto?.date"></b></p>
                    <p><span class="text-slate-400">Ukuran:</span> <b x-text="selectedPhoto?.size"></b></p>
                </div>
                <div class="mt-5 flex items-center gap-2">
                    <form x-show="canInteract" :action="selectedPhoto?.love_url" method="POST" class="flex-1">
                        @csrf
                        <button class="w-full rounded-xl px-4 py-2.5 text-sm font-black transition-colors" :class="selectedPhoto?.liked_by_me ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-slate-100 text-slate-700 hover:bg-red-50 hover:text-red-700'">
                            <span x-text="selectedPhoto?.liked_by_me ? 'Loved' : 'Love'"></span>
                            <span x-text="'(' + (selectedPhoto?.likes_count || 0) + ')'"></span>
                        </button>
                    </form>
                    <div class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 text-center text-sm font-black text-slate-700">
                        Komentar <span x-text="'(' + (selectedPhoto?.comments_count || 0) + ')'"></span>
                    </div>
                </div>
                <div class="mt-6 border-t border-slate-200 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-outfit text-lg font-black">Komentar</h3>
                        <span class="text-xs font-bold text-slate-400" x-text="(selectedPhoto?.comments_count || 0) + ' diskusi'"></span>
                    </div>
                    <template x-if="canInteract">
                        <form :action="selectedPhoto?.comment_url" method="POST" class="mb-4">
                            @csrf
                            <textarea name="body" required rows="3" maxlength="1000" class="w-full rounded-2xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Tulis komentar untuk foto ini..."></textarea>
                            <div class="mt-2 flex justify-end">
                                <button class="rounded-xl bg-slate-950 px-4 py-2 text-xs font-black text-white hover:bg-red-600">Kirim Komentar</button>
                            </div>
                        </form>
                    </template>
                    <template x-if="!canInteract">
                        <p class="mb-4 rounded-2xl bg-slate-50 p-3 text-sm text-slate-500">Masuk dengan role yang diizinkan untuk memberi love dan komentar.</p>
                    </template>
                    <div class="space-y-3">
                        <template x-for="comment in (selectedPhoto?.comments || [])" :key="comment.id">
                            <div class="rounded-2xl bg-slate-50 p-3 border border-slate-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-slate-900 truncate" x-text="comment.author"></p>
                                        <p class="text-[11px] font-bold text-slate-400" x-text="comment.date"></p>
                                    </div>
                                    <template x-if="comment.can_delete">
                                        <form :action="comment.delete_url" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-[11px] font-black text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </template>
                                </div>
                                <p class="mt-2 whitespace-pre-line text-sm text-slate-700" x-text="comment.body"></p>
                            </div>
                        </template>
                        <p x-show="!(selectedPhoto?.comments || []).length" class="rounded-2xl border border-dashed border-slate-200 p-5 text-center text-sm text-slate-500">Belum ada komentar.</p>
                    </div>
                </div>
                <template x-if="selectedPhoto?.can_delete">
                    <form :action="selectedPhoto.delete_url" method="POST" class="mt-5" onsubmit="return confirm('Hapus foto ini dari galeri?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">Hapus Foto</button>
                    </form>
                </template>
            </div>
        </div>
    </div>

    @if($canUpload)
        <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm p-4">
            <div class="min-h-full flex items-center justify-center">
                <form action="{{ route('gallery-photo.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h2 class="font-outfit text-2xl font-black">Tambah Foto Galeri</h2>
                            <p class="text-sm text-slate-500">Upload sampai 20 foto, format JPG, PNG, atau WEBP.</p>
                        </div>
                        <button @click="uploadOpen = false" type="button" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Pilih Album</label>
                                <select name="album_id" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="">Buat album baru</option>
                                    @foreach($albums as $album)
                                        <option value="{{ $album->id }}">{{ $album->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Nama Album Baru</label>
                                <input name="album_name" value="{{ old('album_name') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Class Meeting 2026">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Deskripsi Album Baru</label>
                            <input name="album_description" value="{{ old('album_description') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Opsional">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Judul Foto</label>
                                <input name="title" value="{{ old('title') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Opsional">
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Tanggal Kegiatan</label>
                                <input type="date" name="taken_at" value="{{ old('taken_at') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Keterangan</label>
                            <textarea name="caption" rows="3" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Opsional">{{ old('caption') }}</textarea>
                        </div>
                        <label class="block rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 hover:bg-red-50 hover:border-red-300 transition-colors p-8 text-center cursor-pointer">
                            <svg class="w-10 h-10 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/></svg>
                            <span class="mt-3 block font-black text-slate-900" x-text="uploadCount ? uploadCount + ' file dipilih' : 'Pilih foto dari perangkat'"></span>
                            <span class="text-xs text-slate-500">Maksimal 8MB per foto</span>
                            <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required class="sr-only" @change="uploadCount = $event.target.files.length">
                        </label>
                    </div>
                    <div class="p-6 bg-slate-50 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" @click="uploadOpen = false" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Upload Foto</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function galleryPage(albums, photos, canInteract) {
            return {
                albums,
                photos,
                canInteract,
                selectedAlbum: null,
                selectedPhoto: null,
                featuredPhoto: null,
                lightboxOpen: false,
                uploadOpen: false,
                uploadCount: 0,
                init() {
                    this.featuredPhoto = this.photos[0] || null;
                    this.selectedPhoto = this.photos[0] || null;
                },
                get filteredPhotos() {
                    if (this.selectedAlbum === null) return this.photos;
                    return this.photos.filter(photo => photo.album_id === this.selectedAlbum);
                },
                get activeTitle() {
                    if (this.selectedAlbum === null) return 'Semua Foto';
                    return this.albums.find(album => album.id === this.selectedAlbum)?.name || 'Album';
                },
                selectAlbum(id) {
                    this.selectedAlbum = id;
                    this.selectedPhoto = this.filteredPhotos[0] || null;
                    this.featuredPhoto = this.selectedPhoto || this.photos[0] || null;
                },
                openPhoto(photo) {
                    this.selectedPhoto = photo;
                    this.featuredPhoto = photo;
                    this.lightboxOpen = true;
                },
            }
        }
    </script>
</body>
</html>
