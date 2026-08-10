<?php

namespace Tests\Feature;

use App\Models\TranscriptConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TranscriptManualSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_scan_requires_an_uploaded_signature_image_when_first_enabled(): void
    {
        $this->operatorRequest()->put(route('operator.transcript.config.update'), [
            'manual_signature_enabled' => '1',
            'manual_signature_x' => 54,
            'manual_signature_y' => 74,
            'manual_signature_width' => 43,
            'scan_color_mode' => 'color',
        ])->assertSessionHasErrors('manual_signature_image');

        $this->assertFalse((bool) TranscriptConfig::query()->value('manual_signature_enabled'));
    }

    public function test_operator_can_upload_and_position_manual_signature_scan(): void
    {
        Storage::fake('public');

        $this->operatorRequest()->put(route('operator.transcript.config.update'), [
            'manual_signature_enabled' => '1',
            'manual_signature_image' => UploadedFile::fake()->image('stempel.jpg', 1200, 760),
            'manual_signature_x' => 51.5,
            'manual_signature_y' => 73.25,
            'manual_signature_width' => 46.5,
            'scan_color_mode' => 'grayscale',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $config = TranscriptConfig::firstOrFail();
        $this->assertTrue($config->manual_signature_enabled);
        $this->assertSame('51.50', $config->manual_signature_x);
        $this->assertSame('73.25', $config->manual_signature_y);
        $this->assertSame('46.50', $config->manual_signature_width);
        $this->assertSame('grayscale', $config->scan_color_mode);
        Storage::disk('public')->assertExists($config->manual_signature_path);
    }

    public function test_signature_position_cannot_cross_the_right_page_edge(): void
    {
        Storage::fake('public');

        $this->operatorRequest()->put(route('operator.transcript.config.update'), [
            'manual_signature_enabled' => '1',
            'manual_signature_image' => UploadedFile::fake()->image('stempel.png'),
            'manual_signature_x' => 70,
            'manual_signature_y' => 74,
            'manual_signature_width' => 40,
            'scan_color_mode' => 'color',
        ])->assertSessionHasErrors('manual_signature_width');

        $this->assertDatabaseCount('transcript_configs', 0);
    }

    private function operatorRequest()
    {
        $permission = Permission::findOrCreate('view operator dashboard', 'web');
        $role = Role::findOrCreate('Operator', 'web');
        $role->givePermissionTo($permission);
        $user = User::factory()->create();
        $user->assignRole($role);

        return $this->actingAs($user)->withSession(['active_role' => 'Operator']);
    }
}
