<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public $timestamps = false;
    protected $fillable = ['region_id', 'code', 'name', 'address'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'organization_id');
    }
}
