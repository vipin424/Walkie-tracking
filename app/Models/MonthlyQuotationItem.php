<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyQuotationItem extends Model
{
    protected $fillable = [
        'monthly_quotation_id',
        'item_name', 'description', 'uom',
        'quantity', 'rate', 'amount', 'sort_order',
    ];

    protected $casts = [
        'quantity'   => 'decimal:2',
        'rate'       => 'decimal:2',
        'amount'     => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function quotation()
    {
        return $this->belongsTo(MonthlyQuotation::class, 'monthly_quotation_id');
    }
}
