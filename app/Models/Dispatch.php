<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','client_id','dispatch_date','expected_return_date',
        'status','total_items'
    ];

    protected $casts = [
        'dispatch_date' => 'date',
        'expected_return_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_PARTIAL = 'Partially Returned';
    public const STATUS_RETURNED = 'Returned';

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnEntry::class);
    }

    public function recalcStatusAndTotals(): void
    {
        $total = $this->items()->sum('quantity');
        $returned = $this->items()->sum('returned_qty');
        $this->total_items = $total;

        if ($returned === 0) {
            $this->status = self::STATUS_ACTIVE;
        } elseif ($returned < $total) {
            $this->status = self::STATUS_PARTIAL;
        } else {
            $this->status = self::STATUS_RETURNED;
        }
        $this->save();
    }

    public function whatsappMessage()
    {
        $items = $this->items->map(function($i) {
            return "- {$i->item_type} ({$i->brand} {$i->model}) × {$i->quantity}";
        })->implode("\n");

        $status = ucfirst($this->status);
        $clientName = $this->client->name;
        $dispatchCode = $this->code;
        $date = $this->dispatch_date->format('d M Y');
        $total = $this->items->sum('quantity');

        return <<<MSG
    Hello {$clientName} 👋

    Here are your dispatch details:
    ---------------------------------
    Dispatch ID: {$dispatchCode}
    Date: {$date}
    Total Items: {$total}

    Items:
    {$items}

    Status: {$status}

    Thank you,
    Crewrent Enterprises
    MSG;
    }

    public function whatsappLink()
    {
        $number = preg_replace('/[^0-9]/', '', $this->client->contact_number);
        $text = urlencode($this->whatsappMessage());
        return "https://wa.me/{$number}?text={$text}";
    }


}
