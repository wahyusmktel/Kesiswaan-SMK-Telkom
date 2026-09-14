<?php

namespace Database\Seeders;

use App\Models\OkrKeyResult;
use App\Models\OkrPeriod;
use App\Models\OkrPlan;
use App\Models\OkrUnit;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ItOkrProgramSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SchoolOkrObjectiveSeeder::class);

        $period = OkrPeriod::query()
            ->where('status', 'active')
            ->latest('id')
            ->first()
            ?? OkrPeriod::query()->latest('id')->first();

        if (! $period) {
            throw new RuntimeException('Periode OKR belum tersedia. Buka halaman Manajemen OKR terlebih dahulu untuk membuat periode aktif.');
        }

        DB::transaction(function () use ($period) {
            $unit = OkrUnit::query()->firstOrCreate(
                ['code' => 'IT'],
                [
                    'name' => 'IT',
                    'role_names' => ['IT', 'Super Admin'],
                    'sort_order' => 9,
                    'is_active' => true,
                ]
            );
            $unit->update([
                'name' => 'IT',
                'role_names' => collect($unit->role_names ?? [])->merge(['IT', 'Super Admin'])->unique()->values()->all(),
                'is_active' => true,
            ]);

            $owner = OkrPlan::query()
                ->where('okr_unit_id', $unit->id)
                ->whereNotNull('owner_id')
                ->oldest('id')
                ->value('owner_id');
            $owner ??= User::query()->whereHas('roles', fn ($query) => $query->where('name', 'IT'))->oldest('id')->value('id');
            $owner ??= User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Super Admin'))->oldest('id')->value('id');

            $schoolKeyResults = OkrKeyResult::query()
                ->whereHas('objective', fn ($query) => $query->where('okr_period_id', $period->id))
                ->get()
                ->keyBy('code');

            $annualPlans = [];
            $annualMayBeRolledUp = [];
            foreach ($this->annualPrograms() as $code => $program) {
                $schoolKeyResult = $schoolKeyResults->get($program['school_kr']);
                if (! $schoolKeyResult) {
                    throw new RuntimeException("Key Result Sekolah {$program['school_kr']} untuk {$code} tidak ditemukan.");
                }

                // Adopsi target manual lama berdasarkan nama fokus agar seeder tidak
                // menggandakan data yang sudah pernah dibuat melalui aplikasi.
                $annual = OkrPlan::query()
                    ->where('okr_unit_id', $unit->id)
                    ->whereNull('parent_id')
                    ->where('level', 'annual')
                    ->whereIn('title', [$program['focus'], "{$code} · {$program['focus']}"])
                    ->first()
                    ?? new OkrPlan;
                $wasNew = ! $annual->exists;
                $canInitializeProgress = $wasNew
                    || ($annual->status === 'not_started' && (float) $annual->progress_percent === 0.0);

                $annual->fill([
                    'okr_key_result_id' => $schoolKeyResult->id,
                    'okr_unit_id' => $unit->id,
                    'parent_id' => null,
                    'owner_id' => $annual->owner_id ?: $owner,
                    'level' => 'annual',
                    'title' => Str::limit("{$code} · {$program['focus']}", 255, ''),
                    'description' => implode("\n\n", [
                        "Relasi KR Sekolah: {$program['school_kr']} · {$schoolKeyResult->title}",
                        "Alasan keselarasan: {$program['alignment']}",
                        'PIC: Kaur IT',
                        'Sumber: Program Kerja Unit IT TP 2026/2027.',
                    ]),
                    'starts_at' => '2026-06-01',
                    'ends_at' => '2027-07-31',
                    'target_value' => 100,
                    'metric_unit' => '% program bulanan',
                    'weight' => 1,
                    'success_indicator' => $program['indicator'],
                    'created_by' => $annual->created_by ?: $owner,
                ]);

                if ($wasNew) {
                    $annual->fill([
                        'current_value' => 0,
                        'progress_percent' => 0,
                        'status' => 'not_started',
                    ]);
                }
                $annual->save();

                $annualPlans[$code] = $annual;
                $annualMayBeRolledUp[$code] = $canInitializeProgress;
            }

            foreach ($this->monthlyPrograms() as $monthlyProgram) {
                $annual = $annualPlans[$monthlyProgram['code']];
                $month = CarbonImmutable::parse($monthlyProgram['month'].'-01');
                $title = Str::limit("{$monthlyProgram['label']} · {$monthlyProgram['code']} · {$monthlyProgram['program']}", 255, '');
                $monthly = OkrPlan::query()->firstOrNew([
                    'okr_unit_id' => $unit->id,
                    'parent_id' => $annual->id,
                    'level' => 'monthly',
                    'title' => $title,
                ]);
                $wasNew = ! $monthly->exists;
                $monthly->fill([
                    'okr_key_result_id' => $annual->okr_key_result_id,
                    'owner_id' => $monthly->owner_id ?: $owner,
                    'description' => implode("\n\n", array_filter([
                        "Fokus: {$this->annualPrograms()[$monthlyProgram['code']]['focus']}",
                        'PIC: Kaur IT',
                        "Unit koordinasi: {$monthlyProgram['coordination']}",
                        $monthlyProgram['evidence'] ? "Bukti kerja sumber: {$monthlyProgram['evidence']}" : null,
                        'Sumber sheet: '.$monthlyProgram['label'].'.',
                    ])),
                    'starts_at' => $month->startOfMonth(),
                    'ends_at' => $month->endOfMonth(),
                    'target_value' => 100,
                    'metric_unit' => '% output',
                    'weight' => 1,
                    'success_indicator' => 'Program bulanan selesai, hasil terverifikasi, dan bukti terdokumentasi.',
                    'created_by' => $monthly->created_by ?: $owner,
                ]);
                if ($wasNew) {
                    $monthly->fill($this->initialProgress($monthlyProgram));
                }
                $monthly->save();

                foreach ($this->weeklyStages() as $weekNumber => $stage) {
                    $detailKey = "{$monthlyProgram['month']}|{$monthlyProgram['code']}|{$weekNumber}";
                    $detail = $this->weeklyHistory()[$detailKey] ?? [];
                    $firstDay = $month->startOfMonth();
                    $firstMonday = $firstDay->dayOfWeek === CarbonInterface::MONDAY
                        ? $firstDay
                        : $firstDay->next(CarbonInterface::MONDAY);
                    $weekStart = $firstMonday->addWeeks($weekNumber - 1);
                    $weeklyTitle = Str::limit(
                        "{$monthlyProgram['label']} · {$monthlyProgram['code']} · Minggu {$weekNumber} · {$stage['name']}",
                        255,
                        ''
                    );
                    $weekly = OkrPlan::query()->firstOrNew([
                        'okr_unit_id' => $unit->id,
                        'parent_id' => $monthly->id,
                        'level' => 'weekly',
                        'title' => $weeklyTitle,
                    ]);
                    $weeklyWasNew = ! $weekly->exists;
                    $agenda = $weekNumber === 1
                        ? 'Persiapan dan pendataan: konfirmasi ruang lingkup, perangkat/data, jadwal, serta unit terkait untuk '.Str::lower($monthlyProgram['program']).'.'
                        : $stage['agenda'];
                    $weekly->fill([
                        'okr_key_result_id' => $annual->okr_key_result_id,
                        'owner_id' => $weekly->owner_id ?: $owner,
                        'description' => implode("\n\n", array_filter([
                            "Program bulanan: {$monthlyProgram['program']}",
                            "Agenda: {$agenda}",
                            'PIC: Kaur IT',
                            "Unit koordinasi: {$monthlyProgram['coordination']}",
                            isset($detail['evidence']) ? "Bukti kerja sumber: {$detail['evidence']}" : null,
                        ])),
                        'starts_at' => $weekStart,
                        'ends_at' => $weekStart->addDays(4),
                        'target_value' => 100,
                        'metric_unit' => '% agenda',
                        'weight' => 1,
                        'success_indicator' => $stage['target'],
                        'created_by' => $weekly->created_by ?: $owner,
                    ]);
                    if ($weeklyWasNew) {
                        $weekly->fill($this->initialProgress($detail));
                    }
                    $weekly->save();
                }
            }

            // Target lama yang masih 0% boleh menerima progres awal dari sumber.
            // Setelah pengguna memperbarui progres, rerun seeder tidak mengubahnya.
            foreach ($annualPlans as $code => $annual) {
                if (! $annualMayBeRolledUp[$code]) {
                    continue;
                }

                $progress = round((float) $annual->children()->avg('progress_percent'), 2);
                $annual->update([
                    'current_value' => $progress,
                    'progress_percent' => $progress,
                    'status' => $this->statusFromProgress($progress),
                ]);
            }
        });

        $this->command?->info('OKR Unit IT selesai: 11 fokus tahunan, 22 program bulanan, dan 88 agenda mingguan tersinkron.');
    }

    private function initialProgress(array $source): array
    {
        $status = $source['status'] ?? 'Belum Mulai';
        $progress = match (Str::lower($status)) {
            'selesai' => 100,
            'berjalan' => 50,
            default => 0,
        };

        return [
            'current_value' => $progress,
            'progress_percent' => $progress,
            'status' => $this->statusFromProgress($progress),
            'latest_evaluation' => collect([
                isset($source['actual']) ? 'Realisasi: '.$source['actual'] : null,
                isset($source['evidence']) ? 'Bukti kerja: '.$source['evidence'] : null,
                isset($source['follow_up']) ? 'Tindak lanjut: '.$source['follow_up'] : null,
            ])->filter()->implode("\n\n") ?: null,
            'completed_at' => $progress >= 100 ? now() : null,
        ];
    }

    private function statusFromProgress(float $progress): string
    {
        return match (true) {
            $progress >= 100 => 'completed',
            $progress > 0 => 'in_progress',
            default => 'not_started',
        };
    }

    private function annualPrograms(): array
    {
        return [
            'KR-IT.1' => [
                'focus' => 'Pengembangan aplikasi inventaris sekolah berbasis web',
                'school_kr' => 'KR 3.2',
                'alignment' => 'Aplikasi inventaris merupakan produk digital operasional yang dikembangkan dan digunakan di lingkungan sekolah.',
                'indicator' => 'Aplikasi inventaris siap digunakan, data awal termigrasi, dan alur aset dapat ditelusuri.',
            ],
            'KR-IT.2' => [
                'focus' => 'Penomoran dan pelabelan aset sesuai standar Telkom Schools',
                'school_kr' => 'KR 4.2',
                'alignment' => 'Identifikasi aset yang tertib mendukung lingkungan belajar yang aman, nyaman, dan mudah diawasi.',
                'indicator' => 'Aset sasaran memiliki nomor dan label standar yang telah direkonsiliasi.',
            ],
            'KR-IT.3' => [
                'focus' => 'Penyusunan dan penerapan SOP penggunaan serta peminjaman aset',
                'school_kr' => 'KR 4.2',
                'alignment' => 'SOP penggunaan dan peminjaman mengurangi risiko kehilangan atau kerusakan serta meningkatkan kenyamanan layanan.',
                'indicator' => 'SOP disahkan, disosialisasikan, dan diterapkan pada transaksi penggunaan aset.',
            ],
            'KR-IT.4' => [
                'focus' => 'Pemerataan cakupan dan kualitas Wi-Fi sekolah',
                'school_kr' => 'KR 1.2',
                'alignment' => 'Konektivitas yang merata menjadi prasyarat pembelajaran dan sertifikasi kompetensi berbasis industri.',
                'indicator' => 'Blank spot terpetakan, cakupan diperbaiki, dan hasil uji beban terdokumentasi.',
            ],
            'KR-IT.5' => [
                'focus' => 'Perawatan rutin dan stabilitas jaringan sekolah',
                'school_kr' => 'KR 1.2',
                'alignment' => 'Jaringan yang stabil menjaga keberlangsungan pembelajaran dan asesmen sertifikasi kompetensi industri.',
                'indicator' => 'Perangkat jaringan terawat, konfigurasi dicadangkan, dan gangguan prioritas ditindaklanjuti.',
            ],
            'KR-IT.6' => [
                'focus' => 'Perawatan dan keandalan jaringan CCTV',
                'school_kr' => 'KR 4.2',
                'alignment' => 'CCTV yang andal mendukung rasa aman dan kenyamanan warga sekolah.',
                'indicator' => 'Titik kamera, perekaman, playback, dan retensi berfungsi serta terdokumentasi.',
            ],
            'KR-IT.7' => [
                'focus' => 'Penyusunan roadmap infrastruktur IT 2027–2031',
                'school_kr' => 'KR 1.3',
                'alignment' => 'Roadmap memastikan infrastruktur sekolah berkembang selaras dengan kebutuhan kurikulum dan standar mitra industri.',
                'indicator' => 'Roadmap lima tahun memuat gap, arsitektur target, prioritas, biaya, dan pengesahan manajemen.',
            ],
            'KR-IT.8' => [
                'focus' => 'Keamanan sistem, pencadangan, dan pemulihan data',
                'school_kr' => 'KR 4.2',
                'alignment' => 'Perlindungan akses dan pemulihan data menjaga layanan digital sekolah tetap aman dan tersedia.',
                'indicator' => 'Sistem kritis terinventarisasi, akses ditinjau, backup berjalan, dan pemulihan diuji.',
            ],
            'KR-IT.9' => [
                'focus' => 'Layanan helpdesk dan dokumentasi layanan IT',
                'school_kr' => 'KR 4.2',
                'alignment' => 'Helpdesk dengan SLA mempercepat penyelesaian gangguan yang memengaruhi kenyamanan kegiatan sekolah.',
                'indicator' => 'Kanal helpdesk, SLA, basis pengetahuan, dan evaluasi tren layanan tersedia.',
            ],
            'KR-IT.10' => [
                'focus' => 'Optimalisasi pemanfaatan SISFO untuk operasional sekolah',
                'school_kr' => 'KR 3.2',
                'alignment' => 'Pengembangan dan adopsi SISFO memperkuat pemanfaatan produk digital karya ekosistem sekolah.',
                'indicator' => 'Modul prioritas dipakai unit sasaran dan hasil pendampingan serta evaluasi terdokumentasi.',
            ],
            'KR-IT.11' => [
                'focus' => 'Maintenance dan development website resmi serta landing page SPMB',
                'school_kr' => 'KR 3.2',
                'alignment' => 'Website dan landing page merupakan produk digital sekolah yang mendukung layanan informasi dan penerimaan siswa.',
                'indicator' => 'Website dan landing page lulus pengujian, optimal, terbarui, dan berhasil dideploy.',
            ],
        ];
    }

    private function monthlyPrograms(): array
    {
        return [
            $this->month('2026-06', 'Jun 2026', 'KR-IT.5', 'Preventive maintenance dan pembaruan konfigurasi/firmware switch, access point, dan router', 'Sarana Prasarana, Laboratorium, Penyedia Layanan Internet', 'Selesai', 'Preventive maintenance jaringan telah dilaksanakan. Konfigurasi dan firmware perangkat jaringan diperbarui mulai dari switch, access point, hingga router; fungsi konektivitas diuji setelah pembaruan.', 'Checklist maintenance; tangkapan layar versi firmware dan konfigurasi; backup konfigurasi; foto perangkat/rack; catatan hasil pengujian koneksi.', 'Monitoring stabilitas pascapembaruan, menyimpan backup konfigurasi terpusat, dan memperbarui dokumentasi topologi jaringan.'),
            $this->month('2026-07', 'Jul 2026', 'KR-IT.5', 'Monitoring pascaperawatan, optimasi konfigurasi, backup konfigurasi, dan dokumentasi topologi', 'Sarana Prasarana, Laboratorium, Penyedia Layanan Internet', 'Selesai', 'Monitoring pascaperawatan dilaksanakan; konektivitas perangkat inti, access point, dan router diperiksa serta konfigurasi yang diperlukan dioptimalkan.', 'Log monitoring; hasil uji koneksi; backup konfigurasi final; dokumentasi topologi dan daftar perangkat yang diperbarui.', 'Melanjutkan monitoring rutin bulanan dan mencatat setiap gangguan melalui helpdesk/log jaringan.'),
            $this->month('2026-08', 'Agu 2026', 'KR-IT.6', 'Audit, pembersihan, pengujian, dan perbaikan jaringan CCTV tahap 1', 'Sarana Prasarana, Keamanan, Manajemen', 'Berjalan', 'Minggu 1: inventarisasi titik kamera dan pemeriksaan awal fungsi kamera/NVR telah dilakukan. Minggu 2: pembersihan, pengecekan koneksi, sudut kamera, kualitas gambar, dan rekaman sedang berjalan.', 'Daftar titik CCTV; checklist pemeriksaan awal; foto kondisi kamera; tangkapan layar status kamera/NVR; daftar temuan sementara.', 'Menyelesaikan pemeriksaan Minggu 2, memperbaiki titik bermasalah, menguji playback/retensi rekaman, dan menyusun laporan akhir pada September.'),
            $this->month('2026-09', 'Sep 2026', 'KR-IT.6', 'Penyelesaian perbaikan CCTV, uji rekaman/retensi, dan dokumentasi', 'Sarana Prasarana, Keamanan, Manajemen'),
            $this->month('2026-09', 'Sep 2026', 'KR-IT.11', 'Audit teknis dan perencanaan pengembangan website serta landing page SPMB', 'Humas, Panitia SPMB, Manajemen, unit pemilik konten'),
            $this->month('2026-10', 'Okt 2026', 'KR-IT.11', 'Development, pengujian, optimasi, dan deployment website/landing page SPMB', 'Humas, Panitia SPMB, Manajemen, unit pemilik konten'),
            $this->month('2026-10', 'Okt 2026', 'KR-IT.2', 'Telaah standar, verifikasi aset, dan persiapan skema penomoran serta label', 'Sarana Prasarana, Laboratorium, Keuangan'),
            $this->month('2026-11', 'Nov 2026', 'KR-IT.2', 'Pencetakan, pemasangan, rekonsiliasi, dan audit label aset', 'Sarana Prasarana, Laboratorium, Keuangan'),
            $this->month('2026-11', 'Nov 2026', 'KR-IT.1', 'Analisis kebutuhan, alur bisnis, rancangan basis data, dan prototipe inventaris', 'Sarana Prasarana, Laboratorium, Keuangan, Manajemen'),
            $this->month('2026-12', 'Des 2026', 'KR-IT.1', 'Development modul inti, pengujian, migrasi awal, dan perbaikan aplikasi inventaris', 'Sarana Prasarana, Laboratorium, Keuangan, Manajemen'),
            $this->month('2027-01', 'Jan 2027', 'KR-IT.3', 'Pemetaan proses dan penyusunan draf SOP penggunaan, peminjaman, kerusakan, dan perawatan', 'Sarana Prasarana, Laboratorium, Manajemen, Tata Usaha'),
            $this->month('2027-02', 'Feb 2027', 'KR-IT.3', 'Review, pengesahan, sosialisasi, dan implementasi SOP', 'Sarana Prasarana, Laboratorium, Manajemen, Tata Usaha'),
            $this->month('2027-02', 'Feb 2027', 'KR-IT.7', 'Audit kondisi infrastruktur dan pengumpulan kebutuhan lima tahun', 'Manajemen, Sarana Prasarana, Laboratorium, Kurikulum, Keuangan, Kaprodi'),
            $this->month('2027-03', 'Mar 2027', 'KR-IT.7', 'Gap analysis, arsitektur target, estimasi biaya, finalisasi, dan pengesahan roadmap', 'Manajemen, Sarana Prasarana, Laboratorium, Kurikulum, Keuangan, Kaprodi'),
            $this->month('2027-03', 'Mar 2027', 'KR-IT.8', 'Inventarisasi sistem kritis, review akses, MFA, dan kebijakan backup', 'Manajemen, Tata Usaha, Kurikulum, Keuangan, pemilik sistem'),
            $this->month('2027-04', 'Apr 2027', 'KR-IT.8', 'Implementasi backup, patching, edukasi keamanan, dan uji pemulihan', 'Manajemen, Tata Usaha, Kurikulum, Keuangan, pemilik sistem'),
            $this->month('2027-04', 'Apr 2027', 'KR-IT.9', 'Penetapan kanal helpdesk, kategori, SLA, dan peluncuran layanan', 'Seluruh Unit'),
            $this->month('2027-05', 'Mei 2027', 'KR-IT.9', 'Monitoring SLA, basis pengetahuan, evaluasi tren, dan perbaikan helpdesk', 'Seluruh Unit'),
            $this->month('2027-05', 'Mei 2027', 'KR-IT.10', 'Pemetaan adopsi SISFO, panduan, dan sosialisasi modul prioritas', 'Kesiswaan, BK, Piket, Kurikulum, SDM/Tata Usaha, Manajemen'),
            $this->month('2027-06', 'Jun 2027', 'KR-IT.10', 'Pelatihan, pendampingan, evaluasi adopsi, dan penyempurnaan SISFO', 'Kesiswaan, BK, Piket, Kurikulum, SDM/Tata Usaha, Manajemen'),
            $this->month('2027-06', 'Jun 2027', 'KR-IT.4', 'Site survey, pemetaan cakupan, dan analisis blank spot Wi-Fi', 'Sarana Prasarana, Laboratorium, Kurikulum'),
            $this->month('2027-07', 'Jul 2027', 'KR-IT.4', 'Optimasi/penambahan access point, uji beban, validasi cakupan, dan dokumentasi', 'Sarana Prasarana, Laboratorium, Kurikulum'),
        ];
    }

    private function month(
        string $month,
        string $label,
        string $code,
        string $program,
        string $coordination,
        string $status = 'Belum Mulai',
        ?string $actual = null,
        ?string $evidence = null,
        ?string $followUp = null
    ): array {
        return compact('month', 'label', 'code', 'program', 'coordination', 'status', 'actual', 'evidence')
            + ['follow_up' => $followUp];
    }

    private function weeklyStages(): array
    {
        return [
            1 => ['name' => 'Persiapan dan pendataan', 'agenda' => '', 'target' => 'Rencana dan data siap'],
            2 => ['name' => 'Pelaksanaan tahap 1', 'agenda' => 'Pelaksanaan tahap 1: kerjakan prioritas utama, catat kondisi awal, perubahan, dan kendala yang ditemukan.', 'target' => 'Pekerjaan prioritas terlaksana'],
            3 => ['name' => 'Pelaksanaan tahap 2 dan verifikasi', 'agenda' => 'Pelaksanaan tahap 2 dan verifikasi: uji fungsi/hasil, lakukan perbaikan, dan validasi bersama unit terkait.', 'target' => 'Hasil diuji dan temuan ditindaklanjuti'],
            4 => ['name' => 'Finalisasi dan laporan', 'agenda' => 'Finalisasi: lengkapi output, bukti kerja, laporan capaian, serta tindak lanjut bulan berikutnya.', 'target' => 'Output selesai dan laporan tersedia'],
        ];
    }

    private function weeklyHistory(): array
    {
        $next = 'Lanjut ke agenda minggu berikutnya dan selesaikan temuan yang masih terbuka.';
        $final = 'Lakukan monitoring hasil, arsipkan dokumen, dan masukkan rekomendasi ke program bulan berikutnya.';

        return [
            '2026-06|KR-IT.5|1' => ['status' => 'Selesai', 'actual' => 'Inventarisasi perangkat dan penyusunan checklist maintenance selesai.', 'evidence' => 'Daftar perangkat; checklist; foto kondisi awal.', 'follow_up' => $next],
            '2026-06|KR-IT.5|2' => ['status' => 'Selesai', 'actual' => 'Pembersihan dan pemeriksaan switch, access point, serta router selesai.', 'evidence' => 'Checklist pemeriksaan; foto perangkat/rack; catatan temuan.', 'follow_up' => $next],
            '2026-06|KR-IT.5|3' => ['status' => 'Selesai', 'actual' => 'Pembaruan konfigurasi dan firmware seluruh perangkat sasaran selesai; koneksi diuji.', 'evidence' => 'Screenshot firmware/konfigurasi; log upgrade; hasil ping dan uji akses.', 'follow_up' => $next],
            '2026-06|KR-IT.5|4' => ['status' => 'Selesai', 'actual' => 'Backup konfigurasi, dokumentasi, dan laporan maintenance diselesaikan.', 'evidence' => 'Backup konfigurasi; laporan maintenance; dokumentasi topologi.', 'follow_up' => $final],
            '2026-07|KR-IT.5|1' => ['status' => 'Selesai', 'actual' => 'Monitoring pascaperawatan Minggu 1 telah dilaksanakan; stabilitas dan konfigurasi perangkat diperiksa.', 'evidence' => 'Log monitoring Minggu 1; hasil uji koneksi; catatan optimasi dan backup konfigurasi.', 'follow_up' => $next],
            '2026-07|KR-IT.5|2' => ['status' => 'Selesai', 'actual' => 'Monitoring pascaperawatan Minggu 2 telah dilaksanakan; stabilitas dan konfigurasi perangkat diperiksa.', 'evidence' => 'Log monitoring Minggu 2; hasil uji koneksi; catatan optimasi dan backup konfigurasi.', 'follow_up' => $next],
            '2026-07|KR-IT.5|3' => ['status' => 'Selesai', 'actual' => 'Monitoring pascaperawatan Minggu 3 telah dilaksanakan; stabilitas dan konfigurasi perangkat diperiksa.', 'evidence' => 'Log monitoring Minggu 3; hasil uji koneksi; catatan optimasi dan backup konfigurasi.', 'follow_up' => $next],
            '2026-07|KR-IT.5|4' => ['status' => 'Selesai', 'actual' => 'Monitoring pascaperawatan Minggu 4 telah dilaksanakan; stabilitas dan konfigurasi perangkat diperiksa.', 'evidence' => 'Log monitoring Minggu 4; hasil uji koneksi; catatan optimasi dan backup konfigurasi.', 'follow_up' => $final],
            '2026-08|KR-IT.6|1' => ['status' => 'Selesai', 'actual' => 'Inventarisasi titik CCTV dan pemeriksaan awal status kamera/NVR selesai.', 'evidence' => 'Daftar titik CCTV; checklist awal; screenshot status kamera/NVR; foto kondisi.', 'follow_up' => 'Lanjut pembersihan, pemeriksaan koneksi, sudut, kualitas gambar, dan rekaman.'],
            '2026-08|KR-IT.6|2' => ['status' => 'Berjalan', 'actual' => 'Pembersihan kamera, pengecekan koneksi, sudut kamera, kualitas gambar, dan fungsi rekaman sedang dilaksanakan.', 'evidence' => 'Foto progres; checklist sementara; daftar temuan kamera/koneksi bermasalah.', 'follow_up' => 'Selesaikan pemeriksaan, lakukan perbaikan titik bermasalah, lalu uji playback dan retensi.'],
        ];
    }
}
