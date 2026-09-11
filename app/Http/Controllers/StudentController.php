<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\House;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current();

        $query = Student::with(['schoolClass', 'section', 'house', 'academicSession']);

        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        } elseif ($currentSession) {
            $query->where('academic_session_id', $currentSession->id);
        }

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        $students = $query->latest('id')->paginate(15)->withQueryString();

        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $houses = House::all();
        $sessions = AcademicSession::orderByDesc('start_date')->get();

        return view('students.index', compact('students', 'classes', 'houses', 'sessions', 'currentSession'));
    }

    public function create()
    {
        $currentSession = AcademicSession::current();
        $sessions = AcademicSession::orderByDesc('start_date')->get();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $houses = House::all();

        $nextAdmissionNo = 'ADM-' . date('Y') . '-' . str_pad((Student::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('students.create', compact('classes', 'houses', 'sessions', 'currentSession', 'nextAdmissionNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_no' => 'required|string|max:30|unique:students,admission_no',
            'roll_no' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date|before:today',
            'blood_group' => 'nullable|string|max:5',
            'parent_name' => 'required|string|max:100',
            'parent_phone' => 'required|string|max:25',
            'parent_email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'house_id' => 'nullable|exists:houses,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,transferred',
        ]);

        $student = Student::create($validated);

        return redirect()->route('students.show', $student->id)->with('success', "Student {$student->full_name} admitted successfully!");
    }

    public function show(Student $student)
    {
        $student->load([
            'schoolClass',
            'section',
            'house',
            'academicSession',
            'feeInvoices.items.feeHead',
            'feeInvoices.payments',
            'examEnrollments.exam',
            'marks.examSchedule.subject',
            'marks.examSchedule.exam',
        ]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $sessions = AcademicSession::orderByDesc('start_date')->get();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $houses = House::all();

        return view('students.edit', compact('student', 'classes', 'houses', 'sessions'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'admission_no' => 'required|string|max:30|unique:students,admission_no,' . $student->id,
            'roll_no' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date|before:today',
            'blood_group' => 'nullable|string|max:5',
            'parent_name' => 'required|string|max:100',
            'parent_phone' => 'required|string|max:25',
            'parent_email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'house_id' => 'nullable|exists:houses,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'admission_date' => 'required|date',
            'status' => 'required|in:active,inactive,transferred',
        ]);

        $student->update($validated);

        return redirect()->route('students.show', $student->id)->with('success', 'Student information updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student record deleted.');
    }
}

