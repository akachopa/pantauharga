<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;

    protected $fillable = ['parent_id', 'code', 'name', 'level'];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function markets()
    {
        return $this->hasMany(Market::class, 'region_id');
    }

    public function monitoringPoints()
    {
        return $this->hasMany(MonitoringPoint::class, 'region_id');
    }
}
