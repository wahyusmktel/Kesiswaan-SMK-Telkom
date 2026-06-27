<?php

namespace App\Http\Controllers\Prakerin;

use App\Http\Controllers\Controller;
use App\Models\PrakerinAbsensi;
use App\Models\PrakerinJurnal;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JurnalSiswaController extends Controller
{
    public function index()
    {
        $penempatan = $this->activePenempatan(false);

        $jurnals = collect();
        $absensis = collect();
        if ($penempatan) {
            $jurnals = PrakerinJurnal::where('prakerin_penempatan_id', $penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->limit(8)
                ->get();
            $absensis = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
                ->orderBy('tanggal', 'desc')
                ->limit(5)
                ->get();
        }

        $setting = PrakerinSetting::first();
        $effectiveSchedule = $penempatan ? $this->effectiveSchedule($penempatan, $setting) : [];
        $absensiHariIni = $penempatan
            ? PrakerinAbsensi::firstOrNew([
                'prakerin_penempatan_id' => $penempatan->id,
                'tanggal' => now()->toDateString(),
            ])
            : null;

        return view('pages.prakerin.jurnal-siswa.index', compact('penempatan', 'jurnals', 'absensis', 'setting', 'effectiveSchedule', 'absensiHariIni'));
    }

    public function create()
    {
        $penempatan = $this->activePenempatan();
        $setting = PrakerinSetting::first();
        $effectiveSchedule = $this->effectiveSchedule($penempatan, $setting);
        $today = now()->toDateString();
        $absensiHariIni = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
            ->whereDate('tanggal', $today)
            ->first();
        $jurnalHariIni = PrakerinJurnal::where('prakerin_penempatan_id', $penempatan->id)
            ->whereDate('tanggal', $today)
            ->latest()
            ->first();

        return view('pages.prakerin.jurnal-siswa.create', compact('penempatan', 'setting', 'effectiveSchedule', 'absensiHariIni', 'jurnalHariIni', 'today'));
    }

    public function absensiHistory()
    {
        $penempatan = $this->activePenempatan();
        $absensis = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('pages.prakerin.jurnal-siswa.absensi-history', compact('penempatan', 'absensis'));
    }

    public function absensiPdf()
    {
        $penempatan = $this->activePenempatan();
        $absensis = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
            ->orderBy('tanggal')
            ->get();
        $setting = PrakerinSetting::first();
        $effectiveSchedule = $this->effectiveSchedule($penempatan, $setting);

        $pdf = Pdf::loadView('pages.prakerin.jurnal-siswa.absensi-pdf', compact('penempatan', 'absensis', 'effectiveSchedule'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekap-absensi-pkl-' . str($penempatan->siswa?->nis ?? 'siswa')->slug() . '.pdf');
    }

    public function store(Request $request)
    {
        $penempatan = $this->activePenempatan();

        $request->validate([
            'tanggal' => 'required|date',
            'kegiatan_dilakukan' => 'required|string',
            'kompetensi_yang_didapat' => 'required|string',
            'foto_kegiatan' => 'nullable|image|max:10240',
        ]);

        $absensi = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
            ->whereDate('tanggal', $request->tanggal)
            ->whereNotNull('check_in_at')
            ->first();

        if (! $absensi) {
            return back()
                ->withInput()
                ->withErrors(['tanggal' => 'Anda harus check-in absensi terlebih dahulu sebelum menambahkan jurnal pada tanggal tersebut.']);
        }

        $path = null;
        if ($request->hasFile('foto_kegiatan')) {
            $path = $request->file('foto_kegiatan')->store('public/jurnal_prakerin');
        }

        PrakerinJurnal::create([
            'prakerin_penempatan_id' => $penempatan->id,
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
        $todayAbsensi = PrakerinAbsensi::where('prakerin_penempatan_id', $penempatan->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        if (! $todayAbsensi?->check_in_at) {
            return back()->withErrors(['absensi' => 'Anda harus check-in terlebih dahulu sebelum check-out.']);
        }

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

    private function activePenempatan(bool $abort = true): ?PrakerinPenempatan
    {
        $penempatan = PrakerinPenempatan::where('master_siswa_id', Auth::user()->masterSiswa?->id)
            ->where('status', 'aktif')
            ->with(['siswa', 'industri', 'guruPembimbing', 'rombelPkl'])
            ->first();

        if (! $penempatan && $abort) {
            abort(403, 'Anda belum termapping ke rombel PKL aktif.');
        }

        return $penempatan;
    }

    private function effectiveSchedule(PrakerinPenempatan $penempatan, ?PrakerinSetting $setting): array
    {
        $rombel = $penempatan->rombelPkl;

        return [
            'period_source' => $rombel?->gunakan_periode_kustom ? 'Rombel kustom' : 'Global',
            'tanggal_mulai' => $rombel?->gunakan_periode_kustom ? $rombel->tanggal_mulai : $setting?->tanggal_mulai,
            'tanggal_selesai' => $rombel?->gunakan_periode_kustom ? $rombel->tanggal_selesai : $setting?->tanggal_selesai,
            'attendance_source' => $rombel?->gunakan_waktu_absensi_kustom ? 'Rombel kustom' : 'Global',
            'jam_check_in_mulai' => $rombel?->gunakan_waktu_absensi_kustom ? $rombel->jam_check_in_mulai : $setting?->jam_check_in_mulai,
            'jam_check_in_selesai' => $rombel?->gunakan_waktu_absensi_kustom ? $rombel->jam_check_in_selesai : $setting?->jam_check_in_selesai,
            'jam_check_out_mulai' => $rombel?->gunakan_waktu_absensi_kustom ? $rombel->jam_check_out_mulai : $setting?->jam_check_out_mulai,
            'jam_check_out_selesai' => $rombel?->gunakan_waktu_absensi_kustom ? $rombel->jam_check_out_selesai : $setting?->jam_check_out_selesai,
        ];
    }
}
