<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RabSubmission extends Model
{
    use HasFactory;

    protected $table = 'rab_submissions';

    protected $fillable = [
        'user_id',
        'nomor_pengajuan',
        'jenis_pengajuan',
        'judul_pengajuan',
        'tahun_anggaran',
        'nomor_akun',
        'nama_akun',
        'drk',
        'kebutuhan_waktu',
        'status',
        'total_anggaran',
        'pembuat_nama',
        'pembuat_nik',
        'pembuat_jabatan',
        'tanggal_dibuat',
        'catatan_anggaran',
        'pemeriksa_nama',
        'pemeriksa_nik',
        'pemeriksa_jabatan',
        'tanggal_diperiksa',
        'penyetuju_nama',
        'penyetuju_nik',
        'penyetuju_jabatan',
        'tanggal_disetujui',
        'catatan',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'date',
        'tanggal_diperiksa' => 'date',
        'tanggal_disetujui' => 'date',
        'total_anggaran' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RabItem::class, 'rab_submission_id')->orderBy('sort_order')->orderBy('id');
    }

    public function realisasi()
    {
        return $this->hasOne(RabRealisasi::class, 'rab_submission_id');
    }

    public function getTerbilangAttribute(): string
    {
        return \App\Helpers\TerbilangHelper::terbilangRupiah($this->total_anggaran);
    }

    public function recalculateTotal(): self
    {
        $this->total_anggaran = $this->items()->sum('jumlah');
        $this->save();
        return $this;
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('rab_submissions')) {
            Schema::create('rab_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nomor_pengajuan')->nullable();
                $table->string('jenis_pengajuan', 30)->default('rab'); // 'rab' atau 'reimburse'
                $table->string('judul_pengajuan');
                $table->string('tahun_anggaran', 20)->default(date('Y'));
                $table->string('nomor_akun', 50)->nullable();
                $table->string('nama_akun')->nullable();
                $table->text('drk')->nullable();
                $table->string('kebutuhan_waktu')->nullable();
                $table->string('status', 30)->default('draft');
                $table->decimal('total_anggaran', 15, 2)->default(0);

                $table->string('pembuat_nama')->nullable();
                $table->string('pembuat_nik', 50)->nullable();
                $table->string('pembuat_jabatan')->nullable();
                $table->date('tanggal_dibuat')->nullable();

                $table->text('catatan_anggaran')->nullable();

                $table->string('pemeriksa_nama')->nullable();
                $table->string('pemeriksa_nik', 50)->nullable();
                $table->string('pemeriksa_jabatan')->nullable();
                $table->date('tanggal_diperiksa')->nullable();

                $table->string('penyetuju_nama')->nullable();
                $table->string('penyetuju_nik', 50)->nullable();
                $table->string('penyetuju_jabatan')->nullable();
                $table->date('tanggal_disetujui')->nullable();

                $table->text('catatan')->nullable();

                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('rab_submissions', 'jenis_pengajuan')) {
            Schema::table('rab_submissions', function (Blueprint $table) {
                $table->string('jenis_pengajuan', 30)->default('rab')->after('nomor_pengajuan');
            });
        }

        if (!Schema::hasTable('rab_items')) {
            Schema::create('rab_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_submission_id')->constrained('rab_submissions')->cascadeOnDelete();
                $table->text('uraian_kegiatan');
                $table->text('spesifikasi')->nullable();
                $table->string('satuan', 50)->default('unit');
                $table->decimal('volume', 10, 2)->default(1);
                $table->decimal('harga_satuan', 15, 2)->default(0);
                $table->decimal('jumlah', 15, 2)->default(0);
                $table->text('catatan')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('rab_items', 'catatan')) {
            Schema::table('rab_items', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('jumlah');
            });
        }

        RabRealisasi::ensureTableExists();
    }
}
