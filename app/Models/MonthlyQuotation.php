<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MonthlyQuotation extends Model
{
    protected $fillable = [
        'code',
        'client_name', 'client_company', 'client_email', 'client_phone', 'client_address', 'client_gstin',
        'bill_to',
        'validity_days', 'payment_terms', 'delivery_timeline',
        'subtotal', 'gst_percent', 'gst_amount', 'total_amount',
        'notes', 'status', 'pdf_path', 'created_by',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'gst_percent'  => 'decimal:2',
        'gst_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
        'validity_days'=> 'integer',
    ];

    public function items()
    {
        return $this->hasMany(MonthlyQuotationItem::class)->orderBy('sort_order');
    }

    public static function generateCode(): string
    {
        $date   = now()->format('Ymd');
        $last   = static::whereDate('created_at', today())->count() + 1;
        $seq    = str_pad($last, 3, '0', STR_PAD_LEFT);
        return "MQ-{$date}-{$seq}";
    }

    public function getValidTillAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->addDays($this->validity_days)->format('d M Y')
            : now()->addDays($this->validity_days)->format('d M Y');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent'  => '<span class="badge bg-success">Sent</span>',
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
