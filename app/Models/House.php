<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'name',
        'color',
        'description',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function houseMasters()
    {
        return $this->hasMany(Staff::class, 'house_id');
    }
}
