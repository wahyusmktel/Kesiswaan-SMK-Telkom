<?php

namespace Tests\Feature;

use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class PublicStudentRegistrationValidationTest extends TestCase
{
    public function test_invalid_registration_returns_to_form_with_errors_and_old_input(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $response = $this->from(route('student-registration.create'))
            ->post(route('student-registration.store'), [
                'nama_lengkap' => 'Calon Siswa',
                'nisn' => '123',
                'tanggal_lahir' => now()->addDay()->format('Y-m-d'),
                'jenis_kelamin' => 'L',
                'alamat' => 'Alamat tetap tersimpan',
                'nomor_hp' => '123',
            ]);

        $response->assertRedirect(route('student-registration.create'));
        $response->assertSessionHasErrors(['nisn', 'tanggal_lahir', 'nomor_hp', 'consent']);
        $response->assertSessionHasInput('nama_lengkap', 'Calon Siswa');
        $response->assertSessionHasInput('alamat', 'Alamat tetap tersimpan');
    }
}
