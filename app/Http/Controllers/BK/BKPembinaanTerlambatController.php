<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BKPembinaanTerlambatController extends Controller
{
    public function store(Request $request, Keterlambatan $keterlambatan)
    {
        $request->validate([
            'catatan_bk' => 'required|string|min:10',
        ]);

        if (!Auth::user()->hasRole('Guru BK')) {
            abort(403, 'Akses ditolak. Hanya Guru BK yang dapat melakukan pembinaan lanjutan.');
        }

        $keterlambatan->update([
            'catatan_bk' => $request->catatan_bk,
            'pembinaan_oleh_bk_id' => Auth::id(),
            'waktu_pembinaan_bk' => now(),
            'status' => 'selesai',
        ]);

        toast('Pembinaan lanjutan oleh BK berhasil dicatat. Status keterlambatan ditandai Selesai.', 'success');
        return back();
    }
}
