<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyRuleCondition extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'group_id',
        'metric',
        'comparator',
        'threshold',
        'baseline',
        'lookback_days',
        'max_age_hours',
        'min_observations',
        'price_type',
        'aggregation',
    ];

    protected $casts = [
        'threshold' => 'decimal:4',
        'lookback_days' => 'integer',
        'max_age_hours' => 'integer',
        'min_observations' => 'integer',
    ];

    public function group()
    {
        return $this->belongsTo(PolicyRuleGroup::class, 'group_id');
    }
}
