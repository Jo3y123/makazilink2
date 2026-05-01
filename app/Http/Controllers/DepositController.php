<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Lease;
use App\Models\Tenant;
use App\Models\Setting;

class DepositController extends Controller
{
    public function index()
    {
        $deposits = Deposit::with('tenant.user', 'lease.unit.property')
            ->latest()
            ->get();

        $totalHeld     = $deposits->sum(fn($d) => $d->balanceHeld());
        $totalReceived = $deposits->sum('amount_received');
        $totalRefunded = $deposits->sum('refund_amount');
        $currency      = Setting::get('currency', 'KES');

        return view('deposits.index', compact(
            'deposits', 'totalHeld', 'totalReceived', 'totalRefunded', 'currency'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lease_id'        => 'required|exists:leases,id',
            'amount_expected' => 'required|numeric|min:0',
            'amount_received' => 'required|numeric|min:0',
            'date_received'   => 'required|date',
            'status'          => 'required|in:pending,partial,received',
            'notes'           => 'nullable|string|max:500',
        ]);

        $lease = Lease::find($request->lease_id);

        // Check if deposit already exists for this lease
        $existing = Deposit::where('lease_id', $lease->id)->first();
        if ($existing) {
            return back()->with('error', 'A deposit record already exists for this lease. Edit the existing one instead.');
        }

        Deposit::create([
            'tenant_id'       => $lease->tenant_id,
            'lease_id'        => $lease->id,
            'amount_expected' => $request->amount_expected,
            'amount_received' => $request->amount_received,
            'date_received'   => $request->date_received,
            'status'          => $request->status,
            'notes'           => $request->notes,
            'recorded_by'     => auth()->id(),
        ]);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit recorded successfully.');
    }

    public function edit(Deposit $deposit)
    {
        $deposit->load('tenant.user', 'lease.unit.property');
        return view('deposits.edit', compact('deposit'));
    }

    public function update(Request $request, Deposit $deposit)
    {
        $request->validate([
            'amount_received'  => 'required|numeric|min:0',
            'date_received'    => 'nullable|date',
            'status'           => 'required|in:pending,partial,received',
            'deduction_amount' => 'nullable|numeric|min:0',
            'deduction_reason' => 'nullable|string|max:500',
            'refund_amount'    => 'nullable|numeric|min:0',
            'refund_date'      => 'nullable|date',
            'refund_method'    => 'nullable|in:cash,mpesa,bank_transfer',
            'refund_reference' => 'nullable|string|max:255',
            'notes'            => 'nullable|string|max:500',
        ]);

        $deposit->update([
            'amount_received'  => $request->amount_received,
            'date_received'    => $request->date_received,
            'status'           => $request->status,
            'deduction_amount' => $request->deduction_amount ?? 0,
            'deduction_reason' => $request->deduction_reason,
            'refund_amount'    => $request->refund_amount ?? 0,
            'refund_date'      => $request->refund_date,
            'refund_method'    => $request->refund_method,
            'refund_reference' => $request->refund_reference,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('deposits.index')
            ->with('success', 'Deposit updated successfully.');
    }

    public function destroy(Deposit $deposit)
    {
        $deposit->delete();
        return redirect()->route('deposits.index')
            ->with('success', 'Deposit record deleted.');
    }
}