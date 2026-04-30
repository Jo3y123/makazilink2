<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Setting;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $salaries = Salary::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->latest()
            ->get();

        $totalThisMonth = $salaries->sum('amount');
        $totalAllTime   = Salary::sum('amount');
        $currency       = Setting::get('currency', 'KES');

        $months = collect(range(1, 12))->map(fn($m) => [
            'value' => $m,
            'label' => now()->setMonth($m)->format('F'),
        ]);

        $years = range(now()->year, now()->year - 4);

        return view('salaries.index', compact(
            'salaries', 'totalThisMonth', 'totalAllTime',
            'currency', 'month', 'year', 'months', 'years'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_name'     => 'required|string|max:255',
            'role'           => 'nullable|string|max:255',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa',
            'reference'      => 'nullable|string|max:255',
            'notes'          => 'nullable|string|max:500',
        ]);

        $monthYear = \Carbon\Carbon::parse($request->payment_date)->format('F Y');

        Salary::create([
            'staff_name'     => $request->staff_name,
            'role'           => $request->role,
            'amount'         => $request->amount,
            'payment_date'   => $request->payment_date,
            'month_year'     => $monthYear,
            'payment_method' => $request->payment_method,
            'reference'      => $request->reference,
            'notes'          => $request->notes,
            'recorded_by'    => auth()->id(),
        ]);

        return redirect()->route('salaries.index')
            ->with('success', 'Salary recorded successfully.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')
            ->with('success', 'Salary record deleted.');
    }
}