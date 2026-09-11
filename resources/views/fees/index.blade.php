<x-layouts.app title="Fee Invoices & Payments">
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center space-x-3">
                <span>Fee Management & Invoices</span>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('fees.structures') }}" class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Fee Structures</span>
                </a>
                <a href="{{ route('fees.invoices.create') }}" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow transition flex items-center space-x-2">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Generate Invoices</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Financial KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-500">Total Invoiced</span>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $currencySymbol }}{{ number_format($stats['total_invoiced'], 2) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-500">Total Collected</span>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $currencySymbol }}{{ number_format($stats['total_collected'], 2) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase text-slate-500">Pending Receivables</span>
                <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $currencySymbol }}{{ number_format($stats['total_pending'], 2) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
        <form method="GET" action="{{ route('fees.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Statuses</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Class</label>
                <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Search Keyword</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice No, Student Name, Adm No..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-700">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-lg text-xs transition shadow flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('fees.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-medium transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Invoice #</th>
                        <th class="px-6 py-3.5">Student</th>
                        <th class="px-6 py-3.5">Class</th>
                        <th class="px-6 py-3.5">Title / Period</th>
                        <th class="px-6 py-3.5">Total Due</th>
                        <th class="px-6 py-3.5">Paid</th>
                        <th class="px-6 py-3.5">Balance</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800">
                                {{ $inv->invoice_no }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('students.show', $inv->student_id) }}" class="font-bold text-slate-800 hover:text-teal-600 transition">
                                    {{ $inv->student?->full_name }}
                                </a>
                                <span class="block text-xs text-slate-400 font-mono">{{ $inv->student?->admission_no }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                {{ $inv->student?->schoolClass?->name }} ({{ $inv->student?->section?->name }})
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $inv->title }}
                                <span class="block text-[11px] text-slate-400">Due: {{ $inv->due_date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-900 text-sm">
                                {{ $currencySymbol }}{{ number_format($inv->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 text-sm">
                                {{ $currencySymbol }}{{ number_format($inv->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-rose-500 text-sm">
                                {{ $currencySymbol }}{{ number_format($inv->due_balance, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase 
                                    {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ str_replace('_', ' ', $inv->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($inv->status !== 'paid')
                                    <a href="{{ route('fees.invoices.collect', $inv->id) }}" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-sm transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-credit-card text-[10px]"></i>
                                        <span>Collect</span>
                                    </a>
                                @endif

                                @if($inv->payments->count() > 0)
                                    <a href="{{ route('fees.receipt', $inv->payments->last()->receipt_no) }}" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Receipt</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No fee invoices found matching the current criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

