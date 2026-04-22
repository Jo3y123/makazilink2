<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterReading;
use App\Models\Unit;

class WaterReadingController extends Controller
{
    public function index()
    {
        $readings = WaterReading::with('unit.property', 'recordedBy')
            ->latest()
            ->get();

        $units = Unit::with('property')
            ->where('has_water_meter', true)
            ->get();

        return view('water.index', compact('readings', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_id'         => 'required|exists:units,id',
            'previous_reading'=> 'required|numeric|min:0',
            'current_reading' => 'required|numeric|min:0|gte:previous_reading',
            'rate_per_unit'   => 'required|numeric|min:0',
            'reading_date'    => 'required|date',
            'billing_period'  => 'required|string|max:50',
        ]);

        $consumed = $request->current_reading - $request->previous_reading;
        $charged  = $consumed * $request->rate_per_unit;

        WaterReading::create([
            'unit_id'          => $request->unit_id,
            'previous_reading' => $request->previous_reading,
            'current_reading'  => $request->current_reading,
            'units_consumed'   => $consumed,
            'rate_per_unit'    => $request->rate_per_unit,
            'amount_charged'   => $charged,
            'reading_date'     => $request->reading_date,
            'billing_period'   => $request->billing_period,
            'recorded_by'      => auth()->id(),
            'notes'            => $request->notes,
        ]);

        return redirect()->route('water.index')
            ->with('success', 'Water reading recorded. Amount charged: KES ' . number_format($charged));
    }

    public function destroy(WaterReading $water)
    {
        $water->delete();
        return redirect()->route('water.index')
            ->with('success', 'Water reading deleted.');
    }
}