<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\MaintenanceRequest;
use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ProfitLossExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected int $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function collection(): Collection
    {
        $months = collect();

        for ($i = 1; $i <= 12; $i++) {
            $income = Payment::whereMonth('payment_date', $i)
                ->whereYear('payment_date', $this->year)
                ->where('status', 'confirmed')
                ->sum('amount');

            $maintenance = MaintenanceRequest::whereMonth('created_at', $i)
                ->whereYear('created_at', $this->year)
                ->whereNotNull('cost')
                ->sum('cost');

            $salaries = Salary::whereMonth('payment_date', $i)
                ->whereYear('payment_date', $this->year)
                ->sum('amount');

            $expenses = $maintenance + $salaries;

            $months->push([
                'month'       => now()->setMonth($i)->format('F'),
                'year'        => $this->year,
                'income'      => $income,
                'maintenance' => $maintenance,
                'salaries'    => $salaries,
                'expenses'    => $expenses,
                'profit'      => $income - $expenses,
            ]);
        }

        return $months;
    }

    public function headings(): array
    {
        return [
            'Month',
            'Year',
            'Income (KES)',
            'Maintenance (KES)',
            'Salaries (KES)',
            'Total Expenses (KES)',
            'Net Profit (KES)',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['month'],
            $row['year'],
            number_format($row['income'], 2),
            number_format($row['maintenance'], 2),
            number_format($row['salaries'], 2),
            number_format($row['expenses'], 2),
            number_format($row['profit'], 2),
            $row['income'] == 0 && $row['expenses'] == 0
                ? 'No Activity'
                : ($row['profit'] >= 0 ? 'Profit' : 'Loss'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1a7a4a']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}