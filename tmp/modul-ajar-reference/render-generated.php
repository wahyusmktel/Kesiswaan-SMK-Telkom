<?php

use App\Models\TeachingModule;
use App\Support\TeachingModuleSchema;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Console\Kernel;

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
Carbon::setLocale('id');

$module = new TeachingModule();
$module->forceFill([
    'program_keahlian' => 'Teknik Komputer dan Jaringan',
    'mata_pelajaran' => 'Mata Pelajaran Pilihan (Cloud Computing)',
    'fase' => 'F',
    'nama_penyusun' => 'Wahyu Rahmat Hidayat, S.Kom., Gr.',
    'instansi' => 'SMK Telkom Lampung',
    'tahun_pelajaran' => '2026/2027',
    'semester' => 'Ganjil',
    'nama_modul' => 'Konsep Dasar Cloud Computing',
    'alokasi_waktu' => '4 JP',
    'jenjang' => 'SMK',
    'kelas' => 'XI',
    'kode_modul' => 'MA-1.1',
    'jumlah_murid' => 'Disesuaikan',
    'lingkup_materi' => 'Pengenalan Cloud Computing',
]);

$content = TeachingModuleSchema::defaults([
    'allocation' => '4 JP',
    'teacher_name' => $module->nama_penyusun,
]);
$content['identification']['students'] = [
    'Peserta didik kelas XI SMK pada program keahlian TKJ memiliki pengalaman menggunakan internet, website, layanan penyimpanan daring, dan aplikasi digital, tetapi sebagian belum memahami bahwa layanan tersebut berjalan di atas infrastruktur server dan komputasi awan.',
];
$content['identification']['materials'] = [
    'Materi mencakup konsep dasar cloud computing, karakteristik cloud, manfaat cloud, perbedaan komputasi tradisional dan cloud, model layanan IaaS, PaaS, SaaS, serta gambaran private cloud dan public cloud.',
    'Jenis pengetahuan meliputi faktual, konseptual, dan prosedural dengan keterkaitan pada kebutuhan industri teknologi informasi.',
];
foreach ($content['identification']['graduate_profile'] as &$dimension) {
    $dimension['selected'] = in_array($dimension['key'], [
        'creativity', 'collaboration', 'communication', 'critical_reasoning', 'independence',
    ], true);
}
unset($dimension);

$content['design']['learning_outcomes'] = [
    'Peserta didik mampu memahami konsep dasar cloud computing, karakteristik layanan komputasi awan, model layanan IaaS, PaaS, dan SaaS, serta model deployment public cloud, private cloud, dan hybrid cloud.',
];
$content['design']['learning_objectives'] = [
    'Memahami konsep dasar cloud computing, manfaat, karakteristik, dan contoh penerapannya.',
    'Mengidentifikasi perbedaan IaaS, PaaS, dan SaaS beserta contoh layanannya.',
    'Menghubungkan konsep cloud computing dengan rencana pembangunan private server.',
];
$content['design']['learning_topics'] = [
    'Pengenalan Cloud Computing dan Perannya dalam Pembangunan Private Server untuk Website Lokal dan Publik.',
];
$content['design']['pedagogical_practices'] = [
    'Model pembelajaran berbasis proyek dengan orientasi masalah.',
    'Strategi diskusi kelompok, studi kasus, eksplorasi layanan cloud, pembuatan peta konsep, dan presentasi rancangan awal.',
];
$content['design']['learning_partners'] = [
    'Guru produktif TKJ.',
    'Teknisi atau laboran jaringan sekolah.',
    'Teman sebaya sebagai mitra diskusi dan uji coba ide proyek.',
];
$content['design']['learning_environment'] = [
    'Ruang fisik: laboratorium komputer, ruang kelas, dan jaringan lokal sekolah.',
    'Ruang virtual: LMS, dokumentasi resmi, video pembelajaran, dan forum diskusi.',
    'Budaya belajar: aman mencoba, saling membantu, berpikir sistematis, dan terbuka terhadap umpan balik.',
];
$content['design']['digital_use'] = [
    'Perencanaan menggunakan LMS, dokumen digital, dan papan kerja proyek.',
    'Pelaksanaan menggunakan video, simulasi topologi, pencarian referensi, dan presentasi digital.',
    'Asesmen menggunakan kuis daring, LKPD digital, refleksi, dan penilaian produk.',
];

$meeting = &$content['experiences'][0];
$meeting['opening'] = [
    'Guru membuka pembelajaran dengan salam, doa, dan sapaan ramah untuk menciptakan suasana positif.',
    'Guru melakukan ice breaking singkat dan mengajukan pertanyaan pemantik tentang layanan digital yang digunakan sehari-hari.',
    'Guru menyampaikan tujuan pembelajaran, alur kegiatan, dan manfaat materi bagi proyek private server.',
];
$phaseContent = [
    [
        ['Menayangkan ilustrasi data center, layanan cloud, dan website yang diakses melalui internet.', 'Mengaitkan masalah dengan kebutuhan server sekolah.'],
        ['Mengamati tayangan, menanggapi pertanyaan, dan menuliskan dugaan awal.'],
        ['Daftar masalah dan pertanyaan awal.'],
    ],
    [
        ['Membagi peserta didik ke dalam kelompok dan membagikan LKPD studi kasus.', 'Menjelaskan tugas, peran kelompok, dan kriteria keberhasilan.'],
        ['Membentuk kelompok, membaca studi kasus, dan membagi peran.'],
        ['Pembagian peran dan rencana kerja kelompok.'],
    ],
    [
        ['Memfasilitasi eksplorasi definisi cloud, karakteristik, dan model layanan.', 'Memberikan pertanyaan pengarah.'],
        ['Mencari informasi, membandingkan IaaS, PaaS, dan SaaS, lalu mencatat temuan.'],
        ['Tabel perbandingan model layanan cloud.'],
    ],
    [
        ['Mengarahkan penyusunan peta konsep atau infografis dan memfasilitasi presentasi.', 'Memberikan umpan balik formatif.'],
        ['Menyusun produk, mempresentasikan hasil, dan menanggapi kelompok lain.'],
        ['Peta konsep atau infografis cloud computing.'],
    ],
    [
        ['Memberikan umpan balik, meluruskan miskonsepsi, dan memimpin refleksi kelas.'],
        ['Menyampaikan refleksi tertulis dan merumuskan kesimpulan.'],
        ['Refleksi individu dan kesimpulan kelas.'],
    ],
];
foreach ($meeting['core_phases'] as $index => &$phase) {
    $phase['teacher_activities'] = $phaseContent[$index][0];
    $phase['student_activities'] = $phaseContent[$index][1];
    $phase['outputs'] = $phaseContent[$index][2];
}
unset($phase);
$meeting['closing'] = [
    'Guru dan peserta didik menyusun simpulan, melakukan refleksi, serta menyampaikan tindak lanjut untuk pertemuan berikutnya.',
];
unset($meeting);

$content['assessment']['initial'] = [
    'Kuis singkat melalui LMS tentang istilah internet, server, hosting, dan cloud.',
    'Tanya jawab awal untuk memetakan pengalaman peserta didik menggunakan layanan cloud.',
];
$content['assessment']['process'] = [
    'Observasi keaktifan diskusi kelompok.',
    'Penilaian LKPD dan peta konsep cloud computing.',
    'Umpan balik formatif saat presentasi kelompok.',
];
$content['assessment']['final'] = [
    'Presentasi peta konsep atau infografis kelompok.',
    'Refleksi tertulis individu dan kuis akhir singkat.',
];
$content['assessment']['criteria'] = [
    'Peserta didik dinyatakan mencapai tujuan apabila mampu menjelaskan konsep cloud computing, memberi contoh IaaS, PaaS, SaaS, membedakan cloud dengan server tradisional, serta menghubungkannya dengan proyek private server.',
];
$content['supporting']['trigger_questions'] = [
    'Apa perbedaan menyimpan file di komputer sendiri dan di layanan cloud?',
    'Mengapa perusahaan lebih memilih menggunakan cloud atau server virtual?',
    'Jika sekolah ingin memiliki website internal, layanan apa yang dibutuhkan?',
];
$content['supporting']['differentiation'] = [
    'Peserta didik yang membutuhkan bantuan diberi glosarium istilah cloud dan contoh sederhana.',
    'Peserta didik yang sudah mahir diberi tantangan membandingkan VPS, hosting, dan private server.',
    'Produk akhir dapat berupa peta konsep, infografis, atau slide sesuai kesiapan peserta didik.',
];
$content['supporting']['enrichment'] = [
    'Mencari contoh layanan cloud industri dan mengidentifikasi model layanannya.',
];
$content['supporting']['remedial'] = [
    'Menjelaskan ulang konsep server, internet, hosting, dan cloud dengan analogi sehari-hari.',
];
$content['attachments']['teaching_materials'] = [
    'Ringkasan Konsep Cloud Computing dan Model Layanan Cloud.',
];
$content['attachments']['worksheets'] = [
    'LKPD MA-1.1: Peta Konsep Cloud Computing dan Analisis IaaS, PaaS, SaaS.',
];
$content['attachments']['assessments'] = [
    'Kuis awal, observasi diskusi, rubrik produk peta konsep, dan refleksi individu.',
];
$content['approval'] = [
    'location' => 'Pringsewu',
    'date' => '2026-07-10',
    'validator_title' => 'Wakil Kepala Sekolah Bidang Kurikulum',
    'validator_name' => 'Siti Khairunnisa, S.Pd., Gr.',
    'validator_nip' => '25930023',
    'teacher_title' => 'Guru Mata Pelajaran',
    'teacher_name' => $module->nama_penyusun,
    'teacher_nip' => '25950022',
];

$imageData = static function (string $path): ?string {
    if (! is_file($path)) {
        return null;
    }

    return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
};

$pdf = Pdf::loadView('pdf.teaching-module', [
    'module' => $module,
    'content' => $content,
    'settings' => null,
    'approvalDate' => Carbon::parse($content['approval']['date']),
    'logoDataUri' => $imageData($root.'/public/images/teaching-module/smk-telkom-lampung.png'),
    'ribbonDataUri' => $imageData($root.'/public/images/teaching-module/header-ribbon.png'),
])->setPaper('a4', 'landscape')->setOption('defaultFont', 'Arial');

$output = __DIR__.'/render/generated-teaching-module.pdf';
file_put_contents($output, $pdf->output());
echo $output.PHP_EOL;
