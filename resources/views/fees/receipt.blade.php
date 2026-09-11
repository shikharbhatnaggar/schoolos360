<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $payment->receipt_no }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .receipt-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4 flex flex-col items-center">

    <!-- Top Action Bar (No-Print) -->
    <div class="no-print max-w-3xl w-full mb-6 flex items-center justify-between">
        <a href="{{ route('fees.index') }}" class="inline-flex items-center space-x-2 text-slate-600 hover:text-slate-900 text-sm font-semibold transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Fee Invoices</span>
        </a>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-sm shadow transition flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print / Save as PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Fee Receipt Card -->
    <div class="receipt-card max-w-3xl w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12 relative text-slate-800">
        
        <!-- School Letterhead -->
        <div class="flex items-center justify-between border-b-2 border-slate-800 pb-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-slate-900 text-teal-400 rounded-2xl flex items-center justify-center text-3xl shadow">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900">EDUPULSE ACADEMY</h1>
                    <p class="text-xs text-slate-500 font-medium">100 Knowledge Boulevard, Education City &bull; Phone: (555) 019-2834</p>
                    <p class="text-xs text-slate-400">Affiliation No: EP-2026/09 &bull; Email: accounts@edupulse.edu</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-100 border border-slate-300 rounded text-xs font-black tracking-wider uppercase text-slate-800">
                    Official Fee Receipt
                </span>
                <p class="text-xs text-slate-500 font-mono mt-2">Receipt No: <strong class="text-slate-900">{{ $payment->receipt_no }}</strong></p>
                <p class="text-xs text-slate-500">Date: <strong class="text-slate-900">{{ $payment->payment_date->format('M d, Y') }}</strong></p>
            </div>
        </div>

        <!-- Student & Payment Meta Information -->
        <div class="grid grid-cols-2 gap-6 bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-xs">
            <div class="space-y-1.5">
                <div><span class="text-slate-500">Student Name:</span> <strong class="text-slate-900 font-bold ml-1 text-sm">{{ $payment->student?->full_name }}</strong></div>
                <div><span class="text-slate-500">Admission No:</span> <strong class="text-slate-800 font-mono ml-1">{{ $payment->student?->admission_no }}</strong></div>
                <div><span class="text-slate-500">Class & Section:</span> <strong class="text-slate-800 ml-1">{{ $payment->student?->schoolClass?->name }} ({{ $payment->student?->section?->name }})</strong></div>
                <div><span class="text-slate-500">House:</span> <strong class="text-slate-800 ml-1">{{ $payment->student?->house?->name ?: 'N/A' }}</strong></div>
            </div>
            <div class="space-y-1.5">
                <div><span class="text-slate-500">Parent / Guardian:</span> <strong class="text-slate-800 ml-1">{{ $payment->student?->parent_name }}</strong></div>
                <div><span class="text-slate-500">Invoice Ref:</span> <strong class="text-slate-800 font-mono ml-1">{{ $payment->invoice?->invoice_no }}</strong></div>
                <div><span class="text-slate-500">Payment Mode:</span> <strong class="text-slate-800 uppercase ml-1">{{ $payment->payment_method }}</strong></div>
                @if($payment->transaction_reference)
                    <div><span class="text-slate-500">Transaction ID:</span> <strong class="text-slate-800 font-mono ml-1">{{ $payment->transaction_reference }}</strong></div>
                @endif
            </div>
        </div>

        <!-- Itemized Fee Structure Breakdown -->
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Itemized Fee Description</h3>
            <table class="w-full text-left text-xs text-slate-700 border border-slate-200 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 uppercase font-semibold text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5">#</th>
                        <th class="px-4 py-2.5">Fee Head</th>
                        <th class="px-4 py-2.5 text-right">Invoiced Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $idx = 1; @endphp
                    @forelse($payment->invoice?->items as $item)
                        <tr>
                            <td class="px-4 py-2 font-mono text-slate-400">{{ $idx++ }}</td>
                            <td class="px-4 py-2 font-medium">{{ $item->feeHead?->name }}</td>
                            <td class="px-4 py-2 text-right font-mono font-semibold">{{ $currencySymbol }}{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-2 font-mono text-slate-400">1</td>
                            <td class="px-4 py-2 font-medium">{{ $payment->invoice?->title }}</td>
                            <td class="px-4 py-2 text-right font-mono font-semibold">{{ $currencySymbol }}{{ number_format($payment->invoice?->total_amount, 2) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Total Calculation Matrix -->
        <div class="flex justify-end mb-10">
            <div class="w-72 space-y-2 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Total Invoice Amount:</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $currencySymbol }}{{ number_format($payment->invoice?->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Previously Paid:</span>
                    <span class="font-semibold text-slate-600 font-mono">{{ $currencySymbol }}{{ number_format($payment->invoice?->paid_amount - $payment->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b-2 border-slate-800 text-sm">
                    <span class="font-bold text-slate-900">Current Amount Paid:</span>
                    <span class="font-black text-emerald-700 font-mono">{{ $currencySymbol }}{{ number_format($payment->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between py-1 text-slate-700">
                    <span class="font-semibold">Remaining Due Balance:</span>
                    <span class="font-bold font-mono text-rose-600">{{ $currencySymbol }}{{ number_format($payment->invoice?->due_balance, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures & Disclaimer Footer -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs">
            <div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    * This is a computer-generated fee receipt of EduPulse Academy. Fees once paid are non-refundable as per institutional policy.
                </p>
                <p class="text-[11px] text-slate-500 mt-2">
                    Received By: <strong class="text-slate-700">{{ $payment->receivedBy?->name ?? 'Accounts Desk' }}</strong>
                </p>
            </div>
            <div class="text-center flex flex-col justify-end items-center">
                <div class="w-48 border-b border-slate-400 pb-1 mb-1 font-bold text-slate-800">
                    Authorized Signatory
                </div>
                <span class="text-[10px] text-slate-400 uppercase tracking-wider">EduPulse Academy Accounts Bureau</span>
            </div>
        </div>

    </div>

</body>
</html>

