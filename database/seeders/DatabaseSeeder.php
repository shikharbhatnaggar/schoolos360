<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\BiometricLog;
use App\Models\Branch;
use App\Models\ClassTeacher;
use App\Models\Exam;
use App\Models\ExamEnrollment;
use App\Models\ExamSchedule;
use App\Models\FeeHead;
use App\Models\FeeInvoiceItem;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\House;
use App\Models\Mark;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeInvoice;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Schools (Multi-Tenant SaaS)
        $schoolDpa = School::create([
            'name' => 'Delhi Public Academy',
            'code' => 'DPA',
            'email' => 'contact@dpa.edu.in',
            'phone' => '+91 11 2689 4000',
            'address' => 'Sector 4, R.K. Puram, New Delhi, Delhi - 110022',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'status' => 'active',
        ]);

        $schoolXavier = School::create([
            'name' => "St. Xavier's International School",
            'code' => 'SXIS',
            'email' => 'admissions@stxaviers.org.in',
            'phone' => '+91 22 2640 1200',
            'address' => '55 Hill Road, Bandra West, Mumbai, Maharashtra - 400050',
            'currency' => 'INR',
            'currency_symbol' => '₹',
            'status' => 'active',
        ]);

        // 2. Branches for Delhi Public Academy
        $branchDel = Branch::create([
            'school_id' => $schoolDpa->id,
            'name' => 'South Delhi Main Campus',
            'code' => 'DPA-DEL',
            'email' => 'southdelhi@dpa.edu.in',
            'phone' => '+91 11 2689 4001',
            'address' => 'Sector 4, R.K. Puram, New Delhi',
            'is_main' => true,
            'status' => 'active',
            'fee_receipt_prefix' => 'DPA-DEL-2026/',
            'fee_receipt_next_no' => 1004,
            'id_card_orientation' => 'portrait',
            'id_card_primary_color' => '#0d9488',
            'id_card_title' => 'Student Identity Card',
            'id_card_show_blood_group' => true,
            'id_card_show_emergency_contact' => true,
            'id_card_show_address' => true,
        ]);

        $branchGgn = Branch::create([
            'school_id' => $schoolDpa->id,
            'name' => 'Gurugram Cyber City Campus',
            'code' => 'DPA-GGN',
            'email' => 'cybercity@dpa.edu.in',
            'phone' => '+91 124 456 7890',
            'address' => 'Phase 2, DLF Cyber City, Gurugram, Haryana - 122002',
            'is_main' => false,
            'status' => 'active',
            'fee_receipt_prefix' => 'DPA-GGN-2026/',
            'fee_receipt_next_no' => 1001,
            'id_card_orientation' => 'landscape',
            'id_card_primary_color' => '#2563eb',
            'id_card_title' => 'Campus Smart ID',
            'id_card_show_blood_group' => true,
            'id_card_show_emergency_contact' => true,
            'id_card_show_address' => false,
        ]);

        // Branches for St. Xavier's
        $branchMum = Branch::create([
            'school_id' => $schoolXavier->id,
            'name' => 'Mumbai Bandra Main Campus',
            'code' => 'SXIS-BDR',
            'email' => 'bandra@stxaviers.org.in',
            'phone' => '+91 22 2640 1201',
            'address' => '55 Hill Road, Bandra West, Mumbai',
            'is_main' => true,
            'status' => 'active',
            'fee_receipt_prefix' => 'SXIS-MUM-2026/',
            'fee_receipt_next_no' => 1001,
            'id_card_orientation' => 'portrait',
            'id_card_primary_color' => '#4f46e5',
            'id_card_title' => 'School Identity Card',
            'id_card_show_blood_group' => true,
            'id_card_show_emergency_contact' => true,
            'id_card_show_address' => true,
        ]);

        $branchPun = Branch::create([
            'school_id' => $schoolXavier->id,
            'name' => 'Pune Kalyani Nagar Campus',
            'code' => 'SXIS-PUN',
            'email' => 'pune@stxaviers.org.in',
            'phone' => '+91 20 2668 9000',
            'address' => 'East Avenue, Kalyani Nagar, Pune - 411006',
            'is_main' => false,
            'status' => 'active',
            'fee_receipt_prefix' => 'SXIS-PUN-2026/',
            'fee_receipt_next_no' => 1001,
            'id_card_orientation' => 'portrait',
            'id_card_primary_color' => '#dc2626',
            'id_card_title' => 'School Identity Card',
            'id_card_show_blood_group' => true,
            'id_card_show_emergency_contact' => true,
            'id_card_show_address' => true,
        ]);

        // 3. User Accounts
        $admin = User::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Principal Harrison (Super Admin)',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $operator = User::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Anita Rao (Accounts Officer)',
            'email' => 'operator@school.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        $teacherUser = User::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Dr. Rajesh Sharma (Senior Faculty)',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $xavierAdmin = User::create([
            'school_id' => $schoolXavier->id,
            'branch_id' => $branchMum->id,
            'name' => "Fr. Anthony D'Souza (Principal)",
            'email' => 'admin@stxaviers.org.in',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // 4. Academic Sessions
        $sessionPast = AcademicSession::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date' => '2026-03-31',
            'is_current' => false,
        ]);

        $sessionCurrent = AcademicSession::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => true,
        ]);

        // 5. Houses
        $houseRed = House::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Red Dragons',
            'color' => '#EF4444',
            'description' => 'Valor, courage, and leadership',
        ]);
        $houseBlue = House::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Blue Eagles',
            'color' => '#3B82F6',
            'description' => 'Wisdom, discipline, and intellectual pursuit',
        ]);
        $houseGreen = House::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Green Emeralds',
            'color' => '#10B981',
            'description' => 'Harmony, growth, and sportsmanship',
        ]);
        $houseYellow = House::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'name' => 'Yellow Falcons',
            'color' => '#F59E0B',
            'description' => 'Speed, agility, and dynamic creativity',
        ]);

        // 6. Classes & Sections for Delhi Branch
        $classes = [];
        $sections = [];
        for ($i = 1; $i <= 10; $i++) {
            $cls = SchoolClass::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'name' => "Class {$i}",
                'numeric_level' => $i,
            ]);
            $classes[$i] = $cls;

            $secA = Section::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'school_class_id' => $cls->id,
                'name' => 'A',
                'capacity' => 40,
            ]);
            $secB = Section::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'school_class_id' => $cls->id,
                'name' => 'B',
                'capacity' => 40,
            ]);

            $sections[$i] = ['A' => $secA, 'B' => $secB];
        }

        // 7. Subjects
        $subjMath = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Mathematics', 'code' => 'MATH-101', 'type' => 'core']);
        $subjEng = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'English Literature', 'code' => 'ENG-101', 'type' => 'core']);
        $subjSci = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'General Science', 'code' => 'SCI-101', 'type' => 'core']);
        $subjSoc = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Social Science', 'code' => 'SOC-101', 'type' => 'core']);
        $subjHin = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Hindi Core', 'code' => 'HIN-101', 'type' => 'core']);
        $subjCS = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Computer Science & AI', 'code' => 'CS-101', 'type' => 'elective']);
        $subjPE = Subject::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Physical Education', 'code' => 'PE-101', 'type' => 'activity']);

        // 8. Staff (Authentic Indian profiles)
        $staff1 = Staff::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'user_id' => $teacherUser->id,
            'employee_id' => 'EMP-101',
            'first_name' => 'Rajesh',
            'last_name' => 'Sharma',
            'email' => 'teacher@school.com',
            'phone' => '+91 98111 00111',
            'gender' => 'Male',
            'blood_group' => 'O+',
            'role_type' => 'house_teacher',
            'designation' => 'Head of Science & Red Dragons House Master',
            'qualification' => 'Ph.D (Physics), B.Ed',
            'house_id' => $houseRed->id,
            'joining_date' => '2020-07-01',
            'status' => 'active',
            'address' => 'B-14, Green Park Extension, New Delhi - 110016',
        ]);
        $teacherUser->update(['staff_id' => $staff1->id]);

        $staff2 = Staff::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'user_id' => null,
            'employee_id' => 'EMP-102',
            'first_name' => 'Sunita',
            'last_name' => 'Deshmukh',
            'email' => 'sunita.d@dpa.edu.in',
            'phone' => '+91 98222 00222',
            'gender' => 'Female',
            'blood_group' => 'B+',
            'role_type' => 'class_teacher',
            'designation' => 'Senior Math Faculty & Class Teacher',
            'qualification' => 'M.Sc (Mathematics), B.Ed',
            'house_id' => $houseBlue->id,
            'joining_date' => '2021-04-15',
            'status' => 'active',
            'address' => 'Flat 402, Mayur Vihar Phase 1, New Delhi - 110091',
        ]);

        $staff3 = Staff::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'user_id' => $operator->id,
            'employee_id' => 'EMP-103',
            'first_name' => 'Anita',
            'last_name' => 'Rao',
            'email' => 'operator@school.com',
            'phone' => '+91 98444 00444',
            'gender' => 'Female',
            'blood_group' => 'AB+',
            'role_type' => 'operator',
            'designation' => 'Bursar & Accounts Officer',
            'qualification' => 'M.Com, Tally ERP Certification',
            'house_id' => null,
            'joining_date' => '2019-02-10',
            'status' => 'active',
            'address' => '12, Lajpat Nagar III, New Delhi - 110024',
        ]);
        $operator->update(['staff_id' => $staff3->id]);

        $staff4 = Staff::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'user_id' => null,
            'employee_id' => 'EMP-104',
            'first_name' => 'Vikram',
            'last_name' => 'Malhotra',
            'email' => 'vikram.m@dpa.edu.in',
            'phone' => '+91 98333 00333',
            'gender' => 'Male',
            'blood_group' => 'A+',
            'role_type' => 'house_teacher',
            'designation' => 'Sports Director & Green Emeralds House Master',
            'qualification' => 'M.P.Ed (Physical Education), NIS Diploma',
            'house_id' => $houseGreen->id,
            'joining_date' => '2018-09-01',
            'status' => 'active',
            'address' => 'Plot 55, Saket, New Delhi - 110017',
        ]);

        $staff5 = Staff::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'user_id' => null,
            'employee_id' => 'EMP-105',
            'first_name' => 'Michael',
            'last_name' => 'Chang',
            'email' => 'michael.c@dpa.edu.in',
            'phone' => '+91 98555 00555',
            'gender' => 'Male',
            'blood_group' => 'O-',
            'role_type' => 'subject_teacher',
            'designation' => 'Computer Science & AI Instructor',
            'qualification' => 'B.Tech (CSE), M.Tech',
            'house_id' => $houseYellow->id,
            'joining_date' => '2023-01-15',
            'status' => 'active',
            'address' => 'A-22, Hauz Khas Enclave, New Delhi - 110016',
        ]);

        // 9. Teacher Assignments (Class 8 & Class 7)
        ClassTeacher::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'staff_id' => $staff2->id,
            'school_class_id' => $classes[8]->id,
            'section_id' => $sections[8]['A']->id,
            'academic_session_id' => $sessionCurrent->id,
        ]);

        SubjectTeacher::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'staff_id' => $staff2->id,
            'school_class_id' => $classes[8]->id,
            'section_id' => $sections[8]['A']->id,
            'subject_id' => $subjMath->id,
            'academic_session_id' => $sessionCurrent->id,
        ]);

        SubjectTeacher::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'staff_id' => $staff1->id,
            'school_class_id' => $classes[8]->id,
            'section_id' => $sections[8]['A']->id,
            'subject_id' => $subjSci->id,
            'academic_session_id' => $sessionCurrent->id,
        ]);

        SubjectTeacher::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'staff_id' => $staff5->id,
            'school_class_id' => $classes[8]->id,
            'section_id' => $sections[8]['A']->id,
            'subject_id' => $subjCS->id,
            'academic_session_id' => $sessionCurrent->id,
        ]);

        // 10. Students (Authentic Indian Names, Class 8 & 7)
        $studentsData = [
            // Class 8 Section A
            ['fn' => 'Aarav', 'ln' => 'Sharma', 'gender' => 'Male', 'class' => 8, 'sec' => 'A', 'house' => $houseRed->id, 'roll' => '01', 'dob' => '2012-05-14', 'blood' => 'O+', 'pname' => 'Sunil Sharma', 'pphone' => '+91 98111 22334', 'addr' => '42, Vasant Vihar, New Delhi'],
            ['fn' => 'Ananya', 'ln' => 'Patel', 'gender' => 'Female', 'class' => 8, 'sec' => 'A', 'house' => $houseBlue->id, 'roll' => '02', 'dob' => '2012-08-22', 'blood' => 'B+', 'pname' => 'Rajesh Patel', 'pphone' => '+91 98222 33445', 'addr' => '15, Golf Links, New Delhi'],
            ['fn' => 'Rohan', 'ln' => 'Verma', 'gender' => 'Male', 'class' => 8, 'sec' => 'A', 'house' => $houseGreen->id, 'roll' => '03', 'dob' => '2012-11-03', 'blood' => 'A+', 'pname' => 'Amit Verma', 'pphone' => '+91 98333 44556', 'addr' => '78, Defence Colony, New Delhi'],
            ['fn' => 'Priya', 'ln' => 'Singh', 'gender' => 'Female', 'class' => 8, 'sec' => 'A', 'house' => $houseYellow->id, 'roll' => '04', 'dob' => '2012-01-19', 'blood' => 'AB+', 'pname' => 'Devendra Singh', 'pphone' => '+91 98444 55667', 'addr' => '23, Hauz Khas, New Delhi'],
            ['fn' => 'Ishaan', 'ln' => 'Gupta', 'gender' => 'Male', 'class' => 8, 'sec' => 'A', 'house' => $houseRed->id, 'roll' => '05', 'dob' => '2012-09-30', 'blood' => 'O-', 'pname' => 'Manoj Gupta', 'pphone' => '+91 98555 66778', 'addr' => '104, Greater Kailash 1, New Delhi'],
            ['fn' => 'Diya', 'ln' => 'Iyer', 'gender' => 'Female', 'class' => 8, 'sec' => 'A', 'house' => $houseBlue->id, 'roll' => '06', 'dob' => '2012-03-25', 'blood' => 'B+', 'pname' => 'S. Ramaswamy Iyer', 'pphone' => '+91 98666 77889', 'addr' => '56, Shanti Niketan, New Delhi'],

            // Class 8 Section B
            ['fn' => 'Kabir', 'ln' => 'Mehta', 'gender' => 'Male', 'class' => 8, 'sec' => 'B', 'house' => $houseGreen->id, 'roll' => '07', 'dob' => '2012-07-11', 'blood' => 'A-', 'pname' => 'Vikram Mehta', 'pphone' => '+91 98777 88990', 'addr' => '31, Panchsheel Park, New Delhi'],
            ['fn' => 'Tanvi', 'ln' => 'Joshi', 'gender' => 'Female', 'class' => 8, 'sec' => 'B', 'house' => $houseYellow->id, 'roll' => '08', 'dob' => '2012-04-18', 'blood' => 'O+', 'pname' => 'Nitin Joshi', 'pphone' => '+91 98888 99001', 'addr' => '89, Safdarjung Enclave, New Delhi'],

            // Class 7 Section A
            ['fn' => 'Aditya', 'ln' => 'Sen', 'gender' => 'Male', 'class' => 7, 'sec' => 'A', 'house' => $houseRed->id, 'roll' => '01', 'dob' => '2013-06-15', 'blood' => 'AB+', 'pname' => 'Debabrata Sen', 'pphone' => '+91 98999 00112', 'addr' => '12, Chittaranjan Park, New Delhi'],
            ['fn' => 'Meera', 'ln' => 'Kulkarni', 'gender' => 'Female', 'class' => 7, 'sec' => 'A', 'house' => $houseBlue->id, 'roll' => '02', 'dob' => '2013-10-04', 'blood' => 'B-', 'pname' => 'Sanjay Kulkarni', 'pphone' => '+91 98101 11223', 'addr' => '67, Anand Lok, New Delhi'],
            ['fn' => 'Vivaan', 'ln' => 'Reddy', 'gender' => 'Male', 'class' => 7, 'sec' => 'A', 'house' => $houseGreen->id, 'roll' => '03', 'dob' => '2013-02-28', 'blood' => 'A+', 'pname' => 'K. Venkat Reddy', 'pphone' => '+91 98212 22334', 'addr' => '44, Jor Bagh, New Delhi'],
            ['fn' => 'Saanvi', 'ln' => 'Nair', 'gender' => 'Female', 'class' => 7, 'sec' => 'A', 'house' => $houseYellow->id, 'roll' => '04', 'dob' => '2013-12-09', 'blood' => 'O+', 'pname' => 'Govind Nair', 'pphone' => '+91 98323 33445', 'addr' => '90, Sundar Nagar, New Delhi'],
        ];

        $createdStudents = [];
        $admCounter = 101;
        foreach ($studentsData as $st) {
            $admNo = 'DPA-2026-' . $admCounter++;
            $student = Student::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'admission_no' => $admNo,
                'roll_no' => $st['roll'],
                'first_name' => $st['fn'],
                'last_name' => $st['ln'],
                'gender' => $st['gender'],
                'dob' => $st['dob'],
                'blood_group' => $st['blood'],
                'parent_name' => $st['pname'],
                'parent_phone' => $st['pphone'],
                'parent_email' => strtolower($st['fn']) . '.parent@example.com',
                'address' => $st['addr'],
                'school_class_id' => $classes[$st['class']]->id,
                'section_id' => $sections[$st['class']][$st['sec']]->id,
                'house_id' => $st['house'],
                'academic_session_id' => $sessionCurrent->id,
                'admission_date' => '2026-04-05',
                'status' => 'active',
            ]);
            $createdStudents[] = $student;
        }

        // 11. Fee Heads (INR)
        $headTuition = FeeHead::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Tuition Fee', 'description' => 'Regular academic instructional fee']);
        $headExam = FeeHead::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Examination & Assessment Fee', 'description' => 'Term evaluations, question papers & report cards']);
        $headLab = FeeHead::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Science & AI Computer Lab Fee', 'description' => 'Laboratory consumables and high-speed fiber internet']);
        $headSports = FeeHead::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Sports & Physical Activity Fee', 'description' => 'Athletics, swimming pool and fitness facilities']);
        $headLibrary = FeeHead::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'name' => 'Library & Digital Resources', 'description' => 'E-journals, books and library subscription']);

        // 12. Fee Structures for Classes 1 to 10
        foreach ($classes as $num => $cls) {
            FeeStructure::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'academic_session_id' => $sessionCurrent->id, 'school_class_id' => $cls->id, 'fee_head_id' => $headTuition->id, 'amount' => 4500.00 + ($num * 250), 'frequency' => 'term']);
            FeeStructure::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'academic_session_id' => $sessionCurrent->id, 'school_class_id' => $cls->id, 'fee_head_id' => $headExam->id, 'amount' => 850.00, 'frequency' => 'term']);
            FeeStructure::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'academic_session_id' => $sessionCurrent->id, 'school_class_id' => $cls->id, 'fee_head_id' => $headLab->id, 'amount' => 600.00, 'frequency' => 'term']);
            FeeStructure::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'academic_session_id' => $sessionCurrent->id, 'school_class_id' => $cls->id, 'fee_head_id' => $headSports->id, 'amount' => 500.00, 'frequency' => 'term']);
            FeeStructure::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'academic_session_id' => $sessionCurrent->id, 'school_class_id' => $cls->id, 'fee_head_id' => $headLibrary->id, 'amount' => 450.00, 'frequency' => 'term']);
        }

        // 13. Fee Invoices & Payments with Branch-Specific Receipt Prefix
        $invCounter = 1;
        $recCounter = 1001;
        foreach ($createdStudents as $st) {
            if ($st->school_class_id === $classes[8]->id) {
                $invNo = 'INV-2026-' . str_pad($invCounter++, 4, '0', STR_PAD_LEFT);
                $totalAmt = 8900.00; // 6500 tuition + 850 exam + 600 lab + 500 sports + 450 library

                $invoice = StudentFeeInvoice::create([
                    'school_id' => $schoolDpa->id,
                    'branch_id' => $branchDel->id,
                    'invoice_no' => $invNo,
                    'student_id' => $st->id,
                    'academic_session_id' => $sessionCurrent->id,
                    'school_class_id' => $classes[8]->id,
                    'title' => 'Term 1 Composite Academic Fee (Apr - Sep 2026)',
                    'due_date' => '2026-05-15',
                    'total_amount' => $totalAmt,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);

                FeeInvoiceItem::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'student_fee_invoice_id' => $invoice->id, 'fee_head_id' => $headTuition->id, 'amount' => 6500.00]);
                FeeInvoiceItem::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'student_fee_invoice_id' => $invoice->id, 'fee_head_id' => $headExam->id, 'amount' => 850.00]);
                FeeInvoiceItem::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'student_fee_invoice_id' => $invoice->id, 'fee_head_id' => $headLab->id, 'amount' => 600.00]);
                FeeInvoiceItem::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'student_fee_invoice_id' => $invoice->id, 'fee_head_id' => $headSports->id, 'amount' => 500.00]);
                FeeInvoiceItem::create(['school_id' => $schoolDpa->id, 'branch_id' => $branchDel->id, 'student_fee_invoice_id' => $invoice->id, 'fee_head_id' => $headLibrary->id, 'amount' => 450.00]);

                // Full payment for Aarav Sharma & Ananya Patel
                if ($st->first_name === 'Aarav' || $st->first_name === 'Ananya') {
                    $recNo = $branchDel->fee_receipt_prefix . str_pad($recCounter++, 5, '0', STR_PAD_LEFT);
                    FeePayment::create([
                        'school_id' => $schoolDpa->id,
                        'branch_id' => $branchDel->id,
                        'receipt_no' => $recNo,
                        'student_fee_invoice_id' => $invoice->id,
                        'student_id' => $st->id,
                        'amount_paid' => $totalAmt,
                        'payment_method' => 'online',
                        'transaction_reference' => 'UPI-HDFC-' . strtoupper(uniqid()),
                        'payment_date' => '2026-04-10',
                        'notes' => 'Paid via UPI Instant Transfer',
                        'received_by_id' => $operator->id,
                    ]);
                    $invoice->update(['paid_amount' => $totalAmt, 'status' => 'paid']);
                } elseif ($st->first_name === 'Rohan') {
                    // Partial payment of ₹ 5,000
                    $recNo = $branchDel->fee_receipt_prefix . str_pad($recCounter++, 5, '0', STR_PAD_LEFT);
                    FeePayment::create([
                        'school_id' => $schoolDpa->id,
                        'branch_id' => $branchDel->id,
                        'receipt_no' => $recNo,
                        'student_fee_invoice_id' => $invoice->id,
                        'student_id' => $st->id,
                        'amount_paid' => 5000.00,
                        'payment_method' => 'cash',
                        'transaction_reference' => 'CHQ-889021',
                        'payment_date' => '2026-04-12',
                        'notes' => 'Part-payment at Accounts counter',
                        'received_by_id' => $operator->id,
                    ]);
                    $invoice->update(['paid_amount' => 5000.00, 'status' => 'partially_paid']);
                }
            }
        }

        // 14. Examination & Schedules
        $examMidTerm = Exam::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'academic_session_id' => $sessionCurrent->id,
            'name' => 'Term 1 Mid-Term Examination 2026',
            'term' => 'Mid-Term',
            'start_date' => '2026-09-15',
            'end_date' => '2026-09-28',
            'description' => 'Comprehensive mid-term evaluation covering Term 1 syllabus.',
            'status' => 'scheduled',
        ]);

        $examFinal = Exam::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'academic_session_id' => $sessionCurrent->id,
            'name' => 'Annual Board & Promotion Examination 2027',
            'term' => 'Final',
            'start_date' => '2027-02-15',
            'end_date' => '2027-03-05',
            'description' => 'Annual comprehensive promotion examination.',
            'status' => 'scheduled',
        ]);

        // Schedules for Class 8
        $schedMath = ExamSchedule::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'exam_id' => $examMidTerm->id,
            'school_class_id' => $classes[8]->id,
            'subject_id' => $subjMath->id,
            'exam_date' => '2026-09-15',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'room_no' => 'Examination Hall A',
            'max_marks' => 100.00,
            'pass_marks' => 40.00,
        ]);

        $schedSci = ExamSchedule::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'exam_id' => $examMidTerm->id,
            'school_class_id' => $classes[8]->id,
            'subject_id' => $subjSci->id,
            'exam_date' => '2026-09-18',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'room_no' => 'Examination Hall A',
            'max_marks' => 100.00,
            'pass_marks' => 40.00,
        ]);

        $schedEng = ExamSchedule::create([
            'school_id' => $schoolDpa->id,
            'branch_id' => $branchDel->id,
            'exam_id' => $examMidTerm->id,
            'school_class_id' => $classes[8]->id,
            'subject_id' => $subjEng->id,
            'exam_date' => '2026-09-22',
            'start_time' => '09:00',
            'end_time' => '12:00',
            'room_no' => 'Room 204',
            'max_marks' => 100.00,
            'pass_marks' => 40.00,
        ]);

        // Enroll Class 8 students and enter Marks
        $sampleScores = [
            'Aarav'  => ['math' => 95.0, 'sci' => 92.5, 'eng' => 88.0],
            'Ananya' => ['math' => 98.0, 'sci' => 96.0, 'eng' => 94.0],
            'Rohan'  => ['math' => 78.0, 'sci' => 72.5, 'eng' => 70.0],
            'Priya'  => ['math' => 88.5, 'sci' => 85.0, 'eng' => 91.0],
            'Ishaan' => ['math' => 38.0, 'sci' => 52.0, 'eng' => 60.0], // math fail (<40)
            'Diya'   => ['math' => null, 'sci' => 84.0, 'eng' => 89.0, 'math_absent' => true], // absent in math
        ];

        foreach ($createdStudents as $st) {
            if ($st->school_class_id === $classes[8]->id) {
                ExamEnrollment::create([
                    'school_id' => $schoolDpa->id,
                    'branch_id' => $branchDel->id,
                    'exam_id' => $examMidTerm->id,
                    'student_id' => $st->id,
                    'exam_roll_no' => $st->roll_no ?: $st->admission_no,
                ]);

                if (isset($sampleScores[$st->first_name])) {
                    $score = $sampleScores[$st->first_name];

                    Mark::create([
                        'school_id' => $schoolDpa->id,
                        'branch_id' => $branchDel->id,
                        'exam_schedule_id' => $schedMath->id,
                        'student_id' => $st->id,
                        'marks_obtained' => $score['math'],
                        'is_absent' => !empty($score['math_absent']),
                        'remarks' => !empty($score['math_absent']) ? 'Medical Leave' : ($score['math'] >= 90 ? 'Outstanding performance' : 'Satisfactory'),
                    ]);

                    Mark::create([
                        'school_id' => $schoolDpa->id,
                        'branch_id' => $branchDel->id,
                        'exam_schedule_id' => $schedSci->id,
                        'student_id' => $st->id,
                        'marks_obtained' => $score['sci'],
                        'is_absent' => false,
                        'remarks' => 'Good conceptual understanding',
                    ]);

                    Mark::create([
                        'school_id' => $schoolDpa->id,
                        'branch_id' => $branchDel->id,
                        'exam_schedule_id' => $schedEng->id,
                        'student_id' => $st->id,
                        'marks_obtained' => $score['eng'],
                        'is_absent' => false,
                        'remarks' => 'Excellent vocabulary & grammar',
                    ]);
                }
            }
        }

        // 15. Attendance Records (Students & Staff for Today & Yesterday)
        $today = date('Y-m-d');
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        foreach ($createdStudents as $idx => $st) {
            $status = ($idx === 4) ? 'absent' : (($idx === 5) ? 'late' : 'present');

            // Today's student attendance
            StudentAttendance::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'student_id' => $st->id,
                'school_class_id' => $st->school_class_id,
                'section_id' => $st->section_id,
                'date' => $today,
                'status' => $status,
                'method' => ($idx % 2 === 0) ? 'qr_scan' : 'manual',
                'remarks' => ($status === 'late') ? 'Arrived at 08:35 AM' : null,
                'recorded_by_id' => $admin->id,
            ]);

            // Yesterday's student attendance
            StudentAttendance::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'student_id' => $st->id,
                'school_class_id' => $st->school_class_id,
                'section_id' => $st->section_id,
                'date' => $yesterday,
                'status' => 'present',
                'method' => 'manual',
                'recorded_by_id' => $admin->id,
            ]);
        }

        // Staff attendance today
        $staffMembersList = [$staff1, $staff2, $staff3, $staff4, $staff5];
        foreach ($staffMembersList as $sIdx => $stf) {
            StaffAttendance::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'staff_id' => $stf->id,
                'date' => $today,
                'status' => 'present',
                'punch_in_time' => sprintf('08:%02d:15', 15 + ($sIdx * 4)),
                'punch_out_time' => ($sIdx < 3) ? '16:30:00' : null,
                'method' => ($sIdx === 0) ? 'qr_scan' : 'biometric',
                'remarks' => 'Regular punch verified',
                'recorded_by_id' => $admin->id,
            ]);
        }

        // 16. Biometric Machine Punch Logs
        $logs = [
            ['emp' => 'EMP-101', 'time' => '08:15:20', 'type' => 'in', 'device' => 'BIO-GATE-01'],
            ['emp' => 'EMP-102', 'time' => '08:19:45', 'type' => 'in', 'device' => 'BIO-GATE-01'],
            ['emp' => 'EMP-103', 'time' => '08:23:10', 'type' => 'in', 'device' => 'BIO-GATE-01'],
            ['emp' => 'EMP-104', 'time' => '08:27:05', 'type' => 'in', 'device' => 'BIO-STAFF-02'],
            ['emp' => 'EMP-105', 'time' => '08:31:50', 'type' => 'in', 'device' => 'BIO-STAFF-02'],
            ['emp' => 'EMP-101', 'time' => '16:30:15', 'type' => 'out', 'device' => 'BIO-GATE-01'],
            ['emp' => 'EMP-102', 'time' => '16:32:40', 'type' => 'out', 'device' => 'BIO-GATE-01'],
        ];

        foreach ($logs as $l) {
            BiometricLog::create([
                'school_id' => $schoolDpa->id,
                'branch_id' => $branchDel->id,
                'device_id' => $l['device'],
                'employee_id' => $l['emp'],
                'punch_timestamp' => Carbon::parse("{$today} {$l['time']}"),
                'punch_type' => $l['type'],
                'processed' => true,
            ]);
        }
    }
}
