<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReservation extends Model
{
    protected $fillable = [
        'item_id',
        'order_id',
        'quantity_reserved',
        'locked_from',
        'locked_until',
        'reason',
    ];

    protected $casts = [
        'locked_from' => 'date',
        'locked_until' => 'date',
        'quantity_reserved' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
