<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringPoint extends Model
{
    public $timestamps = false;

    protected $fillable = ['market_id', 'region_id', 'code', 'name', 'point_type', 'is_demo', 'is_active'];

    protected $casts = [
        'is_demo' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class, 'market_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function assignments()
    {
        return $this->hasMany(PointAssignment::class, 'point_id');
    }
}
