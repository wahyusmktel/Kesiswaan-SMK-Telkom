<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\AbsensiPegawai;
use App\Models\AbsensiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiSayaController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $today   = Carbon::today();
        $setting = AbsensiSetting::getSetting();

        $absensiHariIni = AbsensiPegawai::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        $riwayat = AbsensiPegawai::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get();

        return view('pages.shared.absensi.index', compact(
            'absensiHariIni',
            'riwayat',
            'setting',
            'today'
        ));
    }

    public function checkin(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user    = Auth::user();
        $today   = Carbon::today();
        $now     = Carbon::now();
        $setting = AbsensiSetting::getSetting();

        // Check if already checked in today
        $existing = AbsensiPegawai::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->waktu_checkin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini pada ' . $existing->waktu_checkin->format('H:i'),
            ], 422);
        }

        // Calculate distance from school
        $distance = AbsensiPegawai::haversineDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $setting->latitude_sekolah,
            (float) $setting->longitude_sekolah
        );

        $dalamRadius = $distance <= $setting->radius_meter;

        // Determine status
        $batasCheckin = Carbon::today()->setTimeFromTimeString($setting->jam_masuk_batas);
        $status = $now->greaterThan($batasCheckin) ? 'terlambat' : 'tepat_waktu';

        if ($existing) {
            $existing->update([
                'waktu_checkin'       => $now,
                'lat_checkin'         => $request->latitude,
                'lng_checkin'         => $request->longitude,
                'status'              => $status,
                'dalam_radius_checkin' => $dalamRadius,
            ]);
        } else {
            AbsensiPegawai::create([
                'user_id'              => $user->id,
                'tanggal'              => $today,
                'waktu_checkin'        => $now,
                'lat_checkin'          => $request->latitude,
                'lng_checkin'          => $request->longitude,
                'status'               => $status,
                'dalam_radius_checkin' => $dalamRadius,
            ]);
        }

        $message = $dalamRadius
            ? "Check-in berhasil! Status: " . ($status === 'tepat_waktu' ? 'Tepat Waktu ✓' : 'Terlambat ⚠')
            : "Check-in berhasil, namun Anda berada di luar radius sekolah ({$distance} m). Status: " . ($status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat');

        return response()->json([
            'success'       => true,
            'message'       => $message,
            'status'        => $status,
            'dalam_radius'  => $dalamRadius,
            'jarak_meter'   => round($distance),
            'waktu'         => $now->format('H:i'),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user    = Auth::user();
        $today   = Carbon::today();
        $now     = Carbon::now();
        $setting = AbsensiSetting::getSetting();

        $absensi = AbsensiPegawai::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$absensi || !$absensi->waktu_checkin) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini.',
            ], 422);
        }

        if ($absensi->waktu_checkout) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini pada ' . $absensi->waktu_checkout->format('H:i'),
            ], 422);
        }

        // Calculate distance from school
        $distance = AbsensiPegawai::haversineDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $setting->latitude_sekolah,
            (float) $setting->longitude_sekolah
        );

        $dalamRadius = $distance <= $setting->radius_meter;

        $absensi->update([
            'waktu_checkout'        => $now,
            'lat_checkout'          => $request->latitude,
            'lng_checkout'          => $request->longitude,
            'dalam_radius_checkout' => $dalamRadius,
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Check-out berhasil dicatat pada ' . $now->format('H:i'),
            'dalam_radius'  => $dalamRadius,
            'jarak_meter'   => round($distance),
            'waktu'         => $now->format('H:i'),
        ]);
    }
}
