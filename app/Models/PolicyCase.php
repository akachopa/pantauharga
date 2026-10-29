<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyCase extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'result_id',
        'dedupe_key',
        'status',
        'requested_by',
        'decided_by',
        'decided_at',
        'decision_note',
        'created_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function evaluationResult()
    {
        return $this->belongsTo(PolicyEvaluationResult::class, 'result_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function actionPlans()
    {
        return $this->hasMany(ActionPlan::class, 'policy_case_id');
    }
}
