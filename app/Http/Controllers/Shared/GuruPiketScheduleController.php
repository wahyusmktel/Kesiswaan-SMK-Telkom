<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\GuruPiketSchedule;
use App\Models\MasterGuru;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GuruPiketScheduleController extends Controller
{
    public function index()
    {
        $teachers = $this->eligibleTeachers()->get();
        $eligibleTeacherIds = $teachers->modelKeys();
        $savedSchedules = GuruPiketSchedule::query()->get()->keyBy(
            fn (GuruPiketSchedule $schedule) => $schedule->weekday.'-'.$schedule->slot
        );
        $schedule = collect(GuruPiketSchedule::WEEKDAYS)->mapWithKeys(fn (string $day) => [
            $day => [
                in_array($savedSchedules->get($day.'-1')?->user_id, $eligibleTeacherIds, true) ? (string) $savedSchedules->get($day.'-1')->user_id : '',
                in_array($savedSchedules->get($day.'-2')?->user_id, $eligibleTeacherIds, true) ? (string) $savedSchedules->get($day.'-2')->user_id : '',
            ],
        ])->all();

        return view('pages.shared.guru-piket-schedules.index', compact('teachers', 'schedule'));
    }

    public function update(Request $request)
    {
        $allowedDays = implode(',', GuruPiketSchedule::WEEKDAYS);
        $rules = [
            'schedules' => ['required', 'array:'.$allowedDays],
        ];

        foreach (GuruPiketSchedule::WEEKDAYS as $day) {
            $rules['schedules.'.$day] = ['required', 'array', 'size:2'];
            $rules['schedules.'.$day.'.*'] = ['required', 'integer', 'distinct', Rule::exists('users', 'id')];
        }

        $data = $request->validate($rules, [
            'schedules.*.size' => 'Setiap hari wajib memiliki tepat dua Guru Piket.',
            'schedules.*.*.required' => 'Kedua Guru Piket wajib dipilih.',
            'schedules.*.*.distinct' => 'Guru Piket pertama dan kedua pada hari yang sama harus berbeda.',
        ]);

        $selectedIds = collect($data['schedules'])->flatten()->map(fn ($id) => (int) $id)->unique()->values();
        $eligibleIds = $this->eligibleTeachers()->whereKey($selectedIds)->pluck('id');
        if ($eligibleIds->count() !== $selectedIds->count()) {
            throw ValidationException::withMessages([
                'schedules' => 'Jadwal hanya dapat diisi oleh Guru Kelas aktif yang terhubung dengan data guru.',
            ]);
        }

        DB::transaction(function () use ($data): void {
            GuruPiketSchedule::query()->whereIn('weekday', GuruPiketSchedule::WEEKDAYS)->delete();

            foreach (GuruPiketSchedule::WEEKDAYS as $day) {
                foreach (array_values($data['schedules'][$day]) as $index => $userId) {
                    GuruPiketSchedule::create([
                        'weekday' => $day,
                        'slot' => $index + 1,
                        'user_id' => $userId,
                    ]);
                }
            }
        });

        return back()->with('success', 'Jadwal tugas Guru Piket Senin–Jumat berhasil disimpan.');
    }

    private function eligibleTeachers(): Builder
    {
        return User::query()
            ->role('Guru Kelas')
            ->whereHas('masterGuru', fn (Builder $query) => $query
                ->where('is_active', true)
                ->where(fn (Builder $category) => $category
                    ->whereNull('employee_category')
                    ->orWhere('employee_category', MasterGuru::CATEGORY_TEACHER)))
            ->with('masterGuru:id,user_id,nama_lengkap,kode_guru')
            ->orderBy('name');
    }
}
