<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'company_id', 'plan_id', 'invoice_number', 'amount',
        'period_from', 'period_to', 'status', 'payment_method', 'transaction_id', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime', 'period_from' => 'date', 'period_to' => 'date'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
