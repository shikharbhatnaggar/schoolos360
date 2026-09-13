<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $search = $request->query('search');

        // $query = User::with('staff')->latest('id');

        $schoolId = auth()->user()->school_id;
        $query = User::with('staff')
        ->where('school_id', $schoolId)
        ->latest('id');

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();
        // $unlinkedStaff = Staff::whereNull('user_id')->get();

        $unlinkedStaff = Staff::whereNull('user_id')
        ->where('school_id', $schoolId)
        ->get();

        return view('users.index', compact('users', 'unlinkedStaff', 'role', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,operator,teacher',
            'staff_id' => 'nullable|exists:staff,id',
            'is_active' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'staff_id' => $validated['staff_id'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (!empty($validated['staff_id'])) {
            Staff::where('id', $validated['staff_id'])->update(['user_id' => $user->id]);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,operator,teacher',
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User account {$statusText}.");
    }
}

