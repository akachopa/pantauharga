<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyRuleGroup extends Model
{
    public $timestamps = false;

    protected $fillable = ['version_id', 'parent_group_id', 'logic', 'sort_order'];

    public function version()
    {
        return $this->belongsTo(PolicyVersion::class, 'version_id');
    }

    public function conditions()
    {
        return $this->hasMany(PolicyRuleCondition::class, 'group_id');
    }
}
