<?php

namespace App\Http\Controllers;

use App\Models\BiometricLog;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * 1. Student Attendance - Manual Sheet
     */
    public function studentIndex(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));
        $classes = SchoolClass::with('sections')->get();

        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $sections = $selectedClassId ? Section::where('school_class_id', $selectedClassId)->get() : collect();
        $selectedSectionId = $request->input('section_id', $sections->first()?->id);

        $students = collect();
        $attendances = collect();

        if ($selectedClassId && $selectedSectionId) {
            $students = Student::where('school_class_id', $selectedClassId)
                ->where('section_id', $selectedSectionId)
                ->where('status', 'active')
                ->orderBy('roll_no')
                ->get();

            $attendances = StudentAttendance::where('school_class_id', $selectedClassId)
                ->where('section_id', $selectedSectionId)
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.students', compact(
            'classes',
            'sections',
            'students',
            'attendances',
            'selectedDate',
            'selectedClassId',
            'selectedSectionId'
        ));
    }

    /**
     * Save student manual attendance
     */
    public function studentSave(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'attendances' => 'required|array',
        ]);

        $date = $request->date;
        $classId = $request->school_class_id;
        $sectionId = $request->section_id;
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        foreach ($request->attendances as $studentId => $status) {
            StudentAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'school_id' => $school->id,
                    'branch_id' => $branch?->id,
                    'school_class_id' => $classId,
                    'section_id' => $sectionId,
                    'status' => $status,
                    'method' => 'manual',
                    'remarks' => $request->input("remarks.{$studentId}"),
                    'recorded_by_id' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Student attendance recorded successfully for ' . Carbon::parse($date)->format('d M, Y'));
    }

    /**
     * Student QR Code / Barcode Scan AJAX endpoint
     */
    public function studentQrScan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $rawCode = trim($request->code);
        // Extract admission number if QR contains URL or structured text
        if (preg_match('/admission[_\-=:]*([A-Za-z0-9\-]+)/i', $rawCode, $matches)) {
            $admissionNo = $matches[1];
        } else {
            $admissionNo = $rawCode;
        }

        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        $student = Student::with(['schoolClass', 'section', 'house'])
            ->where(function ($q) use ($admissionNo, $rawCode) {
                $q->where('admission_no', $admissionNo)
                  ->orWhere('admission_no', $rawCode)
                  ->orWhere('id', is_numeric($rawCode) ? (int)$rawCode : 0);
            })
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => "No student found matching code: {$rawCode}",
            ], 404);
        }

        $today = date('Y-m-d');
        $attendance = StudentAttendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $today,
            ],
            [
                'school_id' => $school->id,
                'branch_id' => $branch?->id,
                'school_class_id' => $student->school_class_id,
                'section_id' => $student->section_id,
                'status' => 'present',
                'method' => 'qr_scan',
                'remarks' => 'Scanned via QR Code at ' . date('h:i A'),
                'recorded_by_id' => Auth::id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Attendance marked Present for {$student->full_name}",
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_no' => $student->admission_no,
                'roll_no' => $student->roll_no ?: '-',
                'class' => ($student->schoolClass?->name ?? 'Class') . ' (' . ($student->section?->name ?? 'A') . ')',
                'house' => $student->house?->name ?? 'No House',
                'avatar' => $student->avatar_url,
                'status' => 'Present',
                'time' => date('h:i A'),
            ]
        ]);
    }

    /**
     * 2. Staff Attendance - Manual Sheet
     */
    public function staffIndex(Request $request)
    {
        $selectedDate = $request->input('date', date('Y-m-d'));

        $staffMembers = Staff::where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $attendances = StaffAttendance::whereDate('date', $selectedDate)
            ->get()
            ->keyBy('staff_id');

        return view('attendance.staff', compact('staffMembers', 'attendances', 'selectedDate'));
    }

    /**
     * Save staff manual attendance
     */
    public function staffSave(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
        ]);

        $date = $request->date;
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        foreach ($request->attendances as $staffId => $status) {
            StaffAttendance::updateOrCreate(
                [
                    'staff_id' => $staffId,
                    'date' => $date,
                ],
                [
                    'school_id' => $school->id,
                    'branch_id' => $branch?->id,
                    'status' => $status,
                    'method' => 'manual',
                    'punch_in_time' => $status === 'present' ? date('H:i:s') : null,
                    'remarks' => $request->input("remarks.{$staffId}"),
                    'recorded_by_id' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Staff attendance updated for ' . Carbon::parse($date)->format('d M, Y'));
    }

    /**
     * Staff QR Code Scan AJAX endpoint
     */
    public function staffQrScan(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $rawCode = trim($request->code);
        if (preg_match('/employee[_\-=:]*([A-Za-z0-9\-]+)/i', $rawCode, $matches)) {
            $employeeId = $matches[1];
        } else {
            $employeeId = $rawCode;
        }

        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        $staff = Staff::where('employee_id', $employeeId)
            ->orWhere('employee_id', $rawCode)
            ->orWhere('id', is_numeric($rawCode) ? (int)$rawCode : 0)
            ->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => "No staff found matching code: {$rawCode}",
            ], 404);
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');

        $existing = StaffAttendance::where('staff_id', $staff->id)->whereDate('date', $today)->first();

        if ($existing && $existing->punch_in_time && !$existing->punch_out_time) {
            $existing->update([
                'punch_out_time' => $now,
                'remarks' => ($existing->remarks ? $existing->remarks . ' | ' : '') . 'Out at ' . date('h:i A'),
            ]);
            $action = 'Punch OUT recorded at ' . date('h:i A');
        } else {
            StaffAttendance::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'date' => $today,
                ],
                [
                    'school_id' => $school->id,
                    'branch_id' => $branch?->id,
                    'status' => 'present',
                    'method' => 'qr_scan',
                    'punch_in_time' => $existing?->punch_in_time ?? $now,
                    'remarks' => 'Scanned via QR Code at ' . date('h:i A'),
                    'recorded_by_id' => Auth::id(),
                ]
            );
            $action = 'Punch IN recorded at ' . date('h:i A');
        }

        return response()->json([
            'success' => true,
            'message' => "{$staff->full_name}: {$action}",
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->full_name,
                'employee_id' => $staff->employee_id,
                'designation' => $staff->designation,
                'role' => ucwords(str_replace('_', ' ', $staff->role_type)),
                'avatar' => $staff->avatar_url,
                'status' => 'Present',
                'action' => $action,
                'time' => date('h:i A'),
            ]
        ]);
    }

    /**
     * 3. Biometric Device Simulator & Sync Hub
     */
    public function biometricSimulatorView()
    {
        $staffMembers = Staff::where('status', 'active')->orderBy('first_name')->get();
        $recentLogs = BiometricLog::latest('punch_timestamp')->take(30)->get();
        $pendingCount = BiometricLog::where('processed', false)->count();

        return view('attendance.biometric', compact('staffMembers', 'recentLogs', 'pendingCount'));
    }

    /**
     * Simulate a biometric device punch (Fingerprint / Face ID device push)
     */
    public function biometricSimulatePunch(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string',
            'punch_type' => 'required|in:in,out',
            'device_id' => 'nullable|string',
        ]);

        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        $staff = Staff::where('employee_id', $request->employee_id)->first();
        if (!$staff) {
            return back()->with('error', "Employee ID '{$request->employee_id}' not found.");
        }

        $now = now();
        $log = BiometricLog::create([
            'school_id' => $school->id,
            'branch_id' => $branch?->id,
            'device_id' => $request->device_id ?: 'BIO-MAIN-01',
            'employee_id' => $staff->employee_id,
            'punch_timestamp' => $now,
            'punch_type' => $request->punch_type,
            'processed' => false,
        ]);

        // Auto-sync this punch immediately to staff attendance
        $this->processSingleBiometricLog($log);

        return back()->with('success', "Simulated Biometric {$request->punch_type} punch for {$staff->full_name} ({$staff->employee_id}) at " . $now->format('h:i:s A'));
    }

    /**
     * Sync all unprocessed biometric device logs into staff attendance
     */
    public function biometricSyncLogs(Request $request)
    {
        $unprocessed = BiometricLog::where('processed', false)->get();
        $count = 0;

        foreach ($unprocessed as $log) {
            if ($this->processSingleBiometricLog($log)) {
                $count++;
            }
        }

        return back()->with('success', "Synced {$count} biometric punch records into staff attendance successfully.");
    }

    /**
     * Upload CSV device punch logs
     */
    public function biometricUploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // skip or read header
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        $imported = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                // Expected format: device_id, employee_id, punch_timestamp, punch_type(optional)
                $deviceId = trim($row[0]);
                $employeeId = trim($row[1]);
                $timestamp = Carbon::parse(trim($row[2]));
                $punchType = isset($row[3]) && strtolower(trim($row[3])) === 'out' ? 'out' : 'in';

                $log = BiometricLog::create([
                    'school_id' => $school->id,
                    'branch_id' => $branch?->id,
                    'device_id' => $deviceId,
                    'employee_id' => $employeeId,
                    'punch_timestamp' => $timestamp,
                    'punch_type' => $punchType,
                    'processed' => false,
                ]);

                $this->processSingleBiometricLog($log);
                $imported++;
            }
        }
        fclose($handle);

        return back()->with('success', "Imported and synchronized {$imported} biometric logs from CSV.");
    }

    protected function processSingleBiometricLog(BiometricLog $log): bool
    {
        $staff = Staff::where('employee_id', $log->employee_id)->first();
        if (!$staff) {
            return false;
        }

        $punchDate = $log->punch_timestamp->format('Y-m-d');
        $punchTime = $log->punch_timestamp->format('H:i:s');

        $attendance = StaffAttendance::firstOrNew([
            'staff_id' => $staff->id,
            'date' => $punchDate,
        ]);

        $attendance->school_id = $log->school_id;
        $attendance->branch_id = $log->branch_id;
        $attendance->status = 'present';
        $attendance->method = 'biometric';

        if ($log->punch_type === 'in') {
            if (!$attendance->punch_in_time || $punchTime < $attendance->punch_in_time) {
                $attendance->punch_in_time = $punchTime;
            }
        } else {
            if (!$attendance->punch_out_time || $punchTime > $attendance->punch_out_time) {
                $attendance->punch_out_time = $punchTime;
            }
        }

        $attendance->remarks = "Synced from Device {$log->device_id}";
        $attendance->save();

        $log->update(['processed' => true]);
        return true;
    }
}
