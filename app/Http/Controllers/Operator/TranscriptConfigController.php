<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\TranscriptConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'letterhead_image' => 'nullable|image|max:10240',
            'watermark_image' => 'nullable|image|max:10240',
            'number_start' => 'nullable|string|max:255',
            'number_end' => 'nullable|string|max:255',
            'number_suffix' => 'nullable|string|max:255',
            'number_date' => 'nullable|date',
            'margin_top' => 'nullable|numeric|min:0|max:100',
            'margin_right' => 'nullable|numeric|min:0|max:100',
            'margin_bottom' => 'nullable|numeric|min:0|max:100',
            'margin_left' => 'nullable|numeric|min:0|max:100',
            'paper_size' => 'nullable|in:A4,F4,Letter,Legal',
            'is_borderless' => 'nullable|boolean',
        ]);

        $config = TranscriptConfig::firstOrCreate([]);
        $data['is_borderless'] = $request->boolean('is_borderless');

        if ($request->hasFile('letterhead_image')) {
            if ($config->letterhead_path) {
                Storage::disk('public')->delete($config->letterhead_path);
            }

            $data['letterhead_path'] = $request->file('letterhead_image')->store('transcripts/letterheads', 'public');
        }

        if ($request->hasFile('watermark_image')) {
            if ($config->watermark_path) {
                Storage::disk('public')->delete($config->watermark_path);
            }

            $data['watermark_path'] = $request->file('watermark_image')->store('transcripts/watermarks', 'public');
        }

        unset($data['letterhead_image'], $data['watermark_image']);

        $config->update($data);
        toast('Config transkrip berhasil disimpan.', 'success');

        return back();
    }
}
