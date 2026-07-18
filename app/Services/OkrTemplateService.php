<?php

namespace App\Services;

use App\Models\OkrKeyResult;
use App\Models\OkrObjective;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OkrTemplateService
{
    public function ensureForActiveAcademicYear(User $user): OkrPeriod
    {
        $academicYear = TahunPelajaran::where('is_active', true)->first()
            ?? TahunPelajaran::latest('id')->first();

        $period = OkrPeriod::query()
            ->when($academicYear, function ($query) use ($academicYear) {
                $query->whereHas('academicYear', fn ($year) => $year->where('tahun', $academicYear->tahun));
            }, fn ($query) => $query->whereNull('tahun_pelajaran_id'))
            ->latest('id')
            ->first();

        if ($period) {
            $this->ensureUnits();

            return $period;
        }

        return DB::transaction(function () use ($academicYear, $user) {
            $units = $this->ensureUnits();
            $yearLabel = $academicYear?->tahun ?? now()->format('Y').'/'.now()->addYear()->format('Y');
            [$startYear, $endYear] = array_pad(explode('/', $yearLabel), 2, null);

            $period = OkrPeriod::create([
                'tahun_pelajaran_id' => $academicYear?->id,
                'title' => 'Program Kerja OKR '.$yearLabel,
                'vision' => 'Terwujudnya Lulusan Vokasi Unggul dan Berkarakter di Bidang Teknologi Informasi dan Ekonomi Kreatif dalam Nuansa yang Religius',
                'starts_at' => $startYear ? $startYear.'-07-01' : now()->startOfYear(),
                'ends_at' => $endYear ? $endYear.'-06-30' : now()->endOfYear(),
                'status' => 'active',
                'created_by' => $user->id,
            ]);

            foreach ($this->objectives() as $objectiveIndex => $objectiveData) {
                $objective = OkrObjective::create([
                    'okr_period_id' => $period->id,
                    'code' => $objectiveData['code'],
                    'title' => $objectiveData['title'],
                    'sort_order' => $objectiveIndex + 1,
                ]);

                foreach ($objectiveData['key_results'] as $keyResultIndex => $keyResultData) {
                    OkrKeyResult::create($keyResultData + [
                        'okr_objective_id' => $objective->id,
                        'sort_order' => $keyResultIndex + 1,
                        'due_date' => $period->ends_at,
                    ]);
                }
            }

            $this->seedQmrExample($period, $units->firstWhere('code', 'QMR'), $user);

            return $period;
        });
    }

    private function ensureUnits()
    {
        return collect($this->units())->map(function (array $unit, int $index) {
            return OkrUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit + ['sort_order' => $index + 1, 'is_active' => true]
            );
        });
    }

    private function seedQmrExample(OkrPeriod $period, ?OkrUnit $unit, User $user): void
    {
        if (! $unit) {
            return;
        }

        $keyResult = OkrKeyResult::whereHas(
            'objective',
            fn ($query) => $query->where('okr_period_id', $period->id)
        )->where('code', 'KR 5.3')->first();

        if (! $keyResult || OkrPlan::where('okr_key_result_id', $keyResult->id)->where('okr_unit_id', $unit->id)->exists()) {
            return;
        }

        $annual = OkrPlan::create([
            'okr_key_result_id' => $keyResult->id,
            'okr_unit_id' => $unit->id,
            'owner_id' => $user->id,
            'level' => 'annual',
            'title' => 'Menjamin kesiapan akreditasi A+ dan sertifikasi ISO 9001:2015',
            'description' => 'Menutup temuan AMI, memastikan kepatuhan SOP, dan menyiapkan seluruh eviden mutu sekolah.',
            'starts_at' => $period->starts_at,
            'ends_at' => $period->ends_at,
            'target_value' => 100,
            'metric_unit' => '% kesiapan',
            'success_indicator' => 'Seluruh temuan AMI ditutup, eviden terverifikasi, dan audit berjalan sesuai jadwal.',
            'created_by' => $user->id,
        ]);

        $monthly = OkrPlan::create([
            'okr_key_result_id' => $keyResult->id,
            'okr_unit_id' => $unit->id,
            'parent_id' => $annual->id,
            'owner_id' => $user->id,
            'level' => 'monthly',
            'title' => 'Monitoring sasaran mutu dan kelengkapan eviden unit',
            'description' => 'Rapat monitoring OKR, review dokumen mutu, tindak lanjut AMI, dan pengukuran kepatuhan SOP minimal 90%.',
            'starts_at' => now()->startOfMonth(),
            'ends_at' => now()->endOfMonth(),
            'target_value' => 100,
            'metric_unit' => '% agenda selesai',
            'success_indicator' => 'Rapat terlaksana, dokumen ditinjau, dan progres tindak lanjut tercatat.',
            'created_by' => $user->id,
        ]);

        OkrPlan::create([
            'okr_key_result_id' => $keyResult->id,
            'okr_unit_id' => $unit->id,
            'parent_id' => $monthly->id,
            'owner_id' => $user->id,
            'level' => 'weekly',
            'title' => 'Monitoring implementasi SOP dan eviden minggu berjalan',
            'description' => 'Memantau 1-2 unit, memastikan eviden terunggah, menindaklanjuti temuan, dan memperbarui dashboard.',
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
            'target_value' => 5,
            'metric_unit' => 'aktivitas',
            'success_indicator' => 'Lima aktivitas monitoring mingguan selesai dan terdokumentasi.',
            'created_by' => $user->id,
        ]);
    }

    private function units(): array
    {
        return [
            ['code' => 'QMR', 'name' => 'QMR', 'role_names' => ['Kepala Sekolah', 'Super Admin']],
            ['code' => 'KURIKULUM', 'name' => 'Kurikulum', 'role_names' => ['Kurikulum', 'Kaprodi', 'Guru Kelas', 'Wali Kelas']],
            ['code' => 'KESISWAAN', 'name' => 'Kesiswaan', 'role_names' => ['Waka Kesiswaan', 'Guru BK', 'Guru Piket', 'Petugas UKS']],
            ['code' => 'HUBIN-SINERGI', 'name' => 'Hubin Sinergi UP dan Alumni', 'role_names' => ['Koordinator Prakerin']],
            ['code' => 'HUBIN-PPDB', 'name' => 'Hubin PPDB', 'role_names' => ['Operator']],
            ['code' => 'HUMAN-CAPITAL', 'name' => 'Human Capital', 'role_names' => ['KAUR SDM']],
            ['code' => 'KEUANGAN', 'name' => 'Keuangan', 'role_names' => ['Tata Usaha', 'Kantin']],
            ['code' => 'SARPRAS', 'name' => 'Sarana dan Prasarana', 'role_names' => ['Security']],
            ['code' => 'IT', 'name' => 'IT', 'role_names' => ['Super Admin']],
        ];
    }

    private function objectives(): array
    {
        return [
            [
                'code' => 'O1',
                'title' => 'Menghasilkan Lulusan dengan Kompetensi IT Terdepan',
                'key_results' => [
                    $this->kr('KR 1.1', 'Sertifikasi Profesi', '90% siswa kelas XII memiliki sertifikasi IT internasional (Cisco, Microsoft, Oracle).', 'percentage', 90, '%'),
                    $this->kr('KR 1.2', 'Kompetensi Programming', '95% lulusan menguasai minimal 3 bahasa pemrograman dan framework modern.', 'percentage', 95, '%'),
                    $this->kr('KR 1.3', 'Portfolio Digital', '100% lulusan memiliki portfolio online dengan minimal 5 proyek aplikasi.', 'percentage', 100, '%'),
                ],
            ],
            [
                'code' => 'O2',
                'title' => 'Membangun Jiwa Entrepreneur di Ekonomi Kreatif Digital',
                'key_results' => [
                    $this->kr('KR 2.1', 'Startup Siswa', '5 startup digital berbasis teknologi dibentuk oleh siswa dengan pendampingan komunitas atau industri.', 'number', 5, 'startup'),
                    $this->kr('KR 2.2', 'Digital Marketing', '85% siswa menguasai digital marketing dan memiliki sertifikasi Google/Facebook.', 'percentage', 85, '%'),
                    $this->kr('KR 2.3', 'Omzet Kolektif', 'Total omzet kolektif startup siswa mencapai Rp2 miliar dalam setahun.', 'currency', 2000000000, 'rupiah'),
                ],
            ],
            [
                'code' => 'O3',
                'title' => 'Membentuk Lulusan Berkarakter Religius dan Berintegritas',
                'key_results' => [
                    $this->kr('KR 3.1', 'Kegiatan Religius', '100% siswa aktif dalam kegiatan keagamaan dan memiliki hafalan Al-Quran minimal 3 juz.', 'percentage', 100, '%'),
                    $this->kr('KR 3.2', 'Etika Profesi IT', '95% lulusan lulus sertifikasi etika profesi IT dan cyber ethics.', 'percentage', 95, '%'),
                    $this->kr('KR 3.3', 'Kepemimpinan', '80% lulusan memiliki pengalaman kepemimpinan dalam organisasi atau proyek.', 'percentage', 80, '%'),
                ],
            ],
            [
                'code' => 'O4',
                'title' => 'Membangun Ekosistem Kemitraan dengan Industri Telkom dan IT',
                'key_results' => [
                    $this->kr('KR 4.1', 'Magang Industri', '100% siswa kelas XII menyelesaikan magang di perusahaan IT/Telkom minimal 6 bulan.', 'percentage', 100, '%'),
                    $this->kr('KR 4.2', 'Job Placement', '85% lulusan langsung bekerja di industri IT dengan gaji minimal UMR + 50%.', 'percentage', 85, '%'),
                    $this->kr('KR 4.3', 'Program Pemerintah', 'Memanfaatkan 2 program bantuan pemerintah: SMK Pusat Keunggulan dan Teaching Factory.', 'number', 2, 'program'),
                    $this->kr('KR 4.4', 'Continuing Education', '15% lulusan melanjutkan ke perguruan tinggi dengan beasiswa prestasi.', 'percentage', 15, '%'),
                ],
            ],
            [
                'code' => 'O5',
                'title' => 'Menghasilkan Inovasi dan Prestasi Tingkat Nasional/Internasional',
                'key_results' => [
                    $this->kr('KR 5.1', 'Kompetisi Nasional', 'Meraih minimal 10 juara dalam kompetisi IT/Programming tingkat nasional.', 'number', 10, 'juara'),
                    $this->kr('KR 5.2', 'Inovasi Produk', '25 produk inovasi teknologi dihasilkan siswa dan mendapat HKI/Paten.', 'number', 25, 'produk'),
                    $this->kr('KR 5.3', 'Akreditasi dan Sertifikasi', 'Meraih akreditasi A+ dan sertifikasi ISO 9001:2015 untuk sistem manajemen mutu.', 'boolean', 1, 'tercapai'),
                ],
            ],
        ];
    }

    private function kr(
        string $code,
        string $title,
        string $description,
        string $metricType,
        float $target,
        string $unit
    ): array {
        return [
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'metric_type' => $metricType,
            'baseline_value' => 0,
            'target_value' => $target,
            'metric_unit' => $unit,
            'weight' => 1,
        ];
    }
}
