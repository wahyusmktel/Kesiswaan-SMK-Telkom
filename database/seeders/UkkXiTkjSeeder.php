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

class UkkXiTkjSeeder extends Seeder
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

            // 2. Cari Rombel Kelas XI TKJ 1 dan XI TKJ 2 di TP Aktif
            $targetKelas = ['XI TKJ 1', 'XI TKJ 2'];
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

            // 3. Buat atau perbarui Paket Ujian UKK Level III TKJ (Bilingual)
            $ujian = UkkUjian::updateOrCreate(
                [
                    'tahun_pelajaran_id' => $tpAktif->id,
                    'jurusan'            => 'Teknik Komputer dan Jaringan',
                    'nama_ujian'         => 'Ujian Kualifikasi Kejuruan Level III TKJ',
                ],
                [
                    'nama_project'        => 'KONFIGURASI CISCO INTER-VLAN ROUTING DAN DHCP',
                    'tanggal_pelaksanaan' => now()->toDateString(),
                    'is_bilingual'        => true,
                ]
            );

            // Mapping Rombel XI TKJ 1 & XI TKJ 2
            if (!empty($rombelIds)) {
                $ujian->rombels()->sync($rombelIds);
            }

            // 4. Tugaskan Penguji Otomatis:
            // - Penguji Kejuruan TKJ (User dengan role Guru Kelas / Kaprodi yang membidangi TKJ)
            // - Penguji Pengetahuan Bilingual (Guru Bahasa Inggris / Wahyu Rahmat Hidayat)
            $guruTkj = User::role(['Kaprodi', 'Guru Kelas'])
                ->where(function ($q) {
                    $q->where('name', 'LIKE', '%Azhar%')
                      ->orWhere('name', 'LIKE', '%Erwin%')
                      ->orWhere('name', 'LIKE', '%Hermawan%')
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
            if ($guruTkj) {
                $syncPenguji[$guruTkj->id] = ['peran' => 'keterampilan'];
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
                    'nama_instrumen' => 'KONFIGURASI CISCO INTER-VLAN ROUTING DAN DHCP',
                ],
                [
                    'bobot_pengetahuan' => 30,
                ]
            );

            // 6. Sinkronisasi Soal Pengetahuan (Bilingual - English Interview)
            $soals = [
                1 => [
                    'pertanyaan'    => 'What is the main purpose of creating a VLAN on a Cisco switch?',
                    'kunci_jawaban' => 'To segment a physical network into multiple logical broadcast domains, improving security, reducing broadcast traffic, and enabling flexible network management.',
                ],
                2 => [
                    'pertanyaan'    => 'What is the difference between an access port and a trunk port?',
                    'kunci_jawaban' => 'An access port carries traffic for only one specific VLAN (untagged frames to end devices), while a trunk port carries traffic for multiple VLANs simultaneously using 802.1Q tagging between switches or routers.',
                ],
                3 => [
                    'pertanyaan'    => 'Why do we need Inter-VLAN Routing in a network?',
                    'kunci_jawaban' => 'Because devices in different VLANs cannot communicate directly at Layer 2; a Layer 3 device (router or multilayer switch) is required to route traffic between isolated VLAN subnets.',
                ],
                4 => [
                    'pertanyaan'    => 'What device is usually used to connect different VLANs together?',
                    'kunci_jawaban' => 'A Layer 3 router (using Router-on-a-Stick with subinterfaces) or a Layer 3 Multilayer Switch with SVIs (Switched Virtual Interfaces).',
                ],
                5 => [
                    'pertanyaan'    => 'How do you test if your Inter-VLAN Routing configuration works properly?',
                    'kunci_jawaban' => 'By performing ping tests from an end device in one VLAN to the default gateway, and to devices located in different VLANs (across switches), and verifying routing tables and trunk/VLAN status commands.',
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

            // 7. Sinkronisasi Kategori & Indikator Keterampilan (Total 7 Kategori = 100%)
            $kategoris = [
                1 => [
                    'nama'  => '1. Ketepatan Topologi & Sambungan Port',
                    'bobot' => 10,
                    'indikators' => [
                        1 => 'Configuration Workflow & Sequence',
                        2 => 'Devices and Port Allocation',
                        3 => 'Purpose of Configuration',
                        4 => 'IP Addressing & DHCP Management',
                        5 => 'Verification & Troubleshooting',
                    ],
                ],
                2 => [
                    'nama'  => '2. Pembuatan VLAN & Konfigurasi Access Port',
                    'bobot' => 20,
                    'indikators' => [
                        1 => 'VLAN 10, 20, 30 dibuat pada Switch-A, Switch-B, dan Switch-C',
                        2 => 'Nama VLAN konsisten: TKJ, RPL, TJAT',
                        3 => 'Port access Fa0/1–Fa0/9 Switch-A masuk VLAN yang benar',
                        4 => 'Port access Fa0/1–Fa0/9 Switch-B masuk VLAN yang benar',
                        5 => 'Port access Fa0/1–Fa0/9 Switch-C masuk VLAN yang benar',
                    ],
                ],
                3 => [
                    'nama'  => '3. Konfigurasi Trunk Antarswitch & ke Router',
                    'bobot' => 15,
                    'indikators' => [
                        1 => 'Trunk Switch-A Gig0/1 ↔ Switch-B Gig0/2 (kedua sisi)',
                        2 => 'Trunk Switch-B Fa0/24 ↔ Switch-C Gig0/1 (kedua sisi)',
                        3 => 'Trunk Switch-B Gig0/1 menuju Router Gig0/0',
                        4 => 'VLAN 10, 20, 30 diizinkan dan melewati seluruh trunk',
                    ],
                ],
                4 => [
                    'nama'  => '4. Konfigurasi Inter-VLAN Routing',
                    'bobot' => 20,
                    'indikators' => [
                        1 => 'Interface Gig0/0 router aktif (no shutdown), status up/up',
                        2 => 'Subinterface VLAN 10: encapsulation dot1Q 10 + IP 192.168.10.1/24',
                        3 => 'Subinterface VLAN 20: encapsulation dot1Q 20 + IP 192.168.20.1/24',
                        4 => 'Subinterface VLAN 30: encapsulation dot1Q 30 + IP 192.168.30.1/24',
                    ],
                ],
                5 => [
                    'nama'  => '5. Konfigurasi DHCP untuk Ketiga VLAN',
                    'bobot' => 15,
                    'indikators' => [
                        1 => 'DHCP pool TKJ: 192.168.10.0/24, default-router 192.168.10.1',
                        2 => 'DHCP pool RPL: 192.168.20.0/24, default-router 192.168.20.1',
                        3 => 'DHCP pool TJAT: 192.168.30.0/24, default-router 192.168.30.1',
                        4 => 'Alamat gateway (.1) di-exclude, tidak dibagikan ke klien DHCP',
                        5 => 'Seluruh 27 end device DHCP & memperoleh IP sesuai VLAN, tanpa duplikat',
                    ],
                ],
                6 => [
                    'nama'  => '6. Pengujian & Troubleshooting Konektivitas',
                    'bobot' => 15,
                    'indikators' => [
                        1 => 'Verifikasi show vlan brief & show interfaces trunk (hasil sesuai)',
                        2 => 'Ping ke gateway masing-masing VLAN berhasil',
                        3 => 'Ping antar perangkat dalam VLAN yang sama berhasil (termasuk lintas switch)',
                        4 => 'Ping antar-VLAN melalui router berhasil',
                        5 => 'Troubleshooting: kendala diidentifikasi dan diperbaiki',
                    ],
                ],
                7 => [
                    'nama'  => '7. Kerapian File & Dokumentasi Hasil',
                    'bobot' => 5,
                    'indikators' => [
                        1 => 'File .pkt dapat dibuka kembali & konfigurasi tersimpan',
                        2 => 'Penamaan file .pkt & .pdf sesuai format UK.L3_XITKJ_Nama_NIS',
                        3 => 'Kelengkapan screenshot bukti kerja (Bab XII poin 2–9)',
                        4 => 'Laporan singkat memuat konfigurasi, kendala, dan solusi',
                    ],
                ],
            ];

            foreach ($kategoris as $urutanKat => $katData) {
                $kategori = UkkInstrumenKategori::updateOrCreate(
                    [
                        'instrumen_id' => $instrumen->id,
                        'urutan'       => $urutanKat,
                    ],
                    [
                        'nama_kategori' => $katData['nama'],
                        'bobot'         => $katData['bobot'],
                    ]
                );

                foreach ($katData['indikators'] as $urutanInd => $namaIndikator) {
                    UkkInstrumenIndikator::updateOrCreate(
                        [
                            'kategori_id' => $kategori->id,
                            'urutan'      => $urutanInd,
                        ],
                        [
                            'nama_indikator' => $namaIndikator,
                        ]
                    );
                }
            }

            $this->command->info('Data UKK Level III TKJ (XI TKJ 1 & XI TKJ 2) berhasil di-seed ke database.');
        });
    }
}
