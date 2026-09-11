<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'code',
        'email',
        'phone',
        'address',
        'is_main',
        'status',
        'fee_receipt_prefix',
        'fee_receipt_next_no',
        'id_card_orientation',
        'id_card_primary_color',
        'id_card_title',
        'id_card_show_blood_group',
        'id_card_show_emergency_contact',
        'id_card_show_address',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'id_card_show_blood_group' => 'boolean',
        'id_card_show_emergency_contact' => 'boolean',
        'id_card_show_address' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function generateReceiptNumber(): string
    {
        $prefix = $this->fee_receipt_prefix ?: 'REC-';
        $number = $this->fee_receipt_next_no ?: 1001;
        $this->increment('fee_receipt_next_no');
        return $prefix . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
