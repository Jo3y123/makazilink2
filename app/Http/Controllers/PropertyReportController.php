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
}