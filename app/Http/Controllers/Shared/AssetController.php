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
     * Menampilkan daftar semua aset dari Aplikasi Aset.
     */
    public function index(Request $request)
    {
        $params = array_filter([
            'search'       => $request->input('search'),
            'category_id'  => $request->input('category_id'),
            'status'       => $request->input('status'),
            'purchase_year'=> $request->input('purchase_year'),
            'per_page'     => $request->input('per_page', 15),
            'page'         => $request->input('page', 1),
        ]);

        try {
            $response = Http::timeout(10)->get("{$this->apiBase}/api/assets", $params);

            if ($response->failed()) {
                return view('pages.shared.assets.index', [
                    'error'      => 'Gagal menghubungi server Aplikasi Aset. Pastikan server berjalan.',
                    'assets'     => null,
                    'stats'      => null,
                    'categories' => collect(),
                    'years'      => collect(),
                ]);
            }

            $data = $response->json();

            return view('pages.shared.assets.index', [
                'assets'     => $data['assets'] ?? null,
                'stats'      => $data['stats'] ?? null,
                'categories' => collect($data['categories'] ?? []),
                'years'      => collect($data['years'] ?? []),
                'error'      => null,
                'filters'    => $params,
            ]);
        } catch (\Exception $e) {
            Log::error('Asset API Error (index): ' . $e->getMessage());
            return view('pages.shared.assets.index', [
                'error'      => 'Koneksi ke Aplikasi Aset gagal: ' . $e->getMessage(),
                'assets'     => null,
                'stats'      => null,
                'categories' => collect(),
                'years'      => collect(),
                'filters'    => [],
            ]);
        }
    }

    /**
     * Menampilkan detail satu aset.
     */
    public function show(int $id)
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiBase}/api/assets/{$id}");

            if ($response->status() === 404) {
                abort(404, 'Aset tidak ditemukan.');
            }

            if ($response->failed()) {
                return view('pages.shared.assets.show', [
                    'error' => 'Gagal mengambil data aset dari server.',
                    'asset' => null,
                ]);
            }

            $data = $response->json();

            return view('pages.shared.assets.show', [
                'asset'      => $data['asset'] ?? null,
                'isDisposed' => $data['isDisposed'] ?? false,
                'error'      => null,
            ]);
        } catch (\Exception $e) {
            Log::error('Asset API Error (show): ' . $e->getMessage());
            return view('pages.shared.assets.show', [
                'error' => 'Koneksi ke Aplikasi Aset gagal: ' . $e->getMessage(),
                'asset' => null,
            ]);
        }
    }

    /**
     * Tampilkan form pengajuan peminjaman aset.
     */
    public function showBorrowForm(int $id)
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiBase}/api/assets/{$id}");
            if ($response->status() === 404) abort(404, 'Aset tidak ditemukan.');
            if ($response->failed()) {
                return redirect()->route('inventaris-aset.show', $id)
                    ->with('error', 'Gagal memuat data aset.');
            }
            $data = $response->json();
            return view('pages.shared.assets.borrow-form', [
                'asset'      => $data['asset'] ?? null,
                'isDisposed' => $data['isDisposed'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error('AssetController showBorrowForm: ' . $e->getMessage());
            return redirect()->route('inventaris-aset.show', $id)
                ->with('error', 'Koneksi ke server Aplikasi Aset gagal.');
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
