<?php

namespace App\Http\Controllers\Hubin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinIndustri;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IndustriKerjasamaController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $in60Days = Carbon::today()->addDays(60);

        // KPI Metrik
        $totalIndustri = PrakerinIndustri::count();
        
        $totalAktif = PrakerinIndustri::where('is_mou_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('tanggal_akhir_mou')
                  ->orWhere('tanggal_akhir_mou', '>=', $today);
            })->count();

        $totalSegeraBerakhir = PrakerinIndustri::where('is_mou_active', true)
            ->whereNotNull('tanggal_akhir_mou')
            ->whereBetween('tanggal_akhir_mou', [$today, $in60Days])
            ->count();

        $totalBerakhir = PrakerinIndustri::where(function ($q) use ($today) {
            $q->where('is_mou_active', false)
              ->orWhere(function ($sub) use ($today) {
                  $sub->whereNotNull('tanggal_akhir_mou')
                      ->where('tanggal_akhir_mou', '<', $today);
              });
        })->count();

        // Query Data
        $query = PrakerinIndustri::query();

        // Pencarian
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_industri', 'like', "%{$s}%")
                  ->orWhere('kota', 'like', "%{$s}%")
                  ->orWhere('nama_pic', 'like', "%{$s}%")
                  ->orWhere('nomor_mou', 'like', "%{$s}%")
                  ->orWhere('bidang_usaha', 'like', "%{$s}%");
            });
        }

        // Filter Status MoU
        if ($request->filled('status_mou') && $request->status_mou !== 'all') {
            switch ($request->status_mou) {
                case 'aktif':
                    $query->where('is_mou_active', true)
                        ->where(function ($q) use ($today, $in60Days) {
                            $q->whereNull('tanggal_akhir_mou')
                              ->orWhere('tanggal_akhir_mou', '>', $in60Days);
                        });
                    break;
                case 'segera_berakhir':
                    $query->where('is_mou_active', true)
                        ->whereNotNull('tanggal_akhir_mou')
                        ->whereBetween('tanggal_akhir_mou', [$today, $in60Days]);
                    break;
                case 'berakhir':
                    $query->where(function ($q) use ($today) {
                        $q->whereNotNull('tanggal_akhir_mou')
                          ->where('tanggal_akhir_mou', '<', $today);
                    });
                    break;
                case 'nonaktif':
                    $query->where('is_mou_active', false);
                    break;
            }
        }

        // Filter Bidang Usaha
        if ($request->filled('bidang_usaha') && $request->bidang_usaha !== 'all') {
            $query->where('bidang_usaha', $request->bidang_usaha);
        }

        // Filter Bentuk Kerjasama
        if ($request->filled('bentuk_kerjasama') && $request->bentuk_kerjasama !== 'all') {
            $bentuk = $request->bentuk_kerjasama;
            $query->whereJsonContains('bentuk_kerjasama', $bentuk);
        }

        $industriList = $query->latest()->paginate(10)->withQueryString();

        $bentukKerjasamaOptions = PrakerinIndustri::BENTUK_KERJASAMA_OPTIONS;
        $bidangUsahaOptions = PrakerinIndustri::BIDANG_USAHA_OPTIONS;

        return view('pages.hubin.industri.index', compact(
            'industriList',
            'totalIndustri',
            'totalAktif',
            'totalSegeraBerakhir',
            'totalBerakhir',
            'bentukKerjasamaOptions',
            'bidangUsahaOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_industri' => 'required|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'nama_pic' => 'nullable|string|max:255',
            'jabatan_pic' => 'nullable|string|max:255',
            'no_hp_pic' => 'nullable|string|max:30',
            'email_pic' => 'nullable|email|max:255',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'tanggal_akhir_mou' => 'nullable|date|after_or_equal:tanggal_mou',
            'is_mou_active' => 'nullable|boolean',
            'bentuk_kerjasama' => 'nullable|array',
            'catatan_mou' => 'nullable|string',
            'file_mou' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file_mou')) {
            $path = $request->file('file_mou')->store('hubin/mou', 'public');
            $validated['file_mou'] = $path;
        }

        $validated['is_mou_active'] = $request->boolean('is_mou_active', true);

        PrakerinIndustri::create($validated);

        if (function_exists('toast')) {
            toast('Data industri kerjasama berhasil ditambahkan.', 'success');
        }

        return redirect()->route('hubin.industri.index')->with('success', 'Data industri kerjasama berhasil ditambahkan.');
    }

    public function update(Request $request, PrakerinIndustri $industri)
    {
        $validated = $request->validate([
            'nama_industri' => 'required|string|max:255',
            'bidang_usaha' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'nama_pic' => 'nullable|string|max:255',
            'jabatan_pic' => 'nullable|string|max:255',
            'no_hp_pic' => 'nullable|string|max:30',
            'email_pic' => 'nullable|email|max:255',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'tanggal_akhir_mou' => 'nullable|date|after_or_equal:tanggal_mou',
            'is_mou_active' => 'nullable|boolean',
            'bentuk_kerjasama' => 'nullable|array',
            'catatan_mou' => 'nullable|string',
            'file_mou' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file_mou')) {
            if ($industri->file_mou && Storage::disk('public')->exists($industri->file_mou)) {
                Storage::disk('public')->delete($industri->file_mou);
            }
            $path = $request->file('file_mou')->store('hubin/mou', 'public');
            $validated['file_mou'] = $path;
        }

        $validated['is_mou_active'] = $request->boolean('is_mou_active');

        $industri->update($validated);

        if (function_exists('toast')) {
            toast('Data industri kerjasama berhasil diperbarui.', 'success');
        }

        return redirect()->route('hubin.industri.index')->with('success', 'Data industri kerjasama berhasil diperbarui.');
    }

    public function destroy(PrakerinIndustri $industri)
    {
        if ($industri->file_mou && Storage::disk('public')->exists($industri->file_mou)) {
            Storage::disk('public')->delete($industri->file_mou);
        }

        $industri->delete();

        if (function_exists('toast')) {
            toast('Data industri berhasil dihapus.', 'success');
        }

        return redirect()->route('hubin.industri.index')->with('success', 'Data industri berhasil dihapus.');
    }

    public function downloadMou(PrakerinIndustri $industri)
    {
        if (!$industri->file_mou || !Storage::disk('public')->exists($industri->file_mou)) {
            return back()->with('error', 'Dokumen MoU tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $industri->file_mou,
            'MoU-' . \Illuminate\Support\Str::slug($industri->nama_industri) . '.' . pathinfo($industri->file_mou, PATHINFO_EXTENSION)
        );
    }
}
