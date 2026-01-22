<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code','quotation_id',
        'client_name','client_email','client_phone',
        'event_from','event_to','handle_type','total_days',
        'subtotal','tax_amount',
        'extra_charge_type','extra_charge_rate','extra_charge_total',
        'discount_amount','total_amount',
        'advance_paid','balance_amount','agreement_required',
        'status','created_by','notes','bill_to','pdf_path','security_deposit','damage_charge','late_fee','refund_amount','deposit_adjusted','amount_due','settlement_status','settlement_date','final_payable'
    ];

    protected $casts = [
    'event_from' => 'date',
    'event_to'   => 'date',
    ];


    public function agreement()
    {
        return $this->hasOne(OrderAgreement::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function payment() {
        return $this->hasOne(OrderPayment::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public static function generateCode()
    {
        return 'ORD-' . now()->format('Ymd') . '-' . rand(100,999);
    }
}
