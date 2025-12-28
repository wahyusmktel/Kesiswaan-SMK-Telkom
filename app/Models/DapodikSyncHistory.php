<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikSyncHistory extends Model
{
    protected $table = 'dapodik_sync_history';

    protected $fillable = [
        'user_id',
        'type',
        'total_records',
        'inserted_count',
        'updated_count',
        'failed_count',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
