<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\ClassTeacher;
use App\Models\House;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $roleFilter = $request->query('role');
        $search = $request->query('search');

        $query = Staff::with(['house', 'user', 'classTeachers.schoolClass', 'classTeachers.section']);

        if ($roleFilter) {
            $query->where('role_type', $roleFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $staffList = $query->latest('id')->paginate(15)->withQueryString();
        $houses = House::all();

        return view('staff.index', compact('staffList', 'houses', 'roleFilter', 'search'));
    }

    public function create()
    {
        $houses = House::all();
        $suggestedEmpId = 'EMP-' . str_pad((Staff::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        return view('staff.create', compact('houses', 'suggestedEmpId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|max:30|unique:staff,employee_id',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:staff,email|unique:users,email',
            'phone' => 'nullable|string|max:25',
            'gender' => 'required|string',
            'role_type' => 'required|string|in:teacher,operator,house_teacher,class_teacher,subject_teacher,admin',
            'designation' => 'required|string|max:100',
            'qualification' => 'nullable|string|max:100',
            'house_id' => 'nullable|exists:houses,id',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'create_user_account' => 'nullable|boolean',
            'password' => 'nullable|required_if:create_user_account,1|min:6',
        ]);

        $userId = null;
        if ($request->boolean('create_user_account')) {
            $authRole = in_array($validated['role_type'], ['operator', 'admin']) ? $validated['role_type'] : 'teacher';
            $user = User::create([
                'name' => "{$validated['first_name']} {$validated['last_name']}",
                'email' => $validated['email'],
                'password' => Hash::make($request->password ?: 'password'),
                'role' => $authRole,
                'is_active' => $validated['status'] === 'active',
            ]);
            $userId = $user->id;
        }

        $staff = Staff::create([
            'user_id' => $userId,
            'employee_id' => $validated['employee_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'role_type' => $validated['role_type'],
            'designation' => $validated['designation'],
            'qualification' => $validated['qualification'],
            'house_id' => $validated['house_id'],
            'joining_date' => $validated['joining_date'],
            'status' => $validated['status'],
            'address' => $validated['address'],
        ]);

        if ($userId) {
            User::where('id', $userId)->update(['staff_id' => $staff->id]);
        }

        return redirect()->route('staff.index')->with('success', 'Staff member registered successfully.');
    }

    public function show(Staff $staff)
    {
        $staff->load(['house', 'user', 'classTeachers.schoolClass', 'classTeachers.section', 'classTeachers.academicSession', 'subjectTeachers.subject', 'subjectTeachers.schoolClass', 'subjectTeachers.section']);
        return view('staff.show', compact('staff'));
    }

    public function assignments()
    {
        $currentSession = AcademicSession::current();
        $teachers = Staff::where('status', 'active')->orderBy('first_name')->get();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $subjects = Subject::orderBy('name')->get();
        $houses = House::all();
        $sessions = AcademicSession::orderByDesc('start_date')->get();

        $classTeachers = ClassTeacher::with(['staff', 'schoolClass', 'section', 'academicSession'])
            ->when($currentSession, fn($q) => $q->where('academic_session_id', $currentSession->id))
            ->get();

        $subjectTeachers = SubjectTeacher::with(['staff', 'schoolClass', 'section', 'subject', 'academicSession'])
            ->when($currentSession, fn($q) => $q->where('academic_session_id', $currentSession->id))
            ->get();

        return view('staff.assignments', compact(
            'teachers',
            'classes',
            'subjects',
            'houses',
            'sessions',
            'currentSession',
            'classTeachers',
            'subjectTeachers'
        ));
    }

    public function assignClassTeacher(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        // Upsert assignment for this class section and session
        ClassTeacher::updateOrCreate(
            [
                'school_class_id' => $validated['school_class_id'],
                'section_id' => $validated['section_id'],
                'academic_session_id' => $validated['academic_session_id'],
            ],
            ['staff_id' => $validated['staff_id']]
        );

        return back()->with('success', 'Class Teacher assigned successfully.');
    }

    public function removeClassTeacher(ClassTeacher $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Class Teacher assignment removed.');
    }

    public function assignSubjectTeacher(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        SubjectTeacher::updateOrCreate(
            [
                'school_class_id' => $validated['school_class_id'],
                'section_id' => $validated['section_id'],
                'subject_id' => $validated['subject_id'],
                'academic_session_id' => $validated['academic_session_id'],
            ],
            ['staff_id' => $validated['staff_id']]
        );

        return back()->with('success', 'Subject Teacher assigned successfully.');
    }

    public function removeSubjectTeacher(SubjectTeacher $assignment)
    {
        $assignment->delete();
        return back()->with('success', 'Subject Teacher assignment removed.');
    }
}

