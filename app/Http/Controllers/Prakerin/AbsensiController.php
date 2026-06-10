<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinAbsensi;
use App\Models\PrakerinRombel;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $rombels = PrakerinRombel::orderBy('nama_rombel')->get();
        $absensis = PrakerinAbsensi::with(['penempatan.siswa', 'penempatan.rombelPkl.industri'])
            ->when($request->filled('tanggal'), fn ($q) => $q->whereDate('tanggal', $request->tanggal))
            ->when($request->filled('rombel_id'), fn ($q) => $q->whereHas('penempatan', fn ($p) => $p->where('prakerin_rombel_id', $request->rombel_id)))
            ->when($request->filled('search'), fn ($q) => $q->whereHas('penempatan.siswa', fn ($s) => $s->where('nama_lengkap', 'like', '%' . $request->search . '%')->orWhere('nis', 'like', '%' . $request->search . '%')))
            ->latest('tanggal')
            ->paginate(20)
            ->withQueryString();

        return view('pages.prakerin.absensi.index', compact('absensis', 'rombels'));
    }
}
