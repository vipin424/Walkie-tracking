<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalkieInventory extends Model
{
    protected $fillable = [
        'item_id',
        'serial_number',
        'status',
        'condition',
        'purchase_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    // Status constants
    const STATUS_AVAILABLE   = 'available';
    const STATUS_RENTED      = 'rented';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RETIRED     = 'retired';

    // Condition constants
    const CONDITION_GOOD = 'good';
    const CONDITION_FAIR = 'fair';
    const CONDITION_POOR = 'poor';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE   => 'Available',
            self::STATUS_RENTED      => 'Rented',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_RETIRED     => 'Retired',
        ];
    }

    public static function conditionOptions(): array
    {
        return [
            self::CONDITION_GOOD => 'Good',
            self::CONDITION_FAIR => 'Fair',
            self::CONDITION_POOR => 'Poor',
        ];
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available'   => '<span class="badge bg-success">Available</span>',
            'rented'      => '<span class="badge bg-warning text-dark">Rented</span>',
            'maintenance' => '<span class="badge bg-info text-dark">Maintenance</span>',
            'retired'     => '<span class="badge bg-secondary">Retired</span>',
            default       => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }

    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            'good' => '<span class="badge bg-success-subtle text-success border border-success-subtle">Good</span>',
            'fair' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Fair</span>',
            'poor' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Poor</span>',
            default => '<span class="badge bg-secondary">' . $this->condition . '</span>',
        };
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
