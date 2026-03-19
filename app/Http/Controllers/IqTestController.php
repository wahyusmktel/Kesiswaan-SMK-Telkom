<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IqTestController extends Controller
{
    public function index()
    {
        $results = \App\Models\IqTestResult::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest()->get();
        return view('pages.shared.tes-iq.start', compact('results'));
    }

    public function test()
    {
        $questions = \App\Models\IqQuestion::inRandomOrder()->get();
        return view('pages.shared.tes-iq.test', compact('questions'));
    }

    public function submit(\Illuminate\Http\Request $request)
    {
        $questions = \App\Models\IqQuestion::all();
        $answers = $request->input('answers', []);
        
        $correctCount = 0;
        foreach ($questions as $q) {
            if (isset($answers[$q->id]) && strtolower($answers[$q->id]) === strtolower($q->correct_option)) {
                $correctCount++;
            }
        }

        $iqScore = 80 + ($correctCount * 5); 
        $certificateCode = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));

        $result = \App\Models\IqTestResult::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'total_correct' => $correctCount,
            'iq_score' => $iqScore,
            'certificate_code' => $certificateCode,
        ]);

        toast('Tes selesai! Nilai dan sertifikat berhasil disimpan.', 'success');
        return redirect()->route('tes-iq.result', $result);
    }

    public function result(\App\Models\IqTestResult $result)
    {
        if ($result->user_id !== \Illuminate\Support\Facades\Auth::id()) abort(403);
        
        return view('pages.shared.tes-iq.result', compact('result'));
    }

    public function downloadCertificate(\App\Models\IqTestResult $result)
    {
        if ($result->user_id !== \Illuminate\Support\Facades\Auth::id()) abort(403);

        $verifyUrl = route('tes-iq.verify', $result->certificate_code);
        
        // SimpleQrCode generates SVG by default
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(0)->generate($verifyUrl);
        $qrCodeBase64 = base64_encode($qrCode);

        // Load the view and set paper size
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.shared.tes-iq.certificate', compact('result', 'qrCodeBase64'))
                  ->setPaper('A4', 'landscape');
                  
        // Enable dompdf to render base64 SVG or images correctly if needed
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('Sertifikat-Tes-IQ-' . str_replace(' ', '-', $result->user->name) . '.pdf');
    }
}
