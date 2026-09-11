<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Schedule Sheet - {{ $exam->name }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .sheet-card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center">

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print max-w-4xl w-full mb-6 flex items-center justify-between">
        <a href="{{ route('exams.show', $exam->id) }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-900 text-sm font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Exam Management</span>
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center space-x-2">
            <i class="fa-solid fa-print"></i>
            <span>Print Schedule Sheet</span>
        </button>
    </div>

    <!-- Official Printable Schedule Sheet -->
    <div class="sheet-card max-w-4xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12 relative text-slate-800">
        
        <!-- Letterhead -->
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-slate-900 text-teal-400 rounded-2xl flex items-center justify-center text-3xl shadow">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">EDUPULSE ACADEMY</h1>
                    <p class="text-xs text-slate-500 font-medium">Department of Examinations & Academic Evaluations</p>
                    <p class="text-xs text-slate-400">Affiliated to Board of Secondary Education &bull; session: {{ $exam->academicSession?->name }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-900 text-white rounded text-xs font-black tracking-wider uppercase">
                    Official Timetable
                </span>
                <p class="text-xs text-slate-600 font-bold mt-2">{{ $exam->name }}</p>
                <p class="text-xs text-slate-500">{{ $exam->start_date->format('M d, Y') }} &ndash; {{ $exam->end_date->format('M d, Y') }}</p>
            </div>
        </div>

        @if($selectedClass)
            <div class="mb-4 bg-slate-50 p-3 rounded-lg border border-slate-200 flex items-center justify-between text-xs">
                <span>Class Filtered: <strong class="text-slate-800 text-sm">{{ $selectedClass->name }}</strong></span>
                <span class="text-slate-500">Term: {{ $exam->term }}</span>
            </div>
        @endif

        <!-- Examination Schedule Table -->
        <div class="mb-8">
            <table class="w-full text-left text-xs border border-slate-300 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 uppercase font-bold text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="px-4 py-3 border-r border-slate-300">Date & Day</th>
                        <th class="px-4 py-3 border-r border-slate-300">Class</th>
                        <th class="px-4 py-3 border-r border-slate-300">Subject Code</th>
                        <th class="px-4 py-3 border-r border-slate-300">Subject Name</th>
                        <th class="px-4 py-3 border-r border-slate-300">Exam Timing</th>
                        <th class="px-4 py-3 border-r border-slate-300">Room</th>
                        <th class="px-4 py-3 text-right">Max / Pass</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($schedules as $sched)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-900 border-r border-slate-200">
                                {{ $sched->exam_date->format('M d, Y') }}<br>
                                <span class="text-[10px] text-slate-500 font-normal uppercase">{{ $sched->exam_date->format('l') }}</span>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-800 border-r border-slate-200">
                                {{ $sched->schoolClass?->name }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-600 border-r border-slate-200 font-semibold">
                                {{ $sched->subject?->code }}
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 border-r border-slate-200 text-sm">
                                {{ $sched->subject?->name }}
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-700 border-r border-slate-200">
                                {{ date('g:i A', strtotime($sched->start_time)) }} &ndash; {{ date('g:i A', strtotime($sched->end_time)) }}
                            </td>
                            <td class="px-4 py-3 border-r border-slate-200">
                                {{ $sched->room_no ?: 'Exam Hall' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold">
                                {{ $sched->max_marks }} / {{ $sched->pass_marks }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                No exam schedule items found for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Instructions for Students -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-8 text-xs text-slate-600 space-y-1.5">
            <h4 class="font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                <span>Important Instructions for Examinees</span>
            </h4>
            <p>1. Candidates must arrive at the examination hall at least <strong>15 minutes</strong> before the scheduled commencement of the paper.</p>
            <p>2. School Admit Card and Uniform are strictly mandatory. Entry will not be permitted without authorization.</p>
            <p>3. Electronic devices, smartwatches, and programmable calculators are strictly prohibited inside the hall.</p>
            <p>4. Reading time of 10 minutes will be given prior to writing the examination.</p>
        </div>

        <!-- Official Signatures -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-12 text-xs">
            <div class="text-center">
                <div class="w-48 mx-auto border-b border-slate-400 pb-1 mb-1 font-bold text-slate-800">
                    Dr. Margaret Sterling
                </div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider">Controller of Examinations</span>
            </div>

            <div class="text-center">
                <div class="w-48 mx-auto border-b border-slate-400 pb-1 mb-1 font-bold text-slate-800">
                    Principal & Academic Director
                </div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider">EduPulse Academy</span>
            </div>
        </div>

    </div>

</body>
</html>

