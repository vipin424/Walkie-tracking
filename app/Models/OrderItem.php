<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','item_name','item_type',
        'description','quantity',
        'unit_price','tax_percent','total_price'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
