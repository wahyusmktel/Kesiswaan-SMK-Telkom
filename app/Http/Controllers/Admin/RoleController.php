<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users'); // Hitung user di setiap role

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->orderBy('name', 'asc')->paginate(10);
        return view('pages.admin.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => $request->name]);

        toast('Role berhasil dibuat.', 'success');
        return back();
    }

    public function update(Request $request, Role $role)
    {
        // Cegah edit Super Admin agar sistem tidak rusak
        if ($role->name === 'Super Admin') {
            toast('Role Super Admin tidak dapat diubah.', 'error');
            return back();
        }

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
        ]);

        $role->update(['name' => $request->name]);

        toast('Role berhasil diperbarui.', 'success');
        return back();
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            toast('Role Super Admin tidak dapat dihapus!', 'error');
            return back();
        }

        if ($role->users_count > 0) {
            toast('Gagal! Masih ada user yang menggunakan role ini.', 'error');
            return back();
        }

        $role->delete();
        toast('Role berhasil dihapus.', 'success');
        return back();
    }
}
