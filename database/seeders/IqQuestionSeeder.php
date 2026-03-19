<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IqQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            [
                'question_text' => 'Jika 5 mesin butuh 5 menit untuk membuat 5 perangkat, berapa menit waktu yang dibutuhkan 100 mesin untuk membuat 100 perangkat?',
                'option_a' => '100 menit',
                'option_b' => '50 menit',
                'option_c' => '5 menit',
                'option_d' => '1 menit',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Pola angka: 2, 4, 8, 16, 32, ... Angka selanjutnya adalah?',
                'option_a' => '48',
                'option_b' => '64',
                'option_c' => '56',
                'option_d' => '72',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Buku berkaitan dengan Membaca sebagaimana Garpu berkaitan dengan...',
                'option_a' => 'Makan',
                'option_b' => 'Dapur',
                'option_c' => 'Sendok',
                'option_d' => 'Besi',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Manakah yang tidak termasuk dalam kelompoknya?',
                'option_a' => 'Apel',
                'option_b' => 'Anggur',
                'option_c' => 'Wortel',
                'option_d' => 'Pisang',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Jika \'Kucing\' adalah 6, \'Kuda\' adalah 4, \'Gajah\' adalah 5. Maka \'Jerapah\' adalah?',
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'correct_option' => 'C', 
            ],
            [
                'question_text' => 'Budi lebih tinggi dari Andi. Cika lebih tinggi dari Budi. Siapa yang paling pendek?',
                'option_a' => 'Andi',
                'option_b' => 'Budi',
                'option_c' => 'Cika',
                'option_d' => 'Tidak bisa ditentukan',
                'correct_option' => 'A',
            ],
            [
                'question_text' => 'Ada 3 buah apel di meja, kamu mengambil 2. Berapa apel yang kamu miliki sekarang?',
                'option_a' => '1',
                'option_b' => '2',
                'option_c' => '3',
                'option_d' => '0',
                'correct_option' => 'B',
            ],
            [
                'question_text' => 'Angka berapa yang melengkapi: 1, 1, 2, 3, 5, 8, ...',
                'option_a' => '11',
                'option_b' => '12',
                'option_c' => '13',
                'option_d' => '15',
                'correct_option' => 'C',
            ],
            [
                'question_text' => 'Sebuah kereta listrik bergerak ke utara dengan kecepatan 100 km/jam. Ke mana arah asapnya berhembus?',
                'option_a' => 'Utara',
                'option_b' => 'Selatan',
                'option_c' => 'Timur',
                'option_d' => 'Kereta tidak mengeluarkan asap',
                'correct_option' => 'D',
            ],
            [
                'question_text' => 'Satu-satunya saudara perempuan saudara laki-lakiku adalah...',
                'option_a' => 'Bibi',
                'option_b' => 'Kakek',
                'option_c' => 'Kakak ipar',
                'option_d' => 'Saya sendiri / Saudari',
                'correct_option' => 'D',
            ],
        ];

        foreach ($questions as $q) {
            \App\Models\IqQuestion::create($q);
        }
    }
}
