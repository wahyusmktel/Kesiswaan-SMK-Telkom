@extends('notted.app')

@section('content')
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

            <!-- Inline Comment Box -->
            <div class="px-8 py-4 bg-white border-t border-slate-50">
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
@endsection
