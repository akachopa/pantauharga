<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    public $timestamps = false;
    protected $fillable = ['code', 'name', 'category', 'base_unit', 'icon', 'heir_category', 'reference_price', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'reference_price' => 'decimal:2',
    ];

    public function reportItems()
    {
        return $this->hasMany(ReportItem::class, 'commodity_id');
    }
}
