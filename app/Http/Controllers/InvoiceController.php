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
        $invoices = Invoice::with('tenant.user', 'unit.property')
            ->latest()
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $leases = Lease::with('tenant.user', 'unit.property')
            ->where('status', 'active')
            ->get();

        return view('invoices.create', compact('leases'));
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

        Invoice::create([
            'invoice_number' => Invoice::generateNumber(),
            'lease_id'       => $lease->id,
            'tenant_id'      => $lease->tenant_id,
            'unit_id'        => $lease->unit_id,
            'rent_amount'    => $rent,
            'water_amount'   => $water,
            'garbage_amount' => $garbage,
            'other_amount'   => $other,
            'total_amount'   => $total,
            'amount_paid'    => 0,
            'balance'        => $total,
            'due_date'       => $request->due_date,
            'period_start'   => $request->period_start,
            'period_end'     => $request->period_end,
            'notes'          => $request->notes,
            'status'         => 'draft',
        ]);

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
}