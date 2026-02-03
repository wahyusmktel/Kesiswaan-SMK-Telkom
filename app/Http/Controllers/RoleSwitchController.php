<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();
        $requestedRole = $request->role;

        if ($user->hasRole($requestedRole)) {
            session(['active_role' => $requestedRole]);

            // Redirect based on role
            return redirect()->route($this->getDashboardRoute($requestedRole))
                ->with('success', 'Berhasil beralih ke role ' . $requestedRole);
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke role tersebut.');
    }

    private function getDashboardRoute($role)
    {
        return match ($role) {
            'Super Admin' => 'super-admin.dashboard.index',
            'Waka Kesiswaan' => 'kesiswaan.dashboard.index',
            'Kurikulum' => 'kurikulum.dashboard.index',
            'Guru Kelas' => 'guru-kelas.dashboard.index',
            'Wali Kelas' => 'wali-kelas.dashboard.index',
            'Guru BK' => 'bk.dashboard.index',
            'Piket' => 'piket.dashboard.index',
            'Siswa' => 'siswa.dashboard.index',
            'Petugas Keamanan' => 'security.dashboard.index',
            'KAUR SDM' => 'sdm.dashboard.index',
            'Operator' => 'operator.dashboard.index',
            'Koordinator Prakerin' => 'dashboard',
            default => 'dashboard',
        };
    }
}
