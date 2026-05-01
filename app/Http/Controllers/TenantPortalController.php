<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
 
class TenantPortalController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $tenant = $user->tenant;
 
        if (!$tenant) {
            return view('tenant.portal', compact('user', 'tenant'));
        }
 
        // Load active lease with unit and property
        $tenant->load('activeLease.unit.property');
        $lease = $tenant->activeLease;
 
        // Recent invoices
        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();
 
        // Recent payments
        $payments = Payment::where('tenant_id', $tenant->id)
            ->where('status', 'confirmed')
            ->latest()
            ->take(5)
            ->get();
 
        // Maintenance requests
        $maintenanceRequests = MaintenanceRequest::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();
 
        // Summary stats
        $totalCharged    = Invoice::where('tenant_id', $tenant->id)->sum('total_amount');
        $totalPaid       = Payment::where('tenant_id', $tenant->id)->where('status', 'confirmed')->sum('amount');
        $totalBalance    = Invoice::where('tenant_id', $tenant->id)
                            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
                            ->sum('balance');
        $unpaidInvoices  = Invoice::where('tenant_id', $tenant->id)
                            ->whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
                            ->count();
 
        return view('tenant.portal', compact(
            'user',
            'tenant',
            'lease',
            'invoices',
            'payments',
            'maintenanceRequests',
            'totalCharged',
            'totalPaid',
            'totalBalance',
            'unpaidInvoices'
        ));
    }

    public function submitMaintenance(Request $request)
    {
    $user   = Auth::user();
    $tenant = $user->tenant;

    if (!$tenant || !$tenant->activeLease) {
        return back()->with('error', 'You do not have an active lease.');
    }

    $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'category'    => 'required|in:plumbing,electrical,general,structural,appliance,other',
        'priority'    => 'required|in:low,normal,high,urgent',
    ]);

    $lease = $tenant->activeLease;

    MaintenanceRequest::create([
        'title'       => $request->title,
        'description' => $request->description,
        'category'    => $request->category,
        'priority'    => $request->priority,
        'status'      => 'open',
        'unit_id'     => $lease->unit_id,
        'tenant_id'   => $tenant->id,
        'reported_by' => $user->id,
    ]);

    return back()->with('success', 'Maintenance request submitted successfully. The caretaker will be in touch.');
    }
}
 