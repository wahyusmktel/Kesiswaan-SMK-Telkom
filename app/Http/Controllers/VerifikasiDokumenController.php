<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;

class VerifikasiDokumenController extends Controller
{
    public function show(string $token)
    {
        $doc = DigitalDocument::where('token', $token)->first();

        if (!$doc) {
            return view('pages.public.verifikasi-dokumen', [
                'doc'    => null,
                'status' => 'not_found',
            ]);
        }

        $hmacValid = $doc->verifyHmac();

        $status = match(true) {
            !$doc->is_valid     => 'revoked',
            !$hmacValid         => 'tampered',
            default             => 'valid',
        };

        return view('pages.public.verifikasi-dokumen', compact('doc', 'status'));
    }
}
