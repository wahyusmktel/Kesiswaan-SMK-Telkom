<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IqVerificationController extends Controller
{
    public function verify($code)
    {
        $result = \App\Models\IqTestResult::with('user')->where('certificate_code', $code)->firstOrFail();
        return view('pages.shared.tes-iq.verify', compact('result'));
    }
}
