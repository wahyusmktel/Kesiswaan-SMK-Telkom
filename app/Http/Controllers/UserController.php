<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Menampilkan daftar user
    public function index()
    {
        try {
            $users = User::with('roles')->latest()->paginate(10);
            $roles = \Spatie\Permission\Models\Role::pluck('name'); // Ambil list role
            return view('pages.users.index', compact('users', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            toast('Gagal memuat data pengguna.', 'error');
            return redirect()->back();
        }
    }

    // Menampilkan form untuk membuat user baru
    public function create()
    {
        try {
            $roles = Role::pluck('name', 'name');
            return view('pages.users.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error showing create user form: ' . $e->getMessage());
            toast('Gagal menampilkan form tambah pengguna.', 'error');
            return redirect()->route('users.index');
        }
    }

    // Menyimpan user baru ke database
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // GUNAKAN SYNCROLES UNTUK ARRAY
            $user->assignRole($request->roles);

            DB::commit();
            toast('Pengguna berhasil ditambahkan!', 'success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing user: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menyimpan data.', 'error');
            return redirect()->back()->withInput();
        }
    }

    // Menampilkan form untuk mengedit user
    public function edit(User $user)
    {
        try {
            $roles = Role::pluck('name', 'name');
            return view('pages.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error showing edit user form: ' . $e->getMessage());
            toast('Gagal menampilkan form edit pengguna.', 'error');
            return redirect()->route('users.index');
        }
    }

    // Mengupdate data user di database
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        if ($validator->fails()) {
            toast($validator->errors()->first(), 'error');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->syncRoles($request->roles);

            DB::commit();
            toast('Pengguna berhasil diperbarui!', 'success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            toast('Terjadi kesalahan saat memperbarui data.', 'error');
            return redirect()->back()->withInput();
        }
    }

    // Menghapus user dari database
    public function destroy(User $user)
    {
        // Hati-hati: jangan biarkan user menghapus dirinya sendiri
        if (auth()->id() == $user->id) {
            toast('Anda tidak bisa menghapus akun Anda sendiri.', 'error');
            return redirect()->route('users.index');
        }

        try {
            $user->delete();
            toast('Pengguna berhasil dihapus!', 'success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            toast('Gagal menghapus pengguna.', 'error');
            return redirect()->route('users.index');
        }
    }
}
