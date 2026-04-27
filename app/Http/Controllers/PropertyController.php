<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
            'image'   => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);
 
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('properties', 'public');
        }
 
        Property::create([
            'name'        => $request->name,
            'address'     => $request->address,
            'county'      => $request->county,
            'town'        => $request->town,
            'type'        => $request->type,
            'description' => $request->description,
            'owner_id'    => Auth::id(),
            'image_path'  => $imagePath,
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
            'image'   => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ]);
 
        $data = $request->only('name', 'address', 'county', 'town', 'type', 'description');
 
        if ($request->hasFile('image')) {
            if ($property->image_path) {
                Storage::disk('public')->delete($property->image_path);
            }
            $data['image_path'] = $request->file('image')->store('properties', 'public');
        }
 
        $property->update($data);
 
        return redirect()->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }
 
    public function destroy(Property $property)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admins can delete properties.');
        }
 
        if ($property->image_path) {
            Storage::disk('public')->delete($property->image_path);
        }
 
        $property->delete();
 
        return redirect()->route('properties.index')
            ->with('success', 'Property deleted successfully.');
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
                // Use explicit float casting to avoid decimal issues
                $invoice->water_amount  = (float) $invoice->water_amount + $waterAmount;
                $invoice->total_amount  = (float) $invoice->total_amount + $waterAmount;
                $invoice->balance       = (float) $invoice->balance + $waterAmount;
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
 