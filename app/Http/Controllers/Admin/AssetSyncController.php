<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SyncedAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssetSyncController extends Controller
{
    /**
     * Tampilkan halaman sinkronisasi aset.
     */
    public function index()
    {
        $lastSync = SyncedAsset::max('last_synced_at');
        $totalAssets = SyncedAsset::count();
        
        return view('pages.admin.asset-sync.index', compact('lastSync', 'totalAssets'));
    }

    /**
     * Proses tarik data dari Aplikasi_Aset dan simpan di lokal.
     */
    public function sync()
    {
        try {
            $apiUrl = config('asset.api_url') . '/api/assets';
            
            // Mengambil semua data dari API
            $response = Http::timeout(60)->get($apiUrl, ['per_page' => 1000]);
            
            if ($response->failed()) {
                Log::error('AssetSyncController: Failed to fetch assets API. Status: ' . $response->status());
                return redirect()->back()->with('error', 'Gagal terhubung ke API Aplikasi Aset.');
            }

            $assets = $response->json('data.data');

            if (!is_array($assets)) {
                return redirect()->back()->with('error', 'Format data dari API tidak valid.');
            }

            $syncedCount = 0;
            $now = now();

            foreach ($assets as $assetData) {
                SyncedAsset::updateOrCreate(
                    ['asset_id' => $assetData['id']],
                    [
                        'asset_code_ypt'   => $assetData['asset_code_ypt'] ?? null,
                        'name'             => $assetData['name'] ?? 'Unknown Asset',
                        'category'         => $assetData['category'] ?? null,
                        'condition'        => $assetData['condition'] ?? null,
                        'current_status'   => $assetData['current_status'] ?? 'Tersedia',
                        'institution'      => $assetData['institution'] ?? null,
                        'building'         => $assetData['building'] ?? null,
                        'room'             => $assetData['room'] ?? null,
                        'faculty'          => $assetData['faculty'] ?? null,
                        'department'       => $assetData['department'] ?? null,
                        'person_in_charge' => $assetData['person_in_charge'] ?? null,
                        'asset_function'   => $assetData['asset_function'] ?? null,
                        'funding_source'   => $assetData['funding_source'] ?? null,
                        'sequence_number'  => $assetData['sequence_number'] ?? null,
                        'status'           => $assetData['status'] ?? 'Aktif',
                        'purchase_cost'    => $assetData['purchase_cost'] ?? null,
                        'salvage_value'    => $assetData['salvage_value'] ?? null,
                        'useful_life'      => $assetData['useful_life'] ?? null,
                        'book_value'       => $assetData['book_value'] ?? null,
                        'disposal_date'    => $assetData['disposal_date'] ?? null,
                        'disposal_method'  => $assetData['disposal_method'] ?? null,
                        'disposal_reason'  => $assetData['disposal_reason'] ?? null,
                        'last_synced_at'   => $now,
                    ]
                );
                $syncedCount++;
            }

            return redirect()->back()->with('success', "Sinkronisasi berhasil! $syncedCount aset telah diperbarui.");

        } catch (\Exception $e) {
            Log::error('AssetSyncController Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage());
        }
    }
}
