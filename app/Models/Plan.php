<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'features', 'max_orders', 'max_invoices', 'max_users', 'is_active'];

    protected $casts = ['features' => 'array'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
