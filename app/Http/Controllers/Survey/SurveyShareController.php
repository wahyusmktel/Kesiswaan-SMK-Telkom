<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyShare;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SurveyShareController extends Controller
{
    /**
     * Dapatkan daftar kolaborator survei dan pengguna yang tersedia untuk dibagikan.
     */
    public function index(Request $request, Survey $survey): JsonResponse
    {
        $user = $request->user();
        abort_unless(
            $survey->canManageShares($user) || $survey->canViewResults($user),
            403,
            'Anda tidak memiliki izin untuk melihat daftar kolaborator survei ini.'
        );

        $survey->loadMissing('creator:id,name,email');
        $shares = $survey->shares()
            ->with('user:id,name,email')
            ->get()
            ->map(fn (SurveyShare $share) => [
                'id' => $share->id,
                'user_id' => $share->user_id,
                'name' => $share->user?->name ?? 'Pengguna',
                'email' => $share->user?->email ?? '-',
                'role' => $share->role,
                'created_at' => $share->created_at?->translatedFormat('d M Y H:i'),
            ]);

        $excludedUserIds = array_merge(
            [$survey->created_by],
            $survey->shares()->pluck('user_id')->all()
        );

        // Ambil daftar pengguna non-siswa untuk opsi berbagi
        $availableUsers = User::query()
            ->whereNotIn('id', $excludedUserIds)
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'Siswa'))
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get()
            ->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]);

        return response()->json([
            'survey' => [
                'id' => $survey->id,
                'title' => $survey->title,
                'owner' => [
                    'id' => $survey->creator?->id,
                    'name' => $survey->creator?->name ?? 'Pembuat',
                    'email' => $survey->creator?->email ?? '-',
                ],
                'share_url' => route('surveys.show', $survey),
            ],
            'can_manage' => $survey->canManageShares($user),
            'shares' => $shares,
            'available_users' => $availableUsers,
        ]);
    }

    /**
     * Bagikan akses survei ke pengguna lain.
     */
    public function store(Request $request, Survey $survey): JsonResponse|RedirectResponse
    {
        abort_unless(
            $survey->canManageShares($request->user()),
            403,
            'Hanya pemilik survei atau admin yang dapat membagikan akses survei.'
        );

        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::notIn([$survey->created_by]),
            ],
            'role' => ['required', 'in:editor,viewer'],
        ], [
            'user_id.not_in' => 'Pemilik survei sudah memiliki akses penuh.',
            'role.in' => 'Peran akses harus berupa Editor atau Viewer.',
        ]);

        $share = SurveyShare::updateOrCreate(
            [
                'survey_id' => $survey->id,
                'user_id' => $validated['user_id'],
            ],
            [
                'role' => $validated['role'],
                'shared_by' => $request->user()->id,
            ]
        );

        $share->load('user:id,name,email');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akses survei berhasil dibagikan kepada ' . ($share->user?->name ?? 'pengguna') . '.',
                'share' => [
                    'id' => $share->id,
                    'user_id' => $share->user_id,
                    'name' => $share->user?->name,
                    'email' => $share->user?->email,
                    'role' => $share->role,
                    'created_at' => $share->created_at?->translatedFormat('d M Y H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Akses survei berhasil dibagikan.');
    }

    /**
     * Perbarui peran akses kolaborator (editor <-> viewer).
     */
    public function update(Request $request, Survey $survey, User $user): JsonResponse|RedirectResponse
    {
        abort_unless(
            $survey->canManageShares($request->user()),
            403,
            'Hanya pemilik survei atau admin yang dapat mengubah peran kolaborator.'
        );

        $validated = $request->validate([
            'role' => ['required', 'in:editor,viewer'],
        ]);

        $share = SurveyShare::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $share->update(['role' => $validated['role']]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peran akses ' . $user->name . ' berhasil diperbarui menjadi ' . ucfirst($validated['role']) . '.',
                'role' => $share->role,
            ]);
        }

        return back()->with('success', 'Peran akses berhasil diperbarui.');
    }

    /**
     * Cabut / hapus akses kolaborator dari survei.
     */
    public function destroy(Request $request, Survey $survey, User $user): JsonResponse|RedirectResponse
    {
        abort_unless(
            $survey->canManageShares($request->user()),
            403,
            'Hanya pemilik survei atau admin yang dapat mencabut akses kolaborator.'
        );

        SurveyShare::where('survey_id', $survey->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Akses ' . $user->name . ' terhadap survei ini telah dicabut.',
            ]);
        }

        return back()->with('success', 'Akses kolaborator berhasil dihapus.');
    }
}
