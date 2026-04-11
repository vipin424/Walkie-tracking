<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Client extends Model
{
    use HasFactory, BelongsToCompany;
    protected $fillable = ['name', 'contact_number', 'company_name', 'email', 'company_id'];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }
}
