<?php

namespace App\Http\Controllers;

use App\Models\NottedPost;
use App\Models\NottedComment;
use App\Models\NottedLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NottedController extends Controller
{
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
        $posts = NottedPost::with(['user', 'rootComments.user', 'likes'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);
        return view('notted.app', compact('posts'));
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
            'rootComments' => function ($query) {
                $query->with(['user', 'replies.user', 'likes', 'replies.likes'])
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
     * Toggle like for a post or comment.
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string|in:post,comment',
        ]);

        $userId = Auth::id();
        $likeableType = $request->input('type') === 'post' ? NottedPost::class : NottedComment::class;
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

        $count = $likeableType::find($likeableId)->likes()->count();

        return response()->json([
            'status' => $status,
            'count' => $count,
        ]);
    }
}