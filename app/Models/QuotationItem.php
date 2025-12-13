<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id','item_name','item_type','description','quantity','unit_price','tax_percent','total_price'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
