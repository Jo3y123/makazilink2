<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\Models\Property;

class UnitController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $units = Unit::with('property')
        ->when(!$user->isAdmin() && $user->role !== 'caretaker', function($q) use ($user) {
            $q->whereHas('property', function ($query) use ($user) {
                $query->where('owner_id', $user->id)
                    ->orWhere('agent_id', $user->id);
            });
        })
        ->latest()
        ->get();

        $properties = $user->isAdmin()
            ? Property::all()
            : Property::where('owner_id', $user->id)
                      ->orWhere('agent_id', $user->id)
                      ->get();

        return view('units.index', compact('units', 'properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id'    => 'required|exists:properties,id',
            'unit_number'    => 'required|string|max:50',
            'type'           => 'required|in:bedsitter,single_room,one_bedroom,two_bedroom,three_bedroom,commercial',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'floor_number'   => 'nullable|integer|min:0',
            'image'          => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('units', 'public');
        }

        Unit::create([
            'property_id'        => $request->property_id,
            'unit_number'        => $request->unit_number,
            'type'               => $request->type,
            'rent_amount'        => $request->rent_amount,
            'deposit_amount'     => $request->deposit_amount ?? 0,
            'floor_number'       => $request->floor_number ?? 0,
            'has_water_meter'    => $request->boolean('has_water_meter'),
            'water_meter_number' => $request->water_meter_number,
            'notes'              => $request->notes,
            'image_path'         => $imagePath,
        ]);

        return redirect()->route('units.index')
            ->with('success', 'Unit added successfully.');
    }

    public function edit(Unit $unit)
    {
        $user = auth()->user();

        $properties = $user->isAdmin()
            ? Property::all()
            : Property::where('owner_id', $user->id)
                      ->orWhere('agent_id', $user->id)
                      ->get();

        return view('units.edit', compact('unit', 'properties'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'property_id'    => 'required|exists:properties,id',
            'unit_number'    => 'required|string|max:50',
            'type'           => 'required|in:bedsitter,single_room,one_bedroom,two_bedroom,three_bedroom,commercial',
            'rent_amount'    => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'floor_number'   => 'nullable|integer|min:0',
            'image'          => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);

        $data = [
            'property_id'        => $request->property_id,
            'unit_number'        => $request->unit_number,
            'type'               => $request->type,
            'rent_amount'        => $request->rent_amount,
            'deposit_amount'     => $request->deposit_amount ?? 0,
            'floor_number'       => $request->floor_number ?? 0,
            'has_water_meter'    => $request->boolean('has_water_meter'),
            'water_meter_number' => $request->water_meter_number,
            'notes'              => $request->notes,
        ];

        if ($request->hasFile('image')) {
            if ($unit->image_path) {
                Storage::disk('public')->delete($unit->image_path);
            }
            $data['image_path'] = $request->file('image')->store('units', 'public');
        }

        $unit->update($data);

        return redirect()->route('units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->image_path) {
            Storage::disk('public')->delete($unit->image_path);
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}