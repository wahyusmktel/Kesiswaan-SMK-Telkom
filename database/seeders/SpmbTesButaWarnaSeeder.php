<?php

namespace Database\Seeders;

use App\Models\SpmbTesButaWarnaSoal;
use Illuminate\Database\Seeder;

class SpmbTesButaWarnaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $soals = [
            [
                'nomor_urut' => 1,
                'judul' => 'Soal nomor 1',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/1.png',
                'pilihan_opsi' => ['2', '8', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '2',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 2,
                'judul' => 'Soal nomor 2',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/2.png',
                'pilihan_opsi' => ['5', '2', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '5',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 3,
                'judul' => 'Soal nomor 3',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/3.png',
                'pilihan_opsi' => ['9', '8', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '9',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 4,
                'judul' => 'Soal nomor 4',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/4.png',
                'pilihan_opsi' => ['3', '8', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '3',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 5,
                'judul' => 'Soal nomor 5',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/5.png',
                'pilihan_opsi' => ['3', '5', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '3',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 6,
                'judul' => 'Soal nomor 6',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/6.png',
                'pilihan_opsi' => ['6', '5', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '6',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 7,
                'judul' => 'Soal nomor 7',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/7.png',
                'pilihan_opsi' => ['57', '35', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '57',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 8,
                'judul' => 'Soal nomor 8',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/8.png',
                'pilihan_opsi' => ['26', '6', '2'],
                'kunci_jawaban' => '26',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 9,
                'judul' => 'Soal nomor 9',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/9.png',
                'pilihan_opsi' => ['71', '74', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '71',
                'bobot' => 10,
                'is_active' => true,
            ],
            [
                'nomor_urut' => 10,
                'judul' => 'Soal nomor 10',
                'petunjuk' => 'Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut',
                'gambar' => 'images/soal-tes-buta-warna/10.png',
                'pilihan_opsi' => ['73', '23', 'Tidak Terlihat Angka'],
                'kunci_jawaban' => '73',
                'bobot' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($soals as $item) {
            SpmbTesButaWarnaSoal::updateOrCreate(
                ['nomor_urut' => $item['nomor_urut']],
                $item
            );
        }
    }
}
