<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Lease;
use App\Models\MaintenanceRequest;

class ReportController extends Controller
{
    public function index()
    {
        // Monthly revenue for last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRevenue[] = [
                'month'  => $month->format('M Y'),
                'amount' => Payment::whereMonth('payment_date', $month->month)
                                ->whereYear('payment_date', $month->year)
                                ->where('status', 'confirmed')
                                ->sum('amount'),
            ];
        }

        // Payment method breakdown
        $paymentMethods = Payment::selectRaw('payment_method, sum(amount) as total, count(*) as count')
            ->where('status', 'confirmed')
            ->groupBy('payment_method')
            ->get();

        // Occupancy stats
        $totalUnits    = Unit::count();
        $occupiedUnits = Unit::where('status', 'occupied')->count();
        $vacantUnits   = Unit::where('status', 'vacant')->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;

        // Invoice stats
        $invoiceStats = [
            'paid'    => Invoice::where('status', 'paid')->count(),
            'partial' => Invoice::where('status', 'partial')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'draft'   => Invoice::where('status', 'draft')->count(),
        ];

        // Maintenance stats
        $maintenanceStats = [
            'open'        => MaintenanceRequest::where('status', 'open')->count(),
            'in_progress' => MaintenanceRequest::where('status', 'in_progress')->count(),
            'resolved'    => MaintenanceRequest::where('status', 'resolved')->count(),
        ];

        // Total revenue all time
        $totalRevenue = Payment::where('status', 'confirmed')->sum('amount');

        // This month revenue
        $thisMonthRevenue = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('status', 'confirmed')
            ->sum('amount');

        // Outstanding balance
        $outstandingBalance = Invoice::whereIn('status', ['draft', 'sent', 'partial', 'overdue'])
            ->sum('balance');

        // Recent payments
        $recentPayments = Payment::with('tenant.user', 'unit.property')
            ->where('status', 'confirmed')
            ->latest()
            ->take(10)
            ->get();

        return view('reports.index', compact(
            'monthlyRevenue',
            'paymentMethods',
            'totalUnits',
            'occupiedUnits',
            'vacantUnits',
            'occupancyRate',
            'invoiceStats',
            'maintenanceStats',
            'totalRevenue',
            'thisMonthRevenue',
            'outstandingBalance',
            'recentPayments'
        ));
    }
}