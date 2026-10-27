<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingService
{
    /**
     * Create an invoice for patient services.
     *
     * @param  array<array<string, mixed>>  $items
     */
    public function createInvoice(
        Clinic $clinic,
        Patient $patient,
        array $items,
        ?Appointment $appointment = null,
        float $discount = 0.0,
        float $tax = 0.0,
        ?Carbon $dueDate = null,
        ?string $notes = null
    ): Invoice {
        return DB::transaction(function () use (
            $clinic,
            $patient,
            $items,
            $appointment,
            $discount,
            $tax,
            $dueDate,
            $notes
        ) {
            $subtotal = 0.0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['unit_price'] ?? 0);
                $subtotal += ($qty * $price);
            }

            $total = max(0, $subtotal - $discount + $tax);
            $invoiceNumber = 'INV-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

            $invoice = Invoice::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'appointment_id' => $appointment?->id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'paid_amount' => 0.0,
                'currency' => $clinic->currency ?? 'BDT',
                'status' => InvoiceStatus::Unpaid,
                'due_date' => $dueDate ?? now()->addDays(7),
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['unit_price'] ?? 0);
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'] ?? 'Consultation/Medical Service',
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total_price' => $qty * $price,
                ]);
            }

            AuditService::log('invoice.created', $invoice, null, [
                'patient_id' => $patient->id,
                'total_amount' => $total,
            ], $clinic->id);

            return $invoice->load('items');
        });
    }

    /**
     * Record a payment against an invoice.
     */
    public function recordPayment(
        Invoice $invoice,
        float $amount,
        string $method,
        User $recordedBy,
        ?string $reference = null
    ): Payment {
        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payment amount must be greater than zero.',
            ]);
        }

        $due = $invoice->dueAmount();
        if ($amount > ($due + 0.01)) { // minor floating point tolerance
            throw ValidationException::withMessages([
                'amount' => "Payment amount ({$amount}) exceeds remaining due ({$due}).",
            ]);
        }

        return DB::transaction(function () use ($invoice, $amount, $method, $recordedBy, $reference) {
            $paymentNumber = 'PAY-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));

            $payment = Payment::create([
                'clinic_id' => $invoice->clinic_id,
                'invoice_id' => $invoice->id,
                'payment_number' => $paymentNumber,
                'amount' => $amount,
                'currency' => $invoice->currency,
                'method' => $method,
                'reference' => $reference,
                'status' => 'completed',
                'recorded_by_user_id' => $recordedBy->id,
                'paid_at' => now(),
            ]);

            $newPaidAmount = round((float) $invoice->paid_amount + $amount, 2);
            $newStatus = ($newPaidAmount >= (float) $invoice->total_amount)
                ? InvoiceStatus::Paid
                : InvoiceStatus::PartiallyPaid;

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $newStatus,
            ]);

            AuditService::log('payment.recorded', $payment, null, [
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'method' => $method,
            ], $invoice->clinic_id);

            return $payment;
        });
    }
}
