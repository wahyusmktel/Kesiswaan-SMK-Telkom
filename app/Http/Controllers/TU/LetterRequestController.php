<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\TuLetterRequest;
use App\Models\TuLetterCode;
use App\Models\TuOutgoingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LetterRequestController extends Controller
{
    /**
     * Display a listing for TU to manage.
     */
    public function index()
    {
        $requests = TuLetterRequest::with('user', 'letterCode', 'outgoingLetter')->latest()->paginate(10);
        return view('pages.tu.requests.index', compact('requests'));
    }

    /**
     * Form for users to request numbers.
     */
    public function create()
    {
        $letterCodes = TuLetterCode::all();
        $myRequests = TuLetterRequest::with('letterCode', 'outgoingLetter')
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.correspondence.request', compact('letterCodes', 'myRequests'));
    }

    /**
     * Store new request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'letter_code_id' => 'required|exists:tu_letter_codes,id',
            'subject' => 'required|string|max:255',
            'type' => 'required|in:upload,create',
            'file' => 'required_if:type,upload|file|mimes:pdf|max:2048',
            'content' => 'required_if:type,create|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tu/requests', 'public');
        }

        TuLetterRequest::create([
            'user_id' => Auth::id(),
            'letter_code_id' => $request->letter_code_id,
            'subject' => $request->subject,
            'type' => $request->type,
            'file_path' => $filePath,
            'content' => $request->content,
            'status' => 'pending',
        ]);

        toast('Permohonan nomor surat telah dikirim', 'success');
        return back();
    }

    /**
     * Download the uploaded PDF.
     */
    public function download(TuLetterRequest $letterRequest)
    {
        if (!$letterRequest->file_path) {
            toast('File tidak ditemukan', 'error');
            return back();
        }

        return response()->download(storage_path('app/public/' . $letterRequest->file_path));
    }

    /**
     * Print/Generate PDF from digital content.
     */
    public function print(TuLetterRequest $letterRequest)
    {
        if ($letterRequest->type !== 'create') {
            toast('Hanya surat yang dibuat di aplikasi yang bisa dicetak', 'error');
            return back();
        }

        $pdf = Pdf::loadView('pages.tu.requests.print', compact('letterRequest'));
        return $pdf->stream('Surat-' . $letterRequest->id . '.pdf');
    }

    /**
     * TU Approves request and issues number.
     */
    public function approve(Request $request, TuLetterRequest $letterRequest)
    {
        if ($letterRequest->status !== 'pending') {
            toast('Permohonan sudah diproses sebelumnya', 'error');
            return back();
        }

        $date = now();
        $year = $date->year;
        $monthRoman = $this->getRomanMonth($date->month);

        $code = $letterRequest->letterCode;

        // Issue number
        $lastSequence = TuOutgoingLetter::whereYear('date', $year)
            ->max('number_sequence') ?? 0;

        $newSequence = $lastSequence + 1;
        $formattedSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        $fullNumber = "{$formattedSequence}/SMKTEL-LPG/{$code->code}/{$monthRoman}/{$year}";

        $outgoing = TuOutgoingLetter::create([
            'number_sequence' => $newSequence,
            'letter_code_id' => $letterRequest->letter_code_id,
            'date' => $date,
            'subject' => $letterRequest->subject,
            'full_number' => $fullNumber,
            'user_id' => $letterRequest->user_id, // Issued for the requester
        ]);

        $letterRequest->update([
            'status' => 'approved',
            'outgoing_letter_id' => $outgoing->id,
            'notes' => $request->notes,
        ]);

        toast('Permohonan disetujui, nomor surat telah terbit', 'success');
        return back();
    }

    public function reject(Request $request, TuLetterRequest $letterRequest)
    {
        $letterRequest->update([
            'status' => 'rejected',
            'notes' => $request->notes,
        ]);

        toast('Permohonan ditolak', 'info');
        return back();
    }

    protected function getRomanMonth($month)
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];
        return $romans[$month] ?? 'I';
    }
}
