@extends('notted.app')

@section('content')
<div class="col-span-1 lg:col-span-6 flex flex-col gap-6">

    <!-- Reels Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Reels</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Ekspresikan Kreativitasmu</p>
        </div>
        <button onclick="openCreateReelModal()"
            class="flex items-center gap-2 px-6 py-3 notted-gradient text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Reel
        </button>
    </div>

    <!-- Reels Grid / Vertical Feed -->
    <div id="reels-container" class="flex flex-col gap-4">
        @forelse ($reels as $reel)
            <div class="reel-card bg-slate-900 rounded-[32px] overflow-hidden relative group shadow-2xl border border-slate-800"
                data-reel-id="{{ $reel->id }}"
                style="aspect-ratio: 9/16; max-height: 85vh;">

                {{-- Video Player --}}
                <video
                    class="reel-video w-full h-full object-cover cursor-pointer"
                    src="{{ asset('storage/' . $reel->video) }}"
                    loop
                    muted
                    playsinline
                    preload="metadata"
                    onclick="togglePlayback(this)"
                    ondblclick="doubleTapLike(event, {{ $reel->id }}, this)">
                </video>

                {{-- Play/Pause Overlay --}}
                <div class="play-overlay absolute inset-0 flex items-center justify-center pointer-events-none opacity-0 transition-opacity duration-300">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>

                {{-- Double Tap Like Heart Animation --}}
                <div class="like-heart-overlay absolute inset-0 flex items-center justify-center pointer-events-none opacity-0 z-30">
                    <svg class="w-28 h-28 text-pink-500 drop-shadow-2xl" fill="currentColor" viewBox="0 0 24 24" style="filter: drop-shadow(0 0 20px rgba(236,72,153,0.6));">
                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>

                {{-- Gradient Overlays --}}
                <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-black/60 to-transparent pointer-events-none"></div>
                <div class="absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-black/80 to-transparent pointer-events-none"></div>

                {{-- Repost Badge --}}
                @if ($reel->repost_from_id && $reel->originalReel)
                    <div class="absolute top-4 left-4 flex items-center gap-2 bg-white/10 backdrop-blur-md rounded-full px-3 py-1.5 z-20">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-[10px] font-bold text-white uppercase">Repost dari {{ $reel->originalReel->user->name ?? 'Unknown' }}</span>
                    </div>
                @endif

                {{-- Sound Toggle --}}
                <button onclick="toggleMute(event, this)" class="absolute top-4 right-4 z-20 p-2.5 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4 mute-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" class="mute-line"/>
                    </svg>
                    <svg class="w-4 h-4 unmute-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                    </svg>
                </button>

                {{-- Right Action Bar --}}
                <div class="absolute right-4 bottom-32 flex flex-col gap-5 z-20">
                    {{-- Like --}}
                    <div class="flex flex-col items-center gap-1">
                        <button onclick="toggleReelLike({{ $reel->id }}, this)"
                            class="reel-like-btn p-3 bg-white/10 backdrop-blur-md rounded-full {{ $reel->isLikedBy(Auth::user()) ? 'text-pink-500' : 'text-white' }} hover:bg-white/20 transition-all hover:scale-110 active:scale-90">
                            <svg class="w-6 h-6" fill="{{ $reel->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="reel-like-count text-[10px] font-black text-white">{{ $reel->likes_count }}</span>
                    </div>

                    {{-- Comment --}}
                    <div class="flex flex-col items-center gap-1">
                        <button onclick="openReelComments({{ $reel->id }})"
                            class="p-3 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-all hover:scale-110 active:scale-90">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </button>
                        <span class="text-[10px] font-black text-white">{{ $reel->comments_count }}</span>
                    </div>

                    {{-- Repost --}}
                    <div class="flex flex-col items-center gap-1">
                        <button onclick="repostReel({{ $reel->id }}, this)"
                            class="p-3 bg-white/10 backdrop-blur-md rounded-full text-white hover:bg-white/20 transition-all hover:scale-110 active:scale-90">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                        <span class="text-[10px] font-black text-white">{{ $reel->reposts_count ?? 0 }}</span>
                    </div>

                    {{-- Views --}}
                    <div class="flex flex-col items-center gap-1">
                        <div class="p-3 bg-white/10 backdrop-blur-md rounded-full text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-white">{{ $reel->views_count }}</span>
                    </div>
                </div>

                {{-- Bottom Info --}}
                <div class="absolute bottom-0 left-0 right-16 p-6 z-20">
                    <div class="flex items-center gap-3 mb-3">
                        <a href="{{ route('notted.profile', $reel->user_id) }}" class="w-10 h-10 rounded-full bg-white/20 border-2 border-white overflow-hidden hover:scale-110 transition-transform">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($reel->user->name) }}&background=6366f1&color=fff" class="w-full h-full object-cover">
                        </a>
                        <div>
                            <a href="{{ route('notted.profile', $reel->user_id) }}" class="text-sm font-black text-white hover:text-indigo-300 transition-colors block leading-tight">{{ $reel->user->name }}</a>
                            <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">{{ $reel->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if ($reel->caption)
                        <p class="text-sm text-white/90 leading-relaxed line-clamp-2 font-medium">{{ $reel->caption }}</p>
                    @endif
                </div>

                {{-- Progress bar --}}
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-white/10 z-20">
                    <div class="reel-progress h-full notted-gradient rounded-r-full transition-all" style="width: 0%;"></div>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-[40px] border-2 border-dashed border-slate-200 text-center">
                <div class="w-24 h-24 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada Reels</h3>
                <p class="text-sm text-slate-500 mb-6">Jadilah yang pertama membagikan reel kreatifmu!</p>
                <button onclick="openCreateReelModal()"
                    class="px-8 py-3 notted-gradient text-white rounded-2xl text-xs font-black uppercase shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Upload Reel Pertamamu
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reels->links() }}
    </div>
</div>

{{-- ============================================ --}}
{{--           CREATE REEL MODAL                  --}}
{{-- ============================================ --}}
<div id="createReelModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeCreateReelModal()"></div>
    <div class="absolute inset-4 md:inset-10 lg:inset-y-16 lg:inset-x-[25%] bg-white rounded-[40px] shadow-2xl overflow-hidden flex flex-col z-10">
        {{-- Modal Header --}}
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900 tracking-tight">Buat Reel Baru</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Bagikan momenmu</p>
            </div>
            <button onclick="closeCreateReelModal()" class="p-2 hover:bg-slate-50 rounded-xl transition-all">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="flex-1 overflow-y-auto p-6">
            <form id="create-reel-form" onsubmit="submitReel(event)" class="flex flex-col gap-6">
                {{-- Video Upload Area --}}
                <div id="video-upload-area"
                    class="relative border-2 border-dashed border-slate-200 rounded-3xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-all group"
                    onclick="document.getElementById('reel-video-input').click()">
                    <input type="file" id="reel-video-input" name="video" accept="video/mp4,video/webm,video/quicktime" class="hidden" onchange="previewReelVideo(event)" required>
                    <div id="video-upload-placeholder" class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700">Klik untuk upload video</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">MP4, WEBM, MOV • Max 50MB</p>
                        </div>
                    </div>
                    <div id="video-preview-area" class="hidden">
                        <video id="reel-video-preview" class="w-full max-h-[50vh] rounded-2xl object-contain bg-black" controls></video>
                        <button type="button" onclick="removeReelVideo(event)" class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Video
                        </button>
                    </div>
                </div>

                {{-- Caption --}}
                <div>
                    <label class="text-xs font-black text-slate-600 uppercase tracking-widest mb-2 block">Caption</label>
                    <textarea name="caption" id="reel-caption" placeholder="Tulis caption yang menarik... (opsional)"
                        class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-500 min-h-[100px] resize-none" maxlength="500"></textarea>
                    <div class="flex justify-end mt-2">
                        <span id="caption-counter" class="text-[10px] font-bold text-slate-400">0/500</span>
                    </div>
                </div>
            </form>
        </div>

        {{-- Modal Footer --}}
        <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <button type="button" onclick="closeCreateReelModal()" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
                Batal
            </button>
            <button type="button" onclick="document.getElementById('create-reel-form').dispatchEvent(new Event('submit', {cancelable: true}))" id="submit-reel-btn"
                class="px-8 py-3 notted-gradient text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span id="submit-btn-text">Posting Reel</span>
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{--         COMMENTS DRAWER MODAL                --}}
{{-- ============================================ --}}
<div id="reelCommentsDrawer" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeReelComments()"></div>
    <div class="absolute bottom-0 left-0 right-0 lg:left-auto lg:right-0 lg:top-0 lg:w-[420px] bg-white rounded-t-[40px] lg:rounded-none lg:rounded-l-[40px] shadow-2xl flex flex-col z-10"
        style="max-height: 85vh; height: 85vh;">

        {{-- Drawer Header --}}
        <div class="p-6 border-b border-slate-50 flex justify-between items-center flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 notted-gradient rounded-full"></div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Komentar</h3>
            </div>
            <button onclick="closeReelComments()" class="p-2 hover:bg-slate-50 rounded-xl transition-all">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Drawer drag handle (mobile) --}}
        <div class="lg:hidden flex justify-center py-2">
            <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
        </div>

        {{-- Comments List --}}
        <div id="reel-comments-list" class="flex-1 overflow-y-auto p-6 flex flex-col gap-5 scrollbar-hide">
            <div class="flex justify-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        </div>

        {{-- Comment Input --}}
        <div class="p-5 border-t border-slate-50 bg-slate-50/50 flex-shrink-0">
            <div id="reel-reply-indicator" class="hidden mb-2 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-xl flex flex-col gap-1">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-indigo-600 font-black uppercase tracking-widest">Membalas komentar...</span>
                    <button onclick="cancelReelReply()" class="text-indigo-400 hover:text-indigo-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p id="reel-reply-snippet" class="text-[11px] text-slate-500 italic line-clamp-1"></p>
            </div>
            <form id="reel-comment-form" onsubmit="submitReelComment(event)" class="flex gap-3 items-end">
                <input type="hidden" id="reel-comment-parent-id" value="">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex-shrink-0 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=6366f1&color=fff" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 relative">
                    <input type="text" placeholder="Tulis komentar..." id="reel-comment-input"
                        class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-indigo-500 transition-all pr-12 shadow-sm">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 notted-gradient text-white rounded-lg shadow-sm active:scale-90 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{--         TOAST NOTIFICATION                   --}}
{{-- ============================================ --}}
<div id="reelToast" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[200] hidden">
    <div class="bg-slate-900/90 backdrop-blur-md text-white px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3">
        <svg id="toast-icon" class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span id="toast-message" class="text-sm font-bold"></span>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .reel-card { scroll-snap-align: start; }
    #reels-container { scroll-snap-type: y mandatory; }

    @keyframes heartBounce {
        0%   { transform: scale(0); opacity: 0; }
        15%  { transform: scale(1.3); opacity: 1; }
        30%  { transform: scale(0.95); }
        45%  { transform: scale(1.1); }
        60%  { transform: scale(1); }
        100% { transform: scale(1); opacity: 0; }
    }
    .animate-heart-bounce {
        animation: heartBounce 0.9s ease-out forwards;
    }

    @keyframes slideUp {
        from { transform: translateY(100%); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }
    #reelCommentsDrawer > div:last-child {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.3s ease-out;
    }
</style>
<script>
    // csrfToken is already defined in app.blade.php
    let currentReelId = null;
    let lastTapTime = {};

    // ==========================================
    //          VIDEO PLAYBACK
    // ==========================================

    function togglePlayback(videoEl) {
        const overlay = videoEl.closest('.reel-card').querySelector('.play-overlay');
        if (videoEl.paused) {
            videoEl.play();
            overlay.style.opacity = '0';
        } else {
            videoEl.pause();
            overlay.style.opacity = '1';
        }
    }

    function toggleMute(event, btn) {
        event.stopPropagation();
        const card = btn.closest('.reel-card');
        const video = card.querySelector('.reel-video');
        video.muted = !video.muted;
        btn.querySelector('.mute-icon').classList.toggle('hidden', !video.muted);
        btn.querySelector('.unmute-icon').classList.toggle('hidden', video.muted);
    }

    // Auto-play videos when in viewport
    const reelObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target.querySelector('.reel-video');
            if (entry.isIntersecting) {
                video.play().catch(() => {});
                trackProgress(video, entry.target);
            } else {
                video.pause();
            }
        });
    }, { threshold: 0.7 });

    document.querySelectorAll('.reel-card').forEach(card => reelObserver.observe(card));

    function trackProgress(video, card) {
        const progressBar = card.querySelector('.reel-progress');
        const update = () => {
            if (!video.paused && video.duration) {
                const pct = (video.currentTime / video.duration) * 100;
                progressBar.style.width = pct + '%';
            }
            if (!video.paused) requestAnimationFrame(update);
        };
        requestAnimationFrame(update);
    }

    // ==========================================
    //          DOUBLE TAP LIKE
    // ==========================================

    function doubleTapLike(event, reelId, videoEl) {
        event.preventDefault();
        const now = Date.now();
        const THRESHOLD = 300;

        if (lastTapTime[reelId] && (now - lastTapTime[reelId]) < THRESHOLD) {
            // Double tap detected
            const card = videoEl.closest('.reel-card');
            const heartOverlay = card.querySelector('.like-heart-overlay');

            // Animate heart
            heartOverlay.style.opacity = '1';
            heartOverlay.querySelector('svg').classList.remove('animate-heart-bounce');
            void heartOverlay.querySelector('svg').offsetWidth; // force reflow
            heartOverlay.querySelector('svg').classList.add('animate-heart-bounce');
            setTimeout(() => { heartOverlay.style.opacity = '0'; }, 900);

            // Toggle like (only like, not unlike on double tap)
            const likeBtn = card.querySelector('.reel-like-btn');
            const isAlreadyLiked = likeBtn.classList.contains('text-pink-500');
            if (!isAlreadyLiked) {
                toggleReelLike(reelId, likeBtn);
            }

            lastTapTime[reelId] = 0;
        } else {
            lastTapTime[reelId] = now;
            // Single tap → toggle play/pause
            setTimeout(() => {
                if (lastTapTime[reelId] === now) {
                    togglePlayback(videoEl);
                }
            }, THRESHOLD);
        }
    }

    // ==========================================
    //          LIKE
    // ==========================================

    async function toggleReelLike(reelId, btn) {
        try {
            const response = await fetch('/notted/toggle-like', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: reelId, type: 'reel' })
            });
            if (response.ok) {
                const data = await response.json();
                const icon = btn.querySelector('svg');
                const countEl = btn.closest('.flex.flex-col').querySelector('.reel-like-count');
                if (data.status === 'liked') {
                    btn.classList.remove('text-white');
                    btn.classList.add('text-pink-500');
                    icon.setAttribute('fill', 'currentColor');
                } else {
                    btn.classList.remove('text-pink-500');
                    btn.classList.add('text-white');
                    icon.setAttribute('fill', 'none');
                }
                if (countEl) countEl.innerText = data.count;
            }
        } catch (error) { console.error('Error toggling reel like:', error); }
    }

    // ==========================================
    //          REPOST
    // ==========================================

    async function repostReel(reelId, btn) {
        if (!confirm('Repost reel ini ke profil kamu?')) return;
        try {
            btn.disabled = true;
            const response = await fetch(`/notted/reels/${reelId}/repost`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const data = await response.json();
            if (response.ok) {
                showToast(data.message);
                // Update count
                const countEl = btn.closest('.flex.flex-col').querySelector('span');
                if (countEl) countEl.innerText = parseInt(countEl.innerText || 0) + 1;
            } else {
                showToast(data.message || 'Gagal repost', 'error');
            }
        } catch (error) {
            console.error('Error reposting:', error);
            showToast('Terjadi kesalahan', 'error');
        } finally { btn.disabled = false; }
    }

    // ==========================================
    //          COMMENTS
    // ==========================================

    async function openReelComments(reelId) {
        currentReelId = reelId;
        const drawer = document.getElementById('reelCommentsDrawer');
        drawer.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        const commentsList = document.getElementById('reel-comments-list');
        commentsList.innerHTML = '<div class="flex justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div></div>';

        try {
            const response = await fetch(`/notted/reels/${reelId}`);
            const reel = await response.json();
            renderReelComments(reel.comments || []);
        } catch (error) {
            commentsList.innerHTML = '<p class="text-center text-slate-400 text-sm">Gagal memuat komentar</p>';
        }
    }

    function closeReelComments() {
        document.getElementById('reelCommentsDrawer').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        cancelReelReply();
    }

    function renderReelComments(comments) {
        const list = document.getElementById('reel-comments-list');
        if (!comments.length) {
            list.innerHTML = `
                <div class="text-center py-12 flex flex-col items-center gap-3">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-slate-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                </div>`;
            return;
        }
        list.innerHTML = comments.map(c => renderReelCommentItem(c)).join('');
    }

    function renderReelCommentItem(comment, depth = 0) {
        const currentUserId = {{ Auth::id() }};
        const isLiked = comment.likes && comment.likes.some(l => l.user_id === currentUserId);
        let html = `
        <div class="flex flex-col gap-3 ${depth > 0 ? 'ml-6 border-l-2 border-slate-100 pl-4' : ''} animate-fade-in-up">
            <div class="flex gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=random" class="w-full h-full object-cover">
                </div>
                <div class="bg-slate-50 p-3.5 rounded-2xl flex-1 border border-slate-100">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-black text-slate-800">${comment.user.name}</span>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">${comment.content}</p>
                    <div class="mt-2.5 flex gap-4">
                        <button onclick="toggleLike(${comment.id}, 'reel_comment', this)" class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-tight ${isLiked ? 'text-pink-600' : 'text-slate-400'} hover:text-pink-600 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="${isLiked ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span class="like-count">${comment.likes_count || 0}</span>
                        </button>
                        <button onclick="setReelReply(${comment.id}, '${comment.user.name.replace(/'/g, "\\'")}', '${(comment.content || '').substring(0, 60).replace(/'/g, "\\'")}')" class="text-[10px] font-black uppercase text-slate-400 hover:text-indigo-600 transition-colors">Balas</button>
                    </div>
                </div>
            </div>`;
        if (comment.replies && comment.replies.length > 0) {
            html += comment.replies.map(reply => renderReelCommentItem(reply, depth + 1)).join('');
        }
        html += `</div>`;
        return html;
    }

    function setReelReply(commentId, authorName, snippet) {
        document.getElementById('reel-comment-parent-id').value = commentId;
        document.getElementById('reel-reply-indicator').classList.remove('hidden');
        document.getElementById('reel-reply-snippet').innerText = snippet;
        const input = document.getElementById('reel-comment-input');
        input.value = `@${authorName} `;
        input.focus();
    }

    function cancelReelReply() {
        document.getElementById('reel-comment-parent-id').value = '';
        document.getElementById('reel-reply-indicator').classList.add('hidden');
        document.getElementById('reel-reply-snippet').innerText = '';
    }

    async function submitReelComment(event) {
        event.preventDefault();
        const input = document.getElementById('reel-comment-input');
        const parentId = document.getElementById('reel-comment-parent-id').value;
        const content = input.value.trim();
        if (!content || !currentReelId) return;

        input.disabled = true;
        try {
            const response = await fetch(`/notted/reels/${currentReelId}/comment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ content, parent_id: parentId || null })
            });

            if (response.ok) {
                input.value = '';
                cancelReelReply();
                // Reload comments
                const reelResp = await fetch(`/notted/reels/${currentReelId}`);
                const reel = await reelResp.json();
                renderReelComments(reel.comments || []);
            }
        } catch (error) {
            console.error('Error submitting comment:', error);
        } finally { input.disabled = false; }
    }

    // ==========================================
    //          CREATE REEL MODAL
    // ==========================================

    function openCreateReelModal() {
        document.getElementById('createReelModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeCreateReelModal() {
        document.getElementById('createReelModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        removeReelVideo(null);
        document.getElementById('reel-caption').value = '';
        document.getElementById('caption-counter').innerText = '0/500';
    }

    function previewReelVideo(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Check file size (50MB)
        if (file.size > 50 * 1024 * 1024) {
            showToast('Ukuran video maksimal 50MB', 'error');
            event.target.value = '';
            return;
        }

        const preview = document.getElementById('reel-video-preview');
        const placeholder = document.getElementById('video-upload-placeholder');
        const previewArea = document.getElementById('video-preview-area');

        preview.src = URL.createObjectURL(file);
        placeholder.classList.add('hidden');
        previewArea.classList.remove('hidden');
    }

    function removeReelVideo(event) {
        if (event) event.stopPropagation();
        const input = document.getElementById('reel-video-input');
        const preview = document.getElementById('reel-video-preview');
        const placeholder = document.getElementById('video-upload-placeholder');
        const previewArea = document.getElementById('video-preview-area');

        input.value = '';
        preview.src = '';
        placeholder.classList.remove('hidden');
        previewArea.classList.add('hidden');
    }

    // Caption counter
    document.getElementById('reel-caption')?.addEventListener('input', function() {
        document.getElementById('caption-counter').innerText = `${this.value.length}/500`;
    });

    async function submitReel(event) {
        event.preventDefault();
        const form = document.getElementById('create-reel-form');
        const btn = document.getElementById('submit-reel-btn');
        const btnText = document.getElementById('submit-btn-text');

        const videoInput = document.getElementById('reel-video-input');
        if (!videoInput.files.length) {
            showToast('Pilih video terlebih dahulu', 'error');
            return;
        }

        btn.disabled = true;
        btnText.innerText = 'Mengupload...';

        const formData = new FormData();
        formData.append('video', videoInput.files[0]);
        formData.append('caption', document.getElementById('reel-caption').value);

        try {
            const response = await fetch('/notted/reels', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            });

            if (response.ok) {
                showToast('Reel berhasil diposting! 🎬');
                closeCreateReelModal();
                setTimeout(() => location.reload(), 800);
            } else {
                const data = await response.json();
                showToast(data.message || 'Gagal mengupload reel', 'error');
            }
        } catch (error) {
            console.error('Error uploading reel:', error);
            showToast('Terjadi kesalahan saat upload', 'error');
        } finally {
            btn.disabled = false;
            btnText.innerText = 'Posting Reel';
        }
    }

    // ==========================================
    //          TOAST NOTIFICATION
    // ==========================================

    function showToast(message, type = 'success') {
        const toast = document.getElementById('reelToast');
        const icon = document.getElementById('toast-icon');
        const msg = document.getElementById('toast-message');

        msg.innerText = message;
        if (type === 'error') {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            icon.classList.remove('text-emerald-400');
            icon.classList.add('text-red-400');
        } else {
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
            icon.classList.remove('text-red-400');
            icon.classList.add('text-emerald-400');
        }

        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
</script>
@endpush
