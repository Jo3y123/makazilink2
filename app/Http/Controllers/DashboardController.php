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
        if ($user->role === 'superadmin') {
            $subscription   = \App\Models\Subscription::first();
            $tenantCount    = \App\Models\Tenant::count();
            $calculatedFee  = $tenantCount * 100;
            $currency       = Setting::get('currency', 'KES');
            $renewalHistory    = \App\Models\RenewalHistory::latest()->take(10)->get();
            $adminNotes        = Setting::get('superadmin_notes', '');
            $myPaybill         = Setting::get('my_paybill', '');
            $myPaybillType     = Setting::get('my_paybill_type', 'paybill');
            $systemVersion     = Setting::get('system_version', '1.0.0');
        return view('dashboard-superadmin', compact(
            'subscription', 'tenantCount', 'calculatedFee',
            'currency', 'user', 'renewalHistory', 'adminNotes',
            'myPaybill', 'myPaybillType', 'systemVersion'
        ));
        }
        $alertDays = (int) Setting::get('lease_alert_days', 30);

        // Date filter — defaults to current month
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        // Superadmin gets their own dashboard
        if ($user->role === 'superadmin') {
            $subscription   = \App\Models\Subscription::first();
            $tenantCount    = \App\Models\Tenant::count();
            $calculatedFee  = $tenantCount * 100;
            $currency       = Setting::get('currency', 'KES');
            $renewalHistory = \App\Models\RenewalHistory::latest()->take(10)->get();
            $adminNotes     = Setting::get('superadmin_notes', '');
            return view('dashboard-superadmin', compact(
                'subscription', 'tenantCount', 'calculatedFee',
                'currency', 'user', 'renewalHistory', 'adminNotes'
            ));
        }

        if (in_array($user->role, ['admin', 'superadmin'])) {
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

        // Check subscription warning — only for regular admin not superadmin
        $subscriptionWarning = null;
        if ($user->role === 'admin') {
            try {
                $subscription = \App\Models\Subscription::first();
                if ($subscription && !$subscription->is_exempt) {
                    $days = $subscription->daysRemaining();
                    if ($days <= 7) {
                        $subscriptionWarning = [
                            'days'       => $days,
                            'expires_at' => $subscription->expires_at?->format('d M Y'),
                            'plan'       => $subscription->planLabel(),
                        ];
                    }
                }
            } catch (\Exception $e) {
                // subscription table may not exist
            }
        }

        return view('dashboard', compact('stats', 'user', 'dateFrom', 'dateTo', 'subscriptionWarning'));
    }
}