<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code','quotation_id',
        'client_name','client_email','client_phone',
        'event_from','event_to','event_time','event_location','handle_type','total_days',
        'subtotal','tax_amount',
        'extra_charge_type','extra_charge_rate','extra_charge_total',
        'discount_amount','total_amount',
        'advance_paid','balance_amount','agreement_required',
        'status','created_by','notes','bill_to','pdf_path','security_deposit','damage_charge','late_fee','refund_amount','deposit_adjusted','amount_due','settlement_status','payment_status','settlement_date','final_payable'
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

    /**
     * Get all payment transactions for this order
     */
    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get all payment reminders sent for this order
     */
    public function paymentReminders()
    {
        return $this->hasMany(PaymentReminder::class);
    }

    /**
     * Get total amount paid for this order
     */
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Get the latest payment transaction
     */
    public function latestPayment()
    {
        return $this->hasOne(PaymentTransaction::class)->latestOfMany('paid_at');
    }

    /**
     * Check if order is fully paid
     */
    public function isFullyPaid()
    {
        return $this->payment_status === 'paid' && $this->final_payable <= 0;
    }

    /**
     * Check if payment is overdue (for orders with event passed)
     */
    public function isPaymentOverdue()
    {
        return $this->payment_status !== 'paid' 
            && $this->final_payable > 0 
            && now()->greaterThan($this->event_to);
    }
}
