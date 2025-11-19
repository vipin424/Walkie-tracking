<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code','client_id','order_date','start_date','end_date',
        'event_name','location','delivery_type','delivery_charges','status','reminder_date',
        'remarks'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function payment() {
        return $this->hasOne(OrderPayment::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
