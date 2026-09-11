<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'currency',
        'currency_symbol',
        'status',
        'subscription_plan',
        'logo_url',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function mainBranch()
    {
        return $this->hasOne(Branch::class)->where('is_main', true);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function academicSessions()
    {
        return $this->hasMany(AcademicSession::class);
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function houses()
    {
        return $this->hasMany(House::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function feeHeads()
    {
        return $this->hasMany(FeeHead::class);
    }

    public function studentFeeInvoices()
    {
        return $this->hasMany(StudentFeeInvoice::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
