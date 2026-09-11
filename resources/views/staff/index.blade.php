<x-layouts.app title="Faculty & Staff Directory">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <span>Faculty & Staff Management</span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('staff.assignments') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-id-badge"></i>
                    <span>Teacher Role Assignments</span>
                </a>
                <a href="{{ route('staff.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Register New Staff</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" action="{{ route('staff.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Filter By Role</label>
                <select name="role" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Roles</option>
                    <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="operator" {{ request('role') === 'operator' ? 'selected' : '' }}>Operator / Office Staff</option>
                    <option value="house_teacher" {{ request('role') === 'house_teacher' ? 'selected' : '' }}>House Teacher</option>
                    <option value="class_teacher" {{ request('role') === 'class_teacher' ? 'selected' : '' }}>Class Teacher</option>
                    <option value="subject_teacher" {{ request('role') === 'subject_teacher' ? 'selected' : '' }}>Subject Teacher</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search Staff</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Employee ID, Email, Designation..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition shadow flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('staff.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-medium transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Employee</th>
                        <th class="px-6 py-3.5">Employee ID</th>
                        <th class="px-6 py-3.5">Designation</th>
                        <th class="px-6 py-3.5">Role Type</th>
                        <th class="px-6 py-3.5">House / Supervision</th>
                        <th class="px-6 py-3.5">Contact</th>
                        <th class="px-6 py-3.5">Portal User</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($staffList as $staff)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('staff.show', $staff->id) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition">
                                            {{ $staff->full_name }}
                                        </a>
                                        <span class="block text-xs text-slate-400">{{ $staff->gender }} &bull; {{ $staff->qualification }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-700">
                                {{ $staff->employee_id }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800 text-xs">
                                {{ $staff->designation }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold uppercase 
                                    {{ $staff->role_type === 'operator' ? 'bg-amber-100 text-amber-800' : ($staff->role_type === 'admin' ? 'bg-rose-100 text-rose-800' : 'bg-indigo-100 text-indigo-800') }}">
                                    {{ str_replace('_', ' ', $staff->role_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($staff->house)
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-xs font-semibold" style="background-color: {{ $staff->house->color }}20; color: {{ $staff->house->color }};">
                                        <i class="fa-solid fa-flag text-[10px]"></i>
                                        <span>{{ $staff->house->name }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <p class="text-slate-700 font-medium">{{ $staff->email }}</p>
                                <p class="text-slate-400 font-mono">{{ $staff->phone }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($staff->user)
                                    <span class="inline-flex items-center space-x-1 text-emerald-600 font-semibold">
                                        <i class="fa-solid fa-check-circle text-xs"></i>
                                        <span>Active ({{ $staff->user->role }})</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">No login</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('staff.show', $staff->id) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-sm">
                                No staff members found matching filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffList->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $staffList->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

