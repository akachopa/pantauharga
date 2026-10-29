<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'version_no',
        'state',
        'severity',
        'valid_from',
        'valid_until',
        'cooldown_hours',
        'recommendation',
        'requires_approval',
        'published_by',
        'published_at',
        'frozen_definition',
        'is_demo',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'is_demo' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'published_at' => 'datetime',
        'frozen_definition' => 'array',
    ];

    public function package()
    {
        return $this->belongsTo(PolicyPackage::class, 'package_id');
    }

    public function ruleGroups()
    {
        return $this->hasMany(PolicyRuleGroup::class, 'version_id');
    }

    public function targets()
    {
        return $this->hasMany(PolicyTarget::class, 'version_id');
    }
}
