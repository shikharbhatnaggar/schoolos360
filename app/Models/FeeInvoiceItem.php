<?php

namespace App\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInvoiceItem extends Model
{
    use HasFactory, BelongsToSchool;

    protected $fillable = [
        'school_id',
        'branch_id',
        'student_fee_invoice_id',
        'fee_head_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(StudentFeeInvoice::class, 'student_fee_invoice_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }
}

