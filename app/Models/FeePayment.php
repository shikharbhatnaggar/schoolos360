<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'receipt_no',
        'student_fee_invoice_id',
        'student_id',
        'amount_paid',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'notes',
        'received_by_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(StudentFeeInvoice::class, 'student_fee_invoice_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }
}

