<?php

namespace Database\Seeders;

use App\Models\MillionaireQuestion;
use App\Models\MillionaireSet;
use Illuminate\Database\Seeder;

class MillionaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $set = MillionaireSet::create([
            'name' => 'Pengetahuan Umum Dasar',
            'description' => 'Uji pengetahuan umum dasar Anda di sini!',
            'user_id' => 4, // Guru Kelas
            'is_active' => true,
        ]);

        $questions = [
            [
                'question' => 'Apa ibukota Indonesia?',
                'option_a' => 'Bandung', 'option_b' => 'Jakarta', 'option_c' => 'Surabaya', 'option_d' => 'Medan',
                'correct_answer' => 'B', 'level' => 1
            ],
            [
                'question' => 'Berapakah hasil dari 5 + 7?',
                'option_a' => '10', 'option_b' => '11', 'option_c' => '12', 'option_d' => '13',
                'correct_answer' => 'C', 'level' => 2
            ],
            [
                'question' => 'Planet manakah yang dikenal sebagai Planet Merah?',
                'option_a' => 'Venus', 'option_b' => 'Mars', 'option_c' => 'Yupiter', 'option_d' => 'Saturnus',
                'correct_answer' => 'B', 'level' => 3
            ],
            [
                'question' => 'Siapakah penemu bola lampu?',
                'option_a' => 'Isaac Newton', 'option_b' => 'Albert Einstein', 'option_c' => 'Thomas Alva Edison', 'option_d' => 'Nikola Tesla',
                'correct_answer' => 'C', 'level' => 4
            ],
            [
                'question' => 'Samudra terbesar di dunia adalah...',
                'option_a' => 'Hindia', 'option_b' => 'Atlantik', 'option_c' => 'Pasifik', 'option_d' => 'Arktik',
                'correct_answer' => 'C', 'level' => 5
            ],
            [
                'question' => 'Benua terkecil di dunia adalah...',
                'option_a' => 'Eropa', 'option_b' => 'Australia', 'option_c' => 'Afrika', 'option_d' => 'Amerika Selatan',
                'correct_answer' => 'B', 'level' => 6
            ],
            [
                'question' => 'Zat hijau daun disebut...',
                'option_a' => 'Klorofil', 'option_b' => 'Stomata', 'option_c' => 'Fotosisntesis', 'option_d' => 'Epidermis',
                'correct_answer' => 'A', 'level' => 7
            ],
            [
                'question' => 'Negara manakah yang memiliki Menara Eiffel?',
                'option_a' => 'Jerman', 'option_b' => 'Italia', 'option_c' => 'Prancis', 'option_d' => 'Inggris',
                'correct_answer' => 'C', 'level' => 8
            ],
            [
                'question' => 'Hewan mamalia terbesar di dunia adalah...',
                'option_a' => 'Gajah', 'option_b' => 'Paus Biru', 'option_c' => 'Hiu Putih', 'option_d' => 'Badak',
                'correct_answer' => 'B', 'level' => 9
            ],
            [
                'question' => 'Lagu kebangsaan Indonesia adalah...',
                'option_a' => 'Indonesia Pusaka', 'option_b' => 'Indonesia Merdeka', 'option_c' => 'Indonesia Raya', 'option_d' => 'Indonesia Tanah Airku',
                'correct_answer' => 'C', 'level' => 10
            ],
            [
                'question' => 'Sila pertama Pancasila adalah...',
                'option_a' => 'Kemanusiaan yang adil dan beradab', 'option_b' => 'Ketuhanan Yang Maha Esa', 'option_c' => 'Persatuan Indonesia', 'option_d' => 'Keadilan sosial',
                'correct_answer' => 'B', 'level' => 11
            ],
            [
                'question' => 'Logam yang berwujud cair pada suhu ruang adalah...',
                'option_a' => 'Emas', 'option_b' => 'Perak', 'option_c' => 'Raksa', 'option_d' => 'Tembaga',
                'correct_answer' => 'C', 'level' => 12
            ],
            [
                'question' => 'Gunung tertinggi di dunia adalah...',
                'option_a' => 'K2', 'option_b' => 'Gunung Everest', 'option_c' => 'Kilimanjaro', 'option_d' => 'Gunung Fuji',
                'correct_answer' => 'B', 'level' => 13
            ],
            [
                'question' => 'Siapakah penulis novel "Laskar Pelangi"?',
                'option_a' => 'Tere Liye', 'option_b' => 'Andrea Hirata', 'option_c' => 'Habiburrahman El Shirazy', 'option_d' => 'Dewi Lestari',
                'correct_answer' => 'B', 'level' => 14
            ],
            [
                'question' => 'Unsur kimia dengan lambang "Au" adalah...',
                'option_a' => 'Perak', 'option_b' => 'Aluminium', 'option_c' => 'Emas', 'option_d' => 'Tembaga',
                'correct_answer' => 'C', 'level' => 15
            ],
        ];

        foreach ($questions as $q) {
            $q['set_id'] = $set->id;
            MillionaireQuestion::create($q);
        }
    }
}
