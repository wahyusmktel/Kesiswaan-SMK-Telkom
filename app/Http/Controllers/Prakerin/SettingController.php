<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = PrakerinSetting::firstOrCreate([]);

        return view('pages.prakerin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_check_in_mulai' => 'nullable|date_format:H:i',
            'jam_check_in_selesai' => 'nullable|date_format:H:i',
            'jam_check_out_mulai' => 'nullable|date_format:H:i',
            'jam_check_out_selesai' => 'nullable|date_format:H:i',
            'instruksi_jurnal' => 'nullable|string',
        ]);

        PrakerinSetting::firstOrCreate([])->update($data + [
            'wajib_foto_absensi' => $request->boolean('wajib_foto_absensi'),
            'wajib_lokasi' => $request->boolean('wajib_lokasi'),
        ]);

        toast('Seting PKL berhasil diperbarui.', 'success');

        return back();
    }
}
