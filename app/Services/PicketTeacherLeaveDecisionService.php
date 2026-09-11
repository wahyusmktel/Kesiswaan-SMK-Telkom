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
                $message = 'Ada pengajuan Izin Guru (Luar Sekolah) baru dari '.($izin->guru?->nama_lengkap ?? 'pegawai');
                foreach (User::whereHas('roles', fn ($query) => $query->where('name', 'Kurikulum'))->get() as $approver) {
                    $approver->notify(new PengajuanIzinGuruNotification($izin, 'pending_approval', $message, route('kurikulum.persetujuan-izin-guru.index')));
                }
            }

            return [$izin, $isSchool];
        });

        [$izin, $isSchool] = $result;
        $progress = $isSchool
            ? 'Izin Anda telah disetujui Guru Piket dan dinyatakan selesai.'
            : 'Izin Anda telah disetujui Guru Piket dan diteruskan ke Waka Kurikulum.';
        $izin->guru?->user?->notify(new PengajuanIzinGuruNotification($izin, 'status_updated', $progress, route('guru.izin.index')));
        $this->telegramNotifications->notifyApplicant($izin, $progress);

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

    private function autoSign(GuruIzin $izin, User $actor): void
    {
        $signature = UserDigitalSignature::where('user_id', $actor->id)->first();
        if ($signature?->isReady() && $signature->auto_sign_izin_guru) {
            DigitalDocument::autoSign(
                $actor,
                'IZIN_GURU_PIKET',
                'Izin Guru (Piket) - '.($izin->guru?->nama_lengkap ?? ''),
                $izin->id,
                ['IZIN_GURU_PIKET', (string) $izin->id, (string) $izin->master_guru_id, $izin->guru?->nama_lengkap ?? ''],
            );
        }
    }
}
