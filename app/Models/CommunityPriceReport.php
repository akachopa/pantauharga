<?php

namespace App\Models;

use Database\Factories\CommunityPriceReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityPriceReport extends Model
{
    /** @use HasFactory<CommunityPriceReportFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'commodity_id',
        'market_id',
        'reported_price',
        'official_price',
        'reporter_name',
        'phone',
        'note',
        'status',
        'review_note',
        'reviewed_at',
        'created_at',
    ];

    protected $casts = [
        'reported_price' => 'decimal:2',
        'official_price' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class, 'market_id');
    }
}
