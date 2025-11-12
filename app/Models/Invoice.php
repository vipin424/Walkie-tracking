<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'client_id', 'invoice_code', 'start_date', 'end_date',
        'total_amount', 'total_days', 'total_items', 'invoice_path',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'client_id', 'client_id');
    }
}
