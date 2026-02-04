<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tu_letter_codes', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->nullable(); // QMR, SDM, KEUANGAN, TATA USAHA
            $table->string('code')->unique();   // QMR.01, SDM.02, TATA.01
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('tu_incoming_letters', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('sender');           // Instansi Pengirim
            $table->string('subject');          // Keperluan / Perihal
            $table->string('letter_number')->nullable(); // Nomor surat dari pengirim
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('tu_outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->integer('number_sequence'); // 0001
            $table->foreignId('letter_code_id')->constrained('tu_letter_codes');
            $table->date('date');
            $table->string('subject');
            $table->string('recipient')->nullable();
            $table->string('full_number')->unique(); // 0001/SMKTEL-LPG/TATA.01/I/2026
            $table->foreignId('user_id')->constrained('users'); // Diajukan oleh
            $table->timestamps();
        });

        Schema::create('tu_letter_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('letter_code_id')->constrained('tu_letter_codes');
            $table->string('subject');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('outgoing_letter_id')->nullable()->constrained('tu_outgoing_letters')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tu_letter_requests');
        Schema::dropIfExists('tu_outgoing_letters');
        Schema::dropIfExists('tu_incoming_letters');
        Schema::dropIfExists('tu_letter_codes');
    }
};
