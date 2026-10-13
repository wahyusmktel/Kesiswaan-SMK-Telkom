<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabRealisasiItem extends Model
{
    use HasFactory;

    protected $table = 'rab_realisasi_items';

    protected $fillable = [
        'rab_realisasi_id',
        'tanggal',
        'uraian',
        'penerimaan',
        'pengeluaran',
        'saldo',
        'keterangan',
        'sort_order',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'penerimaan' => 'decimal:2',
        'pengeluaran' => 'decimal:2',
        'saldo' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function realisasi()
    {
        return $this->belongsTo(RabRealisasi::class, 'rab_realisasi_id');
    }
}
