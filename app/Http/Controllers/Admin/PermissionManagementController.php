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
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $permissions = Permission::all()->groupBy(function($perm) {
            // Grouping logic based on prefix or keyword in name
            if (str_contains($perm->name, 'users')) return 'Manajemen Pengguna';
            if (str_contains($perm->name, 'roles') || str_contains($perm->name, 'permissions')) return 'Role & Hak Akses';
            if (str_contains($perm->name, 'manage') && (str_contains($perm->name, 'kelas') || str_contains($perm->name, 'siswa') || str_contains($perm->name, 'rombel') || str_contains($perm->name, 'tahun') || str_contains($perm->name, 'mapel') || str_contains($perm->name, 'guru') || str_contains($perm->name, 'jam'))) return 'Master Data';
            if (str_contains($perm->name, 'kesiswaan') || str_contains($perm->name, 'poin') || str_contains($perm->name, 'panggilan') || str_contains($perm->name, 'database') || str_contains($perm->name, 'kartu')) return 'Kesiswaan';
            if (str_contains($perm->name, 'bk') || str_contains($perm->name, 'pembinaan') || str_contains($perm->name, 'konsultasi') || str_contains($perm->name, 'chat')) return 'Bimbingan Konseling (BK)';
            if (str_contains($perm->name, 'piket') || str_contains($perm->name, 'perizinan') || str_contains($perm->name, 'terlambat') || str_contains($perm->name, 'absensi')) return 'Piket';
            if (str_contains($perm->name, 'kurikulum') || str_contains($perm->name, 'jadwal')) return 'Kurikulum';
            if (str_contains($perm->name, 'security') || str_contains($perm->name, 'verifikasi')) return 'Security';
            if (str_contains($perm->name, 'settings')) return 'Pengaturan';
            
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
        if ($role->name === 'Super Admin') {
            return response()->json(['message' => 'Role Super Admin tidak dapat diubah.'], 403);
        }

        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['message' => 'Hak akses berhasil diperbarui untuk role ' . $role->name]);
    }
}
