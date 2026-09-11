<?php

namespace App\Services;

use App\Models\AbsensiGuru;
use App\Models\DigitalDocument;
use App\Models\GuruIzin;
use App\Models\User;
use App\Models\UserDigitalSignature;
use App\Notifications\PengajuanIzinGuruNotification;
use App\Notifications\TeacherAbsenceStudentNotification;
use Illuminate\Support\Facades\DB;

class PicketTeacherLeaveDecisionService
{
    public function __construct(private readonly TelegramLeaveNotificationService $telegramNotifications) {}

    public function approve(GuruIzin $izin, User $actor): array
    {
        $result = DB::transaction(function () use ($izin, $actor) {
            $izin = GuruIzin::query()->with(['guru.user', 'jadwals.rombel'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless(in_array($izin->kategori_penyetujuan, [GuruIzin::CATEGORY_SCHOOL, GuruIzin::CATEGORY_OUTSIDE], true), 409, 'Kategori izin ini tidak memerlukan persetujuan Piket.');
            abort_unless($izin->status_piket === 'menunggu', 409, 'Tahap Piket sudah diputuskan.');

            $data = ['status_piket' => 'disetujui', 'piket_id' => $actor->id, 'piket_at' => now()];
            $isSchool = $izin->kategori_penyetujuan === GuruIzin::CATEGORY_SCHOOL;
            if ($isSchool) {
                $data += [
                    'status_kurikulum' => 'disetujui',
                    'status_sdm' => 'disetujui',
                    'kurikulum_id' => $actor->id,
                    'sdm_id' => $actor->id,
                    'kurikulum_at' => now(),
                    'sdm_at' => now(),
                    'status_kepala_sekolah' => 'tidak_diperlukan',
                ];
            }
            $izin->update($data);
            $this->autoSign($izin, $actor);

            if ($isSchool) {
                foreach ($izin->jadwals as $jadwal) {
                    AbsensiGuru::updateOrCreate(
                        ['jadwal_pelajaran_id' => $jadwal->id, 'tanggal' => $izin->tanggal_mulai],
                        [
                            'status' => 'izin',
                            'keterangan' => 'Izin Guru (Lingkungan Sekolah): '.$izin->jenis_izin.' ('.$izin->deskripsi.')',
                            'waktu_absen' => now(),
                            'dicatat_oleh' => $actor->id,
                        ],
                    );
                    foreach ($jadwal->rombel?->siswa()->with('user')->get() ?? [] as $student) {
                        $student->user?->notify(new TeacherAbsenceStudentNotification($izin, $jadwal));
                    }
                }
            } else {
                // Notifikasi tahap berikutnya dikirim setelah transaksi berhasil.
            }

            return [$izin, $isSchool];
        });

        [$izin, $isSchool] = $result;
        $progress = $isSchool
            ? 'Izin Anda telah disetujui Guru Piket dan dinyatakan selesai.'
            : 'Izin Anda telah disetujui Guru Piket dan diteruskan ke Waka Kurikulum.';
        $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', $progress, route('guru.izin.index')));
        $this->telegramNotifications->notifyApplicant($izin, $progress);
        if (! $isSchool) {
            $this->telegramNotifications->notifyRoleApprovers($izin, 'kurikulum');
        }

        return ['izin' => $izin, 'message' => $progress, 'complete' => $isSchool];
    }

    public function reject(GuruIzin $izin, User $actor, string $note): array
    {
        $izin = DB::transaction(function () use ($izin, $actor, $note) {
            $izin = GuruIzin::query()->with('guru.user')->lockForUpdate()->findOrFail($izin->id);
            abort_unless(in_array($izin->kategori_penyetujuan, [GuruIzin::CATEGORY_SCHOOL, GuruIzin::CATEGORY_OUTSIDE], true), 409, 'Kategori izin ini tidak memerlukan persetujuan Piket.');
            abort_unless($izin->status_piket === 'menunggu', 409, 'Tahap Piket sudah diputuskan.');
            $izin->update([
                'status_piket' => 'ditolak',
                'piket_id' => $actor->id,
                'piket_at' => now(),
                'catatan_piket' => $note,
            ]);

            return $izin;
        });

        $message = 'Permohonan izin Anda ditolak oleh Guru Piket. Catatan: '.$note;
        $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', $message, route('guru.izin.index')));
        $this->telegramNotifications->notifyApplicant($izin, $message);

        return ['izin' => $izin, 'message' => $message];
    }

    public function approveKurikulum(GuruIzin $izin, User $actor): array
    {
        $izin = DB::transaction(function () use ($izin, $actor) {
            $izin = GuruIzin::query()->with('guru.user')->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->kategori_penyetujuan === GuruIzin::CATEGORY_OUTSIDE && $izin->status_piket === 'disetujui' && $izin->status_kurikulum === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kurikulum.');
            $izin->update(['status_kurikulum' => 'disetujui', 'kurikulum_id' => $actor->id, 'kurikulum_at' => now()]);
            $this->autoSign($izin, $actor, 'IZIN_GURU_KURIKULUM');

            return $izin;
        });
        $message = 'Izin Anda telah disetujui Waka Kurikulum dan diteruskan ke KAUR SDM.';
        $this->notifyApplicant($izin, $message);
        $this->telegramNotifications->notifyRoleApprovers($izin, 'sdm');

        return ['izin' => $izin, 'message' => $message];
    }

    public function rejectKurikulum(GuruIzin $izin, User $actor, string $note): array
    {
        $izin = DB::transaction(function () use ($izin, $actor, $note) {
            $izin = GuruIzin::query()->with('guru.user')->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->kategori_penyetujuan === GuruIzin::CATEGORY_OUTSIDE && $izin->status_piket === 'disetujui' && $izin->status_kurikulum === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kurikulum.');
            $izin->update(['status_kurikulum' => 'ditolak', 'kurikulum_id' => $actor->id, 'kurikulum_at' => now(), 'catatan_kurikulum' => $note]);

            return $izin;
        });
        $message = 'Permohonan izin Anda ditolak oleh Waka Kurikulum. Catatan: '.$note;
        $this->notifyApplicant($izin, $message);

        return ['izin' => $izin, 'message' => $message];
    }

    public function approveSdm(GuruIzin $izin, User $actor): array
    {
        [$izin, $requiresHeadmaster] = DB::transaction(function () use ($izin, $actor) {
            $izin = GuruIzin::query()->with(['guru.user', 'guru.dapodikGuru', 'jadwals.rombel.siswa.user'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless(in_array($izin->kategori_penyetujuan, [GuruIzin::CATEGORY_OUTSIDE, GuruIzin::CATEGORY_ABSENT, GuruIzin::CATEGORY_LATE], true) || ($izin->kategori_penyetujuan === GuruIzin::CATEGORY_SCHOOL && $izin->guru?->is_tpa), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
            abort_unless($izin->status_kurikulum === 'disetujui' && $izin->status_sdm === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan SDM.');
            $requiresHeadmaster = $izin->requiresHeadmasterApproval();
            $izin->update([
                'status_sdm' => 'disetujui',
                'status_kepala_sekolah' => $requiresHeadmaster ? 'menunggu' : 'tidak_diperlukan',
                'sdm_id' => $actor->id,
                'sdm_at' => now(),
            ]);
            $this->autoSign($izin, $actor, 'IZIN_GURU_SDM');
            if (! $requiresHeadmaster) {
                $this->finalizeAttendance($izin, $actor);
            }

            return [$izin, $requiresHeadmaster];
        });

        $message = $requiresHeadmaster
            ? 'Izin Anda telah disetujui KAUR SDM dan menunggu persetujuan akhir Kepala Sekolah.'
            : 'Izin Anda telah disetujui sepenuhnya oleh KAUR SDM.';
        $this->notifyApplicant($izin, $message);
        if ($requiresHeadmaster) {
            $this->telegramNotifications->notifyRoleApprovers($izin, 'kepsek');
        }

        return ['izin' => $izin, 'message' => $message, 'requires_headmaster' => $requiresHeadmaster];
    }

    public function rejectSdm(GuruIzin $izin, User $actor, string $note): array
    {
        $izin = DB::transaction(function () use ($izin, $actor, $note) {
            $izin = GuruIzin::query()->with('guru.user')->lockForUpdate()->findOrFail($izin->id);
            abort_unless(in_array($izin->kategori_penyetujuan, [GuruIzin::CATEGORY_OUTSIDE, GuruIzin::CATEGORY_ABSENT, GuruIzin::CATEGORY_LATE], true) || ($izin->kategori_penyetujuan === GuruIzin::CATEGORY_SCHOOL && $izin->guru?->is_tpa), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
            abort_unless($izin->status_kurikulum === 'disetujui' && $izin->status_sdm === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan SDM.');
            $izin->update(['status_sdm' => 'ditolak', 'sdm_id' => $actor->id, 'sdm_at' => now(), 'catatan_sdm' => $note]);

            return $izin;
        });
        $message = 'Permohonan izin Anda ditolak oleh KAUR SDM. Catatan: '.$note;
        $this->notifyApplicant($izin, $message);

        return ['izin' => $izin, 'message' => $message];
    }

    public function approveHeadmaster(GuruIzin $izin, User $actor): array
    {
        $izin = DB::transaction(function () use ($izin, $actor) {
            $izin = GuruIzin::query()->with(['guru.user', 'guru.dapodikGuru', 'jadwals.rombel.siswa.user'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->status_sdm === 'disetujui' && $izin->status_kepala_sekolah === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kepala Sekolah.');
            abort_unless($izin->requiresHeadmasterApproval(), 409, 'Persetujuan Kepala Sekolah tidak diperlukan untuk izin ini.');
            $izin->update(['status_kepala_sekolah' => 'disetujui', 'kepala_sekolah_id' => $actor->id, 'kepala_sekolah_at' => now(), 'catatan_kepala_sekolah' => null]);
            $this->finalizeAttendance($izin, $actor);

            return $izin;
        });
        $message = 'Permohonan izin Anda telah disetujui sepenuhnya oleh Kepala Sekolah.';
        $this->notifyApplicant($izin, $message);

        return ['izin' => $izin, 'message' => $message];
    }

    public function rejectHeadmaster(GuruIzin $izin, User $actor, string $note): array
    {
        $izin = DB::transaction(function () use ($izin, $actor, $note) {
            $izin = GuruIzin::query()->with(['guru.user', 'guru.dapodikGuru'])->lockForUpdate()->findOrFail($izin->id);
            abort_unless($izin->status_sdm === 'disetujui' && $izin->status_kepala_sekolah === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan Kepala Sekolah.');
            abort_unless($izin->requiresHeadmasterApproval(), 409, 'Persetujuan Kepala Sekolah tidak diperlukan untuk izin ini.');
            $izin->update(['status_kepala_sekolah' => 'ditolak', 'kepala_sekolah_id' => $actor->id, 'kepala_sekolah_at' => now(), 'catatan_kepala_sekolah' => $note]);

            return $izin;
        });
        $message = 'Permohonan izin Anda ditolak oleh Kepala Sekolah. Catatan: '.$note;
        $this->notifyApplicant($izin, $message);

        return ['izin' => $izin, 'message' => $message];
    }

    private function notifyApplicant(GuruIzin $izin, string $message): void
    {
        $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', $message, route('guru.izin.index')));
        $this->telegramNotifications->notifyApplicant($izin, $message);
    }

    private function finalizeAttendance(GuruIzin $izin, User $actor): void
    {
        foreach ($izin->jadwals as $jadwal) {
            AbsensiGuru::updateOrCreate(
                ['jadwal_pelajaran_id' => $jadwal->id, 'tanggal' => $izin->tanggal_mulai],
                ['status' => 'izin', 'keterangan' => 'Izin Guru: '.$izin->jenis_izin.' ('.$izin->deskripsi.')', 'waktu_absen' => now(), 'dicatat_oleh' => $actor->id],
            );
            foreach ($jadwal->rombel?->siswa ?? [] as $student) {
                $student->user?->notify(new TeacherAbsenceStudentNotification($izin, $jadwal));
            }
        }
    }

    private function autoSign(GuruIzin $izin, User $actor, string $documentType = 'IZIN_GURU_PIKET'): void
    {
        $signature = UserDigitalSignature::where('user_id', $actor->id)->first();
        if ($signature?->isReady() && $signature->auto_sign_izin_guru) {
            DigitalDocument::autoSign(
                $actor,
                $documentType,
                'Izin Guru - '.($izin->guru?->nama_lengkap ?? ''),
                $izin->id,
                [$documentType, (string) $izin->id, (string) $izin->master_guru_id, $izin->guru?->nama_lengkap ?? ''],
            );
        }
    }
}
