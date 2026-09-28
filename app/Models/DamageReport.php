<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    protected $fillable = [
        'report_code', 'client_id', 'subscription_id',
        'client_name', 'client_phone', 'client_email', 'billing_details',
        'walkie_units',                         // JSON array [{model, serial, unit_id}]
        'damage_description', 'damage_date', 'damage_photos',
        'replacement_amount', 'notes',
        'pdf_path', 'status', 'sent_at', 'paid_at',
    ];

    protected $casts = [
        'damage_date'   => 'date',
        'damage_photos' => 'array',
        'walkie_units'  => 'array',
        'sent_at'       => 'datetime',
        'paid_at'       => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subscription()
    {
        return $this->belongsTo(MonthlySubscription::class, 'subscription_id');
    }

    public static function generateCode(): string
    {
        return 'DMG-' . now()->format('Ymd') . '-' . rand(1000, 9999);
    }
}
