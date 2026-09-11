<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class BiometricLog extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'branch_id',
        'device_id',
        'employee_id',
        'punch_timestamp',
        'punch_type',
        'processed',
    ];

    protected $casts = [
        'punch_timestamp' => 'datetime',
        'processed' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'employee_id', 'employee_id');
    }
}
