<x-layouts.app title="Generate Student Fee Invoices">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('fees.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Generate Class Fee Invoices</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 p-8" x-data="{
        targetType: 'class',
        selectedClass: '{{ $classes->first()?->id }}'
    }">
        <div class="mb-6 pb-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Batch Fee Invoicing</h3>
            <p class="text-xs text-slate-500">Generate fee invoices based on the configured class fee structure</p>
        </div>

        <form action="{{ route('fees.invoices.generate') }}" method="POST" class="space-y-6">
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
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Class *</label>
                <select name="school_class_id" x-model="selectedClass" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->students_count ?? 0 }} enrolled)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Target Audience *</label>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="radio" name="target_type" value="class" x-model="targetType" checked class="text-teal-600 focus:ring-teal-500">
                        <span>All Active Students in this Class</span>
                    </label>
                    <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                        <input type="radio" name="target_type" value="individual" x-model="targetType" class="text-teal-600 focus:ring-teal-500">
                        <span>Individual Student Only</span>
                    </label>
                </div>
            </div>

            <div x-show="targetType === 'individual'" class="space-y-1" x-cloak>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Select Student</label>
                <input type="number" name="student_id" placeholder="Enter Student ID" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Invoice Title / Description *</label>
                <input type="text" name="title" value="Term 1 Tuition & Facilities Fee - {{ date('F Y') }}" placeholder="e.g. Term 1 Tuition & Facilities Fee" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Due Date *</label>
                <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
            </div>

            <div class="p-4 bg-teal-50 border border-teal-200 rounded-lg text-xs text-teal-800">
                <i class="fa-solid fa-info-circle mr-1"></i>
                Invoices will be automatically created with all fee heads defined in the Class Fee Structure for this session.
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-4">
                <a href="{{ route('fees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Generate Invoices</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>

