<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbPiketAbsensi;
use App\Models\SpmbPiketPengganti;
use App\Models\SpmbPiketPetugas;
use App\Models\SpmbPiketTanggal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpmbPiketAbsensiController extends Controller
{
    /**
     * Display the SPMB Piket attendance portal for the logged-in teacher / officer.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $guruId = $user->masterGuru?->id;

        // Fetch officer record(s) where current user is the permanent officer
        $petugasRecords = SpmbPiketPetugas::where('user_id', $user->id)
            ->when($guruId, fn ($q) => $q->orWhere('master_guru_id', $guruId))
            ->with([
                'kelompok.tanggals' => function ($q) {
                    $q->orderBy('tanggal', 'asc');
                },
                'absensis.tanggal',
            ])
            ->get();

        // Fetch replacements where current user is assigned as replacement officer
        $penggantiRecords = SpmbPiketPengganti::where('user_id', $user->id)
            ->when($guruId, fn ($q) => $q->orWhere('master_guru_id', $guruId))
            ->with(['tanggal.kelompok', 'petugas'])
            ->get();

        // If user is Super Admin and has no officer record, load first available officer
        $isOfficer = $petugasRecords->isNotEmpty() || $penggantiRecords->isNotEmpty();
        if (! $isOfficer && $user->hasRole('Super Admin')) {
            $samplePetugas = SpmbPiketPetugas::with(['kelompok.tanggals' => fn ($q) => $q->orderBy('tanggal', 'asc'), 'absensis.tanggal'])->first();
            if ($samplePetugas) {
                $petugasRecords = collect([$samplePetugas]);
                $isOfficer = true;
            }
        }

        $activePetugas = $petugasRecords->first();
        if (! $activePetugas && $penggantiRecords->isNotEmpty()) {
            $activePetugas = $penggantiRecords->first()->petugas;
        }

        // Gather all accessible tanggals
        $allTanggals = collect();
        foreach ($petugasRecords as $p) {
            if ($p->kelompok && $p->kelompok->tanggals) {
                $allTanggals = $allTanggals->merge($p->kelompok->tanggals);
            }
        }
        foreach ($penggantiRecords as $pg) {
            if ($pg->tanggal) {
                $allTanggals->push($pg->tanggal);
            }
        }
        $allTanggals = $allTanggals->unique('id')->sortBy('tanggal')->values();

        $assignedKelompok = $activePetugas?->kelompok;

        $today = Carbon::today()->toDateString();

        // Check if there is a schedule today
        $jadwalHariIni = $allTanggals->firstWhere('tanggal', $today);

        // Upcoming schedule
        $jadwalBerikutnya = $allTanggals->first(function ($t) use ($today) {
            return Carbon::parse($t->tanggal)->gte($today);
        });

        // Attendance history
        $historyAbsensi = SpmbPiketAbsensi::where(function ($q) use ($user, $activePetugas) {
            $q->where('user_id', $user->id);
            if ($activePetugas) {
                $q->orWhere('spmb_piket_petugas_id', $activePetugas->id);
            }
        })
        ->with('tanggal')
        ->orderBy('created_at', 'desc')
        ->get();

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
            $selectedTanggal = SpmbPiketTanggal::with(['kelompok', 'laporanPetugasBy'])->find($selectedTanggalId);
        }
        if (! $selectedTanggal && $targetTanggal) {
            $selectedTanggal = $targetTanggal;
        }

        if ($selectedTanggal) {
            $selectedTanggal->loadMissing(['kelompok', 'laporanPetugasBy', 'penggantis']);
        }

        $pesertaSeleksis = $selectedTanggal
            ? SpmbPesertaSeleksi::where('spmb_piket_tanggal_id', $selectedTanggal->id)
                ->with('wawancaraSesi')
                ->orderBy('nomor_pendaftaran', 'asc')
                ->get()
            : collect();

        // Check if current user is replacement on the selected date
        $isPenggantiOnSelected = false;
        $penggantiInfo = null;
        if ($selectedTanggal) {
            $penggantiInfo = $penggantiRecords->firstWhere('spmb_piket_tanggal_id', $selectedTanggal->id);
            $isPenggantiOnSelected = ! is_null($penggantiInfo);
        }

        return view('pages.guru.spmb-piket.absensi', compact(
            'user',
            'isOfficer',
            'petugasRecords',
            'penggantiRecords',
            'activePetugas',
            'assignedKelompok',
            'allTanggals',
            'jadwalHariIni',
            'jadwalBerikutnya',
            'historyAbsensi',
            'tanggalsBelumAbsen',
            'selectedTanggal',
            'pesertaSeleksis',
            'isPenggantiOnSelected',
            'penggantiInfo'
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

        // Check ownership or replacement assignment unless Super Admin
        if (! $user->hasRole('Super Admin')) {
            $isOwner = ($petugas->user_id === $user->id) || ($guruId && $petugas->master_guru_id === $guruId);
            $isPengganti = SpmbPiketPengganti::where('spmb_piket_tanggal_id', $validated['spmb_piket_tanggal_id'])
                ->where('spmb_piket_petugas_id', $petugas->id)
                ->where(function ($q) use ($user, $guruId) {
                    $q->where('user_id', $user->id);
                    if ($guruId) {
                        $q->orWhere('master_guru_id', $guruId);
                    }
                })->exists();

            if (! $isOwner && ! $isPengganti) {
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

    /**
     * Update attendance of candidate and parents by duty officers.
     */
    public function updateKehadiranPeserta(Request $request, SpmbPesertaSeleksi $pesertaSeleksi)
    {
        $validated = $request->validate([
            'status_kehadiran_siswa' => 'required|in:Terjadwal,Menunggu,Hadir,Tidak Hadir',
            'status_kehadiran_ortu' => 'required|in:Terjadwal,Menunggu,Hadir,Tidak Hadir',
            'catatan' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'status_kehadiran_siswa' => $validated['status_kehadiran_siswa'],
            'status_kehadiran_ortu' => $validated['status_kehadiran_ortu'],
        ];

        if (isset($validated['catatan'])) {
            $updateData['catatan'] = $validated['catatan'];
        }

        if ($validated['status_kehadiran_siswa'] === 'Hadir' && ! $pesertaSeleksi->waktu_kehadiran_siswa) {
            $updateData['waktu_kehadiran_siswa'] = now();
        } elseif ($validated['status_kehadiran_siswa'] !== 'Hadir') {
            $updateData['waktu_kehadiran_siswa'] = null;
        }

        if ($validated['status_kehadiran_ortu'] === 'Hadir' && ! $pesertaSeleksi->waktu_kehadiran_ortu) {
            $updateData['waktu_kehadiran_ortu'] = now();
        } elseif ($validated['status_kehadiran_ortu'] !== 'Hadir') {
            $updateData['waktu_kehadiran_ortu'] = null;
        }

        $pesertaSeleksi->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran calon peserta berhasil diperbarui.',
                'peserta' => $pesertaSeleksi,
            ]);
        }

        return redirect()->back()->with('success', "Status kehadiran {$pesertaSeleksi->nama_lengkap} berhasil diperbarui.");
    }

    /**
     * Store duty officer report and physical attendance sheet photos (eviden manual).
     */
    public function storeLaporanEviden(Request $request)
    {
        $validated = $request->validate([
            'spmb_piket_tanggal_id' => 'required|exists:spmb_piket_tanggals,id',
            'foto_eviden_absen_siswa' => 'nullable|image|max:8192',
            'foto_eviden_absen_ortu' => 'nullable|image|max:8192',
            'catatan_petugas' => 'nullable|string|max:2000',
        ]);

        $tanggal = SpmbPiketTanggal::findOrFail($validated['spmb_piket_tanggal_id']);

        $updateData = [
            'catatan_petugas' => $validated['catatan_petugas'] ?? $tanggal->catatan_petugas,
            'laporan_petugas_by' => Auth::id(),
            'laporan_petugas_at' => now(),
        ];

        if ($request->hasFile('foto_eviden_absen_siswa')) {
            if ($tanggal->foto_eviden_absen_siswa && Storage::disk('public')->exists($tanggal->foto_eviden_absen_siswa)) {
                Storage::disk('public')->delete($tanggal->foto_eviden_absen_siswa);
            }
            $updateData['foto_eviden_absen_siswa'] = $request->file('foto_eviden_absen_siswa')->store('spmb-piket-eviden-lapangan', 'public');
        }

        if ($request->hasFile('foto_eviden_absen_ortu')) {
            if ($tanggal->foto_eviden_absen_ortu && Storage::disk('public')->exists($tanggal->foto_eviden_absen_ortu)) {
                Storage::disk('public')->delete($tanggal->foto_eviden_absen_ortu);
            }
            $updateData['foto_eviden_absen_ortu'] = $request->file('foto_eviden_absen_ortu')->store('spmb-piket-eviden-lapangan', 'public');
        }

        $tanggal->update($updateData);

        return redirect()->back()->with('success', 'Laporan pelaksanaan tugas piket & unggah eviden presensi manual berhasil disimpan!');
    }

    /**
     * Print View: Daftar Hadir Calon Siswa Seleksi (Fisik / Manual).
     */
    public function cetakHadirSiswa(Request $request)
    {
        return app(\App\Http\Controllers\KaurSpmb\PesertaSeleksiController::class)->printHadirSiswa($request);
    }

    /**
     * Print View: Daftar Hadir Orang Tua Pendamping Seleksi (Fisik / Manual).
     */
    public function cetakHadirOrtu(Request $request)
    {
        return app(\App\Http\Controllers\KaurSpmb\PesertaSeleksiController::class)->printHadirOrtu($request);
    }
}
