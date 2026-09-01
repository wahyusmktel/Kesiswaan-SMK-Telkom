<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetReport;
use App\Models\AssetReportBuilding;
use App\Models\AssetReportLocation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetReportManagementController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->routeIs('super-admin.asset-reports.index') ? 'reports' : 'qrs';
        $buildings = AssetReportBuilding::withCount(['locations', 'locations as active_locations_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')->orderBy('name')->get();

        $locations = AssetReportLocation::with('building')->withCount('reports')
            ->when($request->filled('building_id'), fn ($query) => $query->where('asset_report_building_id', $request->integer('building_id')))
            ->orderBy('asset_report_building_id')->orderBy('sort_order')->orderBy('name')->get();

        $reportsQuery = AssetReport::with(['location.building', 'handler'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('urgency'), fn ($query) => $query->where('urgency', $request->input('urgency')))
            ->when($request->filled('location_id'), fn ($query) => $query->where('asset_report_location_id', $request->integer('location_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.trim((string) $request->input('search')).'%';
                $query->where(fn ($subQuery) => $subQuery
                    ->where('ticket_number', 'like', $search)
                    ->orWhere('reporter_name', 'like', $search)
                    ->orWhere('asset_name', 'like', $search)
                    ->orWhere('description', 'like', $search));
            });

        $reports = $reportsQuery->latest()->paginate(20)->withQueryString();
        $stats = [
            'total_locations' => AssetReportLocation::count(),
            'new_reports' => AssetReport::where('status', 'baru')->count(),
            'in_progress' => AssetReport::whereIn('status', ['diverifikasi', 'diproses'])->count(),
            'completed' => AssetReport::where('status', 'selesai')->count(),
        ];

        return view('pages.admin.asset-reports.index', compact('buildings', 'locations', 'reports', 'stats', 'section'));
    }

    public function storeBuilding(Request $request)
    {
        $data = $request->validate($this->buildingRules());
        $data['is_active'] = $request->boolean('is_active');
        AssetReportBuilding::create($data);

        return back()->with('success', 'Gedung berhasil ditambahkan.');
    }

    public function updateBuilding(Request $request, AssetReportBuilding $building)
    {
        $data = $request->validate($this->buildingRules($building));
        $data['is_active'] = $request->boolean('is_active');
        $building->update($data);

        return back()->with('success', 'Data gedung berhasil diperbarui.');
    }

    public function destroyBuilding(AssetReportBuilding $building)
    {
        if ($building->locations()->exists()) {
            return back()->with('error', 'Gedung masih memiliki ruangan. Pindahkan atau hapus ruangan terlebih dahulu.');
        }

        $building->delete();

        return back()->with('success', 'Gedung berhasil dihapus.');
    }

    public function storeLocation(Request $request)
    {
        $data = $request->validate($this->locationRules());
        $data['is_active'] = $request->boolean('is_active');
        $data['public_token'] = (string) Str::uuid();
        AssetReportLocation::create($data);

        return back()->with('success', 'Ruangan dan QR Code berhasil dibuat.');
    }

    public function updateLocation(Request $request, AssetReportLocation $location)
    {
        $data = $request->validate($this->locationRules($location));
        $data['is_active'] = $request->boolean('is_active');
        $location->update($data);

        return back()->with('success', 'Data ruangan berhasil diperbarui tanpa mengubah QR Code.');
    }

    public function destroyLocation(AssetReportLocation $location)
    {
        if ($location->reports()->exists()) {
            return back()->with('error', 'Ruangan memiliki riwayat laporan dan tidak dapat dihapus. Nonaktifkan saja agar QR tidak bisa digunakan.');
        }

        $location->delete();

        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    public function updateReport(Request $request, AssetReport $report)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(AssetReport::STATUSES))],
            'admin_notes' => ['nullable', 'string', 'max:3000'],
        ]);
        $data['handled_by'] = $request->user()->id;
        $data['handled_at'] = $report->handled_at ?? now();
        $data['completed_at'] = $data['status'] === 'selesai' ? ($report->completed_at ?? now()) : null;
        $report->update($data);

        return back()->with('success', 'Status laporan '.$report->ticket_number.' berhasil diperbarui.');
    }

    public function photo(AssetReport $report)
    {
        abort_unless($report->photo_path && Storage::exists($report->photo_path), 404);

        return response()->file(Storage::path($report->photo_path));
    }

    public function printQr(Request $request)
    {
        $locations = AssetReportLocation::with('building')
            ->when($request->filled('building_id'), fn ($query) => $query->where('asset_report_building_id', $request->integer('building_id')))
            ->when($request->filled('location_id'), fn ($query) => $query->whereKey($request->integer('location_id')))
            ->where('is_active', true)
            ->orderBy('asset_report_building_id')->orderBy('sort_order')->get();

        abort_if($locations->isEmpty(), 404, 'Tidak ada QR Code aktif untuk dicetak.');

        $qrCodes = $locations->mapWithKeys(function (AssetReportLocation $location) {
            $svg = QrCode::format('svg')->size(420)->margin(1)->errorCorrection('H')->generate($location->public_url);

            return [$location->id => 'data:image/svg+xml;base64,'.base64_encode($svg)];
        });

        $brandLogo = 'data:image/png;base64,'.base64_encode(file_get_contents(
            public_path('images/asset-report/smk-telkom-lampung-white.png')
        ));

        $pdf = Pdf::loadView('pdf.asset-report-qr', compact('locations', 'qrCodes', 'brandLogo'))
            ->setPaper('a4', 'portrait');

        $suffix = $locations->count() === 1 ? Str::slug($locations->first()->code) : 'semua-ruangan';

        return $pdf->download('qr-laporan-aset-'.$suffix.'.pdf');
    }

    private function buildingRules(?AssetReportBuilding $building = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', Rule::unique('asset_report_buildings')->ignore($building)],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    private function locationRules(?AssetReportLocation $location = null): array
    {
        return [
            'asset_report_building_id' => ['required', 'exists:asset_report_buildings,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:40', Rule::unique('asset_report_locations')->ignore($location)],
            'type' => ['required', 'in:kelas,toilet,laboratorium,ruang_kerja,perpustakaan,uks,aula,tempat_ibadah,kantin,gudang,pos_keamanan,area_umum,lainnya'],
            'floor' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
