<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'series_id',
        'source_id',
        'status',
        'submitted_by',
        'verified_by',
        'verified_at',
        'previous_report_id',
        'version_no',
        'is_demo',
        'notes',
        'evidence_path',
        'created_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'is_demo' => 'boolean',
    ];

    public function series()
    {
        return $this->belongsTo(ReportSeries::class, 'series_id');
    }

    public function source()
    {
        return $this->belongsTo(DataSource::class, 'source_id');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function items()
    {
        return $this->hasMany(ReportItem::class, 'report_id');
    }

    public function previousReport()
    {
        return $this->belongsTo(Report::class, 'previous_report_id');
    }
}
