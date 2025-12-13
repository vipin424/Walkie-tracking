<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id','item_type','brand','model','quantity',
        'rental_type','rate_per_day','rate_per_month','total_amount'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
