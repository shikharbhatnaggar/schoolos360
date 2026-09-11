<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Staff
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('gender')->default('Other');
            $table->string('blood_group')->nullable();
            $table->string('role_type')->default('teacher'); // operator, teacher, house_teacher, class_teacher, subject_teacher
            $table->string('designation'); // Principal, Senior Teacher, House Master, Lab Assistant, Clerk
            $table->string('qualification')->nullable();
            $table->foreignId('house_id')->nullable()->constrained('houses')->nullOnDelete(); // For House Teacher / House Master
            $table->date('joining_date');
            $table->string('status')->default('active'); // active, inactive
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
            $table->unique(['school_id', 'employee_id']);
        });

        // 2. Class Teachers (session-bound assignment)
        Schema::create('class_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['school_class_id', 'section_id', 'academic_session_id'], 'unique_class_section_session_teacher');
        });

        // 3. Subject Teachers (teaching specific subject to a class/section in a session)
        Schema::create('subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('admission_no');
            $table->string('roll_no')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender')->default('Male');
            $table->date('dob');
            $table->string('blood_group')->nullable();
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->string('parent_email')->nullable();
            $table->text('address')->nullable();
            $table->string('photo_url')->nullable();
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('restrict');
            $table->foreignId('section_id')->constrained('sections')->onDelete('restrict');
            $table->foreignId('house_id')->nullable()->constrained('houses')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->onDelete('restrict');
            $table->date('admission_date');
            $table->string('status')->default('active'); // active, alumni, transferred, suspended
            $table->timestamps();
            $table->unique(['school_id', 'admission_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('subject_teachers');
        Schema::dropIfExists('class_teachers');
        Schema::dropIfExists('staff');
    }
};
