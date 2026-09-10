<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\AbsensiGuru;
use App\Models\AppSetting;
use App\Models\GuruIzin;
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
        ])->whereIn('kategori_penyetujuan', ['luar', 'tidak_masuk', 'terlambat'])
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

    public function approve(GuruIzin $izin)
    {
        abort_unless(in_array($izin->kategori_penyetujuan, ['luar', 'tidak_masuk', 'terlambat'], true), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
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
            foreach (\App\Models\User::role('Kepala Sekolah')->get() as $headmaster) {
                $headmaster->notify(new \App\Notifications\PengajuanIzinGuruNotification(
                    $izin,
                    'approval_required',
                    'Izin Pegawai Tetap '.$izin->guru->nama_lengkap.' menunggu persetujuan Anda.',
                    route('kepala-sekolah.persetujuan-izin-guru.index')
                ));
            }
            $izin->guru?->user?->notify(new \App\Notifications\PengajuanIzinGuruNotification(
                $izin,
                'status_updated',
                'Permohonan izin disetujui KAUR SDM dan menunggu persetujuan akhir Kepala Sekolah.',
                route('guru.izin.index')
            ));

            return back()->with('success', 'Persetujuan SDM tersimpan. Karena pemohon Pegawai Tetap, izin diteruskan ke Kepala Sekolah.');
        }

        // Notify Teacher
        $teacherUser = $izin->guru->user;
        if ($teacherUser) {
            $msg = 'Permohonan izin Anda telah disetujui sepenuhnya oleh KAUR SDM.';
            $url = route('guru.izin.index');
            $teacherUser->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'status_updated', $msg, $url));
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

    public function reject(Request $request, GuruIzin $izin)
    {
        abort_unless(in_array($izin->kategori_penyetujuan, ['luar', 'tidak_masuk', 'terlambat'], true), 409, 'Kategori izin ini tidak memerlukan persetujuan SDM.');
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
        }

        return redirect()->back()->with('info', 'Permohonan izin telah ditolak oleh KAUR SDM.');
    }

    public function printPdf(GuruIzin $izin)
    {
        if (! $izin->isFullyApproved()) {
            abort(403, 'Surat izin belum memperoleh seluruh persetujuan wajib.');
        }

        // Security check: If teacher, only allow printing their own permit
        // Bypass this if user also has KAUR SDM role
        $user = Auth::user();
        if ($user->hasRole('Guru Kelas') && ! $user->hasRole('KAUR SDM')) {
            $guru = $user->masterGuru;
            if (! $guru || $izin->master_guru_id !== $guru->id) {
                abort(403, 'Anda tidak memiliki akses untuk mengunduh surat izin ini.');
            }
        }

        $izin->load([
            'guru',
            'piket',
            'kurikulum',
            'sdm',
            'kepalaSekolah',
            'jadwals.rombel.kelas',
            'jadwals.mataPelajaran',
        ]);

        $settings = AppSetting::first();

        // Digital signature QR codes
        $docPiket = \App\Models\DigitalDocument::where('document_type', 'IZIN_GURU_PIKET')->where('reference_id', $izin->id)->where('is_valid', true)->first();
        $docKurikulum = \App\Models\DigitalDocument::where('document_type', 'IZIN_GURU_KURIKULUM')->where('reference_id', $izin->id)->where('is_valid', true)->first();
        $docSdm = \App\Models\DigitalDocument::where('document_type', 'IZIN_GURU_SDM')->where('reference_id', $izin->id)->where('is_valid', true)->first();

        $qrPiketBase64 = $docPiket ? $this->generateQrBase64(route('verifikasi.dokumen', $docPiket->token)) : null;
        $qrKurikulumBase64 = $docKurikulum ? $this->generateQrBase64(route('verifikasi.dokumen', $docKurikulum->token)) : null;
        $qrSdmBase64 = $docSdm ? $this->generateQrBase64(route('verifikasi.dokumen', $docSdm->token)) : null;

        $pdf = Pdf::loadView('pdf.izin-guru', compact(
            'izin', 'settings',
            'docPiket', 'docKurikulum', 'docSdm',
            'qrPiketBase64', 'qrKurikulumBase64', 'qrSdmBase64'
        ));

        return $pdf->stream('Surat_Izin_Guru_'.str_replace(' ', '_', $izin->guru->nama_lengkap).'.pdf');
    }
}
