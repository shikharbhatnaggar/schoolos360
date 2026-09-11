<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\BiometricLog;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\FeePayment;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SchoolManagementTest extends TestCase
{
    protected function getAdmin()
    {
        return User::where('email', 'admin@school.com')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_login_authenticates_user(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@school.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_dashboard_renders_with_kpis_and_branch(): void
    {
        $admin = $this->getAdmin();
        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Students');
        $response->assertSee('Staff & Teachers', false);
        $response->assertSee('Fee Collections');
        $response->assertSee('Delhi Public Academy');
        $response->assertSee('South Delhi Main Campus');
    }

    public function test_branch_management_and_switching(): void
    {
        $admin = $this->getAdmin();

        // 1. List branches
        $response = $this->actingAs($admin)->get('/branches');
        $response->assertStatus(200);
        $response->assertSee('South Delhi Main Campus');
        $response->assertSee('Gurugram Cyber City Campus');

        // 2. Switch branch to Gurugram
        $ggnBranch = Branch::where('code', 'DPA-GGN')->first();
        $switchResponse = $this->actingAs($admin)->post('/branches/switch', [
            'branch_id' => $ggnBranch->id,
        ]);
        $switchResponse->assertRedirect();
        $switchResponse->assertSessionHas('active_branch_id', $ggnBranch->id);
    }

    public function test_student_directory_and_indian_profiles(): void
    {
        $admin = $this->getAdmin();

        // 1. List students
        $response = $this->actingAs($admin)->get('/students');
        $response->assertStatus(200);
        $response->assertSee('Aarav Sharma');
        $response->assertSee('Ananya Patel');

        // 2. Admission form
        $response = $this->actingAs($admin)->get('/students/create');
        $response->assertStatus(200);

        // 3. Store new admission
        $class = SchoolClass::first();
        $section = $class->sections->first();
        $session = AcademicSession::current();

        $admResponse = $this->actingAs($admin)->post('/students', [
            'admission_no' => 'DPA-TEST-999',
            'roll_no' => '99',
            'first_name' => 'Dhruv',
            'last_name' => 'Kapoor',
            'gender' => 'Male',
            'dob' => '2013-05-10',
            'blood_group' => 'B+',
            'parent_name' => 'Rajesh Kapoor',
            'parent_phone' => '+91 98111 88888',
            'parent_email' => 'rajesh.kapoor@example.com',
            'address' => 'Model Town, New Delhi',
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'academic_session_id' => $session->id,
            'admission_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $admResponse->assertRedirect();
        $this->assertDatabaseHas('students', ['admission_no' => 'DPA-TEST-999']);
    }

    public function test_student_attendance_and_qr_scan(): void
    {
        $admin = $this->getAdmin();

        // 1. Student Attendance Sheet
        $response = $this->actingAs($admin)->get('/attendance/students');
        $response->assertStatus(200);
        $response->assertSee('Student Attendance');

        // 2. Student QR Scan AJAX
        $student = Student::first();
        $scanResponse = $this->actingAs($admin)->postJson('/attendance/students/scan', [
            'code' => "admission:{$student->admission_no}",
        ]);

        $scanResponse->assertStatus(200);
        $scanResponse->assertJson([
            'success' => true,
        ]);
        $this->assertDatabaseHas('student_attendances', [
            'student_id' => $student->id,
            'method' => 'qr_scan',
        ]);
    }

    public function test_staff_attendance_and_qr_scan(): void
    {
        $admin = $this->getAdmin();

        // 1. Staff Attendance Roster
        $response = $this->actingAs($admin)->get('/attendance/staff');
        $response->assertStatus(200);
        $response->assertSee('Staff Attendance');

        // 2. Staff QR Punch AJAX
        $staff = Staff::where('employee_id', 'EMP-101')->first();
        $scanResponse = $this->actingAs($admin)->postJson('/attendance/staff/scan', [
            'code' => "employee:{$staff->employee_id}",
        ]);

        $scanResponse->assertStatus(200);
        $scanResponse->assertJson([
            'success' => true,
        ]);
    }

    public function test_biometric_hub_simulator_and_sync(): void
    {
        $admin = $this->getAdmin();

        // 1. View Biometric Hub
        $response = $this->actingAs($admin)->get('/attendance/biometric');
        $response->assertStatus(200);
        $response->assertSee('Biometric Attendance Hub');
        $response->assertSee('Live Biometric Machine Simulator');

        // 2. Simulate Punch
        $punchResponse = $this->actingAs($admin)->post('/attendance/biometric/simulate', [
            'employee_id' => 'EMP-101',
            'punch_type' => 'in',
            'device_id' => 'BIO-GATE-01',
        ]);
        $punchResponse->assertRedirect();
        $this->assertDatabaseHas('biometric_logs', [
            'employee_id' => 'EMP-101',
            'punch_type' => 'in',
        ]);

        // 3. Sync logs
        $syncResponse = $this->actingAs($admin)->post('/attendance/biometric/sync');
        $syncResponse->assertRedirect();
    }

    public function test_id_card_studio_and_printable_sheet(): void
    {
        $admin = $this->getAdmin();

        // 1. ID Card Studio Index
        $response = $this->actingAs($admin)->get('/idcards?type=students');
        $response->assertStatus(200);
        $response->assertSee('ID Card Studio');
        $response->assertSee('Print ID Card Sheet');

        // 2. Staff ID Cards
        $staffResponse = $this->actingAs($admin)->get('/idcards?type=staff');
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Rajesh Sharma');

        // 3. Printable Sheet Preview
        $printResponse = $this->actingAs($admin)->get('/idcards/print?type=students');
        $printResponse->assertStatus(200);
        $printResponse->assertSee('ID Card Sheet Print Preview');
    }

    public function test_fees_and_branch_receipt_numbering(): void
    {
        $admin = $this->getAdmin();

        // 1. Fees Index
        $response = $this->actingAs($admin)->get('/fees');
        $response->assertStatus(200);
        $response->assertSee('Fee Invoices');

        // 2. Printable Receipt with Branch Prefix
        $payment = FeePayment::first();
        if ($payment) {
            $response = $this->actingAs($admin)->get('/fees/receipts/' . $payment->receipt_no);
            $response->assertStatus(200);
            $response->assertSee('Official Fee Receipt');
            $response->assertSee($payment->receipt_no);
            $this->assertStringContainsString('DPA-DEL-2026/', $payment->receipt_no);
        }
    }

    public function test_settings_school_and_branch(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('School Tenant Configuration');
        $response->assertSee('Branch Configuration');
        $response->assertSee('INR');
        $response->assertSee('₹');
    }

    public function test_import_export_hub_and_csv_generation(): void
    {
        $admin = $this->getAdmin();

        // 1. View Hub
        $response = $this->actingAs($admin)->get('/import-export');
        $response->assertStatus(200);
        $response->assertSee('Bulk Data Hub');

        // 2. Student CSV Sample download
        $sampleResponse = $this->actingAs($admin)->get('/export/students/sample');
        $sampleResponse->assertStatus(200);
        $sampleResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // 3. Staff CSV Sample download
        $staffSampleResponse = $this->actingAs($admin)->get('/export/staff/sample');
        $staffSampleResponse->assertStatus(200);
        $staffSampleResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_exam_timetable_and_printable_schedule(): void
    {
        $admin = $this->getAdmin();
        $exam = Exam::first();

        // 1. Exam index
        $response = $this->actingAs($admin)->get('/exams');
        $response->assertStatus(200);

        // 2. Exam show with timetable
        $response = $this->actingAs($admin)->get('/exams/' . $exam->id);
        $response->assertStatus(200);
        $response->assertSee('Timetable');

        // 3. Printable schedule sheet
        $response = $this->actingAs($admin)->get('/exams/' . $exam->id . '/print-schedule');
        $response->assertStatus(200);
        $response->assertSee('Examination Schedule Sheet');
    }

    public function test_user_management(): void
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('admin@school.com');
        $response->assertSee('operator@school.com');
        $response->assertSee('teacher@school.com');
    }
}
