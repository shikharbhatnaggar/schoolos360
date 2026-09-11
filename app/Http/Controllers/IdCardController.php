<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Services\TenantContext;

class IdCardController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'students'); // students or staff
        $branch = TenantContext::getBranch();
        $school = TenantContext::getSchool();
        $activeSession = AcademicSession::current();

        $orientation = $request->input('orientation', $branch?->id_card_orientation ?? 'portrait');
        $primaryColor = $request->input('primary_color', $branch?->id_card_primary_color ?? '#0d9488');
        $showBloodGroup = $request->boolean('show_blood_group', $branch?->id_card_show_blood_group ?? true);
        $showEmergency = $request->boolean('show_emergency', $branch?->id_card_show_emergency_contact ?? true);

        $classes = SchoolClass::with('sections')->get();
        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $sections = $selectedClassId ? Section::where('school_class_id', $selectedClassId)->get() : collect();
        $selectedSectionId = $request->input('section_id');

        $students = collect();
        $staffMembers = collect();

        if ($type === 'students') {
            $query = Student::with(['schoolClass', 'section', 'house', 'academicSession'])
                ->where('status', 'active');

            if ($selectedClassId) {
                $query->where('school_class_id', $selectedClassId);
            }
            if ($selectedSectionId) {
                $query->where('section_id', $selectedSectionId);
            }

            $students = $query->orderBy('first_name')->take(50)->get();
        } else {
            $staffMembers = Staff::with('house')
                ->where('status', 'active')
                ->orderBy('first_name')
                ->get();
        }

        return view('idcards.index', compact(
            'type',
            'orientation',
            'primaryColor',
            'showBloodGroup',
            'showEmergency',
            'classes',
            'sections',
            'selectedClassId',
            'selectedSectionId',
            'students',
            'staffMembers',
            'activeSession'
        ));
    }

    public function print(Request $request)
    {
        $type = $request->input('type', 'students');
        $branch = TenantContext::getBranch();
        $school = TenantContext::getSchool();
        $activeSession = AcademicSession::current();

        $orientation = $request->input('orientation', $branch?->id_card_orientation ?? 'portrait');
        $primaryColor = $request->input('primary_color', $branch?->id_card_primary_color ?? '#0d9488');
        $showBloodGroup = $request->boolean('show_blood_group', true);
        $showEmergency = $request->boolean('show_emergency', true);

        $items = collect();

        if ($type === 'students') {
            $query = Student::with(['schoolClass', 'section', 'house', 'academicSession'])
                ->where('status', 'active');

            if ($request->has('student_id')) {
                $query->where('id', $request->student_id);
            } elseif ($request->has('class_id')) {
                $query->where('school_class_id', $request->class_id);
                if ($request->has('section_id') && $request->section_id) {
                    $query->where('section_id', $request->section_id);
                }
            }

            $items = $query->orderBy('first_name')->get();
        } else {
            $query = Staff::with('house')->where('status', 'active');
            if ($request->has('staff_id')) {
                $query->where('id', $request->staff_id);
            }
            $items = $query->orderBy('first_name')->get();
        }

        return view('idcards.print', compact(
            'type',
            'orientation',
            'primaryColor',
            'showBloodGroup',
            'showEmergency',
            'items',
            'activeSession'
        ));
    }
}
