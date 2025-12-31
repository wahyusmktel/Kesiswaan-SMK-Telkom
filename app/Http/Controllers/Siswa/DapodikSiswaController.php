<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\DapodikSiswa;
use Illuminate\Support\Facades\Auth;

class DapodikSiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        $siswa->load('dapodik', 'rombels');
        
        // Create empty dapodik if not exists
        if (!$siswa->dapodik) {
            $siswa->setRelation('dapodik', new \App\Models\DapodikSiswa([
                'master_siswa_id' => $siswa->id,
            ]));
        }
        
        return view('pages.siswa.dapodik.index', compact('siswa'));
    }

    public function edit()
    {
        $siswa = Auth::user()->masterSiswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        $siswa->load('dapodik');

        // Check for pending submission
        $pendingSubmission = $siswa->dapodikSubmissions()->where('status', 'pending')->first();
        if ($pendingSubmission) {
            return redirect()->route('siswa.dapodik.submissions')->with('warning', 'Anda masih memiliki pengajuan perubahan data yang belum diproses.');
        }

        if (!$siswa->dapodik) {
            $siswa->setRelation('dapodik', new \App\Models\DapodikSiswa([
                'master_siswa_id' => $siswa->id,
            ]));
        }

        return view('pages.siswa.dapodik.edit', compact('siswa'));
    }

    public function storeSubmission(\Illuminate\Http\Request $request)
    {
        $siswa = Auth::user()->masterSiswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Validate
        $validated = $request->validate([
            // Data Pribadi
            'nama_lengkap' => 'nullable|string|max:255',
            'nipd' => 'nullable|string|max:255',
            'nisn' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:255',
            'sekolah_asal' => 'nullable|string|max:255',
            
            // Alamat & Kontak
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'jenis_tinggal' => 'nullable|string|max:255',
            'alat_transportasi' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'lintang' => 'nullable|string|max:255',
            'bujur' => 'nullable|string|max:255',

            // Data Ayah
            'nama_ayah' => 'nullable|string|max:255',
            'tahun_lahir_ayah' => 'nullable|string|max:4',
            'nik_ayah' => 'nullable|string|max:20',
            'jenjang_pendidikan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'penghasilan_ayah' => 'nullable|string|max:255',

            // Data Ibu
            'nama_ibu' => 'nullable|string|max:255',
            'tahun_lahir_ibu' => 'nullable|string|max:4',
            'nik_ibu' => 'nullable|string|max:20',
            'jenjang_pendidikan_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'penghasilan_ibu' => 'nullable|string|max:255',

            // Data Wali
            'nama_wali' => 'nullable|string|max:255',
            'tahun_lahir_wali' => 'nullable|string|max:4',
            'nik_wali' => 'nullable|string|max:20',
            'jenjang_pendidikan_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'penghasilan_wali' => 'nullable|string|max:255',

            // Dokumen & Bantuan
            'no_seri_ijazah' => 'nullable|string|max:255',
            'no_registrasi_akta_lahir' => 'nullable|string|max:255',
            'no_kk' => 'nullable|string|max:255',
            'penerima_kps' => 'nullable|string|max:255',
            'no_kps' => 'nullable|string|max:255',
            'penerima_kip' => 'nullable|string|max:255',
            'nomor_kip' => 'nullable|string|max:255',
            'nama_di_kip' => 'nullable|string|max:255',
            'nomor_kks' => 'nullable|string|max:255',
            'layak_pip' => 'nullable|string|max:255',
            'alasan_layak_pip' => 'nullable|string|max:255',

            // Data Lainnya
            'bank' => 'nullable|string|max:255',
            'nomor_rekening_bank' => 'nullable|string|max:255',
            'rekening_atas_nama' => 'nullable|string|max:255',
            'kebutuhan_khusus' => 'nullable|string|max:255',
            'anak_ke_berapa' => 'nullable|integer',
            'jumlah_saudara_kandung' => 'nullable|integer',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lingkar_kepala' => 'nullable|numeric',
            'jarak_rumah_ke_sekolah' => 'nullable|numeric',

            // Attachments
            'doc_ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_akta' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_kps' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_kip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_kks' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'doc_rekening' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $attachments = [];
        $docFields = ['doc_ijazah', 'doc_kk', 'doc_akta', 'doc_kps', 'doc_kip', 'doc_kks', 'doc_rekening'];

        // CATEGORY CHANGE DETECTION FOR SERVER-SIDE VALIDATION
        $categoryFields = [
            'ijazah' => ['nama_lengkap', 'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal', 'no_seri_ijazah'],
            'kk' => ['alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'nama_ayah', 'nama_ibu', 'nama_wali', 'no_kk', 'anak_ke_berapa', 'jumlah_saudara_kandung'],
            'akta' => ['no_registrasi_akta_lahir'],
            'kps' => ['penerima_kps', 'no_kps'],
            'kip' => ['penerima_kip', 'nomor_kip', 'nama_di_kip'],
            'kks' => ['nomor_kks'],
            'rekening' => ['bank', 'nomor_rekening_bank', 'rekening_atas_nama'],
        ];

        $errors = [];
        foreach ($categoryFields as $docType => $fields) {
            $changed = false;
            foreach ($fields as $field) {
                $currentValue = ($field === 'nama_lengkap') ? $siswa->nama_lengkap : ($siswa->dapodik->$field ?? null);
                if ($request->has($field) && $request->input($field) != $currentValue) {
                    $changed = true;
                    break;
                }
            }

            if ($changed && !$request->hasFile("doc_$docType")) {
                $errors["doc_$docType"] = "Lampiran $docType wajib diunggah karena terdapat perubahan pada data terkait.";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        foreach ($docFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("dapodik/submissions/{$siswa->id}", 'public');
                $attachments[$field] = $path;
            }
        }

        // Remove attachments from data array to be stored in old_data/new_data
        $data = collect($validated)->except($docFields)->toArray();

        $oldData = $siswa->dapodik ? collect($siswa->dapodik->toArray())->except(['id', 'master_siswa_id', 'created_at', 'updated_at'])->toArray() : [];

        \App\Models\DapodikSubmission::create([
            'master_siswa_id' => $siswa->id,
            'old_data' => $oldData,
            'new_data' => $data,
            'attachments' => $attachments,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return redirect()->route('siswa.dapodik.submissions')->with('success', 'Pengajuan perubahan data berhasil dikirim dan menunggu verifikasi Operator.');
    }

    public function submissions()
    {
        $siswa = Auth::user()->masterSiswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard.index')->with('error', 'Data siswa tidak ditemukan.');
        }

        $submissions = $siswa->dapodikSubmissions()->latest()->paginate(10);

        return view('pages.siswa.dapodik.history', compact('submissions'));
    }
}
