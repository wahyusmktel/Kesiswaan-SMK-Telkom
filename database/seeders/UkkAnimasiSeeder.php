<?php

namespace Database\Seeders;

use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UkkInstrumen;
use App\Models\UkkInstrumenIndikator;
use App\Models\UkkInstrumenKategori;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkNilaiSikap;
use App\Models\UkkUjian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UkkAnimasiSeeder extends Seeder
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

            // 2. Cari Rombel Kelas X Animasi
            $rombelXAnimasi = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'X Animasi')->orWhere('nama_kelas', 'LIKE', '%X%Animasi%'))
                ->first();

            // Jika di TP aktif belum ada rombel X Animasi, cari rombel X Animasi dari TP sebelumnya
            if (!$rombelXAnimasi) {
                $rombelXAnimasi = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'X Animasi'))->first();
            }

            // Jika rombel di TP aktif belum memiliki siswa, salin dari rombel X Animasi yang ada siswanya
            if ($rombelXAnimasi && $rombelXAnimasi->siswa()->count() === 0) {
                $rombelWithStudents = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'X Animasi'))
                    ->has('siswa')
                    ->first();

                if ($rombelWithStudents) {
                    $siswaIds = $rombelWithStudents->siswa()->pluck('master_siswa.id')->all();
                    $rombelXAnimasi->siswa()->syncWithoutDetaching($siswaIds);
                }
            }

            // 3. Buat atau perbarui Paket Ujian UKK Animasi
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Animasi',
                    'nama_project'       => 'Membuat Gambar Penceritaan (Storyboard Drawing)',
                ],
                [
                    'nama_ujian'          => 'Uji Kualifikasi Kejuruan (UKK) Animasi',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => false,
                ]
            );

            // Mapping Rombel
            if ($rombelXAnimasi) {
                $ujian->rombels()->sync([$rombelXAnimasi->id]);
            }

            // 4. Tugaskan Penguji Otomatis (Guru Kejuruan Animasi / Guru Kelas)
            $guruPenguji = User::role(['Kaprodi', 'Guru Kelas'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Wahyu Rahmat%')
                      ->orWhere('name', 'LIKE', '%Nur Cahyana%');
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

            // 5. Buat atau perbarui Instrumen
            $instrumen = UkkInstrumen::updateOrCreate(
                [
                    'ukk_ujian_id'   => $ujian->id,
                    'nama_instrumen' => 'Membuat Gambar Penceritaan (Storyboard Drawing)',
                ],
                [
                    'bobot_pengetahuan'  => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan
            $soals = [
                1 => 'Mampu menganalisis alur cerita, pesan, serta aksi karakter dari naskah.',
                2 => 'Mampu membedakan fungsi shot type (Wide, Medium, Close-Up, ECU), camera angle (Eye Level, Low/High Angle, Dutch Angle), dan camera movement.',
                3 => 'Memahami penggunaan dan fungsi panah aksi (action arrows), garis gerak, serta simbol teknis sinematografi pada storyboard.',
                4 => 'Memahami pentingnya kesinambungan (continuity) adegan antar panel serta penentuan alokasi waktu (duration).',
                5 => 'Memahami kesesuaian antara gaya gambar kartun sederhana dengan tone cerita.',
            ];

            foreach ($soals as $urutan => $pertanyaan) {
                UkkInstrumenSoal::updateOrCreate(
                    [
                        'instrumen_id' => $instrumen->id,
                        'urutan'       => $urutan,
                    ],
                    [
                        'pertanyaan' => $pertanyaan,
                    ]
                );
            }

            // 7. Sinkronisasi Kategori & Indikator Keterampilan
            // Kategori A. Persiapan (20%)
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
                1 => 'Menyiapkan kertas (A4 Landscape), pensil, penghapus, dan penggaris sesuai ketentuan teknis ujian.',
                2 => 'Menentukan dan mencatat elemen kunci naskah (Aksi Tokoh, Camera Angle/Shot, Setting) sebelum melukiskan sketsa.',
                3 => 'Membuat grid 10 panel berasio 16:9 secara rapi, presisi, dan proporsional pada lembar kerja.',
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

            // Kategori B. Pelaksanaan (50%)
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
                1 => 'Menggambar adegan dengan jenis shot dan camera angle yang tepat sesuai perintah naskah di tiap panel.',
                2 => 'Mengatur letak objek dengan memperlihatkan Foreground (FG), Midground (MG), dan Background (BG) secara jelas.',
                3 => 'Menggambar karakter dengan proporsi gaya yang konsisten, ekspresi tepat, dan gesture yang dinamis.',
                4 => 'Menggambarkan detail kostum khas dan properti sesuai acuan.',
                5 => 'Menyimbolkan panah aksi (action arrow), petunjuk kamera, action text, dialog baku, dan SFX/BGM secara benar pada tiap panel.',
                6 => 'Menggunakan garis bantu (guidelines) tipis yang rapi dan penegasan garis sketsa (freehand) yang tegas tanpa alat bantu cetak.',
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

            // Kategori C. Hasil (30%)
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
                1 => 'Storyboard informatif, komunikatif, dan mudah dipahami oleh pembaca/animator sebagai acuan produksi.',
                2 => 'Visualisasi gambar berhasil menyampaikan esensi akulturasi budaya, klimaks cerita, dan emosi karakter sesuai naskah.',
                3 => 'Hasil akhir lembar storyboard bersih dari noda, kertas tidak terlipat, dan tata letak panel terstruktur secara profesional.',
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

            $this->command->info('Data UKK Animasi berhasil di-seed ke database.');
        });
    }
}
