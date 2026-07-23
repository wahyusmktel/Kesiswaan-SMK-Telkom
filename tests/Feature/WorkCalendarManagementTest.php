<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkCalendarEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkCalendarManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_kaur_sdm_can_import_excel_starting_from_row_six(): void
    {
        $user = $this->kaurSdm();
        $file = $this->calendarWorkbook([
            [1, '2026-07-01', '2026-07-11', 'Libur Antar Semester', 'Libur'],
            [2, '2026-07-13', '2026-07-17', 'MPLS', 'Kegiatan Akademik'],
            [3, '2026-09-14', '2026-09-19', 'STS Ganjil', 'Asesmen/Ujian'],
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'KAUR SDM'])
            ->post(route('sdm.calendar.import'), [
                'agenda_file' => $file,
                'month' => '2026-07',
            ]);

        $response->assertRedirect(route('sdm.calendar.index', ['month' => '2026-07']));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('work_calendar_events', 3);
        $this->assertDatabaseHas('work_calendar_events', [
            'title' => 'Libur Antar Semester',
            'type' => 'school_break',
            'is_non_working' => true,
            'date_from' => '2026-07-01 00:00:00',
            'date_to' => '2026-07-11 00:00:00',
        ]);
        $this->assertDatabaseHas('work_calendar_events', [
            'title' => 'MPLS',
            'type' => 'academic_activity',
            'is_non_working' => false,
        ]);
    }

    public function test_kaur_sdm_can_update_and_delete_an_agenda(): void
    {
        $user = $this->kaurSdm();
        $event = WorkCalendarEvent::create([
            'title' => 'Agenda Lama',
            'type' => 'academic_activity',
            'is_non_working' => false,
            'date_from' => '2026-07-10',
            'date_to' => '2026-07-10',
        ]);

        $this->actingAs($user)
            ->withSession(['active_role' => 'KAUR SDM'])
            ->put(route('sdm.calendar.update', $event), [
                'title' => 'Agenda Diperbarui',
                'type' => 'national_holiday',
                'is_non_working' => '1',
                'date_from' => '2026-07-11',
                'date_to' => '2026-07-12',
                'description' => 'Keterangan baru',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('work_calendar_events', [
            'id' => $event->id,
            'title' => 'Agenda Diperbarui',
            'type' => 'national_holiday',
            'is_non_working' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['active_role' => 'KAUR SDM'])
            ->delete(route('sdm.calendar.destroy', $event))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('work_calendar_events', ['id' => $event->id]);
    }

    public function test_calendar_lists_only_events_overlapping_the_selected_month(): void
    {
        $user = $this->kaurSdm();
        WorkCalendarEvent::create([
            'title' => 'Agenda Juli',
            'type' => 'academic_activity',
            'is_non_working' => false,
            'date_from' => '2026-07-30',
            'date_to' => '2026-08-02',
        ]);
        WorkCalendarEvent::create([
            'title' => 'Agenda September',
            'type' => 'assessment',
            'is_non_working' => false,
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-02',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['active_role' => 'KAUR SDM'])
            ->get(route('sdm.calendar.index', ['month' => '2026-08']));

        $response->assertOk();
        $response->assertSee('Agenda Juli');
        $response->assertViewHas('monthEvents', function ($events) {
            return $events->pluck('title')->all() === ['Agenda Juli'];
        });
        $response->assertSee(route('sdm.calendar.index', ['month' => '2026-07']), false);
        $response->assertSee(route('sdm.calendar.index', ['month' => '2026-09']), false);
    }

    private function kaurSdm(): User
    {
        $permission = Permission::findOrCreate('view sdm dashboard', 'web');
        $role = Role::findOrCreate('KAUR SDM', 'web');
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function calendarWorkbook(array $events): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['REKAP KEGIATAN TIDAK EFEKTIF'],
            ['Kalender Akademik'],
            ['Sumber'],
            [],
            ['No.', 'Tanggal Mulai', 'Tanggal Selesai', 'Nama Kegiatan', 'Jenis Kegiatan'],
        ], null, 'A1');

        foreach ($events as $index => [$number, $dateFrom, $dateTo, $title, $type]) {
            $row = $index + 6;
            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValue("B{$row}", ExcelDate::PHPToExcel(new \DateTimeImmutable($dateFrom)));
            $sheet->setCellValue("C{$row}", ExcelDate::PHPToExcel(new \DateTimeImmutable($dateTo)));
            $sheet->setCellValue("D{$row}", $title);
            $sheet->setCellValue("E{$row}", $type);
        }

        $path = tempnam(sys_get_temp_dir(), 'calendar-import-').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile(
            $path,
            'agenda.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );
    }
}
