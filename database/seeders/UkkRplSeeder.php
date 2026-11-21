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

class UkkRplSeeder extends Seeder
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

            // 2. Cari Rombel Kelas X RPL 1 dan X RPL 2 di TP Aktif
            $targetKelas = ['X RPL 1', 'X RPL 2'];
            $rombelIds = [];

            foreach ($targetKelas as $namaKelas) {
                $rombel = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                    ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))
                    ->first();

                // Jika belum ada di TP aktif, cari di TP sebelumnya
                if (!$rombel) {
                    $rombel = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))->first();
                }

                if ($rombel) {
                    // Jika rombel di TP aktif belum memiliki siswa, salin dari rombel dengan kelas yang sama yang punya siswa
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

            // 3. Buat atau perbarui Paket Ujian UKK Level 1 RPL
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Rekayasa Perangkat Lunak',
                    'nama_ujian'         => 'Uji Kualifikasi Kejuruan Level 1 RPL',
                ],
                [
                    'nama_project'        => 'Membuat Desain dan Prototype Website Personal Portfolio menggunakan Figma',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => false,
                ]
            );

            // Mapping Rombel X RPL 1 & X RPL 2
            if (!empty($rombelIds)) {
                $ujian->rombels()->sync($rombelIds);
            }

            // 4. Tugaskan Penguji Otomatis (Guru Kejuruan RPL / Kaprodi RPL)
            $guruPenguji = User::role(['Kaprodi', 'Guru Kelas'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Ikhsan%')
                      ->orWhere('name', 'LIKE', '%Wahyu Rahmat%');
                })
                ->pluck('id')
                ->all();

            if (!empty($guruPenguji)) {
                $syncPenguji = [];
                foreach ($guruPenguji as $gId) {
                    $syncPenguji[$gId] = ['peran' => 'semua'];
                }
                $ujian->penguji()->syncWithoutDetaching($syncPenguji);
            }

            // 5. Buat atau perbarui Instrumen (Pengetahuan kosong -> bobot 0, keterampilan 100)
            $instrumen = UkkInstrumen::updateOrCreate(
                [
                    'ukk_ujian_id'   => $ujian->id,
                    'nama_instrumen' => 'Membuat Desain dan Prototype Website Personal Portfolio menggunakan Figma',
                ],
                [
                    'bobot_pengetahuan' => 0,
                ]
            );

            // 6. Pengetahuan: Kosong (tidak ada soal untuk saat ini)

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
                1 => 'Menyiapkan dan memeriksa gambar referensi desain website serta asset yang disediakan penguji untuk digunakan di Figma.',
                2 => 'Menyusun Site Map dan User Flow sederhana yang menunjukkan struktur halaman serta alur navigasi pengunjung.',
                3 => 'Merencanakan struktur Single Page Website dengan urutan section Home, About, Process, Portfolio, Client, dan Contact.',
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
                1 => 'Membuat desain High-Fidelity Personal Portfolio di Figma dengan mengacu pada gambar referensi yang diberikan.',
                2 => 'Mendesain Header dan section Home/Say Hello yang memuat perkenalan, nama, profesi, foto profil, deskripsi singkat, tombol My Profile, dan pencapaian.',
                3 => 'Mendesain section About dengan judul profil, foto, deskripsi pengalaman/keahlian, tombol My Project, dan Download CV.',
                4 => 'Mendesain section Work Process dengan empat tahapan kerja: Research, Analysis, Design, dan Launch.',
                5 => 'Mendesain section Portfolio dengan minimal 6 card proyek yang memuat gambar, judul, deskripsi singkat, tombol Case Study, dan More Project.',
                6 => 'Mendesain section Happy Clients yang memuat logo/identitas klien serta testimonial atau pengalaman klien.',
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
                1 => 'Menghasilkan desain High-Fidelity Personal Portfolio satu halaman dengan seluruh section wajib tersusun lengkap dan mudah dipahami.',
                2 => 'Menghasilkan Prototype Figma yang seluruh enam menu navigasinya berfungsi menuju section yang sesuai dan dapat diuji melalui Present.',
                3 => 'Mengumpulkan tautan file desain Figma, tautan Prototype Figma, serta dokumen PDF dengan akses Can View atau Can Edit agar dapat diperiksa penguji.',
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

            $this->command->info('Data UKK Level 1 RPL berhasil di-seed ke database.');
        });
    }
}
