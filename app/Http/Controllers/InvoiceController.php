<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
    $activeTenants = Tenant::with('user', 'activeLease.unit.property')
        ->whereHas('activeLease')
        ->latest()
        ->get();

    $vacatedTenants = Tenant::with('user', 'activeLease.unit.property')
        ->whereDoesntHave('activeLease')
        ->latest()
        ->get();

    return view('tenants.index', compact('activeTenants', 'vacatedTenants'));
    }

    public function create()
    {
        $leases = Lease::with('tenant.user', 'unit.property')
            ->where('status', 'active')
            ->get();

        $currency = \App\Models\Setting::get('currency', 'KES');

        return view('invoices.create', compact('leases', 'currency'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id'       => 'required|exists:leases,id',
            'rent_amount'    => 'required|numeric|min:0',
            'water_amount'   => 'nullable|numeric|min:0',
            'garbage_amount' => 'nullable|numeric|min:0',
            'other_amount'   => 'nullable|numeric|min:0',
            'due_date'       => 'required|date',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after:period_start',
        ]);

        $lease = Lease::find($request->lease_id);

        $rent    = $request->rent_amount;
        $water   = $request->water_amount   ?? 0;
        $garbage = $request->garbage_amount ?? 0;
        $other   = $request->other_amount   ?? 0;
        $total   = $rent + $water + $garbage + $other;

        $unit    = $lease->unit;
                $water   = $unit->water_charge   ?? 0;
                $garbage = $unit->garbage_charge  ?? 0;
                $service = $unit->service_charge  ?? 0;

                // Check for latest metered water reading this period
                $waterReading = \App\Models\WaterReading::where('unit_id', $unit->id)
                    ->whereMonth('reading_date', now()->month)
                    ->whereYear('reading_date', now()->year)
                    ->latest()
                    ->first();

                if ($waterReading && $waterReading->amount_charged > 0) {
                    $water = $waterReading->amount_charged;
                }

                $rent  = $lease->monthly_rent;
                $total = $rent + $water + $garbage + $service;

                // Add late penalty if tenant has overdue balance
                $latePenalty = 0;
                $penalty     = (float) \App\Models\Setting::get('late_payment_penalty', 0);
                if ($penalty > 0) {
                    $hasOverdue = \App\Models\Invoice::where('tenant_id', $lease->tenant_id)
                        ->where('status', 'overdue')
                        ->exists();
                    if ($hasOverdue) {
                        $latePenalty = $penalty;
                        $total      += $latePenalty;
                    }
                }

                Invoice::create([
                    'invoice_number' => Invoice::generateNumber(),
                    'lease_id'       => $lease->id,
                    'tenant_id'      => $lease->tenant_id,
                    'unit_id'        => $lease->unit_id,
                    'rent_amount'    => $rent,
                    'water_amount'   => $water,
                    'garbage_amount' => $garbage,
                    'other_amount'   => $service + $latePenalty,
                    'total_amount'   => $total,
                    'amount_paid'    => 0,
                    'balance'        => $total,
                    'due_date'       => $request->due_date,
                    'period_start'   => $request->period_start,
                    'period_end'     => $request->period_end,
                    'status'         => 'draft',
                    'notes'          => \App\Models\Setting::get('invoice_notes', ''),
                ]);

        // Send SMS notification if enabled
        if (Setting::get('sms_on_invoice', '0') === '1') {
            $tenant = $lease->tenant;
            $phone  = $tenant->user->phone ?? '';
            $name   = $tenant->user->name ?? '';
            if ($phone) {
                $sms = new \App\Services\SmsService();
                $sms->sendInvoiceNotification(
                    $phone,
                    $name,
                    Invoice::generateNumber(),
                    $total,
                    \Carbon\Carbon::parse($request->due_date)->format('d M Y')
                );
            }
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('tenant.user', 'unit.property', 'lease', 'payments');
        return view('invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('tenant.user', 'unit.property', 'lease', 'payments');
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after:period_start',
            'due_date'     => 'required|date',
        ]);

        $leases  = Lease::with('tenant', 'unit')->where('status', 'active')->get();
        $created = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($leases as $lease) {
            $exists = Invoice::where('lease_id', $lease->id)
                ->where('period_start', $request->period_start)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            try {
                Invoice::create([
                    'invoice_number' => Invoice::generateNumber(),
                    'lease_id'       => $lease->id,
                    'tenant_id'      => $lease->tenant_id,
                    'unit_id'        => $lease->unit_id,
                    'rent_amount'    => $lease->monthly_rent,
                    'water_amount'   => 0,
                    'garbage_amount' => 0,
                    'other_amount'   => 0,
                    'total_amount'   => $lease->monthly_rent,
                    'amount_paid'    => 0,
                    'balance'        => $lease->monthly_rent,
                    'due_date'       => $request->due_date,
                    'period_start'   => $request->period_start,
                    'period_end'     => $request->period_end,
                    'status'         => 'draft',
                    'notes'          => \App\Models\Setting::get('invoice_notes', ''),
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = 'Lease #' . $lease->id . ': ' . $e->getMessage();
            }
        }

        $message = "Bulk generation complete. Created: {$created}, Skipped (already exist): {$skipped}.";

        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }

        return redirect()->route('invoices.index')->with('success', $message);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted.');
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'exists:invoices,id',
        ]);

        $count = Invoice::whereIn('id', $request->ids)->delete();

        return redirect()->route('invoices.index')
            ->with('success', "{$count} invoice(s) deleted successfully.");
    }

    public function edit(Invoice $invoice)
{
    $invoice->load('tenant.user', 'unit.property', 'lease');
    return view('invoices.edit', compact('invoice'));
}

public function update(Request $request, Invoice $invoice)
{
    $request->validate([
        'rent_amount'    => 'required|numeric|min:0',
        'water_amount'   => 'nullable|numeric|min:0',
        'garbage_amount' => 'nullable|numeric|min:0',
        'other_amount'   => 'nullable|numeric|min:0',
        'due_date'       => 'required|date',
        'period_start'   => 'required|date',
        'period_end'     => 'required|date|after:period_start',
        'notes'          => 'nullable|string|max:500',
    ]);

    $rent    = (float) $request->rent_amount;
    $water   = (float) ($request->water_amount   ?? 0);
    $garbage = (float) ($request->garbage_amount ?? 0);
    $other   = (float) ($request->other_amount   ?? 0);
    $total   = $rent + $water + $garbage + $other;
    $balance = $total - (float) $invoice->amount_paid;

    $invoice->update([
        'rent_amount'    => $rent,
        'water_amount'   => $water,
        'garbage_amount' => $garbage,
        'other_amount'   => $other,
        'total_amount'   => $total,
        'balance'        => $balance,
        'due_date'       => $request->due_date,
        'period_start'   => $request->period_start,
        'period_end'     => $request->period_end,
        'notes'          => $request->notes,
        'status'         => $balance <= 0 ? 'paid' : $invoice->status,
    ]);

    return redirect()->route('invoices.show', $invoice)
        ->with('success', 'Invoice updated successfully.');
    }
}