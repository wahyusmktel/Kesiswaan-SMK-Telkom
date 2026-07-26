<?php

namespace Tests\Feature;

use App\Models\MasterSiswa;
use App\Models\StudentRegistration;
use App\Models\StudentRegistrationApprovalPact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Middleware\RoleMiddleware;
use Tests\TestCase;

class StudentRegistrationBulkApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_integrity_statement_is_required(): void
    {
        $this->withoutMiddleware();
        $user = User::factory()->create();
        $registration = $this->createRegistration('Calon Siswa Satu');

        $statements = collect(StudentRegistrationApprovalPact::STATEMENTS)
            ->keys()
            ->reject(fn (string $key) => $key === 'accountability')
            ->mapWithKeys(fn (string $key) => [$key => '1'])
            ->all();

        $response = $this->actingAs($user)->post(route('master-data.student-registration.bulk-approve'), [
            'registration_ids' => [$registration->id],
            'statements' => $statements,
        ]);

        $response->assertSessionHasErrors('statements.accountability');
        $this->assertDatabaseHas('student_registrations', [
            'id' => $registration->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseCount('student_registration_approval_pacts', 0);
    }

    public function test_bulk_approval_creates_students_and_downloadable_pact_pdf(): void
    {
        $this->withoutMiddleware();
        $user = User::factory()->create(['name' => 'Operator Penguji']);
        $first = $this->createRegistration('Calon Siswa Satu');
        $second = $this->createRegistration('Calon Siswa Dua');

        $statements = collect(StudentRegistrationApprovalPact::STATEMENTS)
            ->keys()
            ->mapWithKeys(fn (string $key) => [$key => '1'])
            ->all();

        $response = $this->actingAs($user)->post(route('master-data.student-registration.bulk-approve'), [
            'registration_ids' => [$first->id, $second->id],
            'statements' => $statements,
        ]);

        $pact = StudentRegistrationApprovalPact::firstOrFail();

        $response->assertRedirect(route('master-data.student-registration.index', ['status' => 'pending']));
        $response->assertSessionHas('pact_download_url', route('master-data.student-registration.pacts.download', $pact));
        $this->assertSame(2, $pact->approved_count);
        $this->assertCount(2, $pact->student_snapshots);
        $this->assertCount(4, $pact->statements);
        $this->assertSame(2, MasterSiswa::where('data_source', 'registrasi')->count());
        $this->assertDatabaseMissing('student_registrations', ['id' => $first->id, 'status' => 'pending']);
        $this->assertDatabaseMissing('student_registrations', ['id' => $second->id, 'status' => 'pending']);

        $this->withMiddleware();
        $this->withoutMiddleware(RoleMiddleware::class);

        $pdfResponse = $this->actingAs($user)->get(route('master-data.student-registration.pacts.download', $pact));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdfResponse->getContent());
    }

    private function createRegistration(string $name): StudentRegistration
    {
        return StudentRegistration::create([
            'source' => 'public',
            'status' => 'pending',
            'nama_lengkap' => $name,
            'nisn' => (string) fake()->unique()->numberBetween(1000000000, 9999999999),
            'nik' => (string) fake()->unique()->numberBetween(1000000000000000, 9999999999999999),
            'tempat_lahir' => 'Bandar Lampung',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat calon siswa',
            'nomor_hp' => '081234567890',
            'sekolah_asal' => 'SMP Asal',
        ]);
    }
}
