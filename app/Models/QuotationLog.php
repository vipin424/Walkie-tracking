<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLog extends Model
{
    protected $fillable = ['quotation_id','user_id','action','meta'];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
