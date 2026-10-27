<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\GuruIzin;
use App\Models\GuruIzinJadwal;
use App\Models\JadwalPelajaran;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsSubmission;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsController extends Controller
{
    /**
     * Display a listing of the resource.
     * Shows list of classes/subjects enrolled by the student with schedules & leave info.
     */
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $rombel = $siswa->rombels()->with('kelas')->latest()->first();

        if (!$rombel) {
            return view('pages.siswa.lms.index', [
                'rombel' => null,
                'courses' => collect([]),
                'todayName' => '',
                'activeIzinCount' => 0,
                'pendingAssignmentsCount' => 0,
                'totalMaterialsCount' => 0,
            ]);
        }

        $todayMap = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $todayName = $todayMap[now()->format('l')] ?? '';

        // Fetch all teaching schedules for this rombel
        $schedules = JadwalPelajaran::where('rombel_id', $rombel->id)
            ->with([
                'mataPelajaran',
                'guru.user',
                'izins' => function ($q) {
                    $q->orderBy('tanggal_mulai', 'desc');
                },
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $groupedByMapel = $schedules->groupBy('mata_pelajaran_id');

        // Fetch materials count
        $materialsCounts = LmsMaterial::where('rombel_id', $rombel->id)
            ->where('is_published', true)
            ->selectRaw('mata_pelajaran_id, count(*) as total')
            ->groupBy('mata_pelajaran_id')
            ->pluck('total', 'mata_pelajaran_id');

        // Fetch assignments with student submission
        $assignments = LmsAssignment::where('rombel_id', $rombel->id)
            ->with(['submissions' => function ($q) use ($siswa) {
                $q->where('master_siswa_id', $siswa->id);
            }])
            ->get()
            ->groupBy('mata_pelajaran_id');

        $courses = collect();
        $totalActiveIzin = 0;
        $totalPendingAssignments = 0;
        $totalMaterialsCount = $materialsCounts->sum();

        foreach ($groupedByMapel as $mapelId => $mapelSchedules) {
            $firstSched = $mapelSchedules->first();
            $mapel = $firstSched->mataPelajaran;
            $guru = $firstSched->guru;

            if (!$mapel) {
                continue;
            }

            // Consolidate schedules by day
            $schedulesByDay = $mapelSchedules->groupBy('hari')->map(function ($daySchedules, $day) {
                $sorted = $daySchedules->sortBy('jam_mulai');
                $start = substr($sorted->first()->jam_mulai, 0, 5);
                $end = substr($sorted->last()->jam_selesai, 0, 5);
                $jamKeList = $sorted->pluck('jam_ke')->filter()->values();
                $jamKeStr = $jamKeList->isNotEmpty()
                    ? ($jamKeList->count() > 1 ? 'Jam ' . $jamKeList->first() . '-' . $jamKeList->last() : 'Jam ' . $jamKeList->first())
                    : '';

                return (object) [
                    'hari' => $day,
                    'jam_mulai' => $start,
                    'jam_selesai' => $end,
                    'time_range' => "{$start} - {$end}",
                    'jam_ke' => $jamKeStr,
                ];
            });

            $hasScheduleToday = $schedulesByDay->has($todayName);
            $todaySchedule = $hasScheduleToday ? $schedulesByDay->get($todayName) : null;

            // Check if teacher has active leave today or ongoing
            $activeIzin = null;
            $recentIzin = null;
            foreach ($mapelSchedules as $s) {
                foreach ($s->izins as $izin) {
                    $isToday = $izin->tanggal_mulai && $izin->tanggal_mulai->isToday();
                    $isOngoing = $izin->tanggal_mulai && $izin->tanggal_mulai->lte(now()) && (!$izin->tanggal_selesai || $izin->tanggal_selesai->gte(now()->startOfDay()));
                    if (($isToday || $isOngoing) && !$activeIzin) {
                        $activeIzin = $izin;
                    }
                    if (!$recentIzin) {
                        $recentIzin = $izin;
                    }
                }
            }

            if ($activeIzin) {
                $totalActiveIzin++;
            }

            $matCount = $materialsCounts->get($mapelId, 0);
            $mapelAssignments = $assignments->get($mapelId, collect());
            $asgCount = $mapelAssignments->count();

            $submittedCount = 0;
            $pendingCount = 0;
            foreach ($mapelAssignments as $asg) {
                if ($asg->submissions->isNotEmpty()) {
                    $submittedCount++;
                } else {
                    $pendingCount++;
                }
            }
            $totalPendingAssignments += $pendingCount;

            $courses->push((object) [
                'mapel' => $mapel,
                'guru' => $guru,
                'schedules' => $schedulesByDay,
                'has_schedule_today' => $hasScheduleToday,
                'today_schedule' => $todaySchedule,
                'active_izin' => $activeIzin,
                'recent_izin' => $recentIzin,
                'materials_count' => $matCount,
                'assignments_count' => $asgCount,
                'submitted_assignments_count' => $submittedCount,
                'pending_assignments_count' => $pendingCount,
            ]);
        }

        // Sort: items with active_izin first, then items with schedule today, then by mapel name
        $courses = $courses->sortBy([
            fn ($a, $b) => ($b->active_izin ? 1 : 0) <=> ($a->active_izin ? 1 : 0),
            fn ($a, $b) => ($b->has_schedule_today ? 1 : 0) <=> ($a->has_schedule_today ? 1 : 0),
            fn ($a, $b) => strcmp($a->mapel->nama_mapel, $b->mapel->nama_mapel),
        ]);

        return view('pages.siswa.lms.index', compact(
            'rombel',
            'courses',
            'todayName',
            'totalActiveIzin',
            'totalPendingAssignments',
            'totalMaterialsCount'
        ));
    }

    public function show(MataPelajaran $mapel)
    {
        $siswa = Auth::user()->masterSiswa;
        $rombel = $siswa->rombels()->with('kelas')->latest()->first();

        if (!$rombel) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar dalam kelas.');
        }

        // Get schedules and teacher for this mapel in this rombel
        $schedules = JadwalPelajaran::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->with([
                'guru.user',
                'izins' => function ($q) {
                    $q->orderBy('tanggal_mulai', 'desc');
                },
            ])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $guru = $schedules->first()?->guru;

        // Consolidate schedules by day
        $schedulesByDay = $schedules->groupBy('hari')->map(function ($daySchedules, $day) {
            $sorted = $daySchedules->sortBy('jam_mulai');
            $start = substr($sorted->first()->jam_mulai, 0, 5);
            $end = substr($sorted->last()->jam_selesai, 0, 5);
            $jamKeList = $sorted->pluck('jam_ke')->filter()->values();
            $jamKeStr = $jamKeList->isNotEmpty()
                ? ($jamKeList->count() > 1 ? 'Jam ' . $jamKeList->first() . '-' . $jamKeList->last() : 'Jam ' . $jamKeList->first())
                : '';

            return (object) [
                'hari' => $day,
                'jam_mulai' => $start,
                'jam_selesai' => $end,
                'time_range' => "{$start} - {$end}",
                'jam_ke' => $jamKeStr,
            ];
        });

        // Check active / recent leave linked to this mapel
        $activeIzin = null;
        $recentIzins = collect();
        foreach ($schedules as $s) {
            foreach ($s->izins as $izin) {
                if (!$recentIzins->contains('id', $izin->id)) {
                    $recentIzins->push($izin);
                }
                $isToday = $izin->tanggal_mulai && $izin->tanggal_mulai->isToday();
                $isOngoing = $izin->tanggal_mulai && $izin->tanggal_mulai->lte(now()) && (!$izin->tanggal_selesai || $izin->tanggal_selesai->gte(now()->startOfDay()));
                if (($isToday || $isOngoing) && !$activeIzin) {
                    $activeIzin = $izin;
                }
            }
        }

        $materials = LmsMaterial::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $assignments = LmsAssignment::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->with(['submissions' => function ($q) use ($siswa) {
                $q->where('master_siswa_id', $siswa->id);
            }])
            ->orderBy('due_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalAssignments = $assignments->count();
        $submittedCount = $assignments->filter(fn ($a) => $a->submissions->isNotEmpty())->count();
        $gradedCount = $assignments->filter(fn ($a) => $a->submissions->isNotEmpty() && $a->submissions->first()->grade !== null)->count();

        return view('pages.siswa.lms.course', compact(
            'rombel',
            'mapel',
            'guru',
            'schedulesByDay',
            'activeIzin',
            'recentIzins',
            'materials',
            'assignments',
            'totalAssignments',
            'submittedCount',
            'gradedCount'
        ));
    }

    public function showAssignment(LmsAssignment $assignment)
    {
        $siswa = Auth::user()->masterSiswa;

        $assignment->load(['mataPelajaran', 'guru.user', 'rombel.kelas']);

        // Check submission
        $submission = LmsSubmission::where('lms_assignment_id', $assignment->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        // Check if this assignment was assigned via teacher leave (GuruIzin)
        $leaveJadwal = GuruIzinJadwal::where('lms_assignment_id', $assignment->id)->first();
        $relatedLeave = $leaveJadwal ? GuruIzin::find($leaveJadwal->guru_izin_id) : null;

        return view('pages.siswa.lms.assignment.show', compact('assignment', 'submission', 'relatedLeave'));
    }

    public function storeSubmission(Request $request, LmsAssignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $siswa = Auth::user()->masterSiswa;

        $existing = LmsSubmission::where('lms_assignment_id', $assignment->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        if (empty($request->content) && !$request->hasFile('file') && (!$existing || !$existing->file_path)) {
            return redirect()->back()->withInput()->with('error', 'Silakan masukkan catatan jawaban teks atau unggah file tugas Anda.');
        }

        $filePath = $existing ? $existing->file_path : null;
        if ($request->hasFile('file')) {
            if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $filePath = $request->file('file')->store('lms/submissions', 'public');
        }

        LmsSubmission::updateOrCreate(
            [
                'lms_assignment_id' => $assignment->id,
                'master_siswa_id' => $siswa->id,
            ],
            [
                'content' => $request->content,
                'file_path' => $filePath,
                'submitted_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
