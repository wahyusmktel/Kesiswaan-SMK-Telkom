<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AppSetting;
use App\Models\DigitalDocument;
use App\Models\GuruIzin;
use App\Models\User;
use App\Services\TelegramLeaveNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersetujuanIzinGuruController extends Controller
{
    public function index(Request $request)
    {
        $query = GuruIzin::with([
            'guru',
            'jadwals.rombel.kelas',
            'jadwals.mataPelajaran',
        ])->where(function ($approval) {
            $approval->whereIn('kategori_penyetujuan', ['luar', 'tidak_masuk', 'terlambat'])
                ->orWhere(fn ($school) => $school->where('kategori_penyetujuan', 'sekolah')
                    ->whereHas('guru', fn ($guru) => $guru->where('employee_category', \App\Models\MasterGuru::CATEGORY_TPA)));
        })
            ->where('status_kurikulum', 'disetujui')->latest();

        if ($request->filled('status')) {
            $query->where('status_sdm', $request->status);
        } else {
            $query->where('status_sdm', 'menunggu');
        }

        $izins = $query->paginate(10);

        // Manually load LMS materials and assignments for pivot data
        $this->loadLmsResourcesForIzins($izins);

        return view('pages.sdm.izin-guru.index', compact('izins'));
    }

    private function loadLmsResourcesForIzins($izins)
    {
        $materialIds = [];
        $assignmentIds = [];

        foreach ($izins as $izin) {
            foreach ($izin->jadwals as $jadwal) {
                if ($jadwal->pivot->lms_material_id) {
                    $materialIds[] = $jadwal->pivot->lms_material_id;
                }
                if ($jadwal->pivot->lms_assignment_id) {
                    $assignmentIds[] = $jadwal->pivot->lms_assignment_id;
                }
            }
        }

        $materials = \App\Models\LmsMaterial::whereIn('id', array_unique($materialIds))->get()->keyBy('id');
        $assignments = \App\Models\LmsAssignment::whereIn('id', array_unique($assignmentIds))->get()->keyBy('id');

        foreach ($izins as $izin) {
            foreach ($izin->jadwals as $jadwal) {
                $jadwal->pivot->loadedMaterial = $jadwal->pivot->lms_material_id
                    ? $materials->get($jadwal->pivot->lms_material_id)
                    : null;
                $jadwal->pivot->loadedAssignment = $jadwal->pivot->lms_assignment_id
                    ? $assignments->get($jadwal->pivot->lms_assignment_id)
                    : null;
            }
        }
    }

    public function approve(GuruIzin $izin, TelegramLeaveNotificationService $telegramNotifications)
    {
        abort_unless(in_array($izin->kategori_penyetujuan, ['luar', 'tidak_masuk', 'terlambat'], true) || ($izin->kategori_penyetujuan === 'sekolah' && $izin->guru?->is_tpa), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
        abort_unless($izin->status_kurikulum === 'disetujui' && $izin->status_sdm === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan SDM.');
        $requiresHeadmaster = $izin->requiresHeadmasterApproval();
        $izin->update([
            'status_sdm' => 'disetujui',
            'status_kepala_sekolah' => $requiresHeadmaster ? 'menunggu' : 'tidak_diperlukan',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
        ]);

        // Auto-sign TTD KAUR SDM (izin guru)
        $user = Auth::user();
        $sig = \App\Models\UserDigitalSignature::where('user_id', $user->id)->first();
        if ($sig && $sig->isReady() && $sig->auto_sign_izin_guru) {
            \App\Models\DigitalDocument::autoSign(
                $user,
                'IZIN_GURU_SDM',
                'Izin Guru (SDM) - '.($izin->guru->nama_lengkap ?? ''),
                $izin->id,
                ['IZIN_GURU_SDM', (string) $izin->id, (string) $izin->master_guru_id, $izin->guru->nama_lengkap ?? '']
            );
        }

        if ($requiresHeadmaster) {
            $telegramNotifications->notifyRoleApprovers($izin, 'kepsek');
            $izin->guru?->user?->notify(new \App\Notifications\PengajuanIzinGuruNotification(
                $izin,
                'status_updated',
                'Permohonan izin disetujui KAUR SDM dan menunggu persetujuan akhir Kepala Sekolah.',
                route('guru.izin.index')
            ));
            $telegramNotifications->notifyApplicant($izin, 'Permohonan izin disetujui KAUR SDM dan menunggu persetujuan akhir Kepala Sekolah.');

            return back()->with('success', 'Persetujuan SDM tersimpan. Karena pemohon Pegawai Tetap, izin diteruskan ke Kepala Sekolah.');
        }

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = 'Permohonan izin Anda telah disetujui sepenuhnya oleh KAUR SDM.';
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
            $telegramNotifications->notifyApplicant($izin, $msg);
        }

        // Sync to AbsensiGuru
        foreach ($izin->jadwals as $jadwal) {
            AbsensiGuru::updateOrCreate(
                [
                    'jadwal_pelajaran_id' => $jadwal->id,
                    'tanggal' => $izin->tanggal_mulai,
                ],
                [
                    'status' => 'izin',
                    'keterangan' => 'Izin Guru: '.$izin->jenis_izin.' ('.$izin->deskripsi.')',
                    'waktu_absen' => now(),
                    'dicatat_oleh' => Auth::id(),
                ]
            );

            // Notify Students
            $students = $jadwal->rombel->siswa()->with('user')->get();
            foreach ($students as $student) {
                if ($student->user) {
                    $student->user->notify(new \App\Notifications\TeacherAbsenceStudentNotification($izin, $jadwal));
                }
            }
        }

        return redirect()->back()->with('success', 'Permohonan izin telah disetujui sepenuhnya dan absensi telah diperbarui.');
    }

    private function generateQrBase64(string $url): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'outputBase64' => true,
            'scale' => 4,
            'quietzoneSize' => 1,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::M,
        ]);

        return (new \chillerlan\QRCode\QRCode($options))->render($url);
    }

    public function reject(Request $request, GuruIzin $izin, TelegramLeaveNotificationService $telegramNotifications)
    {
        abort_unless(in_array($izin->kategori_penyetujuan, ['luar', 'tidak_masuk', 'terlambat'], true) || ($izin->kategori_penyetujuan === 'sekolah' && $izin->guru?->is_tpa), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
        abort_unless($izin->status_kurikulum === 'disetujui' && $izin->status_sdm === 'menunggu', 409, 'Izin tidak lagi menunggu persetujuan SDM.');
        $request->validate(['catatan_sdm' => 'required|string']);

        $izin->update([
            'status_sdm' => 'ditolak',
            'sdm_id' => Auth::id(),
            'sdm_at' => now(),
            'catatan_sdm' => $request->catatan_sdm,
        ]);

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = 'Permohonan izin Anda ditolak oleh KAUR SDM.';
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
            $telegramNotifications->notifyApplicant($izin, $msg.' Catatan: '.$request->catatan_sdm);
        }

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh KAUR SDM.');
    }

    private function resolveDigitalSignatureQr(?User $user, string $documentType, string $roleTitle, GuruIzin $izin): ?string
    {
        if (! $user) {
            return null;
        }

        // 1. Cek apakah dokumen digital sudah terdaftar dan valid
        $doc = DigitalDocument::where('document_type', $documentType)
            ->where('reference_id', $izin->id)
            ->where('is_valid', true)
            ->first();

        // 2. Jika belum ada dokumen digital, cek apakah pegawai sudah setup tanda tangan digital
        if (! $doc) {
            $user->loadMissing(['digitalSignature', 'masterGuru.dapodikGuru']);
            if ($user->digitalSignature && $user->digitalSignature->isReady()) {
                $doc = DigitalDocument::autoSign(
                    $user,
                    $documentType,
                    'Izin Guru (' . $roleTitle . ') - ' . ($izin->guru?->nama_lengkap ?? ''),
                    $izin->id,
                    [$documentType, (string) $izin->id, (string) $izin->master_guru_id, $izin->guru?->nama_lengkap ?? '']
                );
            }
        }

        if ($doc && $doc->token) {
            return $this->generateQrBase64(route('verifikasi.dokumen', $doc->token));
        }

        return null;
    }

    public function printPdf(GuruIzin $izin)
    {
        if (! $izin->isFullyApproved()) {
            abort(403, 'Surat izin belum memperoleh seluruh persetujuan wajib.');
        }

        // Employees may only print their own permit. KAUR SDM retains the
        // administrative access needed to print approved permits for employees.
        $user = Auth::user();
        if (! $user->hasRole('KAUR SDM')) {
            $guru = $user->masterGuru;
            if (! $guru || $izin->master_guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh surat izin ini.');
            }
        }

        $izin->load([
            'guru.dapodikGuru',
            'guru.user.digitalSignature',
            'piket.masterGuru.dapodikGuru',
            'piket.digitalSignature',
            'kurikulum.masterGuru.dapodikGuru',
            'kurikulum.digitalSignature',
            'sdm.masterGuru.dapodikGuru',
            'sdm.digitalSignature',
            'kepalaSekolah.masterGuru.dapodikGuru',
            'kepalaSekolah.digitalSignature',
            'jadwals.rombel.kelas',
            'jadwals.mataPelajaran',
        ]);

        $settings = AppSetting::first();

        // Logo sekolah
        $logoPath = null;
        if (!empty($settings?->logo) && file_exists(public_path('storage/' . $settings->logo))) {
            $logoPath = public_path('storage/' . $settings->logo);
        } elseif (file_exists(public_path('images/teaching-module/smk-telkom-lampung.png'))) {
            $logoPath = public_path('images/teaching-module/smk-telkom-lampung.png');
        }
        $logoBase64 = $logoPath ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        // Digital signature QR codes
        $qrPiketBase64 = $this->resolveDigitalSignatureQr($izin->piket, 'IZIN_GURU_PIKET', 'Guru Piket', $izin);
        $qrKurikulumBase64 = $this->resolveDigitalSignatureQr($izin->kurikulum, 'IZIN_GURU_KURIKULUM', 'Waka Kurikulum', $izin);
        $qrSdmBase64 = $this->resolveDigitalSignatureQr($izin->sdm, 'IZIN_GURU_SDM', 'KAUR SDM', $izin);
        $qrKepalaSekolahBase64 = $this->resolveDigitalSignatureQr($izin->kepalaSekolah, 'IZIN_GURU_KEPALA_SEKOLAH', 'Kepala Sekolah', $izin);

        $pdf = Pdf::loadView('pdf.izin-guru', compact(
            'izin', 'settings', 'logoBase64',
            'qrPiketBase64', 'qrKurikulumBase64', 'qrSdmBase64', 'qrKepalaSekolahBase64'
        ))->setPaper('a4', 'portrait');

        $filename = 'Surat_Izin_Guru_' . str_replace(' ', '_', $izin->guru?->nama_lengkap ?? 'Pegawai') . '.pdf';

        return $pdf->stream($filename);
    }
}
