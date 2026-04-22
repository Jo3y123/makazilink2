<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $properties = Property::with('owner', 'units')
        ->when(!$user->isAdmin(), function($q) use ($user) {
        $q->where('owner_id', $user->id)
        ->orWhere('agent_id', $user->id);
    })
    ->latest()
    ->get();

        return view('properties.index', compact('properties'));
    }

    public function create()
    {
        return view('properties.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'county'  => 'nullable|string|max:255',
            'town'    => 'nullable|string|max:255',
            'type'    => 'required|in:apartment,maisonette,bedsitter,single_room,double_room,commercial,land',
        ]);

        Property::create([
            'name'        => $request->name,
            'address'     => $request->address,
            'county'      => $request->county,
            'town'        => $request->town,
            'type'        => $request->type,
            'description' => $request->description,
            'owner_id'    => Auth::id(),
        ]);

        return redirect()->route('properties.index')
            ->with('success', 'Property added successfully.');
    }

    public function edit(Property $property)
    {
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'county'  => 'nullable|string|max:255',
            'town'    => 'nullable|string|max:255',
            'type'    => 'required|in:apartment,maisonette,bedsitter,single_room,double_room,commercial,land',
        ]);

        $property->update($request->only('name', 'address', 'county', 'town', 'type', 'description'));

        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property)
    {
    if (!auth()->user()->isAdmin()) {
        abort(403, 'Only admins can delete properties.');
    }

    $property->delete();

    return redirect()->route('properties.index')
        ->with('success', 'Property deleted successfully.');
    }
}