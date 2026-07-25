<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erapor_reference_imports', function (Blueprint $table) {
            $table->id();
            $table->string('dataset', 80);
            $table->string('source', 120)->default('e-Rapor SMK');
            $table->string('source_version', 30);
            $table->char('checksum', 64);
            $table->unsignedInteger('files_count')->default(0);
            $table->unsignedInteger('records_total')->default(0);
            $table->unsignedInteger('records_imported')->default(0);
            $table->unsignedInteger('records_skipped')->default(0);
            $table->unsignedInteger('records_conflicted')->default(0);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->unique(['dataset', 'source_version', 'checksum'], 'erapor_ref_import_identity_unique');
            $table->index(['dataset', 'status']);
        });

        Schema::create('erapor_ref_curricula', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('name');
            $table->unsignedInteger('education_level_id')->nullable();
            $table->unsignedBigInteger('major_external_id')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('source_updated_at')->nullable();
            $table->foreignId('reference_import_id')->nullable()
                ->constrained('erapor_reference_imports')->nullOnDelete();
            $table->timestamps();

            $table->index(['education_level_id', 'is_active'], 'erapor_curricula_level_active_idx');
        });

        Schema::create('erapor_ref_subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->string('name');
            $table->unsignedBigInteger('major_external_id')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('source_updated_at')->nullable();
            $table->foreignId('reference_import_id')->nullable()
                ->constrained('erapor_reference_imports')->nullOnDelete();
            $table->timestamps();

            $table->index(['name', 'is_active']);
        });

        Schema::create('erapor_ref_curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erapor_ref_curriculum_id')
                ->constrained('erapor_ref_curricula')->cascadeOnDelete();
            $table->foreignId('erapor_ref_subject_id')
                ->constrained('erapor_ref_subjects')->cascadeOnDelete();
            $table->unsignedSmallInteger('education_level_id');
            $table->unsignedSmallInteger('hours')->default(0);
            $table->unsignedSmallInteger('maximum_hours')->default(0);
            $table->unsignedSmallInteger('curriculum_status')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('reference_import_id')->nullable()
                ->constrained('erapor_reference_imports')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['erapor_ref_curriculum_id', 'erapor_ref_subject_id', 'education_level_id'],
                'erapor_curriculum_subject_unique'
            );
            $table->index(['education_level_id', 'is_active'], 'erapor_curriculum_subject_level_active_idx');
        });

        Schema::create('erapor_subject_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')
                ->unique()->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('erapor_ref_subject_id')
                ->constrained('erapor_ref_subjects')->restrictOnDelete();
            $table->decimal('confidence', 5, 2)->default(100);
            $table->text('notes')->nullable();
            $table->foreignId('mapped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('mapped_at');
            $table->timestamps();

            $table->index('erapor_ref_subject_id');
        });

        Schema::create('erapor_teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')
                ->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')
                ->constrained('mata_pelajarans')->restrictOnDelete();
            $table->foreignId('master_guru_id')
                ->constrained('master_gurus')->restrictOnDelete();
            $table->foreignId('erapor_subject_mapping_id')->nullable()
                ->constrained('erapor_subject_mappings')->nullOnDelete();
            $table->string('subject_group', 40)->nullable();
            $table->unsignedSmallInteger('sort_order')->nullable();
            $table->decimal('passing_grade', 5, 2)->nullable();
            $table->enum('source', ['schedule', 'manual'])->default('schedule');
            $table->char('source_key', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->json('sync_metadata')->nullable();
            $table->timestamps();

            $table->unique(
                ['tahun_pelajaran_id', 'rombel_id', 'mata_pelajaran_id', 'master_guru_id'],
                'erapor_assignment_identity_unique'
            );
            $table->index(['tahun_pelajaran_id', 'is_active']);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'configure erapor',
            'guard_name' => 'web',
        ]);

        Role::query()
            ->whereIn('name', ['Super Admin', 'Waka Kesiswaan', 'Operator', 'Kurikulum'])
            ->get()
            ->each(fn (Role $role) => $role->givePermissionTo($permission));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permission = Permission::query()
            ->where('name', 'configure erapor')
            ->where('guard_name', 'web')
            ->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->delete();
        }

        Schema::dropIfExists('erapor_teaching_assignments');
        Schema::dropIfExists('erapor_subject_mappings');
        Schema::dropIfExists('erapor_ref_curriculum_subjects');
        Schema::dropIfExists('erapor_ref_subjects');
        Schema::dropIfExists('erapor_ref_curricula');
        Schema::dropIfExists('erapor_reference_imports');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
