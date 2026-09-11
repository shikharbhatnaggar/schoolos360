<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use BelongsToSchool;
    protected $fillable = [
        'school_id',
        'branch_id',
        'student_id',
        'school_class_id',
        'section_id',
        'academic_session_id',
        'date',
        'status',
        'method',
        'remarks',
        'recorded_by_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_id');
    }
}
