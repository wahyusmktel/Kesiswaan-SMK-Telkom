<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\TranscriptConfig;
use Illuminate\Http\Request;

class TranscriptConfigController extends Controller
{
    public function index()
    {
        $config = TranscriptConfig::firstOrCreate([]);

        return view('pages.operator.transcript.config', compact('config'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'graduation_date' => 'nullable|date',
            'signature_city' => 'nullable|string|max:100',
            'signature_date' => 'nullable|date',
            'principal_name' => 'nullable|string|max:255',
            'principal_nip' => 'nullable|string|max:100',
            'letterhead' => 'nullable|string',
            'number_start' => 'nullable|string|max:255',
            'number_end' => 'nullable|string|max:255',
            'number_suffix' => 'nullable|string|max:255',
            'number_date' => 'nullable|date',
        ]);

        TranscriptConfig::firstOrCreate([])->update($data);
        toast('Config transkrip berhasil disimpan.', 'success');

        return back();
    }
}
