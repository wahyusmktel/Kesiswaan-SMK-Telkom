<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\LmsSubmission;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsController extends Controller
{
    /**
     * Display a listing of the resource.
     * Shows list of classes/subjects taught by the logged-in teacher.
     */
    public function index()
    {
        $guru = Auth::user()->masterGuru;

        if (!$guru) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan.');
        }

        // Get unique combinations of Rombel and MataPelajaran from JadwalPelajaran
        // or we can query MataPelajaran directly if there is a relation.
        // Assuming JadwalPelajaran holds the truth of what a teacher teaches.
        $teachingSchedules = JadwalPelajaran::where('master_guru_id', $guru->id)
            ->with(['rombel.kelas', 'mataPelajaran'])
            ->get()
            ->groupBy(function ($item) {
                return $item->rombel_id . '-' . $item->mata_pelajaran_id;
            })
            ->map(function ($items) {
                return $items->first();
            });

        return view('pages.guru.lms.index', compact('teachingSchedules'));
    }

    /**
     * Show the course page (stream of materials and assignments).
     */
    public function show(Rombel $rombel, MataPelajaran $mapel)
    {
        $guru = Auth::user()->masterGuru;
        
        // Verify access (optional but recommended)
        // $hasAccess = JadwalPelajaran::where('master_guru_id', $guru->id)
        //     ->where('rombel_id', $rombel->id)
        //     ->where('mata_pelajaran_id', $mapel->id)
        //     ->exists();

        $materials = LmsMaterial::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $assignments = LmsAssignment::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.guru.lms.course', compact('rombel', 'mapel', 'materials', 'assignments'));
    }

    public function createMaterial(Rombel $rombel, MataPelajaran $mapel)
    {
        return view('pages.guru.lms.material.create', compact('rombel', 'mapel'));
    }

    public function storeMaterial(Request $request, Rombel $rombel, MataPelajaran $mapel)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lms/materials', 'public');
        }

        LmsMaterial::create([
            'mata_pelajaran_id' => $mapel->id,
            'master_guru_id' => Auth::user()->masterGuru->id,
            'rombel_id' => $rombel->id,
            'title' => $request->title,
            'content' => $request->content,
            'file_path' => $path,
        ]);

        return redirect()->route('guru.lms.course.show', ['rombel' => $rombel->id, 'mapel' => $mapel->id])
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function createAssignment(Rombel $rombel, MataPelajaran $mapel)
    {
        return view('pages.guru.lms.assignment.create', compact('rombel', 'mapel'));
    }

    public function storeAssignment(Request $request, Rombel $rombel, MataPelajaran $mapel)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'points' => 'required|integer|min:0|max:100',
            'file' => 'nullable|file|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lms/assignments', 'public');
        }

        LmsAssignment::create([
            'mata_pelajaran_id' => $mapel->id,
            'master_guru_id' => Auth::user()->masterGuru->id,
            'rombel_id' => $rombel->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'due_date' => $request->due_date,
            'points' => $request->points,
        ]);

        return redirect()->route('guru.lms.course.show', ['rombel' => $rombel->id, 'mapel' => $mapel->id])
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function showAssignment(LmsAssignment $assignment)
    {
        $assignment->load(['submissions.siswa']);
        return view('pages.guru.lms.assignment.show', compact('assignment'));
    }

    public function showSubmission(LmsSubmission $submission)
    {
        return view('pages.guru.lms.submission.show', compact('submission'));
    }

    public function gradeSubmission(Request $request, LmsSubmission $submission)
    {
        $request->validate([
            'grade' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
        ]);

        return redirect()->route('guru.lms.assignment.show', $submission->lms_assignment_id)
            ->with('success', 'Nilai berhasil disimpan.');
    }
}
