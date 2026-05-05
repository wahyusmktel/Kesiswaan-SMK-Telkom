<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KantinOrder extends Model
{
    protected $fillable = [
        'kantin_id',
        'student_id',
        'order_number',
        'total_amount',
        'payment_method',
        'status',
        'notes',
    ];

    public function kantin()
    {
        return $this->belongsTo(User::class, 'kantin_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function items()
    {
        return $this->hasMany(KantinOrderItem::class);
    }
}
