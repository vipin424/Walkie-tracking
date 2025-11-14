<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id','item_type','brand','model','quantity','returned_qty','rate_per_day','total_amount'
    ];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }
}
