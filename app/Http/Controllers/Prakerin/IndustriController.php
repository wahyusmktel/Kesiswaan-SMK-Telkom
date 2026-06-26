<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinIndustri;
use Illuminate\Http\Request;

class IndustriController extends Controller
{
    public function index(Request $request)
    {
        $query = PrakerinIndustri::query();
        if ($request->filled('search')) {
            $query->where('nama_industri', 'like', '%' . $request->search . '%')
                ->orWhere('kota', 'like', '%' . $request->search . '%');
        }
        $industri = $query->latest()->paginate(10);
        return view('pages.prakerin.industri.index', compact('industri'));
    }

    public function create()
    {
        return view('pages.prakerin.industri.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_industri' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi_code' => 'nullable|string|max:20',
            'provinsi_name' => 'nullable|string|max:255',
            'kabupaten_code' => 'nullable|string|max:20',
            'kabupaten_name' => 'nullable|string|max:255',
            'kecamatan_code' => 'nullable|string|max:20',
            'kecamatan_name' => 'nullable|string|max:255',
            'desa_code' => 'nullable|string|max:20',
            'desa_name' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email_pic' => 'nullable|email|max:255',
            'nama_pic' => 'nullable|string|max:255',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'tanggal_akhir_mou' => 'nullable|date|after_or_equal:tanggal_mou',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'catatan_mou' => 'nullable|string',
        ]);
        PrakerinIndustri::create($request->all() + ['is_mou_active' => $request->boolean('is_mou_active', true)]);
        toast('Data industri berhasil ditambahkan.', 'success');
        return redirect()->route('prakerin.industri.index');
    }

    public function edit(PrakerinIndustri $industri)
    {
        return view('pages.prakerin.industri.edit', compact('industri'));
    }

    public function update(Request $request, PrakerinIndustri $industri)
    {
        $request->validate([
            'nama_industri' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi_code' => 'nullable|string|max:20',
            'provinsi_name' => 'nullable|string|max:255',
            'kabupaten_code' => 'nullable|string|max:20',
            'kabupaten_name' => 'nullable|string|max:255',
            'kecamatan_code' => 'nullable|string|max:20',
            'kecamatan_name' => 'nullable|string|max:255',
            'desa_code' => 'nullable|string|max:20',
            'desa_name' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'email_pic' => 'nullable|email|max:255',
            'nama_pic' => 'nullable|string|max:255',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'tanggal_akhir_mou' => 'nullable|date|after_or_equal:tanggal_mou',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'catatan_mou' => 'nullable|string',
        ]);
        $industri->update($request->all() + ['is_mou_active' => $request->boolean('is_mou_active')]);
        toast('Data industri berhasil diperbarui.', 'success');
        return redirect()->route('prakerin.industri.index');
    }

    public function destroy(PrakerinIndustri $industri)
    {
        $industri->delete();
        toast('Data industri berhasil dihapus.', 'success');
        return redirect()->route('prakerin.industri.index');
    }
}
