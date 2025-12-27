<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Get pending permit requests for approval (for Wali Kelas / Piket).
     */
    public function getPendingApprovals(Request $request)
    {
        $user = $request->user();
        
        // This is a simplified logic. In a real scenario, you might want to filter 
        // by student's wali_kelas_id or based on teacher's role.
        $query = Perizinan::with('user.masterSiswa')
            ->where('status', 'pending');

        // Example: If user is Wali Kelas, they can only see their students
        if ($user->hasRole('wali_kelas')) {
            $siswaIds = $user->siswa()->pluck('id');
            $query->whereIn('user_id', $siswaIds);
        }

        $pending = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $pending,
        ]);
    }

    /**
     * Approve or reject a permit request.
     */
    public function approveIzin(Request $request, $id)
    {
        $perizinan = Perizinan::find($id);

        if (!$perizinan) {
            return response()->json([
                'success' => false,
                'message' => 'Data perizinan tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'required_if:status,ditolak|string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $perizinan->update([
            'status' => $request->status,
            'alasan_penolakan' => $request->alasan_penolakan,
            'disetujui_oleh' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status perizinan berhasil diperbarui',
            'data' => $perizinan,
        ]);
    }

    /**
     * Get permit history for teachers.
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        
        $query = Perizinan::with('user.masterSiswa')
            ->where('status', '!=', 'pending');

        if ($user->hasRole('wali_kelas')) {
            $siswaIds = $user->siswa()->pluck('id');
            $query->whereIn('user_id', $siswaIds);
        }

        $history = $query->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }
}
