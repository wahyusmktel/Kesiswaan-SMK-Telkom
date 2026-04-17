<?php

namespace App\Http\Controllers;

use App\Models\NottedPost;
use App\Models\NottedComment;
use App\Models\NottedLike;
use App\Models\NottedReel;
use App\Models\NottedReelComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\NottedTypingResult;

class NottedController extends Controller
{
    /**
     * Show User Profile
     */
    public function profile(User $user = null)
    {
        $user = $user ?? Auth::user();
        
        // Fetch posts by this user
        $posts = NottedPost::where('user_id', $user->id)
            ->with(['user', 'comments' => function ($q) {
                $q->with('user')->withCount(['likes', 'replies'])->latest()->take(3);
            }, 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);

        // Stats
        $stats = [
            'posts_count' => NottedPost::where('user_id', $user->id)->count(),
            'total_likes_received' => NottedPost::where('notted_posts.user_id', $user->id)
                ->join('notted_likes', function($join) {
                    $join->on('notted_posts.id', '=', 'notted_likes.likeable_id')
                         ->where('notted_likes.likeable_type', '=', NottedPost::class);
                })->count(),
            'joined_at' => $user->created_at->format('M Y'),
        ];

        return view('notted.profile', compact('user', 'posts', 'stats'));
    }

    /**
     * Games Hub Feature
     */
    public function games()
    {
        return view('notted.games');
    }

    /**
     * Typing Test Feature
     */
    public function typingTest()
    {
        $history = NottedTypingResult::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        // Data for chart (last 10 tests, chronological order)
        $chartData = $history->reverse()->values();

        return view('notted.typing-test', compact('history', 'chartData'));
    }

    /**
     * Store Typing Test Result
     */
    public function storeTypingResult(Request $request)
    {
        $validated = $request->validate([
            'kpm' => 'required|integer',
            'accuracy' => 'required|integer',
            'correct_words' => 'required|integer',
            'wrong_words' => 'required|integer',
            'total_chars' => 'required|integer',
            'language' => 'required|string|max:2',
        ]);

        $result = NottedTypingResult::create([
            'user_id' => Auth::id(),
            'kpm' => $validated['kpm'],
            'accuracy' => $validated['accuracy'],
            'correct_words' => $validated['correct_words'],
            'wrong_words' => $validated['wrong_words'],
            'total_chars' => $validated['total_chars'],
            'language' => $validated['language'],
        ]);

        return response()->json($result);
    }

    /**
     * Show NOTTED Landing Page
     */
    public function index()
    {
        return view('notted.landing');
    }

    /**
     * Show NOTTED Application Dashboard
     */
    public function app()
    {
        $posts = NottedPost::with(['user', 'comments' => function ($q) {
            $q->with('user')->withCount(['likes', 'replies'])->latest()->take(3);
        }, 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);

        // Fetch latest reels for feed carousel
        $latestReels = NottedReel::with('user')
            ->withCount(['likes', 'comments'])
            ->latest()
            ->take(10)
            ->get();

        return view('notted.feed', compact('posts', 'latestReels'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notted/posts', 'public');
        }

        NottedPost::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Postingan berhasil dibagikan!');
    }

    /**
     * Display the specified post as JSON.
     */
    public function show(NottedPost $post)
    {
        $post->load([
            'user',
            'comments' => function ($query) {
                $query->with(['user', 'likes', 'replies'])
                    ->withCount(['likes', 'replies'])
                    ->latest();
            },
            'likes'
        ])->loadCount(['likes', 'comments']);

        return response()->json($post);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function storeComment(Request $request, NottedPost $post)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:notted_comments,id',
        ]);

        $comment = NottedComment::create([
            'notted_post_id' => $post->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
        ]);

        return response()->json($comment->load(['user', 'likes'])->loadCount(['likes', 'replies']));
    }

    /**
     * Toggle like for a post, comment, reel, or reel comment.
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string|in:post,comment,reel,reel_comment',
        ]);

        $userId = Auth::id();
        $typeMap = [
            'post' => NottedPost::class,
            'comment' => NottedComment::class,
            'reel' => NottedReel::class,
            'reel_comment' => NottedReelComment::class,
        ];
        $likeableType = $typeMap[$request->input('type')];
        $likeableId = $request->input('id');

        $like = NottedLike::where('user_id', $userId)
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            NottedLike::create([
                'user_id' => $userId,
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableType,
            ]);
            $status = 'liked';
        }

        $count = NottedLike::where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableType)
            ->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
        ]);
    }

    // ================================================
    //                REELS FEATURE
    // ================================================

    /**
     * Show Reels Page
     */
    public function reels()
    {
        $reels = NottedReel::with(['user', 'originalReel.user'])
            ->withCount(['likes', 'comments', 'reposts'])
            ->latest()
            ->paginate(20);

        return view('notted.reels', compact('reels'));
    }

    /**
     * Store a newly created reel.
     */
    public function storeReel(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,webm,mov|max:51200',
            'caption' => 'nullable|string|max:500',
        ]);

        $videoPath = $request->file('video')->store('notted/reels', 'public');

        $reel = NottedReel::create([
            'user_id' => Auth::id(),
            'video' => $videoPath,
            'caption' => $request->input('caption'),
        ]);

        return response()->json($reel->load('user')->loadCount(['likes', 'comments']));
    }

    /**
     * Display the specified reel as JSON.
     */
    public function showReel(NottedReel $reel)
    {
        // Increment view count
        $reel->increment('views_count');

        $reel->load([
            'user',
            'originalReel.user',
            'comments' => function ($query) {
                $query->with(['user', 'likes', 'replies.user'])
                    ->withCount(['likes', 'replies'])
                    ->whereNull('parent_id')
                    ->latest();
            },
            'likes'
        ])->loadCount(['likes', 'comments', 'reposts']);

        return response()->json($reel);
    }

    /**
     * Store a comment on a reel.
     */
    public function storeReelComment(Request $request, NottedReel $reel)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:notted_reel_comments,id',
        ]);

        $comment = NottedReelComment::create([
            'notted_reel_id' => $reel->id,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
        ]);

        return response()->json($comment->load(['user', 'likes'])->loadCount(['likes', 'replies']));
    }

    /**
     * Repost a reel.
     */
    public function repostReel(NottedReel $reel)
    {
        // Prevent reposting own reel
        if ($reel->user_id === Auth::id()) {
            return response()->json(['message' => 'Tidak bisa repost reel sendiri'], 422);
        }

        // Determine which reel is the original
        $originalId = $reel->repost_from_id ?? $reel->id;

        // Check if already reposted
        $existing = NottedReel::where('user_id', Auth::id())
            ->where('repost_from_id', $originalId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Kamu sudah pernah repost reel ini'], 422);
        }

        $repost = NottedReel::create([
            'user_id' => Auth::id(),
            'video' => $reel->video,
            'thumbnail' => $reel->thumbnail,
            'caption' => $reel->caption,
            'repost_from_id' => $originalId,
        ]);

        return response()->json([
            'message' => 'Reel berhasil di-repost!',
            'reel' => $repost->load(['user', 'originalReel.user'])->loadCount(['likes', 'comments']),
        ]);
    }
}