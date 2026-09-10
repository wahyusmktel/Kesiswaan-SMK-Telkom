<?php

namespace App\Http\Controllers\SDM;

use App\Http\Controllers\Controller;
use App\Models\MasterGuru;
use App\Support\EmploymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherActivityController extends Controller
{
    private const EMPLOYMENT_OPTIONS = [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME];

    private const CATEGORY_OPTIONS = [MasterGuru::CATEGORY_TEACHER, MasterGuru::CATEGORY_TPA];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
            'category' => ['nullable', Rule::in(self::CATEGORY_OPTIONS)],
        ]);
        $teachers = MasterGuru::with(['dapodikGuru', 'user'])
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('nama_lengkap', 'like', '%'.$search.'%'))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('is_active', $status === 'active'))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('employee_category', $category))
            ->orderBy('nama_lengkap')->paginate(25)->withQueryString();

        return view('pages.sdm.teacher-activity', [
            'teachers' => $teachers,
            'employmentOptions' => self::EMPLOYMENT_OPTIONS,
            'categoryOptions' => self::CATEGORY_OPTIONS,
        ]);
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

    public function updatePhone(Request $request, MasterGuru $teacher)
    {
        $activeRole = session('active_role') ?: $request->user()->getRoleNames()->first();
        abort_unless($activeRole === 'Super Admin', 403);

        $input = $request->validate([
            'phone_number' => ['nullable', 'string', 'max:25', 'regex:/^(?:\+?62|0)[0-9\s\-]{8,20}$/'],
        ], [
            'phone_number.regex' => 'Nomor HP/WhatsApp harus diawali 0, 62, atau +62 dan berisi 10–22 digit.',
        ]);

        $user = $teacher->user;
        if (! $user) {
            return back()->withErrors(['phone_number' => 'Guru belum memiliki akun pengguna. Hubungkan akun terlebih dahulu sebelum mengisi nomor WhatsApp.']);
        }

        $phoneNumber = filled($input['phone_number'] ?? null)
            ? preg_replace('/[\s\-]/', '', $input['phone_number'])
            : null;

        $user->update(['phone_number' => $phoneNumber]);

        return back()->with('success', 'Nomor HP/WhatsApp '.$teacher->nama_lengkap.' berhasil diperbarui.');
    }

    public function updateCategory(Request $request, MasterGuru $teacher)
    {
        $input = $request->validate([
            'employee_category' => ['required', Rule::in(self::CATEGORY_OPTIONS)],
        ]);

        DB::transaction(function () use ($teacher, $input) {
            $teacher->update(['employee_category' => $input['employee_category']]);
            $teacher->dapodikGuru?->update(['employee_category' => $input['employee_category']]);
        });

        $label = $input['employee_category'] === MasterGuru::CATEGORY_TPA ? 'TPA' : 'Guru';

        return back()->with('success', 'Kategori '.$teacher->nama_lengkap.' berhasil diubah menjadi '.$label.'. Role akun tetap dipertahankan.');
    }

    public function update(Request $request, MasterGuru $teacher)
    {
        $input = $request->validate(['is_active' => ['required', 'boolean']]);
        // Explicit assignment: other teacher forms must not change this protected field.
        $teacher->is_active = (bool) $input['is_active'];
        $teacher->save();

        return back()->with('success', 'Status pegawai berhasil diubah menjadi '.($teacher->is_active ? 'aktif.' : 'nonaktif.'));
    }
}
