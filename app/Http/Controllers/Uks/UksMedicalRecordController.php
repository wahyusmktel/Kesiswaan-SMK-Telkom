<?php

namespace App\Http\Controllers\Uks;

use App\Http\Controllers\Controller;
use App\Models\DigitalDocument;
use App\Models\MasterSiswa;
use App\Models\UksMedicalRecord;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UksMedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = UksMedicalRecord::with(['student.rombels.kelas', 'handler'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->where('complaint', 'like', "%{$search}%")
                        ->orWhere('diagnosis', 'like', "%{$search}%")
                        ->orWhereHas('student', fn ($student) => $student->where('nama_lengkap', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('disposition'), fn ($query) => $query->where('disposition', $request->disposition))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('visited_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('visited_at', '<=', $request->date_to))
            ->latest('visited_at');

        $records = $query->paginate(20)->withQueryString();
        $students = MasterSiswa::with('rombels.kelas')->orderBy('nama_lengkap')->limit(600)->get();
        $statsRows = (clone $query)->get();
        $stats = [
            'total' => $statsRows->count(),
            'today' => UksMedicalRecord::whereDate('visited_at', today())->count(),
            'referrals' => $statsRows->where('disposition', 'rujukan')->count(),
            'resting' => $statsRows->where('disposition', 'istirahat_uks')->count(),
        ];
        $topDiagnoses = $statsRows->filter(fn ($record) => filled($record->diagnosis))
            ->groupBy(fn ($record) => $record->diagnosis)
            ->map(fn ($items) => $items->count())
            ->sortDesc()
            ->take(6);

        return view('pages.uks.medical-records.index', compact('records', 'students', 'stats', 'topDiagnoses'));
    }

    public function store(Request $request)
    {
        $record = UksMedicalRecord::create($this->validatedData($request) + [
            'handled_by' => Auth::id(),
        ]);

        $this->autoSignIfEnabled($record, 'UKS_SICK_NOTE');

        if ($record->disposition === 'rujukan') {
            $this->autoSignIfEnabled($record, 'UKS_REFERRAL');
        }

        toast('Rekam medis UKS berhasil disimpan.', 'success');
        return redirect()->route('uks.records.show', $record);
    }

    public function show(UksMedicalRecord $record)
    {
        $record->load(['student.rombels.kelas', 'handler']);
        $sickDocument = $this->digitalDocument($record, 'UKS_SICK_NOTE');
        $referralDocument = $this->digitalDocument($record, 'UKS_REFERRAL');
        $sickQr = $sickDocument ? $this->qrBase64(route('verifikasi.dokumen', $sickDocument->token)) : null;
        $referralQr = $referralDocument ? $this->qrBase64(route('verifikasi.dokumen', $referralDocument->token)) : null;
        $students = MasterSiswa::with('rombels.kelas')->orderBy('nama_lengkap')->limit(600)->get();

        return view('pages.uks.medical-records.show', compact('record', 'sickDocument', 'referralDocument', 'sickQr', 'referralQr', 'students'));
    }

    public function update(Request $request, UksMedicalRecord $record)
    {
        $record->update($this->validatedData($request));

        toast('Rekam medis UKS berhasil diperbarui.', 'success');
        return redirect()->route('uks.records.show', $record);
    }

    public function destroy(UksMedicalRecord $record)
    {
        $record->delete();

        toast('Rekam medis UKS berhasil dihapus.', 'success');
        return redirect()->route('uks.records.index');
    }

    public function sign(Request $request, UksMedicalRecord $record)
    {
        $data = $request->validate([
            'document_type' => ['required', 'in:UKS_SICK_NOTE,UKS_REFERRAL'],
            'pin' => ['required', 'string'],
        ]);

        $signature = UserDigitalSignature::where('user_id', Auth::id())->first();

        if (!$signature || !$signature->isReady() || !$signature->verifyPin($data['pin'])) {
            return back()->with('error', 'PIN salah atau tanda tangan digital belum disiapkan.');
        }

        $this->signRecord($record, $data['document_type'], Auth::user());

        toast('Dokumen UKS berhasil ditandatangani.', 'success');
        return back();
    }

    public function sickNote(UksMedicalRecord $record)
    {
        return $this->documentPdf($record, 'UKS_SICK_NOTE', 'pdf.uks.sick-note', 'surat-keterangan-sakit-');
    }

    public function referral(UksMedicalRecord $record)
    {
        abort_if($record->disposition !== 'rujukan', 404);

        return $this->documentPdf($record, 'UKS_REFERRAL', 'pdf.uks.referral', 'surat-rujukan-uks-');
    }

    public function report(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $date = $request->filled('date') ? Carbon::parse($request->date) : now();
        $dateFrom = $period === 'weekly' ? $date->copy()->startOfWeek() : $date->copy()->startOfMonth();
        $dateTo = $period === 'weekly' ? $date->copy()->endOfWeek() : $date->copy()->endOfMonth();

        $records = UksMedicalRecord::with(['student.rombels.kelas', 'handler'])
            ->whereBetween('visited_at', [$dateFrom, $dateTo])
            ->orderBy('visited_at')
            ->get();

        $summary = [
            'total' => $records->count(),
            'referrals' => $records->where('disposition', 'rujukan')->count(),
            'home' => $records->where('disposition', 'pulang')->count(),
            'resting' => $records->where('disposition', 'istirahat_uks')->count(),
            'top_diagnoses' => $records->filter(fn ($record) => filled($record->diagnosis))
                ->groupBy(fn ($record) => $record->diagnosis)
                ->map(fn ($items) => $items->count())
                ->sortDesc()
                ->take(10),
        ];

        $pdf = Pdf::loadView('pdf.uks.report', compact('records', 'summary', 'dateFrom', 'dateTo', 'period'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('laporan-uks-' . $period . '-' . $dateFrom->format('Ymd') . '-' . $dateTo->format('Ymd') . '.pdf');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'master_siswa_id' => ['required', 'exists:master_siswa,id'],
            'visited_at' => ['required', 'date'],
            'complaint' => ['required', 'string', 'max:255'],
            'symptoms' => ['nullable', 'array'],
            'symptoms.*' => ['nullable', 'string', 'max:80'],
            'anamnesis' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string', 'max:255'],
            'treatment' => ['nullable', 'string'],
            'medicine' => ['nullable', 'string'],
            'temperature' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'blood_pressure' => ['nullable', 'string', 'max:20'],
            'pulse' => ['nullable', 'integer', 'min:30', 'max:220'],
            'oxygen_saturation' => ['nullable', 'integer', 'min:50', 'max:100'],
            'condition' => ['required', 'in:ringan,sedang,berat'],
            'disposition' => ['required', 'in:kembali_kelas,istirahat_uks,pulang,rujukan'],
            'rest_until' => ['nullable', 'date'],
            'referral_facility_type' => ['nullable', 'string', 'max:50'],
            'referral_facility_name' => ['nullable', 'string', 'max:255'],
            'referral_reason' => ['nullable', 'string'],
            'parent_notification' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['symptoms'] = array_values(array_filter($data['symptoms'] ?? []));

        return $data;
    }

    private function documentPdf(UksMedicalRecord $record, string $type, string $view, string $prefix)
    {
        $record->load(['student.rombels.kelas', 'handler']);
        $document = $this->digitalDocument($record, $type);
        $qrBase64 = $document ? $this->qrBase64(route('verifikasi.dokumen', $document->token)) : null;

        $pdf = Pdf::loadView($view, compact('record', 'document', 'qrBase64'))->setPaper('a4');

        return $pdf->stream($prefix . str($record->student->nama_lengkap)->slug('-') . '.pdf');
    }

    private function autoSignIfEnabled(UksMedicalRecord $record, string $type): void
    {
        $signature = UserDigitalSignature::where('user_id', Auth::id())->first();

        if ($signature?->isReady() && $signature->auto_sign_uks) {
            $this->signRecord($record, $type, Auth::user());
        }
    }

    private function signRecord(UksMedicalRecord $record, string $type, $user): DigitalDocument
    {
        $title = $type === 'UKS_REFERRAL'
            ? 'Surat Rujukan UKS - ' . $record->student->nama_lengkap
            : 'Surat Keterangan Sakit UKS - ' . $record->student->nama_lengkap;

        return DigitalDocument::autoSign($user, $type, $title, $record->id, $this->hashParts($record, $type));
    }

    private function hashParts(UksMedicalRecord $record, string $type): array
    {
        return [
            $type,
            (string) $record->id,
            (string) $record->master_siswa_id,
            (string) $record->visited_at,
            (string) $record->complaint,
            (string) $record->diagnosis,
            (string) $record->disposition,
        ];
    }

    private function digitalDocument(UksMedicalRecord $record, string $type): ?DigitalDocument
    {
        return DigitalDocument::where('document_type', $type)
            ->where('reference_id', $record->id)
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
