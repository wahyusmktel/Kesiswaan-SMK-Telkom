<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    /**
     * Get master data for dropdowns/filters.
     */
    public function getMasterData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'kelas' => Kelas::orderBy('nama_kelas')->get(),
                'rombel' => Rombel::with('waliKelas')->orderBy('nama_rombel')->get(),
                'tahun_pelajaran' => TahunPelajaran::orderBy('tahun', 'desc')->get(),
                'permit_types' => [
                    ['id' => 'Sakit', 'label' => 'Sakit'],
                    ['id' => 'Izin', 'label' => 'Izin'],
                    ['id' => 'Dispensasi', 'label' => 'Dispensasi'],
                ],
                'exit_permit_reasons' => [
                    ['id' => 'Keperluan Keluarga', 'label' => 'Keperluan Keluarga'],
                    ['id' => 'Sakit', 'label' => 'Sakit (Pulang)'],
                    ['id' => 'Lomba/Kegiatan', 'label' => 'Lomba/Kegiatan'],
                    ['id' => 'Lainnya', 'label' => 'Lainnya'],
                ]
            ],
        ]);
    }
}
