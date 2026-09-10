<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->masterGuru;
        if (! $guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }
        $izins = GuruIzin::where('master_guru_id', $guru->id)->latest()->paginate(10);

        return view('pages.guru.izin.index', compact('izins'));
    }

    public function create()
    {
        $guru = Auth::user()->masterGuru;
        if (! $guru) {
            return redirect()->route('dashboard')->with('error', 'Data Master Guru tidak ditemukan. Silakan hubungi admin.');
        }

        // Optional: Get schedule for today or next few days
        return view('pages.guru.izin.create');
    }

    public function getSchedules(Request $request)
    {
        $guru = Auth::user()->masterGuru;
        if (! $guru) {
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
            ->inActiveAcademicPeriod()
            ->where('hari', $hari)
            ->orderBy('jam_ke')
            ->get();

        return response()->json($schedules);
    }

    public function getLmsResources(JadwalPelajaran $schedule)
    {
        $guru = Auth::user()->masterGuru;
        if (
            ! $guru
            || $schedule->master_guru_id !== $guru->id
            || ! $schedule->rombel()->whereHas('tahunPelajaran', fn ($period) => $period->where('is_active', true))->exists()
        ) {
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
            'assignments' => $assignments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin' => 'required|string',
            'kategori_penyetujuan' => 'required|in:sekolah,luar,tidak_masuk,terlambat',
            'deskripsi' => 'required|string',
            'jadwal_ids' => 'nullable|array',
            'jadwal_ids.*' => 'exists:jadwal_pelajarans,id',
            'lms_material_id' => 'nullable|integer',
            'lms_assignment_id' => 'nullable|integer',
        ]);

        $guru = Auth::user()->masterGuru;
        if (! $guru) {
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
            ->inActiveAcademicPeriod()
            ->where('hari', $hari)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('jam_mulai', '<', $endTime)
                    ->where('jam_selesai', '>', $startTime);
            })
            ->get();

        $selectedJadwalIds = array_map('intval', $request->input('jadwal_ids', []));
        if ($availableSchedules->whereIn('id', $selectedJadwalIds)->count() !== count(array_unique($selectedJadwalIds))) {
            return redirect()->back()->withInput()->with('error', 'Pilihan jam pelajaran tidak sesuai dengan jadwal aktif Anda. Silakan muat ulang halaman dan pilih jadwal kembali.');
        }

        if ($availableSchedules->isNotEmpty()) {
            if (count($selectedJadwalIds) === 0) {
                return redirect()->back()->withInput()->with('error', 'Sistem mendeteksi Anda memiliki jam mengajar pada waktu tersebut. Silakan pilih jam pelajaran yang Anda tinggalkan.');
            }

            $materialId = $request->integer('lms_material_id') ?: null;
            $assignmentId = $request->integer('lms_assignment_id') ?: null;

            if (! $materialId && ! $assignmentId) {
                return redirect()->back()->withInput()->with('error', 'Anda wajib memilih minimal satu Materi atau Tugas. Pilihan tersebut otomatis berlaku untuk seluruh jam pelajaran yang terdampak.');
            }

            if ($materialId && ! LmsMaterial::query()
                ->whereKey($materialId)
                ->where('master_guru_id', $guru->id)
                ->where('is_published', true)
                ->exists()) {
                return redirect()->back()->withInput()->with('error', 'Materi LMS yang dipilih tidak tersedia pada akun Anda. Silakan pilih kembali.');
            }

            if ($assignmentId && ! LmsAssignment::query()
                ->whereKey($assignmentId)
                ->where('master_guru_id', $guru->id)
                ->exists()) {
                return redirect()->back()->withInput()->with('error', 'Tugas LMS yang dipilih tidak tersedia pada akun Anda. Silakan pilih kembali.');
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

        if (in_array($request->kategori_penyetujuan, ['tidak_masuk', 'terlambat'], true)) {
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
                    'lms_material_id' => $request->integer('lms_material_id') ?: null,
                    'lms_assignment_id' => $request->integer('lms_assignment_id') ?: null,
                ];
            }
            $izin->jadwals()->sync($pivotData);
        }

        // Notifikasi untuk Approver
        if ($izin->startsAtSdm()) {
            // Langsung ke SDM
            $approvers = \App\Models\User::role('KAUR SDM')->get();
            $msg = 'Ada pengajuan '.$izin->categoryLabel().' baru dari '.$guru->nama_lengkap;
            $url = route('sdm.persetujuan-izin-guru.index');
        } else {
            // Ke Piket terlebih dahulu
            $approvers = \App\Models\User::role('Guru Piket')->get();
            $msg = 'Ada pengajuan Izin Guru baru dari '.$guru->nama_lengkap;
            $url = route('piket.persetujuan-izin-guru.index');
        }

        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\PengajuanIzinGuruNotification($izin, 'pending_approval', $msg, $url));
        }

        return redirect()->route('guru.izin.index')->with('success', 'Permohonan izin berhasil diajukan dan sedang menunggu persetujuan.');
    }
}
