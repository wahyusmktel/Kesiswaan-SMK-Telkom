<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SurveyTargetRombelActiveYearTest extends TestCase
{
    use RefreshDatabase;

    public function test_survey_create_only_displays_rombels_from_active_academic_year(): void
    {
        Role::create(['name' => 'Guru Kelas']);
        $user = User::factory()->create();

        $activePeriod = TahunPelajaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $inactivePeriod = TahunPelajaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => false,
        ]);

        $classActive = Kelas::create([
            'nama_kelas' => 'X TJKT 1',
            'jurusan' => 'TJKT',
        ]);

        $classInactive = Kelas::create([
            'nama_kelas' => 'XII RPL 1',
            'jurusan' => 'RPL',
        ]);

        $activeRombel = Rombel::create([
            'tahun_ajaran' => $activePeriod->tahun,
            'tahun_pelajaran_id' => $activePeriod->id,
            'kelas_id' => $classActive->id,
            'wali_kelas_id' => $user->id,
        ]);

        $inactiveRombel = Rombel::create([
            'tahun_ajaran' => $inactivePeriod->tahun,
            'tahun_pelajaran_id' => $inactivePeriod->id,
            'kelas_id' => $classInactive->id,
            'wali_kelas_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('surveys.create'));

        $response->assertOk();
        $response->assertViewHas('rombels', function ($rombels) use ($activeRombel, $inactiveRombel) {
            return $rombels->contains('id', $activeRombel->id)
                && ! $rombels->contains('id', $inactiveRombel->id);
        });
        $response->assertViewHas('activeYear', function ($year) use ($activePeriod) {
            return $year && $year->id === $activePeriod->id;
        });
    }
}
