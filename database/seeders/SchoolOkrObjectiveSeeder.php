<?php

namespace Database\Seeders;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SchoolOkrObjectiveSeeder extends Seeder
{
    public function run(): void
    {
        $period = OkrPeriod::query()
            ->where('status', 'active')
            ->latest('id')
            ->first()
            ?? OkrPeriod::query()->latest('id')->first();

        if (! $period) {
            throw new RuntimeException('Periode OKR belum tersedia. Buka halaman Manajemen OKR terlebih dahulu untuk membuat periode aktif.');
        }

        DB::transaction(function () use ($period) {
            foreach ($this->objectives() as $objectiveIndex => $objectiveData) {
                $objective = OkrObjective::updateOrCreate(
                    [
                        'okr_period_id' => $period->id,
                        'code' => $objectiveData['code'],
                    ],
                    [
                        'title' => $objectiveData['title'],
                        'sort_order' => $objectiveIndex + 1,
                    ]
                );

                foreach ($objectiveData['key_results'] as $keyResultIndex => $keyResultData) {
                    OkrKeyResult::updateOrCreate(
                        [
                            'okr_objective_id' => $objective->id,
                            'code' => $keyResultData['code'],
                        ],
                        $keyResultData + [
                            'sort_order' => $keyResultIndex + 1,
                            'due_date' => $period->ends_at,
                        ]
                    );
                }
            }
        });

        $this->command?->info("4 objektif dan 12 key result berhasil disinkronkan ke periode {$period->title}.");
    }

    private function objectives(): array
    {
        return [
            [
                'code' => 'O1',
                'title' => 'Lulusan Vokasi Unggul (Kompetensi & Relevansi Industri)',
                'key_results' => [
                    $this->keyResult('KR 1.1', 'Keterserapan Lulusan di Industri Relevan', '> 85% lulusan bekerja sesuai bidangnya dalam 6 bulan pertama setelah kelulusan.', 'percentage', 85, '%'),
                    $this->keyResult('KR 1.2', 'Sertifikasi Kompetensi Standar Industri', '> 90% siswa kelas XII memiliki minimal satu sertifikasi industri yang diakui secara nasional/internasional (misal: BNSP, Cisco, Mikrotik).', 'percentage', 90, '%'),
                    $this->keyResult('KR 1.3', 'Kemitraan Industri Aktif & Sinkronisasi Kurikulum', 'Terjalin minimal 20 MoU aktif dengan industri, yang mencakup sinkronisasi kurikulum, guru tamu, dan program magang setiap tahun.', 'number', 20, 'MoU aktif'),
                    $this->keyResult('KR 1.4', 'Prestasi di Kompetisi Akademik & Vokasi', 'Meraih minimal 5 gelar juara (tingkat provinsi/nasional) dalam kompetisi seperti LKS, debat teknologi, atau kompetisi pemrograman setiap tahun.', 'number', 5, 'gelar juara'),
                ],
            ],
            [
                'code' => 'O2',
                'title' => 'Berkarakter (Integritas, Akhlak, dan Soft Skills)',
                'key_results' => [
                    $this->keyResult('KR 2.1', 'Penilaian Karakter oleh Industri (Saat Magang)', 'Rata-rata skor penilaian soft skills (integritas, kerja sama, disiplin) dari penyelia industri untuk siswa magang mencapai 4.5 dari 5.0.', 'number', 4.5, 'skor (maks. 5)'),
                    $this->keyResult('KR 2.2', 'Indeks Pelanggaran Tata Tertib', 'Penurunan angka pelanggaran tata tertib kategori sedang dan berat sebesar 15% per tahun.', 'percentage', 15, '% penurunan'),
                    $this->keyResult('KR 2.3', 'Partisipasi dalam Kepemimpinan & Organisasi', '> 60% siswa aktif terlibat dalam kegiatan OSIS, ekstrakurikuler, atau kepanitiaan yang', 'percentage', 60, '%'),
                ],
            ],
            [
                'code' => 'O3',
                'title' => 'Ekonomi Kreatif (Jiwa Kewirausahaan & Inovasi)',
                'key_results' => [
                    $this->keyResult('KR 3.1', 'Proyek Startup dari Inkubator Sekolah', 'Minimal 3 startup digital rintisan siswa lahir dari program inkubator sekolah setiap tahun ajaran.', 'number', 3, 'startup'),
                    $this->keyResult('KR 3.2', 'Jumlah Produk Kreatif Digital Siswa', 'Minimal 10 produk digital (aplikasi, web, desain grafis) karya siswa yang memiliki potensi', 'number', 10, 'produk digital'),
                ],
            ],
            [
                'code' => 'O4',
                'title' => 'Nuansa yang Religius (Lingkungan & Pembinaan Spiritual)',
                'key_results' => [
                    $this->keyResult('KR 4.1', 'Tingkat Partisipasi Kegiatan Keagamaan', 'Kehadiran siswa dalam kegiatan pembinaan spiritual rutin (misalnya Shalat Dhuha, Keputrian, dll.) mencapai rata-rata > 95%.', 'percentage', 95, '%'),
                    $this->keyResult('KR 4.2', 'Skor Kepuasan "Lingkungan Aman & Nyaman"', 'Skor rata-rata pada item survei kepuasan siswa (CSI) terkait "nuansa religius, aman, dan nyaman" mencapai > 8.5 dari 10.', 'number', 8.5, 'skor (maks. 10)'),
                    $this->keyResult('KR 4.3', 'Implementasi Budaya 5S dan Nilai Religius', '100% area sekolah menerapkan budaya 5S (Senyum, Sapa, Salam, Sopan, Santun) yang terintegrasi dengan nilai-nilai akhlak mulia.', 'percentage', 100, '% area'),
                ],
            ],
        ];
    }

    private function keyResult(
        string $code,
        string $title,
        string $description,
        string $metricType,
        float $targetValue,
        string $metricUnit
    ): array {
        return [
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'metric_type' => $metricType,
            'baseline_value' => 0,
            'target_value' => $targetValue,
            'metric_unit' => $metricUnit,
            'weight' => 1,
        ];
    }
}
