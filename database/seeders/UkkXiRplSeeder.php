<?php

namespace Database\Seeders;

use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UkkInstrumen;
use App\Models\UkkInstrumenIndikator;
use App\Models\UkkInstrumenKategori;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkUjian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UkkXiRplSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Dapatkan Tahun Pelajaran Aktif
            $tpAktif = TahunPelajaran::where('is_active', true)->first()
                ?? TahunPelajaran::latest()->first();

            if (!$tpAktif) {
                $this->command->error('Tidak ada Tahun Pelajaran aktif di database.');
                return;
            }

            // 2. Cari Rombel Kelas XI RPL 1 dan XI RPL 2 di TP Aktif
            $targetKelas = ['XI RPL 1', 'XI RPL 2'];
            $rombelIds = [];

            foreach ($targetKelas as $namaKelas) {
                $rombel = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                    ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))
                    ->first();

                if (!$rombel) {
                    $rombel = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))->first();
                }

                if ($rombel) {
                    if ($rombel->siswa()->count() === 0) {
                        $rombelWithStudents = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))
                            ->has('siswa')
                            ->first();

                        if ($rombelWithStudents) {
                            $siswaIds = $rombelWithStudents->siswa()->pluck('master_siswa.id')->all();
                            $rombel->siswa()->syncWithoutDetaching($siswaIds);
                        }
                    }

                    $rombelIds[] = $rombel->id;
                }
            }

            // 3. Buat atau perbarui Paket Ujian UKK Level III RPL (Bilingual)
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Rekayasa Perangkat Lunak',
                    'nama_ujian'         => 'Ujian Kualifikasi Kejuruan Level III RPL',
                ],
                [
                    'nama_project'        => 'Aplikasi Website Etalase Digital Karya Siswa',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => true,
                ]
            );

            // Mapping Rombel XI RPL 1 & XI RPL 2
            if (!empty($rombelIds)) {
                $ujian->rombels()->sync($rombelIds);
            }

            // 4. Tugaskan Penguji:
            // - Ahmad Ikhsan (Kaprodi RPL / Penguji Kejuruan) -> peran: keterampilan
            // - Penguji Pengetahuan Bilingual (Guru Bahasa Inggris / Wahyu Rahmat Hidayat) -> peran: pengetahuan
            $guruRpl = User::role(['Kaprodi', 'Guru Kelas'])
                ->where('name', 'LIKE', '%Ikhsan%')
                ->first();

            $guruInggris = User::role(['Guru Kelas', 'Kaprodi'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Inggris%')
                      ->orWhere('name', 'LIKE', '%Wahyu Rahmat%');
                })
                ->first();

            $syncPenguji = [];
            if ($guruRpl) {
                $syncPenguji[$guruRpl->id] = ['peran' => 'keterampilan'];
            }
            if ($guruInggris) {
                $syncPenguji[$guruInggris->id] = ['peran' => 'pengetahuan'];
            }
            if (empty($syncPenguji)) {
                $firstGuru = User::role(['Kaprodi', 'Guru Kelas'])->first();
                if ($firstGuru) {
                    $syncPenguji[$firstGuru->id] = ['peran' => 'semua'];
                }
            }
            $ujian->penguji()->syncWithoutDetaching($syncPenguji);

            // 5. Buat atau perbarui Instrumen
            $instrumen = UkkInstrumen::updateOrCreate(
                [
                    'ukk_ujian_id'   => $ujian->id,
                    'nama_instrumen' => 'Aplikasi Website Etalase Digital Karya Siswa',
                ],
                [
                    'bobot_pengetahuan' => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan (Bilingual - English Interview)
            $soals = [
                1 => [
                    'pertanyaan'    => 'What is the main purpose of creating a digital showcase website for student works?',
                    'kunci_jawaban' => 'To archive, display, and promote student projects and creative portfolios to the public, parents, and industry partners in an accessible, organized digital gallery.',
                ],
                2 => [
                    'pertanyaan'    => 'Who is the primary target audience for this website, and why are they important?',
                    'kunci_jawaban' => 'Industry partners/recruiters (for internships/hiring), school community/teachers (for assessment/recognition), and prospective students/parents (to see student skills and achievements).',
                ],
                3 => [
                    'pertanyaan'    => 'What are three essential features that should be included on the website?',
                    'kunci_jawaban' => '1. Secure user authentication & multi-role access control (Admin & Student). 2. Complete CRUD management for portfolio works with image upload & GitHub links. 3. Responsive digital showcase gallery with search/filtering capabilities.',
                ],
                4 => [
                    'pertanyaan'    => 'How can students ensure their uploaded works look professional and attractive?',
                    'kunci_jawaban' => 'By providing high-quality cover thumbnails/screenshots, clear descriptive project summaries with tech stack details, live demo/repository links, and maintaining proper aspect ratio & clean formatting.',
                ],
                5 => [
                    'pertanyaan'    => 'What is the biggest challenge in managing a student showcase website, and how can you solve it?',
                    'kunci_jawaban' => 'Challenges include content moderation/quality control and file storage security. Solved by implementing admin approval/moderation workflows, file size/extension validation, and secure upload directory permissions.',
                ],
            ];

            foreach ($soals as $urutan => $data) {
                UkkInstrumenSoal::updateOrCreate(
                    [
                        'instrumen_id' => $instrumen->id,
                        'urutan'       => $urutan,
                    ],
                    [
                        'pertanyaan'    => $data['pertanyaan'],
                        'kunci_jawaban' => $data['kunci_jawaban'],
                        'jenis_soal'    => 'bilingual',
                    ]
                );
            }

            // 7. Sinkronisasi Kategori & Indikator Keterampilan
            // Kategori A. Persiapan (Bobot 20%)
            $katA = UkkInstrumenKategori::updateOrCreate(
                [
                    'instrumen_id' => $instrumen->id,
                    'urutan'       => 1,
                ],
                [
                    'nama_kategori' => 'A. Persiapan',
                    'bobot'         => 20,
                ]
            );

            $indA = [
                1 => 'Menyiapkan lingkungan pengembangan website menggunakan XAMPP serta membuat struktur folder proyek.',
                2 => 'Merancang database MySQL beserta tabel users, students, dan karya sesuai kebutuhan aplikasi.',
                3 => 'Membuat koneksi PHP dengan database MySQL.',
                4 => 'Merancang tampilan antarmuka halaman Login, Dashboard Admin, Dashboard Siswa, dan Showcase Digital.',
                5 => 'Mengidentifikasi kebutuhan fitur CRUD serta merancang pembagian hak akses pengguna berdasarkan role Admin dan Siswa.',
            ];
            foreach ($indA as $urutan => $namaIndikator) {
                UkkInstrumenIndikator::updateOrCreate(
                    [
                        'kategori_id' => $katA->id,
                        'urutan'      => $urutan,
                    ],
                    [
                        'nama_indikator' => $namaIndikator,
                    ]
                );
            }

            // Kategori B. Pelaksanaan (Bobot 50%)
            $katB = UkkInstrumenKategori::updateOrCreate(
                [
                    'instrumen_id' => $instrumen->id,
                    'urutan'       => 2,
                ],
                [
                    'nama_kategori' => 'B. Pelaksanaan',
                    'bobot'         => 50,
                ]
            );

            $indB = [
                1 => 'Membuat fitur autentikasi login dan logout menggunakan PHP Session serta menerapkan keamanan password.',
                2 => 'Menerapkan sistem hak akses multi-level berdasarkan role Admin dan Siswa.',
                3 => 'Membuat fitur CRUD data siswa yang dapat dikelola oleh Admin.',
                4 => 'Membuat fitur CRUD karya siswa yang meliputi judul karya, deskripsi, gambar, dan tautan GitHub.',
                5 => 'Menerapkan pembatasan hak akses agar siswa hanya dapat mengelola karya miliknya sendiri serta membuat fitur upload gambar.',
                6 => 'Membuat halaman Showcase Digital yang menampilkan karya siswa yang telah dipublikasikan, dilengkapi fitur pencarian dan tampilan responsif.',
            ];
            foreach ($indB as $urutan => $namaIndikator) {
                UkkInstrumenIndikator::updateOrCreate(
                    [
                        'kategori_id' => $katB->id,
                        'urutan'      => $urutan,
                    ],
                    [
                        'nama_indikator' => $namaIndikator,
                    ]
                );
            }

            // Kategori C. Hasil (Bobot 30%)
            $katC = UkkInstrumenKategori::updateOrCreate(
                [
                    'instrumen_id' => $instrumen->id,
                    'urutan'       => 3,
                ],
                [
                    'nama_kategori' => 'C. Hasil',
                    'bobot'         => 30,
                ]
            );

            $indC = [
                1 => 'Menghasilkan aplikasi Website Etalase Digital yang memiliki fitur CRUD, autentikasi, dan hak akses.',
                2 => 'Seluruh fungsi aplikasi berjalan dengan baik, database terhubung, serta tidak terdapat kesalahan pada proses utama.',
                3 => 'Tampilan menarik, responsif, mudah digunakan, dan siap didemonstrasikan serta dipublikasikan.',
            ];
            foreach ($indC as $urutan => $namaIndikator) {
                UkkInstrumenIndikator::updateOrCreate(
                    [
                        'kategori_id' => $katC->id,
                        'urutan'      => $urutan,
                    ],
                    [
                        'nama_indikator' => $namaIndikator,
                    ]
                );
            }

            $this->command->info('Data UKK Level III RPL (XI RPL 1 & XI RPL 2) berhasil di-seed ke database.');
        });
    }
}
