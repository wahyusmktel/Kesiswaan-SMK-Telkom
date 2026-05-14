<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UkkUjian;
use Illuminate\Http\Request;

class UkkController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first()
            ?? TahunPelajaran::latest()->first();

        $jurusans = Kelas::distinct()->orderBy('jurusan')->pluck('jurusan');

        $ujians = UkkUjian::with([
                'tahunPelajaran',
                'rombels.kelas',
            ])
            ->withCount('rombels')
            ->latest()
            ->paginate(15);

        return view('pages.kaprodi.ukk.index', compact('tahunAktif', 'jurusans', 'ujians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ujian'          => 'required|string|max:255',
            'tahun_pelajaran_id'  => 'required|exists:tahun_pelajaran,id',
            'jurusan'             => 'required|string|max:255',
            'nama_project'        => 'nullable|string|max:255',
            'tanggal_pelaksanaan' => 'nullable|date',
            'rombel_ids'          => 'nullable|array',
            'rombel_ids.*'        => 'exists:rombels,id',
        ]);

        $ujian = UkkUjian::create([
            'nama_ujian'          => $validated['nama_ujian'],
            'tahun_pelajaran_id'  => $validated['tahun_pelajaran_id'],
            'jurusan'             => $validated['jurusan'],
            'nama_project'        => $validated['nama_project'] ?? null,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'] ?? null,
        ]);

        if (!empty($validated['rombel_ids'])) {
            $ujian->rombels()->sync($validated['rombel_ids']);
        }

        return response()->json([
            'message' => 'Data UKK berhasil ditambahkan.',
            'ujian'   => $ujian->load(['tahunPelajaran', 'rombels.kelas'])->loadCount('rombels'),
        ]);
    }

    public function update(Request $request, UkkUjian $ujian)
    {
        $validated = $request->validate([
            'nama_ujian'          => 'required|string|max:255',
            'tahun_pelajaran_id'  => 'required|exists:tahun_pelajaran,id',
            'jurusan'             => 'required|string|max:255',
            'nama_project'        => 'nullable|string|max:255',
            'tanggal_pelaksanaan' => 'nullable|date',
            'rombel_ids'          => 'nullable|array',
            'rombel_ids.*'        => 'exists:rombels,id',
        ]);

        $ujian->update([
            'nama_ujian'          => $validated['nama_ujian'],
            'tahun_pelajaran_id'  => $validated['tahun_pelajaran_id'],
            'jurusan'             => $validated['jurusan'],
            'nama_project'        => $validated['nama_project'] ?? null,
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'] ?? null,
        ]);

        $ujian->rombels()->sync($validated['rombel_ids'] ?? []);

        return response()->json([
            'message' => 'Data UKK berhasil diperbarui.',
            'ujian'   => $ujian->load(['tahunPelajaran', 'rombels.kelas'])->loadCount('rombels'),
        ]);
    }

    public function destroy(UkkUjian $ujian)
    {
        $ujian->rombels()->detach();
        $ujian->delete();

        return response()->json(['message' => 'Data UKK berhasil dihapus.']);
    }

    public function getRombel(Request $request)
    {
        $jurusan     = $request->input('jurusan');
        $tahunAktif  = TahunPelajaran::where('is_active', true)->first()
            ?? TahunPelajaran::latest()->first();

        $query = Rombel::with('kelas')
            ->withCount('siswa')
            ->whereHas('kelas', fn ($q) => $q->where('jurusan', $jurusan));

        if ($tahunAktif) {
            $query->where('tahun_pelajaran_id', $tahunAktif->id);
        }

        $rombels = $query->orderBy('id')->get()->map(fn ($r) => [
            'id'          => $r->id,
            'nama_kelas'  => $r->kelas?->nama_kelas ?? 'Kelas Tidak Diketahui',
            'siswa_count' => $r->siswa_count,
        ]);

        return response()->json($rombels);
    }
}
