<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $school = \App\Services\TenantContext::getSchool();
        $branches = \App\Models\Branch::where('school_id', $school->id)
            ->withCount(['students', 'staff', 'classes'])
            ->get();

        return view('branches.index', compact('school', 'branches'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = \App\Models\Branch::findOrFail($request->branch_id);

        if (!$branch->isActive()) {
            return back()->with('error', "Branch '{$branch->name}' is currently disabled. Please enable it before switching.");
        }

        \App\Services\TenantContext::setBranch($branch->id);

        return back()->with('success', "Switched to branch: {$branch->name}");
    }

    public function store(Request $request)
    {
        $school = \App\Services\TenantContext::getSchool();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'fee_receipt_prefix' => 'required|string|max:20',
            'id_card_orientation' => 'required|in:portrait,landscape',
            'id_card_primary_color' => 'required|string|max:20',
        ]);

        $validated['school_id'] = $school->id;
        $validated['status'] = 'active';

        $branch = \App\Models\Branch::create($validated);

        return redirect()->route('branches.index')->with('success', "Branch '{$branch->name}' created successfully.");
    }

    public function update(Request $request, \App\Models\Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'fee_receipt_prefix' => 'required|string|max:20',
            'fee_receipt_next_no' => 'required|integer|min:1',
            'id_card_orientation' => 'required|in:portrait,landscape',
            'id_card_primary_color' => 'required|string|max:20',
            'id_card_title' => 'required|string|max:100',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', "Branch '{$branch->name}' settings updated successfully.");
    }

    public function toggle(\App\Models\Branch $branch)
    {
        $newStatus = $branch->status === 'active' ? 'disabled' : 'active';
        $branch->update(['status' => $newStatus]);

        $message = $newStatus === 'active' 
            ? "Branch '{$branch->name}' enabled successfully." 
            : "Branch '{$branch->name}' disabled.";

        return back()->with('success', $message);
    }
}
