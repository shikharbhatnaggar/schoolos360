<x-layouts.app title="Create Examination">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('exams.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Schedule New Examination</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <div class="mb-6 pb-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Examination Configuration</h3>
            <p class="text-xs text-slate-500">Define academic term examination details</p>
        </div>

        <form action="{{ route('exams.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Academic Session *</label>
                <select name="academic_session_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                    @foreach($sessions as $sess)
                        <option value="{{ $sess->id }}" {{ $currentSession?->id == $sess->id ? 'selected' : '' }}>
                            {{ $sess->name }} {{ $sess->is_current ? '(Current)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Exam Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Mid-Term Examination 2026" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Term / Type *</label>
                    <input type="text" name="term" value="{{ old('term', 'Term 1') }}" placeholder="e.g. Term 1, Mid-Term, Final" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                        <option value="scheduled" selected>Scheduled</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+14 days'))) }}" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Instructions / Description</label>
                <textarea name="description" rows="3" placeholder="General instructions for examinees and teachers..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-4">
                <a href="{{ route('exams.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Create Exam & Open Timetable</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>

