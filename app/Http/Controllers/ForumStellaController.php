<?php

namespace App\Http\Controllers;

use App\Models\NottedPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumStellaController extends Controller
{
    public const CATEGORIES = [
        'diskusi' => 'Semua Diskusi',
        'pertanyaan' => 'Pertanyaan',
        'pengumuman' => 'Pengumuman',
        'materi' => 'Berbagi Materi',
        'ide' => 'Ide Sekolah',
    ];

    public function index()
    {
        $posts = collect();
        $categories = self::CATEGORIES;
        $stats = [
            'threads' => 0,
            'contributors' => 0,
            'comments' => 0,
        ];
        $categoryCounts = collect($categories)->mapWithKeys(fn ($label, $key) => [$key => 0])->all();

        if (Auth::check()) {
            $posts = NottedPost::with([
                'user',
                'comments' => function ($query) {
                    $query->with('user')->withCount(['likes', 'replies'])->latest()->take(3);
                },
                'likes',
            ])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->paginate(10);

            $stats = [
                'threads' => NottedPost::count(),
                'contributors' => NottedPost::distinct('user_id')->count('user_id'),
                'comments' => \App\Models\NottedComment::count(),
            ];
            $rawCategoryCounts = NottedPost::selectRaw("COALESCE(forum_category, 'diskusi') as category, COUNT(*) as total")
                ->groupBy('category')
                ->pluck('total', 'category')
                ->all();
            $categoryCounts = collect($categories)
                ->mapWithKeys(fn ($label, $key) => [$key => $rawCategoryCounts[$key] ?? 0])
                ->all();
            $categoryCounts['diskusi'] = $stats['threads'];
        }

        return view('public.forum-stella', compact('posts', 'stats', 'categories', 'categoryCounts'));
    }

    public function enter()
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        return redirect()->route('forum-stella.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:3000'],
            'forum_category' => ['required', 'string', 'in:' . implode(',', array_keys(self::CATEGORIES))],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notted/posts', 'public');
        }

        NottedPost::create([
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'image' => $imagePath,
            'forum_category' => $validated['forum_category'],
        ]);

        return redirect()->route('forum-stella.index')->with('success', 'Diskusi berhasil diterbitkan di Forum Stella.');
    }
}
