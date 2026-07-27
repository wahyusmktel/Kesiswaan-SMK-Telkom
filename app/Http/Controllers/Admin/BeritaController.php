<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\NewsArticleAiException;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Berita;
use App\Models\BeritaComment;
use App\Services\NewsArticleAiGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeritaController extends Controller
{
    /**
     * Tampilkan daftar semua berita (admin).
     */
    public function index(Request $request)
    {
        $query = Berita::with('author')->latest();

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $beritas = $query->paginate(10);
        $pendingCommentCount = BeritaComment::where('status', 'pending')->count();

        return view('pages.admin.berita.index', compact('beritas', 'pendingCommentCount'));
    }

    /**
     * Form tambah berita baru.
     */
    public function create()
    {
        $setting = AppSetting::first();
        $aiReady = (bool) (
            $setting?->stella_ai_enabled
            && $setting->stella_ai_base_url
            && $setting->stella_ai_api_key
            && $setting->stella_ai_chat_model
        );

        return view('pages.admin.berita.create', compact('aiReady'));
    }

    public function generateWithAi(Request $request, NewsArticleAiGenerator $generator)
    {
        $validated = $request->validate([
            'kategori' => 'required|in:Akademik,Kesiswaan,Kegiatan,Prestasi,Pengumuman,Lainnya',
            'use_ai_recommendation' => 'required|boolean',
            'paragraph_count' => [
                'nullable',
                Rule::requiredIf(! $request->boolean('use_ai_recommendation')),
                'integer',
                'min:2',
                'max:12',
            ],
            'sentences_per_paragraph' => [
                'nullable',
                Rule::requiredIf(! $request->boolean('use_ai_recommendation')),
                'integer',
                'min:2',
                'max:8',
            ],
            'instructions' => 'nullable|string|max:3000',
            'include_code_snippets' => 'required|boolean',
        ]);

        try {
            $article = $generator->generate(
                $validated['kategori'],
                (bool) $validated['use_ai_recommendation'],
                isset($validated['paragraph_count']) ? (int) $validated['paragraph_count'] : null,
                isset($validated['sentences_per_paragraph']) ? (int) $validated['sentences_per_paragraph'] : null,
                $validated['instructions'] ?? null,
                (bool) $validated['include_code_snippets'],
            );
        } catch (NewsArticleAiException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode() >= 400 ? $exception->getCode() : 502);
        }

        return response()->json([
            'message' => 'Draf artikel berhasil dibuat oleh Stella AI.',
            'article' => $article,
        ]);
    }

    /**
     * Simpan berita baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'focus_keyword' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:2000',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kategori' => 'required|in:Akademik,Kesiswaan,Kegiatan,Prestasi,Pengumuman,Lainnya',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->except('gambar');
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($request->judul).'-'.Str::random(5);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        if ($request->status === 'published') {
            $data['published_at'] = now();
        }

        Berita::create($data);

        toast('Berita berhasil ditambahkan!', 'success');

        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Form edit berita.
     */
    public function edit(Berita $berita)
    {
        return view('pages.admin.berita.edit', compact('berita'));
    }

    /**
     * Perbarui berita.
     */
    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'focus_keyword' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:2000',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'kategori' => 'required|in:Akademik,Kesiswaan,Kegiatan,Prestasi,Pengumuman,Lainnya',
            'status' => 'required|in:draft,published',
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($berita->gambar) {
                Storage::disk('public')->delete($berita->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        // Set published_at if status changed to published
        if ($request->status === 'published' && ! $berita->published_at) {
            $data['published_at'] = now();
        } elseif ($request->status === 'draft') {
            $data['published_at'] = null;
        }

        $berita->update($data);

        toast('Berita berhasil diperbarui!', 'success');

        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Hapus berita.
     */
    public function destroy(Berita $berita)
    {
        if ($berita->gambar) {
            Storage::disk('public')->delete($berita->gambar);
        }

        $berita->delete();

        toast('Berita berhasil dihapus!', 'success');

        return redirect()->route('super-admin.berita.index');
    }

    /**
     * Halaman detail berita (public).
     */
    public function show($slug)
    {
        $setting = AppSetting::first();
        $berita = Berita::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedNews = Berita::published()
            ->where('id', '!=', $berita->id)
            ->where('kategori', $berita->kategori)
            ->latest('published_at')
            ->take(3)
            ->get();

        $comments = $berita->comments()
            ->approved()
            ->whereNull('parent_id')
            ->with('approvedReplies')
            ->oldest()
            ->get();
        $captcha = $this->createCommentCaptcha($berita, request());
        $view = $setting?->theme === 'stella-vue'
            ? 'pages.berita.show-vue'
            : 'pages.berita.show';

        return view($view, compact('berita', 'relatedNews', 'comments', 'captcha', 'setting'));
    }

    /**
     * API endpoint to get latest published news for welcome page.
     */
    public function latestApi()
    {
        $beritas = Berita::published()
            ->latest('published_at')
            ->take(6)
            ->get(['id', 'judul', 'slug', 'ringkasan', 'gambar', 'kategori', 'published_at']);

        return response()->json($beritas->map(function ($b) {
            return [
                'id' => $b->id,
                'judul' => $b->judul,
                'slug' => $b->slug,
                'ringkasan' => $b->ringkasan,
                'gambar_url' => $b->gambar_url,
                'kategori' => $b->kategori,
                'published_at' => $b->published_at->diffForHumans(),
                'url' => route('berita.show', $b->slug),
            ];
        }));
    }

    private function createCommentCaptcha(Berita $berita, Request $request): array
    {
        $left = random_int(2, 9);
        $right = random_int(1, 9);
        $token = (string) Str::uuid();
        $challenges = collect($request->session()->get('news_comment_captchas', []))
            ->filter(fn ($challenge) => (int) ($challenge['expires_at'] ?? 0) >= now()->timestamp)
            ->take(4)
            ->all();
        $challenges[$token] = [
            'berita_id' => $berita->id,
            'answer_hash' => Hash::make((string) ($left + $right)),
            'expires_at' => now()->addMinutes(20)->timestamp,
        ];
        $request->session()->put('news_comment_captchas', $challenges);

        return [
            'token' => $token,
            'question' => "{$left} + {$right} =",
        ];
    }
}
