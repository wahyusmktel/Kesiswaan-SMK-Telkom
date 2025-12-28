<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\DapodikSiswa;
use Illuminate\Http\Request;

class DapodikSiswaController extends Controller
{
    /**
     * Display the dapodik data for a specific student.
     */
    public function show(MasterSiswa $siswa)
    {
        $siswa->load('dapodik', 'rombels');
        
        // Create empty dapodik if not exists
        if (!$siswa->dapodik) {
            $siswa->setRelation('dapodik', new DapodikSiswa([
                'master_siswa_id' => $siswa->id,
            ]));
        }
        
        return view('pages.master-data.siswa.dapodik.show', compact('siswa'));
    }

    /**
     * Show the form for editing dapodik data.
     */
    public function edit(MasterSiswa $siswa)
    {
        $siswa->load('dapodik', 'rombels');
        
        // Create empty dapodik if not exists
        if (!$siswa->dapodik) {
            $siswa->setRelation('dapodik', new DapodikSiswa([
                'master_siswa_id' => $siswa->id,
            ]));
        }
        
        return view('pages.master-data.siswa.dapodik.edit', compact('siswa'));
    }

    /**
     * Update the dapodik data in storage.
     */
    public function update(Request $request, MasterSiswa $siswa)
    {
        $validated = $request->validate([
            // Data Pribadi
            'nipd' => 'nullable|string|max:255',
            'nisn' => 'nullable|string|max:255',
            'nik' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:255',
            // Alamat
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            // Kontak dan Transportasi
            'jenis_tinggal' => 'nullable|string|max:255',
            'alat_transportasi' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            // Dokumen
            'skhun' => 'nullable|string|max:255',
            'no_peserta_ujian_nasional' => 'nullable|string|max:255',
            'no_seri_ijazah' => 'nullable|string|max:255',
            'no_registrasi_akta_lahir' => 'nullable|string|max:255',
            'no_kk' => 'nullable|string|max:20',
            // KPS/KIP
            'penerima_kps' => 'nullable|string|max:255',
            'no_kps' => 'nullable|string|max:255',
            'penerima_kip' => 'nullable|string|max:255',
            'nomor_kip' => 'nullable|string|max:255',
            'nama_di_kip' => 'nullable|string|max:255',
            'nomor_kks' => 'nullable|string|max:255',
            // PIP
            'layak_pip' => 'nullable|string|max:255',
            'alasan_layak_pip' => 'nullable|string',
            // Bank
            'bank' => 'nullable|string|max:255',
            'nomor_rekening_bank' => 'nullable|string|max:255',
            'rekening_atas_nama' => 'nullable|string|max:255',
            // Data Ayah
            'nama_ayah' => 'nullable|string|max:255',
            'tahun_lahir_ayah' => 'nullable|string|max:4',
            'jenjang_pendidikan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'penghasilan_ayah' => 'nullable|string|max:255',
            'nik_ayah' => 'nullable|string|max:20',
            // Data Ibu
            'nama_ibu' => 'nullable|string|max:255',
            'tahun_lahir_ibu' => 'nullable|string|max:4',
            'jenjang_pendidikan_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'penghasilan_ibu' => 'nullable|string|max:255',
            'nik_ibu' => 'nullable|string|max:20',
            // Data Wali
            'nama_wali' => 'nullable|string|max:255',
            'tahun_lahir_wali' => 'nullable|string|max:4',
            'jenjang_pendidikan_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'penghasilan_wali' => 'nullable|string|max:255',
            'nik_wali' => 'nullable|string|max:20',
            // Lainnya
            'rombel_saat_ini' => 'nullable|string|max:255',
            'kebutuhan_khusus' => 'nullable|string|max:255',
            'sekolah_asal' => 'nullable|string|max:255',
            'anak_ke_berapa' => 'nullable|integer',
            'lintang' => 'nullable|string|max:20',
            'bujur' => 'nullable|string|max:20',
            'berat_badan' => 'nullable|integer',
            'tinggi_badan' => 'nullable|integer',
            'lingkar_kepala' => 'nullable|integer',
            'jumlah_saudara_kandung' => 'nullable|integer',
            'jarak_rumah_ke_sekolah' => 'nullable|numeric',
        ]);

        $validated['master_siswa_id'] = $siswa->id;

        DapodikSiswa::updateOrCreate(
            ['master_siswa_id' => $siswa->id],
            $validated
        );

        return redirect()->route('master-data.siswa.dapodik.show', $siswa)
            ->with('success', 'Data Dapodik berhasil diperbarui.');
    }
}
