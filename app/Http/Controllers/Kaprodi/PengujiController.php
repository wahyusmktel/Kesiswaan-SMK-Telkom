<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\TahunPelajaran;
use App\Models\UkkUjian;
use App\Models\User;
use Illuminate\Http\Request;

class PengujiController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ujians = UkkUjian::with(['tahunPelajaran', 'penguji.masterGuru'])
            ->when($tahunAktif, fn ($q) => $q->where('tahun_pelajaran_id', $tahunAktif->id))
            ->latest()
            ->get();

        $guruKelas = User::whereHas('roles', fn ($q) => $q->where('name', 'Guru Kelas'))
            ->orWhereHas('masterGuru')
            ->with('masterGuru')
            ->orderBy('name')
            ->get()
            ->unique('id')
            ->map(fn ($u) => [
                'id'     => $u->id,
                'nama'   => $u->masterGuru?->nama_lengkap ?? $u->name,
                'kode'   => $u->masterGuru?->kode_guru ?? '—',
                'avatar' => $u->avatar,
            ])
            ->values();

        return view('pages.kaprodi.penguji.index', compact('ujians', 'guruKelas', 'tahunAktif'));
    }

    public function store(Request $request, UkkUjian $ujian)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'peran'   => 'nullable|string|in:semua,pengetahuan,keterampilan',
        ]);

        $peran = $request->input('peran', 'semua');

        if ($ujian->penguji()->where('user_id', $request->user_id)->exists()) {
            return response()->json(['message' => 'Penguji sudah ditambahkan.'], 422);
        }

        $ujian->penguji()->attach($request->user_id, ['peran' => $peran]);

        $user = User::with('masterGuru')->find($request->user_id);

        return response()->json([
            'message' => 'Penguji berhasil ditambahkan.',
            'penguji' => [
                'id'     => $user->id,
                'nama'   => $user->masterGuru?->nama_lengkap ?? $user->name,
                'kode'   => $user->masterGuru?->kode_guru ?? '—',
                'avatar' => $user->avatar,
                'peran'  => $peran,
            ],
        ]);
    }

    public function updatePeran(Request $request, UkkUjian $ujian, User $user)
    {
        $request->validate([
            'peran' => 'required|string|in:semua,pengetahuan,keterampilan',
        ]);

        $ujian->penguji()->updateExistingPivot($user->id, ['peran' => $request->peran]);

        return response()->json([
            'message' => 'Peran penguji berhasil diperbarui.',
            'peran'   => $request->peran,
        ]);
    }

    public function destroy(UkkUjian $ujian, User $user)
    {
        $ujian->penguji()->detach($user->id);

        return response()->json(['message' => 'Penguji berhasil dihapus.']);
    }
}
