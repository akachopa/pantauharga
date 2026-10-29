<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionPlan extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'deadline',
        'progress_pct',
        'target_date',
        'notes',
        'evidence_path',
        'is_demo',
        'policy_case_id',
        'target_point_id',
        'created_at',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'deadline' => 'date',
        'target_date' => 'date',
        'progress_pct' => 'integer',
        'created_at' => 'datetime',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function targetPoint()
    {
        return $this->belongsTo(MonitoringPoint::class, 'target_point_id');
    }

    public function policyCase()
    {
        return $this->belongsTo(PolicyCase::class, 'policy_case_id');
    }
}
