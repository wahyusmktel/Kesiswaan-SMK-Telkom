<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    use HasFactory;

    protected $table = 'rab_items';

    protected $fillable = [
        'rab_submission_id',
        'uraian_kegiatan',
        'spesifikasi',
        'satuan',
        'volume',
        'harga_satuan',
        'jumlah',
        'catatan',
        'sort_order',
    ];

    protected $casts = [
        'volume' => 'float',
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function submission()
    {
        return $this->belongsTo(RabSubmission::class, 'rab_submission_id');
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->jumlah = ($item->volume ?? 1) * ($item->harga_satuan ?? 0);
        });
    }
}
