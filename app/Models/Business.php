<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    public $timestamps = false;

    protected $fillable = ['region_id', 'code', 'name', 'owner_name', 'business_type', 'phone', 'address', 'is_verified'];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
