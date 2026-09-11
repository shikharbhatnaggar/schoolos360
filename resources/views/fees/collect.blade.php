<x-layouts.app title="Collect Fee Payment">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('fees.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Collect Fee Payment</span>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Invoice Summary Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 mb-4 gap-2">
                <div>
                    <span class="text-xs font-mono font-bold text-slate-400">INVOICE {{ $invoice->invoice_no }}</span>
                    <h3 class="text-xl font-extrabold text-slate-800">{{ $invoice->title }}</h3>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Student:</span>
                    <p class="text-sm font-bold text-slate-800">{{ $invoice->student?->full_name }} ({{ $invoice->student?->admission_no }})</p>
                    <span class="text-xs text-slate-500">{{ $invoice->student?->schoolClass?->name }} - {{ $invoice->student?->section?->name }}</span>
                </div>
            </div>

            <!-- Financial Details -->
            <div class="grid grid-cols-3 gap-4 text-center p-4 bg-slate-50 rounded-xl">
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase">Total Invoiced</span>
                    <p class="text-lg font-bold text-slate-800 mt-0.5">{{ $currencySymbol }}{{ number_format($invoice->total_amount, 2) }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase">Already Paid</span>
                    <p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $currencySymbol }}{{ number_format($invoice->paid_amount, 2) }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase">Remaining Balance</span>
                    <p class="text-xl font-black text-rose-600 mt-0.5">{{ $currencySymbol }}{{ number_format($invoice->due_balance, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Collection Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h4 class="font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-cash-register text-teal-600"></i>
                <span>Record Fee Transaction</span>
            </h4>

            <form action="{{ route('fees.invoices.pay', $invoice->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Amount to Pay ($) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-bold">{{ $currencySymbol }}</span>
                            <input type="number" step="0.01" min="1" max="{{ $invoice->due_balance }}" name="amount_paid" value="{{ $invoice->due_balance }}" required
                                class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-base font-bold text-slate-900">
                        </div>
                        <span class="text-[11px] text-slate-500 mt-1 block">Maximum payable: {{ $currencySymbol }}{{ number_format($invoice->due_balance, 2) }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm font-medium text-slate-800">
                            <option value="cash">Cash Counter</option>
                            <option value="online">Online / Card</option>
                            <option value="bank_transfer">Bank Transfer / NEFT</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Transaction Reference / Cheque No</label>
                        <input type="text" name="transaction_reference" placeholder="e.g. TXN-8921829 or Cheque #12345"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Remarks / Note</label>
                    <textarea name="notes" rows="2" placeholder="Optional cashier notes or remarks..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-800"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-200 flex items-center justify-end space-x-4">
                    <a href="{{ route('fees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow transition flex items-center space-x-2">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Confirm Payment & Print Receipt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

