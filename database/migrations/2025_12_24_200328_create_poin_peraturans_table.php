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
        Schema::create('poin_peraturans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poin_category_id')->constrained('poin_categories')->onDelete('cascade');
            $table->string('pasal');
            $table->string('ayat')->nullable();
            $table->text('deskripsi');
            $table->integer('bobot_poin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poin_peraturans');
    }
};
