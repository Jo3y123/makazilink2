<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Payment;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyReportController extends Controller
{
    public function index()
    {
        $properties = Property::with('units.activeLease.tenant.user')->get();
        return view('reports.property', compact('properties'));
    }

    public function show(Property $property)
    {
        $property->load('units.activeLease.tenant.user', 'owner');

        $units = $property->units;

        $totalRevenue = Payment::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->where('status', 'confirmed')->sum('amount');

        $thisMonthRevenue = Payment::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->where('status', 'confirmed')
          ->whereMonth('payment_date', now()->month)
          ->whereYear('payment_date', now()->year)
          ->sum('amount');

        $outstandingBalance = Invoice::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');

        $occupiedUnits = $units->where('status', 'occupied')->count();
        $vacantUnits   = $units->where('status', 'vacant')->count();
        $totalUnits    = $units->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        return view('reports.property-show', compact(
            'property',
            'units',
            'totalRevenue',
            'thisMonthRevenue',
            'outstandingBalance',
            'occupiedUnits',
            'vacantUnits',
            'totalUnits',
            'occupancyRate'
        ));
    }

    public function pdf(Property $property)
    {
        $property->load('units.activeLease.tenant.user', 'owner');

        $units = $property->units;

        $totalRevenue = Payment::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->where('status', 'confirmed')->sum('amount');

        $thisMonthRevenue = Payment::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->where('status', 'confirmed')
          ->whereMonth('payment_date', now()->month)
          ->whereYear('payment_date', now()->year)
          ->sum('amount');

        $outstandingBalance = Invoice::whereHas('unit', function($q) use ($property) {
            $q->where('property_id', $property->id);
        })->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->sum('balance');

        $occupiedUnits = $units->where('status', 'occupied')->count();
        $vacantUnits   = $units->where('status', 'vacant')->count();
        $totalUnits    = $units->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        $pdf = Pdf::loadView('reports.property-pdf', compact(
            'property',
            'units',
            'totalRevenue',
            'thisMonthRevenue',
            'outstandingBalance',
            'occupiedUnits',
            'vacantUnits',
            'totalUnits',
            'occupancyRate'
        ));

        return $pdf->download('property-report-' . str_replace(' ', '-', $property->name) . '.pdf');
    }

    public function sharedWater(Property $property)
    {
        $property->load('units.activeLease.tenant.user');

        $units = $property->units->filter(fn($u) => $u->status === 'occupied' && $u->activeLease);

        return view('properties.shared-water', compact('property', 'units'));
    }

    public function applySharedWater(Request $request, Property $property)
    {
    $request->validate([
        'total_water_bill' => 'required|numeric|min:1',
        'unit_ids'         => 'required|array|min:1',
        'unit_ids.*'       => 'exists:units,id',
        'split_method'     => 'required|in:equal,custom',
        'custom_amounts'   => 'nullable|array',
    ]);

    $property->load('units.activeLease.tenant');

    $selectedUnits = $property->units->whereIn('id', $request->unit_ids);
    $totalBill     = (float) $request->total_water_bill;
    $count         = $selectedUnits->count();
    $applied       = 0;
    $created       = 0;
    $errors        = [];

    foreach ($selectedUnits as $unit) {
        // Calculate water amount for this unit
        if ($request->split_method === 'equal') {
            $waterAmount = round($totalBill / $count, 2);
        } else {
            $waterAmount = (float) ($request->custom_amounts[$unit->id] ?? 0);
        }

        if ($waterAmount <= 0) continue;

        $lease  = $unit->activeLease;
        $tenant = $lease->tenant;

        // Find existing unpaid invoice
        $invoice = \App\Models\Invoice::where('tenant_id', $tenant->id)
            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
            ->latest()
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->water_amount  += $waterAmount;
            $invoice->total_amount  += $waterAmount;
            $invoice->balance       += $waterAmount;
            $invoice->save();
            $applied++;
        } else {
            // Create new water-only invoice
            \App\Models\Invoice::create([
                'invoice_number' => \App\Models\Invoice::generateNumber(),
                'lease_id'       => $lease->id,
                'tenant_id'      => $tenant->id,
                'unit_id'        => $unit->id,
                'rent_amount'    => 0,
                'water_amount'   => $waterAmount,
                'garbage_amount' => 0,
                'other_amount'   => 0,
                'total_amount'   => $waterAmount,
                'amount_paid'    => 0,
                'balance'        => $waterAmount,
                'due_date'       => now()->addDays((int) \App\Models\Setting::get('invoice_due_days', 5)),
                'period_start'   => now()->startOfMonth(),
                'period_end'     => now()->endOfMonth(),
                'status'         => 'draft',
                'notes'          => 'Shared water billing',
            ]);
            $created++;
        }
    }

    $message = "Shared water applied. Updated {$applied} invoice(s), created {$created} new invoice(s).";

    return redirect()->route('properties.index')
        ->with('success', $message);
    }
}