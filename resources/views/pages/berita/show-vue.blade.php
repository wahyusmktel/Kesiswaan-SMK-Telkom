@php
    $schoolName = $setting?->school_name ?: 'SMK Telkom Lampung';
    $seoTitle = $berita->seo_title ?: $berita->judul;
    $seoDescription = $berita->seo_description ?: ($berita->ringkasan ?: Str::limit(strip_tags($berita->konten), 160));
    $canonicalUrl = route('berita.show', $berita->slug);
    $coverUrl = $berita->gambar_url ? url($berita->gambar_url) : asset('images/landing-vue/integrated-modules.png');
    $plainContent = trim(preg_replace('/[`#*_>\[\]\(\)-]+/', ' ', strip_tags($berita->konten)));
    $wordCount = str_word_count($plainContent);
    $readingMinutes = max(1, (int) ceil($wordCount / 200));
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $berita->judul,
        'description' => $seoDescription,
        'image' => [$coverUrl],
        'datePublished' => $berita->published_at?->toIso8601String(),
        'dateModified' => $berita->updated_at?->toIso8601String(),
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $canonicalUrl],
        'author' => ['@type' => 'Person', 'name' => $berita->author?->name ?: 'Redaksi '.$schoolName],
        'publisher' => [
            '@type' => 'Organization',
            'name' => $schoolName,
            'logo' => $setting?->logo
                ? ['@type' => 'ImageObject', 'url' => asset('storage/'.ltrim($setting->logo, '/'))]
                : null,
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $seoTitle }} | {{ $schoolName }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    @if($berita->seo_keywords)<meta name="keywords" content="{{ $berita->seo_keywords }}">@endif
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $coverUrl }}">
    <meta property="article:published_time" content="{{ $berita->published_at?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $berita->updated_at?->toIso8601String() }}">
    <meta property="article:section" content="{{ $berita->kategori }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $coverUrl }}">

    @if ($setting?->favicon)
        <link rel="icon" href="{{ asset('storage/'.ltrim($setting->favicon, '/')) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    @vite('resources/js/news-article.js')
</head>
<body class="stella-news-page">
    <header class="news-header">
        <div class="news-shell news-header__inner">
            <a class="news-brand" href="{{ route('welcome') }}">
                @if($setting?->logo)
                    <img src="{{ asset('storage/'.ltrim($setting->logo, '/')) }}" alt="Logo {{ $schoolName }}">
                @else
                    <span class="news-brand__mark">TS</span>
                @endif
                <span><strong>SISFO TS</strong><small>{{ $schoolName }}</small></span>
            </a>
            <nav class="news-nav" aria-label="Navigasi utama">
                <a href="{{ route('pengaduan.create') }}">Layanan Aduan</a>
                <a href="{{ route('digireligi') }}">DigiReligi</a>
                <a href="{{ route('gallery-photo.index') }}">Galeri Foto</a>
                <a href="{{ route('forum-stella.index') }}">Forum Stella</a>
                <a class="news-nav__login" href="{{ auth()->check() ? url('/dashboard') : route('login') }}">
                    {{ auth()->check() ? 'Dashboard Utama' : 'Login' }}
                </a>
            </nav>
            <button class="news-menu-toggle" type="button" data-news-menu-toggle aria-label="Buka menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
        <nav class="news-mobile-menu" data-news-mobile-menu hidden aria-label="Navigasi seluler">
            <a href="{{ route('pengaduan.create') }}">Layanan Aduan</a>
            <a href="{{ route('digireligi') }}">DigiReligi</a>
            <a href="{{ route('gallery-photo.index') }}">Galeri Foto</a>
            <a href="{{ route('forum-stella.index') }}">Forum Stella</a>
            <a href="{{ auth()->check() ? url('/dashboard') : route('login') }}">{{ auth()->check() ? 'Dashboard Utama' : 'Login' }}</a>
        </nav>
    </header>

    <main>
        <section class="article-head">
            <div class="news-shell">
                <nav class="article-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('welcome') }}">Beranda</a><span>/</span><span>Berita</span><span>/</span><span>{{ $berita->kategori }}</span>
                </nav>
                <span class="article-category">{{ $berita->kategori }}</span>
                <h1>{{ $berita->judul }}</h1>
                @if($berita->ringkasan)<p class="article-summary">{{ $berita->ringkasan }}</p>@endif
                <div class="article-meta">
                    <span>{{ $berita->author?->name ?: 'Redaksi '.$schoolName }}</span>
                    <span>{{ $berita->published_at?->translatedFormat('d F Y') }}</span>
                    <span>{{ $readingMinutes }} menit baca</span>
                </div>
                <div class="article-reader" data-article-reader>
                    <button class="article-reader__toggle" type="button" data-reader-toggle aria-expanded="false">
                        <svg data-reader-play-icon viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7V5Z"/></svg>
                        <svg data-reader-pause-icon viewBox="0 0 24 24" aria-hidden="true" hidden><path d="M8 5v14M16 5v14"/></svg>
                        <span data-reader-toggle-label>Baca Artikel</span>
                    </button>
                    <div class="article-reader__controls" data-reader-controls hidden>
                        <div class="article-reader__status-row">
                            <span class="article-reader__status" data-reader-status aria-live="polite">Siap membacakan artikel</span>
                            <span class="article-reader__progress-label" data-reader-progress-label>0%</span>
                        </div>
                        <progress class="article-reader__progress" data-reader-progress value="0" max="100">0%</progress>
                        <div class="article-reader__settings">
                            <label>
                                <span>Volume <output data-reader-volume-output>100%</output></span>
                                <input type="range" data-reader-volume min="0" max="1" step="0.1" value="1">
                            </label>
                            <label>
                                <span>Kecepatan <output data-reader-rate-output>1x</output></span>
                                <input type="range" data-reader-rate min="0.5" max="2" step="0.1" value="1">
                            </label>
                            <button class="article-reader__stop" type="button" data-reader-stop disabled>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10v10H7z"/></svg>
                                Berhenti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="news-shell article-layout">
            <article>
                <img class="article-cover" src="{{ $coverUrl }}" alt="{{ $berita->judul }}">
                <textarea data-article-source hidden>{{ $berita->konten }}</textarea>
                <div class="article-prose" data-article-content>{!! nl2br(e($berita->konten)) !!}</div>
            </article>

            <aside class="article-aside">
                <div class="article-aside__label">Berita Terkait</div>
                @forelse($relatedNews as $related)
                    <a class="related-mini" href="{{ route('berita.show', $related->slug) }}">
                        <small>{{ $related->published_at?->translatedFormat('d M Y') }}</small>
                        <h3>{{ $related->judul }}</h3>
                    </a>
                @empty
                    <p class="comment-note">Belum ada berita terkait pada kategori ini.</p>
                @endforelse
            </aside>
        </div>

        <section class="comment-band" id="komentar">
            <div class="news-shell comment-grid">
                <div>
                    <h2 class="comment-title">Diskusi Pembaca</h2>
                    <p class="comment-note">{{ $comments->count() }} komentar telah disetujui admin.</p>
                    @forelse($comments as $comment)
                        <article class="comment-item">
                            <div class="comment-item__head">
                                <strong>{{ $comment->name }}</strong>
                                <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                            </div>
                            <p>{{ $comment->content }}</p>
                            <button class="comment-reply-button" type="button" data-reply-to="{{ $comment->id }}" data-reply-name="{{ $comment->name }}">Balas</button>
                            @if($comment->approvedReplies->isNotEmpty())
                                <div class="comment-replies">
                                    @foreach($comment->approvedReplies as $reply)
                                        <article class="comment-item">
                                            <div class="comment-item__head">
                                                <strong>{{ $reply->name }}</strong>
                                                <time datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->diffForHumans() }}</time>
                                            </div>
                                            <p>{{ $reply->content }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <p class="comment-empty">Belum ada komentar yang dipublikasikan. Jadilah pembaca pertama yang memulai diskusi.</p>
                    @endforelse
                </div>

                <form id="comment-form" class="comment-form" method="POST" action="{{ route('berita.comments.store', $berita) }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ old('parent_id') }}" data-comment-parent>
                    <input type="hidden" name="captcha_token" value="{{ $captcha['token'] }}">
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">
                    <h3>Tulis Komentar</h3>
                    <p class="comment-note">Komentar dan balasan akan tampil setelah ditinjau admin.</p>
                    @if(session('comment_success'))<div class="comment-alert">{{ session('comment_success') }}</div>@endif
                    <div class="reply-state" data-reply-state @if(!old('parent_id')) hidden @endif>
                        Membalas <strong data-reply-name>komentar terpilih</strong>.
                        <button type="button" data-cancel-reply>Batalkan</button>
                    </div>
                    @guest
                        <div class="form-row">
                            <div class="form-field">
                                <label for="comment-name">Nama</label>
                                <input id="comment-name" name="name" value="{{ old('name') }}" maxlength="120" required>
                                @error('name')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-field">
                                <label for="comment-email">Email</label>
                                <input id="comment-email" type="email" name="email" value="{{ old('email') }}" maxlength="255" required>
                                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @else
                        <p class="comment-note">Mengirim sebagai <strong>{{ auth()->user()->name }}</strong>.</p>
                    @endguest
                    <div class="form-field">
                        <label for="comment-content">Komentar</label>
                        <textarea id="comment-content" name="content" rows="5" maxlength="2000" required>{{ old('content') }}</textarea>
                        @error('content')<p class="form-error">{{ $message }}</p>@enderror
                        @error('parent_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-field">
                        <label for="captcha-answer">Verifikasi CAPTCHA</label>
                        <div class="captcha-box">
                            <span class="captcha-question">{{ $captcha['question'] }}</span>
                            <input id="captcha-answer" type="number" name="captcha_answer" inputmode="numeric" placeholder="Jawaban" required>
                        </div>
                        @error('captcha_answer')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <button class="comment-submit" type="submit">Kirim untuk Moderasi</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="news-footer">
        <div class="news-shell">
            <div class="news-footer__top">
                <a class="news-brand" href="{{ route('welcome') }}">
                    <span class="news-brand__mark">TS</span><span><strong>SISFO TS</strong><small>{{ $schoolName }}</small></span>
                </a>
                <div class="news-footer__links">
                    <a href="{{ route('privacy') }}">Privacy</a>
                    <a href="{{ route('terms') }}">Terms</a>
                    <a href="{{ route('security') }}">Security</a>
                </div>
            </div>
            <div class="news-footer__statement">THE REAL INFORMATIC SCHOOLS</div>
        </div>
    </footer>
</body>
</html>
