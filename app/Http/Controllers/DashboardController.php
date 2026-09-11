<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\FeePayment;
use App\Models\House;
use App\Models\SchoolClass;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFeeInvoice;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentSession = AcademicSession::current();
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();
        $today = date('Y-m-d');

        // 1. Students Count
        $totalStudents = Student::where('status', 'active')->count();
        $allStudentsCount = Student::count();

        // 2. Staff Count
        $totalStaff = Staff::where('status', 'active')->count();
        $totalTeachers = Staff::where('status', 'active')->whereIn('role_type', ['teacher', 'class_teacher', 'subject_teacher', 'house_teacher'])->count();
        $totalOperators = Staff::where('status', 'active')->where('role_type', 'operator')->count();

        // 3. Today's Student Attendance
        $todayStudentTotal = StudentAttendance::whereDate('date', $today)->count();
        $todayStudentPresent = StudentAttendance::whereDate('date', $today)->where('status', 'present')->count();
        $todayStudentAbsent = StudentAttendance::whereDate('date', $today)->where('status', 'absent')->count();
        $studentAttendanceRate = $totalStudents > 0 && $todayStudentTotal > 0 
            ? round(($todayStudentPresent / $todayStudentTotal) * 100, 1) 
            : 0;

        // 4. Today's Staff Attendance
        $todayStaffTotal = StaffAttendance::whereDate('date', $today)->count();
        $todayStaffPresent = StaffAttendance::whereDate('date', $today)->where('status', 'present')->count();
        $staffAttendanceRate = $totalStaff > 0 && $todayStaffTotal > 0 
            ? round(($todayStaffPresent / $todayStaffTotal) * 100, 1) 
            : 0;

        // 5. Fees & Financials
        $totalInvoiced = StudentFeeInvoice::sum('total_amount');
        $totalCollected = StudentFeeInvoice::sum('paid_amount');
        $totalPending = max(0, $totalInvoiced - $totalCollected);
        $unpaidInvoicesCount = StudentFeeInvoice::whereIn('status', ['unpaid', 'partially_paid'])->count();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $monthCollections = FeePayment::whereDate('payment_date', '>=', $startOfMonth)->sum('amount_paid');

        // 6. Exams
        $upcomingExams = Exam::where('end_date', '>=', $today)
            ->orderBy('start_date')
            ->take(5)
            ->get();
        $examsCount = Exam::count();

        // 7. Academic Breakdown
        $classesWithCount = SchoolClass::withCount('students')->orderBy('numeric_level')->get();
        $housesWithCount = House::withCount('students')->get();

        // 8. Recent Activities
        $recentStudents = Student::with(['schoolClass', 'section', 'house'])
            ->latest('id')
            ->take(6)
            ->get();

        $recentPayments = FeePayment::with(['student.schoolClass', 'invoice'])
            ->latest('id')
            ->take(6)
            ->get();

        // 9. Monthly Trends for Chart.js
        $monthlyFeeLabels = [];
        $monthlyFeeData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyFeeLabels[] = $month->format('M Y');
            $sum = FeePayment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount_paid');
            $monthlyFeeData[] = (float)$sum;
        }

        return view('dashboard', compact(
            'currentSession',
            'school',
            'branch',
            'totalStudents',
            'allStudentsCount',
            'totalStaff',
            'totalTeachers',
            'totalOperators',
            'todayStudentTotal',
            'todayStudentPresent',
            'todayStudentAbsent',
            'studentAttendanceRate',
            'todayStaffTotal',
            'todayStaffPresent',
            'staffAttendanceRate',
            'totalInvoiced',
            'totalCollected',
            'totalPending',
            'unpaidInvoicesCount',
            'monthCollections',
            'upcomingExams',
            'examsCount',
            'classesWithCount',
            'housesWithCount',
            'recentStudents',
            'recentPayments',
            'monthlyFeeLabels',
            'monthlyFeeData'
        ));
    }
}

