<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'admission_no',
        'roll_no',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'blood_group',
        'parent_name',
        'parent_phone',
        'parent_email',
        'address',
        'photo_url',
        'school_class_id',
        'section_id',
        'house_id',
        'academic_session_id',
        'admission_date',
        'status',
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }
        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?name={$name}&background=0d9488&color=fff&size=128&bold=true";
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function feeInvoices()
    {
        return $this->hasMany(StudentFeeInvoice::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function examEnrollments()
    {
        return $this->hasMany(ExamEnrollment::class);
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
}

