<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnEntry extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'dispatch_id','dispatch_item_id','returned_qty','return_date','remarks'
    ];

    protected $casts = [
        'return_date' => 'date'
    ];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DispatchItem::class, 'dispatch_item_id');
    }
}
