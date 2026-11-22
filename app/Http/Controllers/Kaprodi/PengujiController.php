<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UkkPenguji;
use App\Models\UkkUjian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengujiController extends Controller
{
    public function index()
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ujians = UkkUjian::with([
            'tahunPelajaran',
            'rombels.kelas',
            'rombels.siswa',
            'ukkPengujis.user.masterGuru',
            'ukkPengujis.rombel.kelas',
        ])
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

        $ujiansJson = $ujians->map(function ($ujian) {
            return [
                'id'           => $ujian->id,
                'nama_ujian'   => $ujian->nama_ujian,
                'jurusan'      => $ujian->jurusan,
                'nama_project' => $ujian->nama_project,
                'is_bilingual' => (bool) $ujian->is_bilingual,
                'rombels'      => $ujian->rombels->map(function ($r) {
                    $tgl = $r->pivot->tanggal_pelaksanaan;
                    if (is_string($tgl)) {
                        $tgl = \Carbon\Carbon::parse($tgl);
                    }
                    $sortedSiswas = $r->siswa->sortBy('nama_lengkap')->values();
                    return [
                        'id'                  => $r->id,
                        'nama_kelas'          => $r->kelas?->nama_kelas ?? 'Kelas #' . $r->id,
                        'total_siswa'         => $sortedSiswas->count(),
                        'tanggal_pelaksanaan' => $tgl ? $tgl->format('Y-m-d') : '',
                        'tanggal_formatted'   => $tgl ? $tgl->translatedFormat('d M Y') : '',
                        'jam_mulai'           => $r->pivot->jam_mulai ? substr($r->pivot->jam_mulai, 0, 5) : '',
                        'jam_selesai'         => $r->pivot->jam_selesai ? substr($r->pivot->jam_selesai, 0, 5) : '',
                        'ruangan'             => $r->pivot->ruangan ?? '',
                        'siswas'              => $sortedSiswas->map(function ($s, $idx) {
                            return [
                                'id'         => $s->id,
                                'nomor_urut' => $idx + 1,
                                'nama'       => $s->nama_lengkap,
                                'nis'        => $s->nis,
                            ];
                        })->values(),
                    ];
                })->values(),
                'pengujis'     => $ujian->ukkPengujis->map(function ($p) {
                    return [
                        'id'              => $p->id,
                        'user_id'         => $p->user_id,
                        'rombel_id'       => $p->rombel_id,
                        'peran'           => $p->peran,
                        'no_urut_mulai'   => $p->no_urut_mulai,
                        'no_urut_selesai' => $p->no_urut_selesai,
                        'siswa_ids'       => $p->siswa_ids ?: [],
                        'nama'            => $p->user?->masterGuru?->nama_lengkap ?? $p->user?->name,
                        'kode'            => $p->user?->masterGuru?->kode_guru ?? '—',
                        'avatar'          => $p->user?->avatar,
                    ];
                })->values(),
            ];
        })->values();

        return view('pages.kaprodi.penguji.index', compact('ujians', 'ujiansJson', 'guruKelas', 'tahunAktif'));
    }

    public function store(Request $request, UkkUjian $ujian)
    {
        $request->validate([
            'user_id'         => 'required|exists:users,id',
            'rombel_id'       => 'nullable|exists:rombels,id',
            'peran'           => 'nullable|string|in:semua,pengetahuan,keterampilan',
            'no_urut_mulai'   => 'nullable|integer|min:1',
            'no_urut_selesai' => 'nullable|integer',
            'siswa_ids'       => 'nullable|array',
            'siswa_ids.*'     => 'integer|exists:master_siswa,id',
        ]);

        $peran         = $request->input('peran', 'semua');
        $rombelId      = $request->input('rombel_id') ?: null;
        $noUrutMulai   = $request->filled('no_urut_mulai') ? (int) $request->no_urut_mulai : null;
        $noUrutSelesai = $request->filled('no_urut_selesai') ? (int) $request->no_urut_selesai : null;
        $siswaIds      = $request->has('siswa_ids') && is_array($request->siswa_ids) && !empty($request->siswa_ids)
            ? array_values(array_map('intval', $request->siswa_ids))
            : null;

        if ($rombelId) {
            $isMapped = DB::table('ukk_rombel_mappings')
                ->where('ukk_ujian_id', $ujian->id)
                ->where('rombel_id', $rombelId)
                ->exists();

            if (!$isMapped) {
                return response()->json(['message' => 'Kelas tidak terdaftar dalam ujian ini.'], 422);
            }
        }

        $exists = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $request->user_id)
            ->where('rombel_id', $rombelId)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Penguji sudah ditugaskan untuk kelas ini.'], 422);
        }

        $penguji = UkkPenguji::create([
            'ukk_ujian_id'    => $ujian->id,
            'user_id'         => $request->user_id,
            'rombel_id'       => $rombelId,
            'peran'           => $peran,
            'no_urut_mulai'   => $noUrutMulai,
            'no_urut_selesai' => $noUrutSelesai,
            'siswa_ids'       => $siswaIds,
        ]);

        $user = User::with('masterGuru')->find($request->user_id);
        $rombel = $rombelId ? Rombel::with('kelas')->find($rombelId) : null;

        return response()->json([
            'message' => 'Penguji berhasil ditugaskan.',
            'penguji' => [
                'id'              => $penguji->id,
                'user_id'         => $user->id,
                'rombel_id'       => $rombelId,
                'rombel_name'     => $rombel?->kelas?->nama_kelas ?? 'Semua Kelas',
                'nama'            => $user->masterGuru?->nama_lengkap ?? $user->name,
                'kode'            => $user->masterGuru?->kode_guru ?? '—',
                'avatar'          => $user->avatar,
                'peran'           => $peran,
                'no_urut_mulai'   => $noUrutMulai,
                'no_urut_selesai' => $noUrutSelesai,
                'siswa_ids'       => $siswaIds ?: [],
            ],
        ]);
    }

    public function updatePeran(Request $request, UkkUjian $ujian, User $user)
    {
        $request->validate([
            'peran'           => 'required|string|in:semua,pengetahuan,keterampilan',
            'rombel_id'       => 'nullable|integer',
            'penguji_id'      => 'nullable|integer',
            'no_urut_mulai'   => 'nullable|integer|min:1',
            'no_urut_selesai' => 'nullable|integer',
            'siswa_ids'       => 'nullable|array',
            'siswa_ids.*'     => 'integer|exists:master_siswa,id',
        ]);

        $query = UkkPenguji::where('ukk_ujian_id', $ujian->id);

        if ($request->filled('penguji_id')) {
            $query->where('id', $request->penguji_id);
        } else {
            $query->where('user_id', $user->id);
            if ($request->filled('rombel_id')) {
                $query->where('rombel_id', $request->rombel_id);
            }
        }

        $noUrutMulai   = $request->has('no_urut_mulai') ? ($request->no_urut_mulai ? (int) $request->no_urut_mulai : null) : null;
        $noUrutSelesai = $request->has('no_urut_selesai') ? ($request->no_urut_selesai ? (int) $request->no_urut_selesai : null) : null;
        $siswaIds      = $request->has('siswa_ids')
            ? (is_array($request->siswa_ids) && !empty($request->siswa_ids) ? array_values(array_map('intval', $request->siswa_ids)) : null)
            : null;

        $updateData = ['peran' => $request->peran];
        if ($request->has('no_urut_mulai') || $request->has('no_urut_selesai')) {
            $updateData['no_urut_mulai']   = $noUrutMulai;
            $updateData['no_urut_selesai'] = $noUrutSelesai;
        }
        if ($request->has('siswa_ids')) {
            $updateData['siswa_ids'] = $siswaIds;
        }

        $query->update($updateData);

        return response()->json([
            'message'         => 'Data penugasan penguji berhasil diperbarui.',
            'peran'           => $request->peran,
            'no_urut_mulai'   => $noUrutMulai,
            'no_urut_selesai' => $noUrutSelesai,
            'siswa_ids'       => $siswaIds ?: [],
        ]);
    }

    public function updateJadwal(Request $request, UkkUjian $ujian, Rombel $rombel)
    {
        $request->validate([
            'tanggal_pelaksanaan' => 'nullable|date',
            'jam_mulai'           => 'nullable|string|max:10',
            'jam_selesai'         => 'nullable|string|max:10',
            'ruangan'             => 'nullable|string|max:100',
        ]);

        DB::table('ukk_rombel_mappings')
            ->where('ukk_ujian_id', $ujian->id)
            ->where('rombel_id', $rombel->id)
            ->update([
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan ?: null,
                'jam_mulai'           => $request->jam_mulai ?: null,
                'jam_selesai'         => $request->jam_selesai ?: null,
                'ruangan'             => $request->ruangan ?: null,
                'updated_at'          => now(),
            ]);

        return response()->json([
            'message' => 'Jadwal kelas ' . ($rombel->kelas?->nama_kelas ?? '') . ' berhasil diperbarui.',
            'jadwal'  => [
                'tanggal_pelaksanaan' => $request->tanggal_pelaksanaan,
                'jam_mulai'           => $request->jam_mulai,
                'jam_selesai'         => $request->jam_selesai,
                'ruangan'             => $request->ruangan,
            ],
        ]);
    }

    public function destroy(Request $request, UkkUjian $ujian, User $user)
    {
        $query = UkkPenguji::where('ukk_ujian_id', $ujian->id)
            ->where('user_id', $user->id);

        if ($request->filled('rombel_id')) {
            $query->where('rombel_id', $request->rombel_id);
        } elseif ($request->filled('penguji_id')) {
            $query = UkkPenguji::where('id', $request->penguji_id);
        }

        $query->delete();

        return response()->json(['message' => 'Penguji berhasil dihapus.']);
    }
}
