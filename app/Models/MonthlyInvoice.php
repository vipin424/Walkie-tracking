<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyInvoice extends Model
{
    protected $fillable = [
        'subscription_id', 'invoice_code', 'billing_period_from', 'billing_period_to',
        'amount', 'pdf_path', 'status', 'sent_at', 'paid_at'
    ];

    protected $casts = [
        'billing_period_from' => 'date',
        'billing_period_to' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(MonthlySubscription::class, 'subscription_id');
    }

    public function paymentTransactions()
    {
        return $this->morphMany(PaymentTransaction::class, 'payable');
    }

    public static function generateCode()
    {
        return 'INV-' . now()->format('Ymd') . '-' . rand(1000, 9999);
    }
}
