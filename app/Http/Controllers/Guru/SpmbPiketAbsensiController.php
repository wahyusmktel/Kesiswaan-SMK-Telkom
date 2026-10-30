<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SpmbPiketAbsensi;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpmbPiketAbsensiController extends Controller
{
    /**
     * Display the SPMB Piket attendance portal for the logged-in teacher / officer.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $guruId = $user->masterGuru?->id;

        // Fetch officer record(s) for the current user
        $petugasRecords = SpmbPiketPetugas::where('user_id', $user->id)
            ->when($guruId, fn ($q) => $q->orWhere('master_guru_id', $guruId))
            ->with([
                'kelompok.tanggals' => function ($q) {
                    $q->orderBy('tanggal', 'asc');
                },
                'absensis.tanggal',
            ])
            ->get();

        // If user is Super Admin and has no officer record, load first available officer or sample
        $isOfficer = $petugasRecords->isNotEmpty();
        if (! $isOfficer && $user->hasRole('Super Admin')) {
            $samplePetugas = SpmbPiketPetugas::with(['kelompok.tanggals' => fn ($q) => $q->orderBy('tanggal', 'asc'), 'absensis.tanggal'])->first();
            if ($samplePetugas) {
                $petugasRecords = collect([$samplePetugas]);
            }
        }

        $activePetugas = $petugasRecords->first();
        $assignedKelompok = $activePetugas?->kelompok;
        $allTanggals = $assignedKelompok ? $assignedKelompok->tanggals : collect();

        $today = Carbon::today()->toDateString();

        // Check if there is a schedule today
        $jadwalHariIni = $allTanggals->firstWhere('tanggal', $today);

        // Upcoming schedule
        $jadwalBerikutnya = $allTanggals->first(function ($t) use ($today) {
            return Carbon::parse($t->tanggal)->gte($today);
        });

        // Attendance history
        $historyAbsensi = $activePetugas
            ? SpmbPiketAbsensi::where('spmb_piket_petugas_id', $activePetugas->id)
                ->with('tanggal')
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        // Tanggals that have not been filled yet
        $sudahAbsenTanggalIds = $historyAbsensi->pluck('spmb_piket_tanggal_id')->toArray();
        $tanggalsBelumAbsen = $allTanggals->filter(function ($t) use ($sudahAbsenTanggalIds) {
            return ! in_array($t->id, $sudahAbsenTanggalIds);
        });

        // Determine active/selected schedule date for viewing candidate list
        $targetTanggal = $jadwalHariIni ?: ($jadwalBerikutnya ?: $allTanggals->first());
        $selectedTanggalId = $request->get('tanggal_id', $targetTanggal?->id);

        $selectedTanggal = $allTanggals->firstWhere('id', $selectedTanggalId);
        if (! $selectedTanggal && $selectedTanggalId) {
            $selectedTanggal = SpmbPiketTanggal::with('kelompok')->find($selectedTanggalId);
        }
        if (! $selectedTanggal && $targetTanggal) {
            $selectedTanggal = $targetTanggal;
        }

        $pesertaSeleksis = $selectedTanggal
            ? \App\Models\SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $selectedTanggal->id)
                ->orderBy('nomor_pendaftaran', 'asc')
                ->get()
            : collect();

        return view('pages.guru.spmb-piket.absensi', compact(
            'user',
            'isOfficer',
            'petugasRecords',
            'activePetugas',
            'assignedKelompok',
            'allTanggals',
            'jadwalHariIni',
            'jadwalBerikutnya',
            'historyAbsensi',
            'tanggalsBelumAbsen',
            'selectedTanggal',
            'pesertaSeleksis'
        ));
    }

    /**
     * Store teacher's attendance with selfie / evidence photo.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $guruId = $user->masterGuru?->id;

        $validated = $request->validate([
            'spmb_piket_petugas_id' => 'required|exists:spmb_piket_petugas,id',
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'status' => 'required|in:Hadir,Izin,Sakit',
            'foto_eviden' => 'nullable|image|max:5120',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $petugas = SpmbPiketPetugas::findOrFail($validated['spmb_piket_petugas_id']);

        // Check ownership unless Super Admin
        if (! $user->hasRole('Super Admin')) {
            $isOwner = ($petugas->user_id === $user->id) || ($guruId && $petugas->master_guru_id === $guruId);
            if (! $isOwner) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk mengisi absensi petugas ini.');
            }
        }

        // Upload evidence photo
        $fotoPath = null;
        if ($request->hasFile('foto_eviden')) {
            $fotoPath = $request->file('foto_eviden')->store('spmb-piket-eviden', 'public');
        } elseif ($validated['status'] === 'Hadir') {
            return redirect()->back()->with('error', 'Foto bukti / eviden kehadiran wajib diunggah saat status Hadir.');
        }

        SpmbPiketAbsensi::updateOrCreate(
            [
                'spmb_piket_petugas_id' => $petugas->id,
                'spmb_piket_tanggal_id' => $validated['spmb_piket_tanggal_id'],
            ],
            [
                'user_id' => $user->id,
                'status' => $validated['status'],
                'jam_absen' => now()->format('H:i:s'),
                'foto_eviden' => $fotoPath,
                'keterangan' => $validated['keterangan'] ?? 'Absensi mandiri petugas piket SPMB',
                'metode_input' => 'mandiri',
                'created_by_user_id' => $user->id,
            ]
        );

        return redirect()->back()->with('success', 'Absensi kehadiran piket SPMB Anda berhasil dicatat. Terima kasih atas dedikasinya!');
    }
}
