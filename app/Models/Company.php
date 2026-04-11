<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Company extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'primary_color', 'secondary_color',
        'email', 'phone', 'address', 'plan_id', 'subscription_expires_at', 'status',
    ];

    protected $casts = ['subscription_expires_at' => 'date'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function subscriptionInvoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }

    public function isSubscriptionActive(): bool
    {
        return $this->status === 'active'
            && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function ordersThisMonth(): int
    {
        return $this->orders()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    }

    public function hasReachedOrderLimit(): bool
    {
        if (!$this->plan) return false;
        return $this->ordersThisMonth() >= $this->plan->max_orders;
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('image/logo.png');
    }

    // Absolute path for dompdf PDF rendering
    public function getLogoAbsolutePathAttribute(): string
    {
        if ($this->logo && file_exists(storage_path('app/public/' . $this->logo))) {
            return storage_path('app/public/' . $this->logo);
        }
        return public_path('image/logo.png');
    }
}
