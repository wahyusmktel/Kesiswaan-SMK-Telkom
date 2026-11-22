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

class UkkXTkjTjatSeeder extends Seeder
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

            // 2. Cari Rombel Kelas X TKJ 1, X TKJ 2, dan X TJAT di TP Aktif
            $targetKelas = ['X TKJ 1', 'X TKJ 2', 'X TJAT'];
            $rombelIds = [];

            foreach ($targetKelas as $namaKelas) {
                $rombel = Rombel::where('tahun_pelajaran_id', $tpAktif->id)
                    ->whereHas('kelas', fn ($q) => $q->where('nama_kelas', $namaKelas))
                    ->first();

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

            // 3. Buat atau perbarui Paket Ujian UKK Level X TKJ dan TJAT
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Teknik Komputer dan Jaringan',
                    'nama_ujian'         => 'Ujian Kualifikasi Kejuruan Level X TKJ dan TJAT',
                ],
                [
                    'nama_project'        => 'Konfigurasi Dasar Router Mikrotik',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => false,
                ]
            );

            // Mapping Rombel X TKJ 1, X TKJ 2, X TJAT
            if (!empty($rombelIds)) {
                $ujian->rombels()->sync($rombelIds);
            }

            // 4. Tugaskan Penguji:
            $guruPenguji = User::role(['Kaprodi', 'Guru Kelas'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Azhar%')
                      ->orWhere('name', 'LIKE', '%Erwin%')
                      ->orWhere('name', 'LIKE', '%Muchlis%')
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

            // 5. Buat atau perbarui Instrumen
            $instrumen = UkkInstrumen::updateOrCreate(
                [
                    'ukk_ujian_id'   => $ujian->id,
                    'nama_instrumen' => 'Konfigurasi Dasar Router Mikrotik',
                ],
                [
                    'bobot_pengetahuan' => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan (Observasi Langsung Penguji - 8 Butir)
            $soals = [
                1 => [
                    'pertanyaan'    => 'Mengidentifikasi perangkat utama jaringan internet (router dan PC)',
                    'kunci_jawaban' => 'Mampu menunjukkan dan membedakan fungsi fisik router Mikrotik (RB750/RB951) dan PC client dalam topologi jaringan.',
                ],
                2 => [
                    'pertanyaan'    => 'Menjelaskan fungsi router Mikrotik dalam jaringan internet',
                    'kunci_jawaban' => 'Mampu menjelaskan fungsi Mikrotik sebagai pengarah paket data antar jaringan yang berbeda, pengatur gateway, dan pengelola koneksi internet.',
                ],
                3 => [
                    'pertanyaan'    => 'Menjelaskan konsep IP address, subnet mask, gateway, dan DNS',
                    'kunci_jawaban' => 'Mampu menjelaskan pengalamatan IP kelas C/CIDR, fungsi subnet mask untuk membatasi host, gateway sebagai pintu keluar jaringan, dan DNS untuk translasi nama domain ke IP.',
                ],
                4 => [
                    'pertanyaan'    => 'Menjelaskan fungsi DHCP Server pada Jaringan Lokal',
                    'kunci_jawaban' => 'Mampu menjelaskan cara kerja DHCP server dalam mendistribusikan IP address, netmask, gateway, dan DNS secara otomatis kepada host client.',
                ],
                5 => [
                    'pertanyaan'    => 'Menjelaskan fungsi NAT pada Mikrotik untuk akses internet',
                    'kunci_jawaban' => 'Mampu menjelaskan fungsi Network Address Translation (action=masquerade pada chain=srcnat) untuk menerjemahkan IP private lokal menjadi IP public router.',
                ],
                6 => [
                    'pertanyaan'    => 'Menjelaskan DHCP Client di Mikrotik',
                    'kunci_jawaban' => 'Mampu menjelaskan fungsi DHCP client pada interface WAN/Ether1 untuk memperoleh IP address, default route, dan DNS secara dinamis dari ISP/modem.',
                ],
                7 => [
                    'pertanyaan'    => 'Menjelaskan proses perencanaan dalam konfigurasi Mikrotik',
                    'kunci_jawaban' => 'Mampu merancang skema pengalamatan IP (IP plan), alokasi interface (WAN & LAN), DHCP pool range, dan tahapan konfigurasi yang sistematis.',
                ],
                8 => [
                    'pertanyaan'    => 'Menganalisis hasil proses perencanaan',
                    'kunci_jawaban' => 'Mampu mengevaluasi kesesuaian hasil konfigurasi di lapangan dengan tabel perencanaan IP dan topologi jaringan.',
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
                        'jenis_soal'    => 'observasi',
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
                1 => 'Menerapkan K3 & kerapian kerja saat praktik (meja rapi, penanganan perangkat aman)',
                2 => 'Menentukan kebutuhan pengguna secara keseluruhan',
                3 => 'Menyiapkan perangkat jaringan',
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
                1 => 'Melakukan konfigurasi kabel utp straight',
                2 => 'Melakukan pengujian Kabel UPT Straight',
                3 => 'Memasang kabel UTP pada router dan menguji koneksi dengan winbox',
                4 => 'Mengkonfigurasi IP address, subnet, dan DNS pada interface LAN router',
                5 => 'Mengkonfigurasi NAT / masquerade sehingga router dapat akses internet.',
                6 => 'Mengkonfigurasi DHCP server untuk jaringan LAN',
                7 => 'Mengkonfigurasi IP client LAN dan memastikan terhubung ke router',
                8 => 'Melakukan pengujian koneksi (ping gateway, ping internet, uji browsing) pada LAN',
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
                1 => 'Melaporkan hasil perencanaan',
                2 => 'Menganalisis hasil proses perencanaan',
                3 => 'Melakukan analisa hasil konfigurasi',
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

            $this->command->info('Data UKK Level X TKJ dan TJAT (X TKJ 1, X TKJ 2, X TJAT) berhasil di-seed ke database.');
        });
    }
}
