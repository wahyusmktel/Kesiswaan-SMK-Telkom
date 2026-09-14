<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AssetReportExport;
use App\Http\Controllers\Controller;
use App\Models\AssetReport;
use App\Models\AssetReportBuilding;
use App\Models\AssetReportLocation;
use App\Models\DapodikGuru;
use App\Models\DigitalDocument;
use App\Models\MasterGuru;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetReportManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->reportFilters($request);
        $section = $request->routeIs('super-admin.asset-reports.index') ? 'reports' : 'qrs';
        $buildings = AssetReportBuilding::withCount(['locations', 'locations as active_locations_count' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('sort_order')->orderBy('name')->get();

        $locations = AssetReportLocation::with('building')->withCount('reports')
            ->when($request->filled('building_id'), fn ($query) => $query->where('asset_report_building_id', $request->integer('building_id')))
            ->orderBy('asset_report_building_id')->orderBy('sort_order')->orderBy('name')->get();

        $reports = $this->reportsQuery($filters)->latest()->paginate(20)->withQueryString();
        $stats = [
            'total_locations' => AssetReportLocation::count(),
            'new_reports' => AssetReport::where('status', 'baru')->count(),
            'in_progress' => AssetReport::whereIn('status', ['diverifikasi', 'diproses'])->count(),
            'completed' => AssetReport::where('status', 'selesai')->count(),
        ];

        $digitalSignatureReady = $request->user()?->digitalSignature?->isReady() ?? false;

        return view('pages.admin.asset-reports.index', compact('buildings', 'locations', 'reports', 'stats', 'section', 'digitalSignatureReady'));
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

    public function exportExcel(Request $request)
    {
        $filters = $this->reportFilters($request);
        $reports = $this->reportsQuery($filters)->latest()->get();
        $filename = 'rekap-laporan-aset-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(
            new AssetReportExport($reports, $this->reportFilterLabels($filters)),
            $filename
        );
    }

    public function exportPdf(Request $request)
    {
        abort_unless($request->user()->hasRole('KAUR SARPRA'), 403);

        $digitalSignature = UserDigitalSignature::where('user_id', $request->user()->id)->first();
        if (! $digitalSignature?->isReady()) {
            return redirect()->route('super-admin.asset-reports.index', $request->query())
                ->with('error', 'Aktifkan identitas tanda tangan digital KAUR SARPRA sebelum mengunduh laporan PDF resmi.');
        }

        $filters = $this->reportFilters($request);
        $reports = $this->reportsQuery($filters)->latest()->get();
        $signedAt = now();
        $signerNip = $this->signerNipFromDapodik($request);
        $hash = DigitalDocument::generateHash([
            'REKAP_LAPORAN_ASET',
            $request->user()->id,
            $signedAt->toIso8601String(),
            json_encode($filters),
            hash('sha256', $reports->map(fn (AssetReport $report) => $report->id.':'.$report->updated_at?->timestamp)->implode('|')),
        ]);
        $document = DigitalDocument::create([
            'document_type' => 'REKAP_LAPORAN_ASET',
            'document_title' => 'Rekap Laporan Aset dan Sarana Prasarana',
            'document_hash' => $hash,
            'hmac_signature' => DigitalDocument::generateHmac($hash),
            'signed_by' => $request->user()->id,
            'signer_name' => $request->user()->name,
            'signer_nip' => $signerNip,
            'signer_role' => 'KAUR SARPRA',
            'signed_at' => $signedAt,
            'is_valid' => true,
        ]);
        $verificationUrl = route('verifikasi.dokumen', $document->token);
        $signatureQr = 'data:image/svg+xml;base64,'.base64_encode(
            QrCode::format('svg')->size(220)->margin(1)->errorCorrection('H')->generate($verificationUrl)
        );
        $logoPath = public_path('images/teaching-module/smk-telkom-lampung.png');
        $brandLogo = is_file($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
        $summary = $this->reportSummary($reports);
        $filterLabels = $this->reportFilterLabels($filters);

        $pdf = Pdf::loadView('pdf.asset-report-recap', compact(
            'reports',
            'summary',
            'filterLabels',
            'document',
            'signatureQr',
            'verificationUrl',
            'brandLogo',
            'signedAt'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('rekap-laporan-aset-'.$signedAt->format('Ymd-His').'.pdf');
    }

    private function signerNipFromDapodik(Request $request): ?string
    {
        $masterGuru = $request->user()->masterGuru;
        if (! $masterGuru) {
            return null;
        }

        $category = $masterGuru->employee_category === MasterGuru::CATEGORY_TPA
            ? DapodikGuru::CATEGORY_TPA
            : DapodikGuru::CATEGORY_TEACHER;

        $nip = $masterGuru->dapodikGuru()
            ->where('employee_category', $category)
            ->value('nip');

        return filled($nip) ? trim((string) $nip) : null;
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

    private function reportFilters(Request $request): array
    {
        return validator($request->query(), [
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(array_keys(AssetReport::STATUSES))],
            'urgency' => ['nullable', Rule::in(['rendah', 'normal', 'tinggi', 'darurat'])],
            'building_id' => ['nullable', 'integer', 'exists:asset_report_buildings,id'],
            'location_id' => ['nullable', 'integer', 'exists:asset_report_locations,id'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ])->validate();
    }

    private function reportsQuery(array $filters): Builder
    {
        return AssetReport::with(['location.building', 'handler'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['urgency'] ?? null, fn ($query, $urgency) => $query->where('urgency', $urgency))
            ->when($filters['building_id'] ?? null, fn ($query, $buildingId) => $query->whereHas(
                'location',
                fn ($locationQuery) => $locationQuery->where('asset_report_building_id', $buildingId)
            ))
            ->when($filters['location_id'] ?? null, fn ($query, $locationId) => $query->where('asset_report_location_id', $locationId))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when($filters['search'] ?? null, function ($query, $value) {
                $search = '%'.trim((string) $value).'%';
                $query->where(fn ($subQuery) => $subQuery
                    ->where('ticket_number', 'like', $search)
                    ->orWhere('reporter_name', 'like', $search)
                    ->orWhere('asset_name', 'like', $search)
                    ->orWhere('description', 'like', $search));
            });
    }

    private function reportFilterLabels(array $filters): array
    {
        $labels = [];
        if ($filters['date_from'] ?? null) {
            $labels[] = 'Mulai '.date('d/m/Y', strtotime($filters['date_from']));
        }
        if ($filters['date_to'] ?? null) {
            $labels[] = 'Sampai '.date('d/m/Y', strtotime($filters['date_to']));
        }
        if ($filters['building_id'] ?? null) {
            $labels[] = 'Gedung: '.(AssetReportBuilding::find($filters['building_id'])?->name ?? '-');
        }
        if ($filters['location_id'] ?? null) {
            $labels[] = 'Ruangan: '.(AssetReportLocation::find($filters['location_id'])?->name ?? '-');
        }
        if ($filters['status'] ?? null) {
            $labels[] = 'Status: '.(AssetReport::STATUSES[$filters['status']] ?? $filters['status']);
        }
        if ($filters['urgency'] ?? null) {
            $labels[] = 'Urgensi: '.ucfirst($filters['urgency']);
        }
        if ($filters['search'] ?? null) {
            $labels[] = 'Pencarian: "'.trim($filters['search']).'"';
        }

        return $labels ?: ['Semua laporan'];
    }

    private function reportSummary($reports): array
    {
        return [
            'total' => $reports->count(),
            'new' => $reports->where('status', 'baru')->count(),
            'in_progress' => $reports->whereIn('status', ['diverifikasi', 'diproses'])->count(),
            'completed' => $reports->where('status', 'selesai')->count(),
            'urgent' => $reports->whereIn('urgency', ['tinggi', 'darurat'])->count(),
        ];
    }
}
