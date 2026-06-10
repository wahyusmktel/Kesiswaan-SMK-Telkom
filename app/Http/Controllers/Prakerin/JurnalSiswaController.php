<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinAbsensi;
use App\Models\PrakerinJurnal;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalSiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $penempatan = PrakerinPenempatan::where('master_siswa_id', $user->masterSiswa?->id)
            ->where('status', 'aktif')
            ->first();

        $jurnals = collect();
        if ($penempatan) {
            $jurnals = PrakerinJurnal::where('prakerin_penempatan_id', $penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->paginate(10);
        }

        $setting = PrakerinSetting::first();
        $absensiHariIni = $penempatan
            ? PrakerinAbsensi::firstOrNew([
                'prakerin_penempatan_id' => $penempatan->id,
                'tanggal' => now()->toDateString(),
            ])
            : null;

        return view('pages.prakerin.jurnal-siswa.index', compact('penempatan', 'jurnals', 'setting', 'absensiHariIni'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'prakerin_penempatan_id' => 'required|exists:prakerin_penempatans,id',
            'tanggal' => 'required|date',
            'kegiatan_dilakukan' => 'required|string',
            'kompetensi_yang_didapat' => 'required|string',
            'foto_kegiatan' => 'nullable|image|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('foto_kegiatan')) {
            $path = $request->file('foto_kegiatan')->store('public/jurnal_prakerin');
        }

        PrakerinJurnal::create([
            'prakerin_penempatan_id' => $request->prakerin_penempatan_id,
            'tanggal' => $request->tanggal,
            'kegiatan_dilakukan' => $request->kegiatan_dilakukan,
            'kompetensi_yang_didapat' => $request->kompetensi_yang_didapat,
            'foto_kegiatan' => $path,
        ]);

        toast('Jurnal harian berhasil disimpan.', 'success');
        return redirect()->route('siswa.jurnal-prakerin.index');
    }

    public function checkIn(Request $request)
    {
        $penempatan = $this->activePenempatan();
        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|max:10240',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('photo')->store('public/absensi_prakerin');

        PrakerinAbsensi::updateOrCreate(
            ['prakerin_penempatan_id' => $penempatan->id, 'tanggal' => now()->toDateString()],
            [
                'check_in_at' => now()->format('H:i:s'),
                'check_in_latitude' => $data['latitude'],
                'check_in_longitude' => $data['longitude'],
                'check_in_photo' => $path,
                'status' => 'hadir',
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        toast('Check-in PKL berhasil disimpan.', 'success');

        return back();
    }

    public function checkOut(Request $request)
    {
        $penempatan = $this->activePenempatan();
        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|max:10240',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $path = $request->file('photo')->store('public/absensi_prakerin');

        PrakerinAbsensi::updateOrCreate(
            ['prakerin_penempatan_id' => $penempatan->id, 'tanggal' => now()->toDateString()],
            [
                'check_out_at' => now()->format('H:i:s'),
                'check_out_latitude' => $data['latitude'],
                'check_out_longitude' => $data['longitude'],
                'check_out_photo' => $path,
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        toast('Check-out PKL berhasil disimpan.', 'success');

        return back();
    }

    private function activePenempatan(): PrakerinPenempatan
    {
        $penempatan = PrakerinPenempatan::where('master_siswa_id', Auth::user()->masterSiswa?->id)
            ->where('status', 'aktif')
            ->first();

        abort_if(!$penempatan, 403, 'Anda belum termapping ke rombel PKL aktif.');

        return $penempatan;
    }
}
