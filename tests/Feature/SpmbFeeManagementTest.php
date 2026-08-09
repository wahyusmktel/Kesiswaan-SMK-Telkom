<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\SpmbFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SpmbFeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_public_api_returns_active_spmb_fees_and_total(): void
    {
        AppSetting::create(['spmb_academic_year' => '2027/2028']);

        $response = $this->withHeader('Origin', 'https://ppdb.smktelkom-lpg.sch.id')
            ->getJson('/api/spmb/fees');

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://ppdb.smktelkom-lpg.sch.id')
            ->assertJsonPath('meta.academic_year', '2027/2028')
            ->assertJsonPath('meta.total', 6500000)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'number', 'name', 'amount', 'formatted_amount']],
                'meta' => ['academic_year', 'total', 'formatted_total', 'last_updated_at'],
            ]);
    }

    public function test_super_admin_can_manage_spmb_fees(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.spmb-fees.index'))
            ->assertOk()
            ->assertSee('Pembiayaan SPMB')
            ->assertSee('Biaya DSP / Uang Gedung');

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->post(route('super-admin.spmb-fees.store'), [
                'name' => 'Tes Minat dan Bakat',
                'amount' => 150000,
                'sort_order' => 6,
                'is_active' => '1',
            ])
            ->assertRedirect();

        $fee = SpmbFee::query()->where('name', 'Tes Minat dan Bakat')->firstOrFail();

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->put(route('super-admin.spmb-fees.update', $fee), [
                'name' => 'Tes Potensi',
                'amount' => 175000,
                'sort_order' => 2,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('spmb_fees', [
            'id' => $fee->id,
            'name' => 'Tes Potensi',
            'amount' => 175000,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->withSession(['active_role' => 'Super Admin'])
            ->put(route('super-admin.spmb-fees.settings.update'), ['spmb_academic_year' => '2028/2029'])
            ->assertRedirect();

        $this->assertDatabaseHas('app_settings', ['spmb_academic_year' => '2028/2029']);
    }

    public function test_non_super_admin_cannot_open_spmb_fee_management(): void
    {
        $user = $this->userWithRole('Guru Kelas');

        $this->actingAs($user)
            ->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.spmb-fees.index'))
            ->assertForbidden();
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::findOrCreate($roleName, 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($role);

        return $user;
    }
}
