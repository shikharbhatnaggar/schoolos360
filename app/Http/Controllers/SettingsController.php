<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\TenantContext;

class SettingsController extends Controller
{
    public function index()
    {
        $school = TenantContext::getSchool();
        $branch = TenantContext::getBranch();

        return view('settings.index', compact('school', 'branch'));
    }

    public function updateSchool(Request $request)
    {
        $school = TenantContext::getSchool();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'logo_url' => 'nullable|string|max:500',
            'status' => 'required|in:active,disabled',
        ]);

        $school->update($validated);

        return back()->with('success', 'School tenant configuration updated successfully.');
    }

    public function updateBranch(Request $request)
    {
        $branch = TenantContext::getBranch();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,disabled',
            'fee_receipt_prefix' => 'required|string|max:20',
            'fee_receipt_next_no' => 'required|integer|min:1',
            'id_card_orientation' => 'required|in:portrait,landscape',
            'id_card_primary_color' => 'required|string|max:20',
            'id_card_title' => 'required|string|max:100',
            'id_card_show_blood_group' => 'boolean',
            'id_card_show_emergency_contact' => 'boolean',
            'id_card_show_address' => 'boolean',
        ]);

        $validated['id_card_show_blood_group'] = $request->boolean('id_card_show_blood_group');
        $validated['id_card_show_emergency_contact'] = $request->boolean('id_card_show_emergency_contact');
        $validated['id_card_show_address'] = $request->boolean('id_card_show_address');

        $branch->update($validated);

        return back()->with('success', "Branch '{$branch->name}' configuration saved successfully.");
    }
}
