<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyPackage extends Model
{
    public $timestamps = false;

    protected $fillable = ['organization_id', 'code', 'name', 'description', 'created_by', 'created_at'];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(PolicyVersion::class, 'package_id');
    }

    public function latestVersion()
    {
        return $this->hasOne(PolicyVersion::class, 'package_id')->latestOfMany('version_no');
    }
}
