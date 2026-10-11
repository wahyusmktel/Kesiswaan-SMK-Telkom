<?php

namespace Database\Seeders;

use App\Models\MasterGuru;
use App\Models\TeacherPortfolio;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherPortfolioDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'name' => 'Muhammad Fajar Siddiq',
                'email' => 'fajar.siddiq@smktelkom-mlg.sch.id',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Kom., M.T.',
                'nip' => '198904122015041001',
                'jabatan' => 'Guru Produktif RPL & Koordinator Lab Software Enterprise',
                'bidang_studi' => 'Rekayasa Perangkat Lunak (PPLG)',
                'kategori' => TeacherProfile::KATEGORI_PRODUKTIF,
                'bio' => 'Praktisi software engineering dan pendidik vokasi dengan pengalaman lebih dari 8 tahun dalam mendampingi talenta muda SMK Telkom menguasai ekosistem industri teknologi modern. Aktif sebagai mentor LKS Web Technologies, asesor BNSP, dan kontributor open-source.',
                'filosofi_mengajar' => 'Membangun karakter engineer tangguh yang memecahkan masalah nyata dunia industri melalui kode bersih dan empati pengguna.',
                'keahlian' => ['Laravel', 'Vue.js', 'Flutter', 'Cloud Computing', 'RESTful API', 'Git & CI/CD', 'Clean Architecture', 'Docker'],
                'sosial_media' => [
                    'linkedin' => 'https://linkedin.com/in/fajarsiddiq-telkom',
                    'github' => 'https://github.com/fajarsiddiq-dev',
                    'instagram' => 'https://instagram.com/fajar.code',
                    'website' => 'https://fajarsiddiq.dev',
                ],
                'pendidikan' => [
                    [
                        'jenjang' => 'S2',
                        'institusi' => 'Institut Teknologi Sepuluh Nopember (ITS) Surabaya',
                        'jurusan' => 'Teknik Informatika - Software Engineering',
                        'tahun_lulus' => '2022',
                    ],
                    [
                        'jenjang' => 'S1',
                        'institusi' => 'Universitas Brawijaya (UB) Malang',
                        'jurusan' => 'Teknik Informatika',
                        'tahun_lulus' => '2018',
                    ],
                ],
                'sertifikasi' => [
                    [
                        'nama' => 'AWS Certified Solutions Architect - Associate',
                        'penerbit' => 'Amazon Web Services',
                        'tahun' => '2024',
                    ],
                    [
                        'nama' => 'Asesor Kompetensi Rekayasa Perangkat Lunak',
                        'penerbit' => 'Badan Nasional Sertifikasi Profesi (BNSP)',
                        'tahun' => '2023',
                    ],
                    [
                        'nama' => 'Google Associate Cloud Engineer',
                        'penerbit' => 'Google Cloud',
                        'tahun' => '2023',
                    ],
                ],
                'portfolios' => [
                    [
                        'judul' => 'SISFO Stella - Ekosistem SuperApp Sekolah Digital',
                        'kategori' => 'Aplikasi Web & Cloud',
                        'deskripsi' => 'Arsitektur dan implementasi ekosistem web portal terpadu untuk kesiswaan, monitoring OKR, portofolio guru, dan presensi real-time berbasis Laravel dan Tailwind.',
                        'tahun' => '2025',
                        'tautan_proyek' => 'https://github.com/smktelkom/sisfo-stella',
                        'teknologi' => ['Laravel 11', 'Tailwind CSS', 'Alpine.js', 'MySQL', 'Docker'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Telkom EduPay - Payment Gateway SPP & Kantin Digital',
                        'kategori' => 'Fintech Mobile',
                        'deskripsi' => 'Aplikasi transaksi cashless sekolah terintegrasi QRIS Bank Indonesia dan kartu RFID siswa untuk ekosistem smart school.',
                        'tahun' => '2024',
                        'tautan_proyek' => 'https://github.com/smktelkom/edupay-mobile',
                        'teknologi' => ['Flutter', 'Node.js', 'PostgreSQL', 'QRIS API'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Sistem Presensi AI Facial Recognition Edge IoT',
                        'kategori' => 'AI & Embedded IoT',
                        'deskripsi' => 'Pengembangan presensi berbasis kamera Raspberry Pi dengan pengenalan wajah on-device yang terhubung ke bot notifikasi orang tua di Telegram.',
                        'tahun' => '2024',
                        'tautan_proyek' => 'https://github.com/smktelkom/ai-presensi',
                        'teknologi' => ['Python', 'OpenCV', 'Raspberry Pi 4', 'MQTT', 'Telegram Bot API'],
                        'is_featured' => false,
                    ],
                ],
            ],

            [
                'name' => 'Dian Kusuma Wardani',
                'email' => 'dian.kusuma@smktelkom-mlg.sch.id',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd., M.Kom.',
                'nip' => '199208152018012002',
                'jabatan' => 'Guru Produktif TJKT & Koordinator Cisco Networking Academy',
                'bidang_studi' => 'Teknik Jaringan Komputer & Telekomunikasi (TJKT)',
                'kategori' => TeacherProfile::KATEGORI_PRODUKTIF,
                'bio' => 'Spesialis infrastruktur jaringan komputer, routing enterprise, cyber security, dan serat optik. Pembina tim LKS bidang Network Cabling dan Cyber Security dengan capaian juara di tingkat wilayah dan nasional.',
                'filosofi_mengajar' => 'Jaringan bukan sekadar menghubungkan kabel, melainkan fondasi interaksi manusia di era peradaban cerdas modern.',
                'keahlian' => ['Cisco CCNA', 'MikroTik MTCRE', 'Cyber Security', 'Network Automation', 'Fiber Optic FTTH', 'Linux System Administration', 'Wireshark'],
                'sosial_media' => [
                    'linkedin' => 'https://linkedin.com/in/diankusuma-net',
                    'github' => 'https://github.com/dian-network',
                    'instagram' => 'https://instagram.com/dian.telecom',
                ],
                'pendidikan' => [
                    [
                        'jenjang' => 'S2',
                        'institusi' => 'Universitas Indonesia (UI) Jakarta',
                        'jurusan' => 'Teknologi Informasi - Keamanan Jaringan',
                        'tahun_lulus' => '2023',
                    ],
                    [
                        'jenjang' => 'S1',
                        'institusi' => 'Universitas Negeri Malang (UM)',
                        'jurusan' => 'Pendidikan Teknik Informatika & Komputer',
                        'tahun_lulus' => '2019',
                    ],
                ],
                'sertifikasi' => [
                    [
                        'nama' => 'Cisco Certified Network Associate (CCNA)',
                        'penerbit' => 'Cisco Systems',
                        'tahun' => '2023',
                    ],
                    [
                        'nama' => 'MikroTik Certified Routing Engineer (MTCRE)',
                        'penerbit' => 'MikroTik SIA',
                        'tahun' => '2022',
                    ],
                    [
                        'nama' => 'Certified Ethical Hacker (CEH) Associate',
                        'penerbit' => 'EC-Council',
                        'tahun' => '2024',
                    ],
                ],
                'portfolios' => [
                    [
                        'judul' => 'Perancangan Topologi Kampus Dual-Homing SDN SMK Telkom',
                        'kategori' => 'Infrastruktur Jaringan',
                        'deskripsi' => 'Desain redudansi jaringan core kampus dengan BGP multi-homing dan Software Defined Network untuk stabilitas akses 1.500+ siswa dan lab komputer.',
                        'tahun' => '2025',
                        'tautan_proyek' => 'https://network.smktelkom-mlg.sch.id',
                        'teknologi' => ['Cisco Catalyst', 'BGP Peering', 'VLAN 802.1Q', 'MikroTik CCR'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Network Threat Monitoring & Intrusion Detection Snort',
                        'kategori' => 'Keamanan Siber',
                        'deskripsi' => 'Implementasi IDS/IPS Snort terintegrasi Grafana dashboard untuk memantau ancaman malware dan serangan brute force jaringan sekolah.',
                        'tahun' => '2024',
                        'tautan_proyek' => null,
                        'teknologi' => ['Snort IDS', 'Grafana', 'Prometheus', 'Ubuntu Server'],
                        'is_featured' => false,
                    ],
                ],
            ],

            [
                'name' => 'Arya Daniswara',
                'email' => 'arya.daniswara@smktelkom-mlg.sch.id',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Sn., M.Ds.',
                'nip' => '199103282019031003',
                'jabatan' => 'Guru Produktif Animasi, Game Art & 3D Visualization',
                'bidang_studi' => 'Animasi & Desain Komunikasi Visual',
                'kategori' => TeacherProfile::KATEGORI_PRODUKTIF,
                'bio' => 'Pendidik kreatif berlatar belakang industri game dan studio animasi nasional. Membimbing siswa SMK Telkom membangun IP (Intellectual Property) orisinal, 3D assets berkualitas game-ready, dan visual effects memukau.',
                'filosofi_mengajar' => 'Kreativitas tanpa batas ketika teknologi bertemu dengan narasi emosional yang menyentuh jiwa penikmat karya.',
                'keahlian' => ['Blender 3D', 'Unreal Engine 5', 'Substance 3D Painter', 'Motion Graphics', 'Adobe After Effects', 'Character Rigging', 'Storyboarding'],
                'sosial_media' => [
                    'linkedin' => 'https://linkedin.com/in/aryadaniswara-art',
                    'instagram' => 'https://instagram.com/aryadaniswara.cg',
                    'website' => 'https://artstation.com/aryadaniswara',
                ],
                'pendidikan' => [
                    [
                        'jenjang' => 'S2',
                        'institusi' => 'Institut Teknologi Bandung (ITB)',
                        'jurusan' => 'Magister Desain Komunikasi Visual',
                        'tahun_lulus' => '2022',
                    ],
                    [
                        'jenjang' => 'S1',
                        'institusi' => 'Institut Seni Indonesia (ISI) Yogyakarta',
                        'jurusan' => 'Desain Produk & Multimedia',
                        'tahun_lulus' => '2017',
                    ],
                ],
                'sertifikasi' => [
                    [
                        'nama' => 'Unity Certified Professional: Artist',
                        'penerbit' => 'Unity Technologies',
                        'tahun' => '2023',
                    ],
                    [
                        'nama' => 'Asesor Kompetensi Animasi 3D',
                        'penerbit' => 'BNSP Indonesia',
                        'tahun' => '2022',
                    ],
                ],
                'portfolios' => [
                    [
                        'judul' => 'Nusantara Heroes - Game Art Asset 3D Karakter Budaya',
                        'kategori' => 'Game Asset & 3D Art',
                        'deskripsi' => 'Pengembangan paket karakter low-poly dan high-poly game ready yang memadukan kostum adat kepulauan nusantara dengan tema sci-fi futuristik.',
                        'tahun' => '2025',
                        'tautan_proyek' => 'https://artstation.com/artwork/nusantara-heroes',
                        'teknologi' => ['Blender 4.0', 'Substance 3D Painter', 'Unreal Engine 5.4'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Animasi AR: Jelajah Sejarah Telekomunikasi Nusantara',
                        'kategori' => 'Animasi Edukasi & AR',
                        'deskripsi' => 'Aplikasi Augmented Reality interaktif bagi museum telekomunikasi yang menampilkan animasi 3D evolusi transmisi sandi morse hingga serat optik 5G.',
                        'tahun' => '2024',
                        'tautan_proyek' => null,
                        'teknologi' => ['Unity', 'ARKit', 'Blender', 'Cinema 4D'],
                        'is_featured' => true,
                    ],
                ],
            ],

            [
                'name' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@smktelkom-mlg.sch.id',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd., M.Ed.',
                'nip' => '199311052020022004',
                'jabatan' => 'Guru Bahasa Inggris & Koordinator Hubungan Industri Internasional',
                'bidang_studi' => 'Bahasa Inggris & Professional Tech Communication',
                'kategori' => TeacherProfile::KATEGORI_UMUM,
                'bio' => 'Pendidik dengan keahlian pengajaran bahasa Inggris vokasi dan komunikasi teknis global. Berpengalaman membimbing siswa SMK Telkom lolos seleksi beasiswa luar negeri, student exchange, serta magang kerja di industri multinasional.',
                'filosofi_mengajar' => 'Bahasa adalah jembatan yang membawa inovasi talenta muda Indonesia melintasi batas geografis dunia.',
                'keahlian' => ['Technical Writing', 'IELTS / TOEIC Coaching', 'Public Speaking', 'Cross-Cultural Communication', 'Curriculum Design', 'Instructional Tech'],
                'sosial_media' => [
                    'linkedin' => 'https://linkedin.com/in/sitirahmawati-med',
                    'instagram' => 'https://instagram.com/miss.rahma.english',
                ],
                'pendidikan' => [
                    [
                        'jenjang' => 'S2',
                        'institusi' => 'Monash University, Australia',
                        'jurusan' => 'Master of Education (TESOL & Digital Learning)',
                        'tahun_lulus' => '2021',
                    ],
                    [
                        'jenjang' => 'S1',
                        'institusi' => 'Universitas Negeri Malang (UM)',
                        'jurusan' => 'Pendidikan Bahasa Inggris',
                        'tahun_lulus' => '2017',
                    ],
                ],
                'sertifikasi' => [
                    [
                        'nama' => 'Certificate in English Language Teaching to Adults (CELTA)',
                        'penerbit' => 'Cambridge Assessment English',
                        'tahun' => '2023',
                    ],
                    [
                        'nama' => 'Certified TOEIC Administrator & Trainer',
                        'penerbit' => 'Educational Testing Service (ETS)',
                        'tahun' => '2022',
                    ],
                ],
                'portfolios' => [
                    [
                        'judul' => 'Modul TechEnglish: Panduan Komunikasi Profesional Siswa IT',
                        'kategori' => 'Modul & Buku Ajar',
                        'deskripsi' => 'Penyusunan modul pembelajaran bahasa Inggris berbasis proyek simulasi daily standup meeting, code review, dan proposal pitch untuk siswa vokasi teknologi.',
                        'tahun' => '2024',
                        'tautan_proyek' => null,
                        'teknologi' => ['Interactive PDF', 'Canva for Education', 'Audio Podcast'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Program Pertukaran Virtual SMK Telkom x Tokyo Tech High School',
                        'kategori' => 'Program Kolaborasi Internasional',
                        'deskripsi' => 'Inisiator dan koordinator proyek kolaboratif daring lintas budaya antara siswa SMK Telkom Malang dengan Tokyo Tech High School bertema Smart City Challenge.',
                        'tahun' => '2024',
                        'tautan_proyek' => null,
                        'teknologi' => ['Zoom Education', 'Miro Board', 'Google Classroom'],
                        'is_featured' => false,
                    ],
                ],
            ],

            [
                'name' => 'Bambang Prakoso',
                'email' => 'bambang.prakoso@smktelkom-mlg.sch.id',
                'gelar_depan' => 'Ir.',
                'gelar_belakang' => 'S.T., M.M., IPU.',
                'nip' => '197805122005011005',
                'jabatan' => 'Wakil Kepala Sekolah Bidang Sarana & Guru Otomasi Industri',
                'bidang_studi' => 'Internet of Things & Otomasi Pabrik Pintar',
                'kategori' => TeacherProfile::KATEGORI_MANAJEMEN,
                'bio' => 'Berdedikasi lebih dari 18 tahun dalam tata kelola fasilitas pendidikan dan kemitraan industri manufaktur teknologi tinggi SMK Telkom. Mengembangkan teaching factory robotika berstandar internasional.',
                'filosofi_mengajar' => 'Disiplin teknis dan integritas moral adalah fondasi utama seorang engineer yang dipercaya industri.',
                'keahlian' => ['PLC Siemens S7-1200', 'SCADA System', 'Industrial IoT', 'Smart Factory Automation', 'Lean 5S Management', 'Keselamatan K3'],
                'sosial_media' => [
                    'linkedin' => 'https://linkedin.com/in/bambangprakoso-eng',
                ],
                'pendidikan' => [
                    [
                        'jenjang' => 'S2',
                        'institusi' => 'Universitas Gadjah Mada (UGM) Yogyakarta',
                        'jurusan' => 'Magister Manajemen Industri & Operasional',
                        'tahun_lulus' => '2020',
                    ],
                    [
                        'jenjang' => 'S1',
                        'institusi' => 'Institut Teknologi Bandung (ITB)',
                        'jurusan' => 'Teknik Elektro - Sistem Kontrol',
                        'tahun_lulus' => '2004',
                    ],
                ],
                'sertifikasi' => [
                    [
                        'nama' => 'Insinyur Profesional Utama (IPU)',
                        'penerbit' => 'Persatuan Insinyur Indonesia (PII)',
                        'tahun' => '2023',
                    ],
                    [
                        'nama' => 'Certified Automation Professional (CAP)',
                        'penerbit' => 'International Society of Automation (ISA)',
                        'tahun' => '2022',
                    ],
                    [
                        'nama' => 'Ahli K3 Umum Ketenagakerjaan',
                        'penerbit' => 'Kementerian Ketenagakerjaan RI',
                        'tahun' => '2021',
                    ],
                ],
                'portfolios' => [
                    [
                        'judul' => 'Smart Energy Metering & Power Management IoT Kampus',
                        'kategori' => 'Industrial IoT & Energi',
                        'deskripsi' => 'Pengembangan sistem pemantauan konsumsi daya listrik gedung sekolah berbasis sensor Modbus RS485 dan dashboard cloud untuk efisiensi beban 18%.',
                        'tahun' => '2025',
                        'tautan_proyek' => null,
                        'teknologi' => ['Siemens PLC', 'Modbus RS485', 'MQTT', 'ThingsBoard Cloud'],
                        'is_featured' => true,
                    ],
                    [
                        'judul' => 'Teaching Factory Lini Konveyor Otomasi Robotik',
                        'kategori' => 'Mekatronika & Robotika',
                        'deskripsi' => 'Pembangunan fasilitas pembelajaran teaching factory lini perakitan mini terintegrasi lengan robot pneumatik untuk simulasi pabrik otomatis.',
                        'tahun' => '2024',
                        'tautan_proyek' => null,
                        'teknologi' => ['Festo Pneumatics', 'Omron PLC', 'SCADA InTouch'],
                        'is_featured' => false,
                    ],
                ],
            ],
        ];

        foreach ($teachers as $data) {
            // 1. Create or update User
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // 2. Create or update MasterGuru
            $masterGuru = MasterGuru::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'kode_guru' => 'G-' . strtoupper(Str::random(4)),
                    'nama_lengkap' => $data['name'],
                    'jenis_kelamin' => 'L',
                    'employee_category' => MasterGuru::CATEGORY_TEACHER,
                ]
            );

            // 3. Create or update TeacherProfile
            $slug = TeacherProfile::generateUniqueSlug($data['name']);
            $profile = TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'master_guru_id' => $masterGuru->id,
                    'nama_lengkap' => $data['name'],
                    'slug' => $slug,
                    'gelar_depan' => $data['gelar_depan'],
                    'gelar_belakang' => $data['gelar_belakang'],
                    'jabatan' => $data['jabatan'],
                    'bidang_studi' => $data['bidang_studi'],
                    'kategori' => $data['kategori'],
                    'bio' => $data['bio'],
                    'filosofi_mengajar' => $data['filosofi_mengajar'],
                    'keahlian' => $data['keahlian'],
                    'riwayat_pendidikan' => $data['pendidikan'],
                    'sertifikasi' => $data['sertifikasi'],
                    'sosial_media' => $data['sosial_media'],
                    'is_published' => true,
                    'views_count' => rand(120, 680),
                ]
            );

            // 4. Create portfolios
            $profile->portfolios()->delete(); // Reset to avoid duplication
            $order = 1;
            foreach ($data['portfolios'] as $pData) {
                TeacherPortfolio::create([
                    'teacher_profile_id' => $profile->id,
                    'judul' => $pData['judul'],
                    'kategori' => $pData['kategori'],
                    'deskripsi' => $pData['deskripsi'],
                    'tahun' => $pData['tahun'],
                    'tautan_proyek' => $pData['tautan_proyek'],
                    'teknologi' => $pData['teknologi'],
                    'is_featured' => $pData['is_featured'],
                    'sort_order' => $order++,
                ]);
            }
        }
    }
}
