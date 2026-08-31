<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_report_buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_report_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_report_building_id')->constrained()->cascadeOnDelete();
            $table->uuid('public_token')->unique();
            $table->string('name', 120);
            $table->string('code', 40)->unique();
            $table->string('type', 40)->default('lainnya')->index();
            $table->string('floor', 40)->nullable();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_report_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ticket_number', 30)->unique();
            $table->string('reporter_name', 120);
            $table->string('reporter_identifier', 80)->nullable();
            $table->string('reporter_type', 30)->default('siswa');
            $table->string('contact', 80)->nullable();
            $table->string('asset_name', 160);
            $table->string('category', 40)->index();
            $table->string('urgency', 20)->default('normal')->index();
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('status', 30)->default('baru')->index();
            $table->text('admin_notes')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->char('ip_hash', 64)->nullable();
            $table->timestamps();
            $table->index(['asset_report_location_id', 'created_at']);
            $table->index(['status', 'urgency', 'created_at']);
        });

        $now = now();
        $buildingIds = [];
        foreach (range(1, 4) as $number) {
            $buildingIds[$number] = DB::table('asset_report_buildings')->insertGetId([
                'name' => 'Gedung '.$number,
                'code' => 'GDG-'.$number,
                'description' => 'Area Gedung '.$number.' SMK Telkom Lampung',
                'is_active' => true,
                'sort_order' => $number,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $locations = [
            [1, 'Ruang Kelas 1', 'kelas', 'Lantai 1'], [1, 'Ruang Kelas 2', 'kelas', 'Lantai 1'],
            [1, 'Toilet Pria', 'toilet', 'Lantai 1'], [1, 'Toilet Wanita', 'toilet', 'Lantai 1'],
            [1, 'Ruang Guru', 'ruang_kerja', 'Lantai 1'], [1, 'Ruang Tata Usaha', 'ruang_kerja', 'Lantai 1'],
            [2, 'Ruang Kelas 3', 'kelas', 'Lantai 1'], [2, 'Ruang Kelas 4', 'kelas', 'Lantai 1'],
            [2, 'Laboratorium Komputer 1', 'laboratorium', 'Lantai 1'], [2, 'Laboratorium Komputer 2', 'laboratorium', 'Lantai 2'],
            [2, 'Toilet Pria', 'toilet', 'Lantai 1'], [2, 'Toilet Wanita', 'toilet', 'Lantai 1'],
            [3, 'Ruang Kelas 5', 'kelas', 'Lantai 1'], [3, 'Ruang Kelas 6', 'kelas', 'Lantai 2'],
            [3, 'Perpustakaan', 'perpustakaan', 'Lantai 1'], [3, 'Ruang BK', 'ruang_kerja', 'Lantai 1'],
            [3, 'UKS', 'uks', 'Lantai 1'], [3, 'Toilet Pria', 'toilet', 'Lantai 1'],
            [3, 'Toilet Wanita', 'toilet', 'Lantai 1'],
            [4, 'Aula Sekolah', 'aula', 'Lantai 1'], [4, 'Mushola', 'tempat_ibadah', 'Lantai 1'],
            [4, 'Kantin', 'kantin', 'Lantai 1'], [4, 'Gudang Sarpras', 'gudang', 'Lantai 1'],
            [4, 'Pos Security', 'pos_keamanan', 'Lantai 1'], [4, 'Area Parkir', 'area_umum', null],
        ];

        foreach ($locations as $index => [$building, $name, $type, $floor]) {
            DB::table('asset_report_locations')->insert([
                'asset_report_building_id' => $buildingIds[$building],
                'public_token' => (string) Str::uuid(),
                'name' => $name,
                'code' => sprintf('GDG%d-%02d', $building, $index + 1),
                'type' => $type,
                'floor' => $floor,
                'description' => 'Laporkan kerusakan aset atau fasilitas di '.$name.'.',
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_reports');
        Schema::dropIfExists('asset_report_locations');
        Schema::dropIfExists('asset_report_buildings');
    }
};
