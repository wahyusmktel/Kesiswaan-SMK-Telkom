<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use Illuminate\Http\Request;

class TeacherActivityController extends Controller
{
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

        return view('pages.sdm.teacher-activity', compact('teachers'));
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
