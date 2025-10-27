<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id','payment_status','advance_amount','total_amount','remarks'
    ];

    public const STATUS_PAID = 'Paid';
    public const STATUS_UNPAID = 'Unpaid';
    public const STATUS_ADVANCE = 'Advance Received';

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(Dispatch::class);
    }
}
