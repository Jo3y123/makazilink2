<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Unit;

class LeaseController extends Controller
{
    public function index()
    {
        $leases = Lease::with('tenant.user', 'unit.property')
            ->latest()
            ->get();

        return view('leases.index', compact('leases'));
    }

    public function create()
    {
        $tenants = Tenant::with('user')->get();
        $units   = Unit::with('property')->where('status', 'vacant')->get();

        return view('leases.create', compact('tenants', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'    => 'required|exists:tenants,id',
            'unit_id'      => 'required|exists:units,id',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_paid' => 'nullable|numeric|min:0',
            'notice_days'  => 'nullable|integer|min:0',
        ]);

        Lease::create([
            'tenant_id'    => $request->tenant_id,
            'unit_id'      => $request->unit_id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'monthly_rent' => $request->monthly_rent,
            'deposit_paid' => $request->deposit_paid ?? 0,
            'notice_days'  => $request->notice_days ?? 30,
            'next_due_date'=> $request->start_date,
            'terms'        => $request->terms,
            'status'       => 'active',
        ]);

        // Mark unit as occupied
        Unit::find($request->unit_id)->update(['status' => 'occupied']);

        return redirect()->route('leases.index')
            ->with('success', 'Lease created successfully.');
    }

    public function show(Lease $lease)
    {
        $lease->load('tenant.user', 'unit.property');
        return view('leases.show', compact('lease'));
    }

    public function edit(Lease $lease)
    {
        $tenants = Tenant::with('user')->get();
        $units   = Unit::with('property')
            ->where(function($q) use ($lease) {
                $q->where('status', 'vacant')
                  ->orWhere('id', $lease->unit_id);
            })
            ->get();

        return view('leases.edit', compact('lease', 'tenants', 'units'));
    }

    public function update(Request $request, Lease $lease)
    {
        $request->validate([
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_paid' => 'nullable|numeric|min:0',
            'notice_days'  => 'nullable|integer|min:0',
            'status'       => 'required|in:active,expired,terminated',
        ]);

        // If status changed to terminated/expired, free up the unit
        if (in_array($request->status, ['terminated', 'expired']) && $lease->status === 'active') {
            $lease->unit->update(['status' => 'vacant']);
        }

        $lease->update([
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'monthly_rent' => $request->monthly_rent,
            'deposit_paid' => $request->deposit_paid ?? 0,
            'notice_days'  => $request->notice_days ?? 30,
            'terms'        => $request->terms,
            'status'       => $request->status,
        ]);

        return redirect()->route('leases.index')
            ->with('success', 'Lease updated successfully.');
    }

    public function destroy(Lease $lease)
    {
        // Free up the unit
        $lease->unit->update(['status' => 'vacant']);
        $lease->delete();

        return redirect()->route('leases.index')
            ->with('success', 'Lease deleted successfully.');
    }
}