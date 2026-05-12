@php
    $commentPayload = function ($comment, $allComments) use (&$commentPayload) {
        return [
            'id' => $comment->id,
            'author' => $comment->author?->name ?? 'Pengguna',
            'body' => $comment->body,
            'date' => $comment->created_at->diffForHumans(),
            'delete_url' => route('gallery-photo.comments.destroy', $comment),
            'can_delete' => auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->hasRole('Super Admin')),
            'replies' => $allComments->where('parent_id', $comment->id)
                ->sortBy('created_at')
                ->map(fn ($reply) => $commentPayload($reply, collect()))
                ->values(),
        ];
    };

    $photoItems = $photos->map(function ($photo) use ($commentPayload) {
        $comments = $photo->comments->sortByDesc('created_at');
        return [
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
        'download_url' => route('gallery-photo.download', $photo),
        'delete_url' => route('gallery-photo.destroy', $photo),
        'can_delete' => auth()->check() && (auth()->id() === $photo->user_id || auth()->user()->hasRole('Super Admin')),
        'comments' => $comments->whereNull('parent_id')->values()->map(fn ($comment) => $commentPayload($comment, $comments))->values(),
        ];
    })->values();

    $albumItems = $albums->map(fn ($album) => [
        'id' => $album->id,
        'name' => $album->name,
        'description' => $album->description,
        'count' => $album->photos_count,
        'cover' => $album->coverPhoto?->image_url,
        'latest' => $photoItems->where('album_id', $album->id)->take(4)->pluck('url')->values(),
    ])->values();

    $authUser = auth()->user();
    $profilePayload = null;
    if ($authUser) {
        $authUser->loadMissing(['masterSiswa', 'masterGuru']);
        $isSiswa = $authUser->hasRole('Siswa') || $authUser->hasRole('siswa');
        $isGuru = $authUser->hasRole('Guru Kelas') || $authUser->hasRole('Guru Piket');
        $profilePayload = [
            'name' => $authUser->masterSiswa?->nama_lengkap ?? $authUser->masterGuru?->nama_lengkap ?? $authUser->name,
            'role' => session('active_role') ?? $authUser->getRoleNames()->first(),
            'avatar' => $authUser->avatar ? Storage::url($authUser->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($authUser->name) . '&background=0f172a&color=ffffff',
            'identifier_label' => $isSiswa ? 'NIS' : ($isGuru ? 'NIP' : 'ID'),
            'identifier' => $isSiswa
                ? ($authUser->masterSiswa?->nis ?? '-')
                : ($authUser->masterGuru?->nuptk ?? $authUser->masterGuru?->nik ?? $authUser->masterGuru?->kode_guru ?? '-'),
            'photos_count' => $photos->where('user_id', $authUser->id)->count(),
            'albums_count' => $albums->where('user_id', $authUser->id)->count(),
            'loved_count' => \App\Models\GalleryPhotoLike::where('user_id', $authUser->id)->count(),
        ];
    }
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
        .love-burst {
            position: fixed;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #ef4444;
            pointer-events: none;
            z-index: 80;
            animation: loveBurst 650ms ease-out forwards;
            box-shadow:
                0 -26px 0 #ef4444,
                18px -18px 0 #f97316,
                26px 0 0 #facc15,
                18px 18px 0 #fb7185,
                0 26px 0 #ef4444,
                -18px 18px 0 #f97316,
                -26px 0 0 #facc15,
                -18px -18px 0 #fb7185;
        }
        @keyframes loveBurst {
            0% { opacity: 1; transform: translate(-50%, -50%) scale(0.25) rotate(0deg); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(1.6) rotate(35deg); }
        }
        @media (max-width: 767px) {
            body { background: #f1f5f9; }
            .mobile-scrollbar-none::-webkit-scrollbar { display: none; }
            .mobile-scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
            .masonry { column-count: 2; column-gap: 0.625rem; }
            .masonry-item { margin-bottom: 0.625rem; border-radius: 1rem; }
        }
    </style>
</head>
<body x-data="galleryPage(@js($albumItems), @js($photoItems), @js($canInteract), @js($profilePayload))" x-init="init()" class="min-h-screen">
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 min-h-16 py-3 grid gap-3 lg:grid-cols-[280px_1fr_auto] lg:items-center">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-9 sm:h-9 rounded-2xl sm:rounded-xl bg-white border border-slate-200 shadow-sm p-1 overflow-hidden">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="w-full h-full object-contain">
                    @else
                        <div class="w-full h-full rounded-lg bg-red-600"></div>
                    @endif
                </div>
                <div>
                    <p class="font-outfit text-sm font-black leading-none">Galeri Photo</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold hidden min-[380px]:block">SMK Telkom Lampung</p>
                </div>
            </a>

            <div class="relative order-3 lg:order-none">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model.debounce.120ms="searchQuery" @focus="searchOpen = true" @keydown.escape="searchOpen = false" type="search" class="w-full rounded-2xl border-slate-200 bg-slate-50 pl-10 pr-4 py-3 sm:py-2.5 text-[16px] sm:text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500" placeholder="Cari foto...">
                </div>
                <div x-show="searchOpen && searchQuery.trim().length" x-cloak @click.outside="searchOpen = false" class="absolute left-0 right-0 top-full mt-2 z-50 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                    <div class="max-h-96 overflow-y-auto p-2">
                        <template x-for="photo in searchResults" :key="photo.id">
                            <button @click="openPhoto(photo); searchOpen = false" type="button" class="w-full grid grid-cols-[56px_1fr] gap-3 rounded-xl p-2 text-left hover:bg-slate-50">
                                <img :src="photo.url" :alt="photo.title" class="w-14 h-14 rounded-xl object-cover bg-slate-100">
                                <div class="min-w-0 self-center">
                                    <p class="text-sm font-black text-slate-900 truncate" x-text="photo.title"></p>
                                    <p class="text-xs text-slate-500 truncate" x-text="photo.album + ' - ' + photo.uploader"></p>
                                </div>
                            </button>
                        </template>
                        <div x-show="!searchResults.length" class="p-5 text-center text-sm text-slate-500">Tidak ada foto yang cocok.</div>
                    </div>
                </div>
            </div>

            <div class="absolute right-3 top-3 lg:static flex items-center justify-end gap-1.5 sm:gap-2">
                <a href="{{ route('welcome') }}" class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50" title="Halaman utama aplikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                    Home
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50" title="Dashboard utama">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6a2 2 0 012-2h4v6H4V6zm10-2h4a2 2 0 012 2v4h-6V4zM4 14h6v6H6a2 2 0 01-2-2v-4zm10 0h6v4a2 2 0 01-2 2h-4v-6z"/></svg>
                        Dashboard
                    </a>
                @endauth
                @if($canUpload)
                    <button @click="uploadOpen = true" class="inline-flex items-center gap-2 rounded-2xl sm:rounded-xl bg-slate-950 p-2.5 sm:px-4 sm:py-2 text-sm font-bold text-white hover:bg-red-600 transition-colors" title="Tambah Foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        <span class="hidden sm:inline">Tambah Foto</span>
                    </button>
                @elseif(!auth()->check())
                    <a href="{{ route('login') }}" class="rounded-xl bg-slate-950 px-4 py-2 text-sm font-bold text-white hover:bg-red-600 transition-colors">Masuk</a>
                @endif
                @auth
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" type="button" class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow-md ring-1 ring-slate-200 bg-slate-100" title="Profil">
                            <img :src="profile.avatar" alt="Profil" class="w-full h-full object-cover">
                        </button>
                        <div x-show="profileOpen" x-cloak @click.outside="profileOpen = false" class="fixed left-3 right-3 top-20 sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 sm:w-80 rounded-3xl border border-slate-200 bg-white p-4 shadow-2xl">
                            <div class="flex items-center gap-3">
                                <img :src="profile.avatar" alt="Profil" class="w-14 h-14 rounded-2xl object-cover bg-slate-100">
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 truncate" x-text="profile.name"></p>
                                    <p class="text-xs font-bold text-slate-500 truncate" x-text="profile.role"></p>
                                    <p class="text-xs font-black text-red-600 mt-1"><span x-text="profile.identifier_label"></span>: <span x-text="profile.identifier"></span></p>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <div class="rounded-2xl bg-slate-50 p-3 text-center">
                                    <p class="text-xl font-black" x-text="profile.photos_count"></p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Foto</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3 text-center">
                                    <p class="text-xl font-black" x-text="profile.albums_count"></p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Album</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-3 text-center">
                                    <p class="text-xl font-black" x-text="profile.loved_count"></p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Love</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8">
        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-bold mb-1">Permintaan belum bisa diproses.</p>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="grid lg:grid-cols-[1fr_360px] gap-4 sm:gap-6 items-start">
            <div class="space-y-4 sm:space-y-6">
                <div class="rounded-3xl sm:rounded-[28px] overflow-hidden bg-slate-950 text-white border border-slate-900 shadow-sm">
                    <div class="grid md:grid-cols-[1.1fr_0.9fr] min-h-[220px] sm:min-h-[300px]">
                        <div class="p-5 sm:p-10 flex flex-col justify-between gap-5 sm:gap-8">
                            <div>
                                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.22em] sm:tracking-[0.28em] text-red-300">Koleksi Dokumentasi</p>
                                <h1 class="font-outfit text-2xl min-[380px]:text-3xl sm:text-5xl font-black tracking-tight mt-2 sm:mt-3 leading-tight">Momen sekolah dalam satu ruang visual.</h1>
                                <p class="text-slate-300 mt-3 sm:mt-4 max-w-2xl text-sm sm:text-base leading-relaxed">Jelajahi album kegiatan, dokumentasi kelas, agenda piket, prestasi, dan keseharian sekolah.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-2 sm:gap-3 max-w-lg">
                                <div class="rounded-2xl bg-white/10 p-3 sm:p-4 border border-white/10">
                                    <p class="text-xl sm:text-2xl font-black">{{ $photos->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Foto</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-3 sm:p-4 border border-white/10">
                                    <p class="text-xl sm:text-2xl font-black">{{ $albums->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Album</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-3 sm:p-4 border border-white/10">
                                    <p class="text-xl sm:text-2xl font-black">{{ $photos->unique('user_id')->count() }}</p>
                                    <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Kontributor</p>
                                </div>
                            </div>
                        </div>
                        <div class="relative bg-slate-900 min-h-[190px] sm:min-h-[260px]">
                            <template x-if="featuredPhoto">
                                <img :src="featuredPhoto.url" :alt="featuredPhoto.title" class="absolute inset-0 w-full h-full object-cover">
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 sm:bottom-5 left-4 sm:left-5 right-4 sm:right-5" x-show="featuredPhoto">
                                <p class="text-sm font-black" x-text="featuredPhoto?.title"></p>
                                <p class="text-xs text-slate-300" x-text="featuredPhoto?.album"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mobile-scrollbar-none flex gap-2 overflow-x-auto pb-1 -mx-3 px-3 sm:mx-0 sm:px-0">
                    <button @click="selectAlbum(null)" :class="selectedAlbum === null ? 'bg-slate-950 text-white' : 'bg-white text-slate-700 border-slate-200'" class="shrink-0 rounded-full border px-4 py-2.5 sm:py-2 text-sm font-bold transition-colors shadow-sm">Semua Foto</button>
                    <template x-for="album in albums" :key="album.id">
                        <button @click="selectAlbum(album.id)" :class="selectedAlbum === album.id ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-700 border-slate-200'" class="shrink-0 rounded-full border px-4 py-2.5 sm:py-2 text-sm font-bold transition-colors shadow-sm">
                            <span x-text="album.name"></span>
                            <span class="opacity-70" x-text="'(' + album.count + ')'"></span>
                        </button>
                    </template>
                </div>

                <section>
                    <div class="flex items-end justify-between gap-4 mb-3 sm:mb-4">
                        <div>
                            <h2 class="font-outfit text-xl sm:text-2xl font-black" x-text="activeTitle"></h2>
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
                    <div class="mobile-scrollbar-none flex lg:block gap-3 lg:space-y-3 max-h-none lg:max-h-[520px] overflow-x-auto lg:overflow-x-visible lg:overflow-y-auto pr-1 -mx-1 px-1">
                        <template x-for="album in albums" :key="album.id">
                            <button @click="selectAlbum(album.id)" class="shrink-0 w-64 lg:w-full rounded-2xl border p-2 text-left transition-all" :class="selectedAlbum === album.id ? 'border-red-200 bg-red-50' : 'border-slate-200 hover:bg-slate-50'">
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

                <div x-show="selectedPhoto" class="hidden lg:block rounded-3xl bg-slate-950 text-white border border-slate-900 p-4 shadow-sm">
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
                            <button x-show="canInteract" @click="toggleLove(selectedPhoto, $event)" type="button" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-black transition-colors" :class="selectedPhoto?.liked_by_me ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-white/10 text-white hover:bg-white/20'">
                                <svg class="w-4 h-4 transition-transform" :class="selectedPhoto?.liked_by_me ? 'fill-current scale-110' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                <span x-text="selectedPhoto?.likes_count || 0"></span>
                            </button>
                            <button @click="openPhoto(selectedPhoto)" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-3 py-2 text-xs font-black text-white hover:bg-white/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                                <span x-text="selectedPhoto?.comments_count || 0"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <div x-show="lightboxOpen" x-cloak class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-xl p-0 sm:p-8" @keydown.escape.window="lightboxOpen = false">
        <button @click="lightboxOpen = false" class="absolute top-3 right-3 sm:top-4 sm:right-4 z-10 w-11 h-11 rounded-full bg-white/10 text-white hover:bg-white/20 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="h-full grid lg:grid-cols-[1fr_420px] gap-0 sm:gap-6">
            <div class="relative min-h-0 h-[48vh] sm:h-auto flex items-center justify-center overflow-hidden" @pointermove.window="dragMove($event)" @pointerup.window="dragEnd()" @pointercancel.window="dragEnd()">
                <template x-if="selectedPhoto">
                    <img :src="selectedPhoto.url" :alt="selectedPhoto.title"
                        @pointerdown.prevent="dragStart($event)"
                        class="max-h-full max-w-full rounded-2xl object-contain shadow-2xl transition-transform duration-100 ease-out select-none"
                        :class="zoom > 1 ? (isDragging ? 'cursor-grabbing' : 'cursor-grab') : 'cursor-default'"
                        :style="'transform: translate(' + panX + 'px, ' + panY + 'px) scale(' + zoom + ')'">
                </template>
                <div class="absolute bottom-3 sm:bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-2 rounded-2xl bg-white/10 p-2 backdrop-blur-xl border border-white/10">
                    <button @click="zoomOut()" type="button" class="w-10 h-10 rounded-xl bg-white/10 text-white hover:bg-white/20 flex items-center justify-center" title="Zoom out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 20l-4.35-4.35M8 11h6m4 0a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <button @click="resetZoom()" type="button" class="w-10 h-10 rounded-xl bg-white/10 text-white hover:bg-white/20 flex items-center justify-center" title="Reset zoom">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5"/></svg>
                    </button>
                    <button @click="zoomIn()" type="button" class="w-10 h-10 rounded-xl bg-white/10 text-white hover:bg-white/20 flex items-center justify-center" title="Zoom in">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M20 20l-4.35-4.35M11 8v6m-3-3h6m4 0a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </div>
            <div class="rounded-t-[28px] lg:rounded-3xl bg-white text-slate-950 p-4 sm:p-5 self-end lg:self-center h-[52vh] lg:h-auto max-h-full overflow-y-auto shadow-2xl lg:shadow-none">
                <button @click="goToAlbum(selectedPhoto?.album_id)" type="button" class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-xs font-black uppercase tracking-widest text-red-600 hover:bg-red-100">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                    <span x-text="selectedPhoto?.album"></span>
                </button>
                <h2 class="font-outfit text-xl sm:text-2xl font-black mt-2" x-text="selectedPhoto?.title"></h2>
                <p class="text-sm text-slate-600 mt-2" x-text="selectedPhoto?.caption || 'Tidak ada keterangan foto.'"></p>
                <div class="mt-5 space-y-2 text-sm">
                    <p><span class="text-slate-400">Uploader:</span> <b x-text="selectedPhoto?.uploader"></b></p>
                    <p><span class="text-slate-400">Album:</span> <button @click="goToAlbum(selectedPhoto?.album_id)" type="button" class="font-black text-red-600 hover:underline" x-text="selectedPhoto?.album"></button></p>
                    <p><span class="text-slate-400">Tanggal:</span> <b x-text="selectedPhoto?.date"></b></p>
                    <p><span class="text-slate-400">Ukuran:</span> <b x-text="selectedPhoto?.size"></b></p>
                </div>
                <div class="mt-4 sm:mt-5 grid grid-cols-[1fr_1fr_auto] items-center gap-2">
                    <button x-show="canInteract" @click="toggleLove(selectedPhoto, $event)" type="button" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-black transition-colors" :class="selectedPhoto?.liked_by_me ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-slate-100 text-slate-700 hover:bg-red-50 hover:text-red-700'">
                        <svg class="w-5 h-5 transition-transform" :class="selectedPhoto?.liked_by_me ? 'fill-current scale-110' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        <span x-text="selectedPhoto?.likes_count || 0"></span>
                    </button>
                    <div class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-center text-sm font-black text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span x-text="selectedPhoto?.comments_count || 0"></span>
                    </div>
                    <a :href="selectedPhoto?.download_url" class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-950 hover:text-white flex items-center justify-center" title="Download foto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/></svg>
                    </a>
                </div>
                <div class="mt-5 sm:mt-6 border-t border-slate-200 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-outfit text-lg font-black">Komentar</h3>
                        <span class="text-xs font-bold text-slate-400" x-text="(selectedPhoto?.comments_count || 0) + ' diskusi'"></span>
                    </div>
                    <template x-if="canInteract">
                        <form @submit.prevent="submitComment($event)" class="mb-4">
                            <textarea name="body" required rows="3" maxlength="1000" class="w-full rounded-2xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Tulis komentar untuk foto ini..."></textarea>
                            <div class="mt-2 flex justify-end">
                                <button class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-xs font-black text-white hover:bg-red-600 disabled:opacity-60" :disabled="commentBusy">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim
                                </button>
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
                                    <div class="flex items-center gap-2">
                                        <button x-show="canInteract" @click="setReply(comment)" type="button" class="inline-flex items-center gap-1 text-[11px] font-black text-slate-500 hover:text-red-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6 6-6"/></svg>
                                            Balas
                                        </button>
                                        <button x-show="comment.can_delete" @click="deleteComment(comment)" type="button" class="text-[11px] font-black text-red-600 hover:text-red-800">Hapus</button>
                                    </div>
                                </div>
                                <p class="mt-2 whitespace-pre-line text-sm text-slate-700" x-text="comment.body"></p>
                                <template x-if="replyTarget?.id === comment.id">
                                    <form @submit.prevent="submitComment($event, comment.id)" class="mt-3 rounded-2xl border border-red-100 bg-white p-3">
                                        <div class="mb-2 flex items-center justify-between gap-2">
                                            <p class="text-xs font-black text-red-600">Balas komentar <span x-text="comment.author"></span></p>
                                            <button @click="replyTarget = null" type="button" class="text-xs font-black text-slate-400 hover:text-slate-700">Batal</button>
                                        </div>
                                        <textarea name="body" required rows="2" maxlength="1000" class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Tulis balasan..."></textarea>
                                        <div class="mt-2 flex justify-end">
                                            <button class="rounded-xl bg-red-600 px-4 py-2 text-xs font-black text-white hover:bg-red-700 disabled:opacity-60" :disabled="commentBusy">Kirim Balasan</button>
                                        </div>
                                    </form>
                                </template>
                                <div x-show="(comment.replies || []).length" class="mt-3 space-y-2 border-l-2 border-slate-200 pl-3">
                                    <template x-for="reply in (comment.replies || [])" :key="reply.id">
                                        <div class="rounded-2xl bg-white p-3 border border-slate-100">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-black text-slate-900 truncate" x-text="reply.author"></p>
                                                    <p class="text-[11px] font-bold text-slate-400" x-text="reply.date"></p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button x-show="canInteract" @click="setReply(comment)" type="button" class="text-[11px] font-black text-slate-500 hover:text-red-600">Balas</button>
                                                    <button x-show="reply.can_delete" @click="deleteComment(reply)" type="button" class="text-[11px] font-black text-red-600 hover:text-red-800">Hapus</button>
                                                </div>
                                            </div>
                                            <p class="mt-2 whitespace-pre-line text-sm text-slate-700" x-text="reply.body"></p>
                                        </div>
                                    </template>
                                </div>
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
        <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm p-0 sm:p-4">
            <div class="min-h-full flex items-end sm:items-center justify-center">
                <form action="{{ route('gallery-photo.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-2xl rounded-t-[28px] sm:rounded-3xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="p-5 sm:p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h2 class="font-outfit text-xl sm:text-2xl font-black">Tambah Foto Galeri</h2>
                            <p class="text-sm text-slate-500">Upload sampai 20 foto, format JPG, PNG, atau WEBP.</p>
                        </div>
                        <button @click="uploadOpen = false" type="button" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-5 sm:p-6 space-y-5 max-h-[70vh] overflow-y-auto">
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
                    <div class="p-5 sm:p-6 bg-slate-50 flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" @click="uploadOpen = false" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">Batal</button>
                        <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Upload Foto</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function galleryPage(albums, photos, canInteract, profile) {
            return {
                albums,
                photos,
                canInteract,
                profile,
                profileOpen: false,
                searchQuery: '',
                searchOpen: false,
                selectedAlbum: null,
                selectedPhoto: null,
                featuredPhoto: null,
                lightboxOpen: false,
                uploadOpen: false,
                uploadCount: 0,
                commentBusy: false,
                replyTarget: null,
                zoom: 1,
                panX: 0,
                panY: 0,
                isDragging: false,
                dragStartX: 0,
                dragStartY: 0,
                dragOriginX: 0,
                dragOriginY: 0,
                init() {
                    this.featuredPhoto = this.photos[0] || null;
                    this.selectedPhoto = this.photos[0] || null;
                },
                get searchResults() {
                    const q = this.searchQuery.trim().toLowerCase();
                    if (!q) return [];
                    return this.photos.filter(photo => [
                        photo.title,
                        photo.caption,
                        photo.album,
                        photo.uploader,
                    ].filter(Boolean).some(value => String(value).toLowerCase().includes(q))).slice(0, 8);
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
                    this.replyTarget = null;
                    this.resetZoom();
                    this.lightboxOpen = true;
                },
                updatePhoto(photo) {
                    const idx = this.photos.findIndex(item => item.id === photo.id);
                    if (idx !== -1) this.photos.splice(idx, 1, photo);
                    if (this.selectedPhoto?.id === photo.id) this.selectedPhoto = photo;
                    if (this.featuredPhoto?.id === photo.id) this.featuredPhoto = photo;
                },
                async requestJson(url, options = {}) {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            ...(options.headers || {}),
                        },
                    });

                    if (!response.ok) throw new Error('Request gagal');
                    return response.json();
                },
                async toggleLove(photo, event) {
                    if (!photo || !this.canInteract) return;
                    this.burstAt(event);
                    const data = await this.requestJson(photo.love_url, { method: 'POST' });
                    this.updatePhoto(data.photo);
                },
                burstAt(event) {
                    const burst = document.createElement('span');
                    burst.className = 'love-burst';
                    const rect = event.currentTarget.getBoundingClientRect();
                    burst.style.left = (rect.left + rect.width / 2) + 'px';
                    burst.style.top = (rect.top + rect.height / 2) + 'px';
                    document.body.appendChild(burst);
                    setTimeout(() => burst.remove(), 700);
                },
                async submitComment(event, parentId = null) {
                    if (!this.selectedPhoto || this.commentBusy) return;
                    const form = event.currentTarget;
                    const body = form.body.value.trim();
                    if (!body) return;

                    this.commentBusy = true;
                    try {
                        const payload = new FormData();
                        payload.append('body', body);
                        if (parentId) payload.append('parent_id', parentId);

                        const data = await this.requestJson(this.selectedPhoto.comment_url, {
                            method: 'POST',
                            body: payload,
                        });
                        this.updatePhoto(data.photo);
                        form.reset();
                        this.replyTarget = null;
                    } finally {
                        this.commentBusy = false;
                    }
                },
                async deleteComment(comment) {
                    if (!comment || !confirm('Hapus komentar ini?')) return;
                    const data = await this.requestJson(comment.delete_url, { method: 'DELETE' });
                    this.updatePhoto(data.photo);
                },
                setReply(comment) {
                    this.replyTarget = comment;
                },
                zoomIn() {
                    this.zoom = Math.min(3, Number((this.zoom + 0.25).toFixed(2)));
                },
                zoomOut() {
                    this.zoom = Math.max(0.5, Number((this.zoom - 0.25).toFixed(2)));
                    if (this.zoom <= 1) {
                        this.panX = 0;
                        this.panY = 0;
                    }
                },
                resetZoom() {
                    this.zoom = 1;
                    this.panX = 0;
                    this.panY = 0;
                    this.isDragging = false;
                },
                dragStart(event) {
                    if (this.zoom <= 1) return;
                    this.isDragging = true;
                    this.dragStartX = event.clientX;
                    this.dragStartY = event.clientY;
                    this.dragOriginX = this.panX;
                    this.dragOriginY = this.panY;
                },
                dragMove(event) {
                    if (!this.isDragging) return;
                    this.panX = this.dragOriginX + event.clientX - this.dragStartX;
                    this.panY = this.dragOriginY + event.clientY - this.dragStartY;
                },
                dragEnd() {
                    this.isDragging = false;
                },
                goToAlbum(albumId) {
                    if (!albumId) return;
                    this.selectAlbum(albumId);
                    this.lightboxOpen = false;
                    this.searchOpen = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
            }
        }
    </script>
</body>
</html>
