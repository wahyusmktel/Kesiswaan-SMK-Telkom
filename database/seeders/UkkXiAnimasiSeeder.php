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

class UkkXiAnimasiSeeder extends Seeder
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

            // 2. Cari Rombel Kelas XI Animasi di TP Aktif
            $rombel = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'XI Animasi')->orWhere('nama_kelas', 'LIKE', '%XI%Animasi%'))
                ->first();

            if (!$rombel) {
                $rombel = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'LIKE', '%XI%Animasi%'))->first();
            }

            // Jika belum ada siswa di rombel aktif, sinkronkan dari rombel XI Animasi yang memiliki siswa
            if ($rombel && $rombel->siswa()->count() === 0) {
                $rombelWithStudents = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'LIKE', '%XI%Animasi%'))
                    ->has('siswa')
                    ->first();

                if ($rombelWithStudents) {
                    $siswaIds = $rombelWithStudents->siswa()->pluck('master_siswa.id')->all();
                    $rombel->siswa()->syncWithoutDetaching($siswaIds);
                }
            }

            // 3. Buat atau perbarui Paket Ujian UKK Level III Animasi (Bilingual)
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Animasi',
                    'nama_ujian'         => 'Ujian Kualifikasi Kejuruan Level III Animasi',
                ],
                [
                    'nama_project'        => '3D Character Design',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => true,
                ]
            );

            // Mapping Rombel XI Animasi
            if ($rombel) {
                $ujian->rombels()->sync([$rombel->id]);
            }

            // 4. Tugaskan Penguji:
            // - Nur Cahyana Aminuallah (Kaprodi / Penguji Kejuruan Animasi) -> peran: keterampilan
            // - Guru Bahasa Inggris / Wahyu Rahmat Hidayat -> peran: pengetahuan / semua
            $guruAnimasi = User::role(['Kaprodi', 'Guru Kelas'])
                ->where('name', 'LIKE', '%Nur Cahyana%')
                ->first();

            $guruInggris = User::role(['Guru Kelas', 'Kaprodi'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Inggris%')
                      ->orWhere('name', 'LIKE', '%Wahyu Rahmat%');
                })
                ->first();

            $syncPenguji = [];
            if ($guruAnimasi) {
                $syncPenguji[$guruAnimasi->id] = ['peran' => 'keterampilan'];
            }
            if ($guruInggris) {
                $syncPenguji[$guruInggris->id] = ['peran' => 'pengetahuan'];
            }
            // Tambahkan juga jika belum ada
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
                    'nama_instrumen' => '3D Character Design',
                ],
                [
                    'bobot_pengetahuan' => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan (Bilingual - English Interview)
            $soals = [
                1 => [
                    'pertanyaan'    => 'What is the first step you usually take before starting to model a 3D character?',
                    'kunci_jawaban' => 'Reference gathering & concept art analysis (understanding character proportions, front/side orthogonal views, design silhouette, and planning topology/mesh flow).',
                ],
                2 => [
                    'pertanyaan'    => 'Why is good topology important when creating a 3D character for animation?',
                    'kunci_jawaban' => 'Proper edge flow (especially quad-based topology around joints, eyes, and mouth) prevents mesh deformation artifacts, pinching, or stretching during rigging and animation.',
                ],
                3 => [
                    'pertanyaan'    => 'What is the difference between texturing and shading in 3D character creation?',
                    'kunci_jawaban' => 'Texturing is creating and mapping 2D image maps (Base Color, Normal, Roughness, Metallic) onto UV coordinates. Shading defines how light interacts with the surface material (specular reflection, subsurface scattering, roughness properties).',
                ],
                4 => [
                    'pertanyaan'    => 'How does rigging bring a static 3D character model to life?',
                    'kunci_jawaban' => 'Rigging creates an internal skeletal armature (bones/joints), weight painting (skinning) to bind vertices to bones, and constraints/IK controls so animators can pose and animate the model naturally.',
                ],
                5 => [
                    'pertanyaan'    => 'What is the hardest part of making a 3D character, and how do you overcome it?',
                    'kunci_jawaban' => 'Common challenges include facial topology/likeness, hand modeling, or realistic cloth folds. Overcome by using good anatomical references, starting with blocking out primary shapes before subdividing, and practicing edge flow.',
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
                1 => 'Menyiapkan alat dan bahan sesuai kebutuhan modeling 3D.',
                2 => 'Membuat concept art atau sketsa karakter sebelum modeling.',
                3 => 'Membuat rencana kerja modeling dan pembagian bagian tubuh karakter.',
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
                1 => 'Membuat model dasar karakter sesuai konsep (bentuk kepala, badan, tangan, kaki).',
                2 => 'Menggunakan alat dasar modeling dengan benar (Extrude, Scale, Rotate).',
                3 => 'Menggunakan modifier seperti Mirror dan Subdivision Surface untuk memperhalus bentuk.',
                4 => 'Mengatur posisi dan pose karakter agar terlihat natural.',
                5 => 'Memberikan material dan warna dasar pada model karakter.',
                6 => 'Menambahkan pencahayaan (lighting) yang sesuai agar karakter terlihat jelas.',
                7 => 'Mengatur kamera agar hasil render fokus pada karakter.',
                8 => 'Melakukan render akhir dengan pengaturan resolusi dan format yang tepat.',
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
                1 => 'Menampilkan model karakter 3D yang proporsional dan rapi.',
                2 => 'Menampilkan hasil render karakter dengan pencahayaan dan kamera yang baik.',
                3 => 'Menyajikan hasil akhir dalam format gambar dengan framing dan angle menarik.',
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

            $this->command->info('Data UKK Level III Animasi (XI Animasi) berhasil di-seed ke database.');
        });
    }
}
