<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StellaLoginController extends Controller
{
    /**
     * Show the barcode scanning page for Stella Access Card.
     */
    public function showScanPage()
    {
        return view('auth.stella-scan');
    }

    /**
     * Process login using scanned NIPD/NIS.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nipd' => 'required|string',
        ]);

        try {
            // Find student by NIS (which is often NIPD in this system)
            $siswa = MasterSiswa::where('nis', $request->nipd)->first();

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak terdaftar (NIPD: ' . $request->nipd . '). Silakan hubungi admin.',
                ], 404);
            }

            if (!$siswa->user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun untuk siswa ' . $siswa->nama_lengkap . ' belum diaktifkan.',
                ], 422);
            }

            $user = User::find($siswa->user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data akun tidak ditemukan.',
                ], 404);
            }

            // Perform login
            Auth::login($user, true); // Remember session

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil! Selamat datang, ' . $user->name,
                'redirect' => route('dashboard'),
            ]);

        } catch (\Exception $e) {
            Log::error('Stella Access Card Login Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses login.',
            ], 500);
        }
    }
}
