<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhotoComment;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

        $like = $photo->likes()->where('user_id', Auth::id())->first();

        if ($like) {
            $like->delete();
            toast('Love pada foto dibatalkan.', 'info');
        } else {
            $photo->likes()->create(['user_id' => Auth::id()]);
            toast('Foto berhasil disukai.', 'success');
        }

        return redirect()->route('gallery-photo.index');
    }

    public function storeComment(Request $request, GalleryPhoto $photo)
    {
        abort_unless(Auth::check() && Auth::user()->hasAnyRole(self::UPLOAD_ROLES), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $photo->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        toast('Komentar berhasil ditambahkan.', 'success');

        return redirect()->route('gallery-photo.index');
    }

    public function destroyComment(GalleryPhotoComment $comment)
    {
        abort_unless(Auth::check(), 403);

        $user = Auth::user();
        abort_unless($user->hasRole('Super Admin') || $comment->user_id === $user->id, 403);

        $comment->delete();

        toast('Komentar berhasil dihapus.', 'success');

        return redirect()->route('gallery-photo.index');
    }
}
