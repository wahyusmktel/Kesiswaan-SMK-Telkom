<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhotoComment;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GalleryPhotoController extends Controller
{
    private const UPLOAD_ROLES = ['Guru Kelas', 'Operator', 'Super Admin', 'Siswa', 'Guru Piket'];

    public function index()
    {
        $albums = GalleryAlbum::withCount('photos')
            ->with('coverPhoto')
            ->latest()
            ->get();

        $photos = GalleryPhoto::with([
                'album',
                'uploader',
                'comments.author',
                'likes' => fn ($query) => Auth::check()
                    ? $query->where('user_id', Auth::id())
                    : $query->whereRaw('1 = 0'),
            ])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->get();

        $canUpload = Auth::check() && Auth::user()->hasAnyRole(self::UPLOAD_ROLES);
        $canInteract = $canUpload;

        return view('public.gallery-photo', compact('albums', 'photos', 'canUpload', 'canInteract'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(self::UPLOAD_ROLES), 403);

        $validated = $request->validate([
            'album_id' => ['nullable', 'exists:gallery_albums,id'],
            'album_name' => ['nullable', 'string', 'max:120'],
            'album_description' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:120'],
            'caption' => ['nullable', 'string', 'max:500'],
            'taken_at' => ['nullable', 'date'],
            'photos' => ['required', 'array', 'min:1', 'max:20'],
            'photos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $album = null;

        if (!empty($validated['album_id'])) {
            $album = GalleryAlbum::findOrFail($validated['album_id']);
        } else {
            $albumName = trim((string) ($validated['album_name'] ?? ''));

            if ($albumName === '') {
                return back()
                    ->withErrors(['album_name' => 'Pilih album atau isi nama album baru.'])
                    ->withInput();
            }

            $album = GalleryAlbum::create([
                'user_id' => Auth::id(),
                'name' => $albumName,
                'description' => $validated['album_description'] ?? null,
            ]);
        }

        foreach ($request->file('photos') as $file) {
            $path = $file->store('gallery/photos/' . $album->id, 'public');

            GalleryPhoto::create([
                'gallery_album_id' => $album->id,
                'user_id' => Auth::id(),
                'title' => $validated['title'] ?? null,
                'caption' => $validated['caption'] ?? null,
                'image_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'taken_at' => $validated['taken_at'] ?? null,
            ]);
        }

        toast('Foto berhasil ditambahkan ke galeri.', 'success');

        return redirect()->route('gallery-photo.index');
    }

    public function destroy(GalleryPhoto $photo)
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();
        abort_unless($user->hasRole('Super Admin') || $photo->user_id === $user->id, 403);

        Storage::disk('public')->delete($photo->image_path);
        $photo->delete();

        toast('Foto berhasil dihapus.', 'success');

        return redirect()->route('gallery-photo.index');
    }

    public function toggleLove(GalleryPhoto $photo)
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(self::UPLOAD_ROLES), 403);

        $expectsJson = $this->expectsJson(request());
        $like = $photo->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            if (!$expectsJson) {
                toast('Love pada foto dibatalkan.', 'info');
            }
        } else {
            $photo->likes()->create(['user_id' => Auth::id()]);
            if (!$expectsJson) {
                toast('Foto berhasil disukai.', 'success');
            }
        }

        if ($expectsJson) {
            return response()->json(['photo' => $this->photoPayload($photo->fresh())]);
        }

        return redirect()->route('gallery-photo.index');
    }

    public function storeComment(Request $request, GalleryPhoto $photo)
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(self::UPLOAD_ROLES), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:gallery_photo_comments,id'],
        ]);

        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = GalleryPhotoComment::where('gallery_photo_id', $photo->id)->findOrFail($parentId);
            $parentId = $parent->parent_id ?: $parent->id;
        }

        $photo->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $parentId,
            'body' => $validated['body'],
        ]);

        if ($this->expectsJson($request)) {
            return response()->json(['photo' => $this->photoPayload($photo->fresh())], 201);
        }

        toast('Komentar berhasil ditambahkan.', 'success');
        return redirect()->route('gallery-photo.index');
    }

    public function destroyComment(GalleryPhotoComment $comment)
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();
        abort_unless($user->hasRole('Super Admin') || $comment->user_id === $user->id, 403);

        $photo = $comment->photo;
        $comment->delete();

        if ($this->expectsJson(request())) {
            return response()->json(['photo' => $this->photoPayload($photo->fresh())]);
        }

        toast('Komentar berhasil dihapus.', 'success');

        return redirect()->route('gallery-photo.index');
    }

    public function download(GalleryPhoto $photo): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($photo->image_path), 404);

        $name = $photo->original_name ?: 'galeri-photo-' . $photo->id . '.' . pathinfo($photo->image_path, PATHINFO_EXTENSION);

        return Storage::disk('public')->download($photo->image_path, $name);
    }

    private function expectsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function photoPayload(GalleryPhoto $photo): array
    {
        $photo->load([
            'album',
            'uploader',
            'comments.author',
            'likes' => fn ($query) => Auth::check()
                ? $query->where('user_id', Auth::id())
                : $query->whereRaw('1 = 0'),
        ])->loadCount(['likes', 'comments']);

        $comments = $photo->comments->sortByDesc('created_at');
        $topLevelComments = $comments->whereNull('parent_id')->values();

        return [
            'id' => $photo->id,
            'album_id' => $photo->gallery_album_id,
            'album' => $photo->album?->name ?? 'Tanpa Album',
            'title' => $photo->title ?: ($photo->album?->name ?? 'Foto Galeri'),
            'caption' => $photo->caption,
            'url' => $photo->image_url,
            'uploader' => $photo->uploader?->name ?? 'Kontributor',
            'date' => optional($photo->taken_at ?? $photo->created_at)->format('d M Y'),
            'size' => $photo->size_for_humans,
            'likes_count' => $photo->likes_count,
            'comments_count' => $photo->comments_count,
            'liked_by_me' => $photo->likes->isNotEmpty(),
            'love_url' => route('gallery-photo.love', $photo),
            'comment_url' => route('gallery-photo.comments.store', $photo),
            'download_url' => route('gallery-photo.download', $photo),
            'delete_url' => route('gallery-photo.destroy', $photo),
            'can_delete' => Auth::check() && (Auth::id() === $photo->user_id || Auth::user()->hasRole('Super Admin')),
            'comments' => $topLevelComments->map(fn ($comment) => $this->commentPayload($comment, $comments))->values(),
        ];
    }

    private function commentPayload(GalleryPhotoComment $comment, $allComments): array
    {
        return [
            'id' => $comment->id,
            'author' => $comment->author?->name ?? 'Pengguna',
            'body' => $comment->body,
            'date' => $comment->created_at->diffForHumans(),
            'delete_url' => route('gallery-photo.comments.destroy', $comment),
            'can_delete' => Auth::check() && (Auth::id() === $comment->user_id || Auth::user()->hasRole('Super Admin')),
            'replies' => $allComments->where('parent_id', $comment->id)
                ->sortBy('created_at')
                ->map(fn ($reply) => $this->commentPayload($reply, collect()))
                ->values(),
        ];
    }
}
