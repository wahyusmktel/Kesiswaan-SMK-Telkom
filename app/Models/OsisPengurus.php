<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OsisPengurus extends Model
{
    use HasFactory;

    protected $table = 'osis_penguruses';
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function masterSiswa(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }

        if ($this->masterSiswa && $this->masterSiswa->foto) {
            return Storage::url($this->masterSiswa->foto);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=0284c7&color=ffffff';
    }
}
