<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;

class DashboardRedirector
{
    public static function routeNameForRole(?string $role): ?string
    {
        return match ($role) {
            'Super Admin' => 'super-admin.dashboard.index',
            'Waka Kesiswaan' => 'kesiswaan.dashboard.index',
            'Kurikulum' => 'kurikulum.dashboard.index',
            'Guru Kelas' => 'guru-kelas.dashboard.index',
            'Wali Kelas' => 'wali-kelas.dashboard.index',
            'Guru BK' => 'bk.dashboard.index',
            'Piket', 'Guru Piket' => 'piket.dashboard.index',
            'Siswa', 'siswa' => 'siswa.dashboard.index',
            'Security', 'Petugas Keamanan' => 'security.dashboard.index',
            'KAUR SDM' => 'sdm.dashboard.index',
            'Operator' => 'operator.dashboard.index',
            'Tata Usaha' => 'tu.dashboard.index',
            'Kantin' => 'kantin.dashboard.index',
            'Kaprodi' => 'kaprodi.ukk.index',
            'Koordinator Prakerin' => 'prakerin.industri.index',
            'Kepala Sekolah' => 'tanda-tangan.index',
            'Petugas UKS' => 'uks.records.index',
            default => null,
        };
    }

    public static function routeNameForUser(User $user): ?string
    {
        $roles = $user->getRoleNames();
        $activeRole = session('active_role');

        if (!$activeRole || !$roles->contains($activeRole)) {
            $activeRole = $roles->first();
            session(['active_role' => $activeRole]);
        }

        $routeName = self::routeNameForRole($activeRole);

        if ($routeName && Route::has($routeName)) {
            return $routeName;
        }

        foreach ($roles as $role) {
            $routeName = self::routeNameForRole($role);

            if ($routeName && Route::has($routeName)) {
                session(['active_role' => $role]);

                return $routeName;
            }
        }

        return null;
    }
}
