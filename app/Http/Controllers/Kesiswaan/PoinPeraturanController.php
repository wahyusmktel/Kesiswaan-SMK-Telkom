<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\PoinCategory;
use App\Models\PoinPeraturan;
use Illuminate\Http\Request;

class PoinPeraturanController extends Controller
{
    public function index()
    {
        $categories = PoinCategory::with('peraturans')->get();
        $peraturans = PoinPeraturan::with('category')->paginate(10);
        return view('kesiswaan.poin.index', compact('categories', 'peraturans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'poin_category_id' => 'required|exists:poin_categories,id',
            'pasal' => 'required|string|max:255',
            'ayat' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'bobot_poin' => 'required|integer|min:0',
        ]);

        PoinPeraturan::create($request->all());

        return redirect()->back()->with('success', 'Peraturan berhasil ditambahkan.');
    }

    public function update(Request $request, PoinPeraturan $poinPeraturan)
    {
        $request->validate([
            'poin_category_id' => 'required|exists:poin_categories,id',
            'pasal' => 'required|string|max:255',
            'ayat' => 'nullable|string|max:255',
            'deskripsi' => 'required|string',
            'bobot_poin' => 'required|integer|min:0',
        ]);

        $poinPeraturan->update($request->all());

        return redirect()->back()->with('success', 'Peraturan berhasil diperbarui.');
    }

    public function destroy(PoinPeraturan $poinPeraturan)
    {
        $poinPeraturan->delete();
        return redirect()->back()->with('success', 'Peraturan berhasil dihapus.');
    }

    // Category methods (Handled in same controller for simplicity)
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        PoinCategory::create($request->all());

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }
}
