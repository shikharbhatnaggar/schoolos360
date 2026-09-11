<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FeeHead;
use App\Models\FeeInvoiceItem;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFeeInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $currentSession = AcademicSession::current();
        $status = $request->query('status');
        $classId = $request->query('class_id');
        $search = $request->query('search');

        $query = StudentFeeInvoice::with(['student.schoolClass', 'student.section', 'payments'])
            ->latest('id');

        if ($request->filled('session_id')) {
            $query->where('academic_session_id', $request->session_id);
        } elseif ($currentSession) {
            $query->where('academic_session_id', $currentSession->id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%");
            })->orWhere('invoice_no', 'like', "%{$search}%");
        }

        $invoices = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::orderBy('numeric_level')->get();
        $sessions = AcademicSession::orderByDesc('start_date')->get();

        $stats = [
            'total_invoiced' => StudentFeeInvoice::sum('total_amount'),
            'total_collected' => StudentFeeInvoice::sum('paid_amount'),
            'total_pending' => max(0, StudentFeeInvoice::sum('total_amount') - StudentFeeInvoice::sum('paid_amount')),
        ];

        return view('fees.index', compact('invoices', 'classes', 'sessions', 'currentSession', 'stats', 'status', 'classId', 'search'));
    }

    public function structures(Request $request)
    {
        $currentSession = AcademicSession::current();
        $sessionId = $request->query('session_id', $currentSession ? $currentSession->id : null);

        $sessions = AcademicSession::orderByDesc('start_date')->get();
        $classes = SchoolClass::orderBy('numeric_level')->get();
        $feeHeads = FeeHead::all();

        $structures = FeeStructure::where('academic_session_id', $sessionId)
            ->get()
            ->groupBy('school_class_id');

        return view('fees.structures', compact('sessions', 'classes', 'feeHeads', 'structures', 'sessionId', 'currentSession'));
    }

    public function storeHead(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:fee_heads,name',
            'description' => 'nullable|string',
        ]);

        FeeHead::create($validated);
        return back()->with('success', 'Fee head created successfully.');
    }

    public function saveStructure(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'rates' => 'required|array', // [fee_head_id => amount]
            'frequency' => 'required|string|in:monthly,term,annual,one_time',
        ]);

        foreach ($validated['rates'] as $feeHeadId => $amount) {
            if ($amount !== null && $amount !== '') {
                FeeStructure::updateOrCreate(
                    [
                        'academic_session_id' => $validated['academic_session_id'],
                        'school_class_id' => $validated['school_class_id'],
                        'fee_head_id' => $feeHeadId,
                    ],
                    [
                        'amount' => (float)$amount,
                        'frequency' => $validated['frequency'],
                    ]
                );
            }
        }

        return back()->with('success', 'Class fee structure saved successfully.');
    }

    public function createInvoice()
    {
        $currentSession = AcademicSession::current();
        $sessions = AcademicSession::orderByDesc('start_date')->get();
        $classes = SchoolClass::with('sections')->orderBy('numeric_level')->get();
        $feeHeads = FeeHead::all();

        return view('fees.create_invoice', compact('currentSession', 'sessions', 'classes', 'feeHeads'));
    }

    public function generateInvoices(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'school_class_id' => 'required|exists:school_classes,id',
            'title' => 'required|string|max:150',
            'due_date' => 'required|date',
            'target_type' => 'required|in:class,individual',
            'student_id' => 'nullable|required_if:target_type,individual|exists:students,id',
        ]);

        // Get fee structures for this class
        $structures = FeeStructure::where('academic_session_id', $validated['academic_session_id'])
            ->where('school_class_id', $validated['school_class_id'])
            ->get();

        if ($structures->isEmpty()) {
            return back()->with('error', 'No fee structure found for this class in selected session. Please define fee structure first.')->withInput();
        }

        $totalFee = $structures->sum('amount');

        DB::beginTransaction();
        try {
            if ($validated['target_type'] === 'individual') {
                $students = Student::where('id', $validated['student_id'])->get();
            } else {
                $students = Student::where('school_class_id', $validated['school_class_id'])
                    ->where('status', 'active')
                    ->get();
            }

            if ($students->isEmpty()) {
                return back()->with('error', 'No active students found in this class.');
            }

            $count = 0;
            foreach ($students as $student) {
                $invoiceNo = 'INV-' . date('Y') . '-' . strtoupper(uniqid());

                $invoice = StudentFeeInvoice::create([
                    'invoice_no' => $invoiceNo,
                    'student_id' => $student->id,
                    'academic_session_id' => $validated['academic_session_id'],
                    'school_class_id' => $validated['school_class_id'],
                    'title' => $validated['title'],
                    'due_date' => $validated['due_date'],
                    'total_amount' => $totalFee,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                ]);

                foreach ($structures as $item) {
                    FeeInvoiceItem::create([
                        'student_fee_invoice_id' => $invoice->id,
                        'fee_head_id' => $item->fee_head_id,
                        'amount' => $item->amount,
                    ]);
                }
                $count++;
            }

            DB::commit();
            return redirect()->route('fees.index')->with('success', "Successfully generated {$count} fee invoices for {$validated['title']}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate invoices: ' . $e->getMessage());
        }
    }

    public function collectForm(StudentFeeInvoice $invoice)
    {
        $invoice->load(['student.schoolClass', 'student.section', 'items.feeHead', 'payments']);
        return view('fees.collect', compact('invoice'));
    }

    public function recordPayment(Request $request, StudentFeeInvoice $invoice)
    {
        $due = $invoice->due_balance;

        $validated = $request->validate([
            'amount_paid' => "required|numeric|min:1|max:{$due}",
            'payment_method' => 'required|in:cash,online,cheque,bank_transfer',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $branch = \App\Services\TenantContext::getBranch();
        $receiptNo = $branch 
            ? $branch->generateReceiptNumber() 
            : ('REC-' . date('Ymd') . '-' . str_pad((FeePayment::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT));

        DB::beginTransaction();
        try {
            $payment = FeePayment::create([
                'school_id' => \App\Services\TenantContext::getSchool()?->id,
                'branch_id' => $branch?->id,
                'receipt_no' => $receiptNo,
                'student_fee_invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount_paid' => $validated['amount_paid'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'],
                'received_by_id' => Auth::id(),
            ]);

            $newPaid = $invoice->paid_amount + $validated['amount_paid'];
            $newStatus = ($newPaid >= $invoice->total_amount) ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $newStatus,
            ]);

            DB::commit();

            return redirect()->route('fees.receipt', $payment->receipt_no)->with('success', 'Fee payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function receipt(string $receiptNo)
    {
        $payment = FeePayment::with([
            'invoice.items.feeHead',
            'student.schoolClass',
            'student.section',
            'student.house',
            'student.academicSession',
            'receivedBy'
        ])->where('receipt_no', $receiptNo)->firstOrFail();

        return view('fees.receipt', compact('payment'));
    }
}

