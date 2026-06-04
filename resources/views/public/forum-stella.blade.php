<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forum Stella - SMK Telkom Lampung</title>
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
        .forum-grid { background-image: linear-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, 0.04) 1px, transparent 1px); background-size: 28px 28px; }
    </style>
</head>
<body class="min-h-screen antialiased">
    @guest
        <main class="min-h-screen overflow-hidden bg-slate-950 text-white">
            <header class="relative z-10 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white p-1.5">
                            @if($appSetting?->logo)
                                <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="h-full w-full object-contain">
                            @else
                                <div class="h-full w-full rounded-xl bg-red-600"></div>
                            @endif
                        </div>
                        <div>
                            <p class="font-outfit text-lg font-black leading-none">Forum Stella</p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">SMK Telkom Lampung</p>
                        </div>
                    </a>
                    <a href="{{ route('login') }}" class="rounded-xl border border-white/15 px-4 py-2 text-sm font-bold text-slate-200 hover:bg-white hover:text-slate-950">Masuk</a>
                </div>
            </header>

            <section class="forum-grid relative">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(239,68,68,0.24),transparent_30%),radial-gradient(circle_at_80%_20%,rgba(34,211,238,0.18),transparent_28%),linear-gradient(180deg,rgba(15,23,42,0)_0%,#020617_100%)]"></div>
                <div class="relative mx-auto grid min-h-[calc(100vh-77px)] max-w-7xl items-center gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1fr_430px] lg:px-8">
                    <div class="max-w-3xl">
                        <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-4 py-2 text-xs font-black uppercase tracking-[0.22em] text-cyan-200">
                            Ruang diskusi terbuka
                        </div>
                        <h1 class="font-outfit text-4xl font-black leading-tight tracking-tight sm:text-6xl lg:text-7xl">Tempat warga sekolah bertanya, berbagi, dan saling menguatkan.</h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">Forum Stella dirancang untuk seluruh role di aplikasi: siswa, guru, wali kelas, BK, kesiswaan, operator, dan tim sekolah. Diskusi tetap rapi, mudah dipindai, dan fokus pada solusi.</p>
                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('forum-stella.enter') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-red-600 px-6 py-4 text-sm font-black text-white shadow-2xl shadow-red-950/40 hover:bg-red-700">
                                Masuk ke Forum
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </a>
                            <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 px-6 py-4 text-sm font-bold text-slate-200 hover:bg-white/10">Kembali ke Beranda</a>
                        </div>
                    </div>

                    <div class="rounded-[32px] border border-white/10 bg-white/10 p-4 shadow-2xl backdrop-blur-xl">
                        <div class="rounded-[24px] bg-slate-950/80 p-5">
                            <div class="mb-5 flex items-center justify-between">
                                <p class="text-xs font-black uppercase tracking-[0.22em] text-slate-400">Preview Forum</p>
                                <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-[10px] font-black text-emerald-300">Live</span>
                            </div>
                            <div class="space-y-3">
                                @foreach([
                                    ['Ask', 'Bagaimana alur izin keluar kelas yang benar?', 'Terjawab oleh Guru Piket'],
                                    ['Share', 'Template catatan mentoring mingguan', 'Dibagikan oleh Wali Kelas'],
                                    ['Info', 'Diskusi program budaya positif bulan ini', 'Aktif hari ini'],
                                ] as $item)
                                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                                        <div class="mb-2 flex items-center gap-2">
                                            <span class="rounded-full bg-cyan-400/10 px-2.5 py-1 text-[10px] font-black text-cyan-200">{{ $item[0] }}</span>
                                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $item[2] }}</span>
                                        </div>
                                        <p class="font-bold text-slate-100">{{ $item[1] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    @else
        @php
            $user = Auth::user();
            $firstName = explode(' ', trim($user->name))[0] ?: $user->name;
            $forumCategories = $categories ?? [
                'diskusi' => 'Semua Diskusi',
                'pertanyaan' => 'Pertanyaan',
                'pengumuman' => 'Pengumuman',
                'materi' => 'Berbagi Materi',
                'ide' => 'Ide Sekolah',
            ];
            $categoryColors = [
                'diskusi' => 'bg-slate-100 text-slate-700',
                'pertanyaan' => 'bg-cyan-50 text-cyan-700',
                'pengumuman' => 'bg-red-50 text-red-700',
                'materi' => 'bg-emerald-50 text-emerald-700',
                'ide' => 'bg-amber-50 text-amber-700',
            ];
            $youtubeId = function (?string $url) {
                if (!$url) return null;
                preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $matches);
                return $matches[1] ?? null;
            };
            $extractUrls = fn (?string $content) => collect(preg_match_all('/https?:\/\/[^\s<>"\']+/i', $content ?? '', $matches) ? $matches[0] : [])
                ->map(fn ($url) => rtrim($url, '.,);]'))
                ->unique()
                ->values();
            $renderForumContent = function (?string $content) {
                $html = e($content ?? '');
                $html = preg_replace('/(^|\s)#([\pL\pN_]+)/u', '$1<span class="inline-flex rounded-full bg-cyan-50 px-2 py-0.5 text-cyan-700 font-black">#$2</span>', $html);
                $html = preg_replace('/(https?:\/\/[^\s<>"\']+)/i', '<a href="$1" target="_blank" rel="noopener" class="font-bold text-red-600 hover:underline">$1</a>', $html);
                return nl2br($html);
            };
        @endphp
        <div x-data="forumStella()" class="min-h-screen bg-slate-100">
            <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl">
                <div class="mx-auto grid max-w-7xl gap-3 px-4 py-3 sm:px-6 lg:grid-cols-[260px_1fr_auto] lg:items-center lg:px-8">
                    <a href="{{ route('forum-stella.index') }}" class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <div>
                            <p class="font-outfit text-lg font-black leading-none text-slate-950">Forum Stella</p>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Semua role sekolah</p>
                        </div>
                    </a>
                    <div class="relative order-3 lg:order-none">
                        <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model.debounce.160ms="search" type="search" class="w-full rounded-2xl border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-[16px] font-semibold text-slate-700 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Cari topik, nama, atau kata kunci...">
                    </div>
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="hidden rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 sm:inline-flex">Dashboard</a>
                        <button @click="openComposer()" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-black text-white hover:bg-red-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Thread
                        </button>
                    </div>
                </div>
            </header>

            <main class="mx-auto grid max-w-7xl gap-5 px-4 py-5 sm:px-6 lg:grid-cols-[260px_1fr_320px] lg:px-8">
                <aside class="hidden lg:block">
                    <div class="sticky top-24 space-y-4">
                        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0f172a&color=ffffff' }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-2xl object-cover">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p>
                                    <p class="truncate text-xs font-bold text-red-600">{{ session('active_role') ?? $user->getRoleNames()->first() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white p-3 shadow-sm">
                            @foreach($forumCategories as $key => $topic)
                                <button type="button" @click="activeCategory = '{{ $key }}'"
                                    :class="activeCategory === '{{ $key }}' ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:bg-slate-50 hover:text-red-600'"
                                    class="flex w-full items-center justify-between rounded-2xl px-4 py-3 text-left text-sm font-bold transition-colors">
                                    <span>{{ $topic }}</span>
                                    <span :class="activeCategory === '{{ $key }}' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-500'"
                                        class="rounded-full px-2 py-0.5 text-[10px] font-black">{{ $categoryCounts[$key] ?? 0 }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <section class="space-y-5">
                    @if(session('success'))
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="mb-1 font-bold">Diskusi belum bisa diterbitkan.</p>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <section class="overflow-hidden rounded-[28px] bg-slate-950 text-white shadow-sm">
                        <div class="grid min-h-[230px] md:grid-cols-[1fr_260px]">
                            <div class="p-6 sm:p-8">
                                <p class="text-xs font-black uppercase tracking-[0.24em] text-cyan-300">Forum warga sekolah</p>
                                <h1 class="font-outfit mt-3 text-3xl font-black leading-tight sm:text-4xl">Halo, {{ $firstName }}. Mulai diskusi yang membuat sekolah bergerak.</h1>
                                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">Gunakan ruang ini untuk tanya jawab, koordinasi, berbagi ide, dan membangun budaya digital yang sehat.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-px bg-white/10 md:grid-cols-1">
                                <div class="bg-white/5 p-5">
                                    <p class="text-3xl font-black">{{ $stats['threads'] }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Thread</p>
                                </div>
                                <div class="bg-white/5 p-5">
                                    <p class="text-3xl font-black">{{ $stats['contributors'] }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kontributor</p>
                                </div>
                                <div class="bg-white/5 p-5">
                                    <p class="text-3xl font-black">{{ $stats['comments'] }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Komentar</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <button @click="openComposer()" type="button" class="flex w-full items-center gap-4 rounded-3xl border border-slate-200 bg-white p-4 text-left shadow-sm hover:border-red-200">
                        <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=ef4444&color=ffffff' }}" alt="{{ $user->name }}" class="h-11 w-11 rounded-2xl object-cover">
                        <span class="flex-1 rounded-2xl bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-500">Tulis pertanyaan atau ide untuk Forum Stella...</span>
                    </button>

                    <div class="space-y-4">
                        @forelse($posts as $post)
                            @php
                                $postCategory = $post->forum_category ?: 'diskusi';
                                $postCategoryLabel = $postCategory === 'diskusi' ? 'Diskusi Umum' : ($forumCategories[$postCategory] ?? 'Diskusi Umum');
                                $postUrls = $extractUrls($post->content);
                                $postImageUrls = $postUrls->filter(fn ($url) => preg_match('/\.(jpe?g|png|webp|gif)(\?.*)?$/i', $url))->values();
                                $postYoutubeIds = $postUrls->map(fn ($url) => $youtubeId($url))->filter()->unique()->values();
                            @endphp
                            <article x-show="matches(@js(strtolower(($post->content ?? '') . ' ' . ($post->user?->name ?? ''))), '{{ $postCategory }}')" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div class="p-5 sm:p-6">
                                    <div class="mb-5 flex items-start gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user?->name ?? 'Pengguna') }}&background=0f172a&color=fff" alt="" class="h-11 w-11 rounded-2xl object-cover">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-black text-slate-950">{{ $post->user?->name ?? 'Pengguna dihapus' }}</p>
                                            <p class="text-xs font-bold text-slate-400">{{ $post->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $categoryColors[$postCategory] ?? $categoryColors['diskusi'] }}">{{ $postCategoryLabel }}</span>
                                    </div>
                                    <div class="block w-full text-left">
                                        <div @click="openPost({{ $post->id }})" class="cursor-pointer text-[15px] leading-8 text-slate-700">{!! $renderForumContent($post->content) !!}</div>
                                        @if($post->image)
                                            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="Lampiran diskusi" class="max-h-[440px] w-full object-cover">
                                            </div>
                                        @endif
                                        @foreach($postImageUrls as $imageUrl)
                                            <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                                                <img src="{{ $imageUrl }}" alt="Embed gambar thread" class="max-h-[440px] w-full object-cover" loading="lazy">
                                            </div>
                                        @endforeach
                                        @foreach($postYoutubeIds as $videoId)
                                            <div class="mt-5 aspect-video overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                                                <iframe class="h-full w-full" src="https://www.youtube.com/embed/{{ $videoId }}" title="YouTube embed" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/70 px-5 py-3 sm:px-6">
                                    <div class="flex items-center gap-5">
                                        <button onclick="toggleLike({{ $post->id }}, 'post', this)" data-liked="{{ $post->isLikedBy($user) ? 'true' : 'false' }}" class="flex items-center gap-2 text-sm font-black {{ $post->isLikedBy($user) ? 'text-red-600' : 'text-slate-500' }} hover:text-red-600">
                                            <svg class="h-5 w-5" fill="{{ $post->isLikedBy($user) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8c-2.761 0-5 1.79-5 4s2.239 4 5 4 5-1.79 5-4-2.239-4-5-4zM4 12c0-4.418 3.582-8 8-8s8 3.582 8 8-3.582 8-8 8-8-3.582-8-8z"/></svg>
                                            <span>Beri Cendol</span>
                                            <span class="like-count">{{ $post->likes_count }}</span>
                                        </button>
                                        <button @click="openPost({{ $post->id }})" class="flex items-center gap-2 text-sm font-black text-slate-500 hover:text-cyan-700">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.86 9.86 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            {{ $post->comments_count }}
                                        </button>
                                    </div>
                                    <button @click="openPost({{ $post->id }})" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-red-600">Buka</button>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[28px] border-2 border-dashed border-slate-200 bg-white p-10 text-center">
                                <h2 class="font-outfit text-2xl font-black text-slate-950">Belum ada thread.</h2>
                                <p class="mt-2 text-sm text-slate-500">Jadilah pembuka diskusi pertama di Forum Stella.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(method_exists($posts, 'links'))
                        <div>{{ $posts->links() }}</div>
                    @endif
                </section>

                <aside class="hidden lg:block">
                    <div class="sticky top-24 space-y-4">
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                            <h2 class="font-outfit text-lg font-black">Etika Forum</h2>
                            <div class="mt-4 space-y-3 text-sm text-slate-600">
                                <p>Gunakan judul atau kalimat pembuka yang jelas.</p>
                                <p>Jaga bahasa tetap sopan, ringkas, dan solutif.</p>
                                <p>Hindari membagikan data pribadi siswa atau pegawai.</p>
                            </div>
                        </div>
                        <div class="rounded-3xl bg-red-600 p-5 text-white shadow-sm">
                            <p class="text-xs font-black uppercase tracking-[0.22em] text-red-100">Stella Connect</p>
                            <h2 class="font-outfit mt-2 text-2xl font-black">Forum untuk kolaborasi cepat.</h2>
                            <p class="mt-3 text-sm leading-6 text-red-50">Semua role bisa masuk, membaca, dan ikut berdiskusi sesuai kebutuhan sekolah.</p>
                        </div>
                    </div>
                </aside>
            </main>

            <div x-show="composerOpen" x-cloak
                @mousemove.window="dragComposerModal($event)"
                @mouseup.window="stopComposerDrag()"
                class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-sm">
                <div class="flex min-h-full items-end justify-center sm:items-center">
                    <form action="{{ route('forum-stella.posts.store') }}" method="POST" enctype="multipart/form-data"
                        :class="composerMaximized
                            ? 'h-[calc(100vh-2rem)] w-full max-w-[calc(100vw-2rem)] rounded-[28px]'
                            : 'w-full max-w-2xl rounded-t-[28px] sm:rounded-[28px]'"
                        :style="composerMaximized ? '' : `transform: translate(${composerOffset.x}px, ${composerOffset.y}px);`"
                        class="overflow-hidden bg-white shadow-2xl transition-[width,height,max-width,border-radius] duration-200">
                        @csrf
                        <div @mousedown="startComposerDrag($event)"
                            :class="composerMaximized ? 'cursor-default' : 'cursor-move'"
                            class="flex items-start justify-between gap-4 border-b border-slate-200 p-5 sm:p-6">
                            <div>
                                <h2 class="font-outfit text-2xl font-black text-slate-950">Buat Thread Forum</h2>
                                <p class="text-sm text-slate-500">Tulis pertanyaan, ide, atau informasi yang relevan.</p>
                                <p x-show="!composerMaximized" class="mt-1 hidden text-xs font-semibold text-slate-400 sm:block">Klik tahan header untuk menggeser modal.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="toggleComposerMaximized()" @mousedown.stop type="button"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200"
                                    :aria-label="composerMaximized ? 'Kecilkan modal' : 'Besarkan modal'">
                                    <svg x-show="!composerMaximized" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/></svg>
                                    <svg x-show="composerMaximized" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4"/></svg>
                                </button>
                                <button @click="composerOpen = false; stopComposerDrag()" @mousedown.stop type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div :class="composerMaximized ? 'max-h-[calc(100vh-206px)]' : 'max-h-[65vh]'"
                            class="space-y-4 overflow-y-auto p-5 sm:p-6">
                            <div>
                                <label class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">Kategori Thread</label>
                                <select name="forum_category" required class="w-full rounded-2xl border-slate-300 text-sm font-bold text-slate-700 focus:border-red-500 focus:ring-red-500">
                                    @foreach($forumCategories as $key => $label)
                                        <option value="{{ $key }}">{{ $key === 'diskusi' ? 'Diskusi Umum' : $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <textarea name="content" required maxlength="3000" rows="7" class="w-full rounded-2xl border-slate-300 text-sm leading-7 focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Ada saran untuk kegiatan literasi digital minggu ini?"></textarea>
                            <div class="grid gap-3 rounded-2xl border border-cyan-100 bg-cyan-50 p-4 text-xs font-semibold text-cyan-900 sm:grid-cols-3">
                                <div>
                                    <p class="font-black uppercase tracking-widest text-cyan-700">Hashtag</p>
                                    <p class="mt-1">Tulis #literasi atau #kelas agar tampil sebagai tag.</p>
                                </div>
                                <div>
                                    <p class="font-black uppercase tracking-widest text-cyan-700">Embed Gambar</p>
                                    <p class="mt-1">Tempel link gambar JPG/PNG/WEBP/GIF di isi thread.</p>
                                </div>
                                <div>
                                    <p class="font-black uppercase tracking-widest text-cyan-700">Embed YouTube</p>
                                    <p class="mt-1">Tempel link YouTube biasa, shorts, atau youtu.be.</p>
                                </div>
                            </div>
                            <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4 hover:border-red-300 hover:bg-red-50">
                                <span class="text-sm font-bold text-slate-600" x-text="fileName || 'Lampirkan gambar pendukung (opsional)'"></span>
                                <span class="rounded-xl bg-white px-3 py-2 text-xs font-black text-slate-600 shadow-sm">Pilih File</span>
                                <input type="file" name="image" accept="image/*" class="sr-only" @change="fileName = $event.target.files[0]?.name || ''">
                            </label>
                        </div>
                        <div class="flex justify-end gap-3 bg-slate-50 p-5 sm:p-6">
                            <button @click="composerOpen = false; stopComposerDrag()" type="button" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-100">Batal</button>
                            <button type="submit" class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">Terbitkan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div x-show="detailOpen" x-cloak
                @mousemove.window="dragDetailModal($event)"
                @mouseup.window="stopDetailDrag()"
                class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/70 p-0 backdrop-blur-sm sm:p-4">
                <div class="flex min-h-full items-end justify-center sm:items-center">
                    <div
                        :class="detailMaximized
                            ? 'h-[calc(100vh-2rem)] w-full max-w-[calc(100vw-2rem)] rounded-[28px]'
                            : 'max-h-[92vh] w-full max-w-4xl rounded-t-[28px] sm:rounded-[28px]'"
                        :style="detailMaximized ? '' : `transform: translate(${detailOffset.x}px, ${detailOffset.y}px);`"
                        class="overflow-hidden bg-white shadow-2xl transition-[width,height,max-width,border-radius] duration-200">
                        <div @mousedown="startDetailDrag($event)"
                            :class="detailMaximized ? 'cursor-default' : 'cursor-move'"
                            class="flex items-center justify-between border-b border-slate-200 p-5">
                            <div>
                                <h2 class="font-outfit text-xl font-black text-slate-950">Detail Diskusi</h2>
                                <p x-show="!detailMaximized" class="mt-1 hidden text-xs font-semibold text-slate-400 sm:block">Klik tahan header untuk menggeser modal.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="toggleDetailMaximized()" @mousedown.stop type="button"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200"
                                    :aria-label="detailMaximized ? 'Kecilkan modal' : 'Besarkan modal'">
                                    <svg x-show="!detailMaximized" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4"/></svg>
                                    <svg x-show="detailMaximized" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 4v4H4M16 4v4h4M8 20v-4H4M16 20v-4h4"/></svg>
                                </button>
                                <button @click="detailOpen = false; stopDetailDrag()" @mousedown.stop type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        <div :class="detailMaximized ? 'max-h-[calc(100vh-104px)]' : 'max-h-[calc(92vh-82px)]'"
                            class="grid overflow-y-auto lg:grid-cols-[1fr_340px]">
                            <div class="border-b border-slate-200 p-5 lg:border-b-0 lg:border-r">
                                <div x-show="loading" class="py-12 text-center text-sm font-bold text-slate-400">Memuat diskusi...</div>
                                <template x-if="selectedPost">
                                    <div>
                                        <div class="mb-5 flex items-center gap-3">
                                            <img :src="avatarUrl(selectedPost.user?.name || 'Pengguna')" alt="" class="h-11 w-11 rounded-2xl">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-black text-slate-950" x-text="selectedPost.user?.name || 'Pengguna'"></p>
                                                <p class="text-xs font-bold text-slate-400" x-text="formatDate(selectedPost.created_at)"></p>
                                            </div>
                                            <span class="rounded-full bg-cyan-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-cyan-700" x-text="categoryLabel(selectedPost.forum_category)"></span>
                                        </div>
                                        <div class="text-[15px] leading-8 text-slate-700" x-html="renderContent(selectedPost.content)"></div>
                                        <template x-if="selectedPost.image">
                                            <img :src="'/storage/' + selectedPost.image" alt="Lampiran diskusi" class="mt-5 max-h-[460px] w-full rounded-2xl object-cover">
                                        </template>
                                        <template x-for="imageUrl in imageEmbeds(selectedPost.content)" :key="imageUrl">
                                            <img :src="imageUrl" alt="Embed gambar thread" class="mt-5 max-h-[460px] w-full rounded-2xl object-cover" loading="lazy">
                                        </template>
                                        <template x-for="videoId in youtubeEmbeds(selectedPost.content)" :key="videoId">
                                            <div class="mt-5 aspect-video overflow-hidden rounded-2xl border border-slate-200 bg-slate-950">
                                                <iframe class="h-full w-full" :src="'https://www.youtube.com/embed/' + videoId" title="YouTube embed" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <div class="flex flex-col">
                                <div class="border-b border-slate-200 p-5">
                                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Komentar</h3>
                                </div>
                                <div :class="detailMaximized ? 'max-h-[calc(100vh-285px)]' : 'max-h-[420px]'"
                                    class="flex-1 space-y-3 overflow-y-auto p-5">
                                    <template x-for="comment in comments" :key="comment.id">
                                        <div class="rounded-2xl bg-slate-50 p-3">
                                            <p class="text-xs font-black text-slate-950" x-text="comment.user?.name || 'Pengguna'"></p>
                                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600" x-text="comment.content"></p>
                                        </div>
                                    </template>
                                    <div x-show="!comments.length && !loading" class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada komentar.</div>
                                </div>
                                <form @submit.prevent="submitComment" class="border-t border-slate-200 bg-slate-50 p-4">
                                    <textarea x-model="commentText" required rows="3" class="w-full rounded-2xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Tulis komentar..."></textarea>
                                    <div class="mt-3 flex justify-end">
                                        <button :disabled="commentBusy" class="rounded-xl bg-slate-950 px-4 py-2 text-xs font-black text-white hover:bg-red-600 disabled:opacity-60">Kirim Komentar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            function forumStella() {
                return {
                    categories: @js($forumCategories),
                    search: '',
                    activeCategory: 'diskusi',
                    composerOpen: false,
                    composerMaximized: false,
                    composerDragging: false,
                    composerOffset: { x: 0, y: 0 },
                    composerDragStart: { x: 0, y: 0, offsetX: 0, offsetY: 0 },
                    detailOpen: false,
                    loading: false,
                    commentBusy: false,
                    fileName: '',
                    selectedPost: null,
                    comments: [],
                    commentText: '',
                    detailMaximized: false,
                    detailDragging: false,
                    detailOffset: { x: 0, y: 0 },
                    detailDragStart: { x: 0, y: 0, offsetX: 0, offsetY: 0 },
                    matches(text, category = 'diskusi') {
                        const q = this.search.trim().toLowerCase();
                        const categoryOk = this.activeCategory === 'diskusi' || category === this.activeCategory;
                        return categoryOk && (!q || text.includes(q));
                    },
                    avatarUrl(name) {
                        return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=0f172a&color=fff';
                    },
                    formatDate(value) {
                        return new Date(value).toLocaleString('id-ID');
                    },
                    categoryLabel(category) {
                        return (category || 'diskusi') === 'diskusi' ? 'Diskusi Umum' : (this.categories[category] || 'Diskusi Umum');
                    },
                    escapeHtml(value) {
                        return String(value || '').replace(/[&<>"']/g, char => ({
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;',
                        }[char]));
                    },
                    urlsFromContent(content) {
                        return [...new Set((String(content || '').match(/https?:\/\/[^\s<>"']+/gi) || [])
                            .map(url => url.replace(/[.,);\\]]+$/g, '')))];
                    },
                    imageEmbeds(content) {
                        return this.urlsFromContent(content).filter(url => /\.(jpe?g|png|webp|gif)(\?.*)?$/i.test(url));
                    },
                    youtubeEmbeds(content) {
                        return [...new Set(this.urlsFromContent(content).map(url => {
                            const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
                            return match ? match[1] : null;
                        }).filter(Boolean))];
                    },
                    renderContent(content) {
                        return this.escapeHtml(content)
                            .replace(/(^|\s)#([\p{L}\p{N}_]+)/gu, '$1<span class="inline-flex rounded-full bg-cyan-50 px-2 py-0.5 text-cyan-700 font-black">#$2</span>')
                            .replace(/(https?:\/\/[^\s<>"']+)/gi, '<a href="$1" target="_blank" rel="noopener" class="font-bold text-red-600 hover:underline">$1</a>')
                            .replace(/\n/g, '<br>');
                    },
                    openComposer() {
                        this.composerOpen = true;
                        this.composerMaximized = false;
                        this.composerOffset = { x: 0, y: 0 };
                        this.stopComposerDrag();
                    },
                    toggleComposerMaximized() {
                        this.stopComposerDrag();
                        this.composerMaximized = !this.composerMaximized;
                    },
                    startComposerDrag(event) {
                        if (this.composerMaximized || window.innerWidth < 640 || event.button !== 0) return;
                        this.composerDragging = true;
                        this.composerDragStart = {
                            x: event.clientX,
                            y: event.clientY,
                            offsetX: this.composerOffset.x,
                            offsetY: this.composerOffset.y,
                        };
                        document.body.classList.add('select-none');
                    },
                    dragComposerModal(event) {
                        if (!this.composerDragging) return;
                        const maxX = Math.round(window.innerWidth * 0.35);
                        const maxY = Math.round(window.innerHeight * 0.35);
                        const nextX = this.composerDragStart.offsetX + event.clientX - this.composerDragStart.x;
                        const nextY = this.composerDragStart.offsetY + event.clientY - this.composerDragStart.y;
                        this.composerOffset = {
                            x: Math.max(-maxX, Math.min(maxX, nextX)),
                            y: Math.max(-maxY, Math.min(maxY, nextY)),
                        };
                    },
                    stopComposerDrag() {
                        this.composerDragging = false;
                        document.body.classList.remove('select-none');
                    },
                    toggleDetailMaximized() {
                        this.stopDetailDrag();
                        this.detailMaximized = !this.detailMaximized;
                    },
                    startDetailDrag(event) {
                        if (this.detailMaximized || window.innerWidth < 640 || event.button !== 0) return;
                        this.detailDragging = true;
                        this.detailDragStart = {
                            x: event.clientX,
                            y: event.clientY,
                            offsetX: this.detailOffset.x,
                            offsetY: this.detailOffset.y,
                        };
                        document.body.classList.add('select-none');
                    },
                    dragDetailModal(event) {
                        if (!this.detailDragging) return;
                        const maxX = Math.round(window.innerWidth * 0.35);
                        const maxY = Math.round(window.innerHeight * 0.35);
                        const nextX = this.detailDragStart.offsetX + event.clientX - this.detailDragStart.x;
                        const nextY = this.detailDragStart.offsetY + event.clientY - this.detailDragStart.y;
                        this.detailOffset = {
                            x: Math.max(-maxX, Math.min(maxX, nextX)),
                            y: Math.max(-maxY, Math.min(maxY, nextY)),
                        };
                    },
                    stopDetailDrag() {
                        this.detailDragging = false;
                        document.body.classList.remove('select-none');
                    },
                    async openPost(postId) {
                        this.detailOpen = true;
                        this.detailMaximized = false;
                        this.detailOffset = { x: 0, y: 0 };
                        this.stopDetailDrag();
                        this.loading = true;
                        this.selectedPost = null;
                        this.comments = [];
                        try {
                            const response = await fetch('/notted/posts/' + postId, { headers: { 'Accept': 'application/json' } });
                            const post = await response.json();
                            this.selectedPost = post;
                            this.comments = post.comments || [];
                        } finally {
                            this.loading = false;
                        }
                    },
                    async submitComment() {
                        if (!this.selectedPost || !this.commentText.trim()) return;
                        this.commentBusy = true;
                        try {
                            const response = await fetch('/notted/posts/' + this.selectedPost.id + '/comment', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({ content: this.commentText.trim(), parent_id: null }),
                            });
                            if (response.ok) {
                                const comment = await response.json();
                                this.comments.unshift(comment);
                                this.commentText = '';
                            }
                        } finally {
                            this.commentBusy = false;
                        }
                    },
                };
            }

            async function toggleLike(id, type, button) {
                const response = await fetch('/notted/toggle-like', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ id, type })
                });
                if (!response.ok) return;
                const data = await response.json();
                const icon = button.querySelector('svg');
                const count = button.querySelector('.like-count');
                count.innerText = data.count;
                if (data.status === 'liked') {
                    button.classList.remove('text-slate-500');
                    button.classList.add('text-red-600');
                    icon.setAttribute('fill', 'currentColor');
                } else {
                    button.classList.remove('text-red-600');
                    button.classList.add('text-slate-500');
                    icon.setAttribute('fill', 'none');
                }
            }
        </script>
    @endguest
</body>
</html>
