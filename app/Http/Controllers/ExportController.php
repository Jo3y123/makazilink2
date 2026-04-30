<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use App\Exports\InvoicesExport;
use App\Exports\TenantsExport;
use App\Exports\ProfitLossExport;
use App\Exports\SalariesExport;

class ExportController extends Controller
{
    public function payments()
    {
        $filename = 'payments-' . now()->format('d-M-Y') . '.xlsx';
        return Excel::download(new PaymentsExport, $filename);
    }

    public function invoices()
    {
        $filename = 'invoices-' . now()->format('d-M-Y') . '.xlsx';
        return Excel::download(new InvoicesExport, $filename);
    }

    public function tenants()
    {
        $filename = 'tenants-' . now()->format('d-M-Y') . '.xlsx';
        return Excel::download(new TenantsExport, $filename);
    }

    public function profitLoss(Request $request)
    {
        $year     = $request->input('year', now()->year);
        $filename = 'profit-loss-' . $year . '.xlsx';
        return Excel::download(new ProfitLossExport((int) $year), $filename);
    }

    public function salaries()
    {
        $filename = 'salaries-' . now()->format('d-M-Y') . '.xlsx';
        return Excel::download(new SalariesExport, $filename);
    }
}