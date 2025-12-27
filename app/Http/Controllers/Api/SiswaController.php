<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Get student statistics (points, etc).
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $masterSiswa = $user->masterSiswa;

        if (!$masterSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $masterSiswa->getCurrentPoints(),
                'status' => $masterSiswa->getPointStatus(),
                'total_violations' => $masterSiswa->pelanggarans()->count(),
                'total_achievements' => $masterSiswa->prestasis()->count(),
                'total_permissions' => $user->perizinan()->count(),
            ],
        ]);
    }

    /**
     * Get student history (violations and permissions).
     */
    public function getRiwayat(Request $request)
    {
        $user = $request->user();
        $masterSiswa = $user->masterSiswa;

        if (!$masterSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
            ], 404);
        }

        $pelanggaran = $masterSiswa->pelanggarans()
            ->with('poinPeraturan')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'type' => 'pelanggaran',
                    'date' => $p->created_at->format('Y-m-d'),
                    'title' => $p->poinPeraturan->nama_pelanggaran,
                    'points' => $p->poinPeraturan->bobot_poin,
                    'category' => $p->poinPeraturan->category->name ?? '-',
                ];
            });

        $perizinan = $user->perizinan()
            ->orderBy('tanggal_izin', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'type' => 'perizinan',
                    'date' => $p->tanggal_izin,
                    'title' => $p->jenis_izin,
                    'description' => $p->keterangan,
                    'status' => $p->status,
                ];
            });

        $history = $pelanggaran->concat($perizinan)->sortByDesc('date')->values();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * Submit new permit request.
     */
    public function requestIzin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_izin' => 'required|string',
            'keterangan' => 'required|string',
            'tanggal_izin' => 'required|date',
            'dokumen' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $dokumenPath = null;

        if ($request->hasFile('dokumen')) {
            $dokumenPath = $request->file('dokumen')->store('perizinan', 'public');
        }

        $perizinan = Perizinan::create([
            'user_id' => $user->id,
            'jenis_izin' => $request->jenis_izin,
            'keterangan' => $request->keterangan,
            'tanggal_izin' => $request->tanggal_izin,
            'dokumen_pendukung' => $dokumenPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan izin berhasil dikirim',
            'data' => $perizinan,
        ]);
    }
}
