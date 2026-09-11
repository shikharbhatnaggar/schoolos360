<?php

use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\AcademicSetupController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Multi-School & Branches Navigation
    Route::post('/branches/switch', [BranchController::class, 'switch'])->name('branches.switch');
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::post('/branches/{branch}/toggle', [BranchController::class, 'toggle'])->name('branches.toggle');

    // 1. Student Registrations & Admission
    Route::resource('students', StudentController::class);

    // 2. Staff and Teachers
    Route::get('/staff/assignments', [StaffController::class, 'assignments'])->name('staff.assignments');
    Route::post('/staff/assignments/class-teacher', [StaffController::class, 'assignClassTeacher'])->name('staff.assignments.class_teacher');
    Route::delete('/staff/assignments/class-teacher/{assignment}', [StaffController::class, 'removeClassTeacher'])->name('staff.assignments.remove_class_teacher');
    Route::post('/staff/assignments/subject-teacher', [StaffController::class, 'assignSubjectTeacher'])->name('staff.assignments.subject_teacher');
    Route::delete('/staff/assignments/subject-teacher/{assignment}', [StaffController::class, 'removeSubjectTeacher'])->name('staff.assignments.remove_subject_teacher');
    Route::resource('staff', StaffController::class);

    // 3. Attendance Module (Student, Staff, QR & Biometric)
    Route::get('/attendance/students', [AttendanceController::class, 'studentIndex'])->name('attendance.students.index');
    Route::post('/attendance/students', [AttendanceController::class, 'studentSave'])->name('attendance.students.save');
    Route::post('/attendance/students/qr-scan', [AttendanceController::class, 'studentQrScan'])->name('attendance.students.qr_scan');
    Route::post('/attendance/students/scan', [AttendanceController::class, 'studentQrScan'])->name('attendance.students.scan');

    Route::get('/attendance/staff', [AttendanceController::class, 'staffIndex'])->name('attendance.staff.index');
    Route::post('/attendance/staff', [AttendanceController::class, 'staffSave'])->name('attendance.staff.save');
    Route::post('/attendance/staff/qr-scan', [AttendanceController::class, 'staffQrScan'])->name('attendance.staff.qr_scan');
    Route::post('/attendance/staff/scan', [AttendanceController::class, 'staffQrScan'])->name('attendance.staff.scan');

    Route::get('/attendance/biometric', [AttendanceController::class, 'biometricSimulatorView'])->name('attendance.biometric.index');
    Route::post('/attendance/biometric/simulate-punch', [AttendanceController::class, 'biometricSimulatePunch'])->name('attendance.biometric.simulate_punch');
    Route::post('/attendance/biometric/simulate', [AttendanceController::class, 'biometricSimulatePunch'])->name('attendance.biometric.simulate');
    Route::post('/attendance/biometric/sync-logs', [AttendanceController::class, 'biometricSyncLogs'])->name('attendance.biometric.sync_logs');
    Route::post('/attendance/biometric/sync', [AttendanceController::class, 'biometricSyncLogs'])->name('attendance.biometric.sync');
    Route::post('/attendance/biometric/upload-csv', [AttendanceController::class, 'biometricUploadCsv'])->name('attendance.biometric.upload_csv');

    // 4. ID Card Studio (Student & Staff)
    Route::get('/id-cards', [IdCardController::class, 'index'])->name('idcards.index');
    Route::get('/idcards', [IdCardController::class, 'index']);
    Route::get('/id-cards/print', [IdCardController::class, 'print'])->name('idcards.print');
    Route::get('/idcards/print', [IdCardController::class, 'print']);

    // 5. Academic Sessions
    Route::get('/sessions', [AcademicSessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [AcademicSessionController::class, 'store'])->name('sessions.store');
    Route::post('/sessions/{session}/set-current', [AcademicSessionController::class, 'setCurrent'])->name('sessions.set_current');
    Route::delete('/sessions/{session}', [AcademicSessionController::class, 'destroy'])->name('sessions.destroy');

    // Academic Structure (Classes, Sections, Houses, Subjects)
    Route::get('/academics', [AcademicSetupController::class, 'index'])->name('academics.index');
    Route::post('/academics/classes', [AcademicSetupController::class, 'storeClass'])->name('academics.classes.store');
    Route::post('/academics/sections', [AcademicSetupController::class, 'storeSection'])->name('academics.sections.store');
    Route::post('/academics/houses', [AcademicSetupController::class, 'storeHouse'])->name('academics.houses.store');
    Route::post('/academics/subjects', [AcademicSetupController::class, 'storeSubject'])->name('academics.subjects.store');

    // 6. Fees Management
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/structures', [FeeController::class, 'structures'])->name('fees.structures');
    Route::post('/fees/heads', [FeeController::class, 'storeHead'])->name('fees.heads.store');
    Route::post('/fees/structures', [FeeController::class, 'saveStructure'])->name('fees.structures.save');
    Route::get('/fees/invoices/create', [FeeController::class, 'createInvoice'])->name('fees.invoices.create');
    Route::post('/fees/invoices/generate', [FeeController::class, 'generateInvoices'])->name('fees.invoices.generate');
    Route::get('/fees/invoices/{invoice}/collect', [FeeController::class, 'collectForm'])->name('fees.invoices.collect');
    Route::post('/fees/invoices/{invoice}/pay', [FeeController::class, 'recordPayment'])->name('fees.invoices.pay');
    Route::get('/fees/receipts/{receiptNo}', [FeeController::class, 'receipt'])->where('receiptNo', '.*')->name('fees.receipt');

    // 7. User Management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    // 8. Exam Creation & Schedule
    Route::resource('exams', ExamController::class);
    Route::post('/exams/{exam}/schedules', [ExamController::class, 'saveSchedule'])->name('exams.schedules.save');
    Route::delete('/exams/schedules/{schedule}', [ExamController::class, 'deleteSchedule'])->name('exams.schedules.delete');
    Route::get('/exams/{exam}/print-schedule', [ExamController::class, 'printSchedule'])->name('exams.print_schedule');
    Route::get('/exams/{exam}/enroll', [ExamController::class, 'enrollmentView'])->name('exams.enroll');
    Route::post('/exams/{exam}/enroll', [ExamController::class, 'storeEnrollment'])->name('exams.enroll.store');

    // 9. Marks Entry (Manual or via CSV)
    Route::get('/marks', [MarksController::class, 'index'])->name('marks.index');
    Route::post('/marks/manual', [MarksController::class, 'saveManual'])->name('marks.save_manual');
    Route::get('/marks/template', [MarksController::class, 'downloadTemplate'])->name('marks.template');
    Route::post('/marks/import-csv', [MarksController::class, 'importCsv'])->name('marks.import_csv');

    // 10. School & Branch Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/school', [SettingsController::class, 'updateSchool'])->name('settings.school');
    Route::post('/settings/branch', [SettingsController::class, 'updateBranch'])->name('settings.branch');

    // 11. Import & Export Features
    Route::get('/import-export', [ImportExportController::class, 'index'])->name('import_export.index');
    Route::get('/export/students', [ImportExportController::class, 'exportStudents'])->name('export.students');
    Route::get('/export/students/sample', [ImportExportController::class, 'sampleStudentsCsv'])->name('export.students.sample');
    Route::get('/sample/students', [ImportExportController::class, 'sampleStudentsCsv'])->name('sample.students');
    Route::post('/import/students', [ImportExportController::class, 'importStudents'])->name('import.students');
    Route::get('/export/staff', [ImportExportController::class, 'exportStaff'])->name('export.staff');
    Route::get('/export/staff/sample', [ImportExportController::class, 'sampleStaffCsv'])->name('export.staff.sample');
    Route::get('/sample/staff', [ImportExportController::class, 'sampleStaffCsv'])->name('sample.staff');
    Route::post('/import/staff', [ImportExportController::class, 'importStaff'])->name('import.staff');
});
