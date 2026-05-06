<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDigitalSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalSignatureApiController extends Controller
{
    /**
     * Kembalikan URL gambar tanda tangan milik user yang sedang login.
     * Dipakai oleh aplikasi eksternal (SARPRA) untuk impor gambar TTD.
     */
    public function myImage(Request $request)
    {
        $sig = UserDigitalSignature::where('user_id', Auth::id())->first();

        if (!$sig || !$sig->ttd_image_path) {
            return response()->json([
                'success'   => false,
                'message'   => 'Tanda tangan digital belum disetup.',
                'image_url' => null,
            ], 404);
        }

        if (!$sig->isReady()) {
            return response()->json([
                'success'   => false,
                'message'   => 'Tanda tangan digital belum aktif atau PIN belum diset.',
                'image_url' => null,
            ], 403);
        }

        return response()->json([
            'success'   => true,
            'image_url' => asset('storage/' . $sig->ttd_image_path),
            'user'      => [
                'id'   => Auth::id(),
                'name' => Auth::user()->name,
            ],
        ]);
    }
}
