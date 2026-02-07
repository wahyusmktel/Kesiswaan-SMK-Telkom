<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->name }} - Profile NOTTED</title>
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
    </style>
</head>

<body class="bg-slate-50 antialiased">

    <!-- Custom NOTTED Navbar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8 w-full max-w-2xl">
                    <a href="{{ route('notted.app') }}" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 notted-gradient rounded-lg flex items-center justify-center font-bold text-white shadow-lg transition-transform group-hover:scale-110">N</div>
                        <span class="text-xl font-bold tracking-tighter text-slate-800">NOTTED</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('notted.app') }}" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
                        </svg>
                    </a>
                    <a href="{{ route('notted.typing-test') }}" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                    <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 border border-indigo-200 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-[1440px] mx-auto pt-16">
        <!-- Cover Photo Area -->
        <div class="h-48 md:h-64 notted-gradient relative">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute -bottom-16 left-4 md:left-8">
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-[40px] bg-white p-1.5 shadow-2xl">
                    <div class="w-full h-full rounded-[34px] bg-slate-100 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=512" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 md:px-8 pt-20 pb-12">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Profile Info & Stats -->
                <div class="lg:w-1/3">
                    <div class="sticky top-24 space-y-6">
                        <div>
                            <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                            <p class="text-slate-500 font-medium mb-4">{{ $user->email }}</p>
                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-bold uppercase tracking-wider">Pioneer Digital Minds</span>
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase tracking-wider">Bergabung {{ $stats['joined_at'] }}</span>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Postingan</p>
                                <p class="text-2xl font-black text-slate-900 leading-none">{{ $stats['posts_count'] }}</p>
                            </div>
                            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2 tracking-widest">Love Diterima</p>
                                <p class="text-2xl font-black text-pink-600 leading-none">{{ $stats['total_likes_received'] }}</p>
                            </div>
                        </div>

                        <!-- User Bio (Mockup) -->
                        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Mengenai Saya</h4>
                            <p class="text-sm text-slate-600 leading-relaxed italic">
                                "Menjelajahi dunia digital dalam ekosistem SMK Telkom Malang. Menulis kode, berbagi ilmu, dan membangun masa depan."
                            </p>
                        </div>
                    </div>
                </div>

                <!-- User posts -->
                <div class="lg:w-2/3 space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Timeline Aktivitas</h4>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-bold uppercase">Terbaru</button>
                            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-bold uppercase">Populer</button>
                        </div>
                    </div>

                    @forelse ($posts as $post)
                        {{-- Identical post card structure to app.blade --}}
                        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden notted-card">
                            <div class="p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=6366f1&color=fff">
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 leading-none mb-1">{{ $post->user->name }}</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-slate-600 text-sm leading-relaxed mb-6 whitespace-pre-line">{{ $post->content }}</div>
                                @if ($post->image)
                                    <div class="rounded-2xl overflow-hidden border border-slate-100 bg-slate-50 max-h-[500px] flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $post->image) }}" class="w-full h-auto object-contain">
                                    </div>
                                @endif
                            </div>
                            <!-- Simple Stats View in Profile -->
                            <div class="px-8 py-4 bg-slate-50/50 flex justify-between items-center border-t border-slate-100">
                                <div class="flex gap-6">
                                    <div class="flex items-center gap-2 text-pink-600">
                                        <svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="text-sm font-bold">{{ $post->likes_count }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        <span class="text-sm font-bold">{{ $post->comments_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 rounded-[40px] border-2 border-dashed border-slate-200 text-center">
                            <p class="text-sm text-slate-500">Belum ada postingan yang dibagikan.</p>
                        </div>
                    @endforelse

                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
