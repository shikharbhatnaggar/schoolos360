<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamEnrollment;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current();
        $sessionId = $request->query('session_id', $currentSession ? $currentSession->id : null);

        $query = Exam::with(['academicSession'])
            ->withCount(['schedules', 'enrollments'])
            ->latest('start_date');

        if ($sessionId) {
            $query->where('academic_session_id', $sessionId);
        }

        $exams = $query->get();
        $sessions = AcademicSession::orderByDesc('start_date')->get();

        return view('exams.index', compact('exams', 'sessions', 'sessionId', 'currentSession'));
    }

    public function create()
    {
        $currentSession = AcademicSession::current();
        $sessions = AcademicSession::orderByDesc('start_date')->get();
        return view('exams.create', compact('currentSession', 'sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'name' => 'required|string|max:150',
            'term' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,published',
        ]);

        $exam = Exam::create($validated);

        return redirect()->route('exams.show', $exam->id)->with('success', 'Exam created. You can now build the schedule timetable and enroll students.');
    }

    public function show(Exam $exam, Request $request)
    {
        $exam->load('academicSession');

        $classFilter = $request->query('class_id');

        $schedulesQuery = ExamSchedule::with(['schoolClass', 'subject'])
            ->where('exam_id', $exam->id)
            ->orderBy('exam_date')
            ->orderBy('start_time');

        if ($classFilter) {
            $schedulesQuery->where('school_class_id', $classFilter);
        }

        $schedules = $schedulesQuery->get();

        $classes = SchoolClass::orderBy('numeric_level')->get();
        $subjects = Subject::orderBy('name')->get();
        $enrollmentCount = ExamEnrollment::where('exam_id', $exam->id)->count();

        return view('exams.show', compact('exam', 'schedules', 'classes', 'subjects', 'classFilter', 'enrollmentCount'));
    }

    public function saveSchedule(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_no' => 'nullable|string|max:50',
            'max_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0|lte:max_marks',
        ]);

        ExamSchedule::updateOrCreate(
            [
                'exam_id' => $exam->id,
                'school_class_id' => $validated['school_class_id'],
                'subject_id' => $validated['subject_id'],
            ],
            [
                'exam_date' => $validated['exam_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'room_no' => $validated['room_no'],
                'max_marks' => $validated['max_marks'],
                'pass_marks' => $validated['pass_marks'],
            ]
        );

        return back()->with('success', 'Exam timetable entry added successfully.');
    }

    public function deleteSchedule(ExamSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Schedule timetable entry removed.');
    }

    public function printSchedule(Exam $exam, Request $request)
    {
        $classId = $request->query('class_id');
        $selectedClass = $classId ? SchoolClass::find($classId) : null;

        $schedules = ExamSchedule::with(['schoolClass', 'subject'])
            ->where('exam_id', $exam->id)
            ->when($classId, fn($q) => $q->where('school_class_id', $classId))
            ->orderBy('school_class_id')
            ->orderBy('exam_date')
            ->orderBy('start_time')
            ->get();

        return view('exams.schedule_print', compact('exam', 'schedules', 'selectedClass'));
    }

    public function enrollmentView(Exam $exam, Request $request)
    {
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $selectedClassId = $request->query('class_id', $classes->first()?->id);
        $selectedSectionId = $request->query('section_id');

        $studentsQuery = Student::where('status', 'active')
            ->where('academic_session_id', $exam->academic_session_id)
            ->when($selectedClassId, fn($q) => $q->where('school_class_id', $selectedClassId))
            ->when($selectedSectionId, fn($q) => $q->where('section_id', $selectedSectionId))
            ->with(['schoolClass', 'section', 'house']);

        $students = $studentsQuery->get();

        $enrolledStudentIds = ExamEnrollment::where('exam_id', $exam->id)
            ->pluck('student_id')
            ->toArray();

        return view('exams.enroll', compact('exam', 'classes', 'selectedClassId', 'selectedSectionId', 'students', 'enrolledStudentIds'));
    }

    public function storeEnrollment(Request $request, Exam $exam)
    {
        $studentIds = $request->input('student_ids', []);

        if ($request->action === 'enroll_all') {
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            $students = Student::where('status', 'active')
                ->where('academic_session_id', $exam->academic_session_id)
                ->when($classId, fn($q) => $q->where('school_class_id', $classId))
                ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                ->get();

            foreach ($students as $student) {
                ExamEnrollment::firstOrCreate([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ], [
                    'exam_roll_no' => $student->roll_no ?: $student->admission_no,
                ]);
            }

            return back()->with('success', 'All students in the selected class/section have been enrolled in this exam.');
        }

        if (empty($studentIds)) {
            return back()->with('error', 'Please select at least one student to enroll.');
        }

        foreach ($studentIds as $id) {
            $st = Student::find($id);
            if ($st) {
                ExamEnrollment::firstOrCreate([
                    'exam_id' => $exam->id,
                    'student_id' => $id,
                ], [
                    'exam_roll_no' => $st->roll_no ?: $st->admission_no,
                ]);
            }
        }

        return back()->with('success', count($studentIds) . ' student(s) enrolled into the exam.');
    }
}

