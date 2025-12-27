<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAgreement extends Model
{
    
    protected $fillable = [
        'order_id',
        'agreement_code',
        'expires_at',
        'signed_at',
        'aadhaar_front',
        'aadhaar_back',
        'aadhaar_full',
        'aadhaar_uploaded_at',
        'aadhaar_uploaded_by',
        'aadhaar_status',
        'signature_image',
        'status',
    ];



    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    protected $casts = [
        'expires_at' => 'datetime',
        'signed_at' => 'datetime',
        'aadhaar_uploaded_at' => 'datetime',
    ];
}
