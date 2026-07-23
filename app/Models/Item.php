<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'type',
        'brand',
        'model',
        'description',
        'image_path',
        'unit_price',
        'security_deposit',
        'tax_percent',
        'total_stock',
        'buffer_days_before',
        'buffer_days_after',
        'is_active'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total_stock' => 'integer',
        'buffer_days_before' => 'integer',
        'buffer_days_after' => 'integer',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
