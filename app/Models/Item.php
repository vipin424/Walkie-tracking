<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Item extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'description',
        'unit_price',
        'tax_percent',
        'is_active'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}
