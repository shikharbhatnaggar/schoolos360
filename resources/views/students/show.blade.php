<x-layouts.app title="{{ $student->full_name }} - Profile">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('students.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Student Profile</span>
        </div>
    </x-slot>

    <!-- Top Profile Summary Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-xl uppercase shadow-md">
                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h2 class="text-2xl font-extrabold text-slate-800">{{ $student->full_name }}</h2>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $student->status }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1 font-medium">
                        <span>Admission No: <strong class="text-slate-700 font-mono">{{ $student->admission_no }}</strong></span>
                        <span>&bull;</span>
                        <span>Roll No: <strong class="text-slate-700 font-mono">{{ $student->roll_no ?: 'N/A' }}</strong></span>
                        <span>&bull;</span>
                        <span>Class: <strong class="text-slate-700">{{ $student->schoolClass?->name }} ({{ $student->section?->name }})</strong></span>
                        <span>&bull;</span>
                        <span>Session: <strong class="text-slate-700">{{ $student->academicSession?->name }}</strong></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                @if($student->house)
                    <div class="px-3 py-1.5 rounded-lg border text-xs font-bold flex items-center space-x-2" style="background-color: {{ $student->house->color }}15; border-color: {{ $student->house->color }}40; color: {{ $student->house->color }};">
                        <i class="fa-solid fa-flag"></i>
                        <span>{{ $student->house->name }} House</span>
                    </div>
                @endif
                <a href="{{ route('students.edit', $student->id) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold transition border border-indigo-200">
                    <i class="fa-solid fa-pen mr-1"></i> Edit Info
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="space-y-6" x-data="{ tab: 'details' }">
        <div class="flex space-x-2 border-b border-slate-200">
            <button @click="tab = 'details'" :class="tab === 'details' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-address-card"></i>
                <span>General Information</span>
            </button>
            <button @click="tab = 'fees'" :class="tab === 'fees' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Fee Invoices & Receipts</span>
            </button>
            <button @click="tab = 'exams'" :class="tab === 'exams' ? 'border-teal-600 text-teal-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-4 border-b-2 text-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Exams & Marks</span>
            </button>
        </div>

        <!-- 1. Details Tab -->
        <div x-show="tab === 'details'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Personal & Academic Details</h4>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <span class="text-slate-500 font-medium">Gender</span>
                    <span class="text-slate-800 font-bold">{{ $student->gender }}</span>

                    <span class="text-slate-500 font-medium">Date of Birth</span>
                    <span class="text-slate-800 font-bold">{{ $student->dob ? $student->dob->format('M d, Y') : '-' }}</span>

                    <span class="text-slate-500 font-medium">Blood Group</span>
                    <span class="text-slate-800 font-bold">{{ $student->blood_group ?: 'Not Specified' }}</span>

                    <span class="text-slate-500 font-medium">Admission Date</span>
                    <span class="text-slate-800 font-bold">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : '-' }}</span>

                    <span class="text-slate-500 font-medium">Class & Section</span>
                    <span class="text-slate-800 font-bold">{{ $student->schoolClass?->name }} - {{ $student->section?->name }}</span>

                    <span class="text-slate-500 font-medium">House</span>
                    <span class="text-slate-800 font-bold">{{ $student->house?->name ?: 'None' }}</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Parent / Guardian Details</h4>
                <div class="grid grid-cols-2 gap-y-3 text-xs">
                    <span class="text-slate-500 font-medium">Guardian Name</span>
                    <span class="text-slate-800 font-bold">{{ $student->parent_name }}</span>

                    <span class="text-slate-500 font-medium">Contact Phone</span>
                    <span class="text-slate-800 font-bold font-mono">{{ $student->parent_phone }}</span>

                    <span class="text-slate-500 font-medium">Email Address</span>
                    <span class="text-slate-800 font-bold">{{ $student->parent_email ?: 'N/A' }}</span>

                    <span class="text-slate-500 font-medium">Address</span>
                    <span class="text-slate-800 font-bold">{{ $student->address ?: 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Fees Tab -->
        <div x-show="tab === 'fees'" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden" x-cloak>
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h4 class="font-bold text-slate-800">Fee Invoices & Transactions</h4>
                <a href="{{ route('fees.invoices.create') }}" class="text-xs text-teal-600 hover:underline font-semibold">Generate Invoices</a>
            </div>
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Invoice No</th>
                        <th class="px-6 py-3">Title / Period</th>
                        <th class="px-6 py-3">Due Date</th>
                        <th class="px-6 py-3">Total Amount</th>
                        <th class="px-6 py-3">Paid Amount</th>
                        <th class="px-6 py-3">Balance Due</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($student->feeInvoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-800">{{ $inv->invoice_no }}</td>
                            <td class="px-6 py-3.5 font-medium text-slate-800">{{ $inv->title }}</td>
                            <td class="px-6 py-3.5 text-xs text-slate-500">{{ $inv->due_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3.5 font-bold text-slate-900">{{ $currencySymbol }}{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-6 py-3.5 font-bold text-emerald-600">{{ $currencySymbol }}{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="px-6 py-3.5 font-bold text-rose-500">{{ $currencySymbol }}{{ number_format($inv->due_balance, 2) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase 
                                    {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ str_replace('_', ' ', $inv->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right space-x-2">
                                @if($inv->status !== 'paid')
                                    <a href="{{ route('fees.invoices.collect', $inv->id) }}" class="px-3 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded shadow-sm transition">
                                        Pay Fee
                                    </a>
                                @endif
                                @if($inv->payments->count() > 0)
                                    <a href="{{ route('fees.receipt', $inv->payments->last()->receipt_no) }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded transition">
                                        Receipt
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400 text-xs">No fee invoices recorded for this student yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 3. Exams & Marks Tab -->
        <div x-show="tab === 'exams'" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6" x-cloak>
            <h4 class="font-bold text-slate-800 mb-4">Academic Examination Record</h4>
            @if($student->marks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3">Examination</th>
                                <th class="px-4 py-3">Subject</th>
                                <th class="px-4 py-3">Max Marks</th>
                                <th class="px-4 py-3">Pass Marks</th>
                                <th class="px-4 py-3">Marks Obtained</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($student->marks as $mark)
                                @php
                                    $sched = $mark->examSchedule;
                                    $passed = $mark->marks_obtained >= ($sched?->pass_marks ?? 0);
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $sched?->exam?->name }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-700">{{ $sched?->subject?->name }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $sched?->max_marks }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $sched?->pass_marks }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-base {{ $mark->is_absent ? 'text-rose-500' : 'text-slate-800' }}">
                                        {{ $mark->is_absent ? 'ABSENT' : $mark->marks_obtained }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($mark->is_absent)
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded text-xs font-semibold">Absent</span>
                                        @elseif($passed)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs font-semibold">Pass</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded text-xs font-semibold">Fail</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500">{{ $mark->remarks ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-slate-400">No marks registered for this student yet.</p>
            @endif
        </div>
    </div>
</x-layouts.app>

