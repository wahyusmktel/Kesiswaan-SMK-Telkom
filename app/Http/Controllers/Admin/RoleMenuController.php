<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleMenuSetting;
use App\Services\RoleMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleMenuController extends Controller
{
    public function index(Request $request, RoleMenuService $menuService): View
    {
        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();
        $selectedRole = $roles->firstWhere('id', (int) $request->integer('role')) ?? $roles->first();
        $menus = $selectedRole ? $menuService->discoverForRole($selectedRole) : [];

        return view('admin.role-menus.index', compact('roles', 'selectedRole', 'menus'));
    }

    public function update(Request $request, Role $role, RoleMenuService $menuService): RedirectResponse
    {
        $knownMenus = collect($menuService->discoverForRole($role));
        $knownKeys = $knownMenus->pluck('key')->all();

        $validated = $request->validate([
            'menus' => ['required', 'array', 'max:500'],
            'menus.*.key' => ['required', 'string', 'distinct', Rule::in($knownKeys)],
            'menus.*.visible' => ['required', 'boolean'],
            'menus.*.order' => ['required', 'integer', 'min:0'],
        ]);

        $lockedKeys = $knownMenus->where('locked', true)->pluck('key');

        DB::transaction(function () use ($role, $validated, $lockedKeys): void {
            RoleMenuSetting::query()->where('role_id', $role->getKey())->delete();

            foreach ($validated['menus'] as $menu) {
                RoleMenuSetting::query()->create([
                    'role_id' => $role->getKey(),
                    'menu_key' => $menu['key'],
                    'is_visible' => $lockedKeys->contains($menu['key']) ? true : (bool) $menu['visible'],
                    'sort_order' => (int) $menu['order'],
                ]);
            }
        });

        $menuService->clear($role);

        return redirect()->route('super-admin.role-menus.index', ['role' => $role->getKey()])
            ->with('success', 'Tampilan dan urutan menu role '.$role->name.' berhasil disimpan.');
    }

    public function reset(Role $role, RoleMenuService $menuService): RedirectResponse
    {
        RoleMenuSetting::query()->where('role_id', $role->getKey())->delete();
        $menuService->clear($role);

        return redirect()->route('super-admin.role-menus.index', ['role' => $role->getKey()])
            ->with('success', 'Pengaturan menu role '.$role->name.' dikembalikan ke bawaan aplikasi.');
    }
}
