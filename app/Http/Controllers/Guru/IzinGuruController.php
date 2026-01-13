<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }
        $izins = GuruIzin::where('master_guru_id', $guru->id)->latest()->paginate(10);
        return view('pages.guru.izin.index', compact('izins'));
    }

    public function create()
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }
        // Optional: Get schedule for today or next few days
        return view('pages.guru.izin.create');
    }

    public function getSchedules(Request $request)
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return response()->json([], 404);
        }
        $tanggal = $request->tanggal;
        $hariMap = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $hari = $hariMap[date('l', strtotime($tanggal))];

        $schedules = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])
            ->where('master_guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_ke')
            ->get();

        return response()->json($schedules);
    }

    public function getLmsResources(JadwalPelajaran $schedule)
    {
        $guru = Auth::user()->masterGuru;
        if (!$guru || $schedule->master_guru_id !== $guru->id) {
            return response()->json([], 403);
        }

        $materials = \App\Models\LmsMaterial::where('master_guru_id', $guru->id)
            ->where('rombel_id', $schedule->rombel_id)
            ->where('mata_pelajaran_id', $schedule->mata_pelajaran_id)
            ->where('is_published', true)
            ->select('id', 'title')
            ->get();

        $assignments = \App\Models\LmsAssignment::where('master_guru_id', $guru->id)
            ->where('rombel_id', $schedule->rombel_id)
            ->where('mata_pelajaran_id', $schedule->mata_pelajaran_id)
            ->select('id', 'title')
            ->get();

        return response()->json([
            'materials' => $materials,
            'assignments' => $assignments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|string',
            'kategori_penyetujuan' => 'required|in:sekolah,luar,terlambat',
            'deskripsi' => 'required|string',
            'jadwal_ids' => 'nullable|array',
            'jadwal_ids.*' => 'exists:jadwal_pelajarans,id',
            'lms_material_ids' => 'nullable|array',
            'lms_assignment_ids' => 'nullable|array',
        ]);

        $guru = Auth::user()->masterGuru;
        if (!$guru) {
            return redirect()->back()->with('error', 'Data Master Guru tidak ditemukan.');
        }

        // Logic check: If there are schedules within the permit timeframe, at least one must be selected
        $startDate = \Carbon\Carbon::parse($request->tanggal_mulai);
        $endDate = \Carbon\Carbon::parse($request->tanggal_selesai);
        
        $hariMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        
        $hari = $hariMap[$startDate->format('l')];
        $startTime = $startDate->format('H:i:s');
        $endTime = $endDate->format('H:i:s');

        $availableSchedules = JadwalPelajaran::where('master_guru_id', $guru->id)
            ->where('hari', $hari)
            ->where(function($q) use ($startTime, $endTime) {
                $q->where('jam_mulai', '<', $endTime)
                  ->where('jam_selesai', '>', $startTime);
            })
            ->get();

        if ($availableSchedules->isNotEmpty()) {
            $selectedJadwalIds = $request->input('jadwal_ids', []);
            if (count($selectedJadwalIds) === 0) {
                return redirect()->back()->withInput()->with('error', 'Sistem mendeteksi Anda memiliki jam mengajar pada waktu tersebut. Silakan pilih jam pelajaran yang Anda tinggalkan.');
            }

            // Mandatory Penugasan Validation
            foreach ($selectedJadwalIds as $id) {
                $mateId = $request->input("lms_material_ids.$id");
                $asgnId = $request->input("lms_assignment_ids.$id");
                
                if (empty($mateId) && empty($asgnId)) {
                    $jadwal = JadwalPelajaran::with('rombel.kelas')->find($id);
                    $kelasNama = $jadwal->rombel->kelas->nama_kelas;
                    return redirect()->back()->withInput()->with('error', "Anda wajib melampirkan minimal satu Materi atau Tugas untuk kelas $kelasNama pada Jam ke-$jadwal->jam_ke.");
                }
            }
        }

        // Check for overlapping permits
        $overlap = GuruIzin::where('master_guru_id', $guru->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('tanggal_mulai', '<=', $endDate)
                      ->where('tanggal_selesai', '>=', $startDate);
            })
            ->where('status_piket', '!=', 'ditolak')
            ->where('status_kurikulum', '!=', 'ditolak')
            ->where('status_sdm', '!=', 'ditolak')
            ->exists();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'Anda sudah memiliki pengajuan izin pada rentang waktu tersebut yang sedang diproses atau sudah disetujui.');
        }

        $statusPiket = 'menunggu';
        $statusKurikulum = 'menunggu';
        
        if ($request->kategori_penyetujuan === 'terlambat') {
            $statusPiket = 'disetujui';
            $statusKurikulum = 'disetujui';
        }

        $izin = GuruIzin::create([
            'master_guru_id' => $guru->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'jenis_izin' => $request->jenis_izin,
            'kategori_penyetujuan' => $request->kategori_penyetujuan,
            'deskripsi' => $request->deskripsi,
            'status_piket' => $statusPiket,
            'status_kurikulum' => $statusKurikulum,
            'status_sdm' => 'menunggu',
        ]);

        if ($request->filled('jadwal_ids')) {
            $pivotData = [];
            foreach ($request->jadwal_ids as $jadwalId) {
                $pivotData[$jadwalId] = [
                    'lms_material_id' => $request->input("lms_material_ids.$jadwalId") ?: null,
                    'lms_assignment_id' => $request->input("lms_assignment_ids.$jadwalId") ?: null,
                ];
            }
            $izin->jadwals()->sync($pivotData);
        }

        // Notifikasi untuk Approver
        if ($izin->kategori_penyetujuan === 'terlambat') {
            // Langsung ke SDM
            $approvers = \App\Models\User::role('KAUR SDM')->get();
            $msg = "Ada pengajuan Izin Terlambat baru dari " . $guru->nama_lengkap;
            $url = route('sdm.persetujuan-izin-guru.index');
        } else {
            // Ke Piket terlebih dahulu
            $approvers = \App\Models\User::role('guru piket')->get();
            $msg = "Ada pengajuan Izin Guru baru dari " . $guru->nama_lengkap;
            $url = route('piket.persetujuan-izin-guru.index');
        }

        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'pending_approval', $msg, $url));
        }

        return redirect()->route('guru.izin.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu persetujuan.');
    }
}
