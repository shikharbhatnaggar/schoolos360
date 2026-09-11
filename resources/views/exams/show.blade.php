<x-layouts.app title="{{ $exam->name }} - Timetable">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <a href="{{ route('exams.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span>{{ $exam->name }} &ndash; Schedule & Timetable</span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('exams.enroll', $exam->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Enroll Students ({{ $enrollmentCount }})</span>
                </a>
                <a href="{{ route('exams.print_schedule', $exam->id) }}" target="_blank" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Print Schedule Sheet</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Add Schedule Entry Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-800 text-sm mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-calendar-plus text-teal-600"></i>
                <span>Add Exam Schedule Timetable Item</span>
            </h4>

            <form action="{{ route('exams.schedules.save', $exam->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @csrf
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Class *</label>
                    <select name="school_class_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Subject *</label>
                    <select name="subject_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Date *</label>
                    <input type="date" name="exam_date" value="{{ $exam->start_date->format('Y-m-d') }}" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Start Time *</label>
                    <input type="time" name="start_time" value="09:00" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">End Time *</label>
                    <input type="time" name="end_time" value="12:00" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Room No</label>
                    <input type="text" name="room_no" placeholder="e.g. Hall 1"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Max Marks *</label>
                    <input type="number" step="0.5" name="max_marks" value="100" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Pass Marks *</label>
                    <input type="number" step="0.5" name="pass_marks" value="40" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800">
                </div>

                <div class="lg:col-span-4 flex items-end">
                    <button type="submit" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-xs transition shadow flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Schedule Paper</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Timetable Entries Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h4 class="font-bold text-slate-800">Examination Timetable</h4>
                
                <!-- Class filter dropdown -->
                <form method="GET" action="{{ route('exams.show', $exam->id) }}" class="flex items-center space-x-2">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Class:</label>
                    <select name="class_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Class</th>
                            <th class="px-6 py-3.5">Subject</th>
                            <th class="px-6 py-3.5">Exam Date</th>
                            <th class="px-6 py-3.5">Time</th>
                            <th class="px-6 py-3.5">Room</th>
                            <th class="px-6 py-3.5">Max / Pass</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($schedules as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-800 text-xs">
                                    {{ $item->schoolClass?->name }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-900">
                                    {{ $item->subject?->name }}
                                    <span class="block text-[11px] font-mono text-slate-400">{{ $item->subject?->code }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                    {{ $item->exam_date->format('D, M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-600">
                                    {{ date('g:i A', strtotime($item->start_time)) }} &ndash; {{ date('g:i A', strtotime($item->end_time)) }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    {{ $item->room_no ?: 'Main Hall' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono">
                                    <span class="font-bold text-slate-800">{{ $item->max_marks }}</span> / <span class="text-slate-500">{{ $item->pass_marks }}</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('marks.index', ['exam_id' => $exam->id, 'class_id' => $item->school_class_id, 'subject_id' => $item->subject_id]) }}" class="px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold rounded transition">
                                        Enter Marks
                                    </a>
                                    <form action="{{ route('exams.schedules.delete', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove this exam paper from timetable?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-400 text-sm">
                                    No exam papers scheduled for this exam yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>

