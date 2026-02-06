<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'channel',
        'status',
        'message',
        'sent_at',
        'sent_by'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
