<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MasterSiswa;
use App\Models\OsisAgenda;
use App\Models\OsisPengurus;
use App\Models\OsisSetting;
use App\Models\PilketosDpt;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\PilketosTimeline;
use App\Models\Rombel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class OsisRoleAndDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Permissions & Role OSIS
        $permissions = [
            'manage osis',
            'manage pilketos',
            'view pilketos',
            'manage dpt osis',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $roleOsis = Role::firstOrCreate(['name' => 'OSIS']);
        $roleOsis->syncPermissions($permissions);

        // Berikan juga ke Super Admin & Waka Kesiswaan
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }

        $wakaRole = Role::where('name', 'Waka Kesiswaan')->first();
        if ($wakaRole) {
            $wakaRole->givePermissionTo($permissions);
        }

        // Buat Akun Demo Admin OSIS
        $osisUser = User::firstOrCreate(
            ['email' => 'osis@smktelkom-lpg.sch.id'],
            [
                'name' => 'Pengurus OSIS',
                'password' => Hash::make('osis12345'),
            ]
        );
        if (!$osisUser->hasRole('OSIS')) {
            $osisUser->assignRole('OSIS');
        }

        // Tambahkan role OSIS ke Super Admin juga agar bisa switch role
        $superAdmin = User::where('email', 'superadmin@smktelkom-lpg.id')->first();
        if ($superAdmin && !$superAdmin->hasRole('OSIS')) {
            $superAdmin->assignRole('OSIS');
        }

        // 2. Buat Demo Kelas & Siswa
        $kelasRpl = Kelas::firstOrCreate(['nama_kelas' => 'XI RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak']);
        $kelasTkj = Kelas::firstOrCreate(['nama_kelas' => 'XI TKJ 1', 'jurusan' => 'Teknik Komputer & Jaringan']);
        $kelasAnim = Kelas::firstOrCreate(['nama_kelas' => 'XI Animasi 1', 'jurusan' => 'Animasi']);
        $kelasX = Kelas::firstOrCreate(['nama_kelas' => 'X PPLG 1', 'jurusan' => 'Pengembangan Perangkat Lunak & Gim']);

        $teacherUser = User::first();
        $rombelRpl = Rombel::firstOrCreate(['kelas_id' => $kelasRpl->id, 'tahun_ajaran' => '2025/2026'], ['wali_kelas_id' => $teacherUser->id ?? 1]);
        $rombelTkj = Rombel::firstOrCreate(['kelas_id' => $kelasTkj->id, 'tahun_ajaran' => '2025/2026'], ['wali_kelas_id' => $teacherUser->id ?? 1]);

        $sampleStudents = [
            ['nis' => '23241001', 'nama_lengkap' => 'Muhammad Fathan Pratama', 'jenis_kelamin' => 'Laki-laki', 'status' => 'aktif', 'kelas' => 'XI RPL 1'],
            ['nis' => '23241002', 'nama_lengkap' => 'Nabila Putri Zahra', 'jenis_kelamin' => 'Perempuan', 'status' => 'aktif', 'kelas' => 'XI RPL 1'],
            ['nis' => '23241003', 'nama_lengkap' => 'Arya Bima Sena', 'jenis_kelamin' => 'Laki-laki', 'status' => 'aktif', 'kelas' => 'XI TKJ 1'],
            ['nis' => '23241004', 'nama_lengkap' => 'Siti Aisyah Rahma', 'jenis_kelamin' => 'Perempuan', 'status' => 'aktif', 'kelas' => 'XI TKJ 1'],
            ['nis' => '23241005', 'nama_lengkap' => 'Rafi Ahmad Fauzi', 'jenis_kelamin' => 'Laki-laki', 'status' => 'aktif', 'kelas' => 'XI Animasi 1'],
            ['nis' => '23241006', 'nama_lengkap' => 'Adelia Citra Lestari', 'jenis_kelamin' => 'Perempuan', 'status' => 'aktif', 'kelas' => 'XI Animasi 1'],
            ['nis' => '23241007', 'nama_lengkap' => 'Dimas Bagus Wicaksono', 'jenis_kelamin' => 'Laki-laki', 'status' => 'aktif', 'kelas' => 'X PPLG 1'],
            ['nis' => '23241008', 'nama_lengkap' => 'Karin Amanda', 'jenis_kelamin' => 'Perempuan', 'status' => 'aktif', 'kelas' => 'X PPLG 1'],
            ['nis' => '23241009', 'nama_lengkap' => 'Gilang Ramadhan', 'jenis_kelamin' => 'Laki-laki', 'status' => 'aktif', 'kelas' => 'XI RPL 1'],
            ['nis' => '23241010', 'nama_lengkap' => 'Zahra Anindya', 'jenis_kelamin' => 'Perempuan', 'status' => 'aktif', 'kelas' => 'XI TKJ 1'],
        ];

        $studentModels = [];
        foreach ($sampleStudents as $st) {
            $siswa = MasterSiswa::firstOrCreate(
                ['nis' => $st['nis']],
                [
                    'nama_lengkap' => $st['nama_lengkap'],
                    'jenis_kelamin' => $st['jenis_kelamin'],
                    'status' => $st['status'],
                    'data_source' => 'seeder',
                    'is_data_verified' => true,
                ]
            );
            $studentModels[] = $siswa;
        }

        // Hubungkan ke rombel
        if (isset($studentModels[0])) { $rombelRpl->siswa()->syncWithoutDetaching([$studentModels[0]->id, $studentModels[1]->id, $studentModels[8]->id]); }
        if (isset($studentModels[2])) { $rombelTkj->siswa()->syncWithoutDetaching([$studentModels[2]->id, $studentModels[3]->id, $studentModels[9]->id]); }

        // 3. Osis Setting
        $setting = OsisSetting::current();

        // 4. Pengurus OSIS Existing
        OsisPengurus::firstOrCreate(['jabatan' => 'Ketua Umum'], [
            'master_siswa_id' => $studentModels[0]->id ?? null,
            'nama' => 'Muhammad Fathan Pratama',
            'sekbid' => 'Badan Pengurus Harian (BPH)',
            'kelas' => 'XI RPL 1',
            'urutan' => 1,
            'is_active' => true,
        ]);

        OsisPengurus::firstOrCreate(['jabatan' => 'Wakil Ketua Umum'], [
            'master_siswa_id' => $studentModels[1]->id ?? null,
            'nama' => 'Nabila Putri Zahra',
            'sekbid' => 'Badan Pengurus Harian (BPH)',
            'kelas' => 'XI RPL 1',
            'urutan' => 2,
            'is_active' => true,
        ]);

        OsisPengurus::firstOrCreate(['jabatan' => 'Sekretaris Umum'], [
            'master_siswa_id' => $studentModels[3]->id ?? null,
            'nama' => 'Siti Aisyah Rahma',
            'sekbid' => 'Badan Pengurus Harian (BPH)',
            'kelas' => 'XI TKJ 1',
            'urutan' => 3,
            'is_active' => true,
        ]);

        OsisPengurus::firstOrCreate(['jabatan' => 'Bendahara Umum'], [
            'master_siswa_id' => $studentModels[5]->id ?? null,
            'nama' => 'Adelia Citra Lestari',
            'sekbid' => 'Badan Pengurus Harian (BPH)',
            'kelas' => 'XI Animasi 1',
            'urutan' => 4,
            'is_active' => true,
        ]);

        OsisPengurus::firstOrCreate(['jabatan' => 'Ketua Sekbid 9 (TIK & Digital)'], [
            'master_siswa_id' => $studentModels[2]->id ?? null,
            'nama' => 'Arya Bima Sena',
            'sekbid' => 'Sekbid 9 - Teknologi Informasi & Komunikasi',
            'kelas' => 'XI TKJ 1',
            'urutan' => 5,
            'is_active' => true,
        ]);

        // 5. Agenda OSIS
        OsisAgenda::firstOrCreate(['judul' => 'Pemilihan Ketua & Wakil Ketua OSIS (Pilketos 2026)'], [
            'deskripsi' => 'Pesta demokrasi akbar tahunan SMK Telkom Lampung dengan pemungutan suara digital (E-Voting) berbasis barcode scan.',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(2)->toDateString(),
            'lokasi' => 'Aula Graha Telkom & Bilik Suara E-Voting',
            'kategori' => 'Demokrasi Sekolah',
            'status' => 'berlangsung',
            'is_published' => true,
        ]);

        OsisAgenda::firstOrCreate(['judul' => 'Latihan Dasar Kepemimpinan Siswa (LDKS) 2026'], [
            'deskripsi' => 'Penggemblengan mental dan kepemimpinan bagi calon pengurus OSIS generasi berikutnya.',
            'tanggal_mulai' => now()->addDays(15)->toDateString(),
            'tanggal_selesai' => now()->addDays(17)->toDateString(),
            'lokasi' => 'Camp Outbound Wira Garden',
            'kategori' => 'Pengembangan Karakter',
            'status' => 'akan_datang',
            'is_published' => true,
        ]);

        OsisAgenda::firstOrCreate(['judul' => 'Telkom E-Sport & Tech Championship (TETRA 2026)'], [
            'deskripsi' => 'Kompetisi coding, game dev, dan e-sport tingkat SMP/SMA se-Provinsi Lampung.',
            'tanggal_mulai' => now()->addDays(30)->toDateString(),
            'tanggal_selesai' => now()->addDays(32)->toDateString(),
            'lokasi' => 'SMK Telkom Lampung',
            'kategori' => 'Event Tahunan',
            'status' => 'akan_datang',
            'is_published' => true,
        ]);

        // 6. Periode Pilketos
        $periode = PilketosPeriode::firstOrCreate(
            ['nama_periode' => 'Pemilihan Ketua & Wakil Ketua OSIS Periode 2026/2027'],
            [
                'tahun_pelajaran' => '2026/2027',
                'deskripsi' => 'Pemilihan umum Ketua dan Wakil Ketua OSIS SMK Telkom Lampung masa bakti 2026/2027. Menggunakan sistem E-Voting Barcode Scanner anti kecurangan.',
                'status' => 'voting_berlangsung',
                'tgl_mulai_voting' => now()->subHours(2),
                'tgl_selesai_voting' => now()->addHours(8),
                'is_active' => true,
            ]
        );

        // 7. Timeline Pilketos
        $timelines = [
            ['judul_tahap' => 'Pendaftaran & Uji Berkas Bakal Paslon', 'tanggal_mulai' => now()->subDays(14)->toDateString(), 'tanggal_selesai' => now()->subDays(10)->toDateString(), 'status_tahap' => 'selesai', 'urutan' => 1, 'deskripsi' => 'Penerimaan berkas calon ketua & wakil ketua OSIS'],
            ['judul_tahap' => 'Uji Kelayakan & Wawancara Kepala Sekolah', 'tanggal_mulai' => now()->subDays(9)->toDateString(), 'tanggal_selesai' => now()->subDays(7)->toDateString(), 'status_tahap' => 'selesai', 'urutan' => 2, 'deskripsi' => 'Penilaian visi misi, integritas, dan rekam jejak akademik'],
            ['judul_tahap' => 'Masa Kampanye Terbuka & Sosial Media', 'tanggal_mulai' => now()->subDays(6)->toDateString(), 'tanggal_selesai' => now()->subDays(2)->toDateString(), 'status_tahap' => 'selesai', 'urutan' => 3, 'deskripsi' => 'Pemaparan program unggulan ke tiap kelas dan IG OSIS'],
            ['judul_tahap' => 'Debat Terbuka Kandidat Paslon', 'tanggal_mulai' => now()->subDays(1)->toDateString(), 'tanggal_selesai' => now()->subDays(1)->toDateString(), 'status_tahap' => 'selesai', 'urutan' => 4, 'deskripsi' => 'Adu gagasan di hadapan guru dan perwakilan siswa'],
            ['judul_tahap' => 'Pemungutan Suara (E-Voting Bilik Barcode)', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->toDateString(), 'status_tahap' => 'berlangsung', 'urutan' => 5, 'deskripsi' => 'Pencoblosan digital di bilik suara menggunakan kartu pemilih barcode'],
            ['judul_tahap' => 'Live Count & Pengumuman Hasil Rekapitulasi', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->addDay()->toDateString(), 'status_tahap' => 'akan_datang', 'urutan' => 6, 'deskripsi' => 'Sidang pleno rekapitulasi dan penetapan ketua terpilih'],
        ];

        foreach ($timelines as $tl) {
            PilketosTimeline::firstOrCreate(
                ['pilketos_periode_id' => $periode->id, 'judul_tahap' => $tl['judul_tahap']],
                $tl
            );
        }

        // 8. Calon Paslon Kandidat
        $paslon1 = PilketosKandidat::firstOrCreate(
            ['pilketos_periode_id' => $periode->id, 'nomor_urut' => 1],
            [
                'ketua_siswa_id' => $studentModels[2]->id ?? null,
                'nama_ketua' => 'Arya Bima Sena',
                'nis_ketua' => '23241003',
                'kelas_ketua' => 'XI TKJ 1',
                'wakil_siswa_id' => $studentModels[3]->id ?? null,
                'nama_wakil' => 'Siti Aisyah Rahma',
                'nis_wakil' => '23241004',
                'kelas_wakil' => 'XI TKJ 1',
                'tagline' => 'Bersama Maju, Cerdas Berteknologi, Berkarakter Juara',
                'visi' => 'Menjadikan OSIS SMK Telkom Lampung sebagai inkubator kepemimpinan digital yang adaptif, transparan, dan solutif bagi seluruh civitas akademika.',
                'misi' => "1. Digitalisasi seluruh alur aspirasi dan event sekolah melalui platform SISFO.\n2. Mengadakan Telkom Coding & Networking Bootcamp antarsiswa.\n3. Mewujudkan ruang kreasi lintas jurusan tanpa sekat.\n4. Memperkuat kedisiplinan dan sopan santun dengan pendekatan teladan.",
                'program_unggulan' => "• Telkom Student Techfest & Hackathon\n• Podcast Aspirasi Siswa 'Suara Telkom'\n• Gerakan 1 Siswa 1 Portofolio Digital",
            ]
        );

        $paslon2 = PilketosKandidat::firstOrCreate(
            ['pilketos_periode_id' => $periode->id, 'nomor_urut' => 2],
            [
                'ketua_siswa_id' => $studentModels[4]->id ?? null,
                'nama_ketua' => 'Rafi Ahmad Fauzi',
                'nis_ketua' => '23241005',
                'kelas_ketua' => 'XI Animasi 1',
                'wakil_siswa_id' => $studentModels[5]->id ?? null,
                'nama_wakil' => 'Adelia Citra Lestari',
                'nis_wakil' => '23241006',
                'kelas_wakil' => 'XI Animasi 1',
                'tagline' => 'Inovasi Tanpa Batas, Karya Nyata untuk Prestasi Emas',
                'visi' => 'Terwujudnya OSIS yang solid, kreatif, dan inklusif yang memadukan bakat seni, teknologi industri, dan jiwa sosial yang tinggi.',
                'misi' => "1. Meningkatkan partisipasi siswa dalam lomba nasional maupun internasional.\n2. Revitalisasi kegiatan ekstrakurikuler seni, olahraga, dan riset.\n3. Membangun kerjasama aktif dengan alumni dan industri kreatif.\n4. Program kepedulian lingkungan 'Green & Clean Telkom Campus'.",
                'program_unggulan' => "• Telkom Youth Art & Animation Showcase\n• Liga Olahraga & E-Sport Antar Kelas\n• OSIS Peduli & Bakti Sosial Rutin",
            ]
        );

        $paslon3 = PilketosKandidat::firstOrCreate(
            ['pilketos_periode_id' => $periode->id, 'nomor_urut' => 3],
            [
                'ketua_siswa_id' => $studentModels[6]->id ?? null,
                'nama_ketua' => 'Dimas Bagus Wicaksono',
                'nis_ketua' => '23241007',
                'kelas_ketua' => 'X PPLG 1',
                'wakil_siswa_id' => $studentModels[7]->id ?? null,
                'nama_wakil' => 'Karin Amanda',
                'nis_wakil' => '23241008',
                'kelas_wakil' => 'X PPLG 1',
                'tagline' => 'Energi Muda, Aksi Nyata, Harapan Baru',
                'visi' => 'Mewujudkan ekosistem kesiswaan yang harmonis, suportif terhadap kesehatan mental, serta tangguh menghadapi tantangan industri masa depan.',
                'misi' => "1. Membuka forum konseling sebaya dan pengembangan bakat mandiri.\n2. Edukasi literasi finansial dan kewirausahaan berbasis UMKM siswa.\n3. Kampanye anti-bullying dan ramah disabilitas di lingkungan sekolah.\n4. Optimalisasi media sosial OSIS sebagai sarana edukasi dan informasi interaktif.",
                'program_unggulan' => "• Telkom Wellness Corner & Peer Counselor\n• Student Mini Market & Creative Bazaar\n• Zero Bullying Campus Campaign",
            ]
        );

        // 9. Generate DPT & Barcode untuk semua siswa
        foreach ($studentModels as $index => $siswa) {
            $status = ($index < 3) ? 'sudah_memilih' : 'belum_memilih';
            $waktu = ($status === 'sudah_memilih') ? now()->subMinutes(rand(10, 60)) : null;
            $bilik = ($status === 'sudah_memilih') ? 'Bilik ' . rand(1, 3) : null;
            $kelasNama = $sampleStudents[$index]['kelas'] ?? 'XI Telkom';

            // Token barcode format Code 128: misal PIL-26-XXXXX
            $token = 'PIL-26-' . str_pad($siswa->nis, 8, '0', STR_PAD_LEFT);

            $dpt = PilketosDpt::firstOrCreate(
                ['pilketos_periode_id' => $periode->id, 'nis' => $siswa->nis],
                [
                    'master_siswa_id' => $siswa->id,
                    'nama_pemilih' => $siswa->nama_lengkap,
                    'nisn' => '00' . rand(10000000, 99999999),
                    'kelas' => $kelasNama,
                    'jenis_kelamin' => $siswa->jenis_kelamin,
                    'barcode_token' => $token,
                    'status_memilih' => $status,
                    'waktu_memilih' => $waktu,
                    'bilik_id' => $bilik,
                ]
            );

            // Jika status sudah memilih, buat vote suara masuk demo
            if ($status === 'sudah_memilih' && $periode->votes()->count() < 3) {
                $chosenPaslon = match ($index % 3) {
                    0 => $paslon1,
                    1 => $paslon2,
                    default => $paslon3,
                };

                \App\Models\PilketosVote::create([
                    'pilketos_periode_id' => $periode->id,
                    'pilketos_kandidat_id' => $chosenPaslon->id,
                    'bilik_id' => $bilik,
                    'voted_at' => $waktu ?? now(),
                ]);
            }
        }
    }
}
