<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'action',
        'meta',
    ];
}
