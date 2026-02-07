<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NOTTED App - Network of Telkom Digital Minds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        .mention-link {
            @apply font-black text-indigo-600 hover:text-indigo-700 transition-colors;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notted-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        .notted-card {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .notted-card:hover {
            border-color: #6366f1;
            box-shadow: 0 10px 30px -10px rgba(99, 102, 241, 0.1);
        }

        .sidebar-item-active {
            background: #6366f1;
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .reply-card {
            border-left: 2px solid #e2e8f0;
            margin-left: 1rem;
            padding-left: 1rem;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased">

    <!-- Custom NOTTED Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Search -->
                <div class="flex items-center gap-8 w-full max-w-2xl">
                    <a href="{{ route('notted.landing') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-8 h-8 notted-gradient rounded-lg flex items-center justify-center font-bold text-white shadow-lg transition-transform group-hover:scale-110">
                            N</div>
                        <span class="text-xl font-bold tracking-tighter text-slate-800">NOTTED</span>
                    </a>

                    <div class="hidden md:flex flex-1 relative">
                        <input type="text" placeholder="Cari di Notted..."
                            class="w-full bg-slate-100 border-none rounded-2xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- User Actions -->
                <div class="flex items-center gap-4">
                    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-all relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span
                            class="absolute top-2 right-2 w-2 h-2 bg-pink-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                    <div class="flex items-center gap-3 pl-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                {{ session('active_role') }}
                            </p>
                        </div>
                        <a href="{{ route('notted.profile', Auth::id()) }}" class="w-10 h-10 rounded-xl bg-indigo-100 border border-indigo-200 overflow-hidden block">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Sidebar -->
            <div class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 flex flex-col gap-4">
                    <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-sm">
                        <div class="flex flex-col gap-1">
                            <a href="#"
                                class="sidebar-item-active flex items-center gap-4 px-4 py-3 rounded-2xl font-bold transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                                </svg>
                                Beranda
                            </a>
                            <a href="#"
                                class="flex items-center gap-4 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-2xl font-semibold transition-all group">
                                <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Reels
                            </a>
                            <a href="#"
                                class="flex items-center gap-4 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-2xl font-semibold transition-all group">
                                <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Kelas Saya
                            </a>
                            <a href="#"
                                class="flex items-center gap-4 px-4 py-3 text-slate-600 hover:bg-slate-50 rounded-2xl font-semibold transition-all group">
                                <svg class="w-6 h-6 text-slate-400 group-hover:text-indigo-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                </svg>
                                Pengumuman
                            </a>
                            <a href="{{ route('notted.typing-test') }}"
                                class="{{ request()->routeIs('notted.typing-test') ? 'sidebar-item-active' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-4 px-4 py-3 rounded-2xl font-semibold transition-all group">
                                <svg class="w-6 h-6 {{ request()->routeIs('notted.typing-test') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-500' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Test Mengetik
                            </a>
                        </div>
                    </div>

                    <div class="bg-indigo-600 rounded-3xl p-6 text-white overflow-hidden relative group">
                        <div class="relative z-10">
                            <h4 class="font-bold mb-2">Sisfo Connect</h4>
                            <p class="text-xs text-indigo-100 mb-4 leading-relaxed">Kembali ke sistem utama sekolah
                                untuk manajemen akademik.</p>
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded-xl text-xs font-black uppercase transition-transform hover:scale-105">
                                Dashboard Sisfo
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                        <div
                            class="absolute -bottom-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle: Stories & Feed -->
            <div class="col-span-1 lg:col-span-6 flex flex-col gap-6">
                <!-- Stories -->
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                    @for ($i = 0; $i <= 6; $i++)
                        <a href="#" class="flex-shrink-0 flex flex-col items-center gap-2 group cursor-pointer hover:scale-105 transition-transform">
                            <div class="relative w-16 h-16 rounded-2xl p-0.5 notted-gradient">
                                <div class="w-full h-full bg-slate-200 rounded-[14px] overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name=User+{{ $i }}&background=random"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">User {{ $i }}</span>
                        </a>
                    @endfor
                </div>

                <!-- Create Post -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <form action="{{ route('notted.posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex gap-4 items-start mb-4">
                            <a href="{{ route('notted.profile', Auth::id()) }}" class="w-12 h-12 rounded-2xl bg-slate-100 overflow-hidden flex-shrink-0 block">
                                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                    alt="">
                            </a>
                            <textarea name="content" required
                                placeholder="Apa cerita digitalmu hari ini, {{ explode(' ', Auth::user()->name)[0] }}?"
                                class="flex-1 bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-500 min-h-[100px] resize-none"></textarea>
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview-container"
                            class="hidden mb-4 rounded-2xl overflow-hidden border border-slate-100 relative group">
                            <img src="" id="image-preview" class="w-full h-auto max-h-80 object-cover">
                            <button type="button" onclick="removeImage()"
                                class="absolute top-2 right-2 p-2 bg-black/50 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                            <div class="flex gap-2">
                                <label
                                    class="cursor-pointer flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
                                    <input type="file" name="image" id="post-image" class="hidden" accept="image/*"
                                        onchange="previewImage(event)">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    FOTO / MEDIA
                                </label>
                            </div>
                            <button type="submit"
                                class="px-8 py-3 notted-gradient text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                                Posting Sekarang
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Feed -->
                @forelse ($posts as $post)
                    <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden notted-card"
                        id="post-card-{{ $post->id }}">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <a href="{{ route('notted.profile', $post->user_id) }}" class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden block hover:scale-105 transition-transform border border-slate-100">
                                    <img
                                        src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=6366f1&color=fff">
                                </a>
                                <div>
                                    <a href="{{ route('notted.profile', $post->user_id) }}" class="text-sm font-bold text-slate-900 leading-none mb-1 hover:text-indigo-600 transition-colors block">{{ $post->user->name }}
                                    </a>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $post->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <div class="cursor-pointer" onclick="openPostModal({{ $post->id }})">
                                <div class="text-slate-600 text-sm leading-relaxed mb-6 whitespace-pre-line">
                                    {{ $post->content }}
                                </div>
                                @if ($post->image)
                                    <div
                                        class="rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 max-h-[500px] flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $post->image) }}"
                                            class="w-full h-auto object-contain">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Post Stats & Actions -->
                        <div class="px-8 py-4 bg-slate-50/50 flex justify-between items-center border-t border-slate-100">
                            <div class="flex gap-6">
                                <button onclick="toggleLike({{ $post->id }}, 'post', this)"
                                    data-liked="{{ $post->isLikedBy(Auth::user()) ? 'true' : 'false' }}"
                                    class="flex items-center gap-2 {{ $post->isLikedBy(Auth::user()) ? 'text-pink-600' : 'text-slate-500' }} hover:text-pink-600 transition-all group">
                                    <svg class="w-5 h-5 group-hover:scale-125 transition-all"
                                        fill="{{ $post->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="text-sm font-bold like-count">{{ $post->likes_count }}</span>
                                </button>
                                <button onclick="focusComment({{ $post->id }})"
                                    class="flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-all group">
                                    <svg class="w-5 h-5 group-hover:scale-125 transition-all" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span class="text-sm font-bold">{{ $post->comments_count }}</span>
                                </button>
                            </div>
                            <button class="text-slate-400 hover:text-indigo-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6a3 3 0 106.632-3.316m0 0a3 3 0 100 6.632" />
                                </svg>
                            </button>
                        </div>

                        <!-- Inline Comment Box (Facebook style) -->
                        <div class="px-8 py-4 bg-white border-t border-slate-50">
                            <!-- Inline Reply Indicator -->
                            <div id="inline-reply-indicator-{{ $post->id }}" class="hidden mb-2 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-xl flex flex-col gap-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] text-indigo-600 font-black uppercase tracking-widest">Membalas komentar...</span>
                                    <button onclick="cancelInlineReply({{ $post->id }})" class="text-indigo-400 hover:text-indigo-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p id="inline-reply-snippet-{{ $post->id }}" class="text-[10px] text-slate-500 italic line-clamp-1"></p>
                            </div>

                            <form onsubmit="submitInlineComment(event, {{ $post->id }})" class="flex gap-3">
                                <input type="hidden" id="inline-parent-id-{{ $post->id }}" value="">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex-shrink-0 overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 relative">
                                    <input type="text" placeholder="Tulis komentar..."
                                        id="inline-input-{{ $post->id }}"
                                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-2 text-xs focus:ring-1 focus:ring-indigo-500 transition-all pr-10">
                                    <button type="submit" class="absolute right-2 top-1.5 p-1 text-indigo-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <!-- Latest Comments Preview -->
                            <div id="preview-comments-{{ $post->id }}" class="mt-4 space-y-3">
                                @foreach ($post->comments as $previewComment)
                                    <div class="flex gap-2 items-start animate-in fade-in slide-in-from-bottom-2 duration-300">
                                        <div class="w-6 h-6 rounded-lg bg-slate-100 flex-shrink-0 overflow-hidden">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($previewComment->user->name) }}&background=random"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="group relative flex-1">
                                            <div class="bg-slate-50 px-3 py-2 rounded-2xl text-[11px] border border-slate-100">
                                                <span class="font-bold text-slate-800">{{ $previewComment->user->name }}</span>
                                                <span class="text-slate-600 ml-1">{!! preg_replace('/(@[a-zA-Z0-9_]+)/', '<span class="mention-link">$1</span>', e(Str::limit($previewComment->content, 60))) !!}</span>
                                            </div>
                                            <!-- Inline Actions -->
                                            <div class="mt-1 flex gap-3 px-1">
                                                <button onclick="toggleLike({{ $previewComment->id }}, 'comment', this)"
                                                    class="flex items-center gap-1 text-[9px] font-bold uppercase tracking-tighter {{ $previewComment->isLikedBy(Auth::user()) ? 'text-pink-600' : 'text-slate-400' }} hover:text-pink-600 transition-colors">
                                                    <svg class="w-3 h-3" fill="{{ $previewComment->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                    <span class="like-count">{{ $previewComment->likes_count ?? 0 }}</span>
                                                </button>
                                                <button onclick="setInlineReply({{ $post->id }}, {{ $previewComment->id }}, '{{ addslashes($previewComment->user->name) }}', '{{ addslashes(Str::limit($previewComment->content, 40)) }}')"
                                                    class="flex items-center gap-1 text-[9px] font-bold uppercase tracking-tighter text-slate-400 hover:text-indigo-600 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                    Balas ({{ $previewComment->replies_count ?? 0 }})
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($post->comments_count > 3)
                                    <button onclick="openPostModal({{ $post->id }})" id="view-more-{{ $post->id }}"
                                        class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest hover:underline mt-2 block">
                                        Lihat {{ $post->comments_count - 3 }} Komentar Lainnya...
                                    </button>
                                @elseif($post->comments_count > 0)
                                    <button onclick="openPostModal({{ $post->id }})" id="view-more-{{ $post->id }}"
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-indigo-600 transition-colors mt-2 block">
                                        Lihat Selengkapnya
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-[40px] border-2 border-dashed border-slate-200 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada cerita digital</h3>
                        <p class="text-sm text-slate-500">Jadilah yang pertama untuk berbagi jejak digital positifmu hari
                            ini!</p>
                    </div>
                @endforelse

                <!-- Pagination Links -->
                <div class="mt-6">
                    {{ $posts->links() }}
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 flex flex-col gap-6">
                    <!-- User Profile Summary -->
                    <div
                        class="bg-white p-8 rounded-[40px] border border-slate-200 shadow-sm text-center relative overflow-hidden">
                        <div class="relative z-10">
                            <a href="{{ route('notted.profile', Auth::id()) }}" class="w-20 h-20 rounded-3xl notted-gradient mx-auto mb-4 p-1 block hover:scale-110 transition-transform">
                                <div class="w-full h-full bg-slate-100 rounded-[22px] overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                        class="w-full h-full object-cover">
                                </div>
                            </a>
                            <a href="{{ route('notted.profile', Auth::id()) }}" class="font-bold text-slate-800 tracking-tight hover:text-indigo-600 transition-colors block">{{ Auth::user()->name }}</a>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-6">{{ Auth::user()->email }}</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 p-3 rounded-2xl">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Post</p>
                                    <p class="text-sm font-black text-slate-800">42</p>
                                </div>
                                <div class="bg-slate-50 p-3 rounded-2xl">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Poin</p>
                                    <p class="text-sm font-black text-indigo-600">850</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -top-10 -left-10 w-24 h-24 bg-indigo-50 rounded-full blur-2xl"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Mobile Navigation Bar -->
    <div
        class="lg:hidden fixed bottom-6 left-6 right-6 h-16 glass-dark rounded-2xl flex items-center justify-around px-8 shadow-2xl z-50">
        <button class="text-white">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
        </button>
        <button class="text-slate-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
        <div
            class="w-12 h-12 notted-gradient rounded-full flex items-center justify-center text-white shadow-xl shadow-indigo-500/40 -translate-y-4 border-4 border-slate-50">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </div>
        <button class="text-slate-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
        </button>
        <button class="text-slate-400">
            <div class="w-6 h-6 rounded-lg bg-slate-100 overflow-hidden border border-slate-400/20">
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff">
            </div>
        </button>
    </div>

    <!-- Post Detail Modal -->
    <div id="postModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePostModal()"></div>
        <div
            class="absolute inset-4 md:inset-10 lg:inset-20 bg-white rounded-[40px] shadow-2xl overflow-hidden flex flex-col md:flex-row animate-in fade-in zoom-in duration-300">
            <!-- Left: Media/Content -->
            <div
                class="w-full md:w-3/5 lg:w-2/3 bg-slate-50 flex items-center justify-center p-4 md:p-12 relative overflow-y-auto">
                <button onclick="closePostModal()"
                    class="md:hidden absolute top-4 right-4 p-2 bg-white/80 rounded-full shadow-lg">
                    <svg class="w-6 h-6 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="w-full max-w-4xl">
                    <div id="modal-post-image-container"
                        class="hidden mb-8 rounded-3xl overflow-hidden shadow-2xl border border-white">
                        <img id="modal-post-image" src="" class="w-full h-auto object-contain max-h-[70vh]">
                    </div>
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-100 overflow-hidden">
                                <img id="modal-post-author-avatar" src="">
                            </div>
                            <div>
                                <h4 id="modal-post-author-name" class="text-lg font-bold text-slate-900 leading-tight">
                                </h4>
                                <p id="modal-post-time"
                                    class="text-xs font-bold text-slate-400 uppercase tracking-widest"></p>
                            </div>
                        </div>
                        <div id="modal-post-content" class="text-slate-600 text-lg leading-relaxed whitespace-pre-line">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Comments -->
            <div class="w-full md:w-2/5 lg:w-1/3 bg-white border-l border-slate-100 flex flex-col">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Diskusi Digital</h3>
                    <button onclick="closePostModal()"
                        class="hidden md:block p-2 hover:bg-slate-50 rounded-xl transition-all">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Comment List -->
                <div id="modal-comments-list" class="flex-1 overflow-y-auto p-6 flex flex-col gap-6 scrollbar-hide">
                    <!-- Comments will be injected here -->
                </div>

                <!-- Comment Input -->
                <div class="p-6 border-t border-slate-50 bg-slate-50/50">
                    <div id="replying-to-indicator" class="hidden mb-2 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-xl flex flex-col gap-1">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] text-indigo-600 font-black uppercase tracking-widest">Membalas komentar...</span>
                            <button onclick="cancelReply()" class="text-indigo-400 hover:text-indigo-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <p id="reply-snippet" class="text-[11px] text-slate-500 italic line-clamp-1"></p>
                    </div>
                    <form id="comment-form" onsubmit="submitComment(event)" class="relative">
                        <input type="hidden" id="comment-parent-id" value="">
                        <textarea id="comment-content" required placeholder="Tulis respon positifmu..."
                            class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-500 min-h-[100px] resize-none pr-16 shadow-sm"></textarea>
                        <button type="submit"
                            class="absolute bottom-4 right-4 p-3 notted-gradient text-white rounded-xl shadow-lg shadow-indigo-500/20 active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // CSRF Token Helper
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Image preview for new posts
        function previewImage(event) {
            const input = event.target;
            const previewContainer = document.getElementById('image-preview-container');
            const previewImg = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('post-image');
            const previewContainer = document.getElementById('image-preview-container');
            const previewImg = document.getElementById('image-preview');

            input.value = "";
            previewImg.src = "";
            previewContainer.classList.add('hidden');
        }

        // Like Logic
        async function toggleLike(id, type, button) {
            try {
                const response = await fetch('/notted/toggle-like', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ id, type })
                });

                if (response.ok) {
                    const data = await response.json();
                    const icon = button.querySelector('svg');
                    const text = button.querySelector('.like-count');

                    if (data.status === 'liked') {
                        button.classList.remove('text-slate-500', 'text-slate-400');
                        button.classList.add('text-pink-600');
                        icon.setAttribute('fill', 'currentColor');
                    } else {
                        button.classList.remove('text-pink-600');
                        button.classList.add('text-slate-500');
                        icon.setAttribute('fill', 'none');
                    }
                    if (text) text.innerText = data.count;
                }
            } catch (error) {
                console.error("Error toggling like:", error);
            }
        }

        // Mentions Formatting
        function formatMentions(text) {
            return text.replace(/(@[a-zA-Z0-9_]+)/g, '<span class="mention-link">$1</span>');
        }

        // Inline Comment
        function focusComment(postId) {
            const input = document.getElementById(`inline-input-${postId}`);
            if (input) {
                input.focus();
            } else {
                openPostModal(postId);
            }
        }

        function setInlineReply(postId, commentId, authorName, snippet) {
            document.getElementById(`inline-parent-id-${postId}`).value = commentId;
            document.getElementById(`inline-reply-indicator-${postId}`).classList.remove('hidden');
            document.getElementById(`inline-reply-snippet-${postId}`).innerText = snippet + (snippet.length >= 40 ? '...' : '');
            
            const input = document.getElementById(`inline-input-${postId}`);
            input.value = `@${authorName} `;
            input.focus();
        }

        function cancelInlineReply(postId) {
            document.getElementById(`inline-parent-id-${postId}`).value = "";
            document.getElementById(`inline-reply-indicator-${postId}`).classList.add('hidden');
            document.getElementById(`inline-reply-snippet-${postId}`).innerText = "";
        }

        async function submitInlineComment(event, postId) {
            event.preventDefault();
            const input = document.getElementById(`inline-input-${postId}`);
            const parentId = document.getElementById(`inline-parent-id-${postId}`).value;
            const content = input.value.trim();
            if (!content) return;

            input.disabled = true;

            try {
                const response = await fetch(`/notted/posts/${postId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ 
                        content,
                        parent_id: parentId || null
                    })
                });

                if (response.ok) {
                    const comment = await response.json();
                    input.value = "";
                    cancelInlineReply(postId);
                    
                    // Add to preview container
                    const container = document.getElementById(`preview-comments-${postId}`);
                    if (container) {
                        // Create element
                        const div = document.createElement('div');
                        div.className = 'flex gap-2 items-start animate-in fade-in slide-in-from-bottom-2 duration-300';
                        div.innerHTML = `
                            <div class="w-6 h-6 rounded-lg bg-slate-100 flex-shrink-0 overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random" class="w-full h-full object-cover">
                            </div>
                            <div class="group relative flex-1">
                                <div class="bg-slate-50 px-3 py-2 rounded-2xl text-[11px] border border-slate-100">
                                    <span class="font-bold text-slate-800">${comment.user.name}</span>
                                    <span class="text-slate-600 ml-1">${formatMentions(comment.content.length > 60 ? comment.content.substring(0, 60) + '...' : comment.content)}</span>
                                </div>
                                <div class="mt-1 flex gap-3 px-1">
                                    <button onclick="toggleLike(${comment.id}, 'comment', this)"
                                        class="flex items-center gap-1 text-[9px] font-bold uppercase tracking-tighter text-slate-400 hover:text-pink-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="like-count">0</span>
                                    </button>
                                    <button onclick="setInlineReply(${postId}, ${comment.id}, '${comment.user.name.replace(/'/g, "\\'")}', '${comment.content.substring(0, 40).replace(/'/g, "\\'")}')"
                                        class="flex items-center gap-1 text-[9px] font-bold uppercase tracking-tighter text-slate-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        Balas (0)
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Insert before the button
                        const viewMoreBtn = document.getElementById(`view-more-${postId}`);
                        if (viewMoreBtn) {
                            container.insertBefore(div, viewMoreBtn);
                        } else {
                            // If no button, add "Lihat Selengkapnya" if it's the first or more
                            container.appendChild(div);
                            const moreBtn = document.createElement('button');
                            moreBtn.id = `view-more-${postId}`;
                            moreBtn.onclick = () => openPostModal(postId);
                            moreBtn.className = 'text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-indigo-600 transition-colors mt-2 block';
                            moreBtn.innerText = 'Lihat Selengkapnya';
                            container.appendChild(moreBtn);
                        }
                    }
                }
            } catch (error) {
                console.error("Error submitting inline comment:", error);
            } finally {
                input.disabled = false;
            }
        }

        // Modal Logic
        let currentPostId = null;

        async function openPostModal(postId, replyTo = null) {
            currentPostId = postId;
            const modal = document.getElementById('postModal');
            document.body.classList.add('overflow-hidden');
            modal.classList.remove('hidden');

            // Reset modal state
            document.getElementById('modal-comments-list').innerHTML = '<div class="flex justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';

            try {
                const response = await fetch(`/notted/posts/${postId}`);
                const post = await response.json();

                // Fill post data
                document.getElementById('modal-post-author-name').innerText = post.user.name;
                document.getElementById('modal-post-author-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(post.user.name)}&background=6366f1&color=fff`;
                document.getElementById('modal-post-time').innerText = new Date(post.created_at).toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('modal-post-content').innerText = post.content;

                const imgContainer = document.getElementById('modal-post-image-container');
                const postImg = document.getElementById('modal-post-image');
                if (post.image) {
                    postImg.src = `/storage/${post.image}`;
                    imgContainer.classList.remove('hidden');
                } else {
                    imgContainer.classList.add('hidden');
                }

                // Fill comments
                renderComments(post.comments || []);
                
                // If triggered from "Balas" button in feed
                if (replyTo) {
                    setReply(replyTo.id, replyTo.name, replyTo.snippet);
                }

            } catch (error) {
                console.error("Error fetching post details:", error);
            }
        }

        function buildCommentTree(comments) {
            const map = {};
            const roots = [];
            
            // First, initialize all comments and clear existing replies
            comments.forEach(c => {
                map[c.id] = { ...c, replies: [] };
            });
            
            // Build the tree
            comments.forEach(c => {
                if (c.parent_id && map[c.parent_id]) {
                    map[c.parent_id].replies.push(map[c.id]);
                } else {
                    roots.push(map[c.id]);
                }
            });
            
            return roots;
        }

        function renderComments(comments) {
            const list = document.getElementById('modal-comments-list');
            const tree = buildCommentTree(comments);
            
            if (tree.length === 0) {
                list.innerHTML = '<div class="text-center py-12 text-slate-400 text-sm font-semibold italic">Belum ada diskusi... Jadilah yang pertama!</div>';
                return;
            }

            list.innerHTML = tree.map(comment => renderCommentItem(comment)).join('');
            list.scrollTop = 0;
        }

        function renderCommentItem(comment, depth = 0) {
            const currentUserId = {{ Auth::id() }};
            const isLiked = comment.likes && comment.likes.some(l => l.user_id === currentUserId);
            const isReply = depth > 0;
            const maxDepth = 5;
            
            let html = `
                <div class="flex flex-col gap-3 ${isReply ? `ml-${Math.min(depth * 4, 8)} border-l-2 border-slate-100 pl-4` : ''}">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex-shrink-0 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random">
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl flex-1 border border-slate-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-black text-slate-800">${comment.user.name}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase">${new Date(comment.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})}</span>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">${formatMentions(comment.content)}</p>
                            
                            <div class="mt-3 flex gap-4">
                                <button onclick="toggleLike(${comment.id}, 'comment', this)" 
                                    class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-tight ${isLiked ? 'text-pink-600' : 'text-slate-400'} hover:text-pink-600 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="like-count">${comment.likes_count || 0}</span>
                                </button>
                                <button onclick="setReply(${comment.id}, '${comment.user.name.replace(/'/g, "\\'")}', '${comment.content.substring(0, 60).replace(/'/g, "\\'")}')" class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-tight text-slate-400 hover:text-indigo-600 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                    </svg>
                                    Balas (${comment.replies_count || 0})
                                </button>
                            </div>
                        </div>
                    </div>
            `;
            
            if (comment.replies && comment.replies.length > 0) {
                html += comment.replies.map(reply => renderCommentItem(reply, depth + 1)).join('');
            }
            
            html += `</div>`;
            return html;
        }

        function setReply(commentId, authorName, snippet) {
            document.getElementById('comment-parent-id').value = commentId;
            document.getElementById('replying-to-indicator').classList.remove('hidden');
            document.getElementById('reply-snippet').innerText = snippet + (snippet.length >= 40 ? '...' : '');
            
            const contentArea = document.getElementById('comment-content');
            contentArea.value = `@${authorName} `;
            contentArea.focus();
        }

        function cancelReply() {
            document.getElementById('comment-parent-id').value = "";
            document.getElementById('replying-to-indicator').classList.add('hidden');
            document.getElementById('reply-snippet').innerText = "";
        }

        function closePostModal() {
            const modal = document.getElementById('postModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            currentPostId = null;
            cancelReply();
        }

        async function submitComment(event) {
            event.preventDefault();
            const input = document.getElementById('comment-content');
            const parentId = document.getElementById('comment-parent-id').value;
            const content = input.value.trim();
            if (!content || !currentPostId) return;

            const submitBtn = event.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');

            try {
                const response = await fetch(`/notted/posts/${currentPostId}/comment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ content, parent_id: parentId })
                });

                if (response.ok) {
                    input.value = "";
                    cancelReply();
                    // Re-fetch post to get all comments
                    const res = await fetch(`/notted/posts/${currentPostId}`);
                    const post = await res.json();
                    renderComments(post.comments || []);
                }
            } catch (error) {
                console.error("Error submitting comment:", error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
            }
        }
    </script>
</body>

</html>