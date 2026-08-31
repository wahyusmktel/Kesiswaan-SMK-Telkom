<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReport extends Model
{
    public const STATUSES = [
        'baru' => 'Baru',
        'diverifikasi' => 'Diverifikasi',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    public const CATEGORIES = [
        'rusak' => 'Rusak/Tidak Berfungsi',
        'hilang' => 'Hilang',
        'kebersihan' => 'Kebersihan',
        'keselamatan' => 'Keselamatan',
        'listrik' => 'Listrik',
        'air_sanitasi' => 'Air dan Sanitasi',
        'jaringan' => 'Jaringan/Internet',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'asset_report_location_id', 'handled_by', 'ticket_number', 'reporter_name',
        'reporter_identifier', 'reporter_type', 'contact', 'asset_name', 'category',
        'urgency', 'description', 'photo_path', 'status', 'admin_notes', 'handled_at',
        'completed_at', 'ip_hash',
    ];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function location()
    {
        return $this->belongsTo(AssetReportLocation::class, 'asset_report_location_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
