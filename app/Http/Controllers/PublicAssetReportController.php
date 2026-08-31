<?php

namespace App\Http\Controllers;

use App\Models\AssetReport;
use App\Models\AssetReportLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicAssetReportController extends Controller
{
    public function create(AssetReportLocation $location)
    {
        abort_unless($location->is_active && $location->building()->where('is_active', true)->exists(), 404);

        $location->load('building');

        return view('public.asset-report.create', compact('location'));
    }

    public function store(Request $request, AssetReportLocation $location)
    {
        abort_unless($location->is_active && $location->building()->where('is_active', true)->exists(), 404);

        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:120'],
            'reporter_identifier' => ['nullable', 'string', 'max:80'],
            'reporter_type' => ['required', 'in:siswa,guru_karyawan,tamu'],
            'contact' => ['nullable', 'string', 'max:80'],
            'asset_name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'in:'.implode(',', array_keys(AssetReport::CATEGORIES))],
            'urgency' => ['required', 'in:rendah,normal,tinggi,darurat'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'website' => ['nullable', 'max:0'],
        ], [
            'description.min' => 'Jelaskan kondisi kerusakan minimal 10 karakter.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        unset($validated['website']);
        $validated['asset_report_location_id'] = $location->id;
        $validated['ticket_number'] = $this->ticketNumber();
        $validated['status'] = 'baru';
        $validated['ip_hash'] = hash('sha256', (string) $request->ip().'|'.config('app.key'));
        $validated['photo_path'] = $request->file('photo')?->store('asset-reports');

        $report = AssetReport::create($validated);

        return redirect()->route('asset-report.public.success', [$location, $report->ticket_number]);
    }

    public function success(AssetReportLocation $location, string $ticket)
    {
        $report = $location->reports()->where('ticket_number', $ticket)->firstOrFail();
        $location->load('building');

        return view('public.asset-report.success', compact('location', 'report'));
    }

    private function ticketNumber(): string
    {
        do {
            $ticket = 'AST-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (AssetReport::where('ticket_number', $ticket)->exists());

        return $ticket;
    }
}
