<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\House;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with('sections')->get();
        return view('import-export.index', compact('classes'));
    }

    /**
     * Export Students to CSV
     */
    public function exportStudents(Request $request): StreamedResponse
    {
        $branch = TenantContext::getBranch();
        $query = Student::with(['schoolClass', 'section', 'house', 'academicSession'])
            ->where('status', 'active');

        if ($request->class_id) {
            $query->where('school_class_id', $request->class_id);
        }

        $students = $query->orderBy('school_class_id')->orderBy('roll_no')->get();
        $filename = 'students_export_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Admission No',
                'Roll No',
                'First Name',
                'Last Name',
                'Gender',
                'DOB (YYYY-MM-DD)',
                'Blood Group',
                'Class',
                'Section',
                'House',
                'Parent Name',
                'Parent Phone',
                'Parent Email',
                'Address',
                'Status'
            ]);

            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->admission_no,
                    $student->roll_no,
                    $student->first_name,
                    $student->last_name,
                    $student->gender,
                    $student->dob ? $student->dob->format('Y-m-d') : '',
                    $student->blood_group,
                    $student->schoolClass?->name ?? '',
                    $student->section?->name ?? '',
                    $student->house?->name ?? '',
                    $student->parent_name,
                    $student->parent_phone,
                    $student->parent_email,
                    $student->address,
                    $student->status,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download Sample Student CSV
     */
    public function sampleStudentsCsv(): StreamedResponse
    {
        $filename = 'sample_students_import.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Admission No',
                'Roll No',
                'First Name',
                'Last Name',
                'Gender',
                'DOB (YYYY-MM-DD)',
                'Blood Group',
                'Class',
                'Section',
                'House',
                'Parent Name',
                'Parent Phone',
                'Parent Email',
                'Address'
            ]);

            $samples = [
                ['DPA-2026-101', '01', 'Aarav', 'Sharma', 'Male', '2012-05-14', 'O+', 'Class 8', 'A', 'Red Dragons', 'Sunil Sharma', '+91 98111 22334', 'sunil.sharma@example.com', '42, Vasant Vihar, New Delhi'],
                ['DPA-2026-102', '02', 'Ananya', 'Patel', 'Female', '2012-08-22', 'B+', 'Class 8', 'A', 'Blue Eagles', 'Rajesh Patel', '+91 98222 33445', 'rajesh.patel@example.com', '15, Golf Links, New Delhi'],
                ['DPA-2026-103', '03', 'Rohan', 'Verma', 'Male', '2012-11-03', 'A+', 'Class 8', 'B', 'Green Emeralds', 'Amit Verma', '+91 98333 44556', 'amit.verma@example.com', '78, Defence Colony, New Delhi'],
                ['DPA-2026-104', '04', 'Priya', 'Singh', 'Female', '2012-01-19', 'AB+', 'Class 8', 'B', 'Yellow Falcons', 'Devendra Singh', '+91 98444 55667', 'dev.singh@example.com', '23, Hauz Khas, New Delhi'],
            ];

            foreach ($samples as $sample) {
                fputcsv($handle, $sample);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Import Students from CSV
     */
    public function importStudents(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();
        $activeSession = AcademicSession::current();

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 5 && !empty(trim($row[0]))) {
                $admissionNo = trim($row[0]);
                $rollNo = trim($row[1] ?? '');
                $firstName = trim($row[2] ?? '');
                $lastName = trim($row[3] ?? '');
                $gender = trim($row[4] ?? 'Male');
                $dob = !empty($row[5]) ? Carbon::parse($row[5]) : null;
                $bloodGroup = trim($row[6] ?? 'O+');
                $className = trim($row[7] ?? 'Class 1');
                $sectionName = trim($row[8] ?? 'A');
                $houseName = trim($row[9] ?? '');
                $parentName = trim($row[10] ?? 'Parent');
                $parentPhone = trim($row[11] ?? '+91 98765 43210');
                $parentEmail = trim($row[12] ?? '');
                $address = trim($row[13] ?? '');

                // Find or create class
                $schoolClass = SchoolClass::firstOrCreate(
                    ['school_id' => $school->id, 'name' => $className],
                    ['branch_id' => $branch?->id, 'numeric_level' => (int)filter_var($className, FILTER_SANITIZE_NUMBER_INT) ?: 1]
                );

                // Find or create section
                $section = Section::firstOrCreate(
                    ['school_id' => $school->id, 'school_class_id' => $schoolClass->id, 'name' => $sectionName],
                    ['branch_id' => $branch?->id, 'capacity' => 40]
                );

                // Find house
                $house = null;
                if (!empty($houseName)) {
                    $house = House::where('school_id', $school->id)->where('name', $houseName)->first();
                }

                $student = Student::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'admission_no' => $admissionNo,
                    ],
                    [
                        'branch_id' => $branch?->id,
                        'roll_no' => $rollNo,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'gender' => $gender,
                        'dob' => $dob ?: now()->subYears(10),
                        'blood_group' => $bloodGroup,
                        'school_class_id' => $schoolClass->id,
                        'section_id' => $section->id,
                        'house_id' => $house?->id,
                        'academic_session_id' => $activeSession?->id,
                        'admission_date' => now(),
                        'parent_name' => $parentName,
                        'parent_phone' => $parentPhone,
                        'parent_email' => $parentEmail,
                        'address' => $address,
                        'status' => 'active',
                    ]
                );

                if ($student->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            }
        }
        fclose($handle);

        return back()->with('success', "Processed CSV: {$imported} new students created, {$updated} existing updated.");
    }

    /**
     * Export Staff to CSV
     */
    public function exportStaff(): StreamedResponse
    {
        $staffMembers = Staff::with('house')->where('status', 'active')->orderBy('first_name')->get();
        $filename = 'staff_export_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($staffMembers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Employee ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Gender',
                'Blood Group',
                'Role Type',
                'Designation',
                'Qualification',
                'House',
                'Joining Date (YYYY-MM-DD)',
                'Address',
                'Status'
            ]);

            foreach ($staffMembers as $staff) {
                fputcsv($handle, [
                    $staff->employee_id,
                    $staff->first_name,
                    $staff->last_name,
                    $staff->email,
                    $staff->phone,
                    $staff->gender,
                    $staff->blood_group,
                    $staff->role_type,
                    $staff->designation,
                    $staff->qualification,
                    $staff->house?->name ?? '',
                    $staff->joining_date ? $staff->joining_date->format('Y-m-d') : '',
                    $staff->address,
                    $staff->status,
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download Sample Staff CSV
     */
    public function sampleStaffCsv(): StreamedResponse
    {
        $filename = 'sample_staff_import.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Employee ID',
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Gender',
                'Blood Group',
                'Role Type',
                'Designation',
                'Qualification',
                'Joining Date (YYYY-MM-DD)',
                'Address'
            ]);

            $samples = [
                ['EMP-101', 'Rajesh', 'Sharma', 'dr.rajesh@school.com', '+91 98111 00111', 'Male', 'O+', 'teacher', 'Head of Science', 'Ph.D Physics, B.Ed', '2020-07-01', 'B-14, Green Park, New Delhi'],
                ['EMP-102', 'Sunita', 'Deshmukh', 'sunita.d@school.com', '+91 98222 00222', 'Female', 'B+', 'class_teacher', 'Senior Math Faculty', 'M.Sc Mathematics, B.Ed', '2021-04-15', 'Flat 402, Mayur Vihar, New Delhi'],
                ['EMP-103', 'Vikram', 'Malhotra', 'vikram.m@school.com', '+91 98333 00333', 'Male', 'A+', 'house_teacher', 'Sports Director & House Master', 'M.P.Ed Physical Education', '2019-01-10', 'Plot 55, Saket, New Delhi'],
                ['EMP-104', 'Anita', 'Rao', 'anita.rao@school.com', '+91 98444 00444', 'Female', 'AB+', 'operator', 'Accounts & Fee Officer', 'M.Com, Tally Pro', '2022-09-01', '12, Lajpat Nagar, New Delhi'],
            ];

            foreach ($samples as $sample) {
                fputcsv($handle, $sample);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Import Staff from CSV
     */
    public function importStaff(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4 && !empty(trim($row[0]))) {
                $employeeId = trim($row[0]);
                $firstName = trim($row[1] ?? '');
                $lastName = trim($row[2] ?? '');
                $email = trim($row[3] ?? '');
                $phone = trim($row[4] ?? '');
                $gender = trim($row[5] ?? 'Other');
                $bloodGroup = trim($row[6] ?? 'O+');
                $roleType = trim($row[7] ?? 'teacher');
                $designation = trim($row[8] ?? 'Faculty');
                $qualification = trim($row[9] ?? '');
                $joiningDate = !empty($row[10]) ? Carbon::parse($row[10]) : now();
                $address = trim($row[11] ?? '');

                $staff = Staff::updateOrCreate(
                    [
                        'school_id' => $school->id,
                        'employee_id' => $employeeId,
                    ],
                    [
                        'branch_id' => $branch?->id,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email ?: ($employeeId . '@school.com'),
                        'phone' => $phone,
                        'gender' => $gender,
                        'blood_group' => $bloodGroup,
                        'role_type' => $roleType,
                        'designation' => $designation,
                        'qualification' => $qualification,
                        'joining_date' => $joiningDate,
                        'address' => $address,
                        'status' => 'active',
                    ]
                );

                if ($staff->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            }
        }
        fclose($handle);

        return back()->with('success', "Processed CSV: {$imported} staff members added, {$updated} updated.");
    }
}
