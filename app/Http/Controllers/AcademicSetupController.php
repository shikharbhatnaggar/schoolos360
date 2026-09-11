<?php

namespace App\Http\Controllers;

use App\Models\House;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicSetupController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $houses = House::withCount('students')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('academics.index', compact('classes', 'houses', 'subjects'));
    }

    // Classes & Sections
    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'numeric_level' => 'required|integer|min:1|max:20',
        ]);

        SchoolClass::create($validated);
        return back()->with('success', 'Class created successfully.');
    }

    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:20',
            'capacity' => 'required|integer|min:1|max:200',
        ]);

        Section::create($validated);
        return back()->with('success', 'Section created successfully.');
    }

    // Houses
    public function storeHouse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        House::create($validated);
        return back()->with('success', 'House created successfully.');
    }

    // Subjects
    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:subjects,code',
            'type' => 'required|in:core,elective,activity',
        ]);

        Subject::create($validated);
        return back()->with('success', 'Subject created successfully.');
    }
}

