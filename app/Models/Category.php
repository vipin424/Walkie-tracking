<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image_path',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
