<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'exam_schedule_id',
        'student_id',
        'marks_obtained',
        'is_absent',
        'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'is_absent' => 'boolean',
    ];

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

