<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RabRealisasi extends Model
{
    use HasFactory;

    protected $table = 'rab_realisasis';

    protected $fillable = [
        'rab_submission_id',
        'nomor_realisasi',
        'status', // draft, diajukan, diverifikasi_keuangan, disetujui, ditolak
        'total_penerimaan',
        'total_pengeluaran',
        'saldo_akhir',
        'pembuat_nama',
        'pembuat_nik',
        'pembuat_jabatan',
        'tanggal_dibuat',
        'pemeriksa_nama',
        'pemeriksa_nik',
        'pemeriksa_jabatan',
        'tanggal_diperiksa',
        'penyetuju_nama',
        'penyetuju_nik',
        'penyetuju_jabatan',
        'tanggal_disetujui',
        'catatan',
        'catatan_keuangan',
    ];

    protected $casts = [
        'tanggal_dibuat' => 'date',
        'tanggal_diperiksa' => 'date',
        'tanggal_disetujui' => 'date',
        'total_penerimaan' => 'decimal:2',
        'total_pengeluaran' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function rabSubmission()
    {
        return $this->belongsTo(RabSubmission::class, 'rab_submission_id');
    }

    public function items()
    {
        return $this->hasMany(RabRealisasiItem::class, 'rab_realisasi_id')->orderBy('sort_order')->orderBy('tanggal')->orderBy('id');
    }

    public function evidences()
    {
        return $this->hasMany(RabRealisasiEvidence::class, 'rab_realisasi_id')->latest();
    }

    /**
     * Hitung ulang total penerimaan, pengeluaran, dan saldo per item secara berurutan.
     */
    public function recalculateTotals(): self
    {
        $runningSaldo = 0;
        $totalPenerimaan = 0;
        $totalPengeluaran = 0;

        $items = $this->items()->orderBy('sort_order')->orderBy('tanggal')->orderBy('id')->get();
        foreach ($items as $item) {
            $penerimaan = (float) ($item->penerimaan ?? 0);
            $pengeluaran = (float) ($item->pengeluaran ?? 0);

            $runningSaldo = $runningSaldo + $penerimaan - $pengeluaran;
            $item->saldo = $runningSaldo;
            $item->saveQuietly();

            $totalPenerimaan += $penerimaan;
            $totalPengeluaran += $pengeluaran;
        }

        $this->total_penerimaan = $totalPenerimaan;
        $this->total_pengeluaran = $totalPengeluaran;
        $this->saldo_akhir = $runningSaldo;
        $this->save();

        return $this;
    }

    public static function ensureTableExists(): void
    {
        if (!Schema::hasTable('rab_realisasis')) {
            Schema::create('rab_realisasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_submission_id')->constrained('rab_submissions')->cascadeOnDelete();
                $table->string('nomor_realisasi')->nullable();
                $table->string('status', 30)->default('draft');
                $table->decimal('total_penerimaan', 15, 2)->default(0);
                $table->decimal('total_pengeluaran', 15, 2)->default(0);
                $table->decimal('saldo_akhir', 15, 2)->default(0);

                $table->string('pembuat_nama')->nullable();
                $table->string('pembuat_nik', 50)->nullable();
                $table->string('pembuat_jabatan')->nullable();
                $table->date('tanggal_dibuat')->nullable();

                $table->string('pemeriksa_nama')->nullable();
                $table->string('pemeriksa_nik', 50)->nullable();
                $table->string('pemeriksa_jabatan')->nullable();
                $table->date('tanggal_diperiksa')->nullable();

                $table->string('penyetuju_nama')->nullable();
                $table->string('penyetuju_nik', 50)->nullable();
                $table->string('penyetuju_jabatan')->nullable();
                $table->date('tanggal_disetujui')->nullable();

                $table->text('catatan')->nullable();
                $table->text('catatan_keuangan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rab_realisasi_items')) {
            Schema::create('rab_realisasi_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_realisasi_id')->constrained('rab_realisasis')->cascadeOnDelete();
                $table->date('tanggal');
                $table->text('uraian');
                $table->decimal('penerimaan', 15, 2)->default(0);
                $table->decimal('pengeluaran', 15, 2)->default(0);
                $table->decimal('saldo', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rab_realisasi_evidences')) {
            Schema::create('rab_realisasi_evidences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_realisasi_id')->constrained('rab_realisasis')->cascadeOnDelete();
                $table->string('judul');
                $table->string('file_path');
                $table->string('file_name');
                $table->string('file_type', 100)->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }
}
