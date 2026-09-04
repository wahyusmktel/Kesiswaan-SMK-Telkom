<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Support\EmploymentStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherActivityController extends Controller
{
    private const EMPLOYMENT_OPTIONS = [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);
        $teachers = MasterGuru::with('dapodikGuru')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('nama_lengkap', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->orderBy('nama_lengkap')->paginate(25)->withQueryString();

        return view('pages.sdm.teacher-activity', ['teachers' => $teachers, 'employmentOptions' => self::EMPLOYMENT_OPTIONS]);
    }

    public function updateEmployment(Request $request, MasterGuru $teacher)
    {
        $input = $request->validate([
            'status_kepegawaian' => ['required', Rule::in(self::EMPLOYMENT_OPTIONS)],
        ]);
        $dapodik = $teacher->dapodikGuru;
        if (! $dapodik) {
            return back()->withErrors(['status_kepegawaian' => 'Hubungkan data Dapodik guru terlebih dahulu sebelum mengubah status kepegawaian.']);
        }
        $dapodik->update($input);

        return back()->with('success', 'Status kepegawaian '.$teacher->nama_lengkap.' berhasil diubah menjadi '.$input['status_kepegawaian'].'.');
    }

    public function update(Request $request, MasterGuru $teacher)
    {
        $input = $request->validate(['is_active' => ['required', 'boolean']]);
        // Explicit assignment: other teacher forms must not change this protected field.
        $teacher->is_active = (bool) $input['is_active'];
        $teacher->save();

        return back()->with('success', 'Status guru berhasil diubah menjadi '.($teacher->is_active ? 'aktif.' : 'nonaktif.'));
    }
}
