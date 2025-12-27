<?php

namespace App\Http\Controllers;

use App\Models\IzinMeninggalkanKelas;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    /**
     * Menampilkan halaman verifikasi keabsahan surat izin.
     *
     * @param  string  $uuid
     * @return \Illuminate\View\View
     */
    public function show(string $uuid)
    {
        $izin = IzinMeninggalkanKelas::with([
            'siswa.masterSiswa.rombels.kelas',
            'guruKelasApprover',
            'guruPiketApprover',
            'securityVerifier'
        ])->where('uuid', $uuid)->firstOrFail();

        return view('pages.verifikasi.show', compact('izin'));
    }

    public function kartuPelajar(string $nis)
    {
        $siswa = \App\Models\MasterSiswa::with(['rombels.kelas', 'rombels.tahunPelajaran'])
            ->where('nis', $nis)
            ->firstOrFail();

        // Get active rombel
        $rombel = $siswa->rombels->where('tahunPelajaran.is_active', true)->first() 
                ?? $siswa->rombels->first();

        // Get school settings
        $settings = \App\Models\AppSetting::first();

        return view('pages.verifikasi.kartu', compact('siswa', 'rombel', 'settings'));
    }
}
