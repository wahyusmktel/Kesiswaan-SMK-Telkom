<?php

namespace Database\Seeders;

use App\Models\SpmbTpaKategori;
use App\Models\SpmbTpaSoal;
use Illuminate\Database\Seeder;

class SpmbTpaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'kode' => 'BINDO',
                'nama' => 'Bahasa Indonesia',
                'deskripsi' => 'Tes pemahaman bacaan, kosa kata, penalaran verbal, logika bahasa, dan kaidah kebahasaan (PUEBI).',
                'durasi_menit' => 20,
                'passing_grade' => 70,
                'jumlah_soal_ditampilkan' => 10,
                'nomor_urut' => 1,
                'warna_tema' => 'red',
                'soals' => [
                    [
                        'pertanyaan' => 'Pilihlah kata yang merupakan SINONIM (persamaan makna) dari kata: "AKSELERASI"',
                        'opsi_a' => 'Percepatan',
                        'opsi_b' => 'Perlambatan',
                        'opsi_c' => 'Perhentian',
                        'opsi_d' => 'Penyesuaian',
                        'opsi_e' => 'Penyederhanaan',
                        'kunci_jawaban' => 'A',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Akselerasi menurut KBBI bermakna percepatan atau peningkatan kecepatan.',
                    ],
                    [
                        'pertanyaan' => 'Pilihlah kata yang merupakan ANTONIM (lawan kata) dari kata: "KONVERGEN"',
                        'opsi_a' => 'Memusat',
                        'opsi_b' => 'Menyebar (Divergen)',
                        'opsi_c' => 'Sejajar (Paralel)',
                        'opsi_d' => 'Stabil',
                        'opsi_e' => 'Tetap',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Konvergen berarti memusat menuju satu titik. Lawan katanya adalah divergen yang bermakna menyebar/memencar.',
                    ],
                    [
                        'pertanyaan' => 'Bacalah paragraf berikut:\n"Perkembangan teknologi kecerdasan buatan (AI) telah mengubah berbagai sektor kehidupan, mulai dari otomasi industri, medis, hingga pendidikan. Di bidang pendidikan, AI mampu memberikan pembelajaran adaptif yang menyesuaikan gaya belajar masing-masing peserta didik secara personal."\n\nIde pokok paragraf di atas adalah...',
                        'opsi_a' => 'AI hanya bermanfaat untuk dunia medis dan industri.',
                        'opsi_b' => 'Perkembangan AI mengubah berbagai sektor kehidupan manusia.',
                        'opsi_c' => 'Siswa tidak perlu guru karena ada teknologi kecerdasan buatan.',
                        'opsi_d' => 'Metode pembelajaran adaptif membutuhkan biaya mahal.',
                        'opsi_e' => 'Gaya belajar peserta didik tidak dapat dipersonalisasi.',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Kalimat utama berada di awal paragraf (deduktif), yaitu mengenai perkembangan kecerdasan buatan yang mengubah berbagai sektor kehidupan.',
                    ],
                    [
                        'pertanyaan' => 'Manakah penulisan kalimat di bawah ini yang mematuhi kaidah Pedoman Umum Ejaan Bahasa Indonesia (PUEBI) dengan benar?',
                        'opsi_a' => 'SMK Telkom Lampung terletak di provinsi Lampung.',
                        'opsi_b' => 'SMK Telkom Lampung terletak di Provinsi Lampung.',
                        'opsi_c' => 'SMK Telkom lampung terletak di propinsi lampung.',
                        'opsi_d' => 'SMK telkom lampung Terletak Di Provinsi Lampung.',
                        'opsi_e' => 'SMK Telkom Lampung terletak diprovinsi Lampung.',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Nama institusi dan nama geografi yang diikuti nama tempat (Provinsi Lampung) ditulis dengan huruf kapital di awal kata.',
                    ],
                    [
                        'pertanyaan' => 'Analogi Kata: "KERTAS : PENA = ... : ..."',
                        'opsi_a' => 'Dinding : Cat',
                        'opsi_b' => 'Kanvas : Kuas',
                        'opsi_c' => 'Papan Tulis : Penghapus',
                        'opsi_d' => 'Komputer : Keyboard',
                        'opsi_e' => 'Buku : Tas',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Pena digunakan untuk menulis/menggores di atas kertas, sebagaimana kuas digunakan untuk melukis di atas kanvas.',
                    ],
                ],
            ],
            [
                'kode' => 'MTK',
                'nama' => 'Matematika',
                'deskripsi' => 'Tes logika numerik, pola barisan angka, aritmetika dasar, aljabar, dan kemampuan problem solving kuantitatif.',
                'durasi_menit' => 25,
                'passing_grade' => 65,
                'jumlah_soal_ditampilkan' => 10,
                'nomor_urut' => 2,
                'warna_tema' => 'blue',
                'soals' => [
                    [
                        'pertanyaan' => 'Perhatikan deret pola angka berikut:\n3, 7, 15, 31, 63, ...\nAngka berikutnya pada barisan tersebut adalah...',
                        'opsi_a' => '95',
                        'opsi_b' => '126',
                        'opsi_c' => '127',
                        'opsi_d' => '128',
                        'opsi_e' => '255',
                        'kunci_jawaban' => 'C',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Pola deret adalah: dikali 2 ditambah 1 (x * 2 + 1). 63 * 2 + 1 = 127. Atau bedanya berturut-turut +4, +8, +16, +32, maka selanjutnya +64 => 63 + 64 = 127.',
                    ],
                    [
                        'pertanyaan' => 'Jika 3x - 5 = 16, maka nilai dari 2x + 7 adalah...',
                        'opsi_a' => '14',
                        'opsi_b' => '19',
                        'opsi_c' => '21',
                        'opsi_d' => '25',
                        'opsi_e' => '28',
                        'kunci_jawaban' => 'C',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => '3x = 16 + 5 = 21 -> x = 7. Maka 2(7) + 7 = 14 + 7 = 21.',
                    ],
                    [
                        'pertanyaan' => 'Sebuah printer kantor mampu mencetak 90 lembar dokumen dalam waktu 3 menit. Berapa lama waktu yang dibutuhkan printer tersebut untuk mencetak 450 lembar dokumen dengan kecepatan yang sama?',
                        'opsi_a' => '12 menit',
                        'opsi_b' => '15 menit',
                        'opsi_c' => '18 menit',
                        'opsi_d' => '20 menit',
                        'opsi_e' => '25 menit',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Kecepatan = 90 / 3 = 30 lembar/menit. Waktu untuk 450 lembar = 450 / 30 = 15 menit.',
                    ],
                    [
                        'pertanyaan' => 'Harga sebuah laptop setelah mendapat diskon 20% adalah Rp 6.400.000. Berapakah harga laptop tersebut sebelum didiskon?',
                        'opsi_a' => 'Rp 7.680.000',
                        'opsi_b' => 'Rp 8.000.000',
                        'opsi_c' => 'Rp 8.200.000',
                        'opsi_d' => 'Rp 8.500.000',
                        'opsi_e' => 'Rp 9.000.000',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Harga setelah diskon = 80% dari harga asli. Harga asli = 6.400.000 / 0.8 = Rp 8.000.000.',
                    ],
                    [
                        'pertanyaan' => 'Sebuah segitiga siku-siku memiliki panjang sisi alas 12 cm dan tinggi 9 cm. Panjang sisi miring (hipotenusa) segitiga tersebut adalah...',
                        'opsi_a' => '13 cm',
                        'opsi_b' => '14 cm',
                        'opsi_c' => '15 cm',
                        'opsi_d' => '17 cm',
                        'opsi_e' => '21 cm',
                        'kunci_jawaban' => 'C',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Teorema Pythagoras: c^2 = a^2 + b^2 = 12^2 + 9^2 = 144 + 81 = 225. Maka c = akar(225) = 15 cm.',
                    ],
                ],
            ],
            [
                'kode' => 'BING',
                'nama' => 'Bahasa Inggris',
                'deskripsi' => 'Tes penguasaan tata bahasa (grammar), kosakata (vocabulary), pemahaman teks, dan percakapan kontekstual.',
                'durasi_menit' => 20,
                'passing_grade' => 65,
                'jumlah_soal_ditampilkan' => 10,
                'nomor_urut' => 3,
                'warna_tema' => 'purple',
                'soals' => [
                    [
                        'pertanyaan' => 'Choose the correct form of verb to complete the sentence:\n"She ... to join SMK Telkom Lampung since she was in junior high school."',
                        'opsi_a' => 'has wanted',
                        'opsi_b' => 'is wanting',
                        'opsi_c' => 'wanted',
                        'opsi_d' => 'will want',
                        'opsi_e' => 'wants',
                        'kunci_jawaban' => 'A',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Kata keterangan waktu "since she was in junior high school" mengindikasikan kegiatan yang dimulai di masa lampau dan masih berlanjut hingga sekarang (Present Perfect Tense: has/have + V3).',
                    ],
                    [
                        'pertanyaan' => 'Rian: "I just won first place in the National Web Programming Competition!"\nAndi: "..."\nWhat is the most suitable response from Andi?',
                        'opsi_a' => 'I am really sorry to hear that.',
                        'opsi_b' => 'Congratulations! You really deserve it after practicing so hard.',
                        'opsi_c' => 'Never mind, better luck next time.',
                        'opsi_d' => 'Are you sure about that?',
                        'opsi_e' => 'I do not care at all.',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Respon yang sopan dan tepat atas pencapaian seseorang adalah ucapan selamat (congratulations).',
                    ],
                    [
                        'pertanyaan' => 'Read the announcement:\n"All prospective students taking the entrance test are required to bring their examination cards and stationery. Electronic devices such as smartwatches must be kept inside bags."\n\nWhat item must NOT be used during the test?',
                        'opsi_a' => 'Pencil',
                        'opsi_b' => 'Eraser',
                        'opsi_c' => 'Examination card',
                        'opsi_d' => 'Smartwatch',
                        'opsi_e' => 'Identity card',
                        'kunci_jawaban' => 'D',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Disebutkan secara eksplisit bahwa perangkat elektronik seperti smartwatch harus disimpan di dalam tas selama tes.',
                    ],
                    [
                        'pertanyaan' => 'Choose the synonym of the underlined word:\n"Technological innovations have made communication much more _convenient_ than before."',
                        'opsi_a' => 'Difficult',
                        'opsi_b' => 'Complicated',
                        'opsi_c' => 'Practical and easy',
                        'opsi_d' => 'Expensive',
                        'opsi_e' => 'Dangerous',
                        'kunci_jawaban' => 'C',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => '"Convenient" bermakna praktis, mudah digunakan, atau nyaman (practical and easy).',
                    ],
                    [
                        'pertanyaan' => 'Complete the sentence:\n"If the weather is fine tomorrow, the students ... their drone flight practice in the school yard."',
                        'opsi_a' => 'would conduct',
                        'opsi_b' => 'will conduct',
                        'opsi_c' => 'conducted',
                        'opsi_d' => 'had conducted',
                        'opsi_e' => 'conducts',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Conditional Sentence Type 1 (kenyataan masa depan): If + Simple Present (is fine), result clause menggunakan Will + Bare Infinitive (will conduct).',
                    ],
                ],
            ],
            [
                'kode' => 'TIK',
                'nama' => 'Dasar TIK',
                'deskripsi' => 'Tes pemahaman dasar teknologi informasi, komponen komputer, jaringan internet, dasar pemrograman, dan literasi digital.',
                'durasi_menit' => 20,
                'passing_grade' => 70,
                'jumlah_soal_ditampilkan' => 10,
                'nomor_urut' => 4,
                'warna_tema' => 'emerald',
                'soals' => [
                    [
                        'pertanyaan' => 'Manakah di bawah ini yang tergolong sebagai perangkat keras masukan (INPUT DEVICE) pada sebuah sistem komputer?',
                        'opsi_a' => 'Monitor',
                        'opsi_b' => 'Printer',
                        'opsi_c' => 'Keyboard dan Scanner',
                        'opsi_d' => 'Speaker',
                        'opsi_e' => 'Proyektor',
                        'kunci_jawaban' => 'C',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Keyboard dan scanner berfungsi memasukkan data/perintah ke komputer (input). Monitor, printer, speaker, dan proyektor adalah output device.',
                    ],
                    [
                        'pertanyaan' => 'Perangkat jaringan yang berfungsi untuk menghubungkan beberapa jaringan komputer yang berbeda serta mengarahkan rute paket data melalui internet adalah...',
                        'opsi_a' => 'Router',
                        'opsi_b' => 'Switch',
                        'opsi_c' => 'Hub',
                        'opsi_d' => 'Repeater',
                        'opsi_e' => 'Kabel UTP',
                        'kunci_jawaban' => 'A',
                        'tingkat_kesulitan' => 'Sedang',
                        'pembahasan' => 'Router adalah perangkat jaringan yang bertugas merutekan (routing) paket data antarjaringan ber-IP yang berbeda (antar segmen/LAN ke WAN).',
                    ],
                    [
                        'pertanyaan' => 'Kombinasi tombol pintasan (shortcut) keyboard standar yang digunakan untuk MENYALIN (Copy) teks atau berkas pada sistem operasi Windows adalah...',
                        'opsi_a' => 'Ctrl + V',
                        'opsi_b' => 'Ctrl + X',
                        'opsi_c' => 'Ctrl + Z',
                        'opsi_d' => 'Ctrl + C',
                        'opsi_e' => 'Ctrl + S',
                        'kunci_jawaban' => 'D',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Ctrl + C = Copy (menyalin), Ctrl + V = Paste (menempel), Ctrl + X = Cut (memotong), Ctrl + Z = Undo, Ctrl + S = Save.',
                    ],
                    [
                        'pertanyaan' => 'Metode penipuan daring di mana pelaku menyamar sebagai instansi resmi melalui pesan palsu atau tautan website tiruan untuk mencuri data pribadi (seperti username dan kata sandi) disebut...',
                        'opsi_a' => 'Firewall',
                        'opsi_b' => 'Phishing',
                        'opsi_c' => 'Debugging',
                        'opsi_d' => 'Defragmentasi',
                        'opsi_e' => 'Bandwidth',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Phishing adalah bentuk kejahatan siber berupa rekayasa sosial untuk memancing korban memberikan kredensial rahasia menggunakan identitas palsu.',
                    ],
                    [
                        'pertanyaan' => 'Perhatikan potongan logika algoritma berikut:\n\nUmur = 16\nIF Umur >= 17 THEN\n    Status = "Bisa Buat KTP"\nELSE\n    Status = "Belum Bisa Buat KTP"\nENDIF\n\nNilai akhir dari variabel Status adalah...',
                        'opsi_a' => '"Bisa Buat KTP"',
                        'opsi_b' => '"Belum Bisa Buat KTP"',
                        'opsi_c' => 'Null / Kosong',
                        'opsi_d' => 'Error tipe data',
                        'opsi_e' => '17',
                        'kunci_jawaban' => 'B',
                        'tingkat_kesulitan' => 'Mudah',
                        'pembahasan' => 'Karena nilai Umur adalah 16, kondisi (16 >= 17) bernilai FALSE, sehingga alur eksekusi masuk ke blok ELSE: Status = "Belum Bisa Buat KTP".',
                    ],
                ],
            ],
        ];

        foreach ($kategoris as $kategoriData) {
            $soals = $kategoriData['soals'];
            unset($kategoriData['soals']);

            $kategori = SpmbTpaKategori::updateOrCreate(
                ['kode' => $kategoriData['kode']],
                $kategoriData
            );
        }

        // Check if Moodle XML files exist in storage, and import authentic bank soal
        $moodleXmlDir = storage_path('app/public/soal-xml-moodle');
        $xmlFiles = \Illuminate\Support\Facades\File::glob($moodleXmlDir . '/*.xml');

        if (! empty($xmlFiles)) {
            $importer = new \App\Services\MoodleXmlImporterService();
            $importer->importAllLocalFiles('replace');
        } else {
            foreach ($kategoris as $kategoriData) {
                $soals = $kategoriData['soals'];
                $kategori = SpmbTpaKategori::where('kode', $kategoriData['kode'])->first();

                foreach ($soals as $idx => $soalData) {
                    $soalData['pertanyaan'] = str_replace(['\r\n', '\n', '\r'], "\n", $soalData['pertanyaan']);
                    if (isset($soalData['pembahasan'])) {
                        $soalData['pembahasan'] = str_replace(['\r\n', '\n', '\r'], "\n", $soalData['pembahasan']);
                    }

                    SpmbTpaSoal::updateOrCreate(
                        [
                            'spmb_tpa_kategori_id' => $kategori->id,
                            'nomor_urut' => $idx + 1,
                        ],
                        array_merge($soalData, [
                            'spmb_tpa_kategori_id' => $kategori->id,
                            'nomor_urut' => $idx + 1,
                            'tipe_soal' => 'pilihan_ganda',
                            'bobot' => 1,
                            'is_active' => true,
                        ])
                    );
                }
            }
        }
    }
}
