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
        Schema::create('synced_assets', function (Blueprint $table) {
            $table->id('local_id'); // ID unik di Aplikasi-Izin
            $table->unsignedBigInteger('asset_id')->unique(); // ID dari Aplikasi Aset
            $table->string('asset_code_ypt')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('condition')->nullable();
            $table->string('current_status')->default('Tersedia');
            $table->string('institution')->nullable();
            $table->string('building')->nullable();
            $table->string('room')->nullable();
            $table->string('faculty')->nullable();
            $table->string('department')->nullable();
            $table->string('person_in_charge')->nullable();
            $table->string('asset_function')->nullable();
            $table->string('funding_source')->nullable();
            $table->string('sequence_number')->nullable();
            $table->string('status')->default('Aktif');
            $table->decimal('purchase_cost', 15, 2)->nullable();
            $table->decimal('salvage_value', 15, 2)->nullable();
            $table->integer('useful_life')->nullable();
            $table->decimal('book_value', 15, 2)->nullable();
            $table->date('disposal_date')->nullable();
            $table->string('disposal_method')->nullable();
            $table->text('disposal_reason')->nullable();
            $table->timestamp('last_synced_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('synced_assets');
    }
};
