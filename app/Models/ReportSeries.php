<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportSeries extends Model
{
    public $timestamps = false;
    protected $fillable = ['point_id', 'observed_at'];

    protected $casts = [
        'observed_at' => 'datetime',
    ];

    public function point()
    {
        return $this->belongsTo(MonitoringPoint::class, 'point_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'series_id');
    }
}
