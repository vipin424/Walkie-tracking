<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionAgreement extends Model
{
    protected $table = 'monthly_subscription_agreements';

    protected $fillable = [
        'subscription_id',
        'agreement_code',
        'agreement_start_date',
        'agreement_end_date',
        'security_deposit',
        'signed_url',

        'expires_at',
        'sent_at',
        'signed_at',
        'signature_image',
        'signed_pdf',
        'status',
    ];

    protected $casts = [
        'agreement_start_date' => 'date',
        'agreement_end_date'   => 'date',
        'expires_at'           => 'datetime',
        'sent_at'              => 'datetime',
        'signed_at'            => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(MonthlySubscription::class, 'subscription_id');
    }

    public static function generateCode(): string
    {
        return 'SAGG-' . now()->format('Ymd') . '-' . rand(100, 999);
    }
}
