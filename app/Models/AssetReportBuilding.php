<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReportBuilding extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function locations()
    {
        return $this->hasMany(AssetReportLocation::class)->orderBy('sort_order')->orderBy('name');
    }
}
