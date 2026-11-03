<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationDate extends Model
{
    protected $fillable = ['quotation_id', 'event_date', 'label', 'sort_order'];

    protected $casts = ['event_date' => 'date'];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationDateItem::class);
    }
}
