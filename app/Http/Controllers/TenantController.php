<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Unit;

class TenantController extends Controller
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
        $units = Unit::with('property')
            ->where('status', 'vacant')
            ->get();

        return view('tenants.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:20',
            'id_number'   => 'nullable|string|max:20',
            'occupation'  => 'nullable|string|max:255',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        // Create user account for tenant
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make('password'),
            'role'     => 'tenant',
        ]);

        // Create tenant profile
        Tenant::create([
            'user_id'                 => $user->id,
            'id_number'               => $request->id_number,
            'occupation'              => $request->occupation,
            'emergency_contact_name'  => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'notes'                   => $request->notes,
        ]);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant registered successfully. Default password is: password');
    }

    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'id_number'  => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $tenant->user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        $tenant->update([
            'id_number'               => $request->id_number,
            'occupation'              => $request->occupation,
            'emergency_contact_name'  => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'notes'                   => $request->notes,
        ]);

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->user->delete();
        return redirect()->route('tenants.index')
            ->with('success', 'Tenant removed successfully.');
    }

    public function statement(Tenant $tenant)
    {
    $tenant->load('user', 'leases.unit.property', 'invoices', 'payments.recordedBy');

    $invoices = \App\Models\Invoice::where('tenant_id', $tenant->id)
        ->orderBy('period_start')
        ->get();

    $payments = \App\Models\Payment::where('tenant_id', $tenant->id)
        ->where('status', 'confirmed')
        ->orderBy('payment_date')
        ->get();

    $totalCharged = $invoices->sum('total_amount');
    $totalPaid    = $payments->sum('amount');
    $balance      = $totalCharged - $totalPaid;

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenants.statement', compact(
        'tenant', 'invoices', 'payments', 'totalCharged', 'totalPaid', 'balance'
    ));

    return $pdf->download('statement-' . str_replace(' ', '-', $tenant->user->name) . '.pdf');
    }

    public function quickAdd()
{
    $units = Unit::with('property')
        ->where('status', 'vacant')
        ->get();

    return view('tenants.quick-add', compact('units'));
}

public function quickStore(Request $request)
{
    $request->validate([
        'name'         => 'required|string|max:255',
        'phone'        => 'required|string|max:20',
        'email'        => 'nullable|email|unique:users,email',
        'unit_id'      => 'required|exists:units,id',
        'monthly_rent' => 'required|numeric|min:1',
        'deposit_paid' => 'nullable|numeric|min:0',
        'move_in_date' => 'required|date',
        'notice_days'  => 'nullable|integer|min:1',
    ]);

    // Generate email if not provided
    $email = $request->email ?? strtolower(str_replace(' ', '.', $request->name)) . '.' . time() . '@tenant.local';

    // Create user account
    $user = User::create([
        'name'      => $request->name,
        'email'     => $email,
        'phone'     => $request->phone,
        'password'  => Hash::make('password'),
        'role'      => 'tenant',
        'is_active' => true,
    ]);

    // Create tenant profile
    $tenant = Tenant::create([
        'user_id' => $user->id,
    ]);

    // Get unit
    $unit = Unit::find($request->unit_id);

    // Create month to month lease
    $lease = \App\Models\Lease::create([
        'tenant_id'    => $tenant->id,
        'unit_id'      => $unit->id,
        'monthly_rent' => $request->monthly_rent,
        'deposit_paid' => $request->deposit_paid ?? 0,
        'start_date'   => $request->move_in_date,
        'end_date'     => null, // month to month
        'notice_days'  => $request->notice_days ?? 30,
        'status'       => 'active',
    ]);

    // Mark unit as occupied
    $unit->update(['status' => 'occupied']);

    // Record deposit if paid
    if ($request->deposit_paid && $request->deposit_paid > 0) {
        \App\Models\Deposit::create([
            'tenant_id'       => $tenant->id,
            'lease_id'        => $lease->id,
            'amount_expected' => $request->deposit_paid,
            'amount_received' => $request->deposit_paid,
            'date_received'   => $request->move_in_date,
            'status'          => 'received',
            'recorded_by'     => auth()->id(),
        ]);
    }

    return redirect()->route('tenants.index')
        ->with('success', 'Tenant ' . $request->name . ' added successfully. Default password is: password');
    }

    public function vacate(Request $request, Tenant $tenant)
    {
    $request->validate([
        'vacate_date'   => 'required|date',
        'vacate_reason' => 'nullable|string|max:500',
    ]);

    // Terminate active lease
    $lease = $tenant->activeLease;
    if ($lease) {
        $lease->update([
            'status'   => 'terminated',
            'end_date' => $request->vacate_date,
        ]);

        // Mark unit as vacant
        $lease->unit->update(['status' => 'vacant']);
    }

    // Deactivate user account
    $tenant->user->update(['is_active' => false]);

    return redirect()->route('tenants.index')
        ->with('success', $tenant->user->name . ' has been vacated successfully. Unit is now available.');
    }
}

