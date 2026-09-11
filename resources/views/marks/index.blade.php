<x-layouts.app title="Student Marks Entry">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <span>Marks Entry & Evaluation</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Selector Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <form method="GET" action="{{ route('marks.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Session</label>
                    <select name="session_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        @foreach($sessions as $sess)
                            <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
                                {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Examination *</label>
                    <select name="exam_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}" {{ $examId == $ex->id ? 'selected' : '' }}>{{ $ex->name }} ({{ $ex->term }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Class *</label>
                    <select name="class_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Section</label>
                    <select name="section_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        <option value="">All Sections</option>
                        @php
                            $currClass = $classes->find($classId);
                        @endphp
                        @if($currClass)
                            @foreach($currClass->sections as $sec)
                                <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>Section {{ $sec->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Subject *</label>
                    <select name="subject_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ $subjectId == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if($schedule)
            <!-- Exam Paper Header Info -->
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl p-5 text-white flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
                <div>
                    <div class="flex items-center space-x-2 text-teal-400 text-xs font-bold uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-book-bookmark"></i>
                        <span>{{ $schedule->subject?->code }} &bull; {{ $schedule->schoolClass?->name }}</span>
                    </div>
                    <h3 class="text-xl font-black text-white">{{ $schedule->subject?->name }}</h3>
                    <p class="text-slate-300 text-xs mt-0.5">
                        Date: <strong>{{ $schedule->exam_date->format('M d, Y') }}</strong> &bull;
                        Time: <strong>{{ date('g:i A', strtotime($schedule->start_time)) }} &ndash; {{ date('g:i A', strtotime($schedule->end_time)) }}</strong> &bull;
                        Room: <strong>{{ $schedule->room_no ?: 'Main Hall' }}</strong>
                    </p>
                </div>
                <div class="flex items-center space-x-6 text-center">
                    <div class="px-4 py-2 bg-slate-800/80 rounded-lg border border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Maximum Marks</span>
                        <span class="text-xl font-extrabold text-teal-400 font-mono">{{ $schedule->max_marks }}</span>
                    </div>
                    <div class="px-4 py-2 bg-slate-800/80 rounded-lg border border-slate-700">
                        <span class="text-[10px] text-slate-400 uppercase font-semibold block">Passing Marks</span>
                        <span class="text-xl font-extrabold text-amber-400 font-mono">{{ $schedule->pass_marks }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabs: Manual Entry vs CSV Import -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-data="{ mode: 'manual' }">
                <div class="px-6 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/70">
                    <div class="flex space-x-3">
                        <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-white text-teal-700 shadow-sm border-slate-300' : 'text-slate-500 hover:text-slate-700 border-transparent'" class="px-4 py-1.5 rounded-lg border text-xs font-bold transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-table-cells"></i>
                            <span>Manual Grid Entry</span>
                        </button>
                        <button type="button" @click="mode = 'csv'" :class="mode === 'csv' ? 'bg-white text-teal-700 shadow-sm border-slate-300' : 'text-slate-500 hover:text-slate-700 border-transparent'" class="px-4 py-1.5 rounded-lg border text-xs font-bold transition flex items-center space-x-1.5">
                            <i class="fa-solid fa-file-csv"></i>
                            <span>CSV Bulk Import</span>
                        </button>
                    </div>

                    <!-- Download CSV Template link -->
                    <a href="{{ route('marks.template', ['exam_id' => $examId, 'class_id' => $classId, 'subject_id' => $subjectId, 'section_id' => $sectionId]) }}"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center space-x-1">
                        <i class="fa-solid fa-download"></i>
                        <span>Download Pre-filled CSV Template</span>
                    </a>
                </div>

                <!-- 1. Manual Entry Tab -->
                <div x-show="mode === 'manual'">
                    <form action="{{ route('marks.save_manual') }}" method="POST">
                        @csrf
                        <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="px-6 py-3">Roll No</th>
                                        <th class="px-6 py-3">Admission No</th>
                                        <th class="px-6 py-3">Student Name</th>
                                        <th class="px-6 py-3">Section</th>
                                        <th class="px-6 py-3">Marks Obtained (Max: {{ $schedule->max_marks }})</th>
                                        <th class="px-6 py-3">Absent?</th>
                                        <th class="px-6 py-3">Remarks / Feedback</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($studentsWithMarks as $student)
                                        @php
                                            $mark = $student->marks->first();
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition" x-data="{ absent: {{ $mark && $mark->is_absent ? 'true' : 'false' }} }">
                                            <td class="px-6 py-3 font-mono text-xs font-bold text-slate-800">
                                                {{ $student->roll_no ?: '-' }}
                                            </td>
                                            <td class="px-6 py-3 font-mono text-xs text-slate-500">
                                                {{ $student->admission_no }}
                                            </td>
                                            <td class="px-6 py-3 font-bold text-slate-900">
                                                {{ $student->full_name }}
                                            </td>
                                            <td class="px-6 py-3 text-xs">
                                                Section {{ $student->section?->name }}
                                            </td>
                                            <td class="px-6 py-3">
                                                <div class="relative w-32">
                                                    <input type="number" step="0.5" min="0" max="{{ $schedule->max_marks }}"
                                                        name="marks[{{ $student->id }}]"
                                                        value="{{ $mark ? $mark->marks_obtained : '' }}"
                                                        :disabled="absent"
                                                        placeholder="0.00"
                                                        class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded text-xs font-mono font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none disabled:bg-slate-200 disabled:text-slate-400">
                                                </div>
                                            </td>
                                            <td class="px-6 py-3">
                                                <label class="inline-flex items-center space-x-1.5 text-xs text-slate-700 cursor-pointer">
                                                    <input type="checkbox" name="absent[{{ $student->id }}]" value="1" x-model="absent" {{ $mark && $mark->is_absent ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                                    <span :class="absent ? 'font-bold text-rose-600' : 'text-slate-500'">Absent</span>
                                                </label>
                                            </td>
                                            <td class="px-6 py-3">
                                                <input type="text" name="remarks[{{ $student->id }}]" value="{{ $mark ? $mark->remarks : '' }}" placeholder="Optional feedback"
                                                    class="w-full px-3 py-1 bg-slate-50 border border-slate-300 rounded text-xs text-slate-700 focus:bg-white focus:outline-none">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                                No students enrolled in this exam for the selected class/section.
                                                <a href="{{ route('exams.enroll', $examId) }}" class="text-teal-600 hover:underline font-bold ml-1">Enroll Students Now</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($studentsWithMarks->isNotEmpty())
                            <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end">
                                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center space-x-2">
                                    <i class="fa-solid fa-save"></i>
                                    <span>Save Student Marks</span>
                                </button>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- 2. CSV Bulk Import Tab -->
                <div x-show="mode === 'csv'" class="p-8 max-w-xl mx-auto space-y-6" x-cloak>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl mx-auto mb-2">
                            <i class="fa-solid fa-file-csv"></i>
                        </div>
                        <h4 class="font-bold text-slate-800">Upload Marks CSV File</h4>
                        <p class="text-xs text-slate-500 mt-1">Import marks directly from a completed CSV spreadsheet</p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-2">
                        <p class="font-bold text-slate-700">Instructions for CSV import:</p>
                        <ol class="list-decimal list-inside space-y-1 text-slate-600 text-[11px]">
                            <li>Download the <a href="{{ route('marks.template', ['exam_id' => $examId, 'class_id' => $classId, 'subject_id' => $subjectId, 'section_id' => $sectionId]) }}" class="text-indigo-600 font-bold underline">pre-filled CSV template</a>.</li>
                            <li>Fill in the <code>marks_obtained</code> column (0 to {{ $schedule->max_marks }}).</li>
                            <li>Set <code>is_absent</code> to 1 if the student was absent.</li>
                            <li>Save file as CSV and upload below.</li>
                        </ol>
                    </div>

                    <form action="{{ route('marks.import_csv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Choose CSV File</label>
                            <input type="file" name="csv_file" accept=".csv,.txt" required
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs transition shadow flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload & Save Marks</span>
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- No Schedule Alert -->
            <div class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fa-solid fa-calendar-xmark text-4xl text-slate-300 mb-3"></i>
                <h4 class="font-bold text-slate-800 text-base">Paper Not Scheduled Yet</h4>
                <p class="text-xs text-slate-500 mt-1">This subject does not have a scheduled paper for this exam and class.</p>
                <a href="{{ route('exams.show', $examId ?? 1) }}" class="inline-block mt-4 px-4 py-2 bg-teal-600 text-white rounded-lg text-xs font-semibold">
                    Open Timetable to Schedule Paper
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>

