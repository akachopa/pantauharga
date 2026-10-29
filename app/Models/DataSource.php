<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSource extends Model
{
    public $timestamps = false;
    protected $fillable = ['code', 'name', 'source_type'];
}
