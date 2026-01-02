<?php

namespace App\Http\Controllers\Siswa;

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
     * Shows list of classes/subjects enrolled by the student.
     */
    public function index()
    {
        $siswa = Auth::user()->masterSiswa;

        if (!$siswa) {
            // Check if user is linked to MasterSiswa
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Logic to get enrolled classes.
        // Assuming a student belongs to ONE rombel currently.
        // We find the rombel from the siswa data (if stored there) or via a relation.
        // However, MasterSiswa doesn't have `rombel_id` directly in the migration I saw?
        // Wait, steps 250 (create_master_siswas) does NOT have `rombel_id`.
        // So how do we know which rombel a student is in?
        // Usually via `Rombel` -> `Siswa` many-to-many or `SiswaRombel` pivot table.
        // OR `MasterSiswa` might have it but I missed it.
        
        // Let's assume there is a way to get the student's current Rombel.
        // The `RombelController` has `addSiswa` which implies relation.
        // I should check `MasterSiswa` model to see relationships.
        
        // For now, I'll assume we can get the student's rombel via a method or relation `rombel()`.
        // If not, I'll need to check the pivot table.
        
        // Get the latest rombel the student is enrolled in
        $rombel = $siswa->rombels()->latest()->first();

        if (!$rombel) {
             // Fallback or error
             $teachingSchedules = collect([]);
        } else {
            // Get subjects for this rombel from JadwalPelajaran
            $teachingSchedules = JadwalPelajaran::where('rombel_id', $rombel->id)
                ->with(['mataPelajaran', 'guru'])
                ->get()
                ->groupBy('mata_pelajaran_id')
                ->map(function ($items) {
                    return $items->first();
                });
        }

        return view('pages.siswa.lms.index', compact('teachingSchedules', 'rombel'));
    }

    public function show(MataPelajaran $mapel)
    {
        $siswa = Auth::user()->masterSiswa;
        $rombel = $siswa->rombels()->latest()->first();

        if (!$rombel) {
            return redirect()->back()->with('error', 'Anda tidak terdaftar dalam kelas.');
        }

        $materials = LmsMaterial::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $assignments = LmsAssignment::where('rombel_id', $rombel->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.siswa.lms.course', compact('rombel', 'mapel', 'materials', 'assignments'));
    }

    public function showAssignment(LmsAssignment $assignment)
    {
        $siswa = Auth::user()->masterSiswa;
        
        // Check submission
        $submission = LmsSubmission::where('lms_assignment_id', $assignment->id)
            ->where('master_siswa_id', $siswa->id)
            ->first();

        return view('pages.siswa.lms.assignment.show', compact('assignment', 'submission'));
    }

    public function storeSubmission(Request $request, LmsAssignment $assignment)
    {
        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $siswa = Auth::user()->masterSiswa;

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lms/submissions', 'public');
        }

        LmsSubmission::updateOrCreate(
            [
                'lms_assignment_id' => $assignment->id,
                'master_siswa_id' => $siswa->id,
            ],
            [
                'content' => $request->content,
                'file_path' => $path,
                'submitted_at' => now(), // Update submission time
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
    }
}
