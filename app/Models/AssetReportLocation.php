<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReportLocation extends Model
{
    protected $fillable = [
        'asset_report_building_id', 'public_token', 'name', 'code', 'type',
        'floor', 'description', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    public function building()
    {
        return $this->belongsTo(AssetReportBuilding::class, 'asset_report_building_id');
    }

    public function reports()
    {
        return $this->hasMany(AssetReport::class);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('asset-report.public.create', $this);
    }
}
