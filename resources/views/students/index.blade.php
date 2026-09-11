<x-layouts.app title="Student Directory">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <span>Student Directory & Admissions</span>
            </div>
            <a href="{{ route('students.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Register New Student</span>
            </a>
        </div>
    </x-slot>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
            <!-- Session Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Session</label>
                <select name="session_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->id }}" {{ request('session_id', $currentSession?->id) == $sess->id ? 'selected' : '' }}>
                            {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Class Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Class</label>
                <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- House Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">School House</label>
                <select name="house_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Houses</option>
                    @foreach($houses as $h)
                        <option value="{{ $h->id }}" {{ request('house_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search Query -->
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search Keyword</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Adm No, Roll..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition shadow flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('students.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-medium transition" title="Clear Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Student</th>
                        <th class="px-6 py-3.5">Admission No</th>
                        <th class="px-6 py-3.5">Roll No</th>
                        <th class="px-6 py-3.5">Class & Section</th>
                        <th class="px-6 py-3.5">House</th>
                        <th class="px-6 py-3.5">Parent / Contact</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs uppercase">
                                        {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('students.show', $student->id) }}" class="font-bold text-slate-800 hover:text-teal-600 transition">
                                            {{ $student->full_name }}
                                        </a>
                                        <span class="block text-xs text-slate-400">{{ $student->gender }}, {{ $student->dob ? $student->dob->age . ' yrs' : '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-700">
                                {{ $student->admission_no }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $student->roll_no ?: '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-semibold text-xs">
                                    {{ $student->schoolClass?->name }} ({{ $student->section?->name }})
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($student->house)
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $student->house->color }}20; color: {{ $student->house->color }};">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $student->house->color }};"></span>
                                        <span>{{ $student->house->name }}</span>
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Not Assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <p class="font-medium text-slate-800">{{ $student->parent_name }}</p>
                                <p class="text-slate-500">{{ $student->parent_phone }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('students.show', $student->id) }}" class="p-1.5 text-teal-600 hover:text-teal-800 transition" title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('students.edit', $student->id) }}" class="p-1.5 text-indigo-600 hover:text-indigo-800 transition" title="Edit Student">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No students match your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

