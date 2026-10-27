<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Service;
use App\Services\BillingService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $billingService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->input('status');

        $invoices = Invoice::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with(['patient', 'appointment'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalBilled = Invoice::sum('total_amount');
        $totalCollected = Invoice::sum('paid_amount');
        $totalOutstanding = max(0, $totalBilled - $totalCollected);

        return view('billing.index', compact('invoices', 'status', 'totalBilled', 'totalCollected', 'totalOutstanding'));
    }

    public function create(Request $request): View
    {
        $patients = Patient::orderBy('first_name')->get();
        $services = Service::where('is_active', true)->get();
        $preselectedPatientId = $request->input('patient_id');

        return view('billing.create', compact('patients', 'services', 'preselectedPatientId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $clinic = TenantContext::getClinic();

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'due_date' => ['nullable', 'date'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.service_id' => ['nullable', 'exists:services,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        $dueDate = ! empty($validated['due_date']) ? Carbon::parse($validated['due_date']) : null;

        $invoice = $this->billingService->createInvoice(
            clinic: $clinic,
            patient: $patient,
            items: $validated['items'],
            discount: (float) ($validated['discount_amount'] ?? 0),
            tax: (float) ($validated['tax_amount'] ?? 0),
            dueDate: $dueDate,
            notes: $validated['notes'] ?? null
        );

        return redirect()->route('billing.show', $invoice)
            ->with('success', "Invoice {$invoice->invoice_number} created successfully.");
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['patient', 'appointment.doctor', 'items.service', 'payments.recordedBy']);

        return view('billing.show', compact('invoice'));
    }

    public function recordPayment(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'string'],
            'reference' => ['nullable', 'string', 'max:100'],
        ]);

        $payment = $this->billingService->recordPayment(
            invoice: $invoice,
            amount: (float) $validated['amount'],
            method: $validated['method'],
            recordedBy: Auth::user(),
            reference: $validated['reference'] ?? null
        );

        return back()->with('success', "Payment {$payment->payment_number} of BDT {$payment->amount} recorded.");
    }
}
