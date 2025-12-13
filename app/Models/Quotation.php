<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quotation extends Model
{
    protected $fillable = [
        'code','client_name','client_email','client_phone','event_from','event_to','total_days',
        'notes','subtotal','tax_amount','discount_amount','extra_charge_type','extra_charge_rate','extra_charge_total','total_amount','status','created_by','pdf_path'
    ];

    protected static function booted()
    {
        static::creating(function ($quotation) {
            if (empty($quotation->code)) {
                $date = now()->format('Ymd');
                $random = strtoupper(Str::random(3));
                $quotation->code = 'QTN-' . $date . '-' . rand(100,999);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function logs()
    {
        return $this->hasMany(QuotationLog::class);
    }
}
