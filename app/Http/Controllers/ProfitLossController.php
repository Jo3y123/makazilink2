<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);

        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $income = Payment::whereMonth('payment_date', $i)
                ->whereYear('payment_date', $year)
                ->where('status', 'confirmed')
                ->sum('amount');

            $expenses = MaintenanceRequest::whereMonth('created_at', $i)
                ->whereYear('created_at', $year)
                ->whereNotNull('cost')
                ->sum('cost');

            $months[] = [
                'month'   => now()->setMonth($i)->format('F'),
                'income'  => $income,
                'expenses'=> $expenses,
                'profit'  => $income - $expenses,
            ];
        }

        $totalIncome   = collect($months)->sum('income');
        $totalExpenses = collect($months)->sum('expenses');
        $totalProfit   = $totalIncome - $totalExpenses;

        $currency = Setting::get('currency', 'KES');

        $years = range(now()->year, now()->year - 4);

        return view('reports.profit-loss', compact(
            'months', 'totalIncome', 'totalExpenses', 'totalProfit',
            'currency', 'year', 'years'
        ));
    }

    public function pdf(Request $request)
    {
        $year = $request->input('year', now()->year);

        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $income = Payment::whereMonth('payment_date', $i)
                ->whereYear('payment_date', $year)
                ->where('status', 'confirmed')
                ->sum('amount');

            $expenses = MaintenanceRequest::whereMonth('created_at', $i)
                ->whereYear('created_at', $year)
                ->whereNotNull('cost')
                ->sum('cost');

            $months[] = [
                'month'    => now()->setMonth($i)->format('F'),
                'income'   => $income,
                'expenses' => $expenses,
                'profit'   => $income - $expenses,
            ];
        }

        $totalIncome   = collect($months)->sum('income');
        $totalExpenses = collect($months)->sum('expenses');
        $totalProfit   = $totalIncome - $totalExpenses;
        $currency      = Setting::get('currency', 'KES');

        $pdf = Pdf::loadView('reports.profit-loss-pdf', compact(
            'months', 'totalIncome', 'totalExpenses', 'totalProfit', 'currency', 'year'
        ));

        return $pdf->download('profit-loss-' . $year . '.pdf');
    }
}