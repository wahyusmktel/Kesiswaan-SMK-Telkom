<?php

namespace Tests\Feature;

use App\Models\RepositoryFile;
use App\Models\RepositorySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RepositoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'repository.disk' => 'repository',
            'repository.chunk_size' => 256 * 1024,
            'repository.max_file_size' => 10 * 1024 * 1024,
            'repository.download_driver' => 'laravel',
        ]);
        Storage::fake('repository');
        $this->withoutVite();
    }

    public function test_repository_management_is_only_available_to_super_admin(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $teacher = $this->userWithRole('Guru Kelas');

        $this->actingAs($teacher)->withSession(['active_role' => 'Guru Kelas'])
            ->get(route('super-admin.repository.index'))
            ->assertForbidden();

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->get(route('super-admin.repository.index'))
            ->assertOk()
            ->assertSee('Repository Bahan Praktikum')
            ->assertSee(route('super-admin.repository.index', absolute: false));
    }

    public function test_super_admin_can_configure_local_and_public_download_links(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $file = $this->storedFile($admin, 'modul-praktikum.zip', 'isi-arsip');

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->put(route('super-admin.repository.settings.update'), [
                'local_base_url' => 'http://10.10.10.2',
                'public_base_url' => 'https://sisfo.example.sch.id',
            ])->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('repository_settings', [
            'local_base_url' => 'http://10.10.10.2',
            'public_base_url' => 'https://sisfo.example.sch.id',
        ]);

        $path = route('repository.download', $file, false);
        $this->get(route('super-admin.repository.index'))
            ->assertOk()
            ->assertSee('http://10.10.10.2'.$path)
            ->assertSee('https://sisfo.example.sch.id'.$path);
    }

    public function test_chunked_upload_creates_downloadable_file_with_range_support(): void
    {
        $admin = $this->userWithRole('Super Admin');
        RepositorySetting::create([
            'local_base_url' => 'http://10.0.0.10',
            'public_base_url' => 'https://sisfo.example.sch.id',
        ]);
        $contents = str_repeat('A', 300000).str_repeat('B', 300000);

        $initialization = $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.repository.uploads.initialize'), [
                'title' => 'ISO Praktikum Jaringan',
                'description' => 'Bahan instalasi untuk kelas XI.',
                'original_name' => 'praktikum-jaringan.iso',
                'size' => strlen($contents),
                'mime_type' => 'application/octet-stream',
            ])
            ->assertCreated()
            ->assertJsonPath('total_chunks', 3);

        $uploadId = $initialization->json('upload_id');
        $chunkSize = $initialization->json('chunk_size');

        for ($index = 0; $index < 3; $index++) {
            $chunk = substr($contents, $index * $chunkSize, $chunkSize);
            $this->call(
                'PUT',
                route('super-admin.repository.uploads.chunk', [$uploadId, $index]),
                [], [], [],
                ['CONTENT_TYPE' => 'application/octet-stream', 'HTTP_ACCEPT' => 'application/json'],
                $chunk
            )->assertOk()->assertJsonPath('uploaded_chunk', $index);
        }

        $completed = $this->postJson(route('super-admin.repository.uploads.complete', $uploadId))
            ->assertOk()
            ->assertJsonPath('file.title', 'ISO Praktikum Jaringan');

        $file = RepositoryFile::firstOrFail();
        Storage::disk('repository')->assertExists($file->path);
        $this->assertSame($contents, Storage::disk('repository')->get($file->path));
        $this->assertSame('http://10.0.0.10'.route('repository.download', $file, false), $completed->json('file.links.local'));

        $this->get(route('repository.download', $file))
            ->assertOk()
            ->assertHeader('accept-ranges', 'bytes')
            ->assertDownload('praktikum-jaringan.iso');

        $this->withHeader('Range', 'bytes=0-9')
            ->get(route('repository.download', $file))
            ->assertStatus(206)
            ->assertHeader('content-range', 'bytes 0-9/600000');
    }

    public function test_nginx_mode_uses_internal_redirect_and_inactive_files_are_hidden(): void
    {
        $admin = $this->userWithRole('Super Admin');
        $file = $this->storedFile($admin, 'ubuntu.iso', 'disk-image');

        config([
            'repository.download_driver' => 'nginx',
            'repository.accel_redirect_prefix' => '/_protected_repository',
        ]);

        $this->get(route('repository.download', $file))
            ->assertOk()
            ->assertHeader('x-accel-redirect', '/_protected_repository/'.$file->path)
            ->assertHeader('content-length', (string) $file->size);

        $file->update(['is_active' => false]);
        $this->get(route('repository.download', $file))->assertNotFound();
    }

    public function test_invalid_extension_is_rejected_and_deleting_metadata_removes_file(): void
    {
        $admin = $this->userWithRole('Super Admin');

        $this->actingAs($admin)->withSession(['active_role' => 'Super Admin'])
            ->postJson(route('super-admin.repository.uploads.initialize'), [
                'title' => 'Script berbahaya',
                'original_name' => 'shell.php',
                'size' => 100,
            ])->assertUnprocessable()->assertJsonValidationErrors('original_name');

        $file = $this->storedFile($admin, 'materi.rar', 'arsip');
        $this->delete(route('super-admin.repository.files.destroy', $file))
            ->assertRedirect()->assertSessionHas('success');

        Storage::disk('repository')->assertMissing($file->path);
        $this->assertDatabaseMissing('repository_files', ['id' => $file->id]);
    }

    private function storedFile(User $user, string $name, string $contents): RepositoryFile
    {
        $token = (string) Str::uuid();
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $path = "files/{$token}.{$extension}";
        Storage::disk('repository')->put($path, $contents);

        return RepositoryFile::create([
            'public_token' => $token,
            'title' => pathinfo($name, PATHINFO_FILENAME),
            'original_name' => $name,
            'path' => $path,
            'extension' => $extension,
            'mime_type' => 'application/octet-stream',
            'size' => strlen($contents),
            'is_active' => true,
            'uploaded_by' => $user->id,
            'published_at' => now(),
        ]);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }
}
