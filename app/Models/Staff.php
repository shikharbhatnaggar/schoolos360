<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'staff';

    protected $fillable = [
        'school_id',
        'branch_id',
        'user_id',
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'blood_group',
        'role_type',
        'designation',
        'qualification',
        'house_id',
        'joining_date',
        'status',
        'address',
        'photo_url',
    ];

    protected $casts = [
        'joining_date' => 'date',
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
        return "https://ui-avatars.com/api/?name={$name}&background=0f766e&color=fff&size=128&bold=true";
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class);
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SubjectTeacher::class);
    }
}
