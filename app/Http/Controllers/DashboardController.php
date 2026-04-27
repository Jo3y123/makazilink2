<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Setting;
 
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $alertDays = (int) Setting::get('lease_alert_days', 30);
 
        // Date filter — defaults to current month
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
 
        if ($user->role === 'admin') {
            $stats = [
                'total_properties' => Property::count(),
                'total_units'      => Unit::count(),
                'occupied_units'   => Unit::where('status', 'occupied')->count(),
                'vacant_units'     => Unit::where('status', 'vacant')->count(),
                'total_tenants'    => Tenant::count(),
                'monthly_revenue'  => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                                        ->where('status', 'confirmed')
                                        ->sum('amount'),
                'pending_payments' => Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count(),
                'open_maintenance' => MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count(),
                'expiring_leases'  => Lease::where('status', 'active')
                                        ->whereNotNull('end_date')
                                        ->whereDate('end_date', '<=', now()->addDays($alertDays))
                                        ->count(),
            ];
 
        } elseif ($user->role === 'caretaker') {
            $stats = [
                'total_properties' => Property::count(),
                'total_units'      => Unit::count(),
                'occupied_units'   => Unit::where('status', 'occupied')->count(),
                'vacant_units'     => Unit::where('status', 'vacant')->count(),
                'total_tenants'    => Tenant::count(),
                'monthly_revenue'  => 0,
                'pending_payments' => 0,
                'open_maintenance' => MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count(),
                'expiring_leases'  => Lease::where('status', 'active')
                                        ->whereNotNull('end_date')
                                        ->whereDate('end_date', '<=', now()->addDays($alertDays))
                                        ->count(),
            ];
 
        } elseif ($user->role === 'accountant') {
            $stats = [
                'total_properties' => Property::count(),
                'total_units'      => Unit::count(),
                'occupied_units'   => Unit::where('status', 'occupied')->count(),
                'vacant_units'     => Unit::where('status', 'vacant')->count(),
                'total_tenants'    => Tenant::count(),
                'monthly_revenue'  => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                                        ->where('status', 'confirmed')
                                        ->sum('amount'),
                'pending_payments' => Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count(),
                'open_maintenance' => MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count(),
                'expiring_leases'  => Lease::where('status', 'active')
                                        ->whereNotNull('end_date')
                                        ->whereDate('end_date', '<=', now()->addDays($alertDays))
                                        ->count(),
            ];
 
        } else {
            // agent
            $stats = [
                'total_properties' => Property::where('owner_id', $user->id)
                                        ->orWhere('agent_id', $user->id)->count(),
                'total_units'      => Unit::whereHas('property', function($q) use ($user) {
                                        $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
                                      })->count(),
                'occupied_units'   => Unit::whereHas('property', function($q) use ($user) {
                                        $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
                                      })->where('status', 'occupied')->count(),
                'vacant_units'     => Unit::whereHas('property', function($q) use ($user) {
                                        $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
                                      })->where('status', 'vacant')->count(),
                'total_tenants'    => Tenant::whereHas('activeLease.unit.property', function($q) use ($user) {
                                        $q->where('owner_id', $user->id)->orWhere('agent_id', $user->id);
                                      })->count(),
                'monthly_revenue'  => 0,
                'pending_payments' => Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])->count(),
                'open_maintenance' => MaintenanceRequest::whereIn('status', ['open', 'in_progress'])->count(),
                'expiring_leases'  => Lease::where('status', 'active')
                                        ->whereNotNull('end_date')
                                        ->whereDate('end_date', '<=', now()->addDays($alertDays))
                                        ->count(),
            ];
        }
 
        return view('dashboard', compact('stats', 'user', 'dateFrom', 'dateTo'));
    }
}
 