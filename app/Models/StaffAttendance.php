<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'branch_id',
        'staff_id',
        'date',
        'status',
        'method',
        'punch_in_time',
        'punch_out_time',
        'remarks',
        'recorded_by_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
