<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'school_class_id',
        'name',
        'capacity',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classTeachers()
    {
        return $this->hasMany(ClassTeacher::class);
    }
}
