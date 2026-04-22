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
        $tenants = Tenant::with('user', 'activeLease.unit.property')
            ->latest()
            ->get();

        return view('tenants.index', compact('tenants'));
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
}

