<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = rtrim(config('asset.api_url', 'http://localhost:8001'), '/');
    }

    /**
     * Menampilkan daftar semua aset dari database lokal hasil sinkronisasi.
     */
    public function index(Request $request)
    {
        $search        = $request->input('search');
        $categoryId    = $request->input('category_id');
        $status        = $request->input('status');
        $purchaseYear  = $request->input('purchase_year');
        $perPage       = $request->input('per_page', 15);

        try {
            $query = \App\Models\SyncedAsset::query();

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_code_ypt', 'like', "%{$search}%")
                      ->orWhere('institution', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%")
                      ->orWhere('room', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $query->where('category', $categoryId);
            }

            if ($status) {
                $query->where('current_status', $status);
            }

            if ($purchaseYear) {
                // Because there is no explicit 'purchase_year' we extract from 'created_at' or purchase_cost
                $query->whereYear('created_at', $purchaseYear); 
            }

            $assets = $query->latest('last_synced_at')->paginate($perPage)->withQueryString();

            // Statistik lokal
            $stats = [
                'total_assets'   => \App\Models\SyncedAsset::count(),
                'total_aktif'    => \App\Models\SyncedAsset::where('current_status', 'Tersedia')->count(),
                'total_dipinjam' => \App\Models\SyncedAsset::where('current_status', 'Dipinjam')->count(),
                'total_rusak'    => \App\Models\SyncedAsset::where('current_status', 'Rusak')->count(),
            ];

            // Categories
            $categories = \App\Models\SyncedAsset::select('category')
                            ->distinct()
                            ->whereNotNull('category')
                            ->pluck('category')
                            ->map(function ($cat) {
                                return ['id' => $cat, 'name' => $cat];
                            });

            // Years
            $years = \App\Models\SyncedAsset::selectRaw('YEAR(created_at) as year')
                            ->distinct()
                            ->orderByDesc('year')
                            ->pluck('year');

            return view('pages.shared.assets.index', [
                'assets'     => $assets,
                'stats'      => $stats,
                'categories' => $categories,
                'years'      => $years,
                'error'      => null,
                'filters'    => $request->all(),
            ]);

        } catch (\Exception $e) {
            Log::error('Asset Local DB Error (index): ' . $e->getMessage());
            return view('pages.shared.assets.index', [
                'error'      => 'Gagal mengambil data dari database lokal: ' . $e->getMessage(),
                'assets'     => null,
                'stats'      => null,
                'categories' => collect(),
                'years'      => collect(),
                'filters'    => [],
            ]);
        }
    }

    /**
     * Menampilkan detail satu aset dari database lokal.
     */
    public function show(int $id)
    {
        try {
            $asset = \App\Models\SyncedAsset::where('asset_id', $id)->first();

            if (!$asset) {
                // Return 404 behavior tapi dalam konteks view untuk user
                abort(404, 'Aset tidak ditemukan di database lokal. Pastikan sudah sinkronisasi.');
            }

            return view('pages.shared.assets.show', [
                'asset'      => $asset->toArray(),
                'isDisposed' => $asset->current_status === 'Dihapuskan',
                'error'      => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Asset Local DB Error (show): ' . $e->getMessage());
            return view('pages.shared.assets.show', [
                'error' => 'Gagal memuat detail aset: ' . $e->getMessage(),
                'asset' => null,
            ]);
        }
    }

    /**
     * Tampilkan form pengajuan peminjaman aset dari database lokal.
     */
    public function showBorrowForm(int $id)
    {
        try {
            $asset = \App\Models\SyncedAsset::where('asset_id', $id)->first();
            
            if (!$asset) {
                return redirect()->route('inventaris-aset.index')
                    ->with('error', 'Aset tidak ditemukan di database lokal. Pastikan sudah sinkronisasi.');
            }

            return view('pages.shared.assets.borrow-form', [
                'asset'      => $asset->toArray(),
                'isDisposed' => $asset->current_status === 'Dihapuskan',
            ]);
        } catch (\Exception $e) {
            Log::error('AssetController showBorrowForm (Local): ' . $e->getMessage());
            return redirect()->route('inventaris-aset.index')
                ->with('error', 'Gagal memuat form peminjaman aset.');
        }
    }

    /**
     * Kirim permintaan peminjaman ke API Aplikasi_Aset.
     */
    public function requestBorrow(Request $request, int $id)
    {
        $request->validate([
            'purpose'    => 'required|string|max:1000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        try {
            $response = Http::timeout(10)->post("{$this->apiBase}/api/borrow-requests", [
                'asset_id'          => $id,
                'requester_user_id' => (string) $user->id,
                'requester_name'    => $user->name,
                'requester_role'    => session('active_role'),
                'requester_app'     => 'aplikasi-izin',
                'purpose'           => $request->input('purpose'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'notes'             => $request->input('notes'),
            ]);

            if ($response->status() === 422) {
                return back()->withErrors(['api' => $response->json('message', 'Validasi gagal.')])
                    ->withInput();
            }

            if ($response->failed()) {
                return back()->withErrors(['api' => 'Gagal mengirim permintaan peminjaman.'])->withInput();
            }

            return redirect()->route('inventaris-aset.borrow-history')
                ->with('success', 'Permintaan peminjaman berhasil diajukan! Tunggu persetujuan admin Aset.');

        } catch (\Exception $e) {
            Log::error('AssetController requestBorrow: ' . $e->getMessage());
            return back()->withErrors(['api' => 'Koneksi ke server Aplikasi Aset gagal.'])->withInput();
        }
    }

    /**
     * Riwayat permintaan peminjaman milik user.
     */
    public function borrowHistory(Request $request)
    {
        $user   = Auth::user();
        $status = $request->input('status');

        try {
            $response = Http::timeout(10)->get("{$this->apiBase}/api/borrow-requests", [
                'requester_user_id' => (string) $user->id,
                'app'               => 'aplikasi-izin',
                'status'            => $status,
                'per_page'          => 15,
                'page'              => $request->input('page', 1),
            ]);

            if ($response->failed()) {
                return view('pages.shared.assets.borrow-history', [
                    'requests' => null,
                    'error'    => 'Gagal memuat riwayat peminjaman.',
                    'filters'  => [],
                ]);
            }

            return view('pages.shared.assets.borrow-history', [
                'requests' => $response->json(),
                'error'    => null,
                'filters'  => ['status' => $status],
            ]);

        } catch (\Exception $e) {
            Log::error('AssetController borrowHistory: ' . $e->getMessage());
            return view('pages.shared.assets.borrow-history', [
                'requests' => null,
                'error'    => 'Koneksi ke server gagal: ' . $e->getMessage(),
                'filters'  => [],
            ]);
        }
    }
}
