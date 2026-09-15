<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionAddendum extends Model
{
    protected $table = 'subscription_addendum_agreements';

    protected $fillable = [
        'subscription_id',
        'addendum_code',
        'effective_date',
        'agreement_end_date',
        'new_items_json',
        'pro_rated_amount',
        'new_monthly_amount',
        'billing_day_of_month',
        'pro_rated_days',
        'pro_rated_until',
        'signed_url',
        'expires_at',
        'sent_at',
        'signed_at',
        'signature_image',
        'signed_pdf',
        'status',
    ];

    protected $casts = [
        'effective_date'     => 'date',
        'agreement_end_date' => 'date',
        'pro_rated_until'    => 'date',
        'expires_at'         => 'datetime',
        'sent_at'            => 'datetime',
        'signed_at'          => 'datetime',
        'new_items_json'     => 'array',
    ];

    public function subscription()
    {
        return $this->belongsTo(MonthlySubscription::class, 'subscription_id');
    }

    public static function generateCode(): string
    {
        return 'ADD-' . now()->format('Ymd') . '-' . rand(100, 999);
    }
}
