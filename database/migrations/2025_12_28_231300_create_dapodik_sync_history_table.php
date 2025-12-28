<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dapodik_sync_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['import', 'sync'])->default('sync');
            $table->integer('total_records')->default(0);
            $table->integer('inserted_count')->default(0);
            $table->integer('updated_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add last_synced_at to master_siswa
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->timestamp('last_synced_at')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapodik_sync_history');
        
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->dropColumn('last_synced_at');
        });
    }
};
