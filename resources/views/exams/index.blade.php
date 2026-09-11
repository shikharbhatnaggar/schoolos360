<x-layouts.app title="Examinations">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <span>Examinations & Schedules</span>
            </div>
            <a href="{{ route('exams.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Create Examination</span>
            </a>
        </div>
    </x-slot>

    <!-- Session Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" action="{{ route('exams.index') }}" class="flex items-center space-x-4 max-w-md">
            <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Session Filter</label>
            <select name="session_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                <option value="">All Sessions</option>
                @foreach($sessions as $sess)
                    <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
                        {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Exams Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-bold uppercase bg-teal-100 text-teal-800">
                            {{ $exam->term }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400 uppercase">
                            {{ $exam->academicSession?->name }}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $exam->name }}</h3>
                    <p class="text-xs text-slate-500 mb-4">{{ $exam->description ?: 'Comprehensive evaluation for enrolled students.' }}</p>

                    <div class="space-y-2 py-3 border-y border-slate-100 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span><i class="fa-solid fa-calendar text-slate-400 mr-1.5"></i> Dates:</span>
                            <span class="font-semibold">{{ $exam->start_date->format('M d') }} &ndash; {{ $exam->end_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span><i class="fa-solid fa-list-check text-slate-400 mr-1.5"></i> Schedule Entries:</span>
                            <span class="font-semibold">{{ $exam->schedules_count }} papers</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span><i class="fa-solid fa-user-graduate text-slate-400 mr-1.5"></i> Enrolled Students:</span>
                            <span class="font-semibold text-teal-600">{{ $exam->enrollments_count }} enrolled</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-2 flex items-center justify-between gap-2">
                    <a href="{{ route('exams.show', $exam->id) }}" class="flex-1 py-2 text-center bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                        Schedule & Timetable
                    </a>
                    <a href="{{ route('exams.print_schedule', $exam->id) }}" target="_blank" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs transition" title="Print Exam Timetable">
                        <i class="fa-solid fa-print"></i>
                    </a>
                    <a href="{{ route('exams.enroll', $exam->id) }}" class="p-2 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-lg text-xs transition" title="Enroll Students">
                        <i class="fa-solid fa-user-check"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-xl p-12 text-center text-slate-400 border border-slate-200">
                <i class="fa-solid fa-clipboard-question text-4xl mb-3 text-slate-300"></i>
                <p class="text-sm">No examinations scheduled yet.</p>
                <a href="{{ route('exams.create') }}" class="inline-block mt-3 px-4 py-2 bg-teal-600 text-white rounded-lg text-xs font-semibold">
                    Schedule First Exam
                </a>
            </div>
        @endforelse
    </div>
</x-layouts.app>

