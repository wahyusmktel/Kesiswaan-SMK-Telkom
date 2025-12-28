<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\NdeRefJenis;
use Illuminate\Http\Request;

class NdeReferensiController extends Controller
{
    public function index()
    {
        $jenisNde = NdeRefJenis::all();
        return view('pages.sdm.nde-referensi.index', compact('jenisNde'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:nde_ref_jenis,kode',
        ]);

        NdeRefJenis::create($request->all());

        return redirect()->back()->with('success', 'Jenis NDE berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jenis = NdeRefJenis::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:nde_ref_jenis,kode,' . $id,
        ]);

        $jenis->update($request->all());

        return redirect()->back()->with('success', 'Jenis NDE berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jenis = NdeRefJenis::findOrFail($id);
        
        if ($jenis->notaDinas()->exists()) {
            return redirect()->back()->with('error', 'Jenis NDE tidak dapat dihapus karena sudah digunakan dalam nota dinas.');
        }

        $jenis->delete();

        return redirect()->back()->with('success', 'Jenis NDE berhasil dihapus.');
    }
}
