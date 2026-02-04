<?php

namespace App\Http\Controllers\TU;

use App\Http\Controllers\Controller;
use App\Models\TuIncomingLetter;
use App\Models\TuOutgoingLetter;
use App\Models\TuLetterRequest;
use App\Models\TuLetterCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CorrespondenceController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'surat_masuk_today' => TuIncomingLetter::whereDate('created_at', today())->count(),
            'surat_keluar_today' => TuOutgoingLetter::whereDate('created_at', today())->count(),
            'pending_requests' => TuLetterRequest::where('status', 'pending')->count(),
            'total_codes' => TuLetterCode::count(),
        ];

        $recentIncoming = TuIncomingLetter::latest()->limit(5)->get();
        $recentOutgoing = TuOutgoingLetter::with('letterCode', 'user')->latest()->limit(5)->get();
        $pendingRequests = TuLetterRequest::with('letterCode', 'user')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('pages.tu.dashboard', compact('stats', 'recentIncoming', 'recentOutgoing', 'pendingRequests'));
    }

    public function incomingIndex()
    {
        $letters = TuIncomingLetter::latest()->paginate(10);
        return view('pages.tu.incoming.index', compact('letters'));
    }

    public function outgoingIndex()
    {
        $letters = TuOutgoingLetter::with('letterCode', 'user')->latest()->paginate(10);
        $letterCodes = TuLetterCode::all();
        return view('pages.tu.outgoing.index', compact('letters', 'letterCodes'));
    }

    public function storeOutgoing(Request $request)
    {
        $request->validate([
            'letter_code_id' => 'required|exists:tu_letter_codes,id',
            'subject' => 'required|string|max:255',
            'recipient' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $code = TuLetterCode::find($request->letter_code_id);
        $date = Carbon::parse($request->date);
        $year = $date->year;
        $monthRoman = $this->getRomanMonth($date->month);

        // Get latest sequence for this year
        $lastSequence = TuOutgoingLetter::whereYear('date', $year)
            ->max('number_sequence') ?? 0;

        $newSequence = $lastSequence + 1;
        $formattedSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);

        // Format: 0001/SMKTEL-LPG/TATA.01/I/2026
        $fullNumber = "{$formattedSequence}/SMKTEL-LPG/{$code->code}/{$monthRoman}/{$year}";

        TuOutgoingLetter::create([
            'number_sequence' => $newSequence,
            'letter_code_id' => $request->letter_code_id,
            'date' => $request->date,
            'subject' => $request->subject,
            'recipient' => $request->recipient,
            'full_number' => $fullNumber,
            'user_id' => Auth::id(),
        ]);

        toast('Nomor surat berhasil diterbitkan', 'success');
        return back();
    }

    private function getRomanMonth($month)
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
