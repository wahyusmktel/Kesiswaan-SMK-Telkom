<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterSiswa::with([
            'user',
            'dapodik',
            'graduationTahunPelajaran',
            'rombels' => fn ($rombelQuery) => $rombelQuery
                ->with(['kelas', 'tahunPelajaran'])
                ->orderByDesc('tahun_pelajaran_id'),
        ])->alumni();

        if ($request->filled('search')) {
            $search = trim((string) $request->string('search'));
            $query->where(function ($studentQuery) use ($search) {
                $studentQuery->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhereHas('dapodik', fn ($dapodikQuery) => $dapodikQuery->where('nisn', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('graduation_tahun_pelajaran_id')) {
            $query->where('graduation_tahun_pelajaran_id', $request->integer('graduation_tahun_pelajaran_id'));
        }

        $alumni = $query->orderByDesc('graduated_at')->orderBy('nama_lengkap')->paginate(15);
        $graduationPeriods = TahunPelajaran::whereHas('graduatedStudents')
            ->orderByDesc('tahun')
            ->get();

        return view('pages.master-data.alumni.index', compact('alumni', 'graduationPeriods'));
    }

    public function update(Request $request, MasterSiswa $alumnus)
    {
        abort_unless($alumnus->status === 'alumni', 404);

        $validated = $request->validate([
            'graduated_at' => 'required|date',
            'graduation_tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'graduation_notes' => 'nullable|string|max:2000',
        ]);

        $alumnus->update($validated);

        return back()->with('success', 'Data kelulusan alumni berhasil diperbarui.');
    }

    public function reactivate(MasterSiswa $alumnus)
    {
        abort_unless($alumnus->status === 'alumni', 404);

        $alumnus->update([
            'status' => 'aktif',
            'graduated_at' => null,
            'graduation_tahun_pelajaran_id' => null,
            'graduation_notes' => null,
        ]);

        if ($alumnus->dapodik?->rombel_saat_ini === 'Lulus') {
            $alumnus->dapodik->update(['rombel_saat_ini' => null]);
        }

        return back()->with('success', 'Siswa dikembalikan menjadi siswa aktif. Tambahkan siswa ke rombel aktif melalui Kelola Siswa.');
    }
}
