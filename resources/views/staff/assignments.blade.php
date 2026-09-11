<x-layouts.app title="Teacher Role Assignments">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('staff.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Teacher Role & Subject Assignments</span>
        </div>
    </x-slot>

    @php
        $classSectionMap = [];
        foreach($classes as $c) {
            $classSectionMap[$c->id] = $c->sections->map(function($s) {
                return ['id' => $s->id, 'name' => $s->name];
            });
        }
    @endphp

    <div class="space-y-8">
        <!-- Forms Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 1. Assign Class Teacher Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6"
                x-data="{
                    classMap: {{ json_encode($classSectionMap) }},
                    selectedClass: '{{ $classes->first()?->id }}',
                    get currentSections() {
                        return this.classMap[this.selectedClass] || [];
                    }
                }">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                        <i class="fa-solid fa-chalkboard text-teal-600"></i>
                        <span>Assign Class Teacher</span>
                    </h3>
                    <p class="text-xs text-slate-500">Designate a teacher as responsible for a class section</p>
                </div>

                <form action="{{ route('staff.assignments.class_teacher') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Academic Session</label>
                        <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->id }}" {{ $currentSession?->id == $sess->id ? 'selected' : '' }}>{{ $sess->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Teacher</label>
                        <select name="staff_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            <option value="">Choose Teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Class</label>
                            <select name="school_class_id" x-model="selectedClass" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Section</label>
                            <select name="section_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                <template x-for="sec in currentSections" :key="sec.id">
                                    <option :value="sec.id" x-text="'Section ' + sec.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-xs transition shadow">
                        Assign Class Teacher
                    </button>
                </form>
            </div>

            <!-- 2. Assign Subject Teacher Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6"
                x-data="{
                    classMap: {{ json_encode($classSectionMap) }},
                    selectedClass: '{{ $classes->first()?->id }}',
                    get currentSections() {
                        return this.classMap[this.selectedClass] || [];
                    }
                }">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center space-x-2">
                        <i class="fa-solid fa-book-open text-indigo-600"></i>
                        <span>Assign Subject Teacher</span>
                    </h3>
                    <p class="text-xs text-slate-500">Allocate subject teaching duties to faculty for specific classes</p>
                </div>

                <form action="{{ route('staff.assignments.subject_teacher') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Academic Session</label>
                        <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            @foreach($sessions as $sess)
                                <option value="{{ $sess->id }}" {{ $currentSession?->id == $sess->id ? 'selected' : '' }}>{{ $sess->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Teacher</label>
                        <select name="staff_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            <option value="">Choose Teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->employee_id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Class</label>
                            <select name="school_class_id" x-model="selectedClass" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Section</label>
                            <select name="section_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                <template x-for="sec in currentSections" :key="sec.id">
                                    <option :value="sec.id" x-text="'Section ' + sec.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Subject</label>
                        <select name="subject_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs transition shadow">
                        Assign Subject Teacher
                    </button>
                </form>
            </div>
        </div>

        <!-- Current Assignments Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Class Teachers Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h4 class="font-bold text-slate-800 text-sm">Active Class Teachers</h4>
                </div>
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Class & Sec</th>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($classTeachers as $ct)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $ct->schoolClass?->name }} - {{ $ct->section?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $ct->staff?->full_name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('staff.assignments.remove_class_teacher', $ct->id) }}" method="POST" onsubmit="return confirm('Remove this class teacher assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-xs">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-slate-400">No class teachers assigned for current session.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Active Subject Teachers Table -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h4 class="font-bold text-slate-800 text-sm">Active Subject Teachers</h4>
                </div>
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Class / Sec</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Teacher</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($subjectTeachers as $st)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $st->schoolClass?->name }} ({{ $st->section?->name }})</td>
                                <td class="px-4 py-3 text-slate-700 font-medium">{{ $st->subject?->name }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $st->staff?->full_name }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form action="{{ route('staff.assignments.remove_subject_teacher', $st->id) }}" method="POST" onsubmit="return confirm('Remove this subject teacher assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-xs">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400">No subject teachers assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

