<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySubscription extends Model
{
    protected $fillable = [
        'subscription_code', 'client_id', 'client_name', 'client_email', 'client_phone', 'cc_emails',
        'billing_start_date', 'billing_day_of_month', 'monthly_amount', 'items_json', 'notes', 'status', 'billing_details'
    ];

    protected $casts = [
        'billing_start_date' => 'date',
        'items_json' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(MonthlyInvoice::class, 'subscription_id');
    }

    public static function generateCode()
    {
        return 'SUB-' . now()->format('Ymd') . '-' . rand(100, 999);
    }
}
