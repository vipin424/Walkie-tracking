<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'transaction_id',
        'notes',
        'paid_at',
        'recorded_by'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get formatted payment method
     */
    public function getFormattedMethodAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->payment_method));
    }
}
