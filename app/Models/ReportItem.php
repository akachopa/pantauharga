<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'report_id',
        'commodity_id',
        'price',
        'stock',
        'unit',
        'price_change_pct',
        'price_change_amount',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'decimal:2',
        'price_change_pct' => 'decimal:2',
        'price_change_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }
}
