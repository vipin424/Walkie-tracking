<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id','total_amount','advance_paid','due_amount','payment_status','payment_mode'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
