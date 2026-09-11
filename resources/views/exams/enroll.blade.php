<x-layouts.app title="Enroll Students - {{ $exam->name }}">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('exams.show', $exam->id) }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Enroll Students: {{ $exam->name }}</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Class Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <form method="GET" action="{{ route('exams.enroll', $exam->id) }}" class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Class</label>
                    <select name="class_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Section</label>
                    <select name="section_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        <option value="">All Sections</option>
                        @php
                            $currentClass = $classes->find($selectedClassId);
                        @endphp
                        @if($currentClass)
                            @foreach($currentClass->sections as $sec)
                                <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>Section {{ $sec->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </form>
        </div>

        <!-- Enrollment Form & Students List -->
        <form action="{{ route('exams.enroll.store', $exam->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
            x-data="{
                selectAll: false,
                toggleAll() {
                    const checkboxes = document.querySelectorAll('.student-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.selectAll);
                }
            }">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">

            <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-slate-800">Students in Class ({{ $students->count() }})</h4>
                    <p class="text-xs text-slate-500">Select students or click "Enroll All" below</p>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="submit" name="action" value="enroll_all" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-users"></i>
                        <span>Enroll Entire Class</span>
                    </button>
                    <button type="submit" name="action" value="enroll_selected" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-check-double"></i>
                        <span>Enroll Selected</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 w-12 text-center">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="w-4 h-4 text-teal-600 rounded border-slate-300">
                            </th>
                            <th class="px-6 py-3">Student Name</th>
                            <th class="px-6 py-3">Admission No</th>
                            <th class="px-6 py-3">Roll No</th>
                            <th class="px-6 py-3">Section</th>
                            <th class="px-6 py-3">House</th>
                            <th class="px-6 py-3 text-right">Enrollment Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $st)
                            @php
                                $isEnrolled = in_array($st->id, $enrolledStudentIds);
                            @endphp
                            <tr class="hover:bg-slate-50 transition {{ $isEnrolled ? 'bg-teal-50/20' : '' }}">
                                <td class="px-6 py-3.5 text-center">
                                    <input type="checkbox" name="student_ids[]" value="{{ $st->id }}" {{ $isEnrolled ? 'checked disabled' : '' }} class="student-checkbox w-4 h-4 text-teal-600 rounded border-slate-300">
                                </td>
                                <td class="px-6 py-3.5 font-bold text-slate-800">
                                    {{ $st->full_name }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-xs text-slate-600">
                                    {{ $st->admission_no }}
                                </td>
                                <td class="px-6 py-3.5 font-mono text-xs text-slate-600">
                                    {{ $st->roll_no ?: '-' }}
                                </td>
                                <td class="px-6 py-3.5 text-xs">
                                    Section {{ $st->section?->name }}
                                </td>
                                <td class="px-6 py-3.5 text-xs">
                                    @if($st->house)
                                        <span class="inline-flex items-center space-x-1 font-semibold" style="color: {{ $st->house->color }};">
                                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $st->house->color }};"></span>
                                            <span>{{ $st->house->name }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    @if($isEnrolled)
                                        <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Enrolled</span>
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                            Not Enrolled
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                    No students found in this class.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</x-layouts.app>

