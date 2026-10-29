<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointAssignment extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id', 'point_id', 'business_id', 'starts_on', 'ends_on', 'report_days'];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function point()
    {
        return $this->belongsTo(MonitoringPoint::class, 'point_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
