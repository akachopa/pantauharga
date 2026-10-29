<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplyLink extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'point_id',
        'commodity_id',
        'notes',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function point(): BelongsTo
    {
        return $this->belongsTo(MonitoringPoint::class, 'point_id');
    }

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(SupplyShipment::class, 'supply_link_id');
    }
}
