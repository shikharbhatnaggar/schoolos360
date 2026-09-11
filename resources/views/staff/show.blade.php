<x-layouts.app title="{{ $staff->full_name }} - Staff Profile">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('staff.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Staff Profile</span>
        </div>
    </x-slot>

    <!-- Top Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl uppercase shadow">
                    {{ substr($staff->first_name, 0, 1) }}{{ substr($staff->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h2 class="text-2xl font-extrabold text-slate-800">{{ $staff->full_name }}</h2>
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold uppercase bg-indigo-100 text-indigo-800">
                            {{ str_replace('_', ' ', $staff->role_type) }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 mt-0.5">{{ $staff->designation }} &bull; {{ $staff->employee_id }}</p>
                </div>
            </div>

            @if($staff->house)
                <div class="px-3 py-2 rounded-lg border text-xs font-bold flex items-center space-x-2" style="background-color: {{ $staff->house->color }}15; border-color: {{ $staff->house->color }}40; color: {{ $staff->house->color }};">
                    <i class="fa-solid fa-flag"></i>
                    <span>House Master: {{ $staff->house->name }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Assigned Roles & Classes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Class Teacher Assignments -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-chalkboard text-teal-600"></i>
                <span>Class Teacher Assignments</span>
            </h4>
            <div class="space-y-2">
                @forelse($staff->classTeachers as $ct)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-800">{{ $ct->schoolClass?->name }} ({{ $ct->section?->name }})</span>
                        <span class="text-slate-500 font-medium">Session: {{ $ct->academicSession?->name }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">No class teacher roles assigned for current session.</p>
                @endforelse
            </div>
        </div>

        <!-- Subject Teacher Assignments -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-book-open text-indigo-600"></i>
                <span>Subject Teaching Duties</span>
            </h4>
            <div class="space-y-2">
                @forelse($staff->subjectTeachers as $st)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $st->subject?->name }} ({{ $st->subject?->code }})</span>
                            <span class="text-slate-500">{{ $st->schoolClass?->name }} - {{ $st->section?->name }}</span>
                        </div>
                        <span class="text-slate-400 font-mono">{{ $st->academicSession?->name }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">No subject teacher roles assigned.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>

