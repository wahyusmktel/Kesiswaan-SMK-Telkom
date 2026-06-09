<?php

namespace App\Http\Controllers\Uks;

use App\Http\Controllers\Controller;
use App\Models\DigitalDocument;
use App\Models\MasterSiswa;
use App\Models\UksEyeExam;
use App\Models\User;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UksEyeExamController extends Controller
{
    public function index(Request $request)
    {
        $query = UksEyeExam::with(['student.rombels.kelas', 'employee.roles', 'handler'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->whereHas('student', fn ($student) => $student->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"))
                        ->orWhereHas('employee', fn ($employee) => $employee->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhere('color_blind_notes', 'like', "%{$search}%")
                        ->orWhere('eye_health_findings', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('examinee_type', $request->type))
            ->when($request->filled('result'), fn ($query) => $query->where('color_blind_result', $request->result))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('examined_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('examined_at', '<=', $request->date_to))
            ->latest('examined_at');

        $exams = $query->paginate(20)->withQueryString();
        $statsRows = (clone $query)->get();
        $stats = [
            'total' => $statsRows->count(),
            'normal' => $statsRows->where('color_blind_result', 'normal')->count(),
            'color_alert' => $statsRows->whereIn('color_blind_result', ['partial', 'total'])->count(),
            'referrals' => $statsRows->where('conclusion', 'perlu_rujukan')->count(),
        ];

        $students = MasterSiswa::with('rombels.kelas')->orderBy('nama_lengkap')->limit(700)->get();
        $employees = User::with('roles')
            ->whereDoesntHave('roles', fn ($role) => $role->where('name', 'Siswa'))
            ->orderBy('name')
            ->limit(700)
            ->get();

        return view('pages.uks.eye-exams.index', compact('exams', 'stats', 'students', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $exam = UksEyeExam::create($data + [
            'handled_by' => Auth::id(),
        ]);

        $this->autoSignIfEnabled($exam);

        toast('Hasil tes kesehatan mata berhasil disimpan.', 'success');
        return redirect()->route('uks.eye-exams.show', $exam);
    }

    public function show(UksEyeExam $eyeExam)
    {
        $eyeExam->load(['student.rombels.kelas', 'employee.roles', 'handler']);
        $document = $this->digitalDocument($eyeExam);
        $qrBase64 = $document ? $this->qrBase64(route('verifikasi.dokumen', $document->token)) : null;

        return view('pages.uks.eye-exams.show', compact('eyeExam', 'document', 'qrBase64'));
    }

    public function sign(Request $request, UksEyeExam $eyeExam)
    {
        $data = $request->validate([
            'pin' => ['required', 'string'],
        ]);

        $signature = UserDigitalSignature::where('user_id', Auth::id())->first();

        if (!$signature || !$signature->isReady() || !$signature->verifyPin($data['pin'])) {
            return back()->with('error', 'PIN salah atau tanda tangan digital belum disiapkan.');
        }

        $this->signExam($eyeExam, Auth::user());

        toast('Resume tes kesehatan mata berhasil ditandatangani.', 'success');
        return back();
    }

    public function resume(UksEyeExam $eyeExam)
    {
        App::setLocale('id');
        Carbon::setLocale('id');

        $eyeExam->load(['student.rombels.kelas', 'employee.roles', 'handler']);
        $document = $this->digitalDocument($eyeExam);
        $qrBase64 = $document ? $this->qrBase64(route('verifikasi.dokumen', $document->token)) : null;

        $pdf = Pdf::loadView('pdf.uks.eye-exam-resume', compact('eyeExam', 'document', 'qrBase64'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('resume-tes-mata-' . str($eyeExam->examinee_name)->slug('-') . '.pdf');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'examinee_type' => ['required', Rule::in(['siswa', 'pegawai'])],
            'master_siswa_id' => ['nullable', 'exists:master_siswa,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'examined_at' => ['required', 'date'],
            'color_blind_result' => ['required', Rule::in(['normal', 'partial', 'total', 'inconclusive'])],
            'color_blind_notes' => ['nullable', 'string'],
            'visual_acuity_right' => ['nullable', 'string', 'max:20'],
            'visual_acuity_left' => ['nullable', 'string', 'max:20'],
            'eye_health_findings' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'conclusion' => ['required', Rule::in(['baik', 'perlu_observasi', 'perlu_rujukan'])],
            'notes' => ['nullable', 'string'],
        ]);

        if ($data['examinee_type'] === 'siswa') {
            $request->validate(['master_siswa_id' => ['required', 'exists:master_siswa,id']]);
            $data['user_id'] = null;
        } else {
            $request->validate(['user_id' => ['required', 'exists:users,id']]);
            $data['master_siswa_id'] = null;
        }

        return $data;
    }

    private function autoSignIfEnabled(UksEyeExam $eyeExam): void
    {
        $signature = UserDigitalSignature::where('user_id', Auth::id())->first();

        if ($signature?->isReady() && $signature->auto_sign_uks) {
            $this->signExam($eyeExam, Auth::user());
        }
    }

    private function signExam(UksEyeExam $eyeExam, User $user): DigitalDocument
    {
        $eyeExam->loadMissing(['student.rombels.kelas', 'employee']);

        return DigitalDocument::autoSign(
            $user,
            'UKS_EYE_EXAM',
            'Resume Tes Kesehatan Mata - ' . $eyeExam->examinee_name,
            $eyeExam->id,
            $this->hashParts($eyeExam)
        );
    }

    private function hashParts(UksEyeExam $eyeExam): array
    {
        return [
            'UKS_EYE_EXAM',
            (string) $eyeExam->id,
            (string) $eyeExam->examinee_type,
            (string) $eyeExam->master_siswa_id,
            (string) $eyeExam->user_id,
            (string) $eyeExam->examined_at,
            (string) $eyeExam->color_blind_result,
            (string) $eyeExam->visual_acuity_right,
            (string) $eyeExam->visual_acuity_left,
            (string) $eyeExam->conclusion,
        ];
    }

    private function digitalDocument(UksEyeExam $eyeExam): ?DigitalDocument
    {
        return DigitalDocument::where('document_type', 'UKS_EYE_EXAM')
            ->where('reference_id', $eyeExam->id)
            ->where('is_valid', true)
            ->first();
    }

    private function qrBase64(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'outputBase64' => true,
            'scale' => 4,
            'quietzoneSize' => 1,
            'eccLevel' => EccLevel::M,
        ]);

        return (new QRCode($options))->render($url);
    }
}
