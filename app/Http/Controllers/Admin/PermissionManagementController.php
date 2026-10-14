<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RoleMenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagementController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::query()->with('permissions')->orderBy('name')->get();
        $allPermissions = Permission::query()->orderBy('name')->get();

        $permissions = $allPermissions->groupBy(fn (Permission $perm) => $this->categorizePermission($perm->name));

        $selectedRoleId = $request->query('role') ? (int) $request->query('role') : $roles->first()?->id;

        return view('pages.admin.permissions.index', compact('roles', 'permissions', 'allPermissions', 'selectedRoleId'));
    }

    public function getRolePermissions(Role $role): JsonResponse
    {
        return response()->json([
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'total_active' => $role->permissions->count(),
        ]);
    }

    public function syncPermissions(Request $request, Role $role, RoleMenuService $menuService): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        // Invalidate Spatie & Menu caches
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $menuService->clear($role);

        return response()->json([
            'success' => true,
            'message' => 'Hak akses untuk role "' . $role->name . '" berhasil disimpan (' . count($permissions) . ' hak akses aktif).',
            'role_id' => $role->id,
            'total_active' => count($permissions),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
        ]);

        $name = strtolower(trim($validated['name']));
        $permission = Permission::create([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        // Auto grant to Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $superAdmin?->givePermissionTo($permission);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Hak akses "' . $permission->name . '" berhasil ditambahkan.',
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'category' => $this->categorizePermission($permission->name),
            ],
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $name = $permission->name;
        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => 'Hak akses "' . $name . '" berhasil dihapus.',
        ]);
    }

    private function categorizePermission(string $name): string
    {
        if (str_contains($name, 'users')) return 'Manajemen Pengguna';
        if (str_contains($name, 'roles') || str_contains($name, 'permissions')) return 'Role & Hak Akses';

        if (preg_match('/\btu\b/i', $name) || str_contains($name, 'tu letter') || str_contains($name, 'tu incoming') || str_contains($name, 'tu outgoing') || str_contains($name, 'tu dashboard')) return 'Tata Usaha & Persuratan';
        if (str_contains($name, 'kantin')) return 'Kantin Sekolah';
        if (str_contains($name, 'uks')) return 'UKS & Kesehatan Siswa';
        if (str_contains($name, 'erapor')) return 'e-Rapor SISFO';
        if (str_contains($name, 'rab') || str_contains($name, 'keuangan')) return 'Anggaran (RAB) & Keuangan';
        if (str_contains($name, 'okr')) return 'OKR & Kinerja Sekolah';
        if (str_contains($name, 'asset') || str_contains($name, 'sarpra') || str_contains($name, 'cctv')) return 'Sarana, Prasarana & CCTV';
        if (str_contains($name, 'ukk')) return 'UKK / Kaprodi';
        if (str_contains($name, 'survey')) return 'Survei & Evaluasi';
        if (str_contains($name, 'lms')) return 'LMS & E-Learning';

        if (str_contains($name, 'prakerin') || str_contains($name, 'hubin')) return 'Prakerin, Hubin & Unit Produksi';
        if (str_contains($name, 'security') || str_contains($name, 'verifikasi izin') || str_contains($name, 'pendataan terlambat') || str_contains($name, 'gate terminal')) return 'Security & Gerbang';
        if (str_contains($name, 'sdm') || str_contains($name, 'perizinan guru') || str_contains($name, 'nde referensi')) return 'KAUR SDM (Kepegawaian)';
        if (str_contains($name, 'wali kelas') || str_contains($name, 'mentoring') || str_contains($name, 'buku induk')) return 'Wali Kelas';
        if (str_contains($name, 'piket') || str_contains($name, 'perizinan siswa') || str_contains($name, 'penanganan terlambat') || (str_contains($name, 'absensi guru') && !str_contains($name, 'monitoring'))) return 'Piket';
        if (str_contains($name, 'bk') || str_contains($name, 'pembinaan') || str_contains($name, 'konsultasi') || str_contains($name, 'chat bk')) return 'Bimbingan Konseling (BK)';
        if (str_contains($name, 'kurikulum') || str_contains($name, 'jadwal pelajaran') || str_contains($name, 'distribusi mapel') || str_contains($name, 'analisa kurikulum') || str_contains($name, 'guru kelas') || str_contains($name, 'monitoring absensi guru')) return 'Kurikulum & Pembelajaran';
        if (str_contains($name, 'operator') || str_contains($name, 'dapodik')) return 'Operator';
        if (str_contains($name, 'kesiswaan') || str_contains($name, 'poin') || str_contains($name, 'panggilan') || str_contains($name, 'database maintenance') || str_contains($name, 'kartu akses') || str_contains($name, 'dispensasi') || str_contains($name, 'monitoring izin') || str_contains($name, 'pengaduan ortu')) return 'Kesiswaan & Kedisiplinan';
        if (str_contains($name, 'master data') || in_array($name, ['manage kelas', 'manage siswa', 'manage rombel', 'manage tahun pelajaran', 'manage mata pelajaran', 'manage guru', 'manage jam pelajaran'], true)) return 'Master Data';
        if (str_contains($name, 'executive') || str_contains($name, 'coaching analytics') || str_contains($name, 'monitoring keterlambatan')) return 'Eksekutif & Analytics';
        if (str_contains($name, 'settings')) return 'Pengaturan Sistem';

        return 'Lainnya';
    }
}
