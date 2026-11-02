<?php

namespace Database\Seeders;

use App\Models\SpmbTesBacaQuranRubrik;
use Illuminate\Database\Seeder;

class SpmbTesBacaQuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubriks = [
            [
                'nomor_urut' => 1,
                'kategori' => 'penilaian',
                'aspek' => 'TAJWID',
                'petunjuk' => "Silahkan pilih sesuai dengan hasil tes baca Al-Qur'an yang telah dilakukan",
                'tipe_input' => 'radio',
                'pilihan_opsi' => ['SANGAT BAIK', 'BAIK', 'CUKUP', 'KURANG'],
                'skor_bobot' => [
                    'SANGAT BAIK' => 100,
                    'BAIK' => 80,
                    'CUKUP' => 65,
                    'KURANG' => 50,
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 2,
                'kategori' => 'penilaian',
                'aspek' => 'MAKHARIJUL HURUF',
                'petunjuk' => "Silahkan pilih sesuai dengan hasil tes baca Al-Qur'an yang telah dilakukan",
                'tipe_input' => 'radio',
                'pilihan_opsi' => ['SANGAT BAIK', 'BAIK', 'CUKUP', 'KURANG'],
                'skor_bobot' => [
                    'SANGAT BAIK' => 100,
                    'BAIK' => 80,
                    'CUKUP' => 65,
                    'KURANG' => 50,
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 3,
                'kategori' => 'penilaian',
                'aspek' => 'KELANCARAN',
                'petunjuk' => "Silahkan pilih sesuai dengan hasil tes baca Al-Qur'an yang telah dilakukan",
                'tipe_input' => 'radio',
                'pilihan_opsi' => ['BAIK', 'CUKUP', 'KURANG'],
                'skor_bobot' => [
                    'BAIK' => 100,
                    'CUKUP' => 75,
                    'KURANG' => 50,
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 4,
                'kategori' => 'rekapitulasi',
                'aspek' => "REKAPITULASI HASIL BACA AL-QUR'AN",
                'petunjuk' => "Silahkan pilih dengan pertimbangan keseluruhan hasil tes baca Al-Qur'an yang telah dilakukan",
                'tipe_input' => 'radio',
                'pilihan_opsi' => ['SANGAT BAIK', 'BAIK', 'CUKUP', 'KURANG'],
                'skor_bobot' => [
                    'SANGAT BAIK' => 100,
                    'BAIK' => 80,
                    'CUKUP' => 65,
                    'KURANG' => 50,
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 5,
                'kategori' => 'media',
                'aspek' => 'UPLOAD REKAMAN TES',
                'petunjuk' => "Silahkan upload hasil rekaman tes baca Al-Qur'an",
                'tipe_input' => 'file_upload',
                'pilihan_opsi' => null,
                'skor_bobot' => null,
                'is_required' => false,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 6,
                'kategori' => 'catatan',
                'aspek' => 'CATATAN',
                'petunjuk' => 'Silahkan berikan catatan secara keseluruhan',
                'tipe_input' => 'textarea',
                'pilihan_opsi' => null,
                'skor_bobot' => null,
                'is_required' => false,
                'is_active' => true,
            ],
        ];

        foreach ($rubriks as $item) {
            SpmbTesBacaQuranRubrik::updateOrCreate(
                ['nomor_urut' => $item['nomor_urut']],
                $item
            );
        }
    }
}
