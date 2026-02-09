<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NOTTED - Digital Ecosystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            50: '#f5f7ff',
                            100: '#ebf0fe',
                            600: '#6366f1',
                            700: '#4f46e5'
                        }
                    }
                }
            }
        }
    </script>
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

        .notted-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .notted-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .notted-card:hover {
            transform: translateY(-4px);
            border-color: #6366f1;
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.1), 0 10px 10px -5px rgba(99, 102, 241, 0.04);
        }

        .sidebar-item-active {
            background: #6366f1;
            color: white !important;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        /* Hide scrollbar but keep functionality */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-slate-50 antialiased selection:bg-indigo-100 selection:text-indigo-700">

    <!-- Global Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-12 w-full max-w-2xl">
                    <a href="{{ route('notted.app') }}" class="flex items-center gap-3 group">
                        <div
                            class="w-10 h-10 notted-gradient rounded-2xl flex items-center justify-center font-black text-white shadow-lg transition-transform group-hover:scale-110 group-hover:rotate-3">
                            N</div>
                        <span class="text-2xl font-black tracking-tighter text-slate-800">NOTTED</span>
                    </a>
                    <div class="hidden md:flex flex-1 relative">
                        <input type="text" placeholder="Cari inspirasi, teman, atau hasil karya..."
                            class="w-full bg-slate-50 border-none rounded-2xl px-12 py-3.5 text-sm focus:ring-2 focus:ring-indigo-600 transition-all font-medium">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button class="p-3 text-slate-500 hover:bg-slate-100 rounded-2xl transition-all relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-3 right-3 w-2 h-2 bg-indigo-600 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="w-px h-8 bg-slate-200 mx-2"></div>
                    <a href="{{ route('notted.profile', Auth::id()) }}" class="flex items-center gap-3 p-1.5 hover:bg-slate-100 rounded-2xl transition-all">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 border border-indigo-200 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff"
                                alt="">
                        </div>
                        <div class="hidden lg:block pr-2">
                            <p class="text-xs font-black text-slate-800 leading-none mb-1">{{ Auth::user()->name }}</p>
                            <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest">Siswa Aktif</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left Sidebar -->
            <div class="hidden lg:block lg:col-span-3">
                <div class="sticky top-28 flex flex-col gap-4">
                    <div class="bg-white rounded-3xl p-4 border border-slate-200 shadow-sm">
                        <div class="flex flex-col gap-1">
                            <a href="{{ route('notted.app') }}"
                                class="{{ request()->routeIs('notted.app') ? 'sidebar-item-active' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-4 px-4 py-3 rounded-2xl font-bold transition-all">
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
                            <a href="{{ route('notted.millionaire.index') }}"
                                class="{{ request()->routeIs('notted.millionaire.*') ? 'sidebar-item-active' : 'text-slate-600 hover:bg-slate-50' }} flex items-center gap-4 px-4 py-3 rounded-2xl font-semibold transition-all group">
                                <svg class="w-6 h-6 {{ request()->routeIs('notted.millionaire.*') ? 'text-white' : 'text-slate-400 group-hover:text-amber-500' }}" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1a2.4 2.4 0 01.33 1.5M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1a2.4 2.4 0 01-.33-1.5M12 16V7" />
                                </svg>
                                Millionaire Game
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

            <!-- Middle Content Area -->
            @yield('content')

            <!-- Right Sidebar -->
            <div class="hidden lg:block lg:col-span-3">
                <div class="sticky top-28 flex flex-col gap-6">
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
                <div id="modal-comments-list" class="flex-1 overflow-y-auto p-6 flex flex-col gap-6 scrollbar-hide">
                </div>
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

        async function toggleLike(id, type, button) {
            try {
                const response = await fetch('/notted/toggle-like', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
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
            } catch (error) { console.error("Error toggling like:", error); }
        }

        function formatMentions(text) {
            return text.replace(/(@[a-zA-Z0-9_]+)/g, '<span class="mention-link">$1</span>');
        }

        function focusComment(postId) {
            const input = document.getElementById(`inline-input-${postId}`);
            if (input) { input.focus(); } else { openPostModal(postId); }
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
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ content, parent_id: parentId || null })
                });
                if (response.ok) {
                    const comment = await response.json();
                    input.value = "";
                    cancelInlineReply(postId);
                    const container = document.getElementById(`preview-comments-${postId}`);
                    if (container) {
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
                            </div>
                        `;
                        const viewMoreBtn = document.getElementById(`view-more-${postId}`);
                        if (viewMoreBtn) { container.insertBefore(div, viewMoreBtn); } else { container.appendChild(div); }
                    }
                }
            } catch (error) { console.error("Error submitting inline comment:", error); } finally { input.disabled = false; }
        }

        let currentPostId = null;
        async function openPostModal(postId) {
            currentPostId = postId;
            const modal = document.getElementById('postModal');
            document.body.classList.add('overflow-hidden');
            modal.classList.remove('hidden');
            document.getElementById('modal-comments-list').innerHTML = '<div class="flex justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';
            try {
                const response = await fetch(`/notted/posts/${postId}`);
                const post = await response.json();
                document.getElementById('modal-post-author-name').innerText = post.user.name;
                document.getElementById('modal-post-author-avatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(post.user.name)}&background=6366f1&color=fff`;
                document.getElementById('modal-post-time').innerText = new Date(post.created_at).toLocaleString('id-ID');
                document.getElementById('modal-post-content').innerText = post.content;
                const imgContainer = document.getElementById('modal-post-image-container');
                const postImg = document.getElementById('modal-post-image');
                if (post.image) { postImg.src = `/storage/${post.image}`; imgContainer.classList.remove('hidden'); } else { imgContainer.classList.add('hidden'); }
                renderComments(post.comments || []);
            } catch (error) { console.error("Error fetching post details:", error); }
        }

        function buildCommentTree(comments) {
            const map = {}; const roots = [];
            comments.forEach(c => { map[c.id] = { ...c, replies: [] }; });
            comments.forEach(c => { if (c.parent_id && map[c.parent_id]) { map[c.parent_id].replies.push(map[c.id]); } else { roots.push(map[c.id]); } });
            return roots;
        }

        function renderComments(comments) {
            const list = document.getElementById('modal-comments-list');
            const tree = buildCommentTree(comments);
            if (tree.length === 0) { list.innerHTML = '<div class="text-center py-12 text-slate-400 text-sm italic">Belum ada diskusi...</div>'; return; }
            list.innerHTML = tree.map(comment => renderCommentItem(comment)).join('');
        }

        function renderCommentItem(comment, depth = 0) {
            const currentUserId = {{ Auth::id() }};
            const isLiked = comment.likes && comment.likes.some(l => l.user_id === currentUserId);
            let html = `<div class="flex flex-col gap-3 ${depth > 0 ? `ml-6 border-l-2 border-slate-100 pl-4` : ''}">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden"><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random"></div>
                    <div class="bg-slate-50 p-4 rounded-2xl flex-1 border border-slate-100">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-black text-slate-800">${comment.user.name}</span>
                        </div>
                        <p class="text-sm text-slate-600 leading-relaxed">${formatMentions(comment.content)}</p>
                        <div class="mt-3 flex gap-4">
                            <button onclick="toggleLike(${comment.id}, 'comment', this)" class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-tight ${isLiked ? 'text-pink-600' : 'text-slate-400'}">
                                <span class="like-count">${comment.likes_count || 0}</span>
                            </button>
                            <button onclick="setReply(${comment.id}, '${comment.user.name.replace(/'/g, "\\'")}', '${comment.content.substring(0, 60).replace(/'/g, "\\'")}')" class="text-[10px] font-black uppercase text-slate-400">Balas</button>
                        </div>
                    </div>
                </div>`;
            if (comment.replies && comment.replies.length > 0) { html += comment.replies.map(reply => renderCommentItem(reply, depth + 1)).join(''); }
            html += `</div>`; return html;
        }

        function setReply(commentId, authorName, snippet) {
            document.getElementById('comment-parent-id').value = commentId;
            document.getElementById('replying-to-indicator').classList.remove('hidden');
            document.getElementById('reply-snippet').innerText = snippet;
            const contentArea = document.getElementById('comment-content');
            contentArea.value = `@${authorName} `; contentArea.focus();
        }

        function cancelReply() {
            document.getElementById('comment-parent-id').value = "";
            document.getElementById('replying-to-indicator').classList.add('hidden');
        }

        function closePostModal() {
            document.getElementById('postModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            cancelReply();
        }

        async function submitComment(event) {
            event.preventDefault();
            const input = document.getElementById('comment-content');
            const parentId = document.getElementById('comment-parent-id').value;
            const content = input.value.trim();
            if (!content || !currentPostId) return;
            try {
                const response = await fetch(`/notted/posts/${currentPostId}/comment`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ content, parent_id: parentId })
                });
                if (response.ok) {
                    input.value = ""; cancelReply();
                    const res = await fetch(`/notted/posts/${currentPostId}`);
                    const post = await res.json(); renderComments(post.comments || []);
                }
            } catch (error) { console.error("Error submitting comment:", error); }
        }
    </script>
    @stack('scripts')
</body>
</html>