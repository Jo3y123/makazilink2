<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('tenant.user', 'unit.property', 'invoice', 'recordedBy')
            ->latest()
            ->get();

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $invoices = Invoice::with('tenant.user', 'unit.property')
            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
            ->get();

        $tenants = Tenant::with('user')->get();

        return view('payments.create', compact('invoices', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id'           => 'nullable|exists:invoices,id',
            'tenant_id'            => 'required|exists:tenants,id',
            'unit_id'              => 'required|exists:units,id',
            'amount'               => 'required|numeric|min:1',
            'payment_method'       => 'required|in:mpesa,cash,bank_transfer,cheque',
            'payment_date'         => 'required|date',
            'reference_number'     => 'nullable|string|max:255',
            'mpesa_transaction_id' => 'nullable|string|max:255',
        ]);

        $payment = Payment::create([
            'receipt_number'       => Payment::generateReceiptNumber(),
            'invoice_id'           => $request->invoice_id,
            'tenant_id'            => $request->tenant_id,
            'unit_id'              => $request->unit_id,
            'amount'               => $request->amount,
            'payment_method'       => $request->payment_method,
            'payment_date'         => $request->payment_date,
            'reference_number'     => $request->reference_number,
            'mpesa_transaction_id' => $request->mpesa_transaction_id,
            'notes'                => $request->notes,
            'status'               => 'confirmed',
            'recorded_by'          => auth()->id(),
        ]);

        // Update invoice if linked
        if ($request->invoice_id) {
            $invoice = Invoice::find($request->invoice_id);
            $invoice->amount_paid += $request->amount;
            $invoice->balance      = $invoice->total_amount - $invoice->amount_paid;

            if ($invoice->balance <= 0) {
                $invoice->status = 'paid';
            } elseif ($invoice->amount_paid > 0) {
                $invoice->status = 'partial';
            }

            $invoice->save();
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded. Receipt: ' . $payment->receipt_number);
    }

    public function show(Payment $payment)
    {
        $payment->load('tenant.user', 'unit.property', 'invoice', 'recordedBy');
        return view('payments.show', compact('payment'));
    }

    public function pdf(Payment $payment)
    {
        $payment->load('tenant.user', 'unit.property', 'invoice', 'recordedBy');
        $pdf = Pdf::loadView('payments.pdf', compact('payment'));
        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }

    public function whatsapp(Payment $payment)
    {
    $payment->load('tenant.user', 'unit.property', 'invoice');

    $companyName  = \App\Models\Setting::get('company_name', 'MakaziLink v2');
    $companyPhone = \App\Models\Setting::get('company_phone', '');
    $currency     = \App\Models\Setting::get('currency', 'KES');

    $message = "Dear " . $payment->tenant->user->name . ",\n\n"
        . "Payment received for " . $payment->unit->property->name . " — Unit " . $payment->unit->unit_number . ".\n\n"
        . "Receipt No: " . $payment->receipt_number . "\n"
        . "Amount: " . $currency . " " . number_format($payment->amount) . "\n"
        . "Method: " . ucfirst(str_replace('_', ' ', $payment->payment_method)) . "\n"
        . "Date: " . $payment->payment_date->format('d M Y') . "\n\n"
        . "Thank you for your payment.\n\n"
        . $companyName
        . ($companyPhone ? "\n📞 " . $companyPhone : '');

        $phone = preg_replace('/[^0-9]/', '', $payment->tenant->user->phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }

        $payment->update([
            'whatsapp_sent'    => true,
            'whatsapp_sent_at' => now(),
        ]);

        $url = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        return redirect($url);
    }

    public function bulkWhatsapp()
    {
    $invoices = Invoice::with('tenant.user', 'unit.property')
        ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
        ->where('balance', '>', 0)
        ->get();

    if ($invoices->isEmpty()) {
        return redirect()->route('payments.index')
            ->with('success', 'No unpaid invoices found.');
    }

    $companyName  = \App\Models\Setting::get('company_name', 'MakaziLink v2');
    $companyPhone = \App\Models\Setting::get('company_phone', '');
    $currency     = \App\Models\Setting::get('currency', 'KES');

    $messages = [];

    foreach ($invoices as $invoice) {
        $phone = preg_replace('/[^0-9]/', '', $invoice->tenant->user->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }

        $message = "Dear " . $invoice->tenant->user->name . ",\n\n"
            . "This is a payment reminder for your rent.\n\n"
            . "Invoice: " . $invoice->invoice_number . "\n"
            . "Unit: " . $invoice->unit->unit_number . " — " . $invoice->unit->property->name . "\n"
            . "Amount Due: " . $currency . " " . number_format($invoice->balance) . "\n"
            . "Due Date: " . $invoice->due_date->format('d M Y') . "\n\n"
            . "Please make payment at your earliest convenience.\n\n"
            . $companyName
            . ($companyPhone ? "\n📞 " . $companyPhone : '');

        $messages[] = [
            'name'    => $invoice->tenant->user->name,
            'phone'   => $phone,
            'balance' => $invoice->balance,
            'url'     => 'https://wa.me/' . $phone . '?text=' . urlencode($message),
        ];
    }

    return view('payments.bulk-whatsapp', compact('messages'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted.');
    }
}