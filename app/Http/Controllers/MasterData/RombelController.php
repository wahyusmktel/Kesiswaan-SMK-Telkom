<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\User;
use App\Models\TahunPelajaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class RombelController extends Controller
{
    /**
     * Menampilkan daftar rombel + Data untuk Dropdown Modal
     */
    public function index(Request $request)
    {
        // 1. Eager Load 'tahunPelajaran' (Relasi)
        $query = Rombel::with(['kelas', 'waliKelas', 'tahunPelajaran'])->withCount('siswa');

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('nama_kelas', 'like', '%' . $request->search . '%');
            })
                ->orWhereHas('waliKelas', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('tahunPelajaran', function ($q) use ($request) {
                    $q->where('tahun', 'like', '%' . $request->search . '%');
                });
        }

        $rombel = $query->latest()->paginate(10);

        // 3. Data Dropdown Tahun Pelajaran
        // PENTING: Gunakan nama variabel '$tahun_pelajaran' agar sesuai dengan View
        // Kita ambil Collection object (get) bukan array (pluck/mapWithKeys)
        // agar di Blade bisa akses $tp->id, $tp->tahun, dll.
        $tahun_pelajaran = TahunPelajaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Ambil ID Tahun Aktif sebagai default value
        $tahun_aktif_id = TahunPelajaran::where('is_active', true)->value('id');

        $kelas = Kelas::orderBy('nama_kelas')->pluck('nama_kelas', 'id');
        $wali_kelas = User::role('Wali Kelas')->orderBy('name')->pluck('name', 'id');

        // Kirim variabel '$tahun_pelajaran' ke view
        return view('pages.master-data.rombel.index', compact(
            'rombel',
            'kelas',
            'wali_kelas',
            'tahun_pelajaran', // <--- Pastikan ini ada!
            'tahun_aktif_id'
        ));
    }

    public function store(Request $request)
    {
        // Validasi menggunakan ID Relasi
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
        ]);

        try {
            $data = $request->all();

            // Fix SQL Error 1364: Field 'tahun_ajaran' doesn't have a default value
            $tp = TahunPelajaran::findOrFail($request->tahun_pelajaran_id);
            $data['tahun_ajaran'] = $tp->tahun;

            $rombel = Rombel::create($data);
            Log::info('User ' . auth()->user()->name . ' created rombel ID: ' . $rombel->id);
            toast('Data rombel berhasil ditambahkan.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error storing rombel: ' . $e->getMessage());
            toast('Gagal menambahkan data rombel.', 'error');
            return back()->withInput();
        }
    }

    public function update(Request $request, Rombel $rombel)
    {
        $request->validate([
            'tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'wali_kelas_id' => 'required|exists:users,id',
        ]);

        try {
            $data = $request->all();

            // Fix SQL Error 1364: Field 'tahun_ajaran' doesn't have a default value
            $tp = TahunPelajaran::findOrFail($request->tahun_pelajaran_id);
            $data['tahun_ajaran'] = $tp->tahun;

            $rombel->update($data);
            Log::info('User ' . auth()->user()->name . ' updated rombel ID: ' . $rombel->id);
            toast('Data rombel berhasil diperbarui.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error updating rombel: ' . $e->getMessage());
            toast('Gagal memperbarui data rombel.', 'error');
            return back()->withInput();
        }
    }

    public function destroy(Rombel $rombel)
    {
        if ($rombel->siswa()->exists()) {
            Log::warning('User ' . auth()->user()->name . ' attempted to delete rombel ID: ' . $rombel->id . ' but it has students.');
            toast('Gagal menghapus! Masih ada siswa terdaftar di rombel ini.', 'error');
            return back();
        }

        try {
            $id = $rombel->id;
            $rombel->delete();
            Log::info('User ' . auth()->user()->name . ' deleted rombel ID: ' . $id);
            toast('Data rombel berhasil dihapus.', 'success');
            return redirect()->route('master-data.rombel.index');
        } catch (\Exception $e) {
            Log::error('Error deleting rombel: ' . $e->getMessage());
            toast('Gagal menghapus data rombel.', 'error');
            return back();
        }
    }

    /**
     * Halaman Detail untuk Add/Remove Siswa
     */
    public function show(Rombel $rombel)
    {
        // 1. Ambil siswa yang sudah ada di rombel ini
        $siswaDiRombel = $rombel->siswa()->orderBy('nama_lengkap')->get();

        // 2. Ambil siswa yang tersedia
        // Logic: Siswa belum punya rombel DI TAHUN PELAJARAN YANG SAMA
        // (Boleh punya rombel di tahun lalu, tapi tahun ini harus kosong)

        $currentTahunId = $rombel->tahun_pelajaran_id;

        $siswaTersedia = MasterSiswa::with('dapodik')
            ->active()
            ->whereNotIn('id', function ($query) use ($currentTahunId) {
                $query->select('master_siswa_id')
                    ->from('rombel_siswa')
                    ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
                    ->where('rombels.tahun_pelajaran_id', $currentTahunId); // Filter by ID Tahun
            })->orderBy('nama_lengkap')->get();

        // 3. Ambil daftar rombel dari Dapodik untuk filter
        $rombelDapodikOptions = \App\Models\DapodikSiswa::whereNotNull('rombel_saat_ini')
            ->where('rombel_saat_ini', '!=', '')
            ->distinct()
            ->pluck('rombel_saat_ini')
            ->sort()
            ->values();

        return view('pages.master-data.rombel.show', compact('rombel', 'siswaDiRombel', 'siswaTersedia', 'rombelDapodikOptions'));
    }

    /**
     * Tambah Siswa ke Rombel (Logic Tetap Sama)
     */
    public function addSiswa(Request $request, Rombel $rombel)
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => [
                'exists:master_siswa,id',
                function ($attribute, $value, $fail) {
                    if (!MasterSiswa::active()->whereKey($value)->exists()) {
                        $fail('Siswa alumni tidak dapat dimasukkan ke rombel aktif.');
                    }
                },
            ],
        ]);

        try {
            $rombel->siswa()->attach($request->siswa_ids);
            Log::info('User ' . auth()->user()->name . ' added students ' . implode(', ', $request->siswa_ids) . ' to rombel ID: ' . $rombel->id);
            toast(count($request->siswa_ids) . ' Siswa berhasil ditambahkan.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error adding students to rombel: ' . $e->getMessage());
            toast('Gagal menambahkan siswa.', 'error');
            return back();
        }
    }

    /**
     * Hapus Siswa dari Rombel (Logic Tetap Sama)
     */
    public function removeSiswa(Rombel $rombel, MasterSiswa $siswa)
    {
        try {
            $rombel->siswa()->detach($siswa->id);
            Log::info('User ' . auth()->user()->name . ' removed student ID: ' . $siswa->id . ' from rombel ID: ' . $rombel->id);
            toast('Siswa berhasil dikeluarkan dari rombel.', 'success');
            return back();
        } catch (\Exception $e) {
            Log::error('Error removing student from rombel: ' . $e->getMessage());
            toast('Gagal mengeluarkan siswa.', 'error');
            return back();
        }
    }

    /**
     * Unduh seluruh akun siswa dalam bentuk ZIP berisi PDF per rombel.
     */
    public function downloadAccountsZip(Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        abort_unless(class_exists(ZipArchive::class), 500, 'Ekstensi PHP ZIP belum tersedia pada server.');

        $activeYear = TahunPelajaran::where('is_active', true)->first();

        $query = Rombel::with([
            'kelas',
            'waliKelas',
            'tahunPelajaran',
            'siswa' => function ($q) {
                $q->with(['user', 'dapodik'])->orderBy('nama_lengkap');
            }
        ]);

        if ($request->filled('tahun_pelajaran_id')) {
            $query->where('tahun_pelajaran_id', $request->tahun_pelajaran_id);
        } elseif ($activeYear) {
            $query->where(function ($q) use ($activeYear) {
                $q->where('tahun_pelajaran_id', $activeYear->id)
                    ->orWhere(function ($fallback) use ($activeYear) {
                        $fallback->whereNull('tahun_pelajaran_id')
                            ->where('tahun_ajaran', $activeYear->tahun);
                    });
            });
        }

        $rombels = $query->get()->sortBy('kelas.nama_kelas')->values();
        $rombelsWithStudents = $rombels->filter(fn($r) => $r->siswa->isNotEmpty());

        // Jika tahun aktif kosong / belum ada rombel bersiswa, fallback ke semua rombel yang memiliki siswa
        if ($rombelsWithStudents->isEmpty()) {
            $rombelsWithStudents = Rombel::with([
                'kelas',
                'waliKelas',
                'tahunPelajaran',
                'siswa' => function ($q) {
                    $q->with(['user', 'dapodik'])->orderBy('nama_lengkap');
                }
            ])->get()->filter(fn($r) => $r->siswa->isNotEmpty())->sortBy('kelas.nama_kelas')->values();
        }

        if ($rombelsWithStudents->isEmpty()) {
            toast('Tidak ada data rombel dengan siswa untuk diunduh.', 'warning');
            return back();
        }

        $schoolSetting = AppSetting::first();
        $logoBase64 = null;
        if ($schoolSetting?->logo && file_exists(public_path('storage/' . $schoolSetting->logo))) {
            $logoPath = public_path('storage/' . $schoolSetting->logo);
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $tempDir = storage_path('app/temp-zips');
        File::ensureDirectoryExists($tempDir);

        $academicYearSlug = $activeYear ? Str::slug($activeYear->tahun, '_') : 'semua_kelas';
        $zipFilename = 'Akun_Siswa_Rombel_' . $academicYearSlug . '_' . date('Ymd_His') . '.zip';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFilename;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            toast('Gagal membuat berkas ZIP.', 'error');
            return back();
        }

        try {
            foreach ($rombelsWithStudents as $rombel) {
                $students = $rombel->siswa;
                $pdf = Pdf::loadView('pdf.rombel-student-accounts', [
                    'rombel' => $rombel,
                    'students' => $students,
                    'schoolSetting' => $schoolSetting,
                    'logoBase64' => $logoBase64,
                ])->setPaper('a4', 'portrait');

                $pdfContent = $pdf->output();

                $className = Str::slug($rombel->kelas?->nama_kelas ?? ('Kelas_' . $rombel->id), '_');
                $pdfFileName = 'Akun_Siswa_' . $className . '.pdf';

                $zip->addFromString($pdfFileName, $pdfContent);
            }

            $zip->close();
        } catch (\Throwable $e) {
            if ($zip->filename) {
                $zip->close();
            }
            File::delete($zipPath);
            Log::error('Error creating student accounts zip: ' . $e->getMessage());
            toast('Terjadi kesalahan saat menghasilkan berkas ZIP: ' . $e->getMessage(), 'error');
            return back();
        }

        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }

    /**
     * Unduh berkas PDF akun siswa untuk satu rombel tertentu.
     */
    public function downloadAccountPdf(Rombel $rombel)
    {
        $rombel->load([
            'kelas',
            'waliKelas',
            'tahunPelajaran',
            'siswa' => function ($q) {
                $q->with(['user', 'dapodik'])->orderBy('nama_lengkap');
            }
        ]);

        if ($rombel->siswa->isEmpty()) {
            toast('Rombel ini belum memiliki siswa terdaftar.', 'warning');
            return back();
        }

        $schoolSetting = AppSetting::first();
        $logoBase64 = null;
        if ($schoolSetting?->logo && file_exists(public_path('storage/' . $schoolSetting->logo))) {
            $logoPath = public_path('storage/' . $schoolSetting->logo);
            $ext = pathinfo($logoPath, PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('pdf.rombel-student-accounts', [
            'rombel' => $rombel,
            'students' => $rombel->siswa,
            'schoolSetting' => $schoolSetting,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'portrait');

        $className = Str::slug($rombel->kelas?->nama_kelas ?? ('Kelas_' . $rombel->id), '_');
        $fileName = 'Akun_Siswa_' . $className . '.pdf';

        return $pdf->download($fileName);
    }
}
