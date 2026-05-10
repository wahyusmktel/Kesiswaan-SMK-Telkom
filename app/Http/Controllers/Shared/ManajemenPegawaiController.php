<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ManajemenPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'masterGuru'])
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Siswa'));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $pegawai = $query->orderBy('name')->paginate(20)->withQueryString();

        $allRoles = Role::where('name', '!=', 'Siswa')->orderBy('name')->get();

        $totalPegawai   = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Siswa'))->count();
        $totalDenganNuptk = MasterGuru::whereNotNull('nuptk')->count();
        $totalRoles     = $allRoles->count();
        $pegawaiBaru    = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Siswa'))
                              ->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->count();

        return view('pages.shared.manajemen-pegawai.index', compact(
            'pegawai', 'allRoles', 'totalPegawai', 'totalDenganNuptk', 'totalRoles', 'pegawaiBaru'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'role'          => 'required|string|exists:roles,name',
            'nuptk'         => 'nullable|string|max:20|unique:master_gurus,nuptk',
            'kode_guru'     => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            if ($request->filled('nuptk') || $request->filled('kode_guru')) {
                MasterGuru::create([
                    'user_id'       => $user->id,
                    'nama_lengkap'  => $request->name,
                    'nuptk'         => $request->nuptk,
                    'kode_guru'     => $request->kode_guru,
                    'jenis_kelamin' => $request->jenis_kelamin ?? 'L',
                ]);
            }

            toast('Pegawai berhasil ditambahkan.', 'success');
        } catch (\Exception $e) {
            Log::error('ManajemenPegawai store error: ' . $e->getMessage());
            toast('Gagal menambahkan pegawai: ' . $e->getMessage(), 'error');
        }

        return back();
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'      => 'nullable|string|min:8',
            'role'          => 'required|string|exists:roles,name',
            'nuptk'         => ['nullable', 'string', 'max:20', Rule::unique('master_gurus', 'nuptk')->ignore($user->masterGuru?->id)],
            'kode_guru'     => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        try {
            $data = ['name' => $request->name, 'email' => $request->email];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);

            $user->syncRoles([$request->role]);

            if ($request->filled('nuptk') || $request->filled('kode_guru')) {
                MasterGuru::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nama_lengkap'  => $request->name,
                        'nuptk'         => $request->nuptk,
                        'kode_guru'     => $request->kode_guru,
                        'jenis_kelamin' => $request->jenis_kelamin ?? $user->masterGuru?->jenis_kelamin ?? 'L',
                    ]
                );
            }

            toast('Data pegawai berhasil diperbarui.', 'success');
        } catch (\Exception $e) {
            Log::error('ManajemenPegawai update error: ' . $e->getMessage());
            toast('Gagal memperbarui data: ' . $e->getMessage(), 'error');
        }

        return back();
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            toast('Tidak dapat menghapus akun sendiri.', 'error');
            return back();
        }

        try {
            $user->masterGuru?->delete();
            $user->delete();
            toast("Akun {$user->name} berhasil dihapus.", 'success');
        } catch (\Exception $e) {
            Log::error('ManajemenPegawai destroy error: ' . $e->getMessage());
            toast('Gagal menghapus pegawai. Data mungkin masih terhubung ke sistem lain.', 'error');
        }

        return back();
    }
}
