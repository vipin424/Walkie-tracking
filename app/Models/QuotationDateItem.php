<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationDateItem extends Model
{
    protected $fillable = [
        'quotation_date_id', 'item_name', 'item_type', 'description',
        'quantity', 'unit_price', 'tax_percent', 'total_price'
    ];

    public function quotationDate()
    {
        return $this->belongsTo(QuotationDate::class);
    }
}
