<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeritaComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BeritaCommentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');
        $comments = BeritaComment::with(['berita:id,judul,slug', 'parent:id,name', 'moderator:id,name'])
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('pages.admin.berita-comments.index', [
            'comments' => $comments,
            'selectedStatus' => $status,
            'counts' => BeritaComment::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function moderate(Request $request, BeritaComment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $comment->update([
            'status' => $validated['status'],
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return back()->with('success', $validated['status'] === 'approved'
            ? 'Komentar telah disetujui.'
            : 'Komentar telah ditolak.');
    }

    public function destroy(BeritaComment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
