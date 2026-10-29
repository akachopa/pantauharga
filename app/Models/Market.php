<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    public $timestamps = false;

    protected $fillable = ['region_id', 'code', 'name', 'address', 'market_days', 'latitude', 'longitude', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function monitoringPoints()
    {
        return $this->hasMany(MonitoringPoint::class, 'market_id');
    }
}
