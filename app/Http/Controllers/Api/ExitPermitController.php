<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExitPermitController extends Controller
{
    /**
     * Submit new exit permit (Izin Meninggalkan Kelas) request.
     */
    public function request(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'tujuan' => 'required|string',
            'keterangan' => 'required|string',
            'estimasi_kembali' => 'required',
            'jadwal_pelajaran_id' => 'required|exists:jadwal_pelajaran,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get student's current rombel
        $masterSiswa = $user->masterSiswa;
        $rombel = $masterSiswa->rombels()->latest()->first();

        if (!$rombel) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar di rombel manapun',
            ], 403);
        }

        $exitPermit = IzinMeninggalkanKelas::create([
            'user_id' => $user->id,
            'rombel_id' => $rombel->id,
            'jadwal_pelajaran_id' => $request->jadwal_pelajaran_id,
            'tujuan' => $request->tujuan,
            'keterangan' => $request->keterangan,
            'estimasi_kembali' => $request->estimasi_kembali,
            'status' => 'pending_guru',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan izin meninggalkan kelas berhasil dikirim',
            'data' => $exitPermit,
        ]);
    }

    /**
     * Get exit permits history for the authenticated student.
     */
    public function getMyHistory(Request $request)
    {
        $permits = $request->user()->izinMeninggalkanKelas()
            ->with(['rombel', 'jadwalPelajaran.mataPelajaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $permits,
        ]);
    }

    /**
     * Get pending exit permits for teacher approval.
     */
    public function getPendingForTeacher(Request $request)
    {
        $user = $request->user();
        
        // Find rombels where this teacher is Wali Kelas OR 
        // they are teaching at the moment (based on current schedule)
        // Simplified: Wali Kelas logic
        $rombelIds = $user->rombels()->pluck('id');

        $pending = IzinMeninggalkanKelas::with(['siswa.masterSiswa', 'jadwalPelajaran.mataPelajaran'])
            ->whereIn('rombel_id', $rombelIds)
            ->where('status', 'pending_guru')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pending,
        ]);
    }

    /**
     * Approve or reject exit permit by teacher.
     */
    public function approveByTeacher(Request $request, $id)
    {
        $permit = IzinMeninggalkanKelas::find($id);

        if (!$permit) {
            return response()->json(['success' => false, 'message' => 'Izin tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'alasan' => 'required_if:status,rejected|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->status === 'approved') {
            $permit->update([
                'status' => 'approved_guru', // Move to piket approval if needed, or just approved
                'guru_kelas_approval_id' => $request->user()->id,
                'guru_kelas_approved_at' => now(),
            ]);
        } else {
            $permit->update([
                'status' => 'rejected',
                'alasan_penolakan' => $request->alasan,
                'ditolak_oleh' => $request->user()->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status izin berhasil diperbarui',
            'data' => $permit,
        ]);
    }
}
