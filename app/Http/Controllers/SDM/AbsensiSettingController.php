<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\AbsensiSetting;
use Illuminate\Http\Request;

class AbsensiSettingController extends Controller
{
    public function index()
    {
        $setting = AbsensiSetting::getSetting();
        return view('pages.sdm.absensi.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk_batas'   => 'required|date_format:H:i',
            'jam_keluar_batas'  => 'required|date_format:H:i',
            'latitude_sekolah'  => 'required|numeric|between:-90,90',
            'longitude_sekolah' => 'required|numeric|between:-180,180',
            'radius_meter'      => 'required|integer|min:10|max:5000',
        ]);

        $setting = AbsensiSetting::getSetting();
        $setting->update([
            'jam_masuk_batas'   => $request->jam_masuk_batas . ':00',
            'jam_keluar_batas'  => $request->jam_keluar_batas . ':00',
            'latitude_sekolah'  => $request->latitude_sekolah,
            'longitude_sekolah' => $request->longitude_sekolah,
            'radius_meter'      => $request->radius_meter,
        ]);

        return redirect()->route('sdm.absensi-settings.index')
            ->with('success', 'Pengaturan absensi berhasil disimpan!');
    }
}
