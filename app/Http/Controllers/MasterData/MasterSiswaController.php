<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Imports\SiswaImport;
use Maatwebsite\Excel\Facades\Excel;

class MasterSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterSiswa::with('user'); // Eager load relasi user

        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%')
                ->orWhere('nis', 'like', '%' . $request->search . '%');
        }

        $siswa = $query->latest()->paginate(10);
        return view('pages.master-data.siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('pages.master-data.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:master_siswa,nis',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
        ]);

        try {
            MasterSiswa::create($request->all());
            toast('Data siswa berhasil ditambahkan.', 'success');
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            Log::error('Error storing student: ' . $e->getMessage());
            toast('Gagal menambahkan data siswa.', 'error');
            return back()->withInput();
        }
    }

    public function edit(MasterSiswa $siswa)
    {
        return view('pages.master-data.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, MasterSiswa $siswa)
    {
        $request->validate([
            'nis' => 'required|string|unique:master_siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
        ]);

        try {
            $siswa->update($request->all());
            toast('Data siswa berhasil diperbarui.', 'success');
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            toast('Gagal memperbarui data siswa.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(MasterSiswa $siswa)
    {
        try {
            // Hapus juga user akun jika ada
            if ($siswa->user) {
                $siswa->user->delete();
            }
            $siswa->delete();
            toast('Data siswa berhasil dihapus.', 'success');
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            toast('Gagal menghapus data siswa.', 'error');
            return back();
        }
    }

    /**
     * Fitur spesial untuk membuat akun login untuk siswa.
     */
    public function generateAkun(MasterSiswa $master_siswa)
    {
        // 1. Cek apakah akun sudah ada
        if ($master_siswa->user_id) {
            toast('Akun untuk siswa ini sudah ada.', 'error');
            return back();
        }

        DB::beginTransaction();
        try {
            // 2. Tentukan email dan password default
            $email = $master_siswa->nis . '@smktelkom-lpg.sch.id';
            $password = 'smktelkom'; // Password default

            // 3. Buat user baru
            $user = User::create([
                'name' => $master_siswa->nama_lengkap,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            // 4. Beri peran 'Siswa'
            $role = Role::findByName('Siswa');
            $user->assignRole($role);

            // 5. Hubungkan user baru ke data master siswa
            $master_siswa->update(['user_id' => $user->id]);

            DB::commit();
            toast('Akun berhasil dibuat! Email: ' . $email . ' | Pass: ' . $password, 'success')->autoClose(10000);
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating student account: ' . $e->getMessage());
            // Cek jika error karena email duplikat
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                toast('Gagal! Email atau data lain sudah terdaftar.', 'error');
            } else {
                toast('Terjadi kesalahan saat membuat akun.', 'error');
            }
            return back();
        }
    }

    /**
     * Fitur untuk membuat akun login untuk semua siswa yang belum punya.
     */
    public function generateAkunMasal()
    {
        $siswaTanpaAkun = MasterSiswa::whereNull('user_id')->get();

        if ($siswaTanpaAkun->isEmpty()) {
            toast('Semua siswa sudah memiliki akun.', 'info');
            return back();
        }

        $berhasil = 0;
        $gagal = 0;
        $roleSiswa = Role::findByName('Siswa');
        $passwordDefault = 'smktelkom';

        DB::beginTransaction();
        try {
            foreach ($siswaTanpaAkun as $siswa) {
                // Cek jika email (berdasarkan NIS) sudah ada di tabel users
                $email = $siswa->nis . '@smktelkom-lpg.sch.id';
                if (User::where('email', $email)->exists()) {
                    $gagal++;
                    continue; // Lanjut ke siswa berikutnya jika email sudah ada
                }

                $user = User::create([
                    'name' => $siswa->nama_lengkap,
                    'email' => $email,
                    'password' => Hash::make($passwordDefault),
                ]);

                $user->assignRole($roleSiswa);
                $siswa->update(['user_id' => $user->id]);
                $berhasil++;
            }

            DB::commit();
            toast("Proses selesai! $berhasil akun berhasil dibuat, $gagal gagal (email duplikat).", 'success')->autoClose(10000);
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating mass student accounts: ' . $e->getMessage());
            toast('Terjadi kesalahan fatal saat proses generate akun masal.', 'error');
            return back();
        }
    }

    /**
     * Fitur untuk mereset password akun siswa ke default.
     */
    public function resetPassword(MasterSiswa $master_siswa)
    {
        if (!$master_siswa->user) {
            toast('Siswa ini tidak memiliki akun untuk direset.', 'error');
            return back();
        }

        try {
            $passwordDefault = 'smktelkom';
            $master_siswa->user->update([
                'password' => Hash::make($passwordDefault)
            ]);

            toast('Password berhasil direset ke: ' . $passwordDefault, 'success');
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            Log::error('Error resetting student password: ' . $e->getMessage());
            toast('Gagal mereset password.', 'error');
            return back();
        }
    }

    /**
     * Method baru untuk menangani import Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv|max:2048', // Maks 2MB
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_import'));

            toast('Data siswa berhasil diimpor!', 'success');
            return redirect()->route('master-data.siswa.index');
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            toast('Gagal mengimpor data. Pastikan format file benar.', 'error');
            return back();
        }
    }
}
