<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'company_name',
        'gst_number',
        'pan_number',
        'address',
        'city',
        'type',
    ];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MonthlySubscription::class);
    }
}
