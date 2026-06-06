<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fingerprint_security_shifts')) {
            Schema::create('fingerprint_security_shifts', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->time('starts_at');
                $table->time('ends_at');
                $table->boolean('is_overnight')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('fingerprint_security_shift_assignments')) {
            Schema::create('fingerprint_security_shift_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('app_user_id')->unique();
                $table->foreignId('fingerprint_security_shift_id');
                $table->timestamps();

                $table->foreign('app_user_id', 'fp_sec_assign_user_fk')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();
                $table->foreign('fingerprint_security_shift_id', 'fp_sec_assign_shift_fk')
                    ->references('id')
                    ->on('fingerprint_security_shifts')
                    ->cascadeOnDelete();
            });
        }

        foreach ([
            [
                'code' => 'shift_1',
                'name' => 'Shift 1',
                'starts_at' => '07:00:00',
                'ends_at' => '14:00:00',
                'is_overnight' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'shift_2',
                'name' => 'Shift 2',
                'starts_at' => '14:00:00',
                'ends_at' => '22:00:00',
                'is_overnight' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'shift_3',
                'name' => 'Shift 3',
                'starts_at' => '22:00:00',
                'ends_at' => '07:00:00',
                'is_overnight' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ] as $shift) {
            DB::table('fingerprint_security_shifts')->updateOrInsert(
                ['code' => $shift['code']],
                $shift
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_security_shift_assignments');
        Schema::dropIfExists('fingerprint_security_shifts');
    }
};
