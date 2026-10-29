<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyShipment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'supply_link_id',
        'scheduled_on',
        'quantity',
        'unit',
        'status',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'scheduled_on' => 'date',
        'quantity' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(SupplyLink::class, 'supply_link_id');
    }
}
