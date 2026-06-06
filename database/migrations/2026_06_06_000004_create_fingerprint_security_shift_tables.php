<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_security_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->boolean('is_overnight')->default(false);
            $table->timestamps();
        });

        Schema::create('fingerprint_security_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('fingerprint_security_shift_id')->constrained('fingerprint_security_shifts')->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table('fingerprint_security_shifts')->insert([
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
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_security_shift_assignments');
        Schema::dropIfExists('fingerprint_security_shifts');
    }
};
