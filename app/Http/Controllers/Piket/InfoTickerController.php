<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\InfoTicker;
use Illuminate\Http\Request;

class InfoTickerController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['konten' => 'required|string|max:500']);

        InfoTicker::create([
            'konten'     => $request->konten,
            'is_active'  => true,
            'created_by' => auth()->id(),
        ]);

        toast('Info running text berhasil ditambahkan.', 'success');
        return back();
    }

    public function toggle(InfoTicker $ticker)
    {
        $ticker->update(['is_active' => !$ticker->is_active]);
        return back();
    }

    public function destroy(InfoTicker $ticker)
    {
        $ticker->delete();
        toast('Info berhasil dihapus.', 'success');
        return back();
    }
}
