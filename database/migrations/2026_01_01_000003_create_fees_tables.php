<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fee Heads
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Fee Structures
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('fee_head_id')->constrained('fee_heads')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('frequency')->default('monthly'); // monthly, term, annual, one_time
            $table->timestamps();
        });

        // 3. Student Fee Invoices
        Schema::create('student_fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('invoice_no');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->string('title');
            $table->date('due_date');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid
            $table->timestamps();
            $table->unique(['school_id', 'invoice_no']);
        });

        // 4. Fee Invoice Items
        Schema::create('fee_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('student_fee_invoice_id')->constrained('student_fee_invoices')->onDelete('cascade');
            $table->foreignId('fee_head_id')->constrained('fee_heads')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        // 5. Fee Payments / Receipts
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('receipt_no');
            $table->foreignId('student_fee_invoice_id')->constrained('student_fee_invoices')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, online, cheque, bank_transfer
            $table->string('transaction_reference')->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->foreignId('received_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['school_id', 'receipt_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_invoice_items');
        Schema::dropIfExists('student_fee_invoices');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_heads');
    }
};
