<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionManagementController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($perm) {
            // Grouping logic based on prefix or keyword in name
            if (str_contains($perm->name, 'users'))
                return 'Manajemen Pengguna';
            if (str_contains($perm->name, 'roles') || str_contains($perm->name, 'permissions'))
                return 'Role & Hak Akses';
            if (str_contains($perm->name, 'master data') || (str_contains($perm->name, 'manage') && (str_contains($perm->name, 'kelas') || str_contains($perm->name, 'siswa') || str_contains($perm->name, 'rombel') || str_contains($perm->name, 'tahun') || str_contains($perm->name, 'mapel') || str_contains($perm->name, 'guru') || str_contains($perm->name, 'jam'))))
                return 'Master Data';
            if (str_contains($perm->name, 'kesiswaan') || str_contains($perm->name, 'poin') || str_contains($perm->name, 'panggilan') || str_contains($perm->name, 'database') || str_contains($perm->name, 'kartu') || str_contains($perm->name, 'dispensasi') || str_contains($perm->name, 'monitoring izin'))
                return 'Kesiswaan';
            if (str_contains($perm->name, 'bk ') || str_contains($perm->name, 'pembinaan') || str_contains($perm->name, 'konsultasi') || str_contains($perm->name, 'chat'))
                return 'Bimbingan Konseling (BK)';
            if (str_contains($perm->name, 'wali kelas') || str_contains($perm->name, 'mentoring'))
                return 'Wali Kelas';
            if (str_contains($perm->name, 'operator') || str_contains($perm->name, 'dapodik'))
                return 'Operator';
            if (str_contains($perm->name, 'piket') || str_contains($perm->name, 'perizinan siswa') || str_contains($perm->name, 'penanganan terlambat') || str_contains($perm->name, 'absensi guru'))
                return 'Piket';
            if (str_contains($perm->name, 'kurikulum') || str_contains($perm->name, 'jadwal') || str_contains($perm->name, 'distribusi mapel') || str_contains($perm->name, 'analisa kurikulum') || str_contains($perm->name, 'absensi per kelas'))
                return 'Kurikulum';
            if (str_contains($perm->name, 'security') || str_contains($perm->name, 'verifikasi izin') || str_contains($perm->name, 'pendataan terlambat') || str_contains($perm->name, 'gate terminal'))
                return 'Security';
            if (str_contains($perm->name, 'sdm') || str_contains($perm->name, 'perizinan guru') || str_contains($perm->name, 'nde referensi'))
                return 'KAUR SDM (Kepegawaian)';
            if (str_contains($perm->name, 'prakerin'))
                return 'Prakerin';
            if (str_contains($perm->name, 'lms'))
                return 'LMS';
            if (str_contains($perm->name, 'coaching analytics'))
                return 'Shared / Dashboard';
            if (str_contains($perm->name, 'settings'))
                return 'Pengaturan';

            return 'Lainnya';
        });

        return view('pages.admin.permissions.index', compact('roles', 'permissions'));
    }

    public function getRolePermissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    public function syncPermissions(Request $request, Role $role)
    {

        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['message' => 'Hak akses berhasil diperbarui untuk role ' . $role->name]);
    }
}
