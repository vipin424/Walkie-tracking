<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'contact_number', 'company_name'];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
