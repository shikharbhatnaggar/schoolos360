<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    public function index()
    {
        $sessions = AcademicSession::withCount(['students', 'exams', 'feeStructures'])
            ->orderBy('start_date', 'desc')
            ->get();

        return view('sessions.index', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicSession::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $isCurrent,
        ]);

        return redirect()->route('sessions.index')->with('success', 'Academic session created successfully.');
    }

    public function setCurrent(AcademicSession $session)
    {
        AcademicSession::query()->update(['is_current' => false]);
        $session->update(['is_current' => true]);

        return redirect()->route('sessions.index')->with('success', "Active academic session changed to {$session->name}.");
    }

    public function destroy(AcademicSession $session)
    {
        if ($session->students()->exists()) {
            return back()->with('error', 'Cannot delete session with enrolled students.');
        }

        $session->delete();
        return redirect()->route('sessions.index')->with('success', 'Academic session deleted.');
    }
}

