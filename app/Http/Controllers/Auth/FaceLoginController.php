<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaceLoginController extends Controller
{
    /**
     * Threshold for face matching.
     * Euclidean distance between 128D descriptors.
     * < 0.40 = high confidence same person
     * < 0.50 = acceptable match
     * >= 0.50 = different person
     */
    private const MATCH_THRESHOLD = 0.45;

    public function login(Request $request)
    {
        $request->validate([
            'face_descriptor' => 'required|string',
        ]);

        try {
            $incoming = json_decode($request->input('face_descriptor'), true);

            if (!is_array($incoming) || count($incoming) !== 128) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data wajah tidak valid. Pastikan wajah terdeteksi dengan jelas.',
                ], 422);
            }

            $users = User::whereNotNull('face_descriptor')
                ->where('face_descriptor', '!=', '')
                ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada akun dengan Face ID terdaftar.',
                ], 404);
            }

            $matchedUser  = null;
            $bestDistance = PHP_FLOAT_MAX;

            foreach ($users as $user) {
                $stored = json_decode($user->face_descriptor, true);

                if (!is_array($stored) || count($stored) !== 128) {
                    continue;
                }

                $distance = $this->euclideanDistance($incoming, $stored);

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $matchedUser  = $user;
                }
            }

            if ($matchedUser && $bestDistance < self::MATCH_THRESHOLD) {
                Auth::login($matchedUser, true);
                $request->session()->regenerate();

                $confidence  = round((1 - $bestDistance / self::MATCH_THRESHOLD) * 100);
                $redirectUrl = $this->getDashboardRoute($matchedUser);

                Log::info("Face login success: {$matchedUser->name} (distance={$bestDistance})");

                return response()->json([
                    'success'    => true,
                    'message'    => 'Wajah dikenali! Selamat datang, ' . $matchedUser->name,
                    'redirect'   => $redirectUrl,
                    'confidence' => $confidence . '%',
                ]);
            }

            Log::info('Face login failed: best distance=' . round($bestDistance, 4));

            return response()->json([
                'success' => false,
                'message' => 'Wajah tidak dikenali. Pastikan pencahayaan cukup dan wajah terlihat jelas.',
            ], 401);

        } catch (\Exception $e) {
            Log::error('Face login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses Face ID. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Euclidean distance between two 128-dimensional face descriptor vectors.
     * Returns 0.0 for identical faces, higher values for different faces.
     * Same person typically < 0.50, different person typically > 0.60.
     */
    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < 128; $i++) {
            $diff  = ($a[$i] ?? 0.0) - ($b[$i] ?? 0.0);
            $sum  += $diff * $diff;
        }
        return sqrt($sum);
    }

    private function getDashboardRoute(User $user): string
    {
        $roleRoutes = [
            'Siswa'          => 'siswa.dashboard.index',
            'Guru Kelas'     => 'guru-kelas.dashboard.index',
            'Wali Kelas'     => 'wali-kelas.dashboard.index',
            'Guru BK'        => 'bk.dashboard.index',
            'Guru Piket'     => 'piket.dashboard.index',
            'Waka Kesiswaan' => 'kesiswaan.dashboard.index',
            'Kurikulum'      => 'kurikulum.dashboard.index',
            'Operator'       => 'operator.dashboard.index',
            'Security'       => 'security.dashboard.index',
            'KAUR SDM'       => 'sdm.dashboard.index',
            'Super Admin'    => 'super-admin.dashboard.index',
            'Kantin'         => 'kantin.dashboard.index',
        ];

        foreach ($roleRoutes as $role => $routeName) {
            if ($user->hasRole($role)) {
                return route($routeName, absolute: false);
            }
        }

        return route('dashboard', absolute: false);
    }
}
