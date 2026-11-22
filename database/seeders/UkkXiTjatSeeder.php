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

class UkkXiTjatSeeder extends Seeder
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

            // 2. Cari Rombel Kelas XI TJAT di TP Aktif
            $rombel = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'XI TJAT')->orWhere('nama_kelas', 'LIKE', '%XI%TJAT%'))
                ->first();

            if (!$rombel) {
                $rombel = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'LIKE', '%XI%TJAT%'))->first();
            }

            if ($rombel && $rombel->siswa()->count() === 0) {
                $rombelWithStudents = Rombel::whereHas('kelas', fn ($q) => $q->where('nama_kelas', 'LIKE', '%XI%TJAT%'))
                    ->has('siswa')
                    ->first();

                if ($rombelWithStudents) {
                    $siswaIds = $rombelWithStudents->siswa()->pluck('master_siswa.id')->all();
                    $rombel->siswa()->syncWithoutDetaching($siswaIds);
                }
            }

            // 3. Buat atau perbarui Paket Ujian UKK Level III TJAT (Bilingual + Observasi)
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Teknik Jaringan Akses Telekomunikasi',
                    'nama_ujian'         => 'Ujian Kualifikasi Kejuruan Level III TJAT',
                ],
                [
                    'nama_project'        => 'Melakukan Penyambungan Menggunakan Fusion Splicer dan Pengukuran Hasil Sambungan Kabel Fiber Optik',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => true,
                ]
            );

            // Mapping Rombel XI TJAT
            if ($rombel) {
                $ujian->rombels()->sync([$rombel->id]);
            }

            // 4. Tugaskan Penguji Otomatis:
            // - Penguji Kejuruan TJAT (Guru TJAT / Kaprodi TJAT)
            // - Penguji Pengetahuan Bilingual (Guru Bahasa Inggris / Wahyu Rahmat Hidayat)
            $guruTjat = User::role(['Kaprodi', 'Guru Kelas'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Muchlis%')
                      ->orWhere('name', 'LIKE', '%Mukhtaris%')
                      ->orWhere('name', 'LIKE', '%Hari%')
                      ->orWhere('name', 'LIKE', '%Wahyu Rahmat%');
                })
                ->first();

            $guruInggris = User::role(['Guru Kelas', 'Kaprodi'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Inggris%')
                      ->orWhere('name', 'LIKE', '%Wahyu Rahmat%');
                })
                ->first();

            $syncPenguji = [];
            if ($guruTjat) {
                $syncPenguji[$guruTjat->id] = ['peran' => 'keterampilan'];
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
                    'nama_instrumen' => 'Melakukan Penyambungan Menggunakan Fusion Splicer dan Pengukuran Hasil Sambungan Kabel Fiber Optik',
                ],
                [
                    'bobot_pengetahuan' => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan (5 Butir Bilingual + 12 Butir Observasi = 17 Butir)
            $soals = [
                // ── Bagian Bilingual (English Oral Interview) ──
                1 => [
                    'pertanyaan'    => 'What is the main function of a fusion splicer?',
                    'kunci_jawaban' => 'To fuse or weld two optical fibers together end-to-end using an electric arc so that light signals can pass through with minimal insertion loss.',
                    'jenis_soal'    => 'bilingual',
                ],
                2 => [
                    'pertanyaan'    => 'What tools do you need before splicing a fiber optic cable?',
                    'kunci_jawaban' => 'Fiber stripper, precision fiber cleaver, lint-free wipes, isopropyl alcohol (IPA 99%), protection sleeves, and a waste fiber disposal container.',
                    'jenis_soal'    => 'bilingual',
                ],
                3 => [
                    'pertanyaan'    => 'Why must you clean the fiber core with alcohol before splicing?',
                    'kunci_jawaban' => 'To remove microscopic dirt, dust, and residual coating oils that would cause high splice loss, bubbles, or weak fusion at the splice point.',
                    'jenis_soal'    => 'bilingual',
                ],
                4 => [
                    'pertanyaan'    => 'How do you protect the fiber joint after splicing is finished?',
                    'kunci_jawaban' => 'Slide a heat-shrink protection sleeve (with a stainless steel strength member) over the bare fused splice point and heat-shrink it in the splicer heating oven.',
                    'jenis_soal'    => 'bilingual',
                ],
                5 => [
                    'pertanyaan'    => 'What tool is used to check if the spliced cable signal is good?',
                    'kunci_jawaban' => 'OTDR (Optical Time Domain Reflectometer) to measure attenuation, splice loss, and return loss, or an Optical Power Meter (OPM) / Light Source / VFL.',
                    'jenis_soal'    => 'bilingual',
                ],

                // ── Bagian Observasi (Tanya Jawab & Observasi Langsung Penguji) ──
                6 => [
                    'pertanyaan'    => 'Memahami Teknologi Fiber Optik',
                    'kunci_jawaban' => 'Prinsip pemantulan internal total (Total Internal Reflection), perbedaan single-mode (SM) dan multi-mode (MM), serta keunggulan kecepatan dan bandwidth serat optik.',
                    'jenis_soal'    => 'observasi',
                ],
                7 => [
                    'pertanyaan'    => 'Mengidentifikasi Kabel Fiber Optik',
                    'kunci_jawaban' => 'Mengenali struktur kabel: core kaca, cladding, coating primer, loose tube, strength member, dan outer jacket pelindung.',
                    'jenis_soal'    => 'observasi',
                ],
                8 => [
                    'pertanyaan'    => 'Mengidentifikasi ukuran, tipe dan jenis warna kabel Fiber Optik',
                    'kunci_jawaban' => '12 kode warna standar (Biru, Oranye, Hijau, Cokelat, Abu-abu, Putih, Merah, Hitam, Kuning, Ungu, Pink, Toska) dan ukuran core SM 9/125 um.',
                    'jenis_soal'    => 'observasi',
                ],
                9 => [
                    'pertanyaan'    => 'Menjelaskan pengetahuan Fiber Optik',
                    'kunci_jawaban' => 'Mampu menjelaskan sifat transmisi cahaya, redaman (dB/km), dan faktor dispersi kromatik pada kabel serat optik.',
                    'jenis_soal'    => 'observasi',
                ],
                10 => [
                    'pertanyaan'    => 'Memahami pengetahuan tentang konektor serat optik',
                    'kunci_jawaban' => 'Mengenal tipe konektor seperti SC, LC, FC, ST, serta jenis polesan PC, UPC (biru), dan APC (hijau berpenampang 8 derajat).',
                    'jenis_soal'    => 'observasi',
                ],
                11 => [
                    'pertanyaan'    => 'Mengidentifikasi tentang cara kerja OTDR',
                    'kunci_jawaban' => 'Menjelaskan prinsip pantulan Fresnel (reflective event) dan hamburan balik Rayleigh (non-reflective event) untuk mendeteksi loss sambungan, konektor, dan titik putus kabel.',
                    'jenis_soal'    => 'observasi',
                ],
                12 => [
                    'pertanyaan'    => 'Memahami pengetahuan penggunaan Lamda pada OTDR',
                    'kunci_jawaban' => 'Memahami panjang gelombang pengujian (1310 nm, 1550 nm, 1625 nm) serta karakteristik redaman serat dan deteksi macrobending pada lamda 1550/1625 nm.',
                    'jenis_soal'    => 'observasi',
                ],
                13 => [
                    'pertanyaan'    => 'Memahami Tentang Jaringan FTTx',
                    'kunci_jawaban' => 'Arsitektur FTTH (Fiber to the Home): OLT di sentral, FDT/ODC, splitter optik pasif, FAT/ODP, kabel drop, dan ONT/roset di rumah pelanggan.',
                    'jenis_soal'    => 'observasi',
                ],
                14 => [
                    'pertanyaan'    => 'Memahami cara kerja Fusion Splicer',
                    'kunci_jawaban' => 'Proses core alignment / cladding alignment otomatis, pembersihan debu pra-arc (pre-fuse), discharge busur listrik penyatuan core, dan kalkulasi estimasi insertion loss.',
                    'jenis_soal'    => 'observasi',
                ],
                15 => [
                    'pertanyaan'    => 'Menentukan perangkat pada instalasi fiber optik',
                    'kunci_jawaban' => 'Menentukan spesifikasi OTB, optical closure/joint box, pigtail, patch cord, ODF, dan adapter sesuai kebutuhan rancangan jaringan.',
                    'jenis_soal'    => 'observasi',
                ],
                16 => [
                    'pertanyaan'    => 'Mampu mengidentifikasi jaringan kabel serat optik',
                    'kunci_jawaban' => 'Mampu memetakan rute kabel udara/tanah, titik sambung joint closure, slack management, dan konfigurasi link jaringan optik.',
                    'jenis_soal'    => 'observasi',
                ],
                17 => [
                    'pertanyaan'    => 'Mampu mengidentifikasi kualitas sambungan serat optik',
                    'kunci_jawaban' => 'Menilai kelayakan sambungan berdasarkan standar batas redaman (loss < 0.10 dB per splice menurut standar Telkom) dan integritas fisik sambungan.',
                    'jenis_soal'    => 'observasi',
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
                        'jenis_soal'    => $data['jenis_soal'],
                    ]
                );
            }

            // 7. Sinkronisasi Kategori & Indikator Keterampilan (Bobot Total = 100%)
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
                1 => 'Menyiapkan pekerjaan penyambungan fiber optik',
                2 => 'Mempersiapkan kabel fiber optik yang akan dipasang pada cassette joint',
                3 => 'Mempersiapkan OTDR dan peralatan penunjang lainnya',
                4 => 'Mempersiapkan kabel fiber optik yang akan dipasang Protector Sleve',
                5 => 'Memperhatikan Keamanan dan Keselamatan Kerja',
                6 => 'Mempersiapkan tempat untuk meletakan sisa potongan serat optik',
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
                1 => 'Memilih Urutan dan Warna Core yang Sesuai',
                2 => 'Membersihkan Port dan Konektor pada OTB dan Perangkat OTDR dan Kabel Patchcore',
                3 => 'Melakukan Pengaturan OTDR sesuai kebutuhan dan panjang jaringan',
                4 => 'Melakukan pengujian hasil pengaturan OTDR pada Saluran',
                5 => 'Melakukan Penyambungan core yang sudah ditentukan',
                6 => 'Memasang Protector Sleve',
                7 => 'Melakukan pengaturan OTDR tahap kedua setelah core dilakukan penyambungan',
                8 => 'Melakukan pengujian menggunakan OTDR setelah core dilakukan penyambungan',
                9 => 'Merapikan dan membersihkan tempat kerja',
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
                1 => 'Mencatat hasil pengukuran OTDR sesuai pengaturan yang sudah ditentukan',
                2 => 'Membuat kesimpulan sesuai dengan hasil pengukuran OTDR pada saluran sebelum penyambungan core',
                3 => 'Mencatat hasil estimasi redaman setelah penyambungan core dengan fusion splicer',
                4 => 'Mencatat hasil pengukuran OTDR setelah saluran dilakukan penyambungan core',
                5 => 'Membuat laporan proses dan hasil pengukuran saluran sebelum dan sesudah core dilakukan penyambungan',
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

            $this->command->info('Data UKK Level III TJAT (XI TJAT) berhasil di-seed ke database.');
        });
    }
}
