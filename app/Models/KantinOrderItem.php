<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KantinOrderItem extends Model
{
    protected $fillable = [
        'kantin_order_id',
        'kantin_menu_id',
        'menu_name',
        'quantity',
        'price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(KantinOrder::class, 'kantin_order_id');
    }

    public function menu()
    {
        return $this->belongsTo(KantinMenu::class, 'kantin_menu_id');
    }
}
